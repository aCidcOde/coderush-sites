#!/usr/bin/env node

const fs = require("node:fs");
const path = require("node:path");

const ROOT = path.resolve(__dirname, "..", "..", "..");
const CONFIG_PATH = path.resolve(__dirname, "..", "config", "sites.json");

const SLUG_STOPWORDS = new Set([
  "a","o","as","os","da","do","das","dos","de","e","em","na","no","nas","nos",
  "ao","aos","um","uma","uns","umas","para","por","com","sem","sobre","entre",
  "que","como","se","ou","esse","essa","esses","essas","este","esta","estes","estas",
  "ja","ainda","sua","suas","seu","seus","ser","foi","sera","tambem"
]);

function slugify(value) {
  return String(value || "")
    .toLowerCase()
    .normalize("NFD")
    .replace(/[̀-ͯ]/g, "")
    .replace(/[^a-z0-9\s-]/g, "")
    .trim()
    .replace(/\s+/g, "-")
    .replace(/-+/g, "-");
}

function slugFromHeadline(headline, maxLen = 70) {
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

function escapeRegex(value) {
  return String(value).replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
}

function replaceAll(text, oldStr, newStr) {
  return text.replace(new RegExp(escapeRegex(oldStr), "g"), newStr);
}

function maybeUpdateFile(filePath, oldSlug, newSlug) {
  if (!fs.existsSync(filePath)) return false;
  const content = fs.readFileSync(filePath, "utf8");
  if (!content.includes(oldSlug)) return false;
  const updated = replaceAll(content, oldSlug, newSlug);
  if (updated === content) return false;
  fs.writeFileSync(filePath, updated, "utf8");
  return true;
}

function migratePost({ rootDir, site, date, oldSlug, newSlug }) {
  const datePartsMatch = date.match(/^(\d{4})-(\d{2})-(\d{2})$/);
  if (!datePartsMatch) throw new Error(`Data invalida: ${date}`);
  const [, year, month, day] = datePartsMatch;

  const ext = site.renderExtension || "php";
  const oldPostDir = path.resolve(rootDir, site.siteRoot, year, month, day, oldSlug);
  const newPostDir = path.resolve(rootDir, site.siteRoot, year, month, day, newSlug);
  const oldCover = path.resolve(rootDir, site.siteRoot, site.assets.postsDir, `${oldSlug}.jpg`);
  const newCover = path.resolve(rootDir, site.siteRoot, site.assets.postsDir, `${newSlug}.jpg`);

  if (!fs.existsSync(oldPostDir)) {
    return { skipped: true, reason: `post dir nao existe: ${oldPostDir}` };
  }
  if (oldSlug === newSlug) {
    return { skipped: true, reason: "slug ja correto" };
  }

  fs.renameSync(oldPostDir, newPostDir);
  if (fs.existsSync(oldCover)) {
    fs.renameSync(oldCover, newCover);
  }

  const newPostFile = path.join(newPostDir, `index.${ext}`);
  maybeUpdateFile(newPostFile, oldSlug, newSlug);

  const homePath = path.resolve(rootDir, site.siteRoot, site.homePath);
  const blogPath = path.resolve(rootDir, site.siteRoot, site.blogIndexPath);
  const sitemapPath = path.resolve(rootDir, site.siteRoot, site.seo.sitemapPath);
  const homeUpdated = maybeUpdateFile(homePath, oldSlug, newSlug);
  const blogUpdated = maybeUpdateFile(blogPath, oldSlug, newSlug);
  const sitemapUpdated = maybeUpdateFile(sitemapPath, oldSlug, newSlug);

  const outDir = path.resolve(rootDir, site.outputRoot, date);
  const oldJson = path.join(outDir, `${oldSlug}.json`);
  const oldMd = path.join(outDir, `${oldSlug}.md`);
  const newJson = path.join(outDir, `${newSlug}.json`);
  const newMd = path.join(outDir, `${newSlug}.md`);
  if (fs.existsSync(oldJson)) {
    const contract = JSON.parse(fs.readFileSync(oldJson, "utf8"));
    contract.slug = newSlug;
    fs.writeFileSync(newJson, JSON.stringify(contract, null, 2), "utf8");
    fs.unlinkSync(oldJson);
  }
  if (fs.existsSync(oldMd)) {
    fs.renameSync(oldMd, newMd);
  }

  return {
    skipped: false,
    oldSlug,
    newSlug,
    homeUpdated,
    blogUpdated,
    sitemapUpdated
  };
}

function findCandidatesByDate(rootDir, sitesConfig, date) {
  const datePartsMatch = date.match(/^(\d{4})-(\d{2})-(\d{2})$/);
  if (!datePartsMatch) return [];
  const [, year, month, day] = datePartsMatch;
  const candidates = [];

  for (const site of sitesConfig.sites) {
    const dayDir = path.resolve(rootDir, site.siteRoot, year, month, day);
    if (!fs.existsSync(dayDir)) continue;
    for (const slug of fs.readdirSync(dayDir)) {
      const slugDir = path.join(dayDir, slug);
      if (!fs.statSync(slugDir).isDirectory()) continue;
      const outJson = path.resolve(rootDir, site.outputRoot, date, `${slug}.json`);
      if (!fs.existsSync(outJson)) continue;
      const contract = JSON.parse(fs.readFileSync(outJson, "utf8"));
      const headline = contract?.content?.headline;
      if (!headline) continue;
      const newSlug = slugFromHeadline(headline);
      if (!newSlug || newSlug === slug) continue;
      candidates.push({ siteId: site.id, oldSlug: slug, newSlug, headline });
    }
  }
  return candidates;
}

function main() {
  const args = process.argv.slice(2);
  const dateArg = args.find((a) => a.startsWith("--date="));
  const dryRun = args.includes("--dry-run");
  const date = dateArg ? dateArg.split("=")[1] : "";
  if (!date) {
    console.error("Uso: node migrate-slugs.js --date=YYYY-MM-DD [--dry-run]");
    process.exit(1);
  }

  const sitesConfig = JSON.parse(fs.readFileSync(CONFIG_PATH, "utf8"));
  const candidates = findCandidatesByDate(ROOT, sitesConfig, date);

  if (candidates.length === 0) {
    console.log(`Nenhum post para migrar em ${date}.`);
    return;
  }

  console.log(`${candidates.length} post(s) candidato(s) a migracao em ${date}:`);
  for (const c of candidates) {
    console.log(`  [${c.siteId}] ${c.oldSlug} -> ${c.newSlug}`);
  }

  if (dryRun) {
    console.log("\n--dry-run: nada foi alterado.");
    return;
  }

  const sitesById = Object.fromEntries(sitesConfig.sites.map((s) => [s.id, s]));
  const results = [];
  for (const c of candidates) {
    const site = sitesById[c.siteId];
    const result = migratePost({ rootDir: ROOT, site, date, oldSlug: c.oldSlug, newSlug: c.newSlug });
    results.push({ siteId: c.siteId, ...result });
  }

  console.log("\nResultado:");
  for (const r of results) console.log(JSON.stringify(r));
}

main();
