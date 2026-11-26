#!/usr/bin/env node
/**
 * [Modulo Blog Bot — publicar um contrato pronto, sem chamar IA]
 * @Author: Andre Gomes ( @acidcode )
 * @since 2026-11-26
 *
 * POR QUE ISSO EXISTE: em 26/11/2026 o usuario pediu um post sobre as notas
 * fiscais novas (IBS/CBS) com o case do Emergency ERP — conteudo que depende de
 * fato verificado no codigo e na legislacao, nao de pauta do Search Console. E o
 * saldo do OpenRouter estava zerado. O run.js so publica o que ele mesmo gera;
 * este script publica um contrato JSON ja escrito (mesmo schema do run.js) pelo
 * mesmo publisher: capa local, topo sem capa, telas do sistema, card no blog,
 * sitemap — tudo igual a um post do bot.
 *
 * Uso: node scripts/publicar-contrato.js out/<site>/<data>/<slug>.json
 */
const fs = require("node:fs");
const path = require("node:path");
const { spawnSync } = require("node:child_process");
const { publishSitePost } = require("../lib/publisher");

const ROOT = path.resolve(__dirname, "..", "..", "..");
const CONFIG = path.resolve(__dirname, "..", "config", "sites.json");

(async () => {
  const arquivo = process.argv[2];
  if (!arquivo) {
    console.error("uso: node scripts/publicar-contrato.js <contrato.json>");
    process.exit(2);
  }
  const contract = JSON.parse(fs.readFileSync(path.resolve(arquivo), "utf8"));
  const config = JSON.parse(fs.readFileSync(CONFIG, "utf8"));
  const sites = Array.isArray(config) ? config : config.sites;
  const site = sites.find((s) => s.id === contract.siteId);
  if (!site) throw new Error(`site ${contract.siteId} nao esta no sites.json`);
  if (site.target === "api") throw new Error("este script so publica em site file-based");

  const res = await publishSitePost(ROOT, site, contract, null);
  console.log("publicado:", res.postPath || res);
  for (const alvo of res.phpLintTargets || []) {
    const r = spawnSync("php", ["-l", path.resolve(ROOT, alvo)], { encoding: "utf8" });
    console.log(r.status === 0 ? "lint ok " : "LINT FALHOU ", alvo, r.status === 0 ? "" : r.stdout + r.stderr);
  }
})().catch((e) => {
  console.error(e.stack || e.message);
  process.exit(1);
});
