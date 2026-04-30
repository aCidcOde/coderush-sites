#!/usr/bin/env node
/**
 * Reescreve o cabecalho do <article> dos posts existentes para o novo
 * formato banner (figure + overlay com label, h1 e summary).
 *
 * - Le headline/summary/coverAlt do contract JSON, com fallback para o HTML atual.
 * - Mantem as <section>s de conteudo intactas.
 * - Mantem CTA, "Veja mais no blog" e blocos relacionados (fora do article).
 */

const fs = require("node:fs");
const path = require("node:path");

const ROOT = path.resolve(__dirname, "..", "..", "..");
const CFG = JSON.parse(fs.readFileSync(path.resolve(__dirname, "..", "config", "sites.json"), "utf8"));
const SITES_BY_ID = Object.fromEntries(CFG.sites.map((s) => [s.id, s]));

const SITE_HEADING_BAR = {
  coderush: "bg-blue-400",
  codafacil: "bg-sky-300",
  fluxointeligenteia: "bg-emerald-400",
  sistemavendadireta: "bg-white"
};

const SITE_ARTICLE_LABEL = {
  coderush: "Blog CodeRush",
  codafacil: "Blog Codafacil.dev",
  fluxointeligenteia: "Blog FluxoInteligente IA",
  sistemavendadireta: "Blog SVD"
};

