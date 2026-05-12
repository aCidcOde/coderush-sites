const fs = require("node:fs");
const path = require("node:path");
const { generateCover: agentGenerateCover } = require("./cover-agent");

const HOME_MARKERS = {
  start: "<!-- BLOG-HOME-CARDS:START -->",
  end: "<!-- BLOG-HOME-CARDS:END -->"
};

const INDEX_MARKERS = {
  start: "<!-- BLOG-INDEX-CARDS:START -->",
  end: "<!-- BLOG-INDEX-CARDS:END -->"
};

const SITE_COPY = {
  coderush: {
    blogName: "Blog CodeRush",
    homeSectionTitle: "Blog CodeRush",
    homeSectionDescription:
      "Conteúdo sobre software sob medida, IA, automação e governança para empresas que precisam escalar com critério.",
    indexTitle: "Blog CodeRush | Todos os posts",
    indexDescription:
      "Artigos da CodeRush sobre software sob medida, IA, automação e operação de tecnologia.",
    articleLabel: "Blog CodeRush",
    footerBlurb:
      "A CodeRush conecta software sob medida, IA e automação ao objetivo do negócio com execução pragmática.",
    ctaTitle: "Quer destravar uma iniciativa de tecnologia com critério?",
    ctaBody:
      "A CodeRush estrutura arquitetura, entrega e automação para tirar iniciativas críticas do papel sem improviso.",
    ctaPath: "#contato",
    ctaLabel: "Fale com a CodeRush",
    phone: "11 99456-6726",
    email: "contato@coderush.com.br",
    accentLinkClass: "text-blue-400 hover:text-blue-300",
    headingBarClass: "bg-blue-400",
    bodyClass: "min-h-screen bg-[#020b1a] text-white antialiased",
    headerClass: "border-b border-white/10 bg-[#020b1a]/95 backdrop-blur",
    footerClass: "border-t border-white/10 bg-[#020b1a]/80",
    navLinks: [
      { href: "", label: "Site principal" },
      { href: "#empresas", label: "Empresas" },
      { href: "blog/", label: "Blog" },
      { href: "#contato", label: "Contato" }
    ]
  },
  codafacil: {
    blogName: "Blog Codafacil.dev",
    homeSectionTitle: "Blog Codafacil.dev",
    homeSectionDescription:
      "Conteúdo sobre software sob medida, entrega orientada por IA, integrações e automação para times de produto e operação.",
    indexTitle: "Blog Codafacil.dev | Todos os posts",
    indexDescription:
      "Artigos da Codafacil.dev sobre desenvolvimento com IA, integrações e software sob medida.",
    articleLabel: "Blog Codafacil.dev",
    footerBlurb:
      "A Codafacil.dev acelera software sob medida com IA sem abrir mão de engenharia, testes e clareza de escopo.",
    ctaTitle: "Precisa tirar um produto ou integração do papel?",
    ctaBody:
      "A Codafacil.dev combina engenharia e IA aplicada para acelerar a entrega sem perder governança técnica.",
    ctaPath: "#contato",
    ctaLabel: "Fale com a Codafacil.dev",
    phone: "11 99456-6726",
    email: "",
    accentLinkClass: "text-sky-300 hover:text-white",
    headingBarClass: "bg-sky-300",
    bodyClass: "min-h-screen bg-[#04110d] text-white antialiased",
    headerClass: "border-b border-white/10 bg-[#04110d]/95 backdrop-blur",
    footerClass: "border-t border-white/10 bg-[#04110d]/80",
    navLinks: [
      { href: "", label: "Site principal" },
      { href: "#servicos", label: "Serviços" },
      { href: "blog/", label: "Blog" },
      { href: "#contato", label: "Contato" }
    ]
  },
  fluxointeligenteia: {
    blogName: "Blog FluxoInteligente IA",
    homeSectionTitle: "Blog FluxoInteligente IA",
    homeSectionDescription:
      "Conteúdo sobre agentes corporativos, RAG, tools, automação segura e IA aplicada a operações que precisam escalar com governança.",
    indexTitle: "Blog FluxoInteligente IA | Todos os posts",
    indexDescription:
      "Artigos da FluxoInteligente IA sobre agentes corporativos, RAG, tools, permissões, auditoria, canais e automação com IA.",
    articleLabel: "Blog FluxoInteligente IA",
    footerBlurb:
      "A FluxoInteligente IA cria agentes corporativos conectados a documentos, sistemas, canais e tools, com permissões, auditoria e governança.",
    ctaTitle: "Quer criar um agente corporativo em produção?",
    ctaBody:
      "A FluxoInteligente IA estrutura RAG, tools, integrações, canais e controles para colocar agentes corporativos em operação real.",
    ctaPath: "#contato",
    ctaLabel: "Fale com a FluxoInteligente IA",
    phone: "11 99456-6726",
    email: "contato@fluxointeligenteia.com.br",
    accentLinkClass: "text-emerald-400 hover:text-emerald-300",
    headingBarClass: "bg-emerald-400",
    bodyClass: "min-h-screen bg-[#04110d] text-white antialiased",
    headerClass: "border-b border-white/10 bg-[#04110d]/95 backdrop-blur",
    footerClass: "border-t border-white/10 bg-[#04110d]/80",
    navLinks: [
      { href: "", label: "Site principal" },
      { href: "#solucoes", label: "Soluções" },
      { href: "blog/", label: "Blog" },
      { href: "#contato", label: "Contato" }
    ]
  },
  sistemavendadireta: {
    blogName: "Blog SVD",
    homeSectionTitle: "Blog SVD",
    homeSectionDescription:
      "Conteúdo sobre vendas diretas, software, IA aplicada ao negócio e evolução operacional com foco em resultado.",
    indexTitle: "Blog SVD | Todos os posts",
    indexDescription:
      "Artigos do Sistema Venda Direta sobre vendas diretas, MMN, software e IA aplicada ao negócio.",
    articleLabel: "Blog SVD",
    footerBlurb:
      "A Sistema Venda Direta desenvolve soluções para operação comercial, vendas diretas e evolução tecnológica com IA aplicada.",
    ctaTitle: "Quer aplicar IA na operação comercial com previsibilidade?",
    ctaBody:
      "A SVD estrutura arquitetura, integração e governança para levar automação ao negócio com previsibilidade.",
    ctaPath: "#contato",
    ctaLabel: "Solicite um orçamento",
    phone: "11 99456-6726",
    email: "contato@sistemavendadireta.com.br",
    accentLinkClass: "text-white hover:text-white/80",
    headingBarClass: "bg-white",
    bodyClass: "min-h-screen bg-brand text-white antialiased",
    headerClass: "border-b border-white/10 bg-brand/95 backdrop-blur",
    footerClass: "border-t border-white/15 bg-brand-dark/40",
    navLinks: [
      { href: "", label: "Site principal" },
      { href: "inteligencia-artificial/", label: "IA para MMN" },
      { href: "wordpress/", label: "WordPress" },
      { href: "blog/", label: "Blog" }
    ]
  }
};

