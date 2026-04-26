const fs = require("node:fs");
const { execFileSync } = require("node:child_process");
const { generateWithOpenAI, generateWithOpenRouter } = require("./ai-writer");

const TARGET_WIDTH = 1200;
const TARGET_HEIGHT = 630;

const MODEL_CATALOG = {
  "gpt-5-image": "openai/gpt-5-image",
  "gpt-5-image-mini": "openai/gpt-5-image-mini",
  "gpt-5.4-image-2": "openai/gpt-5.4-image-2",
  "gemini-3-pro": "google/gemini-3-pro-image-preview",
  "gemini-3-flash": "google/gemini-3.1-flash-image-preview",
  "gemini-flash": "google/gemini-2.5-flash-image"
};

const SITE_STYLE = {
  coderush:
    "Visual editorial corporativo de tecnologia, paleta azul eletrico (#60a5fa) e violeta (#a78bfa) sobre fundo azul-marinho profundo (#020b1a), composicao cinematografica com profundidade, lighting volumetrico, sensacao de hub de tecnologia premium.",
  codafacil:
    "Visual de engenharia de software aplicada, abstrato com elementos de arquitetura digital, paleta azul royal (#0b4db6) e violeta (#8b5cf6) sobre fundo verde-petroleo escuro (#04110d), iluminacao suave com brilho frio, sensacao de precisao e clareza tecnica.",
  fluxointeligenteia:
    "Visual de automacao e fluxos conectados de agentes de IA, paleta verde esmeralda (#34d399) e ciano (#38bdf8) sobre fundo dark esmeralda (#04110d), particulas e linhas de dados, energia operacional moderna.",
  sistemavendadireta:
    "Visual institucional corporativo solido, paleta azul corporativo (#004aad) e branco sobre fundo azul profundo (#12356b), atmosfera confiavel e estavel, abstracoes geometricas limpas, sensacao de governanca e maturidade."
};

function resolveModelId(name) {
  if (!name) {
    return "";
  }
  if (MODEL_CATALOG[name]) {
    return MODEL_CATALOG[name];
  }
  return name;
}

function buildPromptInstruction(site, contract, styleHint) {
  return [
    "Voce e diretor de arte de uma revista de tecnologia. Sua tarefa: escrever UM unico paragrafo (4-6 frases) descrevendo uma imagem editorial 16:9 (1200x630px) que sera a capa de um post de blog corporativo.",
    "",
    "Contexto do post:",
    `- Titulo: "${contract.content.headline}"`,
    `- Resumo: "${contract.content.summary}"`,
    `- Marca: ${site.name}`,
    `- Direcao visual da marca: ${styleHint}`,
    "",
    "REGRAS ABSOLUTAS:",
    "- A imagem NUNCA pode conter texto, palavras, letras, numeros, logotipos ou simbolos legiveis.",
    "- SEM mockup de tela cheia de UI, SEM watermark.",
    "- SEM rosto humano em close (silhuetas distantes ou maos sao OK se for parte do conceito).",
    "- Uma unica metafora visual forte e clara, com area de respiro generosa.",
    "- Estilo: cinematografico, editorial, alta producao, tipo capa de revista Wired ou MIT Technology Review.",
    "",
    "ESCREVA:",
    "Inclua: assunto principal (a metafora visual concreta), composicao e enquadramento, paleta exata de cores, qualidade de luz (ex: rim light, volumetric fog), atmosfera/mood, nivel de detalhe (cinematografico, fotorrealista, ilustracao editorial), e camera/lente sugerida se relevante.",
    "",
    "Responda APENAS em JSON valido: { \"prompt\": \"<paragrafo unico>\" }"
  ].join("\n");
}

