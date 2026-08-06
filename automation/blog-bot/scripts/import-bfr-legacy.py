#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
[Modulo Blog-bot — importacao do acervo legado da BFR]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-05
Converte bfrintelligence/data/.posts.json (acervo herdado do FluxoInteligente +
posts da fase API) em paginas estaticas /YYYY/MM/DD/slug/index.html no template
do site, preenche os markers de cards (home + conteudos), gera sitemap/robots e
o mapa de redirects slug->path pro nginx.

O shell HTML espelha renderBfrPostTemplate() do lib/publisher.js — manter em sincronia.
Idempotente: pode rodar de novo que regenera tudo.
"""
import json, os, re, html as H
import markdown

ROOT = "/data/coderush-sites"
SITE = os.path.join(ROOT, "bfrintelligence")
BASE_URL = "https://bfrintelligence.com.br"
ASSET_VERSION = "20260806-1"   # subir quando o CSS mudar (cache-busting)

def esc(v):
    return H.escape(str(v or ""), quote=True)

def category_for(p):
    cat = p.get("category") or ""
    if cat in ("Engenharia de agentes", "Automação e integrações", "Governança e ROI"):
        return cat
    hay = (p.get("slug", "") + " " + p.get("title", "")).lower()
    if re.search(r"governan|auditoria|permiss|roi|seguran|observabilidade|log", hay):
        return "Governança e ROI"
    if re.search(r"automa|integra|n8n|canal|canais|atendimento", hay):
        return "Automação e integrações"
    return "Engenharia de agentes"

def br_date(iso):
    d = iso[:10]
    return f"{d[8:10]}/{d[5:7]}/{d[0:4]}"

def dated_path(p):
    d = p["published_at"][:10]
    return f"{d[0:4]}/{d[5:7]}/{d[8:10]}/{p['slug']}/"

YT_EMBED = ('<div class="bfr-embed"><iframe src="https://www.youtube-nocookie.com/embed/{vid}" '
            'title="Video do YouTube" loading="lazy" allow="accelerometer; autoplay; clipboard-write; '
            'encrypted-media; gyroscope; picture-in-picture; web-share" '
            'referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe></div>')

def youtube_embeds(body):
    """Converte referencias de video em embed responsivo (antes do markdown)."""
    # formato legado da fase API: thumbnail + linha "Assistir no YouTube"
    body = re.sub(
        r"!\[[^\]]*\]\(https://i\.ytimg\.com/vi/([A-Za-z0-9_-]{6,})/[^)]*\)\s*\n+\s*"
        r"\*\*[^\[]*\[[^\]]+\]\(https://www\.youtube\.com/watch\?v=\1[^)]*\)\*\*",
        lambda m: "@@YT:" + m.group(1) + "@@", body)
    # link ou URL de youtube sozinho numa linha
    body = re.sub(
        r"(?m)^\s*(?:\*\*)?\[?[^\[\]\n]*\]?\(?https://(?:www\.)?youtube\.com/watch\?v=([A-Za-z0-9_-]{6,})[^)\s]*\)?(?:\*\*)?\s*$",
        lambda m: "@@YT:" + m.group(1) + "@@", body)
    return body

def md_to_html(body):
    body = youtube_embeds(body)
    # links de imagem do fluxo viram locais; iframes nao existem no acervo
    body = body.replace("https://fluxointeligenteia.com.br/imagens/posts/", "../../../../imagens/posts/")
    body = body.replace("https://i.ytimg.com/vi/", "https://i.ytimg.com/vi/")  # thumbs externas ok em links
    out = markdown.markdown(body, extensions=["extra", "sane_lists"])
    out = re.sub(r"(?:<p>)?@@YT:([A-Za-z0-9_-]{6,})@@(?:</p>)?", lambda m: YT_EMBED.format(vid=m.group(1)), out)
    return out


TITLE_SUFFIX = " | BFR Intelligence"

def fit_title(raw):
    """Corta o titulo contando o sufixo da marca (limite real de 70 chars)."""
    raw = (raw or "").strip()
    room = 70 - len(TITLE_SUFFIX)
    if len(raw) <= room:
        return raw
    return raw[:room - 1].rsplit(" ", 1)[0] + "…"

LEGACY_DESC = re.compile(r"FluxoInteligente|Atualiza[çc][ãa]o semanal", re.I)
LEAD_VERBS = re.compile(r"^(entenda|descubra|saiba|veja|conhe[çc]a|aprenda)\s+(como|a|o|os|as|por que|porque|a import[âa]ncia de)?\s*", re.I)

def first_sentence(body):
    """Primeira frase util do corpo, sem markdown."""
    for raw in (body or "").split("\n"):
        line = raw.strip()
        if not line or line.startswith(("#", ">", "-", "*", "!", "|")):
            continue
        line = re.sub(r"\*\*|\*|`|\[([^\]]+)\]\([^)]+\)", lambda m: m.group(1) if m.lastindex else "", line)
        line = line.strip()
        if len(line) > 40:
            parts = re.split(r"(?<=[.!?])\s+", line)
            return parts[0].strip()
    return ""

def build_description(p):
    """Description propria da BFR: nunca herda a copy generica do bot antigo."""
    desc = (p.get("meta_description") or "").strip()
    if not desc or LEGACY_DESC.search(desc):
        desc = (p.get("excerpt") or "").strip()
    if len(desc) < 70:
        extra = first_sentence(p.get("body"))
        if extra and extra.lower() not in desc.lower():
            desc = (desc + " " + extra).strip() if desc else extra
    desc = re.sub(r"\s+", " ", desc).strip()
    if len(desc) > 160:
        desc = desc[:159].rsplit(" ", 1)[0] + "…"
    return desc

def title_from_excerpt(p):
    """Titulo alternativo pra posts que repetem headline (dedupe editorial)."""
    base = re.sub(r"\s+", " ", (p.get("excerpt") or "").strip())
    base = LEAD_VERBS.sub("", base).strip()
    base = re.sub(r"[.!?]+$", "", base)
    if not base:
        return None
    base = base[0].upper() + base[1:]
    if len(base) > 58:
        base = base[:57].rsplit(" ", 1)[0] + "…"
    return base

def page_html(p, cat):
    rel = "../../../../"
    slug = p["slug"]
    canonical = f"{BASE_URL}/{dated_path(p)}"
    image_url = f"{BASE_URL}/imagens/posts/{slug}.jpg"
    title = p["title"]
    SUFFIX = TITLE_SUFFIX
    meta_title = fit_title(p.get("meta_title") or title)
    meta_desc = build_description(p)
    excerpt = p.get("excerpt") or ""
    body_html = md_to_html(p.get("body") or "")
    tags = [t for t in (p.get("tags") or []) if t]
    keywords = ", ".join(dict.fromkeys(tags + [cat.lower(), "BFR Intelligence"]))
    ld = {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "headline": title,
        "description": meta_desc,
        "datePublished": p["published_at"],
        "dateModified": p.get("updated_at") or p["published_at"],
        "mainEntityOfPage": {"@type": "WebPage", "@id": canonical},
        "image": [image_url],
        "author": {"@type": "Organization", "name": "BFR Intelligence"},
        "publisher": {"@type": "Organization", "name": "BFR Intelligence"},
    }
    return f"""<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{esc(meta_title)}{SUFFIX}</title>
  <meta name="description" content="{esc(meta_desc)}" />
  <meta name="keywords" content="{esc(keywords)}" />
  <meta name="author" content="BFR Intelligence" />
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
  <link rel="canonical" href="{canonical}" />
  <link rel="icon" type="image/png" href="{rel}assets/logo-bfr.png" />
  <meta property="og:type" content="article" />
  <meta property="og:title" content="{esc(meta_title)}{SUFFIX}" />
  <meta property="og:description" content="{esc(meta_desc)}" />
  <meta property="og:url" content="{canonical}" />
  <meta property="og:image" content="{image_url}" />
  <meta property="og:site_name" content="BFR Intelligence" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:image" content="{image_url}" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="{rel}conteudos/blog.css?v={ASSET_VERSION}" />
  <script type="application/ld+json">
{json.dumps(ld, ensure_ascii=False, indent=2)}
  </script>
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-BF0KGVD1LN"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){{dataLayer.push(arguments);}}
    gtag('js', new Date());
    gtag('config', 'G-BF0KGVD1LN');
  </script>
