# -*- coding: utf-8 -*-
"""Reconstrói index.html na raiz a partir de pages/componentes e pages/lp/sections.
Execute na raiz do projeto:  python tools/build-index.py
Assim a LP funciona abrindo o arquivo direto (file://) sem depender de fetch()."""
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent

# Componentes antes das seções da LP
COMPONENTS_TOP = [
    "cursor",
    "scroll-progress",
    "slot-header",
    "sticky-cta",
]

# Depois do conteúdo principal
COMPONENTS_BOTTOM = [
    "slot-footer",
    "whatsapp",
    "flux-guide",
    "mobile-drawer",
]

SECTIONS = [
    "hero",
    "success-modal",
    "trust",
    "value",
    "how",
    "features",
    "interactive",
    "testimonials",
    "blog",
    "faq",
    "form-section",
    "cta",
    "newsletter",
]

HEAD = """<!-- LP gerada por tools/build-index.py a partir de pages/componentes e pages/lp/sections. -->
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FluxoInteligente IA — Agentes Corporativos com Governança e ROI</title>
<meta name="description" content="Agentes corporativos com RAG, ferramentas seguras, permissões, auditoria e canais integrados. Tire projetos de IA do piloto e coloque em produção com governança e resultado mensurável.">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<link rel="canonical" href="https://fluxointeligenteia.com.br/">
<meta property="og:type" content="website">
<meta property="og:title" content="FluxoInteligente IA — Agentes Corporativos com Governança e ROI">
<meta property="og:description" content="Agentes corporativos com RAG, ferramentas seguras, permissões, auditoria e canais integrados. Tire projetos de IA do piloto e coloque em produção com governança e resultado mensurável.">
<meta property="og:url" content="https://fluxointeligenteia.com.br/">
<meta property="og:site_name" content="FluxoInteligente IA">
<meta name="twitter:card" content="summary_large_image">
<link rel="icon" type="image/svg+xml" href="favicon.svg">
<link rel="alternate icon" href="favicon.ico">
<link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Sora:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Sora:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/index.css">
<link rel="stylesheet" href="css/site-tailwind.css">
<link rel="stylesheet" href="css/hub-parity.css">
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "FluxoInteligente IA",
  "url": "https://fluxointeligenteia.com.br/",
  "description": "Agentes corporativos com RAG, ferramentas seguras, permissões, auditoria e canais integrados."
}
</script>
</head>
<body data-site="home">

"""

TAIL = """
<script defer src="assets/js/site-layout.js"></script>
<script defer src="assets/js/index.js"></script>
</body>
</html>
"""


def preserve_marker_block(rendered: str, existing: str, marker: str) -> str:
    start = f"<!-- {marker}:START -->"
    end = f"<!-- {marker}:END -->"
    existing_start = existing.find(start)
    existing_end = existing.find(end, existing_start)
    rendered_start = rendered.find(start)
    rendered_end = rendered.find(end, rendered_start)

    if min(existing_start, existing_end, rendered_start, rendered_end) == -1:
        return rendered

    existing_block = existing[existing_start : existing_end + len(end)]
    return rendered[:rendered_start] + existing_block + rendered[rendered_end + len(end) :]


def main():
    parts = [HEAD]
    for name in COMPONENTS_TOP:
        f = ROOT / "pages" / "componentes" / name / "fragment.html"
        parts.append(f.read_text(encoding="utf-8").rstrip() + "\n\n")
    parts.append(
        "<!-- Conteúdo principal (gerado a partir de pages/lp/sections/) -->\n"
    )
    for name in SECTIONS:
        f = ROOT / "pages" / "lp" / "sections" / name / "section.html"
        parts.append(f.read_text(encoding="utf-8").rstrip() + "\n\n")
    for name in COMPONENTS_BOTTOM:
        f = ROOT / "pages" / "componentes" / name / "fragment.html"
        parts.append(f.read_text(encoding="utf-8").rstrip() + "\n\n")
    parts.append(TAIL)
    out = ROOT / "index.html"
    existing = out.read_text(encoding="utf-8") if out.exists() else ""
    rendered = "".join(parts)
    rendered = preserve_marker_block(rendered, existing, "BLOG-HOME-CARDS")
    out.write_text(rendered, encoding="utf-8")
    print("OK ->", out, "(%d bytes)" % out.stat().st_size)


if __name__ == "__main__":
    main()
