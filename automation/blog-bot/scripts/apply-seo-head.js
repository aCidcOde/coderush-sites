#!/usr/bin/env node
/**
 * Atualiza <head> dos posts existentes:
 * - <title>, og:title, twitter:title -> seoTitle truncado
 * - injeta favicon, alternate icon, apple-touch-icon (se ausentes)
 * - injeta og:site_name (se ausente)
 *
 * Le contracts JSON de automation/blog-bot/out/<siteId>/<date>/<slug>.json.
 */

const fs = require("node:fs");
const path = require("node:path");
const { buildSeoTitle, smartTruncate } = require("../lib/publisher");

function decodeHtml(s) {
  return String(s || "")
    .replace(/&amp;/g, "&")
    .replace(/&quot;/g, '"')
    .replace(/&lt;/g, "<")
    .replace(/&gt;/g, ">")
    .replace(/&#39;/g, "'");
}

function extractH1(content) {
  const m = content.match(/<h1[^>]*>([\s\S]*?)<\/h1>/i);
  if (!m) return "";
  return decodeHtml(m[1].replace(/<[^>]*>/g, "")).trim();
}

function extractDescriptionMeta(content) {
  const m = content.match(/<meta\s+name="description"\s+content="([^"]*)"/i);
  return m ? decodeHtml(m[1]) : "";
}

const ROOT = path.resolve(__dirname, "..", "..", "..");
const CFG = JSON.parse(fs.readFileSync(path.resolve(__dirname, "..", "config", "sites.json"), "utf8"));
const SITES_BY_ID = Object.fromEntries(CFG.sites.map((s) => [s.id, s]));

function escAttr(value) {
  return String(value || "")
    .replace(/&/g, "&amp;")
    .replace(/"/g, "&quot;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;");
}

function findContract(siteId, slug) {
  const baseDir = path.resolve(ROOT, "automation/blog-bot/out", siteId);
  if (!fs.existsSync(baseDir)) return null;
  for (const dateEntry of fs.readdirSync(baseDir)) {
    const candidate = path.join(baseDir, dateEntry, `${slug}.json`);
    if (fs.existsSync(candidate)) {
      return JSON.parse(fs.readFileSync(candidate, "utf8"));
    }
  }
  return null;
}

function relativeRootFromPostPath(filePath) {
  // post path: <root>/<site?>/YYYY/MM/DD/<slug>/index.<ext>
  const rel = path.relative(ROOT, filePath);
  const depth = rel.split(path.sep).length - 1; // exclude file itself
  // For coderush hub: 2026/MM/DD/slug/index.php -> depth 4 -> ../../../../
  // For other sites: site/2026/MM/DD/slug/index.php -> depth 5 -> ../../../../
  // The relativeRoot must reach the SITE root, not repo root.
  // Convention used by publisher.js: "../../../../"
  return "../../../../";
}

function siteIdFromPostPath(filePath) {
  const rel = path.relative(ROOT, filePath);
  const first = rel.split(path.sep)[0];
  if (first === "codafacil") return "codafacil";
  if (first === "fluxointeligenteia") return "fluxointeligenteia";
  if (first === "sistemavendadireta") return "sistemavendadireta";
  return "coderush";
}

function slugFromPostPath(filePath) {
  const rel = path.relative(ROOT, filePath);
  const parts = rel.split(path.sep);
  // ...YYYY/MM/DD/<slug>/index.<ext>
  return parts[parts.length - 2];
}

function updateHead(content, contract, site, relativeRoot) {
  const seoTitle = buildSeoTitle(contract, site);
  const escTitle = escAttr(seoTitle);
  let updated = content;

  // <title>...</title>
  updated = updated.replace(/<title>[^<]*<\/title>/, `<title>${escTitle}</title>`);
  // og:title
  updated = updated.replace(
    /<meta\s+property="og:title"\s+content="[^"]*"\s*\/?>/,
    `<meta property="og:title" content="${escTitle}" />`
  );
  // twitter:title
  updated = updated.replace(
    /<meta\s+name="twitter:title"\s+content="[^"]*"\s*\/?>/,
    `<meta name="twitter:title" content="${escTitle}" />`
  );

  // og:site_name (insere apos og:url se ausente)
  if (!/property="og:site_name"/.test(updated)) {
    updated = updated.replace(
      /(<meta\s+property="og:url"\s+content="[^"]*"\s*\/?>)/,
      `$1\n  <meta property="og:site_name" content="${escAttr(site.name)}" />`
    );
  }

  // favicons (insere apos canonical se ausentes)
  if (!/rel="icon"\s+type="image\/svg\+xml"/.test(updated)) {
    const block = [
      `<link rel="icon" type="image/svg+xml" href="${relativeRoot}favicon.svg" />`,
      `<link rel="alternate icon" href="${relativeRoot}favicon.ico" />`,
      `<link rel="apple-touch-icon" sizes="180x180" href="${relativeRoot}apple-touch-icon.png" />`
    ].join("\n  ");
    updated = updated.replace(
      /(<link\s+rel="canonical"\s+href="[^"]*"\s*\/?>)/,
      `$1\n  ${block}`
    );
  }

  return { content: updated, seoTitle };
}

function listPosts() {
  const out = [];
  function walk(dir, ext) {
    if (!fs.existsSync(dir)) return;
    for (const entry of fs.readdirSync(dir)) {
      const full = path.join(dir, entry);
      const stat = fs.statSync(full);
      if (stat.isDirectory()) walk(full, ext);
      else if (entry === `index.${ext}`) out.push(full);
    }
  }
  for (const site of CFG.sites) {
    const root = path.resolve(ROOT, site.siteRoot);
    const ext = site.renderExtension || "php";
    if (!fs.existsSync(root)) continue;
    for (const yearEntry of fs.readdirSync(root)) {
      if (!/^\d{4}$/.test(yearEntry)) continue;
      walk(path.join(root, yearEntry), ext);
    }
  }
  return out;
}

function main() {
  const posts = listPosts();
  let updated = 0;
  let skipped = 0;
  for (const filePath of posts) {
    const siteId = siteIdFromPostPath(filePath);
    const slug = slugFromPostPath(filePath);
    const site = SITES_BY_ID[siteId];
    if (!site) { skipped++; continue; }
    const orig = fs.readFileSync(filePath, "utf8");
    let contract = findContract(siteId, slug);
    if (!contract) {
      // legacy fallback: monta contract sintetico do HTML
      const headline = extractH1(orig);
      if (!headline) { skipped++; continue; }
      contract = {
        slug,
        description: extractDescriptionMeta(orig),
        content: { headline }
      };
    }
    const relativeRoot = relativeRootFromPostPath(filePath);
    const { content, seoTitle } = updateHead(orig, contract, site, relativeRoot);
    if (content !== orig) {
      fs.writeFileSync(filePath, content, "utf8");
      updated++;
      console.log(`✓ [${siteId}] ${slug} -> "${seoTitle}" (${seoTitle.length}c)`);
    }
  }
  console.log(`\n${updated} atualizados, ${skipped} sem contract.`);
}

main();
