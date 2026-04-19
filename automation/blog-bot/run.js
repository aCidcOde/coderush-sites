#!/usr/bin/env node

const fs = require("node:fs");
const path = require("node:path");
const crypto = require("node:crypto");
const { execSync } = require("node:child_process");
const { loadEnvFiles } = require("./lib/env-loader");
const { pickSiteTheme, sitePromptStyle } = require("./lib/site-strategy");
const {
  resolveAiConfig,
  generateWithOpenAI,
  generateWithOpenRouter,
  generateFallbackContent
} = require("./lib/ai-writer");
const { publishSitePost } = require("./lib/publisher");

const ROOT = path.resolve(__dirname, "..", "..");
const CONFIG_PATH = path.resolve(__dirname, "config", "sites.json");
const REPORTS_DIR = path.resolve(__dirname, "reports");

loadEnvFiles(ROOT);

function parseArgs() {
  const args = process.argv.slice(2);
  const modeArg = args.find((arg) => arg.startsWith("--mode="));
  const sitesArg = args.find((arg) => arg.startsWith("--sites="));
  const dateArg = args.find((arg) => arg.startsWith("--date="));

  return {
    mode: modeArg ? modeArg.split("=")[1] : process.env.BLOG_BOT_MODE || "dry-run",
    sites: sitesArg
      ? sitesArg
          .split("=")[1]
          .split(",")
          .map((item) => item.trim())
          .filter(Boolean)
      : [],
    date: dateArg ? dateArg.split("=")[1] : ""
  };
}

function ensureDir(dirPath) {
  fs.mkdirSync(dirPath, { recursive: true });
}

function nowInSaoPaulo() {
  const formatter = new Intl.DateTimeFormat("en-CA", {
    timeZone: "America/Sao_Paulo",
    year: "numeric",
    month: "2-digit",
    day: "2-digit"
  });
  return formatter.format(new Date());
}

function focusForWeek(rotation, dateSeed) {
  const hash = crypto.createHash("sha1").update(dateSeed).digest("hex");
  const index = parseInt(hash.slice(0, 8), 16) % rotation.length;
  return rotation[index];
}

function slugify(value) {
  return value
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9\s-]/g, "")
    .trim()
    .replace(/\s+/g, "-")
    .replace(/-+/g, "-");
}

function buildPostContract(site, focus, date) {
  const theme = pickSiteTheme(site.id, date);
  const title = `${site.name}: ${focus.toUpperCase()} aplicado a resultado real`;
  const slug = slugify(`${site.id}-${focus}-${date}`);
  const description = `Atualizacao semanal de ${focus} para ${site.name}, com foco em ${theme}.`;

  return {
    date,
    siteId: site.id,
    postType: site.postType,
    focus,
    theme,
    slug,
    title,
    description,
    sources: ["https://openai.com/news/", "https://github.blog/"],
    status: "draft",
    generatedAt: new Date().toISOString()
  };
}

function buildAiPrompt({ site, contract }) {
  const style = sitePromptStyle(site);
  return [
    `Site: ${site.name}`,
    `Tipo de post: ${style.postType}`,
    `Publico: ${style.audience}`,
    `Tom: ${style.tone}`,
    `Tema: ${contract.theme}`,
    `Foco da semana: ${contract.focus}`,
    "Estruture um post com JSON no formato:",
    '{ "headline": "...", "summary": "...", "sections": [{"title":"...", "body":"..."}] }',
    "Obrigatorio: 4 secoes, incluindo uma secao chamada 'Software sob medida com IA'.",
    "Regras extras:",
    style.constraints.map((item) => `- ${item}`).join("\n")
  ].join("\n");
}

function writeDraft(site, contract) {
  const targetDir = path.resolve(ROOT, site.outputRoot, contract.date);
  ensureDir(targetDir);
  const jsonPath = path.resolve(targetDir, `${contract.slug}.json`);
  const markdownPath = path.resolve(targetDir, `${contract.slug}.md`);

  const sectionsMarkdown = (contract.content.sections || [])
    .map((section) => `## ${section.title}\n\n${section.body}`)
    .join("\n\n");

  const markdown = [
    `# ${contract.content.headline}`,
    "",
    contract.content.summary,
    "",
    sectionsMarkdown,
    "",
    "## Fontes sugeridas",
    "",
    ...(contract.sources || []).map((source) => `- ${source}`),
    "",
    "_Gerado automaticamente pelo blog bot_"
  ].join("\n");

  fs.writeFileSync(jsonPath, JSON.stringify(contract, null, 2), "utf8");
  fs.writeFileSync(markdownPath, markdown, "utf8");
  return { jsonPath, markdownPath };
}

function loadExistingDraft(site, contract) {
  const filePath = path.resolve(ROOT, site.outputRoot, contract.date, `${contract.slug}.json`);
  if (!fs.existsSync(filePath)) {
    return null;
  }

  try {
    const draft = JSON.parse(fs.readFileSync(filePath, "utf8"));
    if (draft?.content?.headline && Array.isArray(draft?.content?.sections)) {
      return draft;
    }
  } catch (error) {
    return null;
  }

  return null;
}

