const fs = require("node:fs");
const crypto = require("node:crypto");
const { execFileSync, execSync } = require("node:child_process");
const { generateWithOpenAI, generateWithOpenRouter } = require("./ai-writer");
const { siteProfile } = require("./site-strategy");
const { loadRecentCoverAlts } = require("./recent-posts");

const COMPOSITION_VARIATIONS = [
  "isometric 3D angle from upper-right, blocks/planes arranged like a system diagram, generous negative space top-left",
  "top-down aerial flat-lay composition, elements arranged as a network or grid, central focal cluster",
  "macro close-up of a single textured object or material detail, shallow depth-of-field, soft background bokeh",
  "mid-shot wide cinematic framing, layered foreground/midground/background depth, rim light separating planes",
  "low-angle dramatic perspective looking up at a single vertical structure or stack, atmospheric fog at base",
  "dutch-tilt diagonal composition, energy lines crossing the frame, asymmetric balance with one heavy quadrant",
  "side-profile lateral composition with strong silhouette and backlit rim, layered transparent planes receding",
  "frontal symmetrical hero shot of a single abstract construct, centered with radial light burst behind"
];

function pickDeterministic(list, hashKey, exclude = new Set()) {
  const all = list && list.length ? list : [""];
  const filtered = exclude.size ? all.filter((item) => !exclude.has(item)) : all;
  const pool = filtered.length ? filtered : all;
  const hash = crypto.createHash("sha1").update(hashKey).digest("hex");
  const index = parseInt(hash.slice(0, 8), 16) % pool.length;
  return pool[index];
}

function pickMotifSubset(motifs, hashKey, count = 2) {
  if (!motifs || !motifs.length) return [];
  if (motifs.length <= count) return [...motifs];
  const hash = crypto.createHash("sha1").update(hashKey).digest("hex");
  const startIndex = parseInt(hash.slice(0, 8), 16) % motifs.length;
  const offset = (parseInt(hash.slice(8, 16), 16) % (motifs.length - 1)) + 1;
  const second = (startIndex + offset) % motifs.length;
  return [motifs[startIndex], motifs[second]];
}

const TARGET_WIDTH = 1200;
const TARGET_HEIGHT = 630;
const TEXT_LEAKAGE_ALPHANUMERIC_THRESHOLD = 8;

const MODEL_CATALOG = {
  "gpt-5-image": "openai/gpt-5-image",
  "gpt-5-image-mini": "openai/gpt-5-image-mini",
  "gpt-5.4-image-2": "openai/gpt-5.4-image-2",
  "gemini-3-pro": "google/gemini-3-pro-image-preview",
  "gemini-3-flash": "google/gemini-3.1-flash-image-preview",
  "gemini-flash": "google/gemini-2.5-flash-image"
};

const DEFAULT_PROMPT_MODEL = "anthropic/claude-sonnet-4-6";

function resolveModelId(name) {
  if (!name) return "";
  return MODEL_CATALOG[name] || name;
}

function joinList(items, fallback = "") {
  const list = (items || []).filter(Boolean);
  if (list.length === 0) return fallback;
  if (list.length === 1) return list[0];
  if (list.length === 2) return `${list[0]} e ${list[1]}`;
  return `${list.slice(0, -1).join(", ")} e ${list[list.length - 1]}`;
}

function defaultCoverArt() {
  return {
    paletteHex: ["#0b1220", "#3b82f6", "#a78bfa"],
    paletteDescription: "azul escuro com brilhos azul e violeta",
    lighting: "iluminacao cinematografica suave",
    mood: "premium, editorial, atmosfera de revista de tecnologia",
    visualMotifs: ["abstracao tecnologica clara", "metafora visual unica e forte"],
    avoid: ["texto", "logos", "telas de UI", "rostos em close"]
  };
}

