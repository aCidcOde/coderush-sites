/**
 * [Modulo Blog Bot — topo dos cards de post]
 * @Author: Andre Gomes ( @acidcode )
 * @since 2026-11-26
 *
 * POR QUE ISSO EXISTE: em 26/11/2026 o topo do post deixou de ser a capa (titulo
 * desenhado na imagem + titulo em HTML = a mesma frase duas vezes). Os cards do
 * blog, da home e do "Leia tambem" continuavam com a capa em cima e o titulo
 * embaixo — o usuario: "aqui os posts ainda tem a imagem de fundo, deixa tudo
 * igual". O card agora repete o topo do post em miniatura: fundo gerado
 * (imagens/posts/fundos/<slug>.webp) com data e titulo por cima, uma vez so.
 *
 * Sem fundo em disco (post de outro site, por exemplo), cai no degrade escuro —
 * mesmo desenho, sem imagem. A capa continua existindo pro og:image.
 *
 * O titulo fica em <hN><a class="hover:underline"> e a data num <span>, nao em
 * <p>: parseCard() (publisher.js) le o titulo por esse padrao e o resumo pelo
 * PRIMEIRO <p> do card — data em <p> virava resumo.
 */
const fs = require("node:fs");
const path = require("node:path");

function esc(value) {
  return String(value || "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function dataBr(iso) {
  const m = String(iso || "").match(/^(\d{4})-(\d{2})-(\d{2})/);
  return m ? `${m[3]}/${m[2]}/${m[1]}` : "";
}

/** Caminho do fundo do post, ou null se o arquivo nao existe no site. */
function fundoDoPost(slug, prefix, siteRootAbs) {
  if (!slug) return null;
  const rel = `imagens/posts/fundos/${slug}.webp`;
  if (siteRootAbs && !fs.existsSync(path.resolve(siteRootAbs, rel))) return null;
  if (!siteRootAbs) return null;
  return `${prefix}${rel}`;
}

/**
 * @param {object} o
 * @param {string} o.href       link do post
 * @param {string} o.title      titulo (sem escapar)
 * @param {string} [o.dateIso]  AAAA-MM-DD
 * @param {string} [o.rotulo]   texto acima do titulo no lugar da data (ex.: nome do site)
 * @param {string|null} [o.fundoSrc]
 * @param {string} [o.headingTag] h2 | h3
 * @param {string} [o.linkAttrs] atributos extras do <a> (target, rel)
 */
function topoDoCard({ href, title, dateIso, rotulo, fundoSrc, headingTag = "h2", linkAttrs = "" }) {
  const fundo = fundoSrc ? `#040c1e url('${fundoSrc}') center/cover no-repeat` : "linear-gradient(135deg,#0b3a8a 0%,#040c1e 100%)";
  const sobre = rotulo ? esc(rotulo) : dataBr(dateIso);
  return [
    `  <div class="relative" style="background:${fundo}">`,
    '    <div class="relative p-4" style="min-height:11rem;display:flex;flex-direction:column;justify-content:flex-end;background:linear-gradient(90deg,rgba(4,12,30,.55) 0%,rgba(4,12,30,.15) 100%)">',
    '      <span class="block h-1 w-10 rounded-full bg-white" aria-hidden="true"></span>',
    sobre ? `      <span class="mt-3 block text-[11px] font-semibold uppercase tracking-[0.22em] text-white/80">${sobre}</span>` : "",
    `      <${headingTag} class="mt-1 text-lg font-semibold leading-snug text-white"><a href="${href}"${linkAttrs} class="hover:underline">${esc(title)}</a></${headingTag}>`,
    "    </div>",
    "  </div>"
  ]
    .filter(Boolean)
    .join("\n");
}

module.exports = { topoDoCard, fundoDoPost, dataBr };
