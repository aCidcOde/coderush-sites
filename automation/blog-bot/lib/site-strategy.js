const crypto = require("node:crypto");

const SITE_PROFILES = {
  coderush: {
    persona:
      "fundadores e CTOs de empresas medias que precisam tirar iniciativas de tecnologia do papel sem improviso",
    personaShort: "lideres de tecnologia",
    coverArt: {
      paletteHex: ["#020b1a", "#60a5fa", "#a78bfa"],
      paletteDescription: "azul-marinho profundo (#020b1a) com brilho azul eletrico (#60a5fa) e violeta (#a78bfa)",
      lighting: "lighting volumetrico cinematografico, rim light suave, profundidade com leve atmosferic fog",
      mood: "premium, hub de tecnologia editorial, sensacao de capa de revista Wired",
      visualMotifs: [
        "arquitetura digital abstrata em isometria limpa",
        "sistemas conectados por linhas de luz",
        "blocos modulares como metafora de sistemas integrados",
        "maos distantes ou silhuetas em segundo plano se houver figura humana"
      ],
      avoid: ["tela de UI cheia", "icones tecnologicos genericos", "engrenagens", "stock photography corporativa"]
    },
    offering:
      "consultoria e execucao em arquitetura, software sob medida, IA aplicada e governanca tecnica",
    differentiators: [
      "execucao pragmatica em iniciativas criticas",
      "aderencia a regras de negocio existentes",
      "integracao com sistemas legados sem big-bang"
    ],
    voice: "consultivo, direto, com clareza de trade-offs e sem hype",
    bannedWords: ["revolucionario", "magico", "disruptivo", "no-code milagroso"],
    angleBias: ["arquitetura", "governanca", "decisao tecnica", "casos de empresa media"],
    cta: { label: "Fale com a CodeRush", path: "#contato" },
    keywords: {
      primary: [
        "software sob medida",
        "consultoria de tecnologia",
        "arquitetura de software"
      ],
      secondary: [
        "transformacao digital",
        "IA para empresas",
        "governanca de tecnologia",
        "modernizacao de sistemas",
        "integracao com sistemas legados"
      ],
      longTail: [
        "consultoria de software sob medida para empresas",
        "como integrar IA em sistemas legados",
        "arquitetura de software para operacao critica"
      ]
    },
    themes: [
      "governanca de tecnologia para crescimento",
      "arquitetura de software para operacoes criticas",
      "decisao entre comprar e construir software",
      "produtividade de times com automacao inteligente"
    ]
  },
  codafacil: {
    persona:
      "lideres de produto e engenharia que precisam acelerar entrega sem perder qualidade",
    personaShort: "times de produto e engenharia",
    coverArt: {
      paletteHex: ["#04110d", "#0b4db6", "#8b5cf6"],
      paletteDescription: "verde-petroleo escuro (#04110d) com azul royal (#0b4db6) e violeta tech (#8b5cf6)",
      lighting: "iluminacao suave com brilho frio, contraste limpo, sem grande dramaticidade",
      mood: "precisao, clareza tecnica, atmosfera de engenharia aplicada",
      visualMotifs: [
        "abstracao de codigo ou arquitetura de sistema, sem texto legivel",
        "elementos de produto digital reduzidos a forma e cor",
        "grafos, fluxos e camadas como metafora de software construido em iteracao",
        "tipografia ZERO na imagem"
      ],
      avoid: ["screenshots de IDE", "letras visiveis", "logos", "stock de equipe corporativa"]
    },
    offering:
      "fabrica de software sob medida com IA aplicada no ciclo de desenvolvimento",
    differentiators: [
      "pareamento com IA durante o ciclo de dev",
      "testes automatizados desde o primeiro sprint",
      "entrega curta com escopo claro"
    ],
    voice: "tecnico, pragmatico, com referencias de codigo, processo e DX",
    bannedWords: ["plug and play", "low code milagroso", "100% automatico"],
    angleBias: ["engenharia", "DX", "tooling de IA para devs", "qualidade de codigo"],
    cta: { label: "Fale com a Codafacil.dev", path: "#contato" },
    keywords: {
      primary: [
        "desenvolvimento de software sob medida",
        "fabrica de software",
        "desenvolvimento Laravel"
      ],
      secondary: [
        "desenvolvimento PHP",
        "engenharia de software com IA",
        "sistemas web sob medida",
        "GitHub Copilot",
        "testes automatizados"
      ],
      longTail: [
        "fabrica de software com IA aplicada",
        "desenvolvimento Laravel sob medida para empresas",
        "como usar IA no desenvolvimento de sistemas"
      ]
    },
    themes: [
      "software sob medida com IA aplicada",
      "integracoes e automacao para operacoes criticas",
      "tooling de IA para engenharia",
      "entrega de produtos digitais com governanca tecnica"
    ]
  },
  fluxointeligenteia: {
    persona:
      "gerentes de operacao e atendimento que precisam reduzir custo e retrabalho com automacao",
    personaShort: "times de operacao",
    coverArt: {
      paletteHex: ["#04110d", "#34d399", "#38bdf8"],
      paletteDescription: "fundo dark esmeralda (#04110d) com verde esmeralda (#34d399) e ciano (#38bdf8)",
      lighting: "particulas de dados em movimento, linhas conectivas com brilho leve, atmosfera de sala-de-controle moderna",
      mood: "energia operacional, agentes inteligentes em fluxo, sensacao de processo em tempo real",
      visualMotifs: [
        "fluxos conectados por linhas de luz como metafora de agentes orquestrados",
        "nos de processamento abstratos representando agentes ou ferramentas",
        "particulas de dados pequenas e rapidas",
        "camadas de profundidade sugerindo pipeline"
      ],
      avoid: ["robos antropomorficos", "cerebros eletronicos cliche", "engrenagens", "telas de chatbot"]
    },
    offering:
      "agentes inteligentes (LangChain, LangGraph), automacao com n8n, integracao de sistemas e orquestracao de fluxos",
    differentiators: [
      "agentes inteligentes em producao com LangChain e LangGraph",
      "n8n auto-hospedado integrado a sistemas reais",
      "fluxos com fallback humano, observabilidade e auditoria",
      "engenharia de prompt e tool-use bem definida por agente"
    ],
    voice: "operacional, focado em processo, custo e SLA",
    bannedWords: ["IA generica", "chatbot besta", "tudo automatizado", "agente magico"],
    angleBias: [
      "agentes inteligentes",
      "langchain",
      "langgraph",
      "n8n",
      "RPA",
      "atendimento",
      "custo operacional",
      "tool-use"
    ],
    cta: { label: "Fale com a FluxoInteligente IA", path: "#contato" },
    keywords: {
      primary: [
        "automacao com IA",
        "agentes inteligentes",
        "automacao de processos"
      ],
      secondary: [
        "LangChain",
        "n8n",
        "agentes de IA",
        "LLM para automacao",
        "RPA com IA",
        "automacao de atendimento"
      ],
      longTail: [
        "como criar agentes de IA com LangChain",
        "automacao de atendimento com IA",
        "n8n auto-hospedado para empresas",
        "agentes inteligentes em producao"
      ]
    },
    themes: [
      "agentes inteligentes com LangChain em producao",
      "orquestracao de agentes com LangGraph",
      "automacao de processos com LLMs e n8n",
      "n8n e integracoes em escala com agentes",
      "reducao de custo operacional com IA aplicada"
    ]
  },
  sistemavendadireta: {
    persona:
      "diretores comerciais e de tecnologia em empresas de venda direta e marketing multinivel",
    personaShort: "diretores comerciais e de TI",
    coverArt: {
      paletteHex: ["#12356b", "#004aad", "#ffffff"],
      paletteDescription: "azul profundo (#12356b) com azul corporativo (#004aad) e acento branco (#ffffff)",
      lighting: "iluminacao estavel e simetrica, sem drama excessivo, leve gradient suave de fundo",
      mood: "institucional, confiavel, governanca, maturidade comercial",
      visualMotifs: [
        "abstracoes geometricas limpas representando rede e estrutura comercial",
        "camadas em formato de hierarquia ou rede multinivel",
        "metaforas visuais de fluxo comercial sem mostrar pessoas",
        "cores solidas corporativas, baixo ruido visual"
      ],
      avoid: ["fotos de equipes em reuniao", "graficos com numeros", "moedas", "icones de dolar", "qualquer coisa que sugira esquema piramide"]
    },
    offering:
      "software, integracoes, IA aplicada e processos para operacao comercial multinivel",
    differentiators: [
      "dominio profundo de venda direta e MMN",
      "integracoes de pagamento, logistica e comissionamento",
      "IA aplicada a campo e suporte ao distribuidor"
    ],
    voice: "executivo, focado em resultado comercial e previsibilidade",
    bannedWords: ["esquema de piramide", "ganhe dinheiro facil", "renda passiva garantida"],
    angleBias: ["MMN", "vendas diretas", "campo", "comissionamento", "CRM"],
    cta: { label: "Solicite um orcamento", path: "#contato" },
    keywords: {
      primary: [
        "sistema de venda direta",
        "software para MMN",
        "marketing multinivel"
      ],
      secondary: [
        "sistema de marketing multinivel",
        "plano de carreira MMN",
        "comissionamento MMN",
        "CRM para venda direta",
        "software para vendas diretas"
      ],
      longTail: [
        "software para empresa de venda direta no Brasil",
        "sistema de comissionamento para MMN",
        "plataforma MMN sob medida"
      ]
    },
    themes: [
      "tecnologia para vendas diretas em escala",
      "crm e automacao para marketing multinivel",
      "integracoes de pagamento e logistica para MMN",
      "governanca comercial com dados e ia"
    ]
  }
};

