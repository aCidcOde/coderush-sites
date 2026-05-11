/**
 * Site Shell — Comportamento compartilhado pelas páginas internas
 * (portal de blog e single post).
 *
 * Responsabilidades:
 *   - Mobile drawer (abrir/fechar/teclado/swipe/scroll-spy do Flux guide)
 *   - Header sticky-on-scroll + barra de progresso
 *   - Custom cursor (dot + ring com easing)
 *   - Sticky CTA flutuante (mostra após 60% da viewport)
 *   - Scroll reveal (.rv / .rv-scale) com IntersectionObserver
 *   - Spotlight em cards .has-spotlight (radial seguindo o mouse)
 *   - Particles canvas animado (apenas quando #particles-canvas existir,
 *     fora de prefers-reduced-motion e em viewport >= 760px)
 *
 * Carregar com <script defer> em conjunto com site-layout.js (este vem
 * antes para injetar o header). Expõe window.SiteShell.closeDrawer e
 * window.SiteShell.openDrawer para casos pontuais.
 */
(function () {
  'use strict';

  var prefersReducedMotion = window.matchMedia &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var isCoarsePointer = window.matchMedia &&
    window.matchMedia('(hover:none),(pointer:coarse)').matches;

  var drawer, drawerPanel, drawerBackdrop, menuToggleBtn, drawerCloseBtn;
  var drawerLinks = [];
  var drawerSectionLinks = [];
  var drawerFluxText;
  var lastFocusedElement = null;
  var touchStartX = 0;
  var touchCurrentX = 0;
  var isDraggingDrawer = false;

  var drawerFluxTips = {
    value: 'Comece por Benefícios para enxergar os ganhos de eficiência e custo logo no início.',
    how: 'Em Como Funciona, você entende o método de implantação com segurança e governança.',
    features: 'Recursos mostra os controles operacionais que reduzem risco e aumentam escala.',
    interactive: 'Plataforma é o melhor ponto para ver o fluxo real e sentir o nível de automação.',
    testimonials: 'Cases traz prova de resultado e contexto de aplicação no mundo real.',
    faq: 'No FAQ você resolve dúvidas práticas sobre prazo, governança, integrações, segurança e ROI.',
    newsletter: 'Na Newsletter você recebe conteúdos aplicáveis para manter evolução contínua da operação.',
    blog: 'No Blog, você aprofunda estratégia e boas práticas para evoluir com consistência.'
  };

  function isDrawerOpen() {
    return drawer && drawer.classList.contains('open');
  }
  function setDrawerState(open) {
    if (!drawer) return;
    drawer.classList.toggle('open', open);
    drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
    if (menuToggleBtn) menuToggleBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    document.body.style.overflow = open ? 'hidden' : '';
    document.body.classList.toggle('drawer-open', open);
    if (!open && drawerPanel) drawerPanel.style.transform = '';
    if (!open && drawerBackdrop) drawerBackdrop.style.opacity = '';
  }
  function openDrawer() {
    if (!drawer) return;
    lastFocusedElement = document.activeElement;
    setDrawerState(true);
    window.setTimeout(function () {
      if (drawerCloseBtn) drawerCloseBtn.focus();
    }, 120);
  }
  function closeDrawer() {
    if (!drawer) return;
    setDrawerState(false);
    if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
      lastFocusedElement.focus();
    } else if (menuToggleBtn) {
      menuToggleBtn.focus();
    }
  }
  function toggleNav() {
    if (isDrawerOpen()) closeDrawer(); else openDrawer();
  }
  function trapDrawerFocus(event) {
    if (!isDrawerOpen() || event.key !== 'Tab' || drawerLinks.length === 0) return;
    var first = drawerLinks[0];
    var last = drawerLinks[drawerLinks.length - 1];
    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault(); last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault(); first.focus();
    }
  }
  function updateDrawerFluxGuide(sectionId) {
    if (!drawerFluxText) return;
    drawerFluxText.textContent = drawerFluxTips[sectionId] ||
      'Navegue pelas seções para descobrir recomendações rápidas do Flux.';
  }

  function refreshDrawerLinks() {
    if (!drawer) return;
    drawerLinks = Array.prototype.slice.call(
      drawer.querySelectorAll('a,button,[tabindex]:not([tabindex="-1"])')
    );
    drawerSectionLinks = Array.prototype.slice.call(
      drawer.querySelectorAll('.drawer-link[data-section]')
    );
  }

  function initDrawer() {
    drawer = document.getElementById('mobile-drawer');
    drawerPanel = document.getElementById('drawer-panel');
    drawerBackdrop = document.getElementById('drawer-backdrop');
    drawerCloseBtn = document.getElementById('drawer-close-btn');
    drawerFluxText = document.getElementById('drawer-flux-text');
    menuToggleBtn = document.getElementById('menu-toggle');
    if (!drawer) return;
    refreshDrawerLinks();

    if (menuToggleBtn) menuToggleBtn.addEventListener('click', toggleNav);
    if (drawerCloseBtn) drawerCloseBtn.addEventListener('click', closeDrawer);
    if (drawerBackdrop) drawerBackdrop.addEventListener('click', closeDrawer);

    drawer.addEventListener('click', function (e) {
      var link = e.target.closest('a');
      if (link && drawer.contains(link)) closeDrawer();
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && isDrawerOpen()) closeDrawer();
      trapDrawerFocus(e);
    });
    window.addEventListener('resize', function () {
      if (window.innerWidth > 768 && isDrawerOpen()) closeDrawer();
    });

    if (drawerPanel) {
      drawerPanel.addEventListener('touchstart', function (e) {
        touchStartX = e.changedTouches[0].clientX;
        touchCurrentX = touchStartX;
        isDraggingDrawer = false;
      }, { passive: true });
      drawerPanel.addEventListener('touchmove', function (e) {
        if (!isDrawerOpen()) return;
        touchCurrentX = e.changedTouches[0].clientX;
        var distance = Math.max(0, touchCurrentX - touchStartX);
        if (distance > 6) isDraggingDrawer = true;
        if (!isDraggingDrawer) return;
        drawerPanel.style.transform = 'translateX(' + Math.min(distance, 160) + 'px)';
        if (drawerBackdrop) drawerBackdrop.style.opacity = '' + Math.max(0.35, 1 - distance / 190);
      }, { passive: true });
      drawerPanel.addEventListener('touchend', function () {
        var distance = touchCurrentX - touchStartX;
        if (distance > 72 && isDrawerOpen()) {
          closeDrawer();
        } else if (isDrawerOpen()) {
          drawerPanel.style.transform = '';
          if (drawerBackdrop) drawerBackdrop.style.opacity = '';
        }
        isDraggingDrawer = false;
      });
    }

    if ('IntersectionObserver' in window && drawerSectionLinks.length) {
      var obs = new IntersectionObserver(function (entries) {
        var best = null;
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          if (!best || entry.intersectionRatio > best.intersectionRatio) best = entry;
        });
        if (!best) return;
        var activeId = best.target.id;
        drawerSectionLinks.forEach(function (link) {
          link.classList.toggle('is-active', link.dataset.section === activeId);
        });
        updateDrawerFluxGuide(activeId);
      }, { threshold: [0.35, 0.6, 0.85], rootMargin: '-20% 0px -55% 0px' });

      drawerSectionLinks.forEach(function (link) {
        var sectionId = link.dataset.section;
        var section = sectionId ? document.getElementById(sectionId) : null;
        if (section) obs.observe(section);
      });
    }
  }

  function initScrollChrome() {
    var header = document.getElementById('header');
    var progress = document.getElementById('scroll-progress');
    var stickyCta = document.getElementById('sticky-cta');
    var lastY = -1;

    function onScroll() {
      var sy = window.scrollY;
      if (sy === lastY) return;
      lastY = sy;
      if (header) header.classList.toggle('scrolled', sy > 40);
      if (progress) {
        var max = document.body.scrollHeight - window.innerHeight;
        var val = max > 0 ? (sy / max) * 100 : 0;
        progress.style.width = val + '%';
      }
      if (stickyCta) {
        stickyCta.classList.toggle('visible', sy > window.innerHeight * 0.6);
      }
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  function initCustomCursor() {
    if (isCoarsePointer || prefersReducedMotion) return;
    var dot = document.getElementById('cursor-dot');
    var ring = document.getElementById('cursor-ring');
    if (!dot || !ring) return;

    var mx = window.innerWidth / 2, my = window.innerHeight / 2;
    var rx = mx, ry = my;

    document.addEventListener('mousemove', function (e) {
      mx = e.clientX; my = e.clientY;
      dot.style.left = mx + 'px';
      dot.style.top = my + 'px';
    }, { passive: true });

    (function loop() {
      rx += (mx - rx) * 0.14;
      ry += (my - ry) * 0.14;
      ring.style.left = rx + 'px';
      ring.style.top = ry + 'px';
      requestAnimationFrame(loop);
    })();

    document.addEventListener('mouseover', function (e) {
      if (e.target.closest('a,button,input,textarea,.fchip,.flux-link,.wa-circle,.r-card,.news-card')) {
        dot.style.transform = 'translate(-50%,-50%) scale(1.8)';
        dot.style.background = 'var(--a1)';
      } else {
        dot.style.transform = 'translate(-50%,-50%) scale(1)';
        dot.style.background = 'var(--a3)';
      }
    });

    document.addEventListener('mouseleave', function () {
      dot.style.opacity = '0';
      ring.style.opacity = '0';
    });
    document.addEventListener('mouseenter', function () {
      dot.style.opacity = '';
      ring.style.opacity = '';
    });
  }

  var revealObserver = null;
  function initScrollReveal() {
    if (!('IntersectionObserver' in window) || prefersReducedMotion) {
      Array.prototype.forEach.call(document.querySelectorAll('.rv,.rv-scale'), function (el) {
        el.classList.add('in');
      });
      return;
    }
    if (!revealObserver) {
      revealObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('in');
            revealObserver.unobserve(entry.target);
          }
        });
      }, { threshold: 0.12 });
    }
    document.querySelectorAll('.rv:not(.in),.rv-scale:not(.in)').forEach(function (el) {
      revealObserver.observe(el);
    });
  }

  function initSpotlight() {
    if (isCoarsePointer) return;
    document.addEventListener('mousemove', function (e) {
      var card = e.target.closest && e.target.closest('.has-spotlight');
      if (!card) return;
      var r = card.getBoundingClientRect();
      card.style.setProperty('--mx', (((e.clientX - r.left) / r.width) * 100).toFixed(1) + '%');
      card.style.setProperty('--my', (((e.clientY - r.top) / r.height) * 100).toFixed(1) + '%');
    }, { passive: true });
  }

  function initParticles() {
    var canvas = document.getElementById('particles-canvas');
    if (!canvas) return;
    if (prefersReducedMotion || window.innerWidth < 760) {
      canvas.style.display = 'none';
      return;
    }
    var ctx = canvas.getContext && canvas.getContext('2d');
    if (!ctx) return;

    var W = 0, H = 0;
    var particles = [];
    var COUNT = 60;

    function size() {
      var rect = canvas.getBoundingClientRect();
      W = canvas.width = Math.max(1, Math.floor(rect.width));
      H = canvas.height = Math.max(1, Math.floor(rect.height));
    }
    function spawn() {
      return {
        x: Math.random() * W,
        y: Math.random() * H,
        vx: (Math.random() - 0.5) * 0.26,
        vy: (Math.random() - 0.5) * 0.26,
        r: Math.random() * 1.3 + 0.4,
        op: Math.random() * 0.32 + 0.1,
        hue: Math.random() > 0.5 ? '0,255,122' : '125,255,77'
      };
    }
    function reset() {
      size();
      particles = [];
      for (var i = 0; i < COUNT; i++) particles.push(spawn());
    }
    reset();
    window.addEventListener('resize', reset);

    (function tick() {
      ctx.clearRect(0, 0, W, H);
      for (var i = 0; i < particles.length; i++) {
        var p = particles[i];
        p.x += p.vx; p.y += p.vy;
        if (p.x < 0) p.x = W; if (p.x > W) p.x = 0;
        if (p.y < 0) p.y = H; if (p.y > H) p.y = 0;
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(' + p.hue + ',' + p.op + ')';
        ctx.fill();
      }
      for (var a = 0; a < particles.length; a++) {
        for (var b = a + 1; b < particles.length; b++) {
          var dx = particles[a].x - particles[b].x;
          var dy = particles[a].y - particles[b].y;
          var d = Math.sqrt(dx * dx + dy * dy);
          if (d < 110) {
            ctx.beginPath();
            ctx.moveTo(particles[a].x, particles[a].y);
            ctx.lineTo(particles[b].x, particles[b].y);
            ctx.strokeStyle = 'rgba(0,255,122,' + (0.07 * (1 - d / 110)) + ')';
            ctx.lineWidth = 0.5;
            ctx.stroke();
          }
        }
      }
      requestAnimationFrame(tick);
    })();
  }

  function start() {
    initDrawer();
    initScrollChrome();
    initCustomCursor();
    initScrollReveal();
    initSpotlight();
    initParticles();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start);
  } else {
    start();
  }

  window.SiteShell = {
    openDrawer: openDrawer,
    closeDrawer: closeDrawer,
    refreshDrawerLinks: refreshDrawerLinks,
    refreshScrollReveal: initScrollReveal
  };
})();
