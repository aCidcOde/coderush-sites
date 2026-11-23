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

/*
 * Temas que JA viraram post publicado, em toda a historia do site.
 *
 * POR QUE (23/11/2026): a exclusao olhava so os 8 rascunhos mais recentes. Com 14
 * temas na lista, o sorteio voltava aos antigos antes de esgota-la — e o SVD
 * publicou duas vezes "comissao por cargo x pontos" (06/08 e 15/10), "integracao
 * com ERP" (15/08 e 22/11), "diferenca venda direta x MMN" (05 e 22/09). Posts
 * repetidos dividem as impressoes do Google e nenhum sobe.
 *
 * "Publicado" = existe pasta com o slug do rascunho em qualquer data do site:
 * rascunho de execucao que falhou nao conta (o de 22/09 virou o post de 05/09, e
 * os de 14, 23 e 30/09 nunca foram ao ar). Sites de API (sem siteRoot) nao tem
 * como conferir: ali todo rascunho conta como publicado.
 */
function publishedThemes(siteId, siteRootAbs) {
  const siteOut = path.join(OUT_ROOT, siteId);
  if (!fs.existsSync(siteOut)) return [];
  const slugsNoSite = new Set();
  if (siteRootAbs && fs.existsSync(siteRootAbs)) {
    for (const ano of fs.readdirSync(siteRootAbs).filter((n) => /^\d{4}$/.test(n))) {
      const pAno = path.join(siteRootAbs, ano);
      for (const mes of fs.readdirSync(pAno).filter((n) => /^\d{2}$/.test(n))) {
        const pMes = path.join(pAno, mes);
        for (const dia of fs.readdirSync(pMes).filter((n) => /^\d{2}$/.test(n))) {
          for (const slug of fs.readdirSync(path.join(pMes, dia))) slugsNoSite.add(slug);
        }
      }
    }
  }
  const temas = new Set();
  for (const dateDir of fs.readdirSync(siteOut).filter((n) => /^\d{4}-\d{2}-\d{2}$/.test(n))) {
    const dir = path.join(siteOut, dateDir);
    if (!fs.statSync(dir).isDirectory()) continue;
    for (const file of fs.readdirSync(dir).filter((n) => n.endsWith(".json"))) {
      const draft = readDraft(path.join(dir, file));
      if (!draft || !draft.theme) continue;
      if (siteRootAbs && !slugsNoSite.has(draft.slug)) continue;
      temas.add(String(draft.theme).trim().toLowerCase());
    }
  }
  return Array.from(temas);
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
  publishedThemes,
  recentAngles
};
