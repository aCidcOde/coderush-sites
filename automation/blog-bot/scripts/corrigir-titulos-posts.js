#!/usr/bin/env node
/*
[Modulo blog-bot — conserta os <title> truncados dos posts publicados]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-17

O BUG: buildSeoTitle reservava 60 caracteres no total e o sufixo
" | Sistema Venda Direta" comia 23 deles. Nos 37 restantes, toda headline era
decepada no meio da frase. O resultado aparecia assim na busca:

  "Como um CRM pode potencializar sua | Sistema Venda Direta"
  "Como a IA pode otimizar o suporte | Sistema Venda Direta"

Um post desses estava na 4a posicao com 29 impressoes e ZERO clique. Titulo
pendurado nao parece promessa incompleta — parece site quebrado.

Este script reconstroi o titulo a partir do H1, que guarda a headline inteira, e
atualiza <title>, og:title e twitter:title juntos (ficavam divergentes).

Uso:
  node scripts/corrigir-titulos-posts.js --site=sistemavendadireta --dry-run
  node scripts/corrigir-titulos-posts.js --site=sistemavendadireta
*/

const fs = require("node:fs");
const path = require("node:path");

const ROOT = path.resolve(__dirname, "..", "..", "..");
const LIMITE = 65;  // o contrato permite ate 70; 60 forcava corte no meio da frase

const MARCA = {
  sistemavendadireta: "Sistema Venda Direta",
  bfrintelligence: "BFR Intelligence"
};

// nao pode terminar o titulo: deixa a frase pendurada
const CAUDA_PROIBIDA = new Set([
  "a", "o", "as", "os", "de", "da", "do", "das", "dos", "e", "em", "na", "no",
  "nas", "nos", "para", "por", "com", "ao", "aos", "um", "uma", "uns", "umas",
  "sobre", "entre", "ja", "só", "sua", "seu", "suas", "seus", "que", "pode",
  "podem", "ser", "como", "mais", "ou", "se", "ate", "até", "sem", "no", "à"
]);

function arg(nome, padrao = null) {
  const hit = process.argv.slice(2).find((a) => a.startsWith(`--${nome}=`));
  return hit ? hit.split("=", 2)[1] : padrao;
}

function cortar(texto, max) {
  const limpo = String(texto || "").replace(/\s+/g, " ").trim();
  if (limpo.length <= max) return limpo;
  let out = "";
  for (const w of limpo.split(" ")) {
    const proximo = out ? `${out} ${w}` : w;
    if (proximo.length > max) break;
    out = proximo;
  }
  // remove caudas penduradas em cascata ("... e a" -> "...")
  let mudou = true;
  while (mudou && out) {
    mudou = false;
    const i = out.lastIndexOf(" ");
    if (i < 0) break;
    if (CAUDA_PROIBIDA.has(out.slice(i + 1).toLowerCase().replace(/[.,;:]$/, ""))) {
      out = out.slice(0, i);
      mudou = true;
    }
  }
  return out.replace(/[\s,;:—-]+$/, "");
}

function extrairH1(html) {
  const m = html.match(/<h1[^>]*>([\s\S]*?)<\/h1>/i);
  if (!m) return null;
  return m[1].replace(/<[^>]*>/g, " ").replace(/&amp;/g, "&").replace(/\s+/g, " ").trim();
}

function listarPosts(siteDir) {
  const out = [];
  const base = path.join(ROOT, siteDir);
  for (const ano of fs.readdirSync(base).filter((d) => /^\d{4}$/.test(d))) {
    const walk = (dir) => {
      for (const e of fs.readdirSync(dir, { withFileTypes: true })) {
        const p = path.join(dir, e.name);
        if (e.isDirectory()) walk(p);
        else if (e.name === "index.php" || e.name === "index.html") out.push(p);
      }
    };
    walk(path.join(base, ano));
  }
  return out;
}

function main() {
  const site = arg("site", "sistemavendadireta");
  const dry = process.argv.includes("--dry-run");
  const marca = MARCA[site];
  if (!marca) {
    console.error(`sem marca configurada para '${site}'`);
    process.exit(1);
  }

  let corrigidos = 0;
  let ok = 0;

  for (const arquivo of listarPosts(site)) {
    const antes = fs.readFileSync(arquivo, "utf8");
    const h1 = extrairH1(antes);
    if (!h1) continue;

    // o H1 as vezes ja carrega o sufixo da marca; tira antes de recompor
    const headline = h1.split(` | ${marca}`)[0].trim();
    const base = cortar(headline, LIMITE);
    const sufixo = ` | ${marca}`;
    const novo = base.length + sufixo.length <= LIMITE ? `${base}${sufixo}` : base;

    const atualM = antes.match(/<title>([\s\S]*?)<\/title>/i);
    const atual = atualM ? atualM[1].trim() : "";
    if (atual === novo) {
      ok += 1;
      continue;
    }

    const escapado = novo.replace(/&/g, "&amp;");
    let depois = antes
      .replace(/<title>[\s\S]*?<\/title>/i, `<title>${escapado}</title>`)
      .replace(/(<meta property="og:title" content=")[^"]*(")/i, `$1${escapado}$2`)
      .replace(/(<meta name="twitter:title" content=")[^"]*(")/i, `$1${escapado}$2`);

    corrigidos += 1;
    console.log(`  [~] ${path.basename(path.dirname(arquivo))}`);
    console.log(`      de:   ${atual}`);
    console.log(`      para: ${novo}`);
    if (!dry) fs.writeFileSync(arquivo, depois);
  }

  console.log(`\n  ${corrigidos} titulo(s) ${dry ? "seriam corrigidos" : "corrigidos"}, ${ok} ja estavam bons`);
  if (dry) console.log("  (dry-run — nada foi gravado)");
}

main();
