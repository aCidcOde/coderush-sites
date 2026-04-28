const fs = require("node:fs");
const path = require("node:path");

const STOPWORDS = new Set([
  "a","ao","aos","as","o","os","da","das","de","do","dos","e","em","na","nas","no","nos","ou","para","por","se",
  "um","uma","uns","umas","com","sem","sobre","entre","esse","essa","esses","essas","este","esta","estes","estas",
  "que","como","quando","mais","menos","sua","suas","seu","seus","tem","tudo","ja","muito","muitas","muitos","sao",
  "ser","foi","sera","tambem","ainda","novo","nova","novos","novas","blog","post","tag","tags","|","-","–"
]);

const INDEX_MARKERS = {
  start: "<!-- BLOG-INDEX-CARDS:START -->",
  end: "<!-- BLOG-INDEX-CARDS:END -->"
};

function escapeRegex(value) {
  return String(value).replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
}

function decodeEntities(value) {
  return String(value || "")
    .replace(/&quot;/g, '"')
    .replace(/&#039;/g, "'")
    .replace(/&amp;/g, "&")
    .replace(/&lt;/g, "<")
    .replace(/&gt;/g, ">");
}

function stripTags(value) {
  return decodeEntities(String(value || "").replace(/<[^>]+>/g, " "))
    .replace(/\s+/g, " ")
    .trim();
}

function tokenize(text) {
  return String(text || "")
    .toLowerCase()
    .normalize("NFD")
    .replace(/[̀-ͯ]/g, "")
    .split(/[^a-z0-9]+/)
    .filter((token) => token.length >= 3 && !STOPWORDS.has(token));
}

function extractMarkedSegment(content, markers) {
  const regex = new RegExp(`${escapeRegex(markers.start)}([\\s\\S]*?)${escapeRegex(markers.end)}`);
  const match = content.match(regex);
  return match ? match[1] : "";
}

function parseDateFromPath(postPath) {
  const match = String(postPath || "").match(/(\d{4})\/(\d{2})\/(\d{2})\//);
  return match ? `${match[1]}-${match[2]}-${match[3]}` : "";
}

function parseSiteIndex(filePath) {
  if (!fs.existsSync(filePath)) return [];
  const content = fs.readFileSync(filePath, "utf8");
  const inside = extractMarkedSegment(content, INDEX_MARKERS) || content;
  const articles = inside.match(/<article\b[\s\S]*?<\/article>/g) || [];
  return articles
    .map((html) => {
      const titleMatch =
        html.match(/<h[23][^>]*>\s*<a[^>]*>([\s\S]*?)<\/a>\s*<\/h[23]>/i) ||
        html.match(/<a[^>]*class="[^"]*hover:underline[^"]*"[^>]*>([\s\S]*?)<\/a>/i);
      const excerptMatch = html.match(/<p[^>]*>([\s\S]*?)<\/p>/i);
      const href =
        html.match(/data-blog-path="([^"]+)"/i)?.[1] ||
        html.match(/<a[^>]+href="([^"]+)"/i)?.[1] ||
        "";
      const image =
        html.match(/data-blog-image="([^"]+)"/i)?.[1] ||
        html.match(/<img[^>]+src="([^"]+)"/i)?.[1] ||
        "";
      const postPath = (href.match(/(\d{4}\/\d{2}\/\d{2}\/[^/"'>]+\/)/) || [])[1] || "";
      const imagePath = (image.match(/(imagens\/posts\/[^?"'\s>]+)/) || [])[1] || "";
      if (!postPath) return null;
      return {
        title: stripTags(titleMatch?.[1] || ""),
        excerpt: stripTags(excerptMatch?.[1] || ""),
        postPath,
        imagePath: imagePath || `imagens/posts/${postPath.replace(/\/$/, "").split("/").pop()}.jpg`,
        date: parseDateFromPath(postPath)
      };
    })
    .filter(Boolean);
}

function buildCrossSiteIndex(rootDir, sitesConfig) {
  const index = [];
  for (const site of sitesConfig.sites) {
    const blogIndexPath = path.resolve(rootDir, site.siteRoot, site.blogIndexPath);
    const cards = parseSiteIndex(blogIndexPath);
    for (const card of cards) {
      index.push({
        siteId: site.id,
        siteName: site.name,
        baseUrl: site.baseUrl,
        title: card.title,
        excerpt: card.excerpt,
        url: `${site.baseUrl}/${card.postPath}`,
        imageUrl: `${site.baseUrl}/${card.imagePath}`,
        date: card.date
      });
    }
  }
  return index;
}

function pickRelatedFromOtherSites({ currentSiteId, currentTitle, currentText = "", index, max = 3 }) {
  const queryTokens = new Set([...tokenize(currentTitle), ...tokenize(currentText)]);
  const candidates = index
    .filter((card) => card.siteId !== currentSiteId && card.title)
    .map((card) => {
      const cardTokens = new Set(tokenize(`${card.title} ${card.excerpt}`));
      let overlap = 0;
      for (const token of queryTokens) {
        if (cardTokens.has(token)) overlap += 1;
      }
      return { ...card, _overlap: overlap };
    })
    .sort((a, b) => {
      if (b._overlap !== a._overlap) return b._overlap - a._overlap;
      return (b.date || "").localeCompare(a.date || "");
    });

  const seenSites = new Map();
  const picked = [];
  for (const card of candidates) {
    const used = seenSites.get(card.siteId) || 0;
    if (used >= 1 && picked.length < max && candidates.some((c) => c.siteId !== card.siteId && !picked.includes(c))) {
      continue;
    }
    picked.push(card);
    seenSites.set(card.siteId, used + 1);
    if (picked.length >= max) break;
  }
  if (picked.length < max) {
    for (const card of candidates) {
      if (picked.includes(card)) continue;
      picked.push(card);
      if (picked.length >= max) break;
    }
  }
  return picked.map(({ _overlap, ...card }) => card);
}

function pickSameSiteRelated({ currentSiteId, currentPostPath, currentTitle, currentText = "", index, max = 3 }) {
  const queryTokens = new Set([...tokenize(currentTitle), ...tokenize(currentText)]);
  return index
    .filter((card) => card.siteId === currentSiteId && card.title && card.url)
    .filter((card) => !currentPostPath || !card.url.includes(currentPostPath))
    .map((card) => {
      const cardTokens = new Set(tokenize(`${card.title} ${card.excerpt}`));
      let overlap = 0;
      for (const token of queryTokens) {
        if (cardTokens.has(token)) overlap += 1;
      }
      return { ...card, _overlap: overlap };
    })
    .sort((a, b) => {
      if (b._overlap !== a._overlap) return b._overlap - a._overlap;
      return (b.date || "").localeCompare(a.date || "");
    })
    .slice(0, max)
    .map(({ _overlap, ...card }) => card);
}

module.exports = {
  buildCrossSiteIndex,
  pickRelatedFromOtherSites,
  pickSameSiteRelated,
  tokenize,
  parseSiteIndex
};
