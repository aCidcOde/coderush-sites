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

const SYSTEM_ROLE =
  "Voce escreve para um blog consultivo de tecnologia em pt-BR. " +
  "Tom: consultivo, direto, levemente descontraido (como conversa com colega senior, sem formalidade nem hype, sem girias). " +
  "O leitor chegou via busca: a primeira resposta no answerBox precisa ser direta. " +
  "Responda APENAS com JSON valido conforme o schema pedido pelo usuario.";

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
        { role: "system", content: SYSTEM_ROLE },
        { role: "user", content: prompt }
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
        { role: "system", content: SYSTEM_ROLE },
        { role: "user", content: prompt }
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

function joinList(items, fallback = "") {
  const list = (items || []).filter(Boolean);
  if (list.length === 0) return fallback;
  if (list.length === 1) return list[0];
  if (list.length === 2) return `${list[0]} e ${list[1]}`;
  return `${list.slice(0, -1).join(", ")} e ${list[list.length - 1]}`;
}

function capitalize(value) {
  const text = String(value || "").trim();
  return text ? text[0].toUpperCase() + text.slice(1) : text;
}

function generateFallbackContent({ siteName, theme, focus, profile = {}, angle = "", research = [] }) {
  const persona = profile.persona || "gestores de operacao";
  const personaShort = profile.personaShort || persona;
  const offering = profile.offering || "tecnologia aplicada";
  const differentiators = joinList(profile.differentiators, "execucao com governanca");
  const ctaLabel = profile.cta?.label || "Fale com o time";
  const ctaPath = profile.cta?.path || "#contato";
  const angleLabel = angle || (profile.angleBias && profile.angleBias[0]) || "aplicacao pratica";
  const primaryKeyword = profile.keywords?.primary?.[0] || theme;
  const secondaryKeyword = profile.keywords?.secondary?.[0] || focus;
  const longTailKeyword = profile.keywords?.longTail?.[0] || "";

  const evidenceLine = research.length
    ? `Movimentos recentes do setor (${research
        .slice(0, 2)
        .map((item) => item.title)
        .filter(Boolean)
        .join("; ")}) reforcam a urgencia de tratar ${primaryKeyword} como capacidade.`
    : `Movimentos do setor reforcam a urgencia de tratar ${primaryKeyword} como capacidade, nao como projeto isolado.`;

  const longTailLine = longTailKeyword
    ? ` Em conversas com ${personaShort}, ${longTailKeyword} aparece como pergunta recorrente.`
    : "";

  return {
    eyebrow: "Guia pratico",
    headline: `${capitalize(primaryKeyword)}: o que ${personaShort} precisam decidir sobre ${angleLabel}`,
    summary: `Guia pratico sobre ${primaryKeyword} no contexto de ${theme}, com leitura de ${secondaryKeyword} e foco em ${differentiators}.`,
    answerBox: {
      question: `Como aplicar ${primaryKeyword} sem virar projeto sem fim?`,
      answer: `${capitalize(primaryKeyword)} entrega valor quando entra em um fluxo critico medido. O caminho curto: escolher um recorte com KPI claro, validar em 30 dias e so escalar com retorno comprovado. ${capitalize(personaShort)} usam ${differentiators} pra evitar reescrita de sistema.`
    },
    tldr: [
      `Recorte um fluxo critico antes de escalar ${primaryKeyword}.`,
      `Meca impacto em 30 dias com KPI definido.`,
      `${capitalize(differentiators)} reduz risco de reescrita.`
    ],
    sections: [
      {
        type: "prose",
        title: `Contexto: ${primaryKeyword} no dia a dia`,
        body: `${capitalize(personaShort)} ja sentem o peso de adotar ${secondaryKeyword} sem criterio. ${evidenceLine}${longTailLine} Antes de escalar, e preciso definir onde ${primaryKeyword} cria valor real e onde ainda e ruido.`
      },
      {
        type: "list",
        title: `Quando ${primaryKeyword} faz sentido na operacao`,
        items: [
          `Existe um fluxo critico onde a operacao trava recorrentemente.`,
          `Os dados de entrada estao razoavelmente estruturados.`,
          `Ha KPI mensuravel para validar em 30 dias.`,
          `O time de operacao consegue dedicar 1-2h por semana de feedback.`
        ]
      },
      {
        type: "callout",
        body: `Ja estruturamos esse recorte em projetos parecidos: ${offering}. Se faz sentido pro seu cenario, vale uma conversa de 30min sem compromisso.`,
        ctaLabel,
        ctaHref: ctaPath
      },
      {
        type: "prose",
        title: `Como avaliar ${secondaryKeyword} sem reescrever tudo`,
        body: `Tratar ${primaryKeyword} como projeto isolado costuma falhar. O caminho mais eficiente e mapear um fluxo critico, medir o impacto e replicar onde houver retorno. ${capitalize(siteName)} aplica ${differentiators} em contextos parecidos, sem eliminar a etapa interna de validacao com os times de operacao.`
      },
      {
        type: "cta-inline",
        body: `Quer ver como esse recorte se aplica ao seu cenario?`,
        ctaLabel,
        ctaHref: ctaPath
      },
      {
        type: "prose",
        title: "Trade-offs e o que evitar",
        body: `O erro mais comum e tratar ${primaryKeyword} como solucao universal. Sem recorte, sem KPI e sem fallback humano, a iniciativa vira ruido operacional. ${capitalize(angleLabel)} exige escolher o ponto certo de automacao e medir efeito antes de generalizar para outros fluxos.`
      }
    ],
    faq: [
      {
        q: `Quanto tempo leva pra ver retorno com ${primaryKeyword}?`,
        a: `Com recorte bem definido, a primeira leitura de impacto vem em 30 dias. Generalizacao para outros fluxos depende do KPI da fase 1 — sem isso, vira projeto sem fim.`
      },
      {
        q: `Precisa reescrever os sistemas atuais?`,
        a: `Nao. ${capitalize(siteName)} prioriza integracao com os sistemas existentes, mantendo as regras de negocio. Reescrita so quando a divida tecnica trava o resultado.`
      },
      {
        q: `Quem deve liderar essa frente internamente?`,
        a: `O time de operacao que sente o problema, com sponsor de tecnologia. Sem dono claro do fluxo critico, ${primaryKeyword} nao gera retorno mensuravel.`
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
