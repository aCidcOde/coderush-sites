const fs = require("node:fs");
const path = require("node:path");

function esc(value) {
  return String(value || "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function formatDateParts(isoDate) {
  const [year, month, day] = isoDate.split("-");
  return { year, month, day };
}

function brDate(isoDate) {
  const [year, month, day] = isoDate.split("-");
  return `${day}/${month}/${year}`;
}

function toParagraphs(sections) {
  return (sections || [])
    .map((section) => `<h2>${esc(section.title)}</h2>\n<p>${esc(section.body)}</p>`)
    .join("\n\n");
}

function ensureCoverImage(root, slug) {
  const postsDir = path.resolve(root, "sistemavendadireta", "imagens", "posts");
  const target = path.resolve(postsDir, `${slug}.jpg`);
  if (fs.existsSync(target)) {
    return target;
  }

  const fallback = path.resolve(
    postsDir,
    "agentes-de-ia-em-2026-mcp-stateful-e-governanca-para-operar-em-escala.jpg"
  );
  fs.copyFileSync(fallback, target);
  return target;
}

function buildCard({ href, image, title, excerpt, imagePrefix }) {
  return [
    '<article class="overflow-hidden rounded-2xl border border-white/20 bg-white/5">',
    `  <a href="${href}">`,
    `    <img src="${imagePrefix}${image}" alt="${esc(title)}" class="h-44 w-full object-cover" width="1200" height="630" loading="lazy" />`,
    "  </a>",
    '  <div class="p-4">',
    `    <h2 class="font-[var(--font-heading)] text-base leading-snug"><a href="${href}" class="hover:underline">${esc(title)}</a></h2>`,
    `    <p class="mt-2 text-sm text-white/85">${esc(excerpt)}</p>`,
    "  </div>",
    "</article>"
  ].join("\n");
}

function extractCards(html) {
  const matches = html.match(/<article class="overflow-hidden[\s\S]*?<\/article>/g);
  return matches || [];
}

function updateBlogIndex(root, postMeta) {
  const filePath = path.resolve(root, "sistemavendadireta", "blog", "index.php");
  const content = fs.readFileSync(filePath, "utf8");
  const cards = extractCards(content);

  const href = `../${postMeta.year}/${postMeta.month}/${postMeta.day}/${postMeta.slug}/`;
  const newCard = buildCard({
    href,
    image: `${postMeta.slug}.jpg`,
    title: postMeta.title,
    excerpt: postMeta.excerpt,
    imagePrefix: "../imagens/posts/"
  });

  if (content.includes(href)) {
    return { updated: false };
  }

  const cardsContainerRegex = /(<div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">\s*)([\s\S]*?)(\s*<\/div>)/;
  const match = content.match(cardsContainerRegex);
  if (!match) {
    throw new Error("Nao foi possivel localizar grid de cards do blog SVD.");
  }

  const updatedCards = [newCard, ...cards].join("\n");
  const updated = content.replace(cardsContainerRegex, `$1${updatedCards}$3`);
  fs.writeFileSync(filePath, updated, "utf8");
  return { updated: true };
}

function updateHomeHighlights(root, postMeta) {
  const filePath = path.resolve(root, "sistemavendadireta", "index.php");
  const content = fs.readFileSync(filePath, "utf8");

  const sectionRegex =
    /(<section class="py-10">[\s\S]*?<div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">\s*)([\s\S]*?)(\s*<\/div>\s*<\/section>)/;
  const match = content.match(sectionRegex);
  if (!match) {
    throw new Error("Nao foi possivel localizar secao Blog SVD na home.");
  }

  const existingCards = extractCards(match[2]);
  const href = `${postMeta.year}/${postMeta.month}/${postMeta.day}/${postMeta.slug}/`;
  if (match[2].includes(href)) {
    return { updated: false };
  }

  const newCard = buildCard({
    href,
    image: `${postMeta.slug}.jpg`,
    title: postMeta.title,
    excerpt: postMeta.excerpt,
    imagePrefix: "imagens/posts/"
  });

  const nextCards = [newCard, ...existingCards].slice(0, 3).join("\n");
  const updated = content.replace(sectionRegex, `$1${nextCards}$3`);
  fs.writeFileSync(filePath, updated, "utf8");
  return { updated: true };
}

function updateSitemap(root, postMeta) {
  const filePath = path.resolve(root, "sistemavendadireta", "sitemap.xml");
  let content = fs.readFileSync(filePath, "utf8");
  const postUrl = `https://www.sistemavendadireta.com.br/${postMeta.year}/${postMeta.month}/${postMeta.day}/${postMeta.slug}/`;

  content = content.replace(
    /(<loc>https:\/\/www\.sistemavendadireta\.com\.br\/<\/loc>\s*[\s\S]*?<lastmod>)([^<]+)(<\/lastmod>)/,
    `$1${postMeta.date}$3`
  );
  content = content.replace(
    /(<loc>https:\/\/www\.sistemavendadireta\.com\.br\/blog\/<\/loc>\s*[\s\S]*?<lastmod>)([^<]+)(<\/lastmod>)/,
    `$1${postMeta.date}$3`
  );

  if (!content.includes(postUrl)) {
    const newEntry = [
      "  <url>",
      `    <loc>${postUrl}</loc>`,
      `    <lastmod>${postMeta.date}</lastmod>`,
      "    <changefreq>monthly</changefreq>",
      "    <priority>0.8</priority>",
      "  </url>"
    ].join("\n");
    content = content.replace("</urlset>", `${newEntry}\n</urlset>`);
  }

  fs.writeFileSync(filePath, content, "utf8");
  return { updated: true };
}

function writePostFile(root, contract) {
  const { year, month, day } = formatDateParts(contract.date);
  const dirPath = path.resolve(root, "sistemavendadireta", year, month, day, contract.slug);
  fs.mkdirSync(dirPath, { recursive: true });
  ensureCoverImage(root, contract.slug);

  const postUrl = `https://www.sistemavendadireta.com.br/${year}/${month}/${day}/${contract.slug}/`;
  const postTitle = `${contract.title} | Sistema Venda Direta`;
  const imageUrl = `https://www.sistemavendadireta.com.br/imagens/posts/${contract.slug}.jpg`;
  const articleHtml = toParagraphs(contract.content.sections);

  const template = `<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>${esc(postTitle)}</title>
  <meta name="description" content="${esc(contract.description)}" />
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
  <link rel="canonical" href="${postUrl}" />
  <meta property="og:type" content="article" />
  <meta property="og:title" content="${esc(postTitle)}" />
  <meta property="og:description" content="${esc(contract.description)}" />
  <meta property="og:url" content="${postUrl}" />
  <meta property="og:image" content="${imageUrl}" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="${esc(postTitle)}" />
  <meta name="twitter:description" content="${esc(contract.description)}" />
  <meta name="twitter:image" content="${imageUrl}" />
  <link rel="stylesheet" href="../../../../css/site-tailwind.css" />
  <link rel="stylesheet" href="../../../../css/site-optimizations.css" />
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    "headline": "${esc(postTitle)}",
    "description": "${esc(contract.description)}",
    "datePublished": "${contract.date}T09:00:00-03:00",
    "dateModified": "${contract.date}T09:00:00-03:00",
    "mainEntityOfPage": {"@type": "WebPage", "@id": "${postUrl}"},
    "image": ["${imageUrl}"],
    "author": {"@type": "Organization", "name": "Sistema Venda Direta"},
    "publisher": {"@type": "Organization", "name": "Sistema Venda Direta"}
  }
  </script>
</head>
<body class="bg-brand text-white antialiased font-[var(--font-body)] site-optimized">
  <main class="mx-auto max-w-[900px] px-4 py-8 sm:px-6 sm:py-10">
    <a href="../../../../" class="inline-flex rounded-full border border-white/60 px-4 py-2 text-xs font-semibold uppercase tracking-wide hover:bg-white/10">Voltar para o site principal</a>
    <article class="mt-5 rounded-3xl border border-white/20 bg-white/5 p-5 sm:p-8">
      <p class="text-xs font-medium uppercase tracking-wide text-white/70">Blog SVD • ${brDate(contract.date)}</p>
      <h1 class="mt-2 font-[var(--font-heading)] text-3xl leading-tight sm:text-4xl">${esc(contract.content.headline)}</h1>
      <img src="../../../../imagens/posts/${contract.slug}.jpg" alt="${esc(contract.content.headline)}" class="mt-6 w-full rounded-2xl border border-white/20" width="1200" height="630" />
      <div class="prose prose-invert mt-6 max-w-none prose-headings:text-white prose-p:text-white/90">
        <p>${esc(contract.content.summary)}</p>
        ${articleHtml}
        <h2>Fontes oficiais</h2>
        <ul>
          ${contract.sources.map((url) => `<li><a href="${url}" target="_blank" rel="noopener noreferrer">${url}</a></li>`).join("\n          ")}
        </ul>
      </div>
    </article>
  </main>
</body>
</html>
`;

  const filePath = path.resolve(dirPath, "index.php");
  fs.writeFileSync(filePath, template, "utf8");
  return { filePath, year, month, day };
}

function publishSvdPost(root, contract) {
  const post = writePostFile(root, contract);
  const excerpt = contract.content.summary.slice(0, 180);
  const postMeta = {
    date: contract.date,
    slug: contract.slug,
    title: contract.content.headline,
    excerpt,
    year: post.year,
    month: post.month,
    day: post.day
  };

  const home = updateHomeHighlights(root, postMeta);
  const blog = updateBlogIndex(root, postMeta);
  const sitemap = updateSitemap(root, postMeta);

  return {
    postPath: filePathRelative(root, post.filePath),
    homeUpdated: home.updated,
    blogUpdated: blog.updated,
    sitemapUpdated: sitemap.updated
  };
}

function filePathRelative(root, filePath) {
  return path.relative(root, filePath);
}

module.exports = {
  publishSvdPost
};