function buildPromptInstruction({ site, contract, coverArt, recentAlts = [] }) {
  const profile = siteProfile(site.id) || {};
  const angle = contract.angle || "";
  const personaShort = profile.personaShort || "leitores tecnicos";
  const longTail = profile.keywords?.longTail?.[0] || contract.theme || "";
  const slug = contract.slug || contract.content?.headline || contract.title || "";

  const motifSubset = pickMotifSubset(coverArt.visualMotifs, `${site.id}:motif:${slug}`, 2);
  const motifs = joinList(motifSubset, "uma metafora visual unica");
  const composition = pickDeterministic(COMPOSITION_VARIATIONS, `${site.id}:comp:${slug}`);
  const avoidList = joinList(coverArt.avoid, "texto, logos, telas, rostos em close");
  const character = coverArt.characterReference;

  const recentBlock = recentAlts.length
    ? [
        "",
        "EVITE REPETIR essas capas recentes do mesmo site (use composicao, metafora central e elementos visuais distintos):",
        ...recentAlts.slice(0, 5).map((item, idx) => `${idx + 1}. ${item.alt}`),
        "Sua capa deve ter uma metafora visual e composicao claramente diferente das listadas acima."
      ].join("\n")
    : "";

  const lines = [
    "Voce e diretor de arte de uma revista de tecnologia premium (referencia: capa Wired ou MIT Technology Review).",
    "Sua tarefa: escrever UM unico paragrafo (4-6 frases, em ingles, denso e visual) descrevendo a capa 16:9 (1200x630px) de um post de blog corporativo.",
    "",
    "Contexto editorial:",
    `- Marca: ${site.name}`,
    `- Persona-alvo: ${personaShort}`,
    `- Titulo do post: "${contract.content?.headline || contract.title || ""}"`,
    `- Resumo do post: "${contract.content?.summary || contract.description || ""}"`,
    `- Tema: ${contract.theme || "n/a"}`,
    `- Angulo da semana: ${angle || "n/a"}`,
    `- Pergunta long-tail que orienta o tom: "${longTail}"`,
    "",
    "METAFORA CENTRAL (mais importante):",
    "- Extraia 2-3 substantivos concretos do titulo OU tema (ex: 'banco de dados', 'cache', 'permissoes', 'auditoria', 'comissionamento', 'inadimplencia', 'certidao', 'inventario').",
    "- Construa UMA metafora visual unica em torno desses substantivos concretos — nao use abstracoes genericas tipo 'documentos flutuando', 'rede luminosa', 'dossie executivo' a menos que sejam diretamente exigidas pelo titulo.",
    "- A metafora deve ser instantaneamente reconhecivel como representando ESSE post especifico, nao um post generico do site.",
    "",
    "Direcao visual da marca (siga estritamente):",
    `- Paleta: ${coverArt.paletteDescription}`,
    `- Iluminacao: ${coverArt.lighting}`,
    `- Mood: ${coverArt.mood}`,
    `- Motivos visuais para esta edicao (use 1 ou combine os 2 — NAO precisa usar todos): ${motifs}`,
    `- Composicao desta edicao: ${composition}`,
    `- EVITAR sob qualquer circunstancia: ${avoidList}`,
    recentBlock
  ];

  if (character) {
    lines.push(
      "",
      `Personagem mascote disponivel: ${character.name}`,
      `- Descricao fisica (rendering reference, descreva-o explicitamente se entrar): ${character.description}`,
      `- Regra de uso: ${character.usageRule}`,
      "- Decida com base no angulo do post se ele entra ou nao. Se entrar, descreva-o explicitamente no paragrafo. Se nao entrar, NAO mencione o personagem nem deixe traco humanoide na cena."
    );
  }

  lines.push(
    "",
    "REGRAS ABSOLUTAS:",
    "- A imagem NUNCA pode conter texto, palavras, letras, numeros, logotipos ou simbolos legiveis.",
    "- SEM mockup de tela cheia de UI, SEM watermark.",
    character
      ? "- ZERO pessoas reais ou personagens humanos fotorrealisticos (sem businessman, sem advogado, sem persona em terno, sem silhuetas humanas, sem maos humanas). A unica figura permitida e o mascote estilizado descrito acima, e SOMENTE quando a regra de uso indicar. Em qualquer outro caso, use composicao puramente abstrata."
      : "- ZERO pessoas reais ou personagens humanos fotorrealisticos. Sem silhuetas, sem businessman, sem maos humanas. Composicao puramente abstrata.",
    "- Uma unica metafora visual forte e clara, com area de respiro generosa.",
    "- Composicao 16:9, com ponto focal claro e profundidade.",
    "",
    "ESCREVA o paragrafo com:",
    "1. Assunto principal (a metafora visual concreta, nao abstrata demais)",
    "2. Composicao e enquadramento (regra dos tercos, lente, distancia)",
    "3. Paleta exata de cores presentes",
    "4. Qualidade de luz (rim light, volumetric fog, godrays etc.)",
    "5. Atmosfera/mood",
    "6. Estilo de render (cinematografico fotorrealista, ilustracao editorial 3D, painting digital, etc.)",
    "",
    "Responda APENAS em JSON valido: { \"prompt\": \"<paragrafo unico em ingles>\" }"
  );

  return lines.join("\n");
}

