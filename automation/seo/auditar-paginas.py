#!/usr/bin/env python3
"""
[Modulo SEO — bateria de revisao das paginas]
@Author: Andre Gomes ( @acidcode )
@since 2026-09-14

POR QUE ISSO EXISTE: o CLAUDE.md define um padrao de qualidade ("qualquer site do
hub deve passar na bateria de revisao antes de ir pro ar") mas a bateria era
manual — e por isso nao rodava. Erro caro que passou batido por semanas: os 34
posts do blog saiam com o <title> decepado no meio da frase, e a descoberta veio
de olhar um post por acaso, nao de processo.

O QUE ESTE SCRIPT CHECA, e por que cada item entrou:

  title decepado — "Como um CRM pode potencializar sua | Sistema Venda Direta"
    ficou em 4o lugar com ZERO clique em 29 impressoes. Terminar em preposicao ou
    artigo e sintoma de corte automatico, nao de escrita.
  title/description fora do limite — o Google reescreve e voce perde o controle
    da promessa que traz o clique.
  termo alvo no title/H1/1o paragrafo — o post do SVD sobre governanca nao
    continha "multinivel" em lugar nenhum e mirava justamente quem busca isso.
  rastreamento de lead — a /sistema-mmn/ recebeu trafego pago sem zap-lead nem
    evento. Clique pago em pagina que nao mede e dinheiro que vira nada.
  atribuicao lida da URL — ler so do sessionStorage falha em WebView; foi o que
    zerou o gclid de uma venda de R$ 3.500.
  canonical, JSON-LD, gtag, peso — higiene basica que so aparece quando quebra.

Uso:
  python3 auditar-paginas.py                    # paginas de anuncio + principais
  python3 auditar-paginas.py --url=https://...  # uma so
  python3 auditar-paginas.py --blog             # inclui os posts
"""
import json
import re
import sys
import urllib.request
from html import unescape

TIMEOUT = 20
LIMITE_TITLE = 70
LIMITE_DESC = 160
PESO_ALERTA_KB = 900

BASE = "https://sistemavendadireta.com.br"
# cada pagina com o termo que ela deveria capturar; None = sem alvo definido
PAGINAS = [
    (f"{BASE}/", "sistema venda direta"),
    (f"{BASE}/oferta/", "sistema venda direta"),
    (f"{BASE}/sistema-mmn/", "sistema mmn"),
    (f"{BASE}/oferta/afiliados/", "plataforma de afiliados"),
    (f"{BASE}/oferta/parceiros/", "programa de parceiros"),
    (f"{BASE}/oferta/cosmeticos/", "sistema para revenda de cosmeticos"),
    (f"{BASE}/oferta/suplementos/", "sistema para distribuidora de suplementos"),
    (f"{BASE}/cases/", None),
    (f"{BASE}/simulador/", "simulador de plano de marketing multinivel"),
    (f"{BASE}/inteligencia-artificial/", None),
    (f"{BASE}/blog/", None),
]

# terminar em preposicao/artigo denuncia corte automatico do title
CAUDA_SUSPEITA = {
    "a", "o", "as", "os", "de", "da", "do", "das", "dos", "em", "no", "na",
    "nos", "nas", "para", "pra", "por", "com", "sem", "sua", "seu", "suas",
    "seus", "que", "e", "ou", "um", "uma", "ao", "aos", "à", "às",
}


def baixar(url):
    req = urllib.request.Request(url, headers={
        "User-Agent": "Mozilla/5.0 (compatible; AuditoriaSVD/1.0)"})
    with urllib.request.urlopen(req, timeout=TIMEOUT) as r:
        return r.status, r.read().decode("utf-8", "replace"), dict(r.headers)


def texto_visivel(html):
    corpo = re.sub(r"<(script|style|noscript)[^>]*>.*?</\1>", " ", html, flags=re.S | re.I)
    return re.sub(r"\s+", " ", unescape(re.sub(r"<[^>]+>", " ", corpo))).strip()


def normalizar(s):
    """Compara sem acento e sem caixa: 'multinível' casa com 'multinivel'."""
    import unicodedata
    s = unicodedata.normalize("NFD", s.lower())
    return "".join(c for c in s if unicodedata.category(c) != "Mn")


