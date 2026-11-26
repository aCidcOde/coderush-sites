#!/usr/bin/env node
/**
 * [Modulo Blog Bot — telas do sistema nos posts ja publicados]
 * @Author: Andre Gomes ( @acidcode )
 * @since 2026-11-26
 *
 * Aplica lib/telas-produto.js nos posts do SVD que ja estao no ar (os novos
 * saem com as telas pelo publisher.js). Idempotente: post que ja tem
 * .tela-produto e pulado.
 *
 * Uso: node scripts/telas-nos-posts.js [--dry-run]
 */
const fs = require("node:fs");
const path = require("node:path");
const { inserirTelas, escolherTelas } = require("../lib/telas-produto");

const ROOT = path.resolve(__dirname, "../../../sistemavendadireta");
const dry = process.argv.includes("--dry-run");
let feitos = 0;
const contagem = {};
const uso = {};
for (const ano of fs.readdirSync(ROOT).filter((d) => /^20\d\d$/.test(d))) {
  for (const f of fs.globSync ? fs.globSync(`${ano}/*/*/*/index.php`, { cwd: ROOT }) : []) {
    const abs = path.join(ROOT, f);
    const slug = path.basename(path.dirname(abs));
    const antes = fs.readFileSync(abs, "utf8");
    const depois = inserirTelas(antes, { slug, relativeRoot: "../../../../", usados: contagem });
    const telas = [...depois.matchAll(/imagens\/posts\/telas\/([a-z-]+)\.webp/g)].map((m) => m[1]);
    telas.forEach((t) => (uso[t] = (uso[t] || 0) + 1));
    console.log(`${depois === antes ? "igual" : "ok   "} ${slug} -> ${telas.join(", ")}`);
    if (depois !== antes) {
      feitos++;
      if (!dry) fs.writeFileSync(abs, depois, "utf8");
    }
  }
}
console.log(`\n${feitos} posts ${dry ? "seriam alterados" : "alterados"}. Uso por tela:`, uso);
