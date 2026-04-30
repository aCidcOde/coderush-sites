#!/usr/bin/env node
/**
 * Renderiza um contract JSON em HTML local pra inspecao visual sem publicar.
 * Uso:
 *   node scripts/render-preview.js                                  # gera fallback do site coderush
 *   node scripts/render-preview.js --site=fluxointeligenteia        # gera fallback de outro site
 *   node scripts/render-preview.js path/to/contract.json            # renderiza contract real
 *
 * Saida: /tmp/blog-bot-preview-<siteId>-<slug>.html (caminho printado no stdout).
 */

const fs = require("node:fs");
const path = require("node:path");

const ROOT = path.resolve(__dirname, "..", "..", "..");
const CONFIG_PATH = path.resolve(__dirname, "..", "config", "sites.json");
const cfg = JSON.parse(fs.readFileSync(CONFIG_PATH, "utf8"));

const { generateFallbackContent } = require("../lib/ai-writer");
const { siteProfile, pickSiteTheme, pickAngle } = require("../lib/site-strategy");
const { buildPostTemplate } = require("../lib/publisher");

function parseArgs(argv) {
  const out = { siteId: null, contractPath: null };
  for (const arg of argv.slice(2)) {
    if (arg.startsWith("--site=")) out.siteId = arg.split("=", 2)[1];
    else if (!arg.startsWith("--")) out.contractPath = arg;
  }
  return out;
}

function buildFallbackContract(site) {
  const profile = siteProfile(site.id);
  const today = new Date().toISOString().slice(0, 10);
  const focus = "ia";
  const theme = pickSiteTheme(site.id, today);
  const angle = pickAngle(site.id, today);
  const fallback = generateFallbackContent({
    siteName: site.name,
    theme,
    focus,
    profile,
    angle,
    research: []
  });
  return {
    date: today,
    siteId: site.id,
    postType: site.postType,
    focus,
    theme,
    angle,
    slug: `${site.id}-preview-${today}`,
    title: fallback.headline,
    description: fallback.summary,
    sources: [],
    status: "draft",
    generatedAt: new Date().toISOString(),
    content: fallback,
    coverAlt: fallback.headline
  };
}

function main() {
  const { siteId, contractPath } = parseArgs(process.argv);
  let contract;
  let site;

  if (contractPath) {
    const abs = path.resolve(process.cwd(), contractPath);
    contract = JSON.parse(fs.readFileSync(abs, "utf8"));
    site = cfg.sites.find((s) => s.id === contract.siteId);
    if (!site) throw new Error(`Site nao encontrado para contract: ${contract.siteId}`);
  } else {
    const targetId = siteId || "coderush";
    site = cfg.sites.find((s) => s.id === targetId);
    if (!site) throw new Error(`Site nao encontrado: ${targetId}`);
    contract = buildFallbackContract(site);
  }

  const html = buildPostTemplate(ROOT, site, contract, []);
  const outPath = path.join("/tmp", `blog-bot-preview-${site.id}-${contract.slug}.html`);
  fs.writeFileSync(outPath, html, "utf8");
  console.log(outPath);
}

main();
