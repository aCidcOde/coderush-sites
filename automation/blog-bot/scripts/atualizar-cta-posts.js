#!/usr/bin/env node
/*
[Modulo blog-bot — atualiza o CTA dos posts ja publicados]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-17

O CTA fica gravado no HTML no momento da publicacao, entao mudar SITE_COPY em
publisher.js so vale pros posts futuros. Este script reescreve os que ja estao no
ar, sem republicar nada.

POR QUE: o CTA do SVD vendia consultoria de IA ("A SVD estrutura arquitetura,
integracao e governanca") — nao e o que a empresa vende, e nao tem relacao com as
buscas que trazem essas pessoas (sistema mmn, plataforma de venda direta). Alem
disso apontava pra ancora #contato do proprio post, um beco sem saida, em vez de
levar pra home, que tem a explicacao completa e o formulario.

Uso:
  node scripts/atualizar-cta-posts.js --site=sistemavendadireta --dry-run
  node scripts/atualizar-cta-posts.js --site=sistemavendadireta
*/

const fs = require("node:fs");
const path = require("node:path");

const ROOT = path.resolve(__dirname, "..", "..", "..");

// De -> Para, por site. Casa o texto exato gravado nos posts antigos.
const TROCAS = {
  sistemavendadireta: [
    {
      de: "Quer aplicar IA na operação comercial com previsibilidade?",
      para: "Precisa de um sistema de marketing multinível pronto para operar?"
    },
    {
      de: "A SVD estrutura arquitetura, integração e governança para levar automação ao negócio com previsibilidade.",
      para: "O Sistema Venda Direta já roda no Brasil, Paraguai e Bolívia: rede binária e unilevel, "
        + "escritório do consultor, loja virtual e financeiro integrados, parametrizados para o seu plano."
    },
    { de: "Solicite um orçamento", para: "Conhecer o sistema" },
    // #contato e ancora do proprio post: leva a lugar nenhum. UTM marcada pra
    // medir o fluxo blog -> produto no painel de leads.
    {
      de: 'href="../../../../#contato"',
      para: 'href="../../../../?utm_source=blog&amp;utm_medium=post&amp;utm_campaign=cta-artigo"'
    }
  ]
};

function arg(nome, padrao = null) {
  const hit = process.argv.slice(2).find((a) => a.startsWith(`--${nome}=`));
  return hit ? hit.split("=", 2)[1] : padrao;
}

function listarPosts(siteDir) {
  const out = [];
  const anos = path.join(ROOT, siteDir);
  for (const ano of fs.readdirSync(anos).filter((d) => /^\d{4}$/.test(d))) {
    const walk = (dir) => {
      for (const e of fs.readdirSync(dir, { withFileTypes: true })) {
        const p = path.join(dir, e.name);
        if (e.isDirectory()) walk(p);
        else if (e.name === "index.php" || e.name === "index.html") out.push(p);
      }
    };
    walk(path.join(anos, ano));
  }
  return out;
}

function main() {
  const site = arg("site", "sistemavendadireta");
  const dry = process.argv.includes("--dry-run");
  const trocas = TROCAS[site];
  if (!trocas) {
    console.error(`sem regras de troca para '${site}'`);
    process.exit(1);
  }

  const posts = listarPosts(site);
  let alterados = 0;
  let intactos = 0;

  for (const arquivo of posts) {
    const antes = fs.readFileSync(arquivo, "utf8");
    let depois = antes;
    for (const { de, para } of trocas) {
      depois = depois.split(de).join(para);
    }
    if (depois === antes) {
      intactos += 1;
      continue;
    }
    alterados += 1;
    console.log(`  [~] ${path.relative(ROOT, arquivo)}`);
    if (!dry) fs.writeFileSync(arquivo, depois);
  }

  console.log(`\n  ${alterados} post(s) ${dry ? "seriam atualizados" : "atualizados"}, `
    + `${intactos} sem o CTA antigo (de ${posts.length} no total)`);
  if (dry) console.log("  (dry-run — nada foi gravado)");
}

main();
