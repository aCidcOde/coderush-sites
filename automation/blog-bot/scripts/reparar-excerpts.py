#!/usr/bin/env python3
"""
[Modulo Blog Bot — reparo dos excerpts contaminados pelo <picture>]
@Author: Andre Gomes ( @acidcode )
@since 2026-10-05

O QUE ACONTECEU: o leitor de cards usava /<p[^>]*>(...)<\\/p>/ pra achar o resumo.
Esse padrao casa com <picture> — "<p" seguido de [^>]* engole o "icture". Enquanto
o card tinha <img> puro, nao havia <picture> e o regex achava o paragrafo certo.
Quando as capas viraram WebP (commit a2a77e0, 05/09/2026) o <picture> entrou na
frente do <p> e virou o "resumo".

Como o card e reescrito a partir do que ja estava gravado, cada passagem do bot
somava mais uma copia do titulo na frente do texto. Dois meses depois o resumo de
39 dos 40 cards do indice do SVD comecava com o titulo repetido, empurrando o texto
real pra fora dos 180 caracteres do truncate.

O reparo nao adivinha nada: le a <meta name="description"> do POST de destino, que
e a fonte original e nunca foi tocada por esse caminho. Cards cujo post nao existe
mais ficam como estao — melhor resumo feio que card apagado.

Uso:
  python3 reparar-excerpts.py --dry-run
  python3 reparar-excerpts.py
"""
import html
import os
import re
import sys

RAIZ = "/data/coderush-sites"
# (site, arquivo de indice, prefixo do caminho dos posts)
INDICES = [
    ("sistemavendadireta", "sistemavendadireta/blog/index.php", "sistemavendadireta"),
    ("sistemavendadireta", "sistemavendadireta/index.php", "sistemavendadireta"),
    ("bfrintelligence", "bfrintelligence/conteudos/index.html", "bfrintelligence"),
    ("bfrintelligence", "bfrintelligence/index.html", "bfrintelligence"),
    ("coderush", "blog/index.php", "."),
    ("coderush", "index.php", "."),
    ("codafacil", "codafacil/blog/index.php", "codafacil"),
    ("fluxointeligenteia", "fluxointeligenteia/blog/index.php", "fluxointeligenteia"),
]

RE_ARTIGO = re.compile(r"<article\b[\s\S]*?</article>")
RE_PATH = re.compile(r'data-blog-path="([^"]+)"')
RE_TITULO = re.compile(r"<h[23][^>]*>\s*<a[^>]*>([\s\S]*?)</a>\s*</h[23]>", re.I)
# o mesmo (?=[\s>]) do codigo corrigido: nao pode casar <picture>
RE_P = re.compile(r'(<p(?=[\s>])[^>]*class="mt-2[^"]*"[^>]*>)([\s\S]*?)(</p>)', re.I)
RE_DESC = re.compile(r'<meta\s+name="description"\s+content="([^"]*)"', re.I)


def limpo(texto):
    return re.sub(r"\s+", " ", html.unescape(re.sub(r"<[^>]+>", " ", texto))).strip()


def descricao_do_post(prefixo, post_path):
    base = os.path.join(RAIZ, prefixo, post_path.strip("/"))
    for nome in ("index.php", "index.html"):
        caminho = os.path.join(base, nome)
        if os.path.exists(caminho):
            m = RE_DESC.search(open(caminho, encoding="utf-8", errors="replace").read())
            if m:
                return html.unescape(m.group(1)).strip()
    return None


def contaminado(excerpt, titulo):
    """Resumo que comeca repetindo o titulo — a assinatura do bug."""
    if not titulo or not excerpt:
        return False
    return excerpt.lower().startswith(titulo.lower()[: max(12, len(titulo) // 2)])


def main():
    dry = "--dry-run" in sys.argv
    total_arq = total_cards = 0

    for _site, rel, prefixo in INDICES:
        caminho = os.path.join(RAIZ, rel)
        if not os.path.exists(caminho):
            continue
        original = open(caminho, encoding="utf-8", errors="replace").read()
        novo = original
        reparados = 0

        for artigo in RE_ARTIGO.findall(original):
            mp = RE_PATH.search(artigo)
            mt = RE_TITULO.search(artigo)
            mex = RE_P.search(artigo)
            if not (mp and mt and mex):
                continue
            titulo = limpo(mt.group(1))
            excerpt = limpo(mex.group(2))
            if not contaminado(excerpt, titulo):
                continue
            desc = descricao_do_post(prefixo, mp.group(1))
            if not desc:
                continue
            artigo_novo = artigo.replace(
                mex.group(0),
                f"{mex.group(1)}{html.escape(desc, quote=False)}{mex.group(3)}",
            )
            novo = novo.replace(artigo, artigo_novo)
            reparados += 1

        if reparados:
            total_arq += 1
            total_cards += reparados
            print(f"  {reparados:3} card(s)  {rel}")
            if not dry:
                open(caminho, "w", encoding="utf-8").write(novo)

    print(f"\n{total_cards} card(s) em {total_arq} arquivo(s)"
          + (" — dry-run, nada gravado" if dry else " reparado(s)"))
    return 0


if __name__ == "__main__":
    sys.exit(main())
