const crypto = require("node:crypto");

const SITE_THEMES = {
  coderush: [
    "governanca de tecnologia para crescimento",
    "arquitetura de software para operacoes criticas",
    "produtividade de times com automacao inteligente"
  ],
  codafacil: [
    "software sob medida com IA aplicada",
    "integracoes e automacao para operacoes criticas",
    "entrega de produtos digitais com governanca tecnica"
  ],
  fluxointeligenteia: [
    "agentes de ia para atendimento e operacao",
    "automacao de processos com llms",
    "reducao de custo operacional com ia aplicada"
  ],
  sistemavendadireta: [
    "tecnologia para vendas diretas em escala",
    "crm e automacao para marketing multinivel",
    "governanca comercial com dados e ia"
  ]
};

function pickSiteTheme(siteId, seed) {
  const candidates = SITE_THEMES[siteId] || ["tecnologia aplicada a negocios"];
  const hash = crypto.createHash("sha1").update(`${siteId}:${seed}`).digest("hex");
  const index = parseInt(hash.slice(0, 8), 16) % candidates.length;
  return candidates[index];
}

function sitePromptStyle(site) {
  return {
    tone: "consultivo, direto, focado em negocio",
    audience: "gestores e donos de operacao",
    constraints: [
      "Texto em pt-BR",
      "Evitar promessas absolutas",
      "Trazer aplicacao pratica",
      "Fechar com CTA para paginas internas"
    ],
    postType: site.postType
  };
}

module.exports = {
  pickSiteTheme,
  sitePromptStyle
};
