  <div class="flex flex-col items-end gap-2" style="position:fixed; right:16px; bottom:16px; z-index:90;">
    <div id="wa-chatbot-panel" class="hidden rounded-2xl border border-white/20 bg-brand/95 p-4 text-white backdrop-blur" style="width:300px; max-width:calc(100vw - 32px); box-shadow:0 16px 36px rgba(0,0,0,0.35);">
      <div class="flex items-start justify-between gap-2">
        <div>
          <p class="text-sm font-semibold">Atendimento no WhatsApp</p>
          <p class="mt-1 text-xs text-white/80">Escolha uma opção rápida para iniciar.</p>
        </div>
        <button type="button" id="wa-chatbot-close" class="rounded-full border border-white/30 px-2 py-0.5 text-xs hover:bg-white/10" aria-label="Fechar chatbot">x</button>
      </div>

      <div class="mt-3 grid gap-2">
        <button type="button" class="wa-quick-msg rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-left text-xs hover:bg-white/15" data-msg="Olá, quero um orçamento para o Sistema Venda Direta.">Quero um orçamento</button>
        <button type="button" class="wa-quick-msg rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-left text-xs hover:bg-white/15" data-msg="Olá, quero ver uma demonstração do Sistema Venda Direta.">Quero ver uma demonstração</button>
        <button type="button" class="wa-quick-msg rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-left text-xs hover:bg-white/15" data-msg="Olá, preciso entender como funciona a integração com ERP e pagamentos.">Quero falar sobre integrações</button>
      </div>

      <a id="wa-chatbot-link" href="https://wa.me/5511994566726?text=Ol%C3%A1%2C%20quero%20um%20or%C3%A7amento%20e%20acesso%20%C3%A0%20demonstra%C3%A7%C3%A3o%20do%20Sistema%20Venda%20Direta." target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex w-full items-center justify-center rounded-full bg-[#25D366] px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#22c55e]">
        Continuar no WhatsApp
      </a>
    </div>

    <button type="button" id="wa-float-toggle" aria-label="Abrir atendimento no WhatsApp" class="inline-flex items-center justify-center rounded-full text-white transition hover:scale-[1.03]" style="width:56px; height:56px; background:#25D366; box-shadow:0 10px 24px rgba(0,0,0,0.35); border:2px solid rgba(255,255,255,0.3); animation:waPulse 2.2s ease-in-out infinite;">
      <span class="inline-flex items-center justify-center rounded-full bg-white/15" style="width:32px; height:32px;">
        <svg viewBox="0 0 32 32" aria-hidden="true" style="width:20px; height:20px; fill:currentColor;">
          <path d="M19.11 17.36c-.27-.14-1.6-.79-1.85-.88-.25-.09-.43-.14-.61.14-.18.27-.7.88-.86 1.07-.16.18-.32.2-.59.07-.27-.14-1.15-.42-2.19-1.33-.81-.72-1.36-1.6-1.52-1.87-.16-.27-.02-.42.12-.55.12-.12.27-.32.41-.48.14-.16.18-.27.27-.46.09-.18.05-.34-.02-.48-.07-.14-.61-1.48-.84-2.03-.22-.53-.45-.46-.61-.47h-.52c-.18 0-.48.07-.73.34-.25.27-.95.93-.95 2.27s.98 2.63 1.11 2.81c.14.18 1.92 2.94 4.65 4.12.65.28 1.16.45 1.55.58.65.21 1.24.18 1.71.11.52-.08 1.6-.65 1.83-1.28.23-.63.23-1.17.16-1.28-.07-.11-.25-.18-.52-.32z"></path>
          <path d="M16.02 3.2c-7.06 0-12.8 5.74-12.8 12.8 0 2.26.6 4.47 1.73 6.41L3.2 28.8l6.57-1.72a12.73 12.73 0 0 0 6.24 1.62h.01c7.06 0 12.8-5.74 12.8-12.8 0-3.42-1.33-6.63-3.75-9.05A12.71 12.71 0 0 0 16.02 3.2zm0 23.2h-.01c-1.92 0-3.8-.52-5.44-1.5l-.39-.23-3.9 1.02 1.04-3.8-.25-.39a10.41 10.41 0 0 1-1.6-5.59c0-5.75 4.69-10.43 10.45-10.43 2.79 0 5.41 1.08 7.38 3.06a10.37 10.37 0 0 1 3.05 7.38c0 5.75-4.69 10.44-10.43 10.44z"></path>
        </svg>
      </span>
    </button>

    <button type="button" id="scroll-top-btn" aria-label="Voltar ao topo" class="hidden items-center justify-center rounded-full text-white text-lg leading-none transition" style="width:44px; height:44px; border:1px solid rgba(255,255,255,0.35); background:rgba(0,74,173,0.85); box-shadow:0 8px 20px rgba(0,0,0,0.25);">
      ↑
    </button>
  </div>

  <style>
    @keyframes waPulse {
      0% { transform: scale(1); box-shadow: 0 10px 24px rgba(0,0,0,0.35), 0 0 0 0 rgba(37, 211, 102, 0.35); }
      70% { transform: scale(1); box-shadow: 0 10px 24px rgba(0,0,0,0.35), 0 0 0 12px rgba(37, 211, 102, 0); }
      100% { transform: scale(1); box-shadow: 0 10px 24px rgba(0,0,0,0.35), 0 0 0 0 rgba(37, 211, 102, 0); }
    }
  </style>

  <script src="js/lottie.min.js" defer></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      var siteHeader = document.getElementById("site-header");
      var prefersReducedMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
      var leadForm = document.getElementById("contact-lead-form");
      var leadNameInput = document.getElementById("contact-nome");
      var leadWhatsappInput = document.getElementById("contact-whatsapp");
      var successWhatsappLink = document.getElementById("contact-success-whatsapp-link");
      var leadStorageKey = "svd-contact-lead";
      var urlParams = new URLSearchParams(window.location.search);
      var mailStatus = urlParams.get("mail");
      var waPanel = document.getElementById("wa-chatbot-panel");
      var waToggle = document.getElementById("wa-float-toggle");
      var waClose = document.getElementById("wa-chatbot-close");
      var waLink = document.getElementById("wa-chatbot-link");
      var waQuickButtons = document.querySelectorAll(".wa-quick-msg[data-msg]");
      var scrollTopBtn = document.getElementById("scroll-top-btn");
      var defaultWaMsg = "Olá, quero um orçamento e acesso à demonstração do Sistema Venda Direta.";
      var phone = "5511994566726";

      function normalizePhone(value) {
        return (value || "").replace(/\D+/g, "");
      }

      function getStoredLead() {
        try {
          var rawLead = window.sessionStorage.getItem(leadStorageKey);
          return rawLead ? JSON.parse(rawLead) : null;
        } catch (error) {
          return null;
        }
      }

      function buildLeadWhatsappUrl(lead) {
        var safeLead = lead || {};
        var messageTemplate = leadForm ? (leadForm.getAttribute("data-whatsapp-message-template") || defaultWaMsg) : defaultWaMsg;
        var customPhone = leadForm ? (leadForm.getAttribute("data-whatsapp-phone") || phone) : phone;
        if (!safeLead.nome && !safeLead.whatsapp) {
          return "https://wa.me/" + customPhone + "?text=" + encodeURIComponent(defaultWaMsg);
        }
        var message = messageTemplate
          .replace("{nome}", (safeLead.nome || "Nao informado").trim())
          .replace("{whatsapp}", (safeLead.whatsapp || "Nao informado").trim());

        return "https://wa.me/" + customPhone + "?text=" + encodeURIComponent(message);
      }

      function updateWaLink(message) {
        if (!waLink) return;
        waLink.href = "https://wa.me/" + phone + "?text=" + encodeURIComponent(message || defaultWaMsg);
      }

      updateWaLink(defaultWaMsg);

      if (leadForm) {
        if (mailStatus !== "ok") {
          var previousLead = getStoredLead();
          if (previousLead) {
            if (leadNameInput && !leadNameInput.value) {
              leadNameInput.value = previousLead.nome || "";
            }
            if (leadWhatsappInput && !leadWhatsappInput.value) {
              leadWhatsappInput.value = previousLead.whatsapp || "";
            }
          }
        }

        leadForm.addEventListener("submit", function () {
          var payload = {
            nome: leadNameInput ? leadNameInput.value.trim() : "",
            whatsapp: leadWhatsappInput ? leadWhatsappInput.value.trim() : "",
            whatsappDigits: normalizePhone(leadWhatsappInput ? leadWhatsappInput.value : "")
          };

          try {
            window.sessionStorage.setItem(leadStorageKey, JSON.stringify(payload));
          } catch (error) {
          }
        });
      }

      if (successWhatsappLink) {
        var storedLead = getStoredLead();
        successWhatsappLink.href = buildLeadWhatsappUrl(storedLead);

        if (mailStatus === "ok") {
          window.setTimeout(function () {
            var popup = window.open(successWhatsappLink.href, "_blank", "noopener,noreferrer");
            if (popup) {
              popup.opener = null;
            }
          }, 250);

          try {
            window.sessionStorage.removeItem(leadStorageKey);
          } catch (error) {
          }
        }
      } else if (mailStatus === "erro") {
        try {
          window.sessionStorage.removeItem(leadStorageKey);
        } catch (error) {
        }
      }

      if (waToggle && waPanel) {
        waToggle.addEventListener("mouseenter", function () {
          waToggle.style.transform = "translateY(-2px) scale(1.04)";
          waToggle.style.boxShadow = "0 14px 28px rgba(0,0,0,0.35)";
        });
        waToggle.addEventListener("mouseleave", function () {
          waToggle.style.transform = "";
          waToggle.style.boxShadow = "0 10px 24px rgba(0,0,0,0.35)";
        });
        waToggle.addEventListener("mousedown", function () {
          waToggle.style.transform = "translateY(0) scale(0.96)";
        });
        waToggle.addEventListener("mouseup", function () {
          waToggle.style.transform = "translateY(-2px) scale(1.04)";
        });
        waToggle.addEventListener("click", function () {
          waPanel.classList.toggle("hidden");
          if (!waPanel.classList.contains("hidden")) {
            waToggle.style.animation = "none";
          } else {
            waToggle.style.animation = "waPulse 2.2s ease-in-out infinite";
          }
        });
      }

      if (waClose && waPanel) {
        waClose.addEventListener("click", function () {
          waPanel.classList.add("hidden");
        });
      }

      waQuickButtons.forEach(function (btn) {
        btn.addEventListener("click", function () {
          var msg = btn.getAttribute("data-msg") || defaultWaMsg;
          updateWaLink(msg);
        });
      });

      function updateScrollTopButton() {
        if (!scrollTopBtn) return;
        if (window.scrollY > 420) {
          scrollTopBtn.classList.remove("hidden");
          scrollTopBtn.classList.add("inline-flex");
        } else {
          scrollTopBtn.classList.add("hidden");
          scrollTopBtn.classList.remove("inline-flex");
        }
      }

      updateScrollTopButton();
      window.addEventListener("scroll", updateScrollTopButton, { passive: true });

      if (scrollTopBtn) {
        scrollTopBtn.addEventListener("mouseenter", function () {
          scrollTopBtn.style.transform = "translateY(-2px) scale(1.05)";
          scrollTopBtn.style.boxShadow = "0 12px 24px rgba(0,0,0,0.3)";
          scrollTopBtn.style.background = "rgba(0,74,173,1)";
        });
        scrollTopBtn.addEventListener("mouseleave", function () {
          scrollTopBtn.style.transform = "";
          scrollTopBtn.style.boxShadow = "0 8px 20px rgba(0,0,0,0.25)";
          scrollTopBtn.style.background = "rgba(0,74,173,0.85)";
        });
        scrollTopBtn.addEventListener("mousedown", function () {
          scrollTopBtn.style.transform = "scale(0.95)";
        });
        scrollTopBtn.addEventListener("mouseup", function () {
          scrollTopBtn.style.transform = "translateY(-2px) scale(1.05)";
        });
        scrollTopBtn.addEventListener("click", function () {
          window.scrollTo({ top: 0, behavior: prefersReducedMotion ? "auto" : "smooth" });
        });
      }

      function updateHeaderState() {
        if (!siteHeader) {
          return;
        }
        if (window.scrollY > 36) {
          siteHeader.classList.add("is-compact");
        } else {
          siteHeader.classList.remove("is-compact");
        }
      }
      updateHeaderState();
      window.addEventListener("scroll", updateHeaderState, { passive: true });

      if (!prefersReducedMotion) {
        var anchorLinks = document.querySelectorAll('a[href^="#"]');
        anchorLinks.forEach(function (link) {
          link.addEventListener("click", function (event) {
            var href = link.getAttribute("href") || "";
            if (href.length < 2) return;
            var target = document.querySelector(href);
            if (!target) return;
            event.preventDefault();
            target.scrollIntoView({ behavior: "smooth", block: "start" });
          });
        });
      }

      var sliderTrack = document.getElementById("hero-slider-track");
      var sliderSlides = sliderTrack ? sliderTrack.querySelectorAll(".hero-slide") : [];
      var sliderDots = document.querySelectorAll(".hero-dot[data-slide-to]");
      var currentSlide = 0;
      var totalSlides = sliderSlides.length;
      var activeSlideAnimationFrame = null;
      var weeklyData = {
        w1: [48, 64, 40, 80, 56, 96, 72],
        w2: [52, 58, 46, 74, 61, 88, 79],
        w3: [44, 69, 53, 78, 66, 90, 83],
        w4: [57, 62, 49, 84, 70, 94, 86]
      };

      function formatKpiValue(value, format) {
        if (format === "int-plus") return "+" + Math.round(value);
        if (format === "percent-comma") return value.toFixed(1).replace(".", ",") + "%";
        if (format === "percent-dot") return value.toFixed(1) + "%";
        if (format === "currency-brl") return "R$ " + Math.round(value);
        if (format === "ms") return Math.round(value) + "ms";
        if (format === "minutes") return Math.round(value) + " min";
        return Math.round(value).toString();
      }

      function animateActiveSlideWidgets(activeSlide) {
        if (!activeSlide) return;
        if (activeSlideAnimationFrame) {
          window.cancelAnimationFrame(activeSlideAnimationFrame);
          activeSlideAnimationFrame = null;
        }

        var progressBars = activeSlide.querySelectorAll("[data-progress-target]");
        progressBars.forEach(function (bar) { bar.style.width = "0%"; });

        var bars = activeSlide.querySelectorAll("[data-bar-target]");
        bars.forEach(function (bar) { bar.style.height = "0px"; });

        activeSlideAnimationFrame = window.requestAnimationFrame(function () {
          progressBars.forEach(function (bar) {
            bar.style.width = (bar.getAttribute("data-progress-target") || "0") + "%";
          });
          bars.forEach(function (bar) {
            bar.style.height = (bar.getAttribute("data-bar-target") || "0") + "px";
          });
        });

        var counters = activeSlide.querySelectorAll("[data-kpi-target]");
        counters.forEach(function (counter) {
          var target = Number(counter.getAttribute("data-kpi-target"));
          var format = counter.getAttribute("data-kpi-format") || "int";
          if (Number.isNaN(target)) return;
          var duration = 700;
          var start = performance.now();

          function tick(now) {
            var progress = Math.min((now - start) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            counter.textContent = formatKpiValue(target * eased, format);
            if (progress < 1) window.requestAnimationFrame(tick);
          }
          window.requestAnimationFrame(tick);
        });
      }

      function animateWeeklyBars(chartElement) {
        if (!chartElement) return;
        var bars = chartElement.querySelectorAll("[data-bar-target]");
        bars.forEach(function (bar) { bar.style.height = "0px"; });
        window.requestAnimationFrame(function () {
          bars.forEach(function (bar) {
            bar.style.height = (bar.getAttribute("data-bar-target") || "0") + "px";
          });
        });
      }

      function initWeeklyCharts() {
        var weeklyCharts = document.querySelectorAll("[data-weekly-chart]");
        weeklyCharts.forEach(function (chartElement) {
          var totalLabel = chartElement.querySelector("[data-weekly-total]");
          var bars = chartElement.querySelectorAll("[data-bar-target]");
          var filters = chartElement.querySelectorAll(".weekly-filter[data-week]");

          function setWeek(weekKey) {
            var selected = weeklyData[weekKey];
            if (!selected) return;
            var total = 0;
            bars.forEach(function (bar, index) {
              var value = selected[index] || 0;
              total += value;
              bar.setAttribute("data-bar-target", String(value));
              bar.setAttribute("title", (bar.getAttribute("data-day-label") || "Dia") + ": " + value + " pedidos");
            });
            if (totalLabel) totalLabel.textContent = String(total);
            filters.forEach(function (filterButton) {
              var active = filterButton.getAttribute("data-week") === weekKey;
              filterButton.setAttribute("aria-pressed", active ? "true" : "false");
              filterButton.classList.toggle("bg-white/15", active);
              filterButton.classList.toggle("bg-transparent", !active);
              filterButton.classList.toggle("text-white/90", active);
              filterButton.classList.toggle("text-white/80", !active);
            });
            animateWeeklyBars(chartElement);
          }

          filters.forEach(function (filterButton) {
            filterButton.addEventListener("click", function () {
              setWeek(filterButton.getAttribute("data-week") || "w1");
            });
          });

          setWeek("w1");
        });
      }

      function updateSlider(nextIndex) {
        if (!sliderTrack || !totalSlides) return;
        currentSlide = (nextIndex + totalSlides) % totalSlides;
        sliderSlides.forEach(function (slide, idx) {
          var isActive = idx === currentSlide;
          slide.style.opacity = isActive ? "1" : "0";
          slide.style.pointerEvents = isActive ? "auto" : "none";
          slide.style.position = isActive ? "relative" : "absolute";
        });
        sliderDots.forEach(function (dot, idx) {
          var isActive = idx === currentSlide;
          dot.setAttribute("aria-selected", isActive ? "true" : "false");
          dot.classList.toggle("bg-white/10", isActive);
          dot.classList.toggle("bg-transparent", !isActive);
        });
        animateActiveSlideWidgets(sliderSlides[currentSlide]);
      }

      if (sliderTrack && totalSlides) {
        updateSlider(0);
        sliderDots.forEach(function (dot) {
          dot.addEventListener("click", function () {
            var nextIndex = Number(dot.getAttribute("data-slide-to"));
            if (!Number.isNaN(nextIndex)) updateSlider(nextIndex);
          });
          dot.addEventListener("keydown", function (event) {
            if (event.key === "Enter" || event.key === " ") {
              event.preventDefault();
              dot.click();
            }
          });
        });
      }

      initWeeklyCharts();

      var featureCards = document.querySelectorAll("[data-feature-card]");
      if (featureCards.length && "IntersectionObserver" in window) {
        var featureObserver = new IntersectionObserver(function (entries, observer) {
          entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            entry.target.style.opacity = "1";
            entry.target.style.transform = "translateY(0)";
            observer.unobserve(entry.target);
          });
        }, { threshold: 0.15 });

        featureCards.forEach(function (card, index) {
          card.style.opacity = "0";
          card.style.transform = "translateY(16px)";
          card.style.transition = "opacity 420ms ease " + (index * 70) + "ms, transform 420ms ease " + (index * 70) + "ms";
          featureObserver.observe(card);
        });
      }

      var caseFilters = document.querySelectorAll(".case-filter[data-case-filter]");
      var caseCards = document.querySelectorAll(".case-card[data-case-category]");
      if (caseFilters.length && caseCards.length) {
        function applyCaseFilter(filterKey) {
          caseCards.forEach(function (card, index) {
            var category = card.getAttribute("data-case-category");
            var show = filterKey === "todos" || category === filterKey;
            if (show) {
              card.style.display = "";
              card.style.opacity = "0";
              card.style.transform = "translateY(10px)";
              window.requestAnimationFrame(function () {
                card.style.transition = "opacity 260ms ease " + (index * 40) + "ms, transform 260ms ease " + (index * 40) + "ms";
                card.style.opacity = "1";
                card.style.transform = "translateY(0)";
              });
            } else {
              card.style.display = "none";
            }
          });
        }

        caseFilters.forEach(function (btn) {
          btn.addEventListener("click", function () {
            var filterKey = btn.getAttribute("data-case-filter") || "todos";
            caseFilters.forEach(function (el) {
              var active = el === btn;
              el.setAttribute("aria-pressed", active ? "true" : "false");
              el.classList.toggle("bg-white/15", active);
              el.classList.toggle("bg-transparent", !active);
              el.classList.toggle("text-white/90", active);
              el.classList.toggle("text-white/80", !active);
            });
            applyCaseFilter(filterKey);
          });
        });
        applyCaseFilter("todos");
      }

      var caseKpis = document.querySelectorAll("[data-case-kpi-target]");
      if (caseKpis.length) {
        caseKpis.forEach(function (kpi) {
          var target = Number(kpi.getAttribute("data-case-kpi-target"));
          if (Number.isNaN(target)) return;
          var prefix = kpi.getAttribute("data-case-kpi-prefix") || "";
          var suffix = kpi.getAttribute("data-case-kpi-suffix") || "";
          var start = performance.now();
          var duration = 850;
          function animate(now) {
            var progress = Math.min((now - start) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            kpi.textContent = prefix + Math.round(target * eased) + suffix;
            if (progress < 1) window.requestAnimationFrame(animate);
          }
          window.requestAnimationFrame(animate);
        });
      }

      var containers = document.querySelectorAll(".lottie-box[data-lottie-src]");
      if (!containers.length || !window.lottie) return;

      var reduceMotion = prefersReducedMotion;
      var isSmallScreen = window.matchMedia && window.matchMedia("(max-width: 767px)").matches;
      var startAnimation = function (container) {
        if (container.dataset.lottieLoaded === "1") return;
        var src = container.getAttribute("data-lottie-src");
        if (!src) return;
        if (container.getAttribute("data-lottie-mobile") === "false" && isSmallScreen) return;
        window.lottie.loadAnimation({
          container: container,
          renderer: "svg",
          loop: !reduceMotion,
          autoplay: !reduceMotion,
          path: src
        });
        container.dataset.lottieLoaded = "1";
      };

      if (!("IntersectionObserver" in window)) {
        containers.forEach(startAnimation);
        return;
      }

      var observer = new IntersectionObserver(function (entries, currentObserver) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          startAnimation(entry.target);
          currentObserver.unobserve(entry.target);
        });
      }, { rootMargin: "120px 0px" });

      containers.forEach(function (container) {
        observer.observe(container);
      });
    });
  </script>
</body>
</html>