function buildAltTextInstruction({ site, contract, coverArt }) {
  const personaShort = siteProfile(site.id)?.personaShort || "leitores";
  return [
    "Gere um alt-text descritivo em pt-BR para a capa de um artigo, com 90-140 caracteres, sem mencionar marca, sem aspas, sem ponto final.",
    "O alt-text deve descrever objetivamente o que aparece na imagem (assunto visual + paleta dominante + atmosfera), util para acessibilidade e SEO.",
    "Nao escreva 'imagem ilustrativa', 'foto de' nem o titulo do post — descreva o que se ve.",
    "",
    `Marca: ${site.name}`,
    `Titulo do post: "${contract.content?.headline || contract.title || ""}"`,
    `Persona-alvo: ${personaShort}`,
    `Direcao visual: ${coverArt.paletteDescription}; ${coverArt.mood}`,
    "",
    "Responda APENAS em JSON valido: { \"alt\": \"<texto descritivo>\" }"
  ].join("\n");
}

function resolvePromptAiConfig(aiConfig, env = process.env) {
  const dedicatedModel = env.BLOG_BOT_PROMPT_MODEL || DEFAULT_PROMPT_MODEL;
  if (!aiConfig) return null;
  return { ...aiConfig, textModel: dedicatedModel };
}

async function callTextModel({ aiConfig, prompt }) {
  if (aiConfig.provider === "openrouter") {
    return generateWithOpenRouter({
      apiKey: aiConfig.apiKey,
      model: aiConfig.textModel,
      prompt,
      appUrl: aiConfig.appUrl,
      appName: aiConfig.appName
    });
  }
  if (aiConfig.provider === "openai") {
    return generateWithOpenAI({
      apiKey: aiConfig.apiKey,
      model: aiConfig.textModel,
      prompt
    });
  }
  return null;
}

async function buildVisualPrompt({ aiConfig, site, contract, coverArt, recentAlts = [] }) {
  const promptAi = resolvePromptAiConfig(aiConfig);
  if (!promptAi) return "";
  const instruction = buildPromptInstruction({ site, contract, coverArt, recentAlts });
  try {
    const result = await callTextModel({ aiConfig: promptAi, prompt: instruction });
    return result?.prompt?.trim() || "";
  } catch (error) {
    return "";
  }
}

async function buildAltText({ aiConfig, site, contract, coverArt }) {
  if (!aiConfig) return "";
  const fastAi = { ...aiConfig, textModel: aiConfig.textModel || "openai/gpt-4o-mini" };
  const instruction = buildAltTextInstruction({ site, contract, coverArt });
  try {
    const result = await callTextModel({ aiConfig: fastAi, prompt: instruction });
    const alt = String(result?.alt || "").replace(/[\r\n]+/g, " ").trim();
    return alt.slice(0, 160);
  } catch (error) {
    return "";
  }
}

function normalizeImageUrl(candidate) {
  if (!candidate) return "";
  if (typeof candidate === "string") return candidate;
  return (
    candidate.url ||
    candidate.image_url?.url ||
    candidate.imageUrl?.url ||
    candidate.image_url ||
    candidate.imageUrl ||
    candidate.b64_json ||
    ""
  );
}

function extractImageUrl(payload) {
  const message = payload?.choices?.[0]?.message;
  const images = message?.images || [];
  for (const image of images) {
    const url = normalizeImageUrl(image);
    if (url) return url;
  }
  const content = Array.isArray(message?.content) ? message.content : [];
  for (const item of content) {
    const url = normalizeImageUrl(item);
    if (url) return url;
  }
  const data = payload?.data || [];
  for (const item of data) {
    const url = normalizeImageUrl(item);
    if (url) return url;
  }
  return "";
}

