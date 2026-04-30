#!/usr/bin/env node

const fs = require("node:fs");
const path = require("node:path");
const crypto = require("node:crypto");
const { execSync } = require("node:child_process");
const { loadEnvFiles } = require("./lib/env-loader");
const { pickSiteTheme, pickAngle, siteProfile, sitePromptStyle } = require("./lib/site-strategy");
const {
  resolveAiConfig,
  generateWithOpenAI,
  generateWithOpenRouter,
  generateFallbackContent
} = require("./lib/ai-writer");
const { gatherResearch, uniqueLinks } = require("./lib/research");
const { publishSitePost } = require("./lib/publisher");
const { publishApiPost } = require("./lib/api-publisher");
const { refreshAllPosts } = require("./lib/related-refresher");

const ROOT = path.resolve(__dirname, "..", "..");
const CONFIG_PATH = path.resolve(__dirname, "config", "sites.json");
const REPORTS_DIR = path.resolve(__dirname, "reports");

const DEFAULT_SOURCES = ["https://openai.com/news/", "https://github.blog/"];
const DUPLICATION_THRESHOLD = 0.6;

const FOCUS_SYNONYMS = {
  ia: "ia ai inteligencia artificial machine learning llm gpt claude agente agent",
  php: "php laravel symfony composer phpunit",
  tecnologia: "tecnologia technology engineering platform cloud devops automacao automation"
};

function expandFocus(focus) {
  return FOCUS_SYNONYMS[focus] || focus;
}

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

const SLUG_STOPWORDS = new Set([
  "a","o","as","os","da","do","das","dos","de","e","em","na","no","nas","nos",
  "ao","aos","um","uma","uns","umas","para","por","com","sem","sobre","entre",
  "que","como","se","ou","esse","essa","esses","essas","este","esta","estes","estas",
  "ja","ainda","sua","suas","seu","seus","ser","foi","sera","tambem"
]);

function slugify(value) {
  return value
    .toLowerCase()
    .normalize("NFD")
    .replace(/[̀-ͯ]/g, "")
    .replace(/[^a-z0-9\s-]/g, "")
    .trim()
    .replace(/\s+/g, "-")
    .replace(/-+/g, "-");
}

function slugFromHeadline(headline, { maxLen = 70 } = {}) {
  const base = slugify(headline);
  if (!base) return "";
  const filtered = base
    .split("-")
    .filter((part) => part && !SLUG_STOPWORDS.has(part))
    .join("-");
  const candidate = filtered || base;
  if (candidate.length <= maxLen) return candidate;
  const parts = candidate.split("-");
  let result = "";
  for (const part of parts) {
    const next = result ? `${result}-${part}` : part;
    if (next.length > maxLen) break;
    result = next;
  }
  return result || candidate.slice(0, maxLen);
}

function buildPostContract(site, focus, date, angle) {
  const theme = pickSiteTheme(site.id, date);
  const angleLabel = angle || theme;
  const title = `${site.name}: ${focus.toUpperCase()} aplicado a ${angleLabel}`;
  const slug = slugify(`${site.id}-${focus}-${date}`);
  const description = `Atualização semanal de ${focus} para ${site.name}, com foco em ${theme}.`;

  return {
    date,
    siteId: site.id,
    postType: site.postType,
    focus,
    theme,
    angle: angle || "",
    slug,
    title,
    description,
    sources: [],
    status: "draft",
    generatedAt: new Date().toISOString()
  };
}

