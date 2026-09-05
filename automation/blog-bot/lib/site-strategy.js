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
      "produtividade de times com automacao inteligente",
      "modernizacao gradual de sistemas legados",
      "escolha de stack para iniciativa critica",
      "ROI e priorizacao de iniciativas de tecnologia",
      "IA aplicada em processos de retaguarda",
      "gestao de risco em substituicao de sistema",
      "como dimensionar equipe de tecnologia em empresa media"
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
      "entrega de produtos digitais com governanca tecnica",
      "testes automatizados no ciclo de entrega",
      "arquitetura Laravel para sistemas sob medida",
      "pareamento humano-IA no desenvolvimento",
      "escala de banco e cache em produtos sob medida",
      "gestao de debito tecnico em produto digital",
      "design de API para integracoes corporativas"
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
      "base de conhecimento corporativo para agentes",
      "observabilidade e logs de agentes em producao",
      "evolucao continua de agentes corporativos",
      "agentes corporativos no atendimento ao cliente",
      "agentes corporativos integrados a ERP e CRM",
      "ROI de agentes corporativos em operacao"
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
    excludeSourcesFromContent: true,
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
      "compliance corporativo apoiado em certidoes cartorarias",
      "investigacao patrimonial em contencioso",
      "certidoes em processos sucessorios e inventarios",
      "KYC e onboarding corporativo com certidoes",
      "riscos ocultos em aquisicao de imovel",
      "LGPD e tratamento de dados em diligencia",
      "diligencia em operacoes de credito e financiamento"
    ]
  },
  bfrintelligence: {
    persona:
      "diretores, gerentes de operacao, atendimento e TI que querem tirar IA do piloto e operar agentes com resultado mensuravel",
    personaShort: "lideres de operacao e TI",
    coverArt: {
      paletteHex: ["#1E88E5", "#283593", "#0D1B3E", "#F0F6FF"],
      paletteDescription:
        "azul eletrico (#1E88E5) em gradiente 130 graus para indigo profundo (#283593), sobre navy noturno (#0D1B3E) ou nevoa clara (#F0F6FF)",
      lighting: "luz limpa de produto SaaS, gradientes suaves azul-indigo, brilho pontual frio, sem neon saturado",
      mood: "inteligencia aplicada, precisao de engenharia, produto de IA serio e mensuravel",
      visualMotifs: [
        "malhas de nos conectados representando agentes orquestrados",
        "blocos geometricos limpos em camadas, estetica de arquitetura de software",
        "feixes de dados em gradiente azul-indigo atravessando a composicao",
        "grades e linhas finas lembrando blueprint tecnico"
      ],
      avoid: [
        "robos antropomorficos",
        "cerebros eletronicos cliche",
        "engrenagens",
        "telas de chatbot",
        "verde esmeralda ou dark emerald (paleta da marca anterior)"
      ]
    },
    offering:
      "agentes de IA para operacao real: contexto, integracao com sistemas, seguranca, governanca e ROI mensuravel",
    differentiators: [
      "agentes com contexto e integracao a sistemas internos, nao chatbot generico",
      "engenharia de agentes: RAG, tools, permissoes e auditoria",
      "foco em sair do piloto e medir resultado em operacao"
    ],
    voice: "editorial tecnico-executivo, claro e direto, focado em operacao real e resultado mensuravel",
    casualVoice: "engenheiro-consultor que ja colocou agente em producao — fala de integracao, guardrail e ROI, sem mistica de IA",
    bannedWords: mergeBanned(["IA magica", "chatbot besta", "tudo automatizado", "autonomia total", "revolucionario"]),
    angleBias: [
      "engenharia de agentes",
      "automacao e integracoes",
      "governanca",
      "ROI",
      "RAG",
      "seguranca"
    ],
    cta: { label: "Criar meu agente", path: "/#contato" },
    keywords: {
      primary: [
        "agentes de IA para empresas",
        "IA na operacao",
        "engenharia de agentes"
      ],
      secondary: [
        "RAG",
        "automacao com IA",
        "governanca de IA",
        "integracao de agentes",
        "ROI de IA",
        "agentes corporativos"
      ],
      longTail: [
        "como tirar agente de IA do piloto",
        "agentes de IA integrados a sistemas internos",
        "medir ROI de agentes de IA na operacao"
      ]
    },
    themes: [
      "engenharia de agentes: do piloto a producao",
      "agentes com RAG e contexto do negocio",
      "automacao e integracoes com sistemas internos",
      "governanca, permissoes e auditoria de agentes",
      "ROI mensuravel de IA na operacao",
      "seguranca e guardrails em agentes corporativos",
      "observabilidade e logs de agentes em producao",
      "orquestracao de multiplos agentes",
      "agentes de IA no atendimento com canais integrados",
      "base de conhecimento privada para agentes"
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
    angleBias: ["MMN", "vendas diretas", "internacionalizacao", "comissionamento", "fiscal", "integracao ERP"],
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
    // TEMAS DERIVADOS DO SEARCH CONSOLE (trocados em 05/09/2026).
    //
    // A lista anterior era escrita a mao em linguagem de consultoria — "governanca
    // comercial com dados e IA", "previsibilidade de receita". Em 30 dias os 9 posts
    // indexados renderam 25 impressoes e 2 cliques: 1% do dominio. Nenhum tema batia
    // com o que o publico busca.
    //
    // Estes vieram das buscas reais em que aparecemos MAL posicionados (>10) e que
    // NAO contem palavra de produto — busca com "sistema/software/plataforma" tem
    // intencao de compra e merece pagina de produto (/sistema-mmn/), nao post.
    // Volume somado: ~605 impressoes/trimestre hoje desperdicadas.
    //
    // Ao revisar, rodar: python3 automation/ads/search-console.py --dias=90
    themes: [
      "quais empresas de marketing multinivel existem no Brasil",
      "o que e marketing multinivel e como funciona na pratica",
      "diferenca entre venda direta e marketing multinivel",
      "como saber se uma empresa de MMN e confiavel",
      "como cadastrar consultores e distribuidores em uma operacao de MMN",
      "marketing de rede no Brasil: panorama e regulamentacao",
      "plano de carreira em marketing multinivel: como estruturar",
      "MMN B2C: vender para consumidor final por meio de consultores",
      "como montar uma operacao de marketing multinivel do zero",
      "quanto custa manter uma operacao de venda direta",
      "recrutamento e retencao de consultores em marketing de rede",
      "comissao por cargo versus plano de pontos em venda direta",
      "integracao de sistema de venda direta com ERP (TOTVS, Bling)",
      "internacionalizacao de operacao de venda direta (idioma, moeda e documento fiscal)"
    ]
  }
};

