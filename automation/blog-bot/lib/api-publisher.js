const fs = require("node:fs");
const path = require("node:path");
const { generateCover } = require("./cover-agent");
const { smartTruncate } = require("./publisher");

const SEO_TITLE_LIMIT = 70;
const COVER_SIZE_LIMIT = 500 * 1024;

function ensureDir(dirPath) {
  fs.mkdirSync(dirPath, { recursive: true });
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
    generatedAt: contract.generatedAt || new Date().toISOString(),
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
  if (fs.existsSync(targetPath)) {
    return {
      path: targetPath,
      source: "existing",
      altText: "",
      warning: null,
      leakage: null,
      prompt: ""
    };
  }
  if (!aiConfig) {
    throw new Error(`aiConfig ausente para gerar cover do site ${site.id}`);
  }
  const result = await generateCover({ aiConfig, site, contract, targetPath });
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
