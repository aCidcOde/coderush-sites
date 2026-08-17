#!/usr/bin/env node
/*
[Modulo blog-bot — faixa de promocao nos posts publicados]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-17

Injeta em cada post a chamada da campanha vigente, logo abaixo do titulo, onde
quem chegou pela busca ainda esta decidindo se fica.

O conteudo NAO e gravado no post: o que entra e um include de inc/promo.php. Isso
e o ponto todo — a promocao acaba em 31/08, e cravar o texto significaria 34
paginas com oferta vencida no dia seguinte. Com o include, a data manda: passou o
prazo, a faixa vira chamada neutra sozinha, sem tocar em post nenhum.

O utm_content leva o slug do post, entao o painel mostra QUAL artigo converteu.

Idempotente: marcadores evitam duplicar em reexecucao.

Uso:
  node scripts/injetar-promo-posts.js --site=sistemavendadireta --dry-run
  node scripts/injetar-promo-posts.js --site=sistemavendadireta
  node scripts/injetar-promo-posts.js --site=sistemavendadireta --remover
*/

const fs = require("node:fs");
const path = require("node:path");

const ROOT = path.resolve(__dirname, "..", "..", "..");
const INICIO = "<!-- PROMO-STRIP START -->";
const FIM = "<!-- PROMO-STRIP END -->";

function arg(nome, padrao = null) {
  const hit = process.argv.slice(2).find((a) => a.startsWith(`--${nome}=`));
  return hit ? hit.split("=", 2)[1] : padrao;
}

function listarPosts(siteDir) {
  const out = [];
  const base = path.join(ROOT, siteDir);
  for (const ano of fs.readdirSync(base).filter((d) => /^\d{4}$/.test(d))) {
    const walk = (dir) => {
      for (const e of fs.readdirSync(dir, { withFileTypes: true })) {
        const p = path.join(dir, e.name);
        if (e.isDirectory()) walk(p);
        else if (e.name === "index.php") out.push(p);
      }
    };
    walk(path.join(base, ano));
  }
  return out;
}

function limpar(conteudo) {
  let i = conteudo.indexOf(INICIO);
  const j = conteudo.indexOf(FIM);
  if (i === -1 || j === -1) return conteudo;
  // engole a quebra de linha que precede o marcador, senao cada reexecucao
  // deixa uma linha em branco a mais e o arquivo muda sem motivo no git
  if (i > 0 && conteudo[i - 1] === "\n") i -= 1;
  return conteudo.slice(0, i) + conteudo.slice(j + FIM.length);
}

function main() {
  const site = arg("site", "sistemavendadireta");
  const dry = process.argv.includes("--dry-run");
  const remover = process.argv.includes("--remover");
  const posts = listarPosts(site);

  let feitos = 0;
  let pulados = 0;

  for (const arquivo of posts) {
    const antes = fs.readFileSync(arquivo, "utf8");
    let depois = limpar(antes);

    if (!remover) {
      // <main> abre o conteudo; a faixa entra logo depois, acima do artigo
      const m = depois.match(/<main[^>]*>/);
      if (!m) {
        console.log(`  [!] ${path.relative(ROOT, arquivo)} — sem <main>, pulado`);
        pulados += 1;
        continue;
      }
      const slug = path.basename(path.dirname(arquivo));
      const bloco = `\n${INICIO}\n<?php require_once __DIR__ . '/../../../../inc/promo.php'; `
        + `echo promoStrip('${slug.replace(/'/g, "")}'); ?>\n${FIM}`;
      const at = m.index + m[0].length;
      depois = depois.slice(0, at) + bloco + depois.slice(at);
    }

    if (depois === antes) {
      pulados += 1;
      continue;
    }
    feitos += 1;
    console.log(`  [${remover ? "-" : "+"}] ${path.relative(ROOT, arquivo)}`);
    if (!dry) fs.writeFileSync(arquivo, depois);
  }

  console.log(`\n  ${feitos} post(s) ${remover ? "limpos" : "com a faixa"}, `
    + `${pulados} sem alteracao (de ${posts.length})`);
  if (dry) console.log("  (dry-run — nada foi gravado)");
}

main();