function esc(v) {
  return String(v || "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function decode(s) {
  return String(s || "")
    .replace(/&amp;/g, "&")
    .replace(/&quot;/g, '"')
    .replace(/&lt;/g, "<")
    .replace(/&gt;/g, ">");
}

function brDate(date) {
  const m = date.match(/^(\d{4})-(\d{2})-(\d{2})$/);
  if (!m) return date;
  return `${m[3]}/${m[2]}/${m[1]}`;
}

function siteIdFromPath(filePath) {
  const rel = path.relative(ROOT, filePath);
  const first = rel.split(path.sep)[0];
  if (first === "codafacil") return "codafacil";
  if (first === "fluxointeligenteia") return "fluxointeligenteia";
  if (first === "sistemavendadireta") return "sistemavendadireta";
  return "coderush";
}

function slugFromPath(filePath) {
  const parts = path.relative(ROOT, filePath).split(path.sep);
  return parts[parts.length - 2];
}

function dateFromPath(filePath) {
  const parts = path.relative(ROOT, filePath).split(path.sep);
  // either YYYY/MM/DD/slug/index.ext (coderush) or site/YYYY/MM/DD/slug/index.ext
  const startsWithYear = /^\d{4}$/.test(parts[0]);
  const idx = startsWithYear ? 0 : 1;
  return `${parts[idx]}-${parts[idx + 1]}-${parts[idx + 2]}`;
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

function extractH1(html) {
  const m = html.match(/<h1[^>]*>([\s\S]*?)<\/h1>/i);
  return m ? decode(m[1].replace(/<[^>]*>/g, "")).trim() : "";
}

function extractSummary(html, articleStart) {
  // primeiro <p> apos h1, com classe que sugere summary (text-base leading-7)
  const slice = html.slice(articleStart);
  const m = slice.match(/<h1[^>]*>[\s\S]*?<\/h1>\s*<p[^>]*>([\s\S]*?)<\/p>/i);
  return m ? decode(m[1].replace(/<[^>]*>/g, "")).trim() : "";
}

function extractAlt(html, articleStart) {
  const slice = html.slice(articleStart);
  const m = slice.match(/<img[^>]*alt="([^"]*)"/i);
  return m ? decode(m[1]) : "";
}

function extractSlug(html, articleStart) {
  const slice = html.slice(articleStart);
  const m = slice.match(/<img[^>]*src="[^"]*\/imagens\/posts\/([^"\.]+)\.jpg"/i);
  return m ? m[1] : "";
}

function extractRelativeRootFromImg(html, articleStart) {
  const slice = html.slice(articleStart);
  const m = slice.match(/<img[^>]*src="([^"]*)imagens\/posts\//i);
  return m ? m[1] : "../../../../";
}

function buildBannerHeader({ slug, headline, summary, coverAlt, label, headingBarClass, date, relativeRoot }) {
  return [
    '<article class="mt-5 overflow-hidden rounded-3xl border border-white/15 bg-white/5">',
    '      <figure class="relative">',
    `        <img src="${relativeRoot}imagens/posts/${slug}.jpg" alt="${esc(coverAlt || headline)}" class="block aspect-[1200/630] w-full object-cover" width="1200" height="630" loading="eager" decoding="async" />`,
    '        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/85 via-black/45 to-transparent" aria-hidden="true"></div>',
    '        <figcaption class="absolute inset-x-0 bottom-0 p-5 sm:p-8 lg:p-10">',
    `          <span class="block h-1 w-12 rounded-full ${headingBarClass}" aria-hidden="true"></span>`,
    `          <p class="mt-3 text-[11px] font-semibold uppercase tracking-[0.22em] text-white/85 sm:text-xs">${esc(label)} • ${brDate(date)}</p>`,
    `          <h1 class="mt-2 text-2xl font-semibold leading-tight text-white sm:text-3xl lg:text-4xl">${esc(headline)}</h1>`,
    `          <p class="mt-3 max-w-3xl text-sm leading-6 text-white/90 sm:text-base sm:leading-7">${esc(summary)}</p>`,
    "        </figcaption>",
    "      </figure>",
    '      <div class="p-5 sm:p-8">'
  ].join("\n");
}

function rewritePost(filePath) {
  const html = fs.readFileSync(filePath, "utf8");

  const articleStart = html.indexOf('<article class="mt-5');
  if (articleStart === -1) return { skipped: true, reason: "no <article> hero" };

  // Identifica fim do header (apos <img>) e comeco das sections
  const slice = html.slice(articleStart);
  const imgMatch = slice.match(/<img[^>]*\/?>/);
  if (!imgMatch) return { skipped: true, reason: "no <img>" };
  const imgEndAbs = articleStart + slice.indexOf(imgMatch[0]) + imgMatch[0].length;

  // ja convertido?
  if (slice.includes("<figcaption")) return { skipped: true, reason: "already banner" };

  // proximo conteudo: pode ser <section ...> de conteudo, ou ja </article>
  const restAfterImg = html.slice(imgEndAbs);
  const sectionStart = restAfterImg.search(/<section[^>]*class="mt-8"[^>]*>/);
  const articleCloseRel = restAfterImg.indexOf("</article>");
  if (articleCloseRel === -1) return { skipped: true, reason: "no </article>" };
  const sectionsStartAbs = sectionStart === -1
    ? imgEndAbs + articleCloseRel
    : imgEndAbs + sectionStart;
  const articleCloseAbs = imgEndAbs + articleCloseRel;

  const sectionsHtml = html.slice(sectionsStartAbs, articleCloseAbs).trim();

  const siteId = siteIdFromPath(filePath);
  const slug = slugFromPath(filePath);
  const date = dateFromPath(filePath);
  const contract = findContract(siteId, slug);

  const headline = (contract?.content?.headline) || extractH1(html);
  const summary = (contract?.content?.summary) || extractSummary(html, articleStart);
  const coverAlt = (contract?.coverAlt) || extractAlt(html, articleStart) || headline;
  const slugForImg = extractSlug(html, articleStart) || slug;
  const relativeRoot = extractRelativeRootFromImg(html, articleStart);

  const newHeader = buildBannerHeader({
    slug: slugForImg,
    headline,
    summary,
    coverAlt,
    label: SITE_ARTICLE_LABEL[siteId],
    headingBarClass: SITE_HEADING_BAR[siteId],
    date,
    relativeRoot
  });

  // Monta novo article: header + sections + close div + close article
  const newArticle = `${newHeader}\n${sectionsHtml ? sectionsHtml + "\n" : ""}      </div>\n    </article>`;

  const updated = html.slice(0, articleStart) + newArticle + html.slice(articleCloseAbs + "</article>".length);
  if (updated === html) return { skipped: true, reason: "no change" };
  fs.writeFileSync(filePath, updated);
  return { skipped: false, headline, summary };
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
  const onlyOne = process.argv.includes("--one");
  const posts = listPosts();
  let changed = 0;
  let skipped = 0;
  for (const p of posts) {
    const r = rewritePost(p);
    if (r.skipped) {
      skipped++;
    } else {
      changed++;
      console.log("✓", path.relative(ROOT, p), "—", r.headline.slice(0, 60));
      if (onlyOne) break;
    }
  }
  console.log(`\n${changed} convertidos, ${skipped} pulados.`);
}

main();