async function writeImageFromUrl(imageUrl, targetPath) {
  if (!imageUrl) throw new Error("URL de imagem vazia.");
  if (imageUrl.startsWith("data:")) {
    const [, data] = imageUrl.split(",", 2);
    if (!data) throw new Error("Data URL invalida.");
    fs.writeFileSync(targetPath, Buffer.from(data, "base64"));
    return;
  }
  const response = await fetch(imageUrl);
  if (!response.ok) throw new Error(`Falha ao baixar imagem (${response.status}).`);
  fs.writeFileSync(targetPath, Buffer.from(await response.arrayBuffer()));
}

function normalizeToCover(targetPath) {
  const script = `
import sys
from PIL import Image, ImageOps
img = Image.open(sys.argv[1]).convert("RGB")
out = ImageOps.fit(img, (${TARGET_WIDTH}, ${TARGET_HEIGHT}), method=Image.Resampling.LANCZOS, centering=(0.5, 0.5))
out.save(sys.argv[2], format="JPEG", quality=92, subsampling=0)
`;
  execFileSync("python3", ["-c", script, targetPath, targetPath], { stdio: "pipe" });
}

function tesseractAvailable() {
  try {
    execSync("which tesseract", { stdio: "pipe" });
    return true;
  } catch (error) {
    return false;
  }
}

function detectTextLeakage(targetPath) {
  if (!tesseractAvailable()) {
    return { skipped: true, reason: "tesseract not installed" };
  }
  try {
    const output = execFileSync(
      "tesseract",
      [targetPath, "-", "--psm", "6"],
      { stdio: ["ignore", "pipe", "pipe"], timeout: 15000 }
    ).toString();
    const alphanumericCount = (output.match(/[A-Za-z0-9]/g) || []).length;
    const sample = output
      .split(/\s+/)
      .filter((word) => word.replace(/[^A-Za-z0-9]/g, "").length >= 3)
      .slice(0, 5)
      .join(" ");
    return {
      skipped: false,
      leaked: alphanumericCount >= TEXT_LEAKAGE_ALPHANUMERIC_THRESHOLD,
      alphanumericCount,
      sample
    };
  } catch (error) {
    return { skipped: true, reason: `tesseract error: ${error.message || error}` };
  }
}

async function callOpenRouterImage({ apiKey, model, prompt, targetPath, appUrl, appName, aspectRatio }) {
  const headers = {
    Authorization: `Bearer ${apiKey}`,
    "Content-Type": "application/json",
    "HTTP-Referer": appUrl,
    "X-Title": appName
  };

  const bodies = [
    {
      model,
      stream: false,
      modalities: ["image"],
      image_config: { aspect_ratio: aspectRatio, output_format: "jpeg" },
      messages: [{ role: "user", content: prompt }]
    },
    {
      model,
      stream: false,
      modalities: ["image", "text"],
      image_config: { aspect_ratio: aspectRatio, output_format: "jpeg" },
      messages: [{ role: "user", content: prompt }]
    }
  ];

  let lastError = null;
  for (const body of bodies) {
    const response = await fetch("https://openrouter.ai/api/v1/chat/completions", {
      method: "POST",
      headers,
      body: JSON.stringify(body)
    });
    if (!response.ok) {
      lastError = new Error(`OpenRouter ${model} (${response.status}): ${await response.text()}`);
      continue;
    }
    const payload = await response.json();
    const imageUrl = extractImageUrl(payload);
    if (!imageUrl) {
      lastError = new Error(`OpenRouter ${model}: resposta sem imagem.`);
      continue;
    }
    await writeImageFromUrl(imageUrl, targetPath);
    return { ok: true };
  }
  throw lastError || new Error(`OpenRouter ${model}: falha desconhecida.`);
}