</head>
<body class="bfr-blog">
  <nav class="bfr-nav">
    <div class="bfr-nav-inner">
      <a class="bfr-logo" href="{rel}"><span class="bfr-logo-mark">B</span><b>BFR Intelligence</b></a>
      <div class="bfr-nav-links">
        <a href="{rel}">Início</a>
        <a href="{rel}conteudos/">Conteúdos</a>
        <a class="bfr-nav-cta" href="{rel}#contato">Criar meu agente</a>
      </div>
    </div>
  </nav>

  <main class="bfr-main">
    <p class="bfr-crumb"><a href="{rel}conteudos/">Conteúdos</a> / {esc(cat)}</p>
    <article class="bfr-article">
      <span class="bfr-chip">{esc(cat)}</span>
      <h1>{esc(title)}</h1>
      <p class="bfr-meta">{esc(p.get('author_name') or 'BFR Intelligence')} · {esc(br_date(p['published_at']))}</p>
      <p class="bfr-excerpt">{esc(excerpt)}</p>
      <figure class="bfr-cover">
        <img src="{rel}imagens/posts/{slug}.jpg" alt="{esc(p.get('featured_image_alt') or title)}" width="1200" height="630" loading="eager" decoding="async" />
      </figure>
      <div class="bfr-content">
{body_html}
      </div>
    </article>

    <div class="bfr-cta" id="cta">
      <h2>Quer tirar a IA do piloto e colocar na operação?</h2>
      <p>A BFR estrutura agentes com contexto do negócio, integração aos seus sistemas, governança e ROI mensurável.</p>
      <a href="{rel}#contato">Criar meu agente</a>
    </div>

    <section class="bfr-related">
      <div class="bfr-cards">
    <!-- BLOG-LEIA-TAMBEM START -->
    <!-- BLOG-LEIA-TAMBEM END -->
    <!-- BLOG-CROSS-SITE START -->
    <!-- BLOG-CROSS-SITE END -->
      </div>
    </section>
  </main>

  <footer class="bfr-footer">
    <div class="bfr-footer-inner">
      <span>© BFR Intelligence — Todos os direitos reservados.</span>
      <span><a href="{rel}termos.html">Termos</a> · <a href="{rel}privacidade.html">Privacidade</a> · <a href="{rel}lgpd.html">LGPD</a></span>
    </div>
  </footer>
