const crypto = require("node:crypto");

const SHARED_BANNED_WORDS = [
  "revolucao",
  "revolucionario",
  "revolucao",
  "revolucionaria",
  "revolução",
  "revolucionário",
  "revolucionária",
  "transforme ja",
  "transforme já",
  "transformar seu negocio",
  "transformar seu negócio",
  "nao perca",
  "não perca",
  "clique aqui",
  "ultima chance",
  "última chance",
  "garantido",
  "imperdivel",
  "imperdível"
];

function mergeBanned(specific) {
  return Array.from(new Set([...SHARED_BANNED_WORDS, ...specific]));
}

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
    casualVoice: "consultor que ja resolveu isso em outras empresas — frases curtas, evita formalidade vazia, sem girias",
    bannedWords: mergeBanned(["magico", "disruptivo", "no-code milagroso"]),
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
    casualVoice: "tech lead que ja viu o codigo virar problema — direto ao ponto, exemplos rapidos, sem academicismo",
    bannedWords: mergeBanned(["plug and play", "low code milagroso", "100% automatico"]),
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
      "diretores, gerentes de operacao, atendimento e TI que precisam colocar agentes corporativos em producao com governanca",
    personaShort: "times de operacao, atendimento e TI",
    coverArt: {
      paletteHex: ["#04110d", "#34d399", "#38bdf8"],
      paletteDescription: "fundo dark esmeralda (#04110d) com verde esmeralda (#34d399) e ciano (#38bdf8)",
      lighting: "particulas de dados em movimento, linhas conectivas com brilho leve, atmosfera de sala-de-controle moderna",
      mood: "controle operacional, agentes corporativos em fluxo, seguranca e integracao em tempo real",
      visualMotifs: [
        "fluxos conectados por linhas de luz como metafora de agentes corporativos orquestrados",
        "nos de processamento abstratos representando RAG, tools, canais e permissoes",
        "particulas de dados pequenas e rapidas",
        "camadas de profundidade sugerindo base de conhecimento, auditoria e observabilidade"
      ],
      avoid: ["robos antropomorficos", "cerebros eletronicos cliche", "engrenagens", "telas de chatbot", "autonomia total sem validacao"]
    },
    offering:
      "agentes corporativos com RAG, tools, canais integrados, permissoes, auditoria, governanca e automacao segura",
    differentiators: [
      "agentes corporativos em producao conectados a documentos e sistemas internos",
      "RAG, embeddings e bases de conhecimento privadas",
      "tools com validacao, permissoes, logs e auditoria",
      "Link como base de plataforma para agentes, onboarding, feedbacks e observabilidade"
    ],
    voice: "operacional e tecnico-comercial, focado em governanca, seguranca, ROI, permissoes e execucao controlada",
    casualVoice: "consultor de operacao que ja colocou agente corporativo em producao — pragmatico, fala de permissoes, auditoria e SLA real, sem mistica de IA",
    bannedWords: mergeBanned(["IA generica", "chatbot besta", "tudo automatizado", "agente magico", "autonomia total"]),
    angleBias: [
      "agentes corporativos",
      "RAG",
      "tools",
      "permissoes",
      "auditoria",
      "governanca",
      "n8n",
      "canais integrados",
      "Link"
    ],
    cta: { label: "Fale com a FluxoInteligente IA", path: "#contato" },
    keywords: {
      primary: [
        "agentes corporativos de IA",
        "IA corporativa com governanca",
        "automacao com IA segura"
      ],
      secondary: [
        "RAG",
        "tools",
        "auditoria de IA",
        "permissoes por usuario",
        "LangChain",
        "n8n",
        "agentes de IA corporativos",
        "LLM para automacao"
      ],
      longTail: [
        "como criar agentes corporativos com RAG",
        "agentes de IA com permissoes e auditoria",
        "IA corporativa conectada a documentos e sistemas",
        "plataforma Link para agentes corporativos"
      ]
    },
    themes: [
      "agentes corporativos com RAG e governanca",
      "tools seguras para agentes de IA",
      "permissoes e auditoria em IA corporativa",
      "canais integrados para agentes corporativos",
      "Link como base operacional para agentes inteligentes"
    ]
  },
  emergency: {
    persona:
      "advogados, controllers, compradores corporativos e analistas de compliance que precisam fechar diligencias documentais sem retrabalho",
    personaShort: "areas juridica, compliance e M&A",
    coverArt: {
      paletteHex: ["#0d1118", "#0f172a", "#d89b1a", "#f3c65a"],
      paletteDescription:
        "fundo deep-navy (#0d1118 a #0f172a) com acento dourado quente (#d89b1a, gold primary, em destaque editorial) e leve realce em gold-soft (#f3c65a). Off-white (#e2e8f0) so como toque sutil em luz/brilho",
      lighting:
        "iluminacao editorial premium estilo capa de revista de negocios, rim light dourado discreto, leve gradient diagonal do navy escuro pro navy meio, profundidade com atmospheric fog suave",
      mood:
        "credibilidade institucional + tech-forward, autoridade juridica com acento dourado de sofisticacao, sensacao de dossie executivo iluminado",
      visualMotifs: [
        "abstracoes geometricas de documentos, dossies sobrepostos ou camadas analiticas em isometria limpa",
        "linhas finas dourado-quente conectando blocos como metafora de cruzamento de fontes",
        "selos, lacres ou marcas d agua reduzidos a forma e cor (sem texto legivel)",
        "particulas douradas dispersas sugerindo dado verificado",
        "mascote IA robótico aparece em cerca de 40% das capas, sempre como ator analitico (revisando dossie, segurando tablet, observando matriz de dados), nunca como hero shot"
      ],
      characterReference: {
        name: "mascote IA",
        description:
          "humanoid robot character with chrome silver-blue body, slim athletic build, friendly youthful face, signature transparent translucent skull revealing a glowing soft-blue brain inside, fine gold trim lines and panels along chest/shoulders/arms, subtle round gold emblem on chest plate, calm confident expression",
        usageRule:
          "Only feature the robot mascot when the post angle involves a concrete analytical activity (revisao de matricula, due diligence imobiliaria, auditoria, checklist, onboarding de fornecedor). Place it at 1/3 of the frame, in mid-shot or upper-body, interacting with abstract documents/dossiers/holographic data, never centered, never staring at camera, never with text-bearing tablet. For abstract/conceptual posts (regulacao, mercado, panorama, tendencias), omit it entirely and use pure abstract metaphor."
      },
      avoid: [
        "balanca da justica",
        "martelo de juiz",
        "lupa sobre papel",
        "globo terrestre",
        "graficos de bolsa com numeros",
        "pessoas em terno apertando mao",
        "mascote centralizado fazendo hero pose",
        "mascote segurando tablet com texto visivel",
        "qualquer rosto humano fotorrealistico em close"
      ]
    },
    offering:
      "emissao de certidoes online cartorarias e forenses + apoio a due diligence imobiliaria, societaria e juridica em ambito nacional",
    differentiators: [
      "atuacao nacional desde 1994 com cobertura cartoraria e forense",
      "emissao orientada com painel de acompanhamento por solicitacao",
      "experiencia em due diligence imobiliaria, societaria e M&A",
      "suporte humano para pedidos complexos, nao so emissao automatica"
    ],
    voice: "consultivo institucional, tecnico mas acessivel, com preocupacao explicita por risco e prazo",
    casualVoice: "consultor que ja fechou diligencia em deal travado — direto, fala em prazo e risco real, sem juridiques desnecessario",
    bannedWords: mergeBanned([
      "100% seguro",
      "risco zero",
      "infalivel",
      "burocracia chata",
      "papelada"
    ]),
    angleBias: [
      "due diligence imobiliaria",
      "due diligence societaria",
      "M&A",
      "compliance",
      "regularizacao",
      "habilitacao de fornecedor",
      "investigacao patrimonial"
    ],
    cta: { label: "Fale com a Emergency", path: "#contato" },
    keywords: {
      primary: [
        "due diligence",
        "certidoes online",
        "due diligence imobiliaria"
      ],
      secondary: [
        "due diligence societaria",
        "compliance corporativo",
        "habilitacao de fornecedor",
        "regularizacao imobiliaria",
        "investigacao patrimonial",
        "certidoes cartorarias"
      ],
      longTail: [
        "como fazer due diligence imobiliaria de matricula",
        "checklist de due diligence em fusoes e aquisicoes",
        "certidoes obrigatorias em compra de imovel",
        "due diligence societaria para aquisicao de empresa",
        "como validar fornecedor pessoa juridica antes de contratar"
      ]
    },
    themes: [
      "due diligence imobiliaria com certidoes orientadas",
      "due diligence societaria em M&A no mercado brasileiro",
      "habilitacao e onboarding de fornecedor com certidoes",
      "regularizacao documental antes de transacao patrimonial",
      "compliance corporativo apoiado em certidoes cartorarias"
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
    casualVoice: "diretor comercial que ja escalou MMN — fala em numero, em processo, sem promessa de ganho facil",
    bannedWords: mergeBanned(["esquema de piramide", "ganhe dinheiro facil", "renda passiva garantida"]),
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
    casualTone: profile.casualVoice || "consultor pragmatico — direto, sem girias",
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
  sitePromptStyle,
  SHARED_BANNED_WORDS
};
