// Extracted from index.php block 1.
(function () {
      var btn = document.getElementById('back-to-top');
      if (!btn) return;
      var threshold = 120;
      function sync() {
        var atTop = window.scrollY < threshold;
        btn.classList.toggle('is-at-top', atTop);
        btn.setAttribute('aria-disabled', atTop ? 'true' : 'false');
        if (atTop) {
          btn.setAttribute('tabindex', '-1');
        } else {
          btn.removeAttribute('tabindex');
        }
      }
      btn.addEventListener('click', function () {
        if (btn.classList.contains('is-at-top')) return;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
          window.scrollTo(0, 0);
        } else {
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }
      });
      window.addEventListener('scroll', sync, { passive: true });
      sync();
    })();

// Extracted from index.php block 2.
(function () {
      var header = document.querySelector('[data-site-header]');
      if (!header) return;
      function onScroll() {
        header.classList.toggle('is-scrolled', window.scrollY > 16);
      }
      onScroll();
      window.addEventListener('scroll', onScroll, { passive: true });
    })();

// Extracted from index.php block 3.
(function () {
      var btn = document.getElementById('mobile-menu-btn');
      var menu = document.getElementById('mobile-menu');
      var iconOpen = document.getElementById('mobile-menu-icon-open');
      var iconClose = document.getElementById('mobile-menu-icon-close');
      if (!btn || !menu) return;

      function setOpen(open) {
        menu.classList.toggle('hidden', !open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        btn.setAttribute('aria-label', open ? 'Fechar menu de navegação' : 'Abrir menu de navegação');
        document.body.classList.toggle('overflow-hidden', open);
        if (iconOpen && iconClose) {
          iconOpen.classList.toggle('hidden', open);
          iconClose.classList.toggle('hidden', !open);
        }
      }

      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        setOpen(menu.classList.contains('hidden'));
      });

      menu.querySelectorAll('a[href^="#"]').forEach(function (a) {
        a.addEventListener('click', function () {
          setOpen(false);
        });
      });

      document.addEventListener('click', function (e) {
        if (menu.classList.contains('hidden')) return;
        if (!menu.contains(e.target) && !btn.contains(e.target)) {
          setOpen(false);
        }
      });

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') setOpen(false);
      });

      window.addEventListener('resize', function () {
        if (window.matchMedia('(min-width: 768px)').matches) setOpen(false);
      });
    })();

// Extracted from index.php block 4.
(function () {
      var section = document.getElementById('cases');
      if (!section) return;
      function reveal() {
        section.classList.add('cases-visible');
      }
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        reveal();
        return;
      }
      if (typeof IntersectionObserver === 'undefined') {
        reveal();
        return;
      }
      var obs = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              reveal();
              obs.disconnect();
            }
          });
        },
        { threshold: 0.1, rootMargin: '0px 0px -6% 0px' }
      );
      obs.observe(section);
    })();

// Extracted from index.php block 5.
(function () {
      var section = document.getElementById('depoimentos');
      if (!section) return;
      function reveal() {
        section.classList.add('testimonials-visible');
      }
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        reveal();
        return;
      }
      if (typeof IntersectionObserver === 'undefined') {
        reveal();
        return;
      }
      var obs = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              reveal();
              obs.disconnect();
            }
          });
        },
        { threshold: 0.1, rootMargin: '0px 0px -7% 0px' }
      );
      obs.observe(section);
    })();

// Extracted from index.php block 6.
(function () {
      var section = document.getElementById('faq');
      if (!section) return;
      function reveal() {
        section.classList.add('faq-visible');
      }
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        reveal();
        return;
      }
      if (typeof IntersectionObserver === 'undefined') {
        reveal();
        return;
      }
      var obs = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              reveal();
              obs.disconnect();
            }
          });
        },
        { threshold: 0.12, rootMargin: '0px 0px -8% 0px' }
      );
      obs.observe(section);
    })();

