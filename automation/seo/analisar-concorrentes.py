#!/usr/bin/env python3
"""
[Modulo SEO — analise comparativa de concorrentes]
@Author: Andre Gomes ( @acidcode )
@since 2026-09-20

POR QUE ISSO EXISTE: "o concorrente aparece pra caramba" e uma observacao certa
que costuma virar conclusao errada — a gente olha o site, acha bonito e copia o
visual, quando o que faz ele aparecer e outra coisa. Este script mede o que
realmente move posicao: volume de conteudo na pagina, profundidade de estrutura,
dado estruturado e tamanho do site indexavel.

NAO mede autoridade de dominio (backlinks), que exige ferramenta paga. Entao a
leitura e "o que da pra igualar sem comprar link", que e justamente o que esta
sob nosso controle.

Uso:
  python3 analisar-concorrentes.py
  python3 analisar-concorrentes.py --url=https://site.com/pagina
"""
import re
import sys
import urllib.request
from html import unescape

TIMEOUT = 25
UA = ("Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 "
      "(KHTML, like Gecko) Chrome/131.0 Safari/537.36")

# a pagina que cada um usa pra disputar o termo de multinivel
ALVOS = [
    ("Eloss", "https://plataformaeloss.com.br/multinivel/"),
    ("Aliadus", "https://aliadus.com.br/"),
    ("MMNWeb", "https://www.mmnweb.com.br/"),
    ("Maxnivel", "https://www.maxnivel.com.br/"),
    ("WiDigital", "https://www.widigital.com.br/"),
    ("NOS — sistema-mmn", "https://sistemavendadireta.com.br/sistema-mmn/"),
    ("NOS — LP", "https://sistemavendadireta.com.br/sistema-venda-direta/"),
]


def baixar(url):
    req = urllib.request.Request(url, headers={"User-Agent": UA})
    with urllib.request.urlopen(req, timeout=TIMEOUT) as r:
        return r.read().decode("utf-8", "replace")


def texto_visivel(html):
    corpo = re.sub(r"<(script|style|noscript|svg)[^>]*>.*?</\1>", " ", html, flags=re.S | re.I)
    return re.sub(r"\s+", " ", unescape(re.sub(r"<[^>]+>", " ", corpo))).strip()


def urls_no_sitemap(base):
    """Tamanho do site indexavel. Sitemap pode ser indice de sitemaps."""
    try:
        raiz = re.match(r"https?://[^/]+", base).group(0)
        sm = baixar(f"{raiz}/sitemap.xml")
        if "<sitemapindex" in sm:
            total = 0
            for loc in re.findall(r"<loc>([^<]+)</loc>", sm)[:12]:
                try:
                    total += len(re.findall(r"<loc>", baixar(loc.strip())))
                except Exception:
                    pass
            return total
        return len(re.findall(r"<loc>", sm))
    except Exception:
        return None


def analisar(nome, url):
    try:
        html = baixar(url)
    except Exception as e:
        return {"nome": nome, "erro": str(e)[:60]}
    txt = texto_visivel(html)
    tipos = sorted(set(re.findall(r'"@type"\s*:\s*"([^"]+)"', html)))
    m = re.search(r"<title[^>]*>(.*?)</title>", html, re.S | re.I)
    d = re.search(r'name="description" content="([^"]*)"', html, re.I)
    h1 = re.findall(r"<h1[^>]*>(.*?)</h1>", html, re.S | re.I)
    return {
        "nome": nome, "url": url,
        "title": unescape(m.group(1)).strip() if m else "",
        "desc": unescape(d.group(1)).strip() if d else "",
        "h1": re.sub(r"\s+", " ", re.sub(r"<[^>]+>", "", h1[0])).strip() if h1 else "",
        "palavras": len(txt.split()),
        "h2": len(re.findall(r"<h2[\s>]", html, re.I)),
        "h3": len(re.findall(r"<h3[\s>]", html, re.I)),
        "faq": "FAQPage" in tipos,
        "tipos": tipos,
        "sitemap": urls_no_sitemap(url),
        "peso_kb": round(len(html.encode()) / 1024),
        # prova social e o que separa pagina de vendas de pagina que converte
        "depoimento": bool(re.search(r"depoiment|testemunh|case[s]? de|cliente[s]? que", txt, re.I)),
        "preco": bool(re.search(r"R\$\s?\d", txt)),
        "demo": bool(re.search(r"demonstra|agendar|teste gr[aá]tis|trial", txt, re.I)),
    }


def main():
    u = next((a.split("=", 1)[1] for a in sys.argv if a.startswith("--url=")), None)
    alvos = [("alvo", u)] if u else ALVOS

    res = [analisar(n, x) for n, x in alvos]
    print(f"\n{'concorrente':22} {'palavras':>8} {'H2':>3} {'H3':>3} {'FAQ':>4} "
          f"{'sitemap':>8} {'preco':>6} {'demo':>5}")
    print("─" * 78)
    for r in res:
        if r.get("erro"):
            print(f"  {r['nome']:20} ERRO: {r['erro']}")
            continue
        print(f"{r['nome']:22} {r['palavras']:8} {r['h2']:3} {r['h3']:3} "
              f"{'sim' if r['faq'] else '—':>4} {str(r['sitemap'] or '?'):>8} "
              f"{'sim' if r['preco'] else '—':>6} {'sim' if r['demo'] else '—':>5}")

    print("\n\n── detalhe ──")
    for r in res:
        if r.get("erro"):
            continue
        print(f"\n{r['nome']}  ({r['url']})")
        print(f"  title ({len(r['title'])}): {r['title'][:88]}")
        print(f"  h1: {r['h1'][:88]}")
        if r["tipos"]:
            print(f"  dado estruturado: {', '.join(r['tipos'][:8])}")
        else:
            print("  dado estruturado: NENHUM")


if __name__ == "__main__":
    main()
