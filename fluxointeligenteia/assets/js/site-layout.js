/**
 * Header e footer globais — uma única fonte de verdade.
 * body[data-site="home"]    → index.html na raiz (sem prefixo)
 * body[data-site="blog"]    → blog/index.html (prefixo "../")
 * body[data-site="post"]    → 2026/MM/DD/slug/index.html (prefixo "../../../../")
 * body[data-site="internal"]→ outra página um nível abaixo da raiz (prefixo "../")
 * body[data-root="..."]     → override explícito do prefixo (tem prioridade)
 */
(function () {
  function siteKind() {
    var s = document.body && document.body.getAttribute('data-site');
    if (s === 'home') return 'home';
    if (s === 'blog') return 'blog';
    if (s === 'post') return 'post';
    return 'internal';
  }

  function rootPrefix(kind) {
    var override = document.body && document.body.getAttribute('data-root');
    if (override) return override;
    if (kind === 'home') return '';
    if (kind === 'post') return '../../../../';
    return '../';
  }

  function sectionHref(mode, homePrefix, id) {
    return mode === 'home' ? '#' + id : homePrefix + '#' + id;
  }

  function buildHeader(mode, ctx) {
    var h = function (id) { return sectionHref(mode, ctx.home, id); };
    return (
      '<header id="header">' +
      '<div class="header-inner">' +
      '<a class="logo" href="' + ctx.logo + '">' +
      '<span class="logo-mark" aria-hidden="true">' +
      '<img src="' + ctx.asset + 'img/flux-icon.png" alt="Ícone Flux" aria-hidden="true">' +
      '</span>' +
      '<span class="logo-text">Fluxo<em>Inteligente</em> IA</span>' +
      '<span class="logo-guide">Flux</span>' +
      '</a>' +
      '<nav>' +
      '<a href="' + h('value') + '">Benefícios</a>' +
      '<a href="' + h('how') + '">Como Funciona</a>' +
      '<a href="' + h('features') + '">Recursos</a>' +
      '<a href="' + h('interactive') + '">Plataforma</a>' +
      '<a href="' + h('testimonials') + '">Cases</a>' +
      (mode === 'home' ? '<a href="' + h('faq') + '">FAQ</a>' : '') +
      '<a href="' + ctx.blog + '">Blog</a>' +
      '</nav>' +
      '<div class="nav-ctas">' +
      '<a class="btn btn-primary" href="' + ctx.cta + '" style="padding:9px 18px;font-size:13px;">' +
      '<div class="shimmer"></div>' +
      '<span class="btn-inner">Criar meu agente <span class="btn-arrow">→</span></span>' +
      '</a>' +
      '<button class="menu-toggle" id="menu-toggle" aria-label="Abrir menu de navegação" aria-controls="mobile-drawer" aria-expanded="false">' +
      '<span class="menu-toggle-lines" aria-hidden="true">' +
      '<span class="menu-toggle-line"></span>' +
      '<span class="menu-toggle-line"></span>' +
      '<span class="menu-toggle-line"></span>' +
      '</span>' +
      '</button>' +
      '</div>' +
      '</div>' +
      '</header>'
    );
  }

  function buildFooter(mode, ctx) {
    var h = function (id) { return sectionHref(mode, ctx.home, id); };
    return (
      '<footer id="footer">' +
      '<div class="footer-top">' +
      '<div class="footer-brand">' +
      '<a href="' + ctx.footerLogo + '" class="logo">' +
      '<div class="logo-mark">' +
      '<img src="' + ctx.asset + 'img/flux-icon.png" alt="Ícone Flux" aria-hidden="true">' +
      '</div>' +
      '<span class="logo-text">Fluxo<em>Inteligente</em> IA</span>' +
      '</a>' +
      '<p>Ajudamos times reais a tirar projetos de IA do piloto e colocar em produção com clareza, segurança e resultado que dá para medir.</p>' +
      '<div class="footer-flux-note">' +
      '<strong style="color:#eaffef;font-size:12px;display:block;margin-bottom:4px">Flux recomenda:</strong>' +
      ' Comece pela FAQ e pela Plataforma para entender rapidamente o que já pode ser automatizado na sua operação.' +
      '</div>' +
      '</div>' +
      '<div>' +
      '<div class="footer-col-title">Navegação</div>' +
      '<div class="footer-links">' +
      '<a href="' + h('value') + '">Benefícios</a>' +
      '<a href="' + h('how') + '">Como funciona</a>' +
      '<a href="' + h('features') + '">Recursos</a>' +
      '<a href="' + h('interactive') + '">Plataforma</a>' +
      '<a href="' + h('testimonials') + '">Cases</a>' +
      '</div>' +
      '</div>' +
      '<div>' +
      '<div class="footer-col-title">Conteúdo</div>' +
      '<div class="footer-links">' +
      '<a href="' + ctx.blog + '">Blog</a>' +
      '<a href="' + h('cta') + '">Contato comercial</a>' +
      '<a href="' + h('form-section') + '">Diagnóstico gratuito</a>' +
      '<a href="' + h('interactive') + '">Demonstração</a>' +
      '</div>' +
      '</div>' +
      '<div class="footer-card">' +
      '<strong>Fale com o time</strong>' +
      '<p>Fala com a gente sem burocracia: entendemos seu cenário e te mostramos o próximo passo mais seguro para começar.</p>' +
      '<a href="https://wa.me/5511994566726" class="btn btn-primary" style="padding:10px 18px;font-size:13px;" target="_blank" rel="noopener">' +
      '<div class="shimmer"></div>' +
      '<span class="btn-inner">Abrir conversa <span class="btn-arrow">→</span></span>' +
      '</a>' +
      '<div style="margin-top:10px">' +
      '<span class="footer-mini">Retorno humano em até 2h úteis</span>' +
      '</div>' +
      '</div>' +
      '</div>' +
      '<div class="footer-bottom">' +
      '<div class="footer-copy">© 2026 FluxoInteligente IA · Feito para operações que querem escala com controle.</div>' +
      '<div class="footer-badge">IA aplicada com método, não promessa</div>' +
      '</div>' +
      '</footer>'
    );
  }

  function mount() {
    var headSlot = document.getElementById('flux-slot-header');
    var footSlot = document.getElementById('flux-slot-footer');
    if (!headSlot || !footSlot) return;

    var kind = siteKind();
    var home = kind === 'home';
    var mode = home ? 'home' : 'internal';
    var rp = rootPrefix(kind);
    var ctx = home
      ? {
          home: '',
          asset: 'assets/',
          blog: 'blog/',
          logo: '#',
          footerLogo: '#',
          cta: '#cta'
        }
      : (function () {
          var idx = rp + 'index.html';
          return {
            home: idx,
            asset: rp + 'assets/',
            blog: kind === 'blog' ? './' : rp + 'blog/',
            logo: idx,
            footerLogo: idx,
            cta: idx + '#cta'
          };
        })();

    var headerHtml = buildHeader(mode, ctx);
    var footerHtml = buildFooter(mode, ctx);

    headSlot.insertAdjacentHTML('beforebegin', headerHtml);
    headSlot.remove();

    footSlot.insertAdjacentHTML('beforebegin', footerHtml);
    footSlot.remove();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mount);
  } else {
    mount();
  }
})();