// Extracted from index.php block 7.
(function () {
      var section = document.getElementById('contato');
      if (!section) return;
      function reveal() {
        section.classList.add('contact-visible');
      }
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        reveal();
        return;
      }
      if (typeof IntersectionObserver === 'undefined') {
        reveal();
        return;
      }
      var obs = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              reveal();
              obs.disconnect();
            }
          });
        },
        { threshold: 0.08, rootMargin: '0px 0px -5% 0px' }
      );
      obs.observe(section);
    })();

// Extracted from index.php block 8.
(function () {
      var section = document.getElementById('sobre');
      if (!section) return;
      function reveal() {
        section.classList.add('sobre-visible');
      }
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        reveal();
        return;
      }
      if (typeof IntersectionObserver === 'undefined') {
        reveal();
        return;
      }
      var obs = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              reveal();
              obs.disconnect();
            }
          });
        },
        { threshold: 0.1, rootMargin: '0px 0px -6% 0px' }
      );
      obs.observe(section);
    })();

// Extracted from index.php block 9.
(function () {
      var section = document.getElementById('site-footer');
      if (!section) return;
      function reveal() {
        section.classList.add('footer-visible');
      }
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        reveal();
        return;
      }
      if (typeof IntersectionObserver === 'undefined') {
        reveal();
        return;
      }
      var obs = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              reveal();
              obs.disconnect();
            }
          });
        },
        { threshold: 0.08, rootMargin: '0px 0px -4% 0px' }
      );
      obs.observe(section);
    })();

// Extracted from index.php block 10.
document.addEventListener('DOMContentLoaded', function () {
      var leadForm = document.getElementById('contact-lead-form');
      var leadNameInput = document.getElementById('contact-nome');
      var leadWhatsappInput = document.getElementById('contact-whatsapp');
      var successBox = document.getElementById('contact-success-box');
      var errorBox = document.getElementById('contact-error-box');
      var successWhatsappLink = document.getElementById('contact-success-whatsapp-link');
      var feedbackEl = document.getElementById('contact-feedback');
      var leadStorageKey = 'coderush-contact-lead';
      var urlParams = new URLSearchParams(window.location.search);
      var mailStatus = urlParams.get('mail');
      var defaultWaMsg = 'Ola, vim pelo site CodeRush e quero saber mais sobre as solucoes do ecossistema.';
      var phone = '5511994566726';

      function getStoredLead() {
        try { var r = window.sessionStorage.getItem(leadStorageKey); return r ? JSON.parse(r) : null; } catch (e) { return null; }
      }

      function buildLeadWhatsappUrl(lead) {
        var s = lead || {};
        var tpl = leadForm ? (leadForm.getAttribute('data-whatsapp-message-template') || defaultWaMsg) : defaultWaMsg;
        var p = leadForm ? (leadForm.getAttribute('data-whatsapp-phone') || phone) : phone;
        if (!s.nome && !s.whatsapp) return 'https://wa.me/' + p + '?text=' + encodeURIComponent(defaultWaMsg);
        var msg = tpl.replace('{nome}', (s.nome || 'Nao informado').trim()).replace('{whatsapp}', (s.whatsapp || 'Nao informado').trim());
        return 'https://wa.me/' + p + '?text=' + encodeURIComponent(msg);
      }

      if (mailStatus === 'ok' && successBox) {
        successBox.classList.remove('hidden');
        if (feedbackEl) { feedbackEl.classList.remove('hidden'); feedbackEl.className = 'contact-feedback-alert is-success'; feedbackEl.textContent = 'Recebemos seus dados. Vamos abrir o WhatsApp com uma mensagem pronta para agilizar seu atendimento.'; }
        var storedLead = getStoredLead();
        if (successWhatsappLink) successWhatsappLink.href = buildLeadWhatsappUrl(storedLead);
        window.setTimeout(function () {
          var popup = window.open(buildLeadWhatsappUrl(storedLead), '_blank', 'noopener,noreferrer');
          if (popup) popup.opener = null;
        }, 250);
        try { window.sessionStorage.removeItem(leadStorageKey); } catch (e) {}
        if (window.location.hash !== '#contato') window.location.hash = '#contato';
        if (window.history && window.history.replaceState) window.history.replaceState(null, '', window.location.pathname + '#contato');
      } else if (mailStatus === 'erro' && errorBox) {
        errorBox.classList.remove('hidden');
        if (feedbackEl) { feedbackEl.classList.remove('hidden'); feedbackEl.className = 'contact-feedback-alert is-error'; feedbackEl.textContent = 'Nao conseguimos enviar seus dados agora. Revise o WhatsApp informado ou use o atalho direto para falar com o comercial.'; }
        try { window.sessionStorage.removeItem(leadStorageKey); } catch (e) {}
        if (window.location.hash !== '#contato') window.location.hash = '#contato';
        if (window.history && window.history.replaceState) window.history.replaceState(null, '', window.location.pathname + '#contato');
      }

      if (leadForm) {
        if (mailStatus !== 'ok') {
          var prev = getStoredLead();
          if (prev) {
            if (leadNameInput && !leadNameInput.value) leadNameInput.value = prev.nome || '';
            if (leadWhatsappInput && !leadWhatsappInput.value) leadWhatsappInput.value = prev.whatsapp || '';
          }
        }
        leadForm.addEventListener('submit', function () {
          var payload = { nome: leadNameInput ? leadNameInput.value.trim() : '', whatsapp: leadWhatsappInput ? leadWhatsappInput.value.trim() : '' };
          try { window.sessionStorage.setItem(leadStorageKey, JSON.stringify(payload)); } catch (e) {}
        });
      }
    });

