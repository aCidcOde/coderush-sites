const FEED_TIMEOUT_MS = 8000;

function decodeXmlEntities(value) {
  return String(value || "")
    .replace(/<!\[CDATA\[([\s\S]*?)\]\]>/g, "$1")
    .replace(/&lt;/g, "<")
    .replace(/&gt;/g, ">")
    .replace(/&quot;/g, '"')
    .replace(/&apos;/g, "'")
    .replace(/&#39;/g, "'")
    .replace(/&amp;/g, "&");
}

function stripTags(value) {
  return decodeXmlEntities(String(value || "").replace(/<[^>]+>/g, " "))
    .replace(/\s+/g, " ")
    .trim();
}

function pickTag(block, tag) {
  const regex = new RegExp(`<${tag}[^>]*>([\\s\\S]*?)<\\/${tag}>`, "i");
  const match = block.match(regex);
  return match ? match[1] : "";
}

function pickAttr(block, tag, attr) {
  const regex = new RegExp(`<${tag}[^>]*${attr}="([^"]+)"[^>]*\\/?>`, "i");
  const match = block.match(regex);
  return match ? match[1] : "";
}

function parseDate(value) {
  if (!value) return null;
  const ts = Date.parse(value);
  return Number.isNaN(ts) ? null : new Date(ts);
}

function parseRssChannel(xml) {
  const items = xml.match(/<item\b[\s\S]*?<\/item>/gi) || [];
  return items.map((block) => ({
    title: stripTags(pickTag(block, "title")),
    link: stripTags(pickTag(block, "link")),
    summary: stripTags(pickTag(block, "description") || pickTag(block, "content:encoded")),
    publishedAt: parseDate(pickTag(block, "pubDate") || pickTag(block, "dc:date"))
  }));
}

function parseAtomFeed(xml) {
  const entries = xml.match(/<entry\b[\s\S]*?<\/entry>/gi) || [];
  return entries.map((block) => ({
    title: stripTags(pickTag(block, "title")),
    link: pickAttr(block, "link", "href") || stripTags(pickTag(block, "link")),
    summary: stripTags(pickTag(block, "summary") || pickTag(block, "content")),
    publishedAt: parseDate(pickTag(block, "updated") || pickTag(block, "published"))
  }));
}

async function fetchFeed(url) {
  const controller = new AbortController();
  const timer = setTimeout(() => controller.abort(), FEED_TIMEOUT_MS);
  try {
    const response = await fetch(url, {
      signal: controller.signal,
      headers: {
        "User-Agent": "CodeRush-BlogBot/1.0 (+https://coderush.com.br)",
        Accept: "application/rss+xml, application/atom+xml, application/xml;q=0.9, */*;q=0.5"
      }
    });
    if (!response.ok) {
      return { url, ok: false, error: `HTTP ${response.status}`, items: [] };
    }
    const xml = await response.text();
    const items = /<feed[\s>]/i.test(xml) ? parseAtomFeed(xml) : parseRssChannel(xml);
    return {
      url,
      ok: true,
      items: items
        .filter((item) => item.title && item.link)
        .map((item) => ({ ...item, sourceUrl: url }))
    };
  } catch (error) {
    return { url, ok: false, error: String(error.message || error), items: [] };
  } finally {
    clearTimeout(timer);
  }
}

function escapeRegex(value) {
  return String(value).replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
}

function tokenizeFocus(focus) {
  return String(focus || "")
    .toLowerCase()
    .split(/[^a-z0-9]+/i)
    .filter((token) => token.length > 0);
}

function buildFocusMatchers(focus) {
  return tokenizeFocus(focus).map((token) => {
    if (token.length <= 3) {
      return new RegExp(`\\b${escapeRegex(token)}\\b`, "i");
    }
    return new RegExp(escapeRegex(token), "i");
  });
}

function rankItems(items, { sinceDays = 21, focus = "" } = {}) {
  const cutoff = Date.now() - sinceDays * 24 * 60 * 60 * 1000;
  const matchers = buildFocusMatchers(focus);

  return [...items]
    .filter((item) => !item.publishedAt || item.publishedAt.getTime() >= cutoff)
    .map((item) => {
      const haystack = `${item.title} ${item.summary}`;
      const focusHits = matchers.reduce((acc, matcher) => acc + (matcher.test(haystack) ? 1 : 0), 0);
      const recency = item.publishedAt ? item.publishedAt.getTime() : 0;
      return { ...item, _score: focusHits * 1e13 + recency };
    })
    .sort((left, right) => right._score - left._score)
    .map(({ _score, ...item }) => item);
}

async function gatherResearch({ feeds, focus, maxItems = 6, sinceDays = 21 }) {
  const list = Array.isArray(feeds) ? feeds.filter(Boolean) : [];
  if (list.length === 0) {
    return { items: [], errors: [], skipped: true };
  }

  const results = await Promise.all(list.map(fetchFeed));
  const errors = results.filter((result) => !result.ok).map((result) => ({ url: result.url, error: result.error }));
  const merged = results.flatMap((result) => result.items);
  const ranked = rankItems(merged, { sinceDays, focus }).slice(0, maxItems);

  return {
    items: ranked.map((item) => ({
      title: item.title,
      link: item.link,
      summary: item.summary?.slice(0, 320) || "",
      publishedAt: item.publishedAt ? item.publishedAt.toISOString() : null,
      sourceUrl: item.sourceUrl
    })),
    errors,
    skipped: false
  };
}

function uniqueLinks(items, max = Infinity) {
  const seen = new Set();
  const out = [];
  for (const item of items) {
    const link = item.link;
    if (!link || seen.has(link)) continue;
    seen.add(link);
    out.push(link);
    if (out.length >= max) break;
  }
  return out;
}

module.exports = {
  gatherResearch,
  uniqueLinks
};
