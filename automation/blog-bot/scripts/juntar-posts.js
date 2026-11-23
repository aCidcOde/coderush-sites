#!/usr/bin/env node
/**
 * [Blog-bot — juntar posts repetidos do SVD num so, com 301]
 * @since 2026-11-23
 *
 * POR QUE: ate 23/11/2026 o bot sorteava tema de lista fixa e so evitava os 8
 * ultimos, e antes de 05/09 os temas eram genericos ("CRM e automacao", "integracao
 * de pagamento e logistica"). Resultado: 49 posts com 13 grupos repetidos — 6 sobre
 * pagamento e logistica, 6 sobre CRM e automacao, 5 sobre governanca (dois com o
 * MESMO titulo). O Google divide as impressoes entre eles e nenhum sobe: o blog
 * inteiro fez 989 impressoes e 7 cliques em 28 dias, 66% num post so.
 *
 * Em cada grupo fica o post com mais impressoes no Search Console (90 dias ate
 * 20/11); empatando no zero, o mais completo/recente. Os outros:
 *   - saem do site (pasta, capa, card no blog/home, sitemap);
 *   - viram 301 pro vencedor no nginx (mapa gerado entre os marcadores
 *     SVD-POSTS-301 em docker/nginx/default.conf) — o pouco sinal que tinham passa
 *     pro vencedor e link de fora nao quebra;
 *   - links internos no corpo de outros posts sao reescritos pro vencedor.
 * O rascunho em out/ FICA (historico do que o bot gerou).
 *
 * Depois de rodar: node scripts/refresh-related.js (refaz "leia tambem") e
 * docker exec coderush-nginx sh -c 'nginx -t && nginx -s reload'.
 *
 * Uso: node scripts/juntar-posts.js [--dry-run]
 */
const fs = require("node:fs");
const path = require("node:path");

const ROOT = path.resolve(__dirname, "..", "..", "..");
const CFG = JSON.parse(fs.readFileSync(path.resolve(__dirname, "..", "config", "sites.json"), "utf8"));
const SITE = CFG.sites.find((s) => s.id === "sistemavendadireta");
const DRY = process.argv.includes("--dry-run");
const NGINX = path.resolve(ROOT, "docker/nginx/default.conf");