// Extracted from index.php block 11.
document.addEventListener('DOMContentLoaded', function () {
      if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
        lucide.createIcons();
      }

      (function initCodeRushChat() {
        var backdrop = document.getElementById('chat-backdrop');
        var panel = document.getElementById('chat-panel');
        var launcher = document.getElementById('chat-launcher');
        var closeBtn = document.getElementById('chat-close');
        var messagesEl = document.getElementById('chat-messages');
        var quickEl = document.getElementById('chat-quick');
        var form = document.getElementById('chat-form');
        var input = document.getElementById('chat-input');
        var iconMsg = document.getElementById('chat-launcher-icon-msg');
        var iconX = document.getElementById('chat-launcher-icon-x');
        var labelOpen = document.getElementById('chat-launcher-label-open');
        var labelClose = document.getElementById('chat-launcher-label-close');
        if (!backdrop || !panel || !launcher || !messagesEl || !quickEl || !form || !input) return;

        var chatOpen = false;
        var initialized = false;
        var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        function icons() {
          if (typeof lucide !== 'undefined' && lucide.createIcons) lucide.createIcons();
        }

        function scrollChatBottom() {
          messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function appendBot(text) {
          var div = document.createElement('div');
          div.className = 'chat-msg chat-msg--bot';
          div.textContent = text;
          messagesEl.appendChild(div);
          scrollChatBottom();
        }

        function appendUser(text) {
          var div = document.createElement('div');
          div.className = 'chat-msg chat-msg--user';
          div.textContent = text;
          messagesEl.appendChild(div);
          scrollChatBottom();
        }

        function appendBotWithCta(text, cta) {
          var wrap = document.createElement('div');
          wrap.className = 'chat-msg chat-msg--bot';
          var p = document.createElement('p');
          p.className = 'mb-2 last:mb-0';
          p.textContent = text;
          wrap.appendChild(p);
          if (cta && cta.href) {
            var a = document.createElement('a');
            a.href = cta.href;
            a.className = 'inline-flex items-center gap-1 font-semibold text-blue-400 underline decoration-blue-400/40 underline-offset-2 hover:text-blue-300';
            a.textContent = cta.label || 'Abrir';
            if (cta.external) {
              a.target = '_blank';
              a.rel = 'noopener noreferrer';
            }
            wrap.appendChild(a);
          }
          messagesEl.appendChild(wrap);
          scrollChatBottom();
        }

        function showTyping(then) {
          var row = document.createElement('div');
          row.className = 'chat-msg chat-msg--bot chat-typing';
          row.setAttribute('aria-hidden', 'true');
          row.innerHTML = '<span></span><span></span><span></span>';
          messagesEl.appendChild(row);
          scrollChatBottom();
          var ms = reduceMotion ? 80 : 520;
          setTimeout(function () {
            if (row.parentNode) row.parentNode.removeChild(row);
            then();
          }, ms);
        }

        function clearQuick() {
          quickEl.innerHTML = '';
        }

        function renderQuick(items) {
          clearQuick();
          items.forEach(function (item) {
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'chat-chip';
            b.textContent = item.label;
            b.addEventListener('click', function () {
              item.onClick();
            });
            quickEl.appendChild(b);
          });
        }

        function setOpen(open) {
          chatOpen = open;
          backdrop.classList.toggle('is-open', open);
          panel.classList.toggle('is-open', open);
          backdrop.setAttribute('aria-hidden', open ? 'false' : 'true');
          panel.setAttribute('aria-hidden', open ? 'false' : 'true');
          launcher.setAttribute('aria-expanded', open ? 'true' : 'false');
          launcher.classList.toggle('is-open', open);
          document.body.classList.toggle('overflow-hidden', open);
          if (iconMsg && iconX) {
            iconMsg.classList.toggle('hidden', open);
            iconX.classList.toggle('hidden', !open);
          }
          if (labelOpen && labelClose) {
            labelOpen.classList.toggle('hidden', open);
            labelClose.classList.toggle('hidden', !open);
          }
          icons();
          if (open) {
            if (!initialized) {
              initialized = true;
              appendBot(
                'Olá! Sou o assistente virtual do ecossistema CodeRush. Posso direcionar você para a linha certa — escolha um tema ou escreva sua dúvida abaixo.'
              );
              renderQuick([
                {
                  label: 'Vendas diretas / MMN',
                  onClick: function () {
                    topicFlow('mmn', 'Quero falar sobre vendas diretas / MMN');
                  },
                },
                {
                  label: 'Software & IA',
                  onClick: function () {
                    topicFlow('dev', 'Preciso de software sob medida / IA');
                  },
                },
                {
                  label: 'Automação & n8n',
                  onClick: function () {
                    topicFlow('auto', 'Quero automação com IA / n8n');
                  },
                },
                {
                  label: 'Falar com humano',
                  onClick: function () {
                    topicFlow('human', 'Quero falar com um especialista');
                  },
                },
                {
                  label: 'Ver empresas do grupo',
                  onClick: function () {
                    appendUser('Ver empresas do grupo');
                    showTyping(function () {
                      appendBotWithCta(
                        'Aqui estão as marcas do ecossistema. Cada uma tem site próprio — clique em Visitar no portfólio.',
                        { href: '#empresas', label: 'Ir para Empresas', external: false }
                      );
                      renderQuick([ctaFormChip()]);
                    });
                    window.location.hash = '#empresas';
                  },
                },
              ]);
            }
            setTimeout(function () {
              input.focus();
            }, reduceMotion ? 0 : 280);
          }
        }

        function ctaFormChip() {
          return {
            label: 'Abrir formulário de contato',
            onClick: function () {
              window.location.hash = '#contato';
              setOpen(false);
              var el = document.getElementById('contato');
              if (el) el.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth' });
            },
          };
        }

        function topicFlow(key, userLine) {
          clearQuick();
          appendUser(userLine);
          var copy = {
            mmn: {
              t:
                'Para plataformas de vendas diretas e MMN, o **Sistema Venda Direta** oferece comissionamento, lojas e backoffice em escala. O próximo passo ideal é conversar com o time pelo formulário.',
              link: { href: '/sistemavendadireta/', label: 'Visitar sistemavendadireta.com.br', external: false },
            },
            dev: {
              t:
                'Para software sob medida, IA e integrações, a **Codafacil.dev** trabalha com Laravel, entregas ágeis e stack moderna. Deixe seu briefing no formulário para retorno técnico.',
              link: { href: '/codafacil/', label: 'Ver Codafacil.dev', external: false },
            },
            auto: {
              t:
                'Para automação com **n8n**, agentes e IA aplicada ao negócio, o **FluxoInteligente IA** desenha fluxos e integrações.',
              link: { href: '/fluxointeligenteia/', label: 'Ver FluxoInteligente IA', external: false },
            },
            human: {
              t:
                'Perfeito. O melhor canal para um retorno personalizado é o **formulário de contato** com nome, e-mail e interesse — nossa equipe responde sem fila robótica.',
              link: null,
            },
          };
          var pack = copy[key] || copy.human;
          showTyping(function () {
            var text = pack.t.replace(/\*\*(.+?)\*\*/g, '$1');
            appendBot(text);
            if (pack.link) {
              var a = document.createElement('div');
              a.className = 'chat-msg chat-msg--bot mt-2';
              var link = document.createElement('a');
              link.href = pack.link.href;
              link.className = 'font-semibold text-blue-400 underline decoration-blue-400/40 underline-offset-2 hover:text-blue-300';
              link.textContent = pack.link.label;
              if (pack.link.external) {
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
              }
              a.appendChild(link);
              messagesEl.appendChild(a);
              scrollChatBottom();
            }
            renderQuick([ctaFormChip(), { label: 'Outra dúvida', onClick: function () { renderQuick(resetMainMenu()); } }]);
          });
        }

        function resetMainMenu() {
          return [
            {
              label: 'Vendas diretas / MMN',
              onClick: function () {
                topicFlow('mmn', 'Quero falar sobre vendas diretas / MMN');
              },
            },
            {
              label: 'Software & IA',
              onClick: function () {
                topicFlow('dev', 'Preciso de software sob medida / IA');
              },
            },
            {
              label: 'Automação & n8n',
              onClick: function () {
                topicFlow('auto', 'Quero automação com IA / n8n');
              },
            },
            {
              label: 'Falar com humano',
              onClick: function () {
                topicFlow('human', 'Quero falar com um especialista');
              },
            },
          ];
        }

        function handleFreeText(raw) {
          var t = raw.trim();
          if (t.length < 2) return;
          appendUser(t);
          input.value = '';
          var lower = t.toLowerCase();
          showTyping(function () {
            if (/orçamento|orcamento|preço|preco|proposta|contratar|projeto/i.test(lower)) {
              appendBot(
                'Entendi que você busca uma proposta ou orçamento. O formulário de contato reúne escopo, prazo e canal — nossa equipe retorna com o próximo passo.'
              );
            } else if (/prazo|urgente|quando/i.test(lower)) {
              appendBot(
                'Prazos variam por escopo. Informe datas desejadas e complexidade no formulário — assim conseguimos alinhar expectativa e prioridade.'
              );
            } else {
              appendBot(
                'Obrigado pela mensagem! Para direcionar ao time certo, use o formulário abaixo com nome, e-mail e tipo de projeto — resposta humana e objetiva.'
              );
            }
            clearQuick();
            renderQuick([ctaFormChip(), { label: 'Ver FAQ', onClick: function () { window.location.hash = '#faq'; setOpen(false); } }]);
          });
        }

        launcher.addEventListener('click', function () {
          setOpen(!chatOpen);
        });
        if (closeBtn) {
          closeBtn.addEventListener('click', function () {
            setOpen(false);
          });
        }
        backdrop.addEventListener('click', function () {
          setOpen(false);
        });
        form.addEventListener('submit', function (e) {
          e.preventDefault();
          handleFreeText(input.value);
        });

        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape' && chatOpen) {
            setOpen(false);
          }
        });

        input.addEventListener('input', function () {
          input.style.height = 'auto';
          input.style.height = Math.min(input.scrollHeight, 120) + 'px';
        });
      })();
    });
