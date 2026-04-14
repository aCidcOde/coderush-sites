const SITE_THEMES = {
  coderush: [
    "governanca de tecnologia para crescimento",
    "arquitetura de software para operacoes criticas",
    "produtividade de times com automacao inteligente"
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

function pickSiteTheme(siteId, usedThemes) {
  const candidates = SITE_THEMES[siteId] || ["tecnologia aplicada a negocios"];
  const available = candidates.filter((theme) => !usedThemes.has(theme));
  const source = available.length > 0 ? available : candidates;
  return source[Math.floor(Math.random() * source.length)];
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