</body>
</html>
"""

def card_html(p, prefix):
    # espelha renderCard() do publisher (data-attrs + estrutura) pro merge futuro funcionar
    post_path = dated_path(p)
    img = f"imagens/posts/{p['slug']}.jpg"
    href = prefix + post_path
    src = prefix + img
    title = esc(p["title"])
    excerpt = esc((p.get("excerpt") or "")[:180])
    return "\n".join([
        f'<article class="overflow-hidden rounded-2xl border border-white/15 bg-white/5" data-blog-path="{esc(post_path)}" data-blog-image="{esc(img)}" data-blog-slug="{esc(p["slug"])}" data-blog-date="{esc(p["published_at"][:10])}">',
        f'  <a href="{href}">',
        f'    <img src="{src}" alt="{title}" class="h-44 w-full object-cover" width="1200" height="630" loading="lazy" />',
        "  </a>",
        '  <div class="p-4">',
        f'    <h2 class="text-base font-semibold leading-snug"><a href="{href}" class="hover:underline">{title}</a></h2>',
        f'    <p class="mt-2 text-sm leading-relaxed text-white/80">{excerpt}</p>',
        "  </div>",
        "</article>",
    ])

def replace_between(text, start, end, replacement):
    pattern = re.compile(re.escape(start) + r"[\s\S]*?" + re.escape(end))
    assert pattern.search(text), f"markers ausentes: {start}"
    return pattern.sub(start + "\n" + replacement + "\n" + end, text)

def main():
    raw = json.load(open(os.path.join(SITE, "data/.posts.json"), encoding="utf-8"))
    posts = [p for p in raw if isinstance(p, dict) and p.get("status") == "published"]
    posts.sort(key=lambda p: p["published_at"], reverse=True)

    # dedupe de titulos (o bot antigo repetiu headlines): mantem o mais recente,
    # os demais recebem titulo derivado do proprio resumo
    seen_titles = {}
    for p in sorted(posts, key=lambda x: x["published_at"], reverse=True):
        key = fit_title(p["title"]).lower()
        if key in seen_titles:
            alt = title_from_excerpt(p)
            if alt and fit_title(alt).lower() not in seen_titles:
                p["title"] = alt
                p["meta_title"] = alt
                seen_titles[fit_title(alt).lower()] = True
                continue
        seen_titles[key] = True
    print(f"posts validos: {len(posts)} (descartados: {len(raw)-len(posts)})")

    redirects = []
    for p in posts:
        cat = category_for(p)
        rel_dir = os.path.join(SITE, dated_path(p))
        os.makedirs(rel_dir, exist_ok=True)
        open(os.path.join(rel_dir, "index.html"), "w", encoding="utf-8").write(page_html(p, cat))
        redirects.append((p["slug"], "/" + dated_path(p)))

    # cards nos markers
    home_cards = "\n".join(card_html(p, "") for p in posts[:3])
    index_cards = "\n".join(card_html(p, "../") for p in posts)
    hp = os.path.join(SITE, "index.html")
    s = open(hp, encoding="utf-8").read()
    s = replace_between(s, "<!-- BLOG-HOME-CARDS:START -->", "<!-- BLOG-HOME-CARDS:END -->", home_cards)
    open(hp, "w", encoding="utf-8").write(s)
    ip = os.path.join(SITE, "conteudos/index.html")
    s = open(ip, encoding="utf-8").read()
    s = replace_between(s, "<!-- BLOG-INDEX-CARDS:START -->", "<!-- BLOG-INDEX-CARDS:END -->", index_cards)
    open(ip, "w", encoding="utf-8").write(s)

    # sitemap + robots
    latest = posts[0]["published_at"][:10]
    urls = [
        (f"{BASE_URL}/", latest, "weekly", "1.0"),
        (f"{BASE_URL}/conteudos/", latest, "weekly", "0.9"),
        (f"{BASE_URL}/termos.html", "2026-08-05", "yearly", "0.3"),
        (f"{BASE_URL}/privacidade.html", "2026-08-05", "yearly", "0.3"),
        (f"{BASE_URL}/lgpd.html", "2026-08-05", "yearly", "0.3"),
    ] + [(f"{BASE_URL}/{dated_path(p)}", p["published_at"][:10], "monthly", "0.8") for p in posts]
    sm = ['<?xml version="1.0" encoding="UTF-8"?>', '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">']
    for u, lm, cf, pr in urls:
        sm += ["  <url>", f"    <loc>{u}</loc>", f"    <lastmod>{lm}</lastmod>",
               f"    <changefreq>{cf}</changefreq>", f"    <priority>{pr}</priority>", "  </url>"]
    sm.append("</urlset>")
    open(os.path.join(SITE, "sitemap.xml"), "w", encoding="utf-8").write("\n".join(sm) + "\n")
    open(os.path.join(SITE, "robots.txt"), "w", encoding="utf-8").write(
        f"User-agent: *\nAllow: /\nDisallow: /data/\nDisallow: /api.php\n\nSitemap: {BASE_URL}/sitemap.xml\n")

    # mapa de redirects pro nginx (artigo.html?slug=X -> path datado)
    out = ["# gerado por import-bfr-legacy.py — map $arg_slug $bfr_artigo_destino"]
    for slug, path in redirects:
        out.append(f"{slug} {path};")
    open(os.path.join(ROOT, "docker/nginx/bfr-artigo-map.conf"), "w", encoding="utf-8").write(
        "map_hash_bucket_size 128;\nmap $arg_slug $bfr_artigo_destino {\n    default \"\";\n" +
        "\n".join(f"    {slug} {path};" for slug, path in redirects) + "\n}\n")

    print(f"paginas geradas: {len(posts)} | cards home: 3 | cards index: {len(posts)}")
    print("sitemap, robots e bfr-artigo-map.conf gerados")

if __name__ == "__main__":
    main()
