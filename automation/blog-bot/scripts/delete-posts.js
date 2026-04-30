#!/usr/bin/env node
/**
 * Remove posts especificados (diretorio, cover, contract, cards do home/blog index, sitemap entry).
 * Uso: node scripts/delete-posts.js
 */

const fs = require("node:fs");
const path = require("node:path");

const ROOT = path.resolve(__dirname, "..", "..", "..");
const CFG = JSON.parse(fs.readFileSync(path.resolve(__dirname, "..", "config", "sites.json"), "utf8"));
const SITES_BY_ID = Object.fromEntries(CFG.sites.map((s) => [s.id, s]));

const TO_DELETE = [
  { siteId: "coderush", date: "2026-04-14", slug: "coderush-ia-2026-04-14" },
  { siteId: "coderush", date: "2026-04-26", slug: "coderush-tecnologia-2026-04-26" },
  { siteId: "codafacil", date: "2026-04-14", slug: "codafacil-ia-2026-04-14" },
  { siteId: "codafacil", date: "2026-04-26", slug: "codafacil-tecnologia-2026-04-26" },
  { siteId: "fluxointeligenteia", date: "2026-04-14", slug: "fluxointeligenteia-ia-2026-04-14" },
  { siteId: "fluxointeligenteia", date: "2026-04-26", slug: "fluxointeligenteia-tecnologia-2026-04-26" },
  { siteId: "sistemavendadireta", date: "2026-04-14", slug: "sistemavendadireta-ia-2026-04-14" },
  { siteId: "sistemavendadireta", date: "2026-04-26", slug: "sistemavendadireta-tecnologia-2026-04-26" }
];

function rmrfIfExists(p) {
  if (fs.existsSync(p)) {
    fs.rmSync(p, { recursive: true, force: true });
    return true;
  }
  return false;
}

function removeFileIfExists(p) {
  if (fs.existsSync(p)) {
    fs.unlinkSync(p);
    return true;
  }
  return false;
}

function removeArticleByDataSlug(filePath, slug) {
  if (!fs.existsSync(filePath)) return false;
  const html = fs.readFileSync(filePath, "utf8");
  // capture <article ... data-blog-slug="<slug>" ...>...</article>
  const re = new RegExp(
    "\\s*<article[^>]*data-blog-slug=\"" + slug.replace(/[.*+?^${}()|[\\]\\\\]/g, "\\\\$&") + "\"[\\s\\S]*?</article>",
    "g"
  );
  const next = html.replace(re, "");
  if (next === html) return false;
  fs.writeFileSync(filePath, next);
  return true;
}

function removeFromSitemap(filePath, postPathContains) {
  if (!fs.existsSync(filePath)) return false;
  const xml = fs.readFileSync(filePath, "utf8");
  const re = new RegExp(
    "\\s*<url>\\s*<loc>[^<]*" + postPathContains.replace(/[.*+?^${}()|[\\]\\\\]/g, "\\\\$&") + "[^<]*</loc>[\\s\\S]*?</url>",
    "g"
  );
  const next = xml.replace(re, "");
  if (next === xml) return false;
  fs.writeFileSync(filePath, next);
  return true;
}

function deletePost({ siteId, date, slug }) {
  const site = SITES_BY_ID[siteId];
  if (!site) { console.log("- skip (site nao encontrado)", siteId); return; }
  const ext = site.renderExtension || "php";
  const [yyyy, mm, dd] = date.split("-");

  const postDir = path.resolve(ROOT, site.siteRoot, yyyy, mm, dd, slug);
  const coverJpg = path.resolve(ROOT, site.siteRoot, site.assets.postsDir, `${slug}.jpg`);
  const homeFile = path.resolve(ROOT, site.siteRoot, site.homePath);
  const blogIndexFile = path.resolve(ROOT, site.siteRoot, site.blogIndexPath);
  const sitemapFile = path.resolve(ROOT, site.siteRoot, site.seo.sitemapPath);

  const contractJson = path.resolve(ROOT, "automation/blog-bot/out", siteId, date, `${slug}.json`);
  const contractMd = path.resolve(ROOT, "automation/blog-bot/out", siteId, date, `${slug}.md`);

  const log = [];
  log.push(rmrfIfExists(postDir) ? `dir(${path.relative(ROOT, postDir)})` : "");
  log.push(removeFileIfExists(coverJpg) ? `cover(${path.relative(ROOT, coverJpg)})` : "");
  log.push(removeFileIfExists(contractJson) ? "contract.json" : "");
  log.push(removeFileIfExists(contractMd) ? "contract.md" : "");
  log.push(removeArticleByDataSlug(homeFile, slug) ? "home-card" : "");
  log.push(removeArticleByDataSlug(blogIndexFile, slug) ? "blog-index-card" : "");
  log.push(removeFromSitemap(sitemapFile, `/${yyyy}/${mm}/${dd}/${slug}/`) ? "sitemap" : "");

  console.log(`✓ [${siteId}] ${slug} -> ${log.filter(Boolean).join(", ") || "nothing changed"}`);
}

function main() {
  for (const item of TO_DELETE) {
    deletePost(item);
  }
}

main();
