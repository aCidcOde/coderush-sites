// Extracted from codafacil/index.php block 1.
document.getElementById("year").textContent = new Date().getFullYear();

// Extracted from codafacil/index.php block 2.
document.addEventListener("DOMContentLoaded", function () {
      var leadForm = document.getElementById("contact-lead-form");
      var leadNameInput = document.getElementById("contact-nome");
      var leadWhatsappInput = document.getElementById("contact-whatsapp");
      var successBox = document.getElementById("contact-success-box");
      var errorBox = document.getElementById("contact-error-box");
      var successWhatsappLink = document.getElementById("contact-success-whatsapp-link");
      var feedbackEl = document.getElementById("contact-feedback");
      var leadStorageKey = "codafacil-contact-lead";
      var urlParams = new URLSearchParams(window.location.search);
      var mailStatus = urlParams.get("mail");
      var defaultWaMsg = "Ola, vim pelo site da Codafacil e quero um orcamento para meu projeto.";
      var phone = "5511994566726";

      function getStoredLead() {
        try { var r = window.sessionStorage.getItem(leadStorageKey); return r ? JSON.parse(r) : null; } catch (e) { return null; }
      }

      function buildLeadWhatsappUrl(lead) {
        var s = lead || {};
        var tpl = leadForm ? (leadForm.getAttribute("data-whatsapp-message-template") || defaultWaMsg) : defaultWaMsg;
        var p = leadForm ? (leadForm.getAttribute("data-whatsapp-phone") || phone) : phone;
        if (!s.nome && !s.whatsapp) return "https://wa.me/" + p + "?text=" + encodeURIComponent(defaultWaMsg);
        var msg = tpl.replace("{nome}", (s.nome || "Nao informado").trim()).replace("{whatsapp}", (s.whatsapp || "Nao informado").trim());
        return "https://wa.me/" + p + "?text=" + encodeURIComponent(msg);
      }

      if (mailStatus === "ok" && successBox) {
        successBox.classList.remove("hidden");
        if (feedbackEl) { feedbackEl.classList.remove("hidden"); feedbackEl.className = "contact-feedback-alert is-success"; feedbackEl.textContent = "Recebemos seus dados. Vamos abrir o WhatsApp com uma mensagem pronta para agilizar seu atendimento."; }
        var storedLead = getStoredLead();
        if (successWhatsappLink) successWhatsappLink.href = buildLeadWhatsappUrl(storedLead);
        window.setTimeout(function () {
          var popup = window.open(buildLeadWhatsappUrl(storedLead), "_blank", "noopener,noreferrer");
          if (popup) popup.opener = null;
        }, 250);
        try { window.sessionStorage.removeItem(leadStorageKey); } catch (e) {}
        if (window.location.hash !== "#contato") window.location.hash = "#contato";
        if (window.history && window.history.replaceState) window.history.replaceState(null, "", window.location.pathname + "#contato");
      } else if (mailStatus === "erro" && errorBox) {
        errorBox.classList.remove("hidden");
        if (feedbackEl) { feedbackEl.classList.remove("hidden"); feedbackEl.className = "contact-feedback-alert is-error"; feedbackEl.textContent = "Nao conseguimos enviar seus dados agora. Revise o WhatsApp informado ou use o atalho direto para falar com o comercial."; }
        try { window.sessionStorage.removeItem(leadStorageKey); } catch (e) {}
        if (window.location.hash !== "#contato") window.location.hash = "#contato";
        if (window.history && window.history.replaceState) window.history.replaceState(null, "", window.location.pathname + "#contato");
      }

      if (leadForm) {
        if (mailStatus !== "ok") {
          var prev = getStoredLead();
          if (prev) {
            if (leadNameInput && !leadNameInput.value) leadNameInput.value = prev.nome || "";
            if (leadWhatsappInput && !leadWhatsappInput.value) leadWhatsappInput.value = prev.whatsapp || "";
          }
        }
        leadForm.addEventListener("submit", function () {
          var payload = { nome: leadNameInput ? leadNameInput.value.trim() : "", whatsapp: leadWhatsappInput ? leadWhatsappInput.value.trim() : "" };
          try { window.sessionStorage.setItem(leadStorageKey, JSON.stringify(payload)); } catch (e) {}
        });
      }
    });