function siteProfile(siteId) {
  return SITE_PROFILES[siteId] || null;
}

function pickSiteTheme(siteId, seed) {
  const profile = siteProfile(siteId);
  const candidates = profile?.themes || ["tecnologia aplicada a negocios"];
  const hash = crypto.createHash("sha1").update(`${siteId}:${seed}`).digest("hex");
  const index = parseInt(hash.slice(0, 8), 16) % candidates.length;
  return candidates[index];
}

function pickAngle(siteId, seed) {
  const profile = siteProfile(siteId);
  const candidates = profile?.angleBias || ["aplicacao pratica"];
  const hash = crypto.createHash("sha1").update(`${siteId}:angle:${seed}`).digest("hex");
  const index = parseInt(hash.slice(0, 8), 16) % candidates.length;
  return candidates[index];
}

function sitePromptStyle(site) {
  const profile = siteProfile(site.id) || {};
  return {
    tone: profile.voice || "consultivo, direto, focado em negocio",
    audience: profile.persona || "gestores e donos de operacao",
    offering: profile.offering || "",
    differentiators: profile.differentiators || [],
    bannedWords: profile.bannedWords || [],
    cta: profile.cta || { label: "Fale com o time", path: "#contato" },
    keywords: profile.keywords || { primary: [], secondary: [], longTail: [] },
    constraints: [
      "Texto em pt-BR",
      "Evitar promessas absolutas e hype",
      "Trazer aplicacao pratica com exemplo concreto",
      "Usar dados ou referencias quando fornecidos",
      "Manter qualidade educacional sobre o tema acima de tudo",
      "CTAs devem ser sutis e naturais, nunca interruptivas ou agressivas",
      "Maximo 2 mencoes inline ao servico/empresa, sempre como transicao natural",
      "Fechar a ultima secao com uma linha leve apontando para contato"
    ],
    postType: site.postType
  };
}

module.exports = {
  pickSiteTheme,
  pickAngle,
  siteProfile,
  sitePromptStyle
};