def auditar(url, alvo):
    achados = []   # (gravidade, mensagem)  gravidade: ERRO | AVISO
    try:
        status, html, headers = baixar(url)
    except Exception as e:
        return [("ERRO", f"nao carregou: {e}")], {}

    if status != 200:
        achados.append(("ERRO", f"HTTP {status}"))

    peso = len(html.encode()) / 1024
    info = {"peso_kb": round(peso, 1), "status": status}
    if peso > PESO_ALERTA_KB:
        achados.append(("AVISO", f"HTML de {peso:.0f} KB (acima de {PESO_ALERTA_KB})"))

    # ── title ──
    m = re.search(r"<title[^>]*>(.*?)</title>", html, re.S | re.I)
    title = unescape(m.group(1)).strip() if m else ""
    info["title"] = title
    if not title:
        achados.append(("ERRO", "sem <title>"))
    else:
        if len(title) > LIMITE_TITLE:
            achados.append(("AVISO", f"title com {len(title)} chars (limite {LIMITE_TITLE}): {title}"))
        # a parte antes do sufixo de marca e a frase que precisa fazer sentido
        frase = re.split(r"\s[|–—-]\s", title)[0].strip()
        ultima = re.sub(r"[^\wÀ-ÿ]", "", frase.split()[-1]).lower() if frase.split() else ""
        if ultima in CAUDA_SUSPEITA:
            achados.append(("ERRO", f"title parece decepado, termina em '{ultima}': {title}"))

    # ── description ──
    m = re.search(r'<meta[^>]+name=["\']description["\'][^>]+content=["\'](.*?)["\']', html, re.S | re.I)
    desc = unescape(m.group(1)).strip() if m else ""
    info["description"] = desc
    if not desc:
        achados.append(("ERRO", "sem meta description"))
    elif len(desc) > LIMITE_DESC:
        achados.append(("AVISO", f"description com {len(desc)} chars (limite {LIMITE_DESC})"))

    # ── canonical ──
    # Pagina noindex e LP de anuncio: canonical ali e higiene, nao SEO. Nao vale
    # gritar ERRO e afogar o que importa.
    noindex = bool(re.search(r'name=["\']robots["\'][^>]*content=["\'][^"\']*noindex', html, re.I))
    info["indexavel"] = "nao (noindex)" if noindex else "sim"
    m = re.search(r'<link[^>]+rel=["\']canonical["\'][^>]+href=["\'](.*?)["\']', html, re.I)
    if not m:
        achados.append(("AVISO" if noindex else "ERRO", "sem canonical"))
    else:
        info["canonical"] = m.group(1)
        if not m.group(1).startswith("http"):
            achados.append(("AVISO", f"canonical relativo: {m.group(1)}"))

    # ── H1 ──
    h1s = re.findall(r"<h1[^>]*>(.*?)</h1>", html, re.S | re.I)
    h1 = unescape(re.sub(r"<[^>]+>", " ", h1s[0])).strip() if h1s else ""
    info["h1"] = h1
    if not h1s:
        achados.append(("ERRO", "sem H1"))
    elif len(h1s) > 1:
        achados.append(("AVISO", f"{len(h1s)} H1 na pagina"))

    # ── termo alvo ──
    if alvo:
        corpo = texto_visivel(html)
        alvo_n = normalizar(alvo)
        primeiro = " ".join(corpo.split()[:120])
        onde = []
        if alvo_n in normalizar(title):
            onde.append("title")
        if alvo_n in normalizar(h1):
            onde.append("H1")
        if alvo_n in normalizar(primeiro):
            onde.append("inicio")
        info["alvo"] = f"{alvo} -> {', '.join(onde) or 'EM LUGAR NENHUM'}"
        if not onde:
            achados.append(("ERRO", f"termo alvo '{alvo}' nao aparece em title, H1 nem no inicio"))
        elif "title" not in onde:
            achados.append(("AVISO", f"termo alvo '{alvo}' fora do title (esta em: {', '.join(onde)})"))

    # ── JSON-LD ──
    blocos = re.findall(r'<script[^>]+type=["\']application/ld\+json["\'][^>]*>(.*?)</script>',
                        html, re.S | re.I)
    tipos = []
    for b in blocos:
        try:
            d = json.loads(b.strip())
            # @graph e a forma canonica de declarar varias entidades num bloco so;
            # ler apenas o @type do topo devolvia "?" pra JSON-LD perfeitamente valido
            itens = d if isinstance(d, list) else [d]
            achatado = []
            for item in itens:
                if isinstance(item, dict) and "@graph" in item:
                    achatado.extend(x for x in item["@graph"] if isinstance(x, dict))
                elif isinstance(item, dict):
                    achatado.append(item)
            for item in achatado:
                t = item.get("@type", "sem @type")
                tipos.append(", ".join(t) if isinstance(t, list) else t)
        except json.JSONDecodeError as e:
            achados.append(("ERRO", f"JSON-LD invalido: {e}"))
    info["jsonld"] = ", ".join(str(t) for t in tipos) or "nenhum"
    if not blocos:
        achados.append(("AVISO", "sem JSON-LD"))

    # ── analytics ──
    if not re.search(r"G-[A-Z0-9]{8,}", html):
        achados.append(("ERRO", "sem gtag/GA4"))

    # ── rastreamento de lead ──
    tem_zap_link = "wa.me" in html
    info["whatsapp"] = "sim" if tem_zap_link else "nao"
    if tem_zap_link:
        faltando = [k for k, t in (("zap-lead.php", "zap-lead.php"),
                                   ("evento whatsapp_click", "whatsapp_click"),
                                   ("sendBeacon", "sendBeacon")) if t not in html]
        if faltando:
            achados.append(("ERRO", f"link de WhatsApp sem rastreamento: falta {', '.join(faltando)}"))
        # A licao do WebView: ler so do sessionStorage falha quando o link abre
        # dentro de app. Basta UMA das duas redes existir — ler a URL no JS, ou
        # mandar page_url pro servidor extrair (o que zap-lead.php faz).
        if "sendBeacon" in html and "URLSearchParams" not in html and "page_url" not in html:
            achados.append(("ERRO", "beacon sem atribuicao da URL nem page_url (falha em WebView)"))

    # ── links internos ──
    # Contar so o que comeca com "/" ja me fez reportar que a home nao linkava
    # cases nem oferta — linkava, por caminho relativo. Link interno e tudo que
    # NAO e externo, nao so o que comeca com barra.
    hrefs = re.findall(r'href=["\']([^"\']+)["\']', html)
    externos = ("http://", "https://", "#", "mailto:", "tel:", "javascript:")
    ativos = (".css", ".js", ".png", ".jpg", ".svg", ".ico", ".webp", ".woff")
    internos = {h.split("#")[0] for h in hrefs
                if (not h.startswith(externos) or "sistemavendadireta.com.br" in h)
                and not h.lower().endswith(ativos)
                and "/cdn-cgi/" not in h}
    internos.discard("")
    info["links_internos"] = len(internos)
    if len(internos) < 3:
        achados.append(("AVISO", f"apenas {len(internos)} links internos distintos"))

    # ── seguranca ──
    if re.search(r'<form\b', html, re.I) and "website" not in html:
        achados.append(("AVISO", "formulario sem campo honeypot 'website'"))

    return achados, info