// vencedor: [perdedores] — caminhos AAAA/MM/DD/slug
const GRUPOS = {
  // pagamento e logistica (6 posts) — 06/24: 14 impressoes; os outros, 0 a 4
  "2026/06/24/integracoes-pagamento-logistica-impactam-mmn": [
    "2026/05/02/integrar-pagamentos-logistica-marketing-multinivel",
    "2026/05/06/integracoes-pagamento-logistica-mmn",
    "2026/05/09/integracoes-pagamento-logistica-marketing-multinivel",
    "2026/05/27/integrando-pagamentos-logistica-marketing-multinivel",
    "2026/07/30/integracoes-pagamento-logistica-marketing-multinivel"
  ],
  // CRM e automacao (6) — 04/30: 231 impressoes, 2 cliques
  "2026/04/30/crm-automacao-marketing-multinivel": [
    "2026/05/15/crm-automacao-comissionamento-mmn",
    "2026/05/18/crm-automacao-marketing-multinivel-usar",
    "2026/05/30/crm-automacao-otimizam-marketing-multinivel",
    "2026/06/18/crm-ia-pode-potencializar-mmn",
    "2026/06/27/crm-automacao-impulsionam-marketing-multinivel"
  ],
  // governanca comercial (5, dois com o mesmo titulo) — 05/03: unico com clique (3)
  "2026/05/03/crm-pode-potencializar-governanca-comercial": [
    "2026/05/12/governanca-comercial-dados-ia-podem-melhorar-resultados",
    "2026/05/24/governanca-comercial-dados-ia-marketing-multinivel",
    "2026/06/21/ia-pode-fortalecer-governanca-comercial-mmn",
    "2026/08/12/governanca-comercial-dados-ia-marketing-multinivel"
  ],
  // tecnologia para vendas diretas em escala (3) — 07/03: 26 impressoes
  "2026/07/03/tecnologia-escalar-vendas-diretas-mmn": [
    "2026/05/21/tecnologia-vendas-diretas-escala-mmn-ia",
    "2026/06/03/tecnologia-vendas-diretas-escala-considerar"
  ],
  // comissionamento x inadimplencia (2) — 07/21: 7 impressoes
  "2026/07/21/gestao-comissionamento-impacta-inadimplencia-mmn": [
    "2026/06/09/comissionamento-gerenciar-inadimplencia-eficazmente"
  ],
  // expansao internacional (4) — 08/02: 12 impressoes, posicao 7,8
  "2026/08/02/levar-operacao-venda-direta-para-outro-pais": [
    "2026/06/12/expandir-internacionalmente-marketing-multinivel",
    "2026/07/12/estrategias-expansao-internacional-marketing-multinivel",
    "2026/10/08/internacionalizacao-venda-direta-desafios-estrategias"
  ],
  // cadastro de consultores (2, ambos sem impressao) — 11/01: o geral, mais recente
  "2026/11/01/cadastrar-consultores-operacao-mmn": [
    "2026/09/05/cadastrar-consultores-distribuidores-mmn-internacional"
  ],
  // diferenca venda direta x MMN (2) — 09/22: 55 impressoes, posicao 5,4
  "2026/09/22/diferenca-venda-direta-marketing-multinivel": [
    "2026/09/05/diferencas-venda-direta-marketing-multinivel"
  ],
  // comissao por cargo x pontos (2) — 10/15: 56 impressoes, posicao 7,4
  "2026/10/15/comissao-cargo-plano-pontos-venda-direta": [
    "2026/08/06/comissao-cargo-vs-plano-pontos-venda-direta"
  ],
  // integracao com ERP (2) — 08/15 com zero em 90 dias; 11/22 cobre tambem o fiscal
  "2026/11/22/integrar-sistemas-venda-direta-erp-fiscal": [
    "2026/08/15/integrar-sistema-venda-direta-erp"
  ],
  // previsibilidade de receita (2) — 06/30: 24 impressoes
  "2026/06/30/garantir-previsibilidade-receita-marketing-multinivel": [
    "2026/08/18/garantir-previsibilidade-receita-mmn"
  ],
  // onboarding de distribuidores (3) — 09/05: 4 impressoes
  "2026/09/05/onboarding-ativacao-distribuidores-papel-integracao-erp": [
    "2026/06/06/crm-pode-acelerar-onboarding-distribuidores",
    "2026/07/06/comissionamento-eficiente-onboarding-distribuidores"
  ],
  // plano de carreira (3) — 06/15: unico com clique
  "2026/06/15/construindo-plano-carreira-marketing-multinivel": [
    "2026/07/15/crm-pode-potencializar-plano-carreira-mmn",
    "2026/10/22/estruturar-plano-carreira-marketing-multinivel"
  ]
};

const siteRoot = path.resolve(ROOT, SITE.siteRoot);
const esc = (v) => String(v).replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
const log = (...a) => console.log(...a);

function escrever(p, conteudo) { if (!DRY) fs.writeFileSync(p, conteudo); }
function apagar(p) {
  if (!fs.existsSync(p)) return false;
  if (!DRY) fs.rmSync(p, { recursive: true, force: true });
  return true;
}

function tirarCard(arquivo, slug) {
  if (!fs.existsSync(arquivo)) return false;
  const html = fs.readFileSync(arquivo, "utf8");
  const re = new RegExp("\\s*<article[^>]*data-blog-slug=\"" + esc(slug) + "\"[\\s\\S]*?</article>", "g");
  const novo = html.replace(re, "");
  if (novo === html) return false;
  escrever(arquivo, novo);
  return true;
}

function tirarDoSitemap(arquivo, trecho) {
  if (!fs.existsSync(arquivo)) return false;
  const xml = fs.readFileSync(arquivo, "utf8");
  const re = new RegExp("\\s*<url>\\s*<loc>[^<]*" + esc(trecho) + "[^<]*</loc>[\\s\\S]*?</url>", "g");
  const novo = xml.replace(re, "");
  if (novo === xml) return false;
  escrever(arquivo, novo);
  return true;
}

function arquivosDoSite(dir, out = []) {
  for (const e of fs.readdirSync(dir, { withFileTypes: true })) {
    const p = path.join(dir, e.name);
    if (e.isDirectory()) {
      if (["vendor", "node_modules", "storage", "imagens", "css", "js", "fonts"].includes(e.name)) continue;
      arquivosDoSite(p, out);
    } else if (/\.(php|html|xml)$/.test(e.name)) out.push(p);
  }
  return out;
}

const perdedores = [];
for (const [vencedor, lista] of Object.entries(GRUPOS)) {
  if (!fs.existsSync(path.join(siteRoot, vencedor, "index.php"))) throw new Error("vencedor nao existe: " + vencedor);
  for (const p of lista) perdedores.push({ de: p, para: vencedor });
}

