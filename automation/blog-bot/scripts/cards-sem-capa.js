#!/usr/bin/env node
/**
 * [Modulo Blog Bot — cards do SVD no desenho sem capa]
 * @Author: Andre Gomes ( @acidcode )
 * @since 2026-11-26
 *
 * Re-renderiza os cards que ja estao no ar no desenho de lib/card-topo.js:
 * indice do blog, home e "Leia tambem"/outros sites dentro de cada post do SVD.
 * Os posts novos ja saem assim pelo publisher. Idempotente.
 *
 * Uso: node scripts/cards-sem-capa.js
 */
const fs = require("node:fs");
const path = require("node:path");
const pub = require("../lib/publisher");
const { refreshAllPosts } = require("../lib/related-refresher");

const ROOT = path.resolve(__dirname, "..", "..", "..");
const config = JSON.parse(fs.readFileSync(path.resolve(__dirname, "..", "config", "sites.json"), "utf8"));
const site = (config.sites || config).find((s) => s.id === "sistemavendadireta");
const siteRootAbs = path.resolve(ROOT, site.siteRoot);

const indice = pub.updateCardsInFile(path.resolve(siteRootAbs, site.blogIndexPath), pub.INDEX_MARKERS, [], "blog-index", Infinity, siteRootAbs);
console.log("blog:", indice.updated ? "atualizado" : "igual", indice.cards.length, "cards");
const home = pub.updateCardsInFile(path.resolve(siteRootAbs, site.homePath || "index.php"), pub.HOME_MARKERS, [], "home", 3, siteRootAbs);
console.log("home:", home.updated ? "atualizado" : "igual", home.cards.length, "cards");
const r = refreshAllPosts(ROOT, config, { soSites: ["sistemavendadireta"] });
console.log("posts:", JSON.stringify({ tocados: r.touched, iguais: r.unchanged, erros: r.errors }));
