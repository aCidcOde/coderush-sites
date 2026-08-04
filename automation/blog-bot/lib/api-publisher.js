const fs = require("node:fs");
const path = require("node:path");
const { generateCover } = require("./cover-agent");
const { smartTruncate } = require("./publisher");

const SEO_TITLE_LIMIT = 70;
const COVER_SIZE_LIMIT = 500 * 1024;

function ensureDir(dirPath) {
  fs.mkdirSync(dirPath, { recursive: true });
}

function nowInBrtIso() {
  const fmt = new Intl.DateTimeFormat("sv-SE", {
    timeZone: "America/Sao_Paulo",
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
    hour12: false
  });
  const parts = Object.fromEntries(
    fmt.formatToParts(new Date()).map((p) => [p.type, p.value])
  );
  return `${parts.year}-${parts.month}-${parts.day}T${parts.hour}:${parts.minute}:${parts.second}-03:00`;
}

function buildPayload({ site, contract, aiConfig }) {
  const headline = contract.content?.headline || "";
  const seoTitle = contract.content?.seoTitle
    || smartTruncate(headline, SEO_TITLE_LIMIT);
  const content = { ...contract.content, seoTitle };
  return {
    siteId: site.id,
    date: contract.date,
    slug: contract.slug,
    postType: contract.postType || "",
    focus: contract.focus || "",
    theme: contract.theme || "",
    angle: contract.angle || "",
    sources: Array.isArray(contract.sources) ? contract.sources : [],
    coverAlt: contract.coverAlt || "",
    generatedAt: contract.generatedAt || nowInBrtIso(),
    generatedBy: "bot",
    model: aiConfig?.textModel || "unknown",
    content
  };
}

async function ensureCoverFile({ root, site, contract, aiConfig }) {
  const coverDir = path.resolve(
    root,
    "automation",
    "blog-bot",
    "out",
    site.id,
    "covers"
  );
  ensureDir(coverDir);
  const targetPath = path.resolve(coverDir, `${contract.slug}.jpg`);
  const altPath = `${targetPath}.alt.txt`;
  if (fs.existsSync(targetPath)) {
    const cachedAlt = fs.existsSync(altPath) ? fs.readFileSync(altPath, "utf8").trim() : "";
    return {
      path: targetPath,
      source: "existing",
      altText: cachedAlt,
      warning: null,
      leakage: null,
      prompt: ""
    };
  }
  if (!aiConfig) {
    throw new Error(`aiConfig ausente para gerar cover do site ${site.id}`);
  }
  const result = await generateCover({ aiConfig, site, contract, targetPath });
  if (result.altText) {
    fs.writeFileSync(altPath, result.altText, "utf8");
  }
  return {
    path: targetPath,
    source: result.source,
    altText: result.altText || "",
    warning: result.leakage?.leaked
      ? `Possivel texto vazado na capa (sample: "${result.leakage.sample}")`
      : null,
    leakage: result.leakage || null,
    prompt: result.prompt || ""
  };
}

function markdownFromContent(content) {
  const parts = [];
  const ab = content?.answerBox || {};
  if (ab.question) {
    parts.push(`## ${ab.question}\n\n${ab.answer || ""}`);
  }
  const tldr = Array.isArray(content?.tldr) ? content.tldr : [];
  if (tldr.length) {
    parts.push("**Em resumo:**\n\n" + tldr.map((t) => `- ${t}`).join("\n"));
  }
  for (const s of content?.sections || []) {
    const title = (s.title || "").trim();
    if (s.type === "prose") {
      parts.push((title ? `## ${title}\n\n` : "") + (s.body || ""));
    } else if (s.type === "list") {
      const items = (s.items || []).map((i) => `- ${i}`).join("\n");
      parts.push((title ? `## ${title}\n\n` : "") + items);
    } else if (s.type === "callout") {
      parts.push(`> **${title || "Atenção"}** — ${(s.body || "").replace(/\n/g, " ")}`);
    }
    // cta-inline: nao replicado — o CTA e do template do destino
  }
  const faq = Array.isArray(content?.faq) ? content.faq : [];
  if (faq.length) {
    parts.push(
      "## Perguntas frequentes\n\n"
        + faq.map((f) => `**${f.q}**\n\n${f.a}`).join("\n\n")
    );
  }
  return parts.filter((p) => p.trim()).join("\n\n");
}

function bfrCategory(contract) {
  const hay = `${contract.theme || ""} ${contract.angle || ""}`.toLowerCase();
  if (/governan|auditoria|permiss|roi|seguran|observabilidade|log/.test(hay)) {
    return "Governança e ROI";
  }
  if (/automa|integra|n8n|canal|canais|atendimento/.test(hay)) {
    return "Automação e integrações";
  }
  return "Engenharia de agentes";
}

function buildBfrPayload({ site, contract, coverAlt }) {
  const content = contract.content || {};
  const description = String(contract.description || content.summary || "").slice(0, 320);
  // SEMPRE "agora" em BRT: o front da BFR esconde posts com published_at futuro,
  // entao qualquer data adiante do relogio deles some do site ate a hora chegar.
  const publishedAt = nowInBrtIso();
  return {
    external_id: `bot-${contract.date}-${contract.slug}`,
    slug: contract.slug,
    title: content.headline || contract.title,
    excerpt: String(content.summary || description).slice(0, 300),
    body: markdownFromContent(content),
    category: bfrCategory(contract),
    author_name: "BFR Intelligence",
    featured_image_url: `${site.api.coverPublicBaseUrl}/${contract.slug}.jpg`,
    featured_image_alt: coverAlt || content.headline || contract.title,
    meta_title: String(content.seoTitle || content.headline || "").slice(0, 70),
    meta_description: description,
    tags: ["inteligência artificial", "agentes", "automação", "governança"],
    status: "published",
    published_at: publishedAt
  };
}

