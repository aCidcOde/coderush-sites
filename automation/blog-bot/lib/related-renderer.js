const path = require("node:path");

const SAME_SITE_MARKERS = {
  start: "<!-- BLOG-LEIA-TAMBEM START -->",
  end: "<!-- BLOG-LEIA-TAMBEM END -->"
};

const CROSS_SITE_MARKERS = {
  start: "<!-- BLOG-CROSS-SITE START -->",
  end: "<!-- BLOG-CROSS-SITE END -->"
};

function escapeHtml(value) {
  return String(value || "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function truncate(value, max = 180) {
  const text = String(value || "").trim();
  if (text.length <= max) return text;
  return `${text.slice(0, max - 3).trim()}...`;
}

function imagePathFromUrl(url) {
  const match = String(url || "").match(/(imagens\/posts\/[^?"'\s>]+)/);
  return match ? match[1] : "";
}

function siteRelativePostPath(url, baseUrl) {
  if (!url || !baseUrl) return "";
  if (url.startsWith(baseUrl)) {
    return url.slice(baseUrl.length).replace(/^\/+/, "");
  }
  return "";
}

function renderSameSiteCard(card, sitePathPrefix) {
  const postPath = siteRelativePostPath(card.url, card.baseUrl);
  const imagePath = imagePathFromUrl(card.imageUrl);
  if (!postPath) return "";
  const href = `${sitePathPrefix}${postPath}`;
  const imageSrc = imagePath ? `${sitePathPrefix}${imagePath}` : "";
  const title = escapeHtml(card.title);
  const excerpt = escapeHtml(truncate(card.excerpt, 160));
  return [
    `<article class="overflow-hidden rounded-2xl border border-white/15 bg-white/5">`,
    imageSrc
      ? `  <a href="${href}"><img src="${imageSrc}" alt="${title}" class="h-44 w-full object-cover" width="1200" height="630" loading="lazy" /></a>`
      : "",
    '  <div class="p-4">',
    `    <h3 class="text-base font-semibold leading-snug"><a href="${href}" class="hover:underline">${title}</a></h3>`,
    excerpt ? `    <p class="mt-2 text-sm leading-relaxed text-white/80">${excerpt}</p>` : "",
    "  </div>",
    "</article>"
  ]
    .filter(Boolean)
    .join("\n");
}

function renderCrossSiteCard(card) {
  if (!card?.url) return "";
  const title = escapeHtml(card.title);
  const excerpt = escapeHtml(truncate(card.excerpt, 140));
  const siteName = escapeHtml(card.siteName);
  const url = escapeHtml(card.url);
  const imageUrl = escapeHtml(card.imageUrl || "");
  return [
    `<article class="overflow-hidden rounded-2xl border border-white/15 bg-white/5">`,
    imageUrl
      ? `  <a href="${url}" rel="noopener" target="_blank"><img src="${imageUrl}" alt="${title}" class="h-44 w-full object-cover" width="1200" height="630" loading="lazy" /></a>`
      : "",
    '  <div class="p-4">',
    `    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/55">${siteName}</p>`,
    `    <h3 class="mt-1 text-base font-semibold leading-snug"><a href="${url}" rel="noopener" target="_blank" class="hover:underline">${title}</a></h3>`,
    excerpt ? `    <p class="mt-2 text-sm leading-relaxed text-white/80">${excerpt}</p>` : "",
    "  </div>",
    "</article>"
  ]
    .filter(Boolean)
    .join("\n");
}

function renderSameSiteSection(cards, sitePathPrefix, blogIndexHref) {
  if (!cards || cards.length === 0) return "";
  return [
    SAME_SITE_MARKERS.start,
    '<section class="mt-8 rounded-2xl border border-white/15 bg-white/5 p-5">',
    '  <div class="flex items-end justify-between gap-4">',
    '    <h2 class="text-2xl font-semibold text-white">Leia também</h2>',
    `    <a href="${blogIndexHref}" class="text-sm font-semibold text-white/85 hover:text-white">Ver todos</a>`,
    "  </div>",
    '  <div class="mt-5 grid gap-4 md:grid-cols-3">',
    cards.map((card) => renderSameSiteCard(card, sitePathPrefix)).filter(Boolean).join("\n"),
    "  </div>",
    "</section>",
    SAME_SITE_MARKERS.end
  ].join("\n");
}

function renderCrossSiteSection(cards) {
  if (!cards || cards.length === 0) return "";
  return [
    CROSS_SITE_MARKERS.start,
    '<section class="mt-8 rounded-2xl border border-white/15 bg-white/5 p-5">',
    '  <div class="flex items-end justify-between gap-4">',
    '    <h2 class="text-2xl font-semibold text-white">Mais do hub CodeRush</h2>',
    '    <a href="https://coderush.com.br/" rel="noopener" target="_blank" class="text-sm font-semibold text-white/85 hover:text-white">Ver hub</a>',
    "  </div>",
    '  <p class="mt-2 text-sm text-white/70">Conteúdo recente dos outros sites do ecossistema.</p>',
    '  <div class="mt-5 grid gap-4 md:grid-cols-3">',
    cards.map(renderCrossSiteCard).filter(Boolean).join("\n"),
    "  </div>",
    "</section>",
    CROSS_SITE_MARKERS.end
  ].join("\n");
}

module.exports = {
  SAME_SITE_MARKERS,
  CROSS_SITE_MARKERS,
  renderSameSiteSection,
  renderCrossSiteSection,
  renderSameSiteCard,
  renderCrossSiteCard
};