async function generateContent(aiConfig, site, prompt, contract) {
  if (!aiConfig) {
    return {
      content: generateFallbackContent({
        siteName: site.name,
        theme: contract.theme,
        focus: contract.focus
      }),
      aiUsed: false,
      aiProvider: "fallback",
      warning: null
    };
  }

  try {
    if (aiConfig.provider === "openrouter") {
      return {
        content: await generateWithOpenRouter({
          apiKey: aiConfig.apiKey,
          model: aiConfig.textModel,
          prompt,
          appUrl: aiConfig.appUrl,
          appName: aiConfig.appName
        }),
        aiUsed: true,
        aiProvider: "openrouter",
        warning: null
      };
    }

    return {
      content: await generateWithOpenAI({
        apiKey: aiConfig.apiKey,
        model: aiConfig.textModel,
        prompt
      }),
      aiUsed: true,
      aiProvider: "openai",
      warning: null
    };
  } catch (error) {
    return {
      content: generateFallbackContent({
        siteName: site.name,
        theme: contract.theme,
        focus: contract.focus
      }),
      aiUsed: false,
      aiProvider: "fallback",
      warning: `Falha ao usar IA, fallback aplicado: ${String(error.message || error)}`
    };
  }
}

function lintPhpFiles(relativeTargets) {
  const targets = [...new Set(relativeTargets || [])];
  if (targets.length === 0) {
    return { ok: true, skipped: true, targets: [] };
  }

  try {
    for (const relativeTarget of targets) {
      const filePath = path.resolve(ROOT, relativeTarget);
      execSync(`php -l "${filePath}"`, { stdio: "pipe" });
    }
    return { ok: true, skipped: false, targets };
  } catch (error) {
    return {
      ok: false,
      skipped: false,
      targets,
      error: String(error.stderr || error.message || error)
    };
  }
}

async function run() {
  const { mode, sites: requestedSites, date: explicitDate } = parseArgs();
  if (!["dry-run", "publish"].includes(mode)) {
    throw new Error(`Modo invalido: ${mode}. Use dry-run ou publish.`);
  }

  const config = JSON.parse(fs.readFileSync(CONFIG_PATH, "utf8"));
  const date = explicitDate || nowInSaoPaulo();
  const focus = focusForWeek(config.rotation, date);
  const aiConfig = resolveAiConfig(process.env);

  ensureDir(REPORTS_DIR);
  const report = {
    mode,
    date,
    timezone: config.timezone,
    focus,
    startedAt: new Date().toISOString(),
    aiProvider: aiConfig?.provider || "fallback",
    sites: []
  };

  const selectedSites = config.sites.filter((site) =>
    requestedSites.length === 0 ? true : requestedSites.includes(site.id)
  );

  if (selectedSites.length === 0) {
    throw new Error("Nenhum site selecionado para processamento.");
  }

  for (const site of selectedSites) {
    if (!site.enabled) {
      report.sites.push({ siteId: site.id, skipped: true, reason: "site disabled" });
      continue;
    }

    let contract = buildPostContract(site, focus, date);
    const existingDraft = loadExistingDraft(site, contract);

    let aiUsed = false;
    let aiProvider = "fallback";
    let warning = null;

    if (existingDraft) {
      contract = existingDraft;
      aiProvider = "existing-draft";
      warning = "Rascunho existente reutilizado para manter o seed consistente.";
    } else {
      const generation = await generateContent(aiConfig, site, buildAiPrompt({ site, contract }), contract);
      contract.content = generation.content;
      aiUsed = generation.aiUsed;
      aiProvider = generation.aiProvider;
      warning = generation.warning;
    }

    const outputPath = writeDraft(site, contract);
    const siteResult = {
      siteId: site.id,
      ok: true,
      aiUsed,
      aiProvider,
      warning,
      outputPath: path.relative(ROOT, outputPath.jsonPath).replace(/\\/g, "/"),
      markdownPath: path.relative(ROOT, outputPath.markdownPath).replace(/\\/g, "/")
    };

    if (mode === "publish") {
      const publishResult = await publishSitePost(ROOT, site, contract, aiConfig);
      const lint = lintPhpFiles(publishResult.phpLintTargets);
      siteResult.publishResult = {
        postPath: publishResult.postPath,
        homeUpdated: publishResult.homeUpdated,
        blogUpdated: publishResult.blogUpdated,
        sitemapUpdated: publishResult.sitemapUpdated,
        robotsUpdated: publishResult.robotsUpdated,
        coverSource: publishResult.coverSource
      };
      siteResult.lint = lint;

      if (publishResult.warning) {
        siteResult.warning = siteResult.warning
          ? `${siteResult.warning} ${publishResult.warning}`
          : publishResult.warning;
      }

      if (!lint.ok) {
        siteResult.ok = false;
      }
    }

    report.sites.push(siteResult);
  }

  report.finishedAt = new Date().toISOString();
  report.ok = report.sites.every((site) => site.ok || site.skipped);
  const reportPath = path.resolve(REPORTS_DIR, `run-${date}-${mode}.json`);
  fs.writeFileSync(reportPath, JSON.stringify(report, null, 2), "utf8");

  if (!report.ok) {
    console.error("Blog bot finalizou com falhas. Veja o relatorio:", path.relative(ROOT, reportPath));
    process.exit(1);
  }

  console.log("Blog bot executado com sucesso.");
  console.log("Relatorio:", path.relative(ROOT, reportPath));
}

run().catch((error) => {
  console.error("Falha fatal no blog bot:", error.message);
  process.exit(1);
});
