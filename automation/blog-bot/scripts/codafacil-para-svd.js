#!/usr/bin/env node
/**
 * [Blog-bot — posts de integracao da Codafacil viram 301 pro post do SVD]
 * @since 2026-11-26
 *
 * POR QUE: o usuario pediu pra "copiar os posts do codafacil pra ca e por 301 do
 * blog deles" — "vamos focar tudo no sistema venda direta". Os 29 posts da
 * Codafacil sao de desenvolvimento de software (Laravel, Copilot, testes, divida
 * tecnica), cerca de 10 deles variacoes de "IA acelera o desenvolvimento sob
 * medida". Trazer tudo desfaria a limpeza do blog do SVD (49 -> 20 em 23/11) e
 * diluiria o assunto do dominio. Decisao aprovada: so os que conversam com quem
 * compra sistema — os 3 de integracao — e, em vez de copiar o texto generico,
 * um post novo no SVD que cobre o assunto pelo angulo de venda direta:
 *   /2026/11/26/api-sistema-venda-direta-integracao/
 *
 * Os 3 da Codafacil saem (pasta, capa, card do blog/home, sitemap) e respondem
 * 301 pro post novo — mapa CODAFACIL-POSTS-301 no docker/nginx/default.conf. Os
 * outros 26 ficam onde estao (o bot da Codafacil esta pausado desde 05/08).
 *
 * Depois de rodar: docker exec coderush-nginx sh -c 'nginx -t && nginx -s reload'
 * Uso: node scripts/codafacil-para-svd.js [--dry-run]
 */
const fs = require("node:fs");
const path = require("node:path");

const ROOT = path.resolve(__dirname, "..", "..", "..");
const CFG = JSON.parse(fs.readFileSync(path.resolve(__dirname, "..", "config", "sites.json"), "utf8"));
const SITE = CFG.sites.find((s) => s.id === "codafacil");
const DRY = process.argv.includes("--dry-run");
const NGINX = path.resolve(ROOT, "docker/nginx/default.conf");
const DESTINO = "https://sistemavendadireta.com.br/2026/11/26/api-sistema-venda-direta-integracao/";
const SAEM = [
  "2026/04/30/integracoes-automacao-operacoes-criticas-ia",
  "2026/06/30/acelerar-operacoes-criticas-ia-integracoes-eficientes",
  "2026/07/12/design-api-integracoes-corporativas-considerar"
];

const siteRoot = path.resolve(ROOT, SITE.siteRoot);
const esc = (v) => String(v).replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
const escrever = (p, c) => { if (!DRY) fs.writeFileSync(p, c); };
function apagar(p) {
  if (!fs.existsSync(p)) return false;
  if (!DRY) fs.rmSync(p, { recursive: true, force: true });
  return true;
}
function trocarNoArquivo(arquivo, re, por) {
  if (!fs.existsSync(arquivo)) return false;
  const antes = fs.readFileSync(arquivo, "utf8");
  const depois = antes.replace(re, por);
  if (depois === antes) return false;
  escrever(arquivo, depois);
  return true;
}
function arquivosDoSite(dir, out = []) {
  for (const e of fs.readdirSync(dir, { withFileTypes: true })) {
    const p = path.join(dir, e.name);
    if (e.isDirectory()) {
      if (["vendor", "node_modules", "storage", "imagens", "css", "js", "fonts"].includes(e.name)) continue;
      arquivosDoSite(p, out);
    } else if (/\.(php|html)$/.test(e.name)) out.push(p);
  }
  return out;
}

if (!DRY && !fs.existsSync(path.resolve(ROOT, "sistemavendadireta/2026/11/26/api-sistema-venda-direta-integracao/index.php"))) {
  throw new Error("post de destino nao existe no SVD");
}

for (const de of SAEM) {
  const slug = de.split("/").pop();
  const feito = [];
  if (apagar(path.join(siteRoot, de))) feito.push("pasta");
  for (const ext of ["jpg", "webp", "png"]) {
    if (apagar(path.join(siteRoot, SITE.assets.postsDir, `${slug}.${ext}`))) feito.push("capa." + ext);
  }
  const card = new RegExp("\\s*<article[^>]*data-blog-slug=\"" + esc(slug) + "\"[\\s\\S]*?</article>", "g");
  if (trocarNoArquivo(path.join(siteRoot, SITE.blogIndexPath), card, "")) feito.push("card-blog");
  if (trocarNoArquivo(path.join(siteRoot, SITE.homePath), card, "")) feito.push("card-home");
  const url = new RegExp("\\s*<url>\\s*<loc>[^<]*" + esc(`/${de}/`) + "[^<]*</loc>[\\s\\S]*?</url>", "g");
  if (trocarNoArquivo(path.join(siteRoot, SITE.seo.sitemapPath), url, "")) feito.push("sitemap");
  console.log(`${DRY ? "[dry] " : ""}${de} | ${feito.join(", ") || "nada"}`);
}

// link no corpo de outro post da Codafacil: o href inteiro vira o endereco do SVD
// (o caminho e relativo — trocar so o trecho deixaria "../../../../https://...")
let trocas = 0;
for (const arq of arquivosDoSite(siteRoot)) {
  let feito = false;
  for (const de of SAEM) {
    const re = new RegExp(`href="[^"]*${esc(de)}/?(?:index\\.php)?"`, "g");
    feito = trocarNoArquivo(arq, re, `href="${DESTINO}"`) || feito;
  }
  if (feito) trocas++;
}
console.log(`links internos reescritos em ${trocas} arquivo(s)`);

const INI = "# >>> CODAFACIL-POSTS-301 (gerado por automation/blog-bot/scripts/codafacil-para-svd.js — nao editar a mao)";
const FIM = "# <<< CODAFACIL-POSTS-301";
const bloco = [INI,
  "# 26/11/2026: os 3 posts de integracao da Codafacil foram consolidados num post do",
  "# SVD (foco do ano: vender o Sistema Venda Direta). O if fica no server da Codafacil.",
  "map $uri $codafacil_post_301 {", "    default \"\";",
  ...SAEM.map((de) => `    "~^/${esc(de)}(?:/(?:index\\.php)?)?$" ${DESTINO};`),
  "}", FIM].join("\n");
let conf = fs.readFileSync(NGINX, "utf8");
if (conf.includes(INI)) {
  conf = conf.replace(new RegExp(esc(INI) + "[\\s\\S]*?" + esc(FIM)), bloco);
} else {
  const ancora = "map $http_user_agent $crawler_indesejado {";
  if (!conf.includes(ancora)) throw new Error("ancora do map nao encontrada no default.conf");
  conf = conf.replace(ancora, bloco + "\n\n" + ancora);
}
const ancoraServer = "    server_name codafacil.dev www.codafacil.dev;\n";
if (!conf.includes("if ($codafacil_post_301)")) {
  if (!conf.includes(ancoraServer)) throw new Error("server da Codafacil nao encontrado no default.conf");
  conf = conf.replace(ancoraServer, ancoraServer +
    "\n    # posts de integracao consolidados no SVD (mapa CODAFACIL-POSTS-301 no topo do arquivo)\n" +
    "    if ($codafacil_post_301) { return 301 $codafacil_post_301; }\n");
}
escrever(NGINX, conf);
console.log(`nginx: ${SAEM.length} redirecionamentos${DRY ? " (dry-run, nada gravado)" : ""}`);
