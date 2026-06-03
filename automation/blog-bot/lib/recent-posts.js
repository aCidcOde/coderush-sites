const fs = require("fs");
const path = require("path");

const ROOT = path.resolve(__dirname, "..");
const OUT_ROOT = path.join(ROOT, "out");
const COVERS_DIRNAME = "covers";

function readDraft(filePath) {
  try {
    return JSON.parse(fs.readFileSync(filePath, "utf8"));
  } catch {
    return null;
  }
}

function loadRecentPosts(siteId, { beforeDate, limit = 8 } = {}) {
  const siteOut = path.join(OUT_ROOT, siteId);
  if (!fs.existsSync(siteOut)) return [];

  const dateDirs = fs
    .readdirSync(siteOut, { withFileTypes: true })
    .filter((entry) => entry.isDirectory() && /^\d{4}-\d{2}-\d{2}$/.test(entry.name))
    .map((entry) => entry.name)
    .filter((name) => (beforeDate ? name < beforeDate : true))
    .sort((a, b) => (a < b ? 1 : -1));

  const out = [];
  for (const dateDir of dateDirs) {
    if (out.length >= limit) break;
    const dir = path.join(siteOut, dateDir);
    const files = fs.readdirSync(dir).filter((name) => name.endsWith(".json"));
    for (const file of files) {
      if (out.length >= limit) break;
      const draft = readDraft(path.join(dir, file));
      if (!draft) continue;
      const content = draft.content || {};
      out.push({
        date: draft.date || dateDir,
        theme: draft.theme || "",
        angle: draft.angle || "",
        slug: draft.slug || "",
        headline: content.headline || "",
        question: content.answerBox?.question || ""
      });
    }
  }
  return out;
}

function uniqueLowerSet(values) {
  const seen = new Set();
  for (const value of values) {
    if (!value) continue;
    seen.add(String(value).trim().toLowerCase());
  }
  return seen;
}

function recentThemes(recents) {
  return Array.from(uniqueLowerSet(recents.map((r) => r.theme)));
}

function recentAngles(recents) {
  return Array.from(uniqueLowerSet(recents.map((r) => r.angle)));
}

function loadRecentCoverAlts(siteId, { excludeSlug, limit = 6 } = {}) {
  const coversDir = path.join(OUT_ROOT, siteId, COVERS_DIRNAME);
  if (!fs.existsSync(coversDir)) return [];
  const entries = fs
    .readdirSync(coversDir)
    .filter((name) => name.endsWith(".jpg.alt.txt"))
    .map((name) => {
      const filePath = path.join(coversDir, name);
      return { name, filePath, mtime: fs.statSync(filePath).mtimeMs };
    })
    .sort((a, b) => b.mtime - a.mtime);

  const out = [];
  for (const entry of entries) {
    if (out.length >= limit) break;
    const slug = entry.name.replace(/\.jpg\.alt\.txt$/, "");
    if (excludeSlug && slug === excludeSlug) continue;
    try {
      const alt = fs.readFileSync(entry.filePath, "utf8").trim();
      if (alt) out.push({ slug, alt });
    } catch {
      // ignore unreadable file
    }
  }
  return out;
}

module.exports = {
  loadRecentPosts,
  loadRecentCoverAlts,
  recentThemes,
  recentAngles
};