function publishCoverPublicly(root, site, contract, coverPath) {
  // A capa precisa de URL publica: copia pro diretorio servido (excecao do 301 do fluxo),
  // que o workflow commita e o deploy publica junto.
  const publicDir = path.resolve(root, site.api.coverPublicDir);
  ensureDir(publicDir);
  const target = path.resolve(publicDir, `${contract.slug}.jpg`);
  fs.copyFileSync(coverPath, target);
  return target;
}

async function postJsonToApi({ site, payload }) {
  const token = resolveToken(site);
  const url = buildEndpoint(site);
  const response = await fetch(url, {
    method: "POST",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
      // Cloudflare do destino bloqueia UA de bot (error 1010)
      "User-Agent":
        "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36"
    },
    body: JSON.stringify(payload)
  });
  const rawText = await response.text();
  let body = null;
  try {
    body = rawText ? JSON.parse(rawText) : null;
  } catch (_err) {
    body = { raw: rawText };
  }
  return { status: response.status, ok: response.ok, body };
}

function resolveToken(site) {
  const envName = site.api?.tokenEnv || "BLOG_API_TOKEN";
  const token = String(process.env[envName] || "").trim();
  if (!token) {
    throw new Error(
      `Env var ${envName} ausente — configure o token da API antes de publicar.`
    );
  }
  return token;
}

function buildEndpoint(site) {
  const baseUrl = site.api?.baseUrl;
  const endpoint = site.api?.endpoint || "/api/blog/posts";
  if (!baseUrl) {
    throw new Error(`api.baseUrl ausente em sites.json para ${site.id}`);
  }
  return new URL(endpoint, baseUrl).toString();
}

async function postToApi({ site, payload, coverPath }) {
  const token = resolveToken(site);
  const url = buildEndpoint(site);

  const coverBytes = fs.readFileSync(coverPath);
  const coverBlob = new Blob([coverBytes], { type: "image/jpeg" });
  const form = new FormData();
  form.append("cover", coverBlob, `${payload.slug}.jpg`);
  form.append("payload", JSON.stringify(payload));

  const response = await fetch(url, {
    method: "POST",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`
    },
    body: form
  });

  const rawText = await response.text();
  let body = null;
  try {
    body = rawText ? JSON.parse(rawText) : null;
  } catch (_err) {
    body = { raw: rawText };
  }

  return {
    status: response.status,
    ok: response.ok,
    body
  };
}

async function publishApiPost(root, site, contract, aiConfig) {
  const cover = await ensureCoverFile({ root, site, contract, aiConfig });
  if (cover.altText && !contract.coverAlt) {
    contract.coverAlt = cover.altText;
  }
  if (!contract.coverAlt) {
    throw new Error(
      `coverAlt ausente para ${site.id}/${contract.slug}: regenere a capa removendo ${cover.path}`
    );
  }

  if (site.api?.format === "bfr-json") {
    const publicPath = publishCoverPublicly(root, site, contract, cover.path);
    const payload = buildBfrPayload({ site, contract, coverAlt: contract.coverAlt });
    const apiResult = await postJsonToApi({ site, payload });
    if (!apiResult.ok && apiResult.status !== 200) {
      const detail = apiResult.body?.message || apiResult.body?.raw || "";
      throw new Error(
        `API ${apiResult.status} ao publicar ${site.id}/${contract.slug}: `
          + (typeof detail === "string" ? detail : JSON.stringify(detail))
      );
    }
    return {
      apiStatus: apiResult.status,
      apiUrl: `${site.baseUrl}/conteudos/artigo.html?slug=${apiResult.body?.data?.slug || contract.slug}`,
      apiId: apiResult.body?.data?.id || null,
      apiPublishedAt: payload.published_at,
      apiResponse: apiResult.body,
      coverPath: publicPath,
      coverSize: fs.statSync(cover.path).size,
      coverSource: cover.source,
      coverAlt: cover.altText || contract.coverAlt || "",
      coverLeakage: cover.leakage || null,
      warning: cover.warning
    };
  }

  const coverSize = fs.statSync(cover.path).size;
  if (coverSize > COVER_SIZE_LIMIT) {
    throw new Error(
      `Cover ${cover.path} excede ${Math.round(COVER_SIZE_LIMIT / 1024)}KB`
        + ` (${Math.round(coverSize / 1024)}KB) — limite da API ${site.id}`
    );
  }

  const payload = buildPayload({ site, contract, aiConfig });
  const apiResult = await postToApi({ site, payload, coverPath: cover.path });

  const treatAsOk = apiResult.ok || apiResult.status === 200;
  if (!treatAsOk) {
    const detail =
      apiResult.body?.message
      || apiResult.body?.errors
      || apiResult.body?.raw
      || "";
    const detailText = typeof detail === "string"
      ? detail
      : JSON.stringify(detail);
    throw new Error(
      `API ${apiResult.status} ao publicar ${site.id}/${contract.slug}: ${detailText}`
    );
  }

  return {
    apiStatus: apiResult.status,
    apiUrl: apiResult.body?.url || null,
    apiId: apiResult.body?.id || null,
    apiPublishedAt: apiResult.body?.publishedAt || null,
    apiResponse: apiResult.body,
    coverPath: cover.path,
    coverSize,
    coverSource: cover.source,
    coverAlt: cover.altText || "",
    coverLeakage: cover.leakage || null,
    warning: cover.warning
  };
}

module.exports = { publishApiPost, buildPayload };
