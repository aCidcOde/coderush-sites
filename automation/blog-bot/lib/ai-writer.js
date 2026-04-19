const fs = require("node:fs");

function resolveAiConfig(env = process.env) {
  const openRouterKey = String(env.API_OPENROUTER || env.OPENROUTER_API_KEY || "").trim();
  if (openRouterKey) {
    return {
      provider: "openrouter",
      apiKey: openRouterKey,
      textModel: env.BLOG_BOT_OPENROUTER_MODEL || env.BLOG_BOT_TEXT_MODEL || "openai/gpt-4o-mini",
      imageModel: env.BLOG_BOT_OPENROUTER_IMAGE_MODEL || "google/gemini-2.5-flash-image",
      appUrl: env.BLOG_BOT_APP_URL || "https://coderush.com.br",
      appName: env.BLOG_BOT_APP_NAME || "CodeRush Blog Bot"
    };
  }

  const openAiKey = String(env.OPENAI_API_KEY || "").trim();
  if (openAiKey) {
    return {
      provider: "openai",
      apiKey: openAiKey,
      textModel: env.BLOG_BOT_OPENAI_MODEL || "gpt-4o-mini",
      imageModel: null,
      appUrl: env.BLOG_BOT_APP_URL || "https://coderush.com.br",
      appName: env.BLOG_BOT_APP_NAME || "CodeRush Blog Bot"
    };
  }

  return null;
}

async function generateWithOpenAI({ apiKey, model, prompt }) {
  const response = await fetch("https://api.openai.com/v1/chat/completions", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${apiKey}`
    },
    body: JSON.stringify({
      model,
      temperature: 0.7,
      messages: [
        {
          role: "system",
          content:
            "Voce escreve artigos tecnicos em pt-BR para blogs corporativos. Responda em JSON valido."
        },
        {
          role: "user",
          content: prompt
        }
      ],
      response_format: { type: "json_object" }
    })
  });

  if (!response.ok) {
    const text = await response.text();
    throw new Error(`OpenAI API error (${response.status}): ${text}`);
  }

  const data = await response.json();
  const content = data.choices?.[0]?.message?.content;
  if (!content) {
    throw new Error("Resposta da OpenAI sem conteudo.");
  }

  return JSON.parse(content);
}

async function generateWithOpenRouter({ apiKey, model, prompt, appUrl, appName }) {
  const response = await fetch("https://openrouter.ai/api/v1/chat/completions", {
    method: "POST",
    headers: {
      Authorization: `Bearer ${apiKey}`,
      "Content-Type": "application/json",
      "HTTP-Referer": appUrl,
      "X-Title": appName
    },
    body: JSON.stringify({
      model,
      temperature: 0.7,
      response_format: { type: "json_object" },
      messages: [
        {
          role: "system",
          content:
            "Voce escreve artigos tecnicos em pt-BR para blogs corporativos. Responda em JSON valido."
        },
        {
          role: "user",
          content: prompt
        }
      ]
    })
  });

  if (!response.ok) {
    const text = await response.text();
    throw new Error(`OpenRouter API error (${response.status}): ${text}`);
  }

  const data = await response.json();
  const content = data.choices?.[0]?.message?.content;
  if (!content) {
    throw new Error("Resposta da OpenRouter sem conteudo.");
  }

  return JSON.parse(content);
}

function normalizeImageUrl(candidate) {
  if (!candidate) {
    return "";
  }

  if (typeof candidate === "string") {
    return candidate;
  }

  return (
    candidate.url ||
    candidate.image_url?.url ||
    candidate.imageUrl?.url ||
    candidate.image_url ||
    candidate.imageUrl ||
    ""
  );
}

function extractGeneratedImageUrl(payload) {
  const message = payload?.choices?.[0]?.message;
  const images = message?.images || [];
  for (const image of images) {
    const url = normalizeImageUrl(image);
    if (url) {
      return url;
    }
  }

  const content = Array.isArray(message?.content) ? message.content : [];
  for (const item of content) {
    const url = normalizeImageUrl(item);
    if (url) {
      return url;
    }
  }

  return "";
}

async function writeGeneratedImage(imageUrl, targetPath) {
  if (!imageUrl) {
    throw new Error("Imagem gerada vazia.");
  }

  if (imageUrl.startsWith("data:")) {
    const [, data] = imageUrl.split(",", 2);
    if (!data) {
      throw new Error("Data URL invalida para imagem.");
    }

    fs.writeFileSync(targetPath, Buffer.from(data, "base64"));
    return;
  }

  const response = await fetch(imageUrl);
  if (!response.ok) {
    throw new Error(`Falha ao baixar imagem gerada (${response.status}).`);
  }

  const buffer = Buffer.from(await response.arrayBuffer());
  fs.writeFileSync(targetPath, buffer);
}

async function generateCoverWithOpenRouter({
  apiKey,
  model,
  prompt,
  targetPath,
  appUrl,
  appName,
  aspectRatio = "16:9"
}) {
  const baseHeaders = {
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
      image_config: {
        aspect_ratio: aspectRatio,
        output_format: "jpeg"
      },
      messages: [{ role: "user", content: prompt }]
    },
    {
      model,
      stream: false,
      modalities: ["image", "text"],
      image_config: {
        aspect_ratio: aspectRatio,
        output_format: "jpeg"
      },
      messages: [{ role: "user", content: prompt }]
    }
  ];

  let lastError = null;
  for (const body of bodies) {
    const response = await fetch("https://openrouter.ai/api/v1/chat/completions", {
      method: "POST",
      headers: baseHeaders,
      body: JSON.stringify(body)
    });

    if (!response.ok) {
      lastError = new Error(`OpenRouter image API error (${response.status}): ${await response.text()}`);
      continue;
    }

    const payload = await response.json();
    const imageUrl = extractGeneratedImageUrl(payload);
    if (!imageUrl) {
      lastError = new Error("Resposta da OpenRouter sem imagem gerada.");
      continue;
    }

    await writeGeneratedImage(imageUrl, targetPath);
    return targetPath;
  }

  throw lastError || new Error("Falha desconhecida ao gerar capa com OpenRouter.");
}

function generateFallbackContent({ siteName, theme, focus }) {
  return {
    headline: `${siteName}: como usar ${focus.toUpperCase()} de forma pratica`,
    summary: `Guia objetivo sobre ${theme} com foco em resultado operacional.`,
    sections: [
      {
        title: "Contexto de mercado",
        body: `Empresas estao acelerando a adocao de ${focus} para reduzir retrabalho, ganhar previsibilidade e melhorar a experiencia do cliente.`
      },
      {
        title: "Aplicacao tecnica",
        body: `A abordagem recomendada e iniciar com um fluxo critico, medir impacto e evoluir com governanca de dados e seguranca desde o inicio.`
      },
      {
        title: "Software sob medida com IA",
        body: "Solucoes personalizadas com IA permitem integrar sistemas legados, padronizar operacoes e manter controle sobre regras de negocio."
      },
      {
        title: "Plano de execucao",
        body: "Comece com piloto de 30 dias, defina KPIs, valide com usuarios reais e escale somente o que trouxer ganho comprovado."
      }
    ]
  };
}

module.exports = {
  resolveAiConfig,
  generateWithOpenAI,
  generateWithOpenRouter,
  generateCoverWithOpenRouter,
  generateFallbackContent
};