function siteProfile(siteId) {
  return SITE_PROFILES[siteId] || null;
}

function pickFromList(candidates, fallback, hashKey, excludeSet) {
  const all = candidates && candidates.length ? candidates : [fallback];
  const filtered = excludeSet && excludeSet.size
    ? all.filter((item) => !excludeSet.has(String(item).trim().toLowerCase()))
    : all;
  const pool = filtered.length ? filtered : all;
  const hash = crypto.createHash("sha1").update(hashKey).digest("hex");
  const index = parseInt(hash.slice(0, 8), 16) % pool.length;
  return pool[index];
}

function pickSiteTheme(siteId, seed, { exclude = [] } = {}) {
  const profile = siteProfile(siteId);
  const excludeSet = new Set(exclude.map((value) => String(value).trim().toLowerCase()));
  return pickFromList(
    profile?.themes,
    "tecnologia aplicada a negocios",
    `${siteId}:${seed}`,
    excludeSet
  );
}

function pickAngle(siteId, seed, { exclude = [] } = {}) {
  const profile = siteProfile(siteId);
  const excludeSet = new Set(exclude.map((value) => String(value).trim().toLowerCase()));
  return pickFromList(
    profile?.angleBias,
    "aplicacao pratica",
    `${siteId}:angle:${seed}`,
    excludeSet
  );
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
    excludeSourcesFromContent: !!profile.excludeSourcesFromContent,
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