def main():
    alvo_url = next((a.split("=", 1)[1] for a in sys.argv if a.startswith("--url=")), None)
    paginas = [(alvo_url, None)] if alvo_url else list(PAGINAS)

    if "--blog" in sys.argv and not alvo_url:
        try:
            _, sm, _ = baixar(f"{BASE}/sitemap.xml")
            posts = re.findall(r"<loc>([^<]*/blog/[^<]+)</loc>", sm)
            paginas += [(p, None) for p in posts]
            print(f"  (+{len(posts)} posts do sitemap)\n")
        except Exception as e:
            print(f"  sitemap indisponivel: {e}\n")

    erros = avisos = 0
    limpas = []
    for url, alvo in paginas:
        achados, info = auditar(url, alvo)
        e = sum(1 for g, _ in achados if g == "ERRO")
        a = len(achados) - e
        erros += e
        avisos += a
        if not achados:
            limpas.append(url)
            continue
        print(f"\n{'━' * 78}\n{url}")
        if info.get("title"):
            print(f"  title ({len(info['title'])}): {info['title']}")
        if info.get("alvo"):
            print(f"  alvo:  {info['alvo']}")
        print(f"  {info.get('peso_kb','?')} KB | {info.get('links_internos','?')} links internos "
              f"| JSON-LD: {info.get('jsonld','?')} | WhatsApp: {info.get('whatsapp','?')}")
        for g, msg in sorted(achados, key=lambda x: x[0]):
            print(f"    [{g}] {msg}")

    print(f"\n{'━' * 78}")
    print(f"  {len(paginas)} pagina(s) | {erros} erro(s) | {avisos} aviso(s) "
          f"| {len(limpas)} sem apontamento")
    for u in limpas:
        print(f"    ok  {u}")


if __name__ == "__main__":
    main()
