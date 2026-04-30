#!/usr/bin/env node
/**
 * Gera UMA capa local sem postar em lugar nenhum.
 * Util pra validar visualmente o design system de um site (paleta, mascote, mood).
 *
 * Uso:
 *   node scripts/preview-cover.js --site=emergency
 *   node scripts/preview-cover.js --site=emergency --theme="due diligence imobiliaria" --angle="due diligence imobiliaria"
 *   node scripts/preview-cover.js --site=emergency --headline="Como revisar matricula em 5 passos"
 *
 * Saida: /tmp/blog-bot-cover-<siteId>-<timestamp>.jpg (caminho printado).
 */

const path = require("node:path");
const { loadEnvFiles } = require("../lib/env-loader");
const { resolveAiConfig } = require("../lib/ai-writer");
const { generateCover } = require("../lib/cover-agent");
const { pickSiteTheme, pickAngle } = require("../lib/site-strategy");

const ROOT = path.resolve(__dirname, "..", "..", "..");
const CONFIG_PATH = path.resolve(__dirname, "..", "config", "sites.json");
const cfg = require(CONFIG_PATH);

function parseArgs(argv) {
  const out = {};
  for (const arg of argv.slice(2)) {
    const m = arg.match(/^--([^=]+)=(.*)$/);
    if (m) out[m[1]] = m[2];
  }
  return out;
}

async function main() {
  loadEnvFiles(ROOT);
  const aiConfig = resolveAiConfig(process.env);
  if (!aiConfig) {
    throw new Error("Sem aiConfig — falta API_OPENROUTER no .env");
  }

  const args = parseArgs(process.argv);
  const siteId = args.site || "emergency";
  const site = cfg.sites.find((s) => s.id === siteId);
  if (!site) throw new Error(`Site nao encontrado em sites.json: ${siteId}`);

  const today = new Date().toISOString().slice(0, 10);
  const theme = args.theme || pickSiteTheme(site.id, today);
  const angle = args.angle || pickAngle(site.id, today);
  const headline = args.headline
    || `Preview ${site.name}: ${theme}`;
  const summary = args.summary
    || `Preview de capa para validar design system de ${site.name}, com foco em ${theme}.`;

  const slug = `preview-${site.id}-${Date.now()}`;
  const targetPath = path.join("/tmp", `blog-bot-cover-${site.id}-${Date.now()}.jpg`);

  const contract = {
    slug,
    theme,
    angle,
    content: { headline, summary }
  };

  console.log(`Site:     ${site.name} (${site.id})`);
  console.log(`Tema:     ${theme}`);
  console.log(`Angulo:   ${angle}`);
  console.log(`Headline: ${headline}`);
  console.log(`Saida:    ${targetPath}`);
  console.log("Gerando...\n");

  const result = await generateCover({ aiConfig, site, contract, targetPath });

  console.log(`\nOK  source: ${result.source}`);
  console.log(`alt: ${result.altText || "(sem alt)"}`);
  console.log(`prompt: ${result.prompt?.slice(0, 300) || "(sem prompt)"}...`);
  console.log(`\nAbra: ${targetPath}`);
}

main().catch((err) => {
  console.error("FALHA:", err.message || err);
  process.exit(1);
});