async function buildVisualPrompt({ aiConfig, site, contract }) {
  const styleHint = SITE_STYLE[site.id] || "Visual tecnologico premium, atmosfera profissional e moderna.";
  const instruction = buildPromptInstruction(site, contract, styleHint);

  if (aiConfig.provider === "openrouter") {
    const result = await generateWithOpenRouter({
      apiKey: aiConfig.apiKey,
      model: aiConfig.textModel,
      prompt: instruction,
      appUrl: aiConfig.appUrl,
      appName: aiConfig.appName
    });
    return result?.prompt || "";
  }

  if (aiConfig.provider === "openai") {
    const result = await generateWithOpenAI({
      apiKey: aiConfig.apiKey,
      model: aiConfig.textModel,
      prompt: instruction
    });
    return result?.prompt || "";
  }

  return "";
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
  if (!imageUrl) {
    throw new Error("URL de imagem vazia.");
  }

  if (imageUrl.startsWith("data:")) {
    const [, data] = imageUrl.split(",", 2);
    if (!data) throw new Error("Data URL invalida.");
    fs.writeFileSync(targetPath, Buffer.from(data, "base64"));
    return;
  }

  const response = await fetch(imageUrl);
  if (!response.ok) {
    throw new Error(`Falha ao baixar imagem (${response.status}).`);
  }
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
    body: JSON.stringify({
      model,
      prompt,
      size,
      n: 1
    })
  });

  if (!response.ok) {
    throw new Error(`OpenAI Images ${model} (${response.status}): ${await response.text()}`);
  }

  const payload = await response.json();
  const item = payload?.data?.[0];
  const imageUrl = item?.url || (item?.b64_json ? `data:image/png;base64,${item.b64_json}` : "");
  if (!imageUrl) {
    throw new Error(`OpenAI Images ${model}: resposta sem imagem.`);
  }

  await writeImageFromUrl(imageUrl, targetPath);
  return { ok: true };
}

async function callImageModel({ aiConfig, modelId, prompt, targetPath }) {
  if (modelId.startsWith("openai/") || modelId === "gpt-image-1" || modelId === "dall-e-3") {
    if (aiConfig.provider === "openai") {
      const cleaned = modelId.replace(/^openai\//, "");
      const size = cleaned === "dall-e-3" ? "1792x1024" : "1536x1024";
      return callOpenAIImage({ apiKey: aiConfig.apiKey, model: cleaned, prompt, targetPath, size });
    }
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

  const primary = envPrimary || config.primary || "flux-pro";
  const fallback = (envFallback ? envFallback.split(",") : config.fallback) || ["flux-schnell", "gemini-flash"];

  return [primary, ...(Array.isArray(fallback) ? fallback : [fallback])]
    .filter(Boolean)
    .map(resolveModelId);
}

async function generateCover({ aiConfig, site, contract, targetPath }) {
  if (!aiConfig) {
    throw new Error("aiConfig ausente para gerar capa.");
  }

  let visualPrompt = "";
  let promptError = null;
  try {
    visualPrompt = await buildVisualPrompt({ aiConfig, site, contract });
  } catch (error) {
    promptError = error.message || String(error);
  }

  if (!visualPrompt) {
    const styleHint = SITE_STYLE[site.id] || "";
    visualPrompt = [
      `Editorial cinematic 16:9 cover image for an article titled "${contract.content.headline}".`,
      `Theme: ${contract.theme}. Brand direction: ${styleHint}`,
      "Single strong visual metaphor, dramatic lighting, polished magazine quality, no text, no watermark, no UI mockups, no faces in close-up."
    ].join(" ");
  }

  const chain = resolveModelChain(site);
  const attempts = [];

  for (const modelId of chain) {
    try {
      await callImageModel({ aiConfig, modelId, prompt: visualPrompt, targetPath });
      try {
        normalizeToCover(targetPath);
      } catch (cropError) {
        attempts.push({ model: modelId, error: `crop-fail: ${cropError.message || cropError}` });
      }
      return {
        source: modelId,
        prompt: visualPrompt,
        attempts,
        promptError
      };
    } catch (error) {
      attempts.push({ model: modelId, error: error.message || String(error) });
    }
  }

  const summary = attempts.map((a) => `${a.model}: ${a.error}`).join(" | ");
  throw new Error(`Cover agent falhou em todos os modelos. ${summary}`);
}

module.exports = {
  generateCover,
  resolveModelChain,
  MODEL_CATALOG
};
