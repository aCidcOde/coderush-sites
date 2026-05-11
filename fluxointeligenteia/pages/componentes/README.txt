Componentes HTML da landing. O index.html na raiz é montado por tools/build-index.py
(junto com pages/lp/sections/). Após editar fragment.html, rode: python tools/build-index.py

Modo opcional com servidor HTTP: assets/js/lp-bootstrap.mjs carrega os mesmos ficheiros
em runtime (não use file:// com esse modo).

Cada pasta tem fragment.html (markup único).

cursor/           — cursor-dot + cursor-ring
scroll-progress/  — barra de progresso do scroll
slot-header/      — #flux-slot-header (site-layout.js injeta o <header>)
sticky-cta/       — CTA fixo inferior
slot-footer/      — #flux-slot-footer (site-layout.js injeta o <footer>)
whatsapp/         — botão flutuante WhatsApp
flux-guide/       — avatar / guia Flux
mobile-drawer/    — menu drawer (mobile)

Ordem de montagem e dependência de servidor HTTP: ver pages/lp/README.txt
e comentários em lp-bootstrap.mjs.