function buildAiPrompt({ site, contract, research = [] }) {
  const style = sitePromptStyle(site);

  const researchBlock = research.length
    ? [
        "",
        "Conteudo recente do setor (use como referencia factual e cite ao menos 2 com link em markdown):",
        ...research.map((item, idx) => {
          const lines = [`${idx + 1}. ${item.title}`];
          if (item.link) lines.push(`   Link: ${item.link}`);
          if (item.summary) lines.push(`   Resumo: ${item.summary.slice(0, 240)}`);
          return lines.join("\n");
        })
      ].join("\n")
    : "";

  const bannedLine = style.bannedWords?.length
    ? `Evite estas palavras/expressoes: ${style.bannedWords.join(", ")}.`
    : "";

  const differentiatorsLine = style.differentiators?.length
    ? `Diferenciais a refletir no conteudo: ${style.differentiators.join("; ")}.`
    : "";

  const keywords = style.keywords || { primary: [], secondary: [], longTail: [] };
  const keywordsBlock = (keywords.primary?.length || keywords.secondary?.length || keywords.longTail?.length)
    ? [
        "",
        "Palavras-chave SEO para usar com naturalidade (sem stuffing):",
        keywords.primary?.length
          ? `- Primarias (cada uma 2-4 vezes ao longo do post, incluindo headline e summary quando fizer sentido): ${keywords.primary.join("; ")}`
          : "",
        keywords.secondary?.length
          ? `- Secundarias (cada uma 1-2 vezes): ${keywords.secondary.join("; ")}`
          : "",
        keywords.longTail?.length
          ? `- Long-tail (use 1-2 destas como pergunta ou frase ao longo do texto): ${keywords.longTail.join("; ")}`
          : ""
      ]
        .filter(Boolean)
        .join("\n")
    : "";

  const ctaLabel = style.cta.label;
  const ctaPath = style.cta.path;

  const lines = [
    `Site: ${site.name}`,
    `Tipo de post: ${style.postType}`,
    `Publico: ${style.audience}`,
    `Oferta da empresa: ${style.offering}`,
    `Tom prosa (sections type=prose): ${style.tone}`,
    `Tom callout/cta-inline: ${style.casualTone}`,
    `Tema da semana: ${contract.theme}`,
    `Angulo desta edicao: ${contract.angle}`,
    `Foco rotativo: ${contract.focus}`,
    differentiatorsLine,
    bannedLine,
    keywordsBlock,
    "",
    "FORMATO DO POST (modelo Hibrido AEO + Consultivo):",
    "O leitor cai aqui via Google. answerBox no topo responde direto a pergunta dele. Depois o post aprofunda em sections tipadas, com 1 callout e 1 cta-inline distribuidos no fluxo. Fecha com FAQ estruturado.",
    "",
    "Retorne JSON valido EXATAMENTE neste schema:",
    `{
  "eyebrow": "string curta (2-4 palavras, ex: 'Guia pratico', 'Consultoria tecnica')",
  "headline": "string (50-65 chars, informativo, sem pitch)",
  "summary": "string (140 chars max, contexto curto que aparece sob o titulo)",
  "answerBox": {
    "question": "string (a pergunta exata que o leitor faria no Google)",
    "answer": "string (40-50 palavras, resposta direta e completa)"
  },
  "tldr": ["bullet 1 (8-12 palavras)", "bullet 2", "bullet 3"],
  "sections": [
    { "type": "prose", "title": "string", "body": "string (90-130 palavras)" },
    { "type": "list", "title": "string", "items": ["item 1 (8-15 palavras)", "...", "..."] },
    { "type": "callout", "body": "string (40-60 palavras, tom levemente descontraido)", "ctaLabel": "${ctaLabel}", "ctaHref": "${ctaPath}" },
    { "type": "prose", "title": "string", "body": "string (110-150 palavras)" },
    { "type": "cta-inline", "body": "string (uma frase curta ate ~20 palavras)", "ctaLabel": "${ctaLabel}", "ctaHref": "${ctaPath}" },
    { "type": "prose", "title": "string", "body": "string (90-130 palavras, trade-offs ou o que evitar)" }
  ],
  "faq": [
    { "q": "pergunta curta", "a": "resposta 30-50 palavras" },
    { "q": "...", "a": "..." },
    { "q": "...", "a": "..." }
  ]
}`,
    "",
    "REGRAS POR CAMPO:",
    "- answerBox.answer: 40-50 palavras, completas, autossuficientes. Vai virar meta description.",
    "- tldr: exatamente 3 bullets. Cada bullet entrega UMA ideia, nao usa virgula serial.",
    "- sections: exatamente 6 itens, na sequencia [prose, list, callout, prose, cta-inline, prose]. Nao reordene.",
    "- list.items: 4-6 itens curtos. Mostre criterios, sinais, passos OU armadilhas.",
    `- callout.body: tom levemente descontraido. Pode usar "ja estruturamos isso em N clientes", "vale uma conversa", "se faz sentido pro seu cenario". NUNCA "a gente", girias, CAPS ou exclamacao.`,
    `- cta-inline.body: uma frase de transicao natural. Nunca "clique aqui", nunca "agora".`,
    "- faq: 3-5 perguntas. As respostas devem ser autossuficientes (LLMs vao extrair sem contexto).",
    "- Quando houver conteudo recente, cite ao menos 2 fontes em markdown ([texto](url)) integradas ao body de uma section type=prose.",
    "",
    "DIRETRIZES DE CONVERSAO:",
    "- O conteudo principal e educacional/consultivo. callout + cta-inline + faq sao pontos de conversao sutis, nunca interruptivos.",
    `- Use o ctaLabel "${ctaLabel}" e ctaHref "${ctaPath}" exatamente como dado no schema.`,
    `- Pode mencionar ${site.name} 1-2 vezes no body de prose, como transicao natural ("nesse tipo de cenario, ${site.name} costuma...").`,
    "- Headline informa sobre o tema, nao vende. Sem 'transforme ja', 'nao perca', 'clique aqui'.",
    researchBlock,
    "",
    "REGRAS ADICIONAIS:",
    style.constraints.map((item) => `- ${item}`).join("\n")
  ];

  return lines.filter((line) => line !== "").join("\n");
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
    "## Fontes",
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
    const ok = draft?.content?.headline && Array.isArray(draft?.content?.sections);
    if (ok) return draft;
  } catch (error) {
    return null;
  }

  return null;
}

