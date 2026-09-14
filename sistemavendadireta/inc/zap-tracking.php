<?php
/*
[Modulo Rastreamento de WhatsApp SVD]
@Author: André Gomes ( @acidcode )
@since 2026-09-14

Snippet unico de rastreamento do clique de WhatsApp. Antes disto o script vivia
copiado dentro da /oferta/ e da /sistema-mmn/, e as outras cinco paginas com
link de zap — inclusive a HOME e a /cases/ — nao tinham nada.

O tamanho do buraco: a home e a maior porta de entrada organica do site e a
/cases/ e destino de sitelink dos anuncios. Todo clique de WhatsApp vindo de la
era invisivel: sem evento no GA4, sem linha no banco, sem gclid. Nao da pra saber
que o organico converte se a conversao do organico nunca foi contada.

Uso, antes do </body>:
    <?php $zapOrigem = 'home'; include __DIR__ . '/inc/zap-tracking.php'; ?>

$zapOrigem identifica a pagina no relatorio (coluna `origem` do leads.sqlite).
Sem ele, cai em 'site'.

DUAS COISAS QUE PARECEM DETALHE E NAO SAO:

1. O codigo [ref XXXXX] vai na mensagem do wa.me E no banco. E o unico jeito de
   casar uma conversa de WhatsApp com a campanha que a originou — sem ele o lead
   chega dizendo "vi voces no Google" e a atribuicao morre ali.

2. A page_url e sempre enviada. O JS le a atribuicao do sessionStorage, mas em
   WebView (link aberto dentro de app) o storage e isolado e volta vazio — foi
   assim que a venda de R$ 3.500 de 26/08 gravou atribuicao em branco vindo de
   clique pago. O zap-lead.php extrai os parametros da page_url quando os campos
   chegam vazios, e isso nao depende de JS, storage nem navegador.
*/

$zapOrigem = isset($zapOrigem) && $zapOrigem !== '' ? $zapOrigem : 'site';
?>
<script>
  (function () {
    var ORIGEM = <?= json_encode($zapOrigem, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    function track(nome, params) {
      if (typeof window.gtag === "function") {
        var dados = params || {};
        // faixa de faturamento escolhida no simulador, quando houver
        if (window.__svdSimBucket) { dados.sim_faturamento = window.__svdSimBucket; }
        window.gtag("event", nome, dados);
      }
    }

    function zapRef() {
      try {
        var r = window.sessionStorage.getItem("svd-zap-ref");
        if (!r) {
          r = Math.random().toString(36).slice(2, 7).toUpperCase().replace(/[^A-Z0-9]/g, "X");
          while (r.length < 5) { r += "X"; }
          window.sessionStorage.setItem("svd-zap-ref", r);
        }
        return r;
      } catch (e) { return "AAAAA"; }
    }

    document.addEventListener("click", function (event) {
      var link = event.target.closest && event.target.closest('a[href*="wa.me"]');
      if (!link) { return; }
      track("whatsapp_click", { page: ORIGEM });
      var ref = zapRef();
      // embute o codigo de referencia na mensagem pre-preenchida do WhatsApp
      if (link.href.indexOf("text=") !== -1 && link.href.indexOf("%5Bref") === -1) {
        link.href += encodeURIComponent(" [ref " + ref + "]");
      }
      try {
        var guardado = {};
        try { guardado = JSON.parse(window.sessionStorage.getItem("svd-attribution") || "{}"); } catch (e) {}
        var daUrl = new URLSearchParams(window.location.search);
        var data = new FormData();
        data.append("ref", ref);
        data.append("origem", ORIGEM);
        data.append("ga_client_id", (document.cookie.match(/(?:^|;\s*)_ga=GA\d+\.\d+\.(\d+\.\d+)/) || [])[1] || "");
        ["gclid", "utm_source", "utm_medium", "utm_campaign", "utm_content"].forEach(function (k) {
          var v = guardado[k] || daUrl.get(k);
          if (v) { data.append(k, v); }
        });
        if (window.__svdSimBucket) { data.append("sim_faturamento", window.__svdSimBucket); }
        // rede de seguranca do WebView: o servidor extrai os parametros daqui
        data.append("page_url", window.location.href.split("#")[0]);
        navigator.sendBeacon("/zap-lead.php", data);
      } catch (e) {}
    });

    var form = document.getElementById("contact-lead-form");
    if (form) {
      form.addEventListener("submit", function () { track("generate_lead", { page: ORIGEM }); });
    }
  })();
</script>
