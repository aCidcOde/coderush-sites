const fs = require("fs");
const path = require("path");

const ROOT = path.resolve(__dirname, "..");
const OUT_ROOT = path.join(ROOT, "out");

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

module.exports = {
  loadRecentPosts,
  recentThemes,
  recentAngles
};