async function generateContent(aiConfig, site, prompt, contract, researchItems) {
  const profile = siteProfile(site.id) || {};
  const fallbackArgs = {
    siteName: site.name,
    theme: contract.theme,
    focus: contract.focus,
    profile,
    angle: contract.angle,
    research: researchItems
  };

  if (!aiConfig) {
    return {
      content: generateFallbackContent(fallbackArgs),
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
      content: generateFallbackContent(fallbackArgs),
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

function bigrams(text) {
  const tokens = String(text || "")
    .toLowerCase()
    .replace(/[^a-z0-9\sçáàâãéêíóôõúü]/giu, " ")
    .split(/\s+/)
    .filter(Boolean);
  const grams = new Set();
  for (let i = 0; i < tokens.length - 1; i++) {
    grams.add(`${tokens[i]} ${tokens[i + 1]}`);
  }
  return grams;
}

function jaccard(a, b) {
  if (a.size === 0 || b.size === 0) return 0;
  let intersection = 0;
  for (const item of a) {
    if (b.has(item)) intersection++;
  }
  return intersection / (a.size + b.size - intersection);
}

function flattenContent(content) {
  if (!content) return "";
  const parts = [content.headline, content.summary];
  if (content.answerBox) {
    parts.push(content.answerBox.question || "", content.answerBox.answer || "");
  }
  if (Array.isArray(content.tldr)) parts.push(content.tldr.join(" "));
  for (const section of content.sections || []) {
    if (!section) continue;
    parts.push(section.title || "", section.body || "");
    if (Array.isArray(section.items)) parts.push(section.items.join(" "));
  }
  for (const item of content.faq || []) {
    parts.push(item?.q || "", item?.a || "");
  }
  return parts.filter(Boolean).join(" ");
}

function detectDuplications(documents) {
  const docs = documents
    .filter((doc) => doc.content)
    .map((doc) => ({ siteId: doc.siteId, grams: bigrams(flattenContent(doc.content)) }));

  const findings = [];
  for (let i = 0; i < docs.length; i++) {
    for (let j = i + 1; j < docs.length; j++) {
      const score = jaccard(docs[i].grams, docs[j].grams);
      if (score >= DUPLICATION_THRESHOLD) {
        findings.push({
          a: docs[i].siteId,
          b: docs[j].siteId,
          similarity: Number(score.toFixed(3))
        });
      }
    }
  }
  return findings;
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

  const documentsForDuplication = [];

  for (const site of selectedSites) {
    if (!site.enabled) {
      report.sites.push({ siteId: site.id, skipped: true, reason: "site disabled" });
      continue;
    }

    const angle = pickAngle(site.id, date);
    let contract = buildPostContract(site, focus, date, angle);
    const existingDraft = loadExistingDraft(site, contract);

    let aiUsed = false;
    let aiProvider = "fallback";
    let warning = null;
    let research = { items: [], errors: [], skipped: true };

    if (existingDraft) {
      contract = existingDraft;
      aiProvider = "existing-draft";
      warning = "Rascunho existente reutilizado para manter o seed consistente.";
    } else {
      research = await gatherResearch({
        feeds: site.research?.feeds || [],
        focus: expandFocus(focus),
        maxItems: site.research?.maxItems || 6,
        sinceDays: site.research?.sinceDays || 21
      });

      const generation = await generateContent(
        aiConfig,
        site,
        buildAiPrompt({ site, contract, research: research.items }),
        contract,
        research.items
      );
      contract.content = generation.content;
      contract.sources = research.items.length
        ? uniqueLinks(research.items, 5)
        : DEFAULT_SOURCES;
      aiUsed = generation.aiUsed;
      aiProvider = generation.aiProvider;
      warning = generation.warning;

      const headlineSlug = slugFromHeadline(generation.content?.headline || "");
      if (headlineSlug && headlineSlug !== contract.slug) {
        contract.slug = headlineSlug;
      }
    }

    const outputPath = writeDraft(site, contract);
    documentsForDuplication.push({ siteId: site.id, content: contract.content });

    const siteResult = {
      siteId: site.id,
      ok: true,
      aiUsed,
      aiProvider,
      warning,
      angle: contract.angle,
      theme: contract.theme,
      research: {
        used: research.items.length,
        skipped: research.skipped,
        errors: research.errors
      },
      outputPath: path.relative(ROOT, outputPath.jsonPath).replace(/\\/g, "/"),
      markdownPath: path.relative(ROOT, outputPath.markdownPath).replace(/\\/g, "/")
    };

    if (mode === "publish") {
      if (site.target === "api") {
        try {
          const apiResult = await publishApiPost(ROOT, site, contract, aiConfig);
          siteResult.publishResult = {
            target: "api",
            apiStatus: apiResult.apiStatus,
            apiUrl: apiResult.apiUrl,
            apiId: apiResult.apiId,
            apiPublishedAt: apiResult.apiPublishedAt,
            coverPath: path
              .relative(ROOT, apiResult.coverPath)
              .replace(/\\/g, "/"),
            coverSize: apiResult.coverSize,
            coverSource: apiResult.coverSource,
            coverAlt: apiResult.coverAlt,
            coverLeakage: apiResult.coverLeakage
          };
          if (apiResult.warning) {
            siteResult.warning = siteResult.warning
              ? `${siteResult.warning} ${apiResult.warning}`
              : apiResult.warning;
          }
          siteResult.lint = { ok: true, skipped: true, targets: [] };
        } catch (error) {
          siteResult.ok = false;
          siteResult.publishResult = {
            target: "api",
            error: error.message || String(error)
          };
          siteResult.lint = { ok: true, skipped: true, targets: [] };
        }
      } else {
        const publishResult = await publishSitePost(ROOT, site, contract, aiConfig);
        const lint = lintPhpFiles(publishResult.phpLintTargets);
        siteResult.publishResult = {
          target: "files",
          postPath: publishResult.postPath,
          homeUpdated: publishResult.homeUpdated,
          blogUpdated: publishResult.blogUpdated,
          sitemapUpdated: publishResult.sitemapUpdated,
          robotsUpdated: publishResult.robotsUpdated,
          coverSource: publishResult.coverSource,
          coverAlt: publishResult.coverAlt,
          coverLeakage: publishResult.coverLeakage
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
    }

    report.sites.push(siteResult);
  }

  const duplications = detectDuplications(documentsForDuplication);
  report.duplications = duplications;
  if (duplications.length > 0) {
    console.warn(
      `Atencao: ${duplications.length} par(es) de sites com similaridade >= ${DUPLICATION_THRESHOLD}.`
    );
  }

  if (mode === "publish") {
    try {
      const refresh = refreshAllPosts(ROOT, config);
      report.relatedRefresh = refresh;
      if (refresh.errors.length > 0) {
        console.warn(`Refresher de related encontrou ${refresh.errors.length} erro(s).`);
      }
    } catch (error) {
      report.relatedRefresh = { error: error.message || String(error) };
      console.warn(`Refresher de related falhou: ${error.message || error}`);
    }
  }

  const generatedSites = report.sites.filter((site) => !site.skipped);
  const fallbackSites = generatedSites.filter((site) => site.aiProvider === "fallback");
  const allFallback =
    aiConfig && generatedSites.length > 0 && fallbackSites.length === generatedSites.length;

  report.fallbackCount = fallbackSites.length;
  report.allFallback = Boolean(allFallback);
  report.finishedAt = new Date().toISOString();
  report.ok =
    report.sites.every((site) => site.ok || site.skipped) && !allFallback;

  const reportPath = path.resolve(REPORTS_DIR, `run-${date}-${mode}.json`);
  fs.writeFileSync(reportPath, JSON.stringify(report, null, 2), "utf8");

  if (allFallback) {
    console.error(
      "Todos os sites cairam em fallback (IA indisponivel). Abortando para nao publicar conteudo generico."
    );
    console.error("Relatorio:", path.relative(ROOT, reportPath));
    process.exit(1);
  }

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
