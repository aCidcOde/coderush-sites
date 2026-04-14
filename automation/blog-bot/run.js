#!/usr/bin/env node

const fs = require("node:fs");
const path = require("node:path");
const crypto = require("node:crypto");
const { execSync } = require("node:child_process");
const { pickSiteTheme, sitePromptStyle } = require("./lib/site-strategy");
const { generateWithOpenAI, generateFallbackContent } = require("./lib/ai-writer");
const { publishSvdPost } = require("./lib/svd-publisher");

const ROOT = path.resolve(__dirname, "..", "..");
const CONFIG_PATH = path.resolve(__dirname, "config", "sites.json");
const REPORTS_DIR = path.resolve(__dirname, "reports");

function parseArgs() {
  const args = process.argv.slice(2);
  const modeArg = args.find((arg) => arg.startsWith("--mode="));
  const mode = modeArg ? modeArg.split("=")[1] : process.env.BLOG_BOT_MODE || "dry-run";
  return { mode };
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

function focusForWeek(rotation) {
  const weekSeed = new Date().toISOString().slice(0, 10);
  const hash = crypto.createHash("sha1").update(weekSeed).digest("hex");
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
  const theme = site.theme;
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
    sources: [
      "https://openai.com/news/",
      "https://github.blog/"
    ],
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

function lintPhpIfNeeded(site) {
  if (!site.requiresPhpLint) {
    return { ok: true, skipped: true };
  }

  const phpTargets = [
    path.resolve(ROOT, "sistemavendadireta", "index.php"),
    path.resolve(ROOT, "sistemavendadireta", "blog", "index.php")
  ];

  try {
    for (const file of phpTargets) {
      execSync(`php -l "${file}"`, { stdio: "pipe" });
    }
    return { ok: true, skipped: false };
  } catch (error) {
    return {
      ok: false,
      skipped: false,
      error: String(error.stderr || error.message || error)
    };
  }
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

async function run() {
  const { mode } = parseArgs();
  if (!["dry-run", "publish"].includes(mode)) {
    throw new Error(`Modo invalido: ${mode}. Use dry-run ou publish.`);
  }

  const config = JSON.parse(fs.readFileSync(CONFIG_PATH, "utf8"));
  const date = nowInSaoPaulo();
  const focus = focusForWeek(config.rotation);
  const apiKey = process.env.OPENAI_API_KEY;
  const model = process.env.BLOG_BOT_OPENAI_MODEL || "gpt-4o-mini";

  ensureDir(REPORTS_DIR);
  const report = {
    mode,
    date,
    timezone: config.timezone,
    focus,
    startedAt: new Date().toISOString(),
    sites: []
  };

  const usedThemes = new Set();
  for (const site of config.sites) {
    if (!site.enabled) {
      report.sites.push({ siteId: site.id, skipped: true, reason: "site disabled" });
      continue;
    }

    site.theme = pickSiteTheme(site.id, usedThemes);
    usedThemes.add(site.theme);
    const contract = buildPostContract(site, focus, date);
    const lint = lintPhpIfNeeded(site);
    if (!lint.ok) {
      report.sites.push({
        siteId: site.id,
        ok: false,
        lint
      });
      continue;
    }

    let content;
    let aiUsed = false;
    let warning = null;

    try {
      if (apiKey) {
        content = await generateWithOpenAI({
          apiKey,
          model,
          prompt: buildAiPrompt({ site, contract })
        });
        aiUsed = true;
      } else {
        content = generateFallbackContent({
          siteName: site.name,
          theme: contract.theme,
          focus: contract.focus
        });
      }
    } catch (error) {
      content = generateFallbackContent({
        siteName: site.name,
        theme: contract.theme,
        focus: contract.focus
      });
      warning = `Falha ao usar IA, fallback aplicado: ${String(error.message || error)}`;
    }

    contract.content = content;
    const outputPath = writeDraft(site, contract);
    const siteResult = {
      siteId: site.id,
      ok: true,
      lint,
      aiUsed,
      warning,
      outputPath: path.relative(ROOT, outputPath.jsonPath),
      markdownPath: path.relative(ROOT, outputPath.markdownPath)
    };

    if (mode === "publish" && site.id === "sistemavendadireta") {
      const publishResult = publishSvdPost(ROOT, contract);
      siteResult.publishResult = publishResult;
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
