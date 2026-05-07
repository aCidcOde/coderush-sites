const SEARCH_TIMEOUT_MS = 10000;

const TRACKER_PATTERNS = [
  /duckduckgo\.com\/y\.js/i,
  /bing\.com\/aclick/i,
  /^https?:\/\/(www\.)?(amazon|facebook|instagram|tiktok|pinterest|reddit)\.com/i,
  /\/ad_provider=/i
];

function decodeDdgUrl(href) {
  if (!href) return null;
  if (href.startsWith("http")) return href;
  if (href.startsWith("//")) href = "https:" + href;
  try {
    const url = new URL(href);
    const uddg = url.searchParams.get("uddg");
    if (uddg) {
      const decoded = decodeURIComponent(uddg);
      return decoded.startsWith("http") ? decoded : null;
    }
    return href.startsWith("http") ? href : null;
  } catch (_e) {
    return null;
  }
}

function stripTags(value) {
  return String(value || "").replace(/<[^>]+>/g, "").replace(/\s+/g, " ").trim();
}

async function ddgSearch(query, { maxResults = 8, lang = "pt-BR" } = {}) {
  const url = `https://html.duckduckgo.com/html/?q=${encodeURIComponent(query)}`;
  const controller = new AbortController();
  const timer = setTimeout(() => controller.abort(), SEARCH_TIMEOUT_MS);
  try {
    const response = await fetch(url, {
      method: "GET",
      headers: {
        "User-Agent": "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36",
        "Accept-Language": `${lang},pt;q=0.9,en;q=0.7`
      },
      signal: controller.signal
    });
    if (!response.ok) {
      return { items: [], error: `HTTP ${response.status}` };
    }
    const html = await response.text();
    const items = [];
    const seen = new Set();
    const aRe = /<a[^>]+class="result__a"[^>]+href="([^"]+)"[^>]*>([\s\S]*?)<\/a>/g;
    let match;
    while ((match = aRe.exec(html)) !== null) {
      const real = decodeDdgUrl(match[1]);
      if (!real) continue;
      if (TRACKER_PATTERNS.some((re) => re.test(real))) continue;
      if (seen.has(real)) continue;
      seen.add(real);
      items.push({
        title: stripTags(match[2]),
        link: real,
        summary: "",
        publishedAt: null,
        sourceUrl: "ddg"
      });
      if (items.length >= maxResults) break;
    }
    return { items };
  } catch (error) {
    return { items: [], error: String(error.message || error) };
  } finally {
    clearTimeout(timer);
  }
}

async function searchWebForTopic(query, { maxResults = 5, lang = "pt-BR" } = {}) {
  const trimmed = String(query || "").trim();
  if (!trimmed) return { items: [], error: "empty query" };
  return ddgSearch(trimmed, { maxResults, lang });
}

module.exports = {
  searchWebForTopic
};