function esc(value) {
  return String(value || "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
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

function formatDateParts(isoDate) {
  const [year, month, day] = isoDate.split("-");
  return { year, month, day };
}

function brDate(isoDate) {
  const [year, month, day] = isoDate.split("-");
  return `${day}/${month}/${year}`;
}

function escapeRegex(value) {
  return value.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
}

function normalizeSiteRelativePath(value) {
  const cleaned = String(value || "").replace(/\\/g, "/");
  if (!cleaned || cleaned === ".") {
    return "";
  }
  return cleaned.replace(/^\/+/, "");
}

function relativeLink(prefix, sitePath) {
  const normalized = normalizeSiteRelativePath(sitePath);
  if (!normalized) {
    return prefix || "./";
  }
  if (/^(https?:)?\/\//.test(normalized)) {
    return normalized;
  }
  if (normalized.startsWith("#")) {
    return `${prefix}${normalized}`;
  }
  return `${prefix}${normalized}`;
}

function joinUrl(baseUrl, sitePath) {
  const normalized = normalizeSiteRelativePath(sitePath);
  if (!normalized) {
    return `${baseUrl}/`;
  }
  return `${baseUrl}/${normalized}`;
}

function relativeAssetPrefix(context) {
  if (context === "home") {
    return "";
  }
  if (context === "blog-index") {
    return "../";
  }
  return "../../../../";
}

function normalizePostPath(candidate) {
  const match = String(candidate || "").replace(/\\/g, "/").match(/(\d{4}\/\d{2}\/\d{2}\/[^/"'>]+\/)/);
  return match ? match[1] : "";
}

function normalizeImagePath(candidate) {
  const match = String(candidate || "").replace(/\\/g, "/").match(/(imagens\/posts\/[^?"'\s>]+)/);
  return match ? match[1] : "";
}

function slugFromPostPath(postPath) {
  const cleaned = normalizePostPath(postPath).replace(/\/$/, "");
  const parts = cleaned.split("/");
  return parts[parts.length - 1] || "";
}

function dateFromPostPath(postPath) {
  const match = normalizePostPath(postPath).match(/^(\d{4})\/(\d{2})\/(\d{2})\//);
  return match ? `${match[1]}-${match[2]}-${match[3]}` : "";
}

function ensureDir(dirPath) {
  fs.mkdirSync(dirPath, { recursive: true });
}

function extractMarkedSegment(content, markers) {
  const regex = new RegExp(`${escapeRegex(markers.start)}([\\s\\S]*?)${escapeRegex(markers.end)}`);
  const match = content.match(regex);
  return match ? match[1] : "";
}

function replaceMarkedSegment(content, markers, replacement) {
  const regex = new RegExp(`${escapeRegex(markers.start)}[\\s\\S]*?${escapeRegex(markers.end)}`);
  if (!regex.test(content)) {
    throw new Error(`Marcadores não encontrados: ${markers.start}`);
  }
  return content.replace(regex, `${markers.start}\n${replacement}\n${markers.end}`);
}

function extractArticles(markup) {
  return String(markup || "").match(/<article\b[\s\S]*?<\/article>/g) || [];
}

function parseCard(articleHtml) {
  const titleMatch =
    articleHtml.match(/<h[23][^>]*>\s*<a[^>]*>([\s\S]*?)<\/a>\s*<\/h[23]>/i) ||
    articleHtml.match(/<a[^>]*class="[^"]*hover:underline[^"]*"[^>]*>([\s\S]*?)<\/a>/i);
  const excerptMatch = articleHtml.match(/<p[^>]*>([\s\S]*?)<\/p>/i);
  const href =
    articleHtml.match(/data-blog-path="([^"]+)"/i)?.[1] ||
    articleHtml.match(/<a[^>]+href="([^"]+)"/i)?.[1] ||
    "";
  const image =
    articleHtml.match(/data-blog-image="([^"]+)"/i)?.[1] ||
    articleHtml.match(/<img[^>]+src="([^"]+)"/i)?.[1] ||
    "";
  const postPath = normalizePostPath(href);
  if (!postPath) {
    return null;
  }

  const title = stripTags(titleMatch?.[1] || "");
  const excerpt = stripTags(excerptMatch?.[1] || "");
  return {
    postPath,
    imagePath: normalizeImagePath(image) || `imagens/posts/${slugFromPostPath(postPath)}.jpg`,
    title,
    excerpt,
    slug: slugFromPostPath(postPath),
    date: dateFromPostPath(postPath)
  };
}

function parseCards(markup) {
  return extractArticles(markup).map(parseCard).filter(Boolean);
}

function sortCards(cards) {
  return [...cards].sort((left, right) => {
    const leftKey = `${left.date}|${left.postPath}`;
    const rightKey = `${right.date}|${right.postPath}`;
    return rightKey.localeCompare(leftKey);
  });
}

function mergeCards(cards, maxItems = Infinity) {
  const bySlug = new Map();
  for (const card of sortCards(cards)) {
    if (!card.slug) {
      continue;
    }
    if (!bySlug.has(card.slug)) {
      bySlug.set(card.slug, card);
    }
  }

  return sortCards([...bySlug.values()]).slice(0, maxItems);
}

function truncate(value, maxLength = 180) {
  const text = String(value || "").trim();
  if (text.length <= maxLength) {
    return text;
  }

  return `${text.slice(0, maxLength - 3).trim()}...`;
}

function buildCardRecord(contract) {
  const { year, month, day } = formatDateParts(contract.date);
  return {
    date: contract.date,
    slug: contract.slug,
    title: contract.content.headline,
    excerpt: truncate(contract.content.summary, 180),
    imagePath: `imagens/posts/${contract.slug}.jpg`,
    postPath: `${year}/${month}/${day}/${contract.slug}/`
  };
}

function renderCard(card, context) {
  const prefix = relativeAssetPrefix(context);
  const href = `${prefix}${card.postPath}`;
  const imageSrc = `${prefix}${card.imagePath}`;
  const headingTag = context === "post-related" ? "h3" : "h2";

  return [
    `<article class="overflow-hidden rounded-2xl border border-white/15 bg-white/5" data-blog-path="${esc(
      card.postPath
    )}" data-blog-image="${esc(card.imagePath)}" data-blog-slug="${esc(card.slug)}" data-blog-date="${esc(card.date)}">`,
    `  <a href="${href}">`,
    `    <img src="${imageSrc}" alt="${esc(card.title)}" class="h-44 w-full object-cover" width="1200" height="630" loading="lazy" />`,
    "  </a>",
    '  <div class="p-4">',
    `    <${headingTag} class="text-base font-semibold leading-snug"><a href="${href}" class="hover:underline">${esc(
      card.title
    )}</a></${headingTag}>`,
    `    <p class="mt-2 text-sm leading-relaxed text-white/80">${esc(card.excerpt)}</p>`,
    "  </div>",
    "</article>"
  ].join("\n");
}

function stylesheetLinks(root, site, prefix) {
  const candidateFiles = [
    "css/site-tailwind.css",
    "css/hub-parity.css",
    "css/styles.css",
    "css/site-optimizations.css"
  ];

  return candidateFiles
    .filter((relativePath) => fs.existsSync(path.resolve(root, site.siteRoot, relativePath)))
    .map((relativePath) => `  <link rel="stylesheet" href="${prefix}${relativePath}" />`)
    .join("\n");
}

function renderNavLinks(copy, prefix) {
  return copy.navLinks
    .map(
      (item) =>
        `          <a href="${relativeLink(prefix, item.href)}" class="text-sm text-white/85 hover:text-white">${esc(
          item.label
        )}</a>`
    )
    .join("\n");
}

function renderFooterLinks(copy, prefix) {
  return copy.navLinks
    .map(
      (item) =>
        `            <a href="${relativeLink(prefix, item.href)}" class="hover:underline">${esc(item.label)}</a>`
    )
    .join("\n");
}

const SEO_TITLE_PIXEL_BUDGET = 60; // total chars (~ 580px @ 16px Helvetica)
const STOP_TAIL = new Set([
  "a","o","as","os","de","da","do","das","dos","e","em","na","no","nas","nos",
  "para","por","com","ao","aos","um","uma","uns","umas","sobre","entre","ja","só"
]);

function smartTruncate(text, max) {
  const clean = String(text || "").trim();
  if (clean.length <= max) return clean;
  const minMeaningful = Math.max(28, Math.floor(max * 0.6));
  // priorizar corte em pontuacao APENAS se gerar prefixo informativo
  for (const sep of [": ", " — ", " - ", "; "]) {
    const idx = clean.indexOf(sep);
    if (idx >= minMeaningful && idx <= max) return clean.slice(0, idx);
  }
  // corte por palavra
  const words = clean.split(/\s+/);
  let out = "";
  for (const w of words) {
    const next = out ? `${out} ${w}` : w;
    if (next.length > max) break;
    out = next;
  }
  while (out) {
    const lastSpace = out.lastIndexOf(" ");
    if (lastSpace < 0) break;
    const tail = out.slice(lastSpace + 1).toLowerCase();
    if (!STOP_TAIL.has(tail)) break;
    out = out.slice(0, lastSpace);
  }
  return out || clean.slice(0, max);
}

function buildSeoTitle(contract, site) {
  const explicit = contract?.content?.seoTitle || contract?.seoTitle;
  const suffix = ` | ${site.name}`;
  const room = Math.max(20, SEO_TITLE_PIXEL_BUDGET - suffix.length);
  const head = explicit && explicit.length <= room
    ? explicit
    : smartTruncate(contract.content.headline, room);
  return `${head}${suffix}`;
}

function buildMetaDescription(contract) {
  const answer = contract?.content?.answerBox?.answer;
  if (typeof answer === "string" && answer.trim().length >= 80) return answer.trim();
  return contract.description || "";
}

function buildJsonLd(site, contract, canonicalUrl, imageUrl) {
  return JSON.stringify(
    {
      "@context": "https://schema.org",
      "@type": "BlogPosting",
      headline: `${contract.content.headline} | ${site.name}`,
      description: buildMetaDescription(contract),
      datePublished: `${contract.date}T09:00:00-03:00`,
      dateModified: `${contract.date}T09:00:00-03:00`,
      mainEntityOfPage: { "@type": "WebPage", "@id": canonicalUrl },
      image: [imageUrl],
      author: { "@type": "Organization", name: site.name },
      publisher: { "@type": "Organization", name: site.name }
    },
    null,
    2
  );
}

function buildFaqJsonLd(contract) {
  const faq = contract?.content?.faq;
  if (!Array.isArray(faq) || faq.length === 0) return "";
  return JSON.stringify(
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      mainEntity: faq.map((item) => ({
        "@type": "Question",
        name: item.q,
        acceptedAnswer: { "@type": "Answer", text: item.a }
      }))
    },
    null,
    2
  );
}

const SITE_ACCENT = {
  coderush: "blue-400",
  codafacil: "sky-300",
  fluxointeligenteia: "emerald-400",
  sistemavendadireta: "white"
};

function accentFor(siteId) {
  return SITE_ACCENT[siteId] || "white";
}

function renderProseSection(section) {
  return [
    '      <section class="mt-8">',
    `        <h2 class="text-xl font-semibold text-white sm:text-2xl">${esc(section.title)}</h2>`,
    `        <p class="mt-3 text-sm leading-7 text-white/85 sm:text-base">${esc(section.body)}</p>`,
    "      </section>"
  ].join("\n");
}

function renderListSection(section, accent) {
  const items = (section.items || []).map((item) => `
        <li class="flex gap-3">
          <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-${accent}" aria-hidden="true"></span>
          <span>${esc(item)}</span>
        </li>`).join("");
  return [
    '      <section class="mt-8">',
    `        <h2 class="text-xl font-semibold text-white sm:text-2xl">${esc(section.title)}</h2>`,
    `        <ul class="mt-4 space-y-2 text-sm leading-7 text-white/85 sm:text-base">${items}
        </ul>`,
    "      </section>"
  ].join("\n");
}

function renderCalloutSection(section, accent, relativeRoot) {
  const href = relativeLink(relativeRoot, section.ctaHref || "#contato");
  const linkPart = section.ctaLabel
    ? ` <a href="${href}" class="font-semibold text-${accent} underline decoration-${accent}/40 underline-offset-4 hover:text-white">${esc(section.ctaLabel)} →</a>`
    : "";
  return [
    `      <aside class="my-8 rounded-2xl border-l-4 border-${accent} bg-${accent}/10 p-5 sm:p-6">`,
    `        <p class="text-sm leading-7 text-white/90 sm:text-base">${esc(section.body)}${linkPart}</p>`,
    "      </aside>"
  ].join("\n");
}

function renderCtaInlineSection(section, accent, relativeRoot) {
  const href = relativeLink(relativeRoot, section.ctaHref || "#contato");
  const label = section.ctaLabel ? esc(section.ctaLabel) : "Fale com o time";
  return [
    `      <p class="my-6 text-sm leading-7 text-white/85 sm:text-base">`,
    `        ${esc(section.body)} <a href="${href}" class="font-semibold text-${accent} underline decoration-${accent}/40 underline-offset-4 hover:text-white">${label} →</a>`,
    `      </p>`
  ].join("\n");
}

function renderSection(section, accent, relativeRoot) {
  if (!section) return "";
  const type = section.type || "prose";
  switch (type) {
    case "list":       return renderListSection(section, accent);
    case "callout":    return renderCalloutSection(section, accent, relativeRoot);
    case "cta-inline": return renderCtaInlineSection(section, accent, relativeRoot);
    case "prose":
    default:           return renderProseSection(section);
  }
}

function renderSections(contract, accent, relativeRoot) {
  return (contract.content.sections || [])
    .map((section) => renderSection(section, accent, relativeRoot))
    .filter(Boolean)
    .join("\n");
}

function renderAnswerBox(answerBox, accent) {
  if (!answerBox || !answerBox.answer) return "";
  return [
    `      <aside class="mt-6 rounded-2xl border-l-4 border-${accent} bg-white/[0.04] p-5 sm:p-6">`,
    answerBox.question
      ? `        <h3 class="text-sm font-semibold uppercase tracking-[0.18em] text-${accent}">${esc(answerBox.question)}</h3>`
      : "",
    `        <p class="mt-2 text-base leading-7 text-white/90 sm:text-lg">${esc(answerBox.answer)}</p>`,
    "      </aside>"
  ].filter(Boolean).join("\n");
}

function renderTldr(tldr) {
  if (!Array.isArray(tldr) || tldr.length === 0) return "";
  const items = tldr.map((item) => `
        <li class="rounded-xl border border-white/10 bg-white/5 p-4 text-sm leading-6 text-white/90">${esc(item)}</li>`).join("");
  return [
    `      <section class="mt-6">`,
    `        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-white/55">Em resumo</p>`,
    `        <ul class="mt-3 grid gap-3 sm:grid-cols-3">${items}
        </ul>`,
    "      </section>"
  ].join("\n");
}

function renderFaq(faq) {
  if (!Array.isArray(faq) || faq.length === 0) return "";
  const items = faq.map((item) => `
        <details class="mt-3 rounded-xl border border-white/10 bg-white/5 p-4">
          <summary class="cursor-pointer text-base font-semibold text-white">${esc(item.q)}</summary>
          <p class="mt-2 text-sm leading-6 text-white/85">${esc(item.a)}</p>
        </details>`).join("");
  return [
    `      <section class="mt-10">`,
    `        <h2 class="text-xl font-semibold text-white sm:text-2xl">Perguntas frequentes</h2>${items}`,
    "      </section>"
  ].join("\n");
}

function renderRelatedSection() {
  return [
    "    <!-- BLOG-LEIA-TAMBEM START -->",
    "    <!-- BLOG-LEIA-TAMBEM END -->",
    "    <!-- BLOG-CROSS-SITE START -->",
    "    <!-- BLOG-CROSS-SITE END -->"
  ].join("\n");
}

function renderFluxoPostTemplate({
  relativeRoot,
  contract,
  copy,
  site,
  seoTitle,
  metaDescription,
  canonicalUrl,
  imageUrl,
  faqJsonLd,
  styles,
  fluxoFontPreload,
  headerMarkup,
  footerMarkup
}) {
  const accent = accentFor(site.id);
  return `<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>${esc(seoTitle)}</title>
  <meta name="description" content="${esc(metaDescription)}" />
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
  <link rel="canonical" href="${canonicalUrl}" />
  <link rel="icon" type="image/svg+xml" href="${relativeRoot}favicon.svg" />
  <link rel="alternate icon" href="${relativeRoot}favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="${relativeRoot}apple-touch-icon.png" />
  <meta property="og:type" content="article" />
  <meta property="og:title" content="${esc(seoTitle)}" />
  <meta property="og:description" content="${esc(metaDescription)}" />
  <meta property="og:url" content="${canonicalUrl}" />
  <meta property="og:image" content="${imageUrl}" />
  <meta property="og:site_name" content="${esc(site.name)}" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="${esc(seoTitle)}" />
  <meta name="twitter:description" content="${esc(metaDescription)}" />
  <meta name="twitter:image" content="${imageUrl}" />${fluxoFontPreload}
${styles}
  <script type="application/ld+json">
${buildJsonLd(site, contract, canonicalUrl, imageUrl)}
  </script>${faqJsonLd ? `
  <script type="application/ld+json">
${faqJsonLd}
  </script>` : ""}
</head>
<body class="${copy.bodyClass}" data-site="post">
  ${headerMarkup}

  <nav class="post-subnav rv" aria-label="Atalhos do artigo">
    <div class="container post-subnav-inner">
      <a href="${relativeRoot}blog/" class="pn-btn pn-portal"><span aria-hidden="true">←</span> Portal</a>
      <a href="${relativeRoot}" class="pn-btn pn-mini">Início site</a>
      <a href="#post-content" class="pn-btn pn-mini">Conteúdo</a>
      <a href="${relativeRoot}#form-section" class="pn-btn pn-top">Criar agente <span aria-hidden="true">↗</span></a>
    </div>
  </nav>

  <section class="post-hero">
    <div class="container">
      <nav class="post-breadcrumb rv" aria-label="Localização neste site">
        <a class="bc-link" href="${relativeRoot}">FluxoInteligente IA</a>
        <span class="bc-sep" aria-hidden="true">/</span>
        <a class="bc-link" href="${relativeRoot}blog/">Blog</a>
        <span class="bc-sep" aria-hidden="true">/</span>
        <span class="bc-current">${esc(contract.content.eyebrow || copy.articleLabel)}</span>
      </nav>
      <h1 class="rv rv-d1">${esc(contract.content.headline)}</h1>
      <p class="sub rv rv-d2">${esc(contract.content.summary)}</p>
      <div class="meta rv rv-d3">
        <span class="tag">${esc(contract.content.eyebrow || copy.articleLabel)}</span>
        <span>${brDate(contract.date)}</span>
        <span>Guia prático</span>
      </div>
    </div>
  </section>
  <div class="sec-divider"></div>

  <main class="container post-wrap" id="main">
    <article class="rv rv-scale post-article">
      <div class="cover cover-interactive">
        <img src="${relativeRoot}imagens/posts/${contract.slug}.jpg" alt="${esc(contract.coverAlt || contract.content.headline)}" width="1200" height="630" loading="eager" decoding="async" />
      </div>
      <div class="article-read-track" aria-hidden="true">
        <div class="article-read-fill"></div>
      </div>
      <div class="content" id="post-content">
${renderAnswerBox(contract.content.answerBox, accent)}
${renderTldr(contract.content.tldr)}
${renderSections(contract, accent, relativeRoot)}
${renderFaq(contract.content.faq)}
      </div>
    </article>

    <aside class="side" aria-label="Apoio do artigo">
      <div class="panel rv rv-d1">
        <h4>Neste artigo</h4>
        <div class="toc">
          ${(contract.content.sections || []).slice(0, 6).map((section) => section?.title ? `<a href="#post-content">${esc(section.title)}</a>` : "").filter(Boolean).join("\n          ")}
        </div>
      </div>
      <div class="panel rv rv-d2">
        <h4>Próximo passo</h4>
        <p>Mapeie processos, permissões e integrações antes de colocar um agente em produção.</p>
        <a href="${relativeRoot}#form-section" class="panel-link">Conversar com especialista</a>
      </div>
    </aside>
  </main>

  <section class="container post-cta rv">
    <h2>${esc(copy.ctaTitle)}</h2>
    <p>${esc(copy.ctaBody)}</p>
    <a href="${relativeLink(relativeRoot, copy.ctaPath)}">${esc(copy.ctaLabel)}</a>
  </section>

  <div class="container post-related-wrap">
${renderRelatedSection()}
  </div>

  ${footerMarkup}
</body>
</html>
`;
}

function buildPostTemplate(root, site, contract, relatedCards) {
  const copy = SITE_COPY[site.id];
  const relativeRoot = "../../../../";
  const canonicalPath = buildCardRecord(contract).postPath;
  const canonicalUrl = `${site.baseUrl}/${canonicalPath}`;
  const imageUrl = `${site.baseUrl}/imagens/posts/${contract.slug}.jpg`;
  const isFluxo = site.id === "fluxointeligenteia";

  const emailMarkup = copy.email
    ? `        <p class="mt-3 text-sm text-white/80">Email: <a href="mailto:${esc(copy.email)}" class="font-semibold hover:underline">${esc(
        copy.email
      )}</a></p>`
    : "";

  const styles = isFluxo
    ? `  <link rel="stylesheet" href="${relativeRoot}assets/css/blog.css" />\n  <link rel="stylesheet" href="${relativeRoot}assets/css/site-shell.css" />\n${stylesheetLinks(root, site, relativeRoot)}`
    : stylesheetLinks(root, site, relativeRoot);
  const fluxoFontPreload = isFluxo
    ? `\n  <link rel="preconnect" href="https://fonts.googleapis.com">\n  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>\n  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">`
    : "";
  const bodyAttrs = isFluxo ? ' data-site="post"' : "";
  const headerMarkup = isFluxo
    ? `<div id="cursor-dot" aria-hidden="true"></div>
  <div id="cursor-ring" aria-hidden="true"></div>
  <div id="scroll-progress"></div>
  <div id="flux-slot-header" aria-hidden="true"></div>`
    : `<header class="${copy.headerClass}">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
      <a href="${relativeRoot}" class="text-lg font-semibold tracking-tight text-white">${esc(site.name)}</a>
      <nav class="hidden items-center gap-5 md:flex">
${renderNavLinks(copy, relativeRoot)}
      </nav>
    </div>
  </header>`;
  const footerMarkup = isFluxo
    ? `<div id="flux-slot-footer" aria-hidden="true"></div>
  <script defer src="${relativeRoot}assets/js/site-layout.js"></script>
  <script defer src="${relativeRoot}assets/js/site-shell.js"></script>`
    : `<footer class="${copy.footerClass}">
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
      <div class="grid gap-8 md:grid-cols-3">
        <div>
          <h2 class="text-xl font-semibold text-white">${esc(site.name)}</h2>
          <p class="mt-3 max-w-sm text-sm leading-7 text-white/80">${esc(copy.footerBlurb)}</p>
          <p class="mt-3 text-sm text-white/80">Telefone: <a href="tel:+5511994566726" class="font-semibold hover:underline">${esc(
            copy.phone
          )}</a></p>
${emailMarkup}
        </div>
        <div>
          <h3 class="text-lg font-semibold text-white">Navegacao</h3>
          <nav class="mt-4 grid gap-2 text-sm text-white/85" aria-label="Mapa do site">
${renderFooterLinks(copy, relativeRoot)}
          </nav>
        </div>
        <div>
          <h3 class="text-lg font-semibold text-white">Próximo passo</h3>
          <p class="mt-3 text-sm leading-7 text-white/80">Resposta humana, sem fila generica. Fale com o time comercial do site.</p>
          <a href="${relativeLink(relativeRoot, copy.ctaPath)}" class="mt-4 inline-flex rounded-full border border-white/40 px-5 py-2.5 text-sm font-semibold uppercase tracking-[0.2em] text-white hover:bg-white/10">
            ${esc(copy.ctaLabel)}
          </a>
        </div>
      </div>
      <div class="mt-8 border-t border-white/10 pt-4 text-xs text-white/45">© ${esc(site.name)} - Todos os direitos reservados.</div>
    </div>
  </footer>`;

  const seoTitle = buildSeoTitle(contract, site);
  const metaDescription = buildMetaDescription(contract);
  const faqJsonLd = buildFaqJsonLd(contract);
  if (isFluxo) {
    return renderFluxoPostTemplate({
      relativeRoot,
      contract,
      copy,
      site,
      seoTitle,
      metaDescription,
      canonicalUrl,
      imageUrl,
      faqJsonLd,
      styles,
      fluxoFontPreload,
      headerMarkup,
      footerMarkup
    });
  }
  return `<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>${esc(seoTitle)}</title>
  <meta name="description" content="${esc(metaDescription)}" />
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
  <link rel="canonical" href="${canonicalUrl}" />
  <link rel="icon" type="image/svg+xml" href="${relativeRoot}favicon.svg" />
  <link rel="alternate icon" href="${relativeRoot}favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="${relativeRoot}apple-touch-icon.png" />
  <meta property="og:type" content="article" />
  <meta property="og:title" content="${esc(seoTitle)}" />
  <meta property="og:description" content="${esc(metaDescription)}" />
  <meta property="og:url" content="${canonicalUrl}" />
  <meta property="og:image" content="${imageUrl}" />
  <meta property="og:site_name" content="${esc(site.name)}" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="${esc(seoTitle)}" />
  <meta name="twitter:description" content="${esc(metaDescription)}" />
  <meta name="twitter:image" content="${imageUrl}" />${fluxoFontPreload}
${styles}
  <script type="application/ld+json">
${buildJsonLd(site, contract, canonicalUrl, imageUrl)}
  </script>${faqJsonLd ? `
  <script type="application/ld+json">
${faqJsonLd}
  </script>` : ""}
</head>
<body class="${copy.bodyClass}"${bodyAttrs}>
  ${headerMarkup}

  <main class="mx-auto max-w-4xl px-4 py-8 sm:px-6 sm:py-10">
    <a href="${relativeRoot}" class="inline-flex rounded-full border border-white/40 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white/85 hover:bg-white/10">
      Voltar para o site principal
    </a>

    <article class="mt-5 overflow-hidden rounded-3xl border border-white/15 bg-white/5">
      <figure class="relative">
        <img src="${relativeRoot}imagens/posts/${contract.slug}.jpg" alt="${esc(contract.coverAlt || contract.content.headline)}" class="block w-full object-cover" style="aspect-ratio:1200/630" width="1200" height="630" loading="eager" decoding="async" />
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/85 via-black/45 to-transparent" aria-hidden="true"></div>
        <figcaption class="absolute inset-x-0 bottom-0 p-5 sm:p-8 lg:p-10">
          <span class="block h-1 w-12 rounded-full ${copy.headingBarClass}" aria-hidden="true"></span>
          <p class="mt-3 text-[11px] font-semibold uppercase tracking-[0.22em] text-white/85 sm:text-xs">${esc(contract.content.eyebrow || copy.articleLabel)} • ${brDate(
            contract.date
          )}</p>
          <h1 class="mt-2 text-2xl font-semibold leading-tight text-white sm:text-3xl lg:text-4xl">${esc(
            contract.content.headline
          )}</h1>
          <p class="mt-3 max-w-3xl text-sm leading-6 text-white/90 sm:text-base sm:leading-7">${esc(contract.content.summary)}</p>
        </figcaption>
      </figure>
      <div class="p-5 sm:p-8">
${renderAnswerBox(contract.content.answerBox, accentFor(site.id))}
${renderTldr(contract.content.tldr)}
${renderSections(contract, accentFor(site.id), relativeRoot)}
${renderFaq(contract.content.faq)}
      </div>
    </article>

    <section class="mt-8 rounded-2xl border border-white/15 bg-white/5 p-5">
      <h2 class="text-xl font-semibold text-white">${esc(copy.ctaTitle)}</h2>
      <p class="mt-3 text-sm leading-7 text-white/85 sm:text-base">${esc(copy.ctaBody)}</p>
      <a href="${relativeLink(relativeRoot, copy.ctaPath)}" class="mt-4 inline-flex rounded-full border border-white/40 px-5 py-2.5 text-sm font-semibold uppercase tracking-[0.2em] text-white hover:bg-white/10">
        ${esc(copy.ctaLabel)}
      </a>
    </section>

    <section class="mt-6 flex items-center justify-center">
      <a href="${relativeRoot}blog/" class="inline-flex rounded-full border border-white/40 px-5 py-2.5 text-sm font-semibold uppercase tracking-[0.2em] text-white hover:bg-white/10">
        Veja mais no blog
      </a>
    </section>

${renderRelatedSection()}
  </main>

  ${footerMarkup}
</body>
</html>
`;
}

function buildBlogIndexTemplate(root, site) {
  const copy = SITE_COPY[site.id];
  const styles = stylesheetLinks(root, site, "../");
  return `<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>${esc(copy.indexTitle)}</title>
  <meta name="description" content="${esc(copy.indexDescription)}" />
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
  <link rel="canonical" href="${site.baseUrl}/blog/" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="${esc(copy.indexTitle)}" />
  <meta property="og:description" content="${esc(copy.indexDescription)}" />
  <meta property="og:url" content="${site.baseUrl}/blog/" />
${styles}
</head>
<body class="${copy.bodyClass}">
  <header class="${copy.headerClass}">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
      <a href="../" class="text-lg font-semibold tracking-tight text-white">${esc(site.name)}</a>
      <nav class="hidden items-center gap-5 md:flex">
${renderNavLinks(copy, "../")}
      </nav>
    </div>
  </header>

  <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10">
    <a href="../" class="inline-flex rounded-full border border-white/40 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white/85 hover:bg-white/10">
      Voltar para o site principal
    </a>

    <section class="py-6">
      <h1 class="text-3xl font-semibold text-white sm:text-4xl">${esc(copy.blogName)}</h1>
      <div class="mt-2 h-1 w-[72px] rounded-full ${copy.headingBarClass}"></div>
      <p class="mt-4 max-w-3xl text-sm leading-7 text-white/85">${esc(copy.homeSectionDescription)}</p>

      <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
${INDEX_MARKERS.start}
${INDEX_MARKERS.end}
      </div>
    </section>
  </main>

  <footer class="${copy.footerClass}">
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
      <div class="flex flex-col gap-3 text-sm text-white/75 md:flex-row md:items-center md:justify-between">
        <p>${esc(copy.footerBlurb)}</p>
        <a href="../" class="font-semibold text-white hover:text-white/80">Voltar ao site</a>
      </div>
    </div>
  </footer>
</body>
</html>
`;
}

function ensureBlogIndexFile(root, site) {
  const filePath = path.resolve(root, site.siteRoot, site.blogIndexPath);
  if (fs.existsSync(filePath)) {
    return filePath;
  }

  ensureDir(path.dirname(filePath));
  fs.writeFileSync(filePath, buildBlogIndexTemplate(root, site), "utf8");
  return filePath;
}

function readCardsFromFile(filePath, markers) {
  if (!fs.existsSync(filePath)) {
    return [];
  }

  const content = fs.readFileSync(filePath, "utf8");
  const markup = extractMarkedSegment(content, markers);
  return parseCards(markup);
}

function updateCardsInFile(filePath, markers, cards, context, maxItems) {
  const content = fs.readFileSync(filePath, "utf8");
  const existingCards = readCardsFromFile(filePath, markers);
  const nextCards = mergeCards([...cards, ...existingCards], maxItems);
  const replacement = nextCards.map((card) => renderCard(card, context)).join("\n");
  const updatedContent = replaceMarkedSegment(content, markers, replacement);
  const updated = updatedContent !== content;

  if (updated) {
    fs.writeFileSync(filePath, updatedContent, "utf8");
  }

  return { updated, cards: nextCards };
}

async function ensureCoverImage(root, site, contract, aiConfig) {
  const postsDir = path.resolve(root, site.siteRoot, site.assets.postsDir);
  ensureDir(postsDir);

  const targetPath = path.resolve(postsDir, `${contract.slug}.jpg`);
  if (fs.existsSync(targetPath)) {
    return { path: targetPath, source: "existing", warning: null, altText: "", leakage: null };
  }

  let warning = null;
  if (aiConfig) {
    try {
      const result = await agentGenerateCover({ aiConfig, site, contract, targetPath });
      const leakageWarning = result.leakage?.leaked
        ? `Possível texto vazado na capa (sample: "${result.leakage.sample}")`
        : null;
      return {
        path: targetPath,
        source: result.source,
        warning: leakageWarning,
        altText: result.altText || "",
        leakage: result.leakage || null,
        prompt: result.prompt || ""
      };
    } catch (error) {
      warning = `Cover agent falhou: ${String(error.message || error)}`;
    }
  }

  const fallbackPath = path.resolve(root, site.siteRoot, site.assets.fallbackCover);
  if (!fs.existsSync(fallbackPath)) {
    throw new Error(`Capa fallback não encontrada para ${site.id}: ${site.assets.fallbackCover}`);
  }

  fs.copyFileSync(fallbackPath, targetPath);
  return { path: targetPath, source: "fallback-raw", warning, altText: "", leakage: null };
}

function writePostFile(root, site, contract, relatedCards) {
  const { year, month, day } = formatDateParts(contract.date);
  const siteRelativeDir = path.join(year, month, day, contract.slug);
  const dirPath = path.resolve(root, site.siteRoot, siteRelativeDir);
  ensureDir(dirPath);

  const fileName = `index.${site.renderExtension}`;
  const filePath = path.resolve(dirPath, fileName);
  fs.writeFileSync(filePath, buildPostTemplate(root, site, contract, relatedCards), "utf8");

  return {
    filePath,
    relativePath: path.relative(root, filePath).replace(/\\/g, "/"),
    siteRelativePath: path.join(siteRelativeDir, fileName).replace(/\\/g, "/")
  };
}

function buildSitemapXml(site, cards) {
  const latestDate = cards[0]?.date || new Date().toISOString().slice(0, 10);
  const entries = [
    {
      url: `${site.baseUrl}/`,
      lastmod: latestDate,
      changefreq: "weekly",
      priority: "1.0"
    },
    {
      url: `${site.baseUrl}/blog/`,
      lastmod: latestDate,
      changefreq: "weekly",
      priority: "0.9"
    },
    ...(site.seo?.extraPaths || []).map((entry) => ({
      url: `${site.baseUrl}${entry.path}`,
      lastmod: entry.lastmod,
      changefreq: entry.changefreq,
      priority: entry.priority
    })),
    ...cards.map((card) => ({
      url: `${site.baseUrl}/${card.postPath}`,
      lastmod: card.date,
      changefreq: "monthly",
      priority: "0.8"
    }))
  ];

  return [
    '<?xml version="1.0" encoding="UTF-8"?>',
    '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
    ...entries.map((entry) =>
      [
        "  <url>",
        `    <loc>${entry.url}</loc>`,
        `    <lastmod>${entry.lastmod}</lastmod>`,
        `    <changefreq>${entry.changefreq}</changefreq>`,
        `    <priority>${entry.priority}</priority>`,
        "  </url>"
      ].join("\n")
    ),
    "</urlset>",
    ""
  ].join("\n");
}

function updateSitemap(root, site, cards) {
  const filePath = path.resolve(root, site.siteRoot, site.seo.sitemapPath);
  const nextContent = buildSitemapXml(site, mergeCards(cards));
  const currentContent = fs.existsSync(filePath) ? fs.readFileSync(filePath, "utf8") : "";
  const updated = currentContent !== nextContent;

  if (updated) {
    fs.writeFileSync(filePath, nextContent, "utf8");
  }

  return { updated, filePath };
}

function updateRobots(root, site) {
  const filePath = path.resolve(root, site.siteRoot, site.seo.robotsPath);
  const nextContent = [
    "User-agent: *",
    "Allow: /",
    "",
    `Sitemap: ${site.baseUrl}/sitemap.xml`,
    ""
  ].join("\n");
  const currentContent = fs.existsSync(filePath) ? fs.readFileSync(filePath, "utf8") : "";
  const updated = currentContent !== nextContent;

  if (updated) {
    fs.writeFileSync(filePath, nextContent, "utf8");
  }

  return { updated, filePath };
}

function buildPhpLintTargets(root, site, postSiteRelativePath) {
  if (!site.requiresPhpLint) {
    return [];
  }

  const targets = [...(site.lintTargets || [])];
  if (site.renderExtension === "php") {
    targets.push(postSiteRelativePath);
  }

  return [...new Set(targets)].map((relativePath) =>
    path.relative(root, path.resolve(root, site.siteRoot, relativePath)).replace(/\\/g, "/")
  );
}

async function publishSitePost(root, site, contract, aiConfig) {
  const blogIndexFile = ensureBlogIndexFile(root, site);
  const existingBlogCards = readCardsFromFile(blogIndexFile, INDEX_MARKERS);
  const newCard = buildCardRecord(contract);
  const relatedCards = mergeCards(existingBlogCards.filter((card) => card.slug !== contract.slug), 3);

  const cover = await ensureCoverImage(root, site, contract, aiConfig);
  if (cover.altText) {
    contract.coverAlt = cover.altText;
  }
  const post = writePostFile(root, site, contract, relatedCards);

  const homeFile = path.resolve(root, site.siteRoot, site.homePath);
  const home = updateCardsInFile(homeFile, HOME_MARKERS, [newCard], "home", 3);
  const blog = updateCardsInFile(blogIndexFile, INDEX_MARKERS, [newCard], "blog-index", Infinity);
  const sitemap = updateSitemap(root, site, blog.cards);
  const robots = updateRobots(root, site);

  return {
    postPath: post.relativePath,
    homeUpdated: home.updated,
    blogUpdated: blog.updated,
    sitemapUpdated: sitemap.updated,
    robotsUpdated: robots.updated,
    coverSource: cover.source,
    coverAlt: cover.altText || "",
    coverLeakage: cover.leakage || null,
    warning: cover.warning,
    phpLintTargets: buildPhpLintTargets(root, site, post.siteRelativePath)
  };
}

module.exports = {
  HOME_MARKERS,
  INDEX_MARKERS,
  publishSitePost,
  buildPostTemplate,
  buildSeoTitle,
  smartTruncate
};