async function callOpenAIImage({ apiKey, model, prompt, targetPath, size }) {
  const response = await fetch("https://api.openai.com/v1/images/generations", {
    method: "POST",
    headers: {
      Authorization: `Bearer ${apiKey}`,
      "Content-Type": "application/json"
    },
    body: JSON.stringify({ model, prompt, size, n: 1 })
  });
  if (!response.ok) {
    throw new Error(`OpenAI Images ${model} (${response.status}): ${await response.text()}`);
  }
  const payload = await response.json();
  const item = payload?.data?.[0];
  const imageUrl = item?.url || (item?.b64_json ? `data:image/png;base64,${item.b64_json}` : "");
  if (!imageUrl) throw new Error(`OpenAI Images ${model}: resposta sem imagem.`);
  await writeImageFromUrl(imageUrl, targetPath);
  return { ok: true };
}

async function callImageModel({ aiConfig, modelId, prompt, targetPath }) {
  if ((modelId.startsWith("openai/") || modelId === "gpt-image-1" || modelId === "dall-e-3") && aiConfig.provider === "openai") {
    const cleaned = modelId.replace(/^openai\//, "");
    const size = cleaned === "dall-e-3" ? "1792x1024" : "1536x1024";
    return callOpenAIImage({ apiKey: aiConfig.apiKey, model: cleaned, prompt, targetPath, size });
  }
  return callOpenRouterImage({
    apiKey: aiConfig.apiKey,
    model: modelId,
    prompt,
    targetPath,
    appUrl: aiConfig.appUrl,
    appName: aiConfig.appName,
    aspectRatio: "16:9"
  });
}

function resolveModelChain(site, env = process.env) {
  const config = site.coverModel || {};
  const envPrimary = env.BLOG_BOT_COVER_MODEL_PRIMARY;
  const envFallback = env.BLOG_BOT_COVER_MODEL_FALLBACK;
  const primary = envPrimary || config.primary || "gemini-3-pro";
  const fallback = (envFallback ? envFallback.split(",") : config.fallback) || ["gpt-5-image", "gemini-flash"];
  return [primary, ...(Array.isArray(fallback) ? fallback : [fallback])]
    .filter(Boolean)
    .map(resolveModelId);
}

async function generateCover({ aiConfig, site, contract, targetPath }) {
  if (!aiConfig) {
    throw new Error("aiConfig ausente para gerar capa.");
  }

  const profile = siteProfile(site.id) || {};
  const coverArt = profile.coverArt || defaultCoverArt();
  const recentAlts = loadRecentCoverAlts(site.id, { excludeSlug: contract.slug, limit: 5 });

  let visualPrompt = await buildVisualPrompt({ aiConfig, site, contract, coverArt, recentAlts });
  if (!visualPrompt) {
    visualPrompt = [
      `Editorial cinematic 16:9 cover for an article titled "${contract.content?.headline || contract.title || ""}".`,
      `Theme: ${contract.theme}. Angle: ${contract.angle || "n/a"}.`,
      `Brand visual direction: palette ${coverArt.paletteDescription}; lighting ${coverArt.lighting}; mood ${coverArt.mood}.`,
      `Visual motifs allowed: ${joinList(coverArt.visualMotifs, "single strong metaphor")}.`,
      `Avoid: ${joinList(coverArt.avoid, "text, logos, UI mockups, faces in close-up")}.`,
      "Single strong metaphor, dramatic lighting, polished magazine quality, no text, no watermark."
    ].join(" ");
  }

  const chain = resolveModelChain(site);
  const attempts = [];
  let usedModel = null;

  for (const modelId of chain) {
    try {
      await callImageModel({ aiConfig, modelId, prompt: visualPrompt, targetPath });
      try {
        normalizeToCover(targetPath);
      } catch (cropError) {
        attempts.push({ model: modelId, warning: `crop-fail: ${cropError.message || cropError}` });
      }
      usedModel = modelId;
      break;
    } catch (error) {
      attempts.push({ model: modelId, error: error.message || String(error) });
    }
  }

  if (!usedModel) {
    const summary = attempts.map((a) => `${a.model}: ${a.error || a.warning}`).join(" | ");
    throw new Error(`Cover agent falhou em todos os modelos. ${summary}`);
  }

  const altText = await buildAltText({ aiConfig, site, contract, coverArt });
  const leakage = detectTextLeakage(targetPath);

  return {
    source: usedModel,
    prompt: visualPrompt,
    altText,
    attempts,
    leakage
  };
}

module.exports = {
  generateCover,
  resolveModelChain,
  MODEL_CATALOG,
  DEFAULT_PROMPT_MODEL
};
