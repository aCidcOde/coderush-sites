Landing page (LP) — conteúdo dividido em fragments HTML.

pages/lp/sections/<nome-da-section>/section.html
  hero
  success-modal
  trust
  value
  how
  features
  interactive
  testimonials
  blog        (bloco da LP #blog; não confundir com pages/blog/ do portal)
  faq
  form-section
  cta
  newsletter

O arquivo index.html na raiz é GERADO pelo script tools/build-index.py (lê estas
pastas e grava HTML único). Depois de editar qualquer fragment ou section:

  python tools/build-index.py

Assim a página funciona também abrindo o arquivo direto (duplo clique / file://),
sem depender de fetch() no navegador.

Para alterar uma seção, edite o section.html correspondente e rode o comando acima.

Opcional: assets/js/lp-bootstrap.mjs só para testes com servidor HTTP (carrega
fragments em runtime).
