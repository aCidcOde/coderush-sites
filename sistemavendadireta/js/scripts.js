// Extracted from sistemavendadireta/index.php block 1.

// --- Atribuicao de lead (client_id do GA, gclid e UTMs) -------------------
// Persiste os parametros de campanha na sessao (sobrevivem a navegacao entre
// paginas) e preenche os campos ocultos do formulario no submit. Base do
// funil lead -> venda (purchase offline via Measurement Protocol).
var SVD_ATTR_KEY = "svd-attribution";

(function captureAttribution() {
  try {
    var params = new URLSearchParams(window.location.search);
    var keys = ["gclid", "utm_source", "utm_medium", "utm_campaign", "utm_content"];
    var stored = {};
    try {
      stored = JSON.parse(window.sessionStorage.getItem(SVD_ATTR_KEY) || "{}");
    } catch (e) {}
    var changed = false;
    keys.forEach(function (k) {
      var v = params.get(k);
      if (v) {
        stored[k] = v;
        changed = true;
      }
    });
    if (changed) {
      window.sessionStorage.setItem(SVD_ATTR_KEY, JSON.stringify(stored));
    }
  } catch (e) {}
})();

function svdGaClientId() {
  var match = document.cookie.match(/(?:^|;\s*)_ga=GA\d+\.\d+\.(\d+\.\d+)/);
  return match ? match[1] : "";
}

function fillLeadTrackingFields(form) {
  var stored = {};
  try {
    stored = JSON.parse(window.sessionStorage.getItem(SVD_ATTR_KEY) || "{}");
  } catch (e) {}

  function setField(name, value) {
    if (!value) return;
    var input = form.querySelector('input[name="' + name + '"]');
    if (!input) {
      input = document.createElement("input");
      input.type = "hidden";
      input.name = name;
      form.appendChild(input);
    }
    input.value = value;
  }

  setField("ga_client_id", svdGaClientId());
  setField("gclid", stored.gclid || "");
  setField("utm_source", stored.utm_source || "");
  setField("utm_medium", stored.utm_medium || "");
  setField("utm_campaign", stored.utm_campaign || "");
  setField("utm_content", stored.utm_content || "");
  setField("sim_faturamento", window.__svdSimBucket || "");
  setField("page_url", window.location.href.split("#")[0]);
}

document.addEventListener("DOMContentLoaded", function () {
      var containers = document.querySelectorAll(".lottie-box[data-lottie-src]");
      var leadForm = document.getElementById("contact-lead-form");
      var leadNameInput = document.getElementById("contact-nome");
      var leadWhatsappInput = document.getElementById("contact-whatsapp");
      var successWhatsappLink = document.getElementById("contact-success-whatsapp-link");
      var leadStorageKey = "svd-contact-lead";
      var urlParams = new URLSearchParams(window.location.search);
      var mailStatus = urlParams.get("mail");

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

      function buildWhatsappUrl(lead) {
        var fallbackMessage = "Ola, quero um orcamento e acesso a demonstracao do Sistema Venda Direta.";
        var phone = leadForm ? (leadForm.getAttribute("data-whatsapp-phone") || "5511994566726") : "5511994566726";
        var template = leadForm ? (leadForm.getAttribute("data-whatsapp-message-template") || fallbackMessage) : fallbackMessage;
        var safeLead = lead || {};
        if (!safeLead.nome && !safeLead.whatsapp) {
          return "https://wa.me/" + phone + "?text=" + encodeURIComponent(fallbackMessage);
        }
        var message = template
          .replace("{nome}", (safeLead.nome || "Nao informado").trim())
          .replace("{whatsapp}", (safeLead.whatsapp || "Nao informado").trim());

        return "https://wa.me/" + phone + "?text=" + encodeURIComponent(message);
      }

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

          fillLeadTrackingFields(leadForm);
        });
      }

      if (successWhatsappLink) {
        var storedLead = getStoredLead();
        successWhatsappLink.href = buildWhatsappUrl(storedLead);

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

      if (!containers.length || !window.lottie) {
        containers = [];
      }

      var reduceMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
      var isSmallScreen = window.matchMedia && window.matchMedia("(max-width: 767px)").matches;
      var startAnimation = function (container) {
        if (container.dataset.lottieLoaded === "1") {
          return;
        }

        var src = container.getAttribute("data-lottie-src");
        if (!src) {
          return;
        }

        if (container.getAttribute("data-lottie-mobile") === "false" && isSmallScreen) {
          return;
        }

        window.lottie.loadAnimation({
          container: container,
          renderer: "svg",
          loop: !reduceMotion,
          autoplay: !reduceMotion,
          path: src
        });

        container.dataset.lottieLoaded = "1";
      };

      if (!containers.length) {
      } else if (!("IntersectionObserver" in window)) {
        containers.forEach(startAnimation);
      } else {
        var observer = new IntersectionObserver(function (entries, currentObserver) {
          entries.forEach(function (entry) {
            if (!entry.isIntersecting) {
              return;
            }
            startAnimation(entry.target);
            currentObserver.unobserve(entry.target);
          });
        }, { rootMargin: "120px 0px" });

        containers.forEach(function (container) {
          observer.observe(container);
        });
      }
    });