const caminhosPerdedores = new Set(perdedores.map((x) => x.de));
const slugsQueFicam = new Set();
for (const ano of fs.readdirSync(siteRoot).filter((n) => /^\d{4}$/.test(n)))
  for (const mes of fs.readdirSync(path.join(siteRoot, ano)).filter((n) => /^\d{2}$/.test(n)))
    for (const dia of fs.readdirSync(path.join(siteRoot, ano, mes)).filter((n) => /^\d{2}$/.test(n)))
      for (const slug of fs.readdirSync(path.join(siteRoot, ano, mes, dia)))
        if (!caminhosPerdedores.has(`${ano}/${mes}/${dia}/${slug}`)) slugsQueFicam.add(slug);

// 1) remove os perdedores
for (const { de, para } of perdedores) {
  const slug = de.split("/").pop();
  const feito = [];
  if (apagar(path.join(siteRoot, de))) feito.push("pasta");
  // a capa e por slug, e ha slug repetido em datas diferentes: so apaga se nenhum
  // post que fica no site usa o mesmo nome
  if (!slugsQueFicam.has(slug)) {
    for (const ext of ["jpg", "webp", "png"]) {
      if (apagar(path.join(siteRoot, SITE.assets.postsDir, `${slug}.${ext}`))) feito.push("capa." + ext);
    }
  }
  if (tirarCard(path.join(siteRoot, SITE.blogIndexPath), slug)) feito.push("card-blog");
  if (tirarCard(path.join(siteRoot, SITE.homePath), slug)) feito.push("card-home");
  if (tirarDoSitemap(path.join(siteRoot, SITE.seo.sitemapPath), `/${de}/`)) feito.push("sitemap");
  log(`${DRY ? "[dry] " : ""}${de} -> ${para} | ${feito.join(", ") || "nada"}`);
}

// 2) links internos pros perdedores passam a apontar pro vencedor
let trocas = 0;
for (const arq of arquivosDoSite(siteRoot)) {
  let t = fs.readFileSync(arq, "utf8");
  const antes = t;
  for (const { de, para } of perdedores) {
    t = t.replace(new RegExp(esc(de) + "/", "g"), para + "/");
  }
  if (t !== antes) { trocas++; escrever(arq, t); }
}
log(`links internos reescritos em ${trocas} arquivo(s)`);

// 3) mapa de 301 no nginx, entre marcadores (regerar e idempotente)
const INI = "# >>> SVD-POSTS-301 (gerado por automation/blog-bot/scripts/juntar-posts.js — nao editar a mao)";
const FIM = "# <<< SVD-POSTS-301";
const linhas = perdedores.map(({ de, para }) =>
  `    "~^/${esc(de)}(?:/(?:index\\.php)?)?$" /${para}/;`);
const bloco = [INI,
  "# Posts repetidos do blog do SVD juntados em 23/11/2026: cada um responde 301 pro",
  "# post que ficou no grupo. O if que usa isto esta no server do sistemavendadireta.",
  "map $uri $svd_post_301 {", "    default \"\";", ...linhas, "}", FIM].join("\n");
let conf = fs.readFileSync(NGINX, "utf8");
if (conf.includes(INI)) {
  conf = conf.replace(new RegExp(esc(INI) + "[\\s\\S]*?" + esc(FIM)), bloco);
} else {
  const ancora = "map $http_user_agent $crawler_indesejado {";
  if (!conf.includes(ancora)) throw new Error("ancora do map nao encontrada no default.conf");
  conf = conf.replace(ancora, bloco + "\n\n" + ancora);
}
const usoIf = "    # posts repetidos juntados (mapa SVD-POSTS-301 no topo do arquivo)\n" +
  "    if ($svd_post_301) { return 301 https://sistemavendadireta.com.br$svd_post_301; }\n";
const ancoraServer = "    server_name sistemavendadireta.com.br www.sistemavendadireta.com.br;\n";
if (!conf.includes("if ($svd_post_301)")) {
  if (!conf.includes(ancoraServer)) throw new Error("server do SVD nao encontrado no default.conf");
  conf = conf.replace(ancoraServer, ancoraServer + "\n" + usoIf);
}
escrever(NGINX, conf);
log(`nginx: ${perdedores.length} redirecionamentos no mapa${DRY ? " (dry-run, nada gravado)" : ""}`);
