<?php
/*
[Modulo Cadastro antes do WhatsApp SVD]
@Author: André Gomes ( @acidcode )
@since 2026-11-23

POR QUE ISSO EXISTE: ate 23/11/2026 todo botao de WhatsApp abria o app direto. O
painel ganhava uma linha "zap" anonima — so um codigo [ref XXXXX], sem nome, sem
telefone — e, se a pessoa desistisse antes de mandar a mensagem, o contato sumia.
Nao dava pra retornar, nao dava pra marcar o lead e nao dava pra montar publico
de remarketing com quem demonstrou interesse.

Decisao do usuario (23/11): "se ele nao der alguma informacao, e que ele nao tem
interesse". Entao o cadastro e OBRIGATORIO — sem botao de pular. Nome e WhatsApp
sao obrigatorios; e-mail e opcional (melhora o casamento do remarketing, mas e o
campo que mais derruba envio no celular).

O QUE ACONTECE NO ENVIO, nesta ordem e no mesmo clique:
  1. o lead vai pro enviar-contato.php (mesmo banco, mesmo e-mail pra equipe,
     mesmos filtros de spam do formulario) com resposta=json;
  2. GA4 recebe user_data (telefone/e-mail pra conversao otimizada) + generate_lead
     e whatsapp_click — os dois que viram conversao no Ads;
  3. o WhatsApp abre com "Ola! Sou {nome}." na frente da mensagem da pagina e o
     mesmo [ref] de sempre, pra casar a conversa com a campanha.
O WhatsApp abre de forma SINCRONA no submit (gesto do usuario); o envio do lead e
keepalive e nao segura a abertura — esperar o fetch faria o bloqueador de pop-up
engolir a janela.

Quem ja se identificou (cadastro aqui ou o formulario da pagina) passa direto nas
proximas vezes, com o nome na mensagem e sem gerar lead duplicado.

A validacao de telefone e a MESMA do servidor (enviar-contato.php): la, numero
invalido e tratado como spam e recebe "ok" silencioso. Sem conferir aqui, um
cliente real que errasse um digito seria descartado sem saber.

CSS proprio com prefixo zg-: o include roda em paginas com builds de Tailwind
diferentes, e classe que nao esta no build some sem aviso.

Uso, antes do </body> (o inc/zap-tracking.php ja inclui):
    <?php $zapOrigem = 'pagina-x'; include __DIR__ . '/inc/zap-gate.php'; ?>
*/

if (!empty($GLOBALS['__svdZapGateIncluido'])) {
    return;
}
$GLOBALS['__svdZapGateIncluido'] = true;
$zapOrigemGate = isset($zapOrigem) && $zapOrigem !== '' ? $zapOrigem : 'site';
?>
<style>
  #zg[hidden]{display:none}
  #zg{position:fixed;inset:0;z-index:9999;display:flex;align-items:flex-end;justify-content:center;background:rgba(2,12,34,.74);padding:16px}
  @media (min-width:640px){#zg{align-items:center}}
  #zg .zg-caixa{position:relative;width:100%;max-width:420px;box-sizing:border-box;background:#0a3f93;color:#fff;border:1px solid rgba(255,255,255,.2);border-radius:22px;padding:22px 20px 18px;box-shadow:0 24px 60px rgba(0,0,0,.45);font-family:Roboto,system-ui,-apple-system,sans-serif;max-height:calc(100vh - 32px);overflow:auto}
  #zg .zg-t{font:700 20px/1.25 Montserrat,system-ui,sans-serif;margin:0 40px 6px 0}
  #zg .zg-s{font-size:14px;line-height:1.5;color:rgba(255,255,255,.86);margin:0 0 6px}
  #zg .zg-l{display:block;font-size:13px;font-weight:600;margin:12px 0 6px;color:rgba(255,255,255,.92)}
  #zg .zg-i{display:block;width:100%;box-sizing:border-box;border-radius:12px;border:1px solid rgba(255,255,255,.28);background:rgba(255,255,255,.1);color:#fff;padding:12px 14px;font-size:16px;line-height:1.3}
  #zg .zg-i::placeholder{color:rgba(255,255,255,.45)}
  #zg .zg-i:focus{outline:2px solid #fcd34d;outline-offset:1px}
  #zg .zg-i[aria-invalid="true"]{border-color:#fca5a5}
  #zg .zg-e{color:#fecaca;font-size:12.5px;margin:6px 0 0}
  #zg .zg-e:empty{display:none}
  #zg .zg-b{display:block;margin-top:18px;width:100%;border:0;border-radius:999px;background:#25D366;color:#fff;font:700 15px/1 Roboto,system-ui,sans-serif;padding:16px;cursor:pointer;text-transform:uppercase;letter-spacing:.04em}
  #zg .zg-b:hover{filter:brightness(1.05)}
  #zg .zg-p{font-size:12px;line-height:1.45;color:rgba(255,255,255,.62);margin:12px 0 0;text-align:center}
  #zg .zg-x{position:absolute;top:12px;right:12px;width:36px;height:36px;border:0;border-radius:50%;background:rgba(255,255,255,.14);color:#fff;font-size:22px;line-height:36px;cursor:pointer;padding:0}
  #zg .zg-hp{position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden}
</style>
<div id="zg" hidden role="dialog" aria-modal="true" aria-labelledby="zg-t">
  <div class="zg-caixa">
    <button type="button" class="zg-x" data-zg-fechar aria-label="Fechar">&times;</button>
    <p id="zg-t" class="zg-t">Antes de abrir o WhatsApp</p>
    <p class="zg-s">Deixe seu nome e WhatsApp. A equipe já te atende pelo nome — e, se a conversa não engatar, a gente retorna.</p>
    <form id="zg-form" novalidate>
      <label class="zg-l" for="zg-nome">Nome</label>
      <input class="zg-i" id="zg-nome" name="nome" type="text" autocomplete="name" placeholder="Seu nome" required />
      <p class="zg-e" id="zg-nome-erro"></p>
      <label class="zg-l" for="zg-whatsapp">WhatsApp com DDD</label>
      <input class="zg-i" id="zg-whatsapp" name="whatsapp" type="tel" inputmode="tel" autocomplete="tel" placeholder="(11) 99999-9999" required />
      <p class="zg-e" id="zg-whatsapp-erro"></p>
      <label class="zg-l" for="zg-email">E-mail <span style="font-weight:400;color:rgba(255,255,255,.6)">(opcional)</span></label>
      <input class="zg-i" id="zg-email" name="email" type="email" inputmode="email" autocomplete="email" placeholder="voce@empresa.com.br" />
      <p class="zg-e" id="zg-email-erro"></p>
      <div class="zg-hp" aria-hidden="true"><label for="zg-website">Não preencha</label><input id="zg-website" name="website" type="text" tabindex="-1" autocomplete="off" /></div>
      <button type="submit" class="zg-b">Abrir o WhatsApp</button>
      <p class="zg-p">Usamos seus dados para retornar o contato e enviar novidades do Sistema Venda Direta, inclusive em anúncios. Você pode pedir a exclusão quando quiser.</p>
    </form>
  </div>
</div>
<script>
  (function () {
    if (window.__svdZapGate) { return; }
    window.__svdZapGate = true;

    var ORIGEM = <?= json_encode($zapOrigemGate, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    var CHAVE = "svd-lead-contato";
    var caixa = document.getElementById("zg");
    var form = document.getElementById("zg-form");
    var campoNome = document.getElementById("zg-nome");
    var campoTel = document.getElementById("zg-whatsapp");
    var campoEmail = document.getElementById("zg-email");
    var pendente = null;
    var focoAntes = null;

    function digitos(v) { return (v || "").replace(/\D+/g, ""); }

    // mesma regra do enviar-contato.php (la, numero fora dela vira spam silencioso)
    function telValido(v) {
      var d = digitos(v).replace(/^0+/, "");
      if (d.length === 10) { return true; }
      if (d.length === 11) { return d.charAt(2) === "9"; }
      if (d.indexOf("55") === 0) { return d.length === 12 || (d.length === 13 && d.charAt(4) === "9"); }
      return false;
    }

    function e164(v) {
      var d = digitos(v).replace(/^0+/, "");
      return "+" + (d.indexOf("55") === 0 && d.length >= 12 ? d : "55" + d);
    }

    function identificado() {
      try {
        var a = JSON.parse(window.localStorage.getItem(CHAVE) || "null");
        if (a && a.nome && a.whatsapp) { return a; }
      } catch (e) {}
      // quem ja mandou o formulario da pagina (js/scripts.js guarda nome e zap)
      try {
        var b = JSON.parse(window.sessionStorage.getItem("svd-contact-lead") || "null");
        if (b && b.nome && b.whatsapp) { return { nome: b.nome, whatsapp: b.whatsapp }; }
      } catch (e) {}
      return null;
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

    function evento(nome, params) {
      if (typeof window.gtag === "function") {
        var p = params || {};
        if (window.__svdSimBucket) { p.sim_faturamento = window.__svdSimBucket; }
        window.gtag("event", nome, p);
      }
    }

    // "Ola! vim pela Promocao... [ref X]" -> "Ola! Sou Ana. vim pela Promocao... [ref X]"
    function montarUrl(href, nome) {
      var base = href.split("?")[0];
      var texto = "";
      try { texto = new URL(href, window.location.href).searchParams.get("text") || ""; } catch (e) {}
      if (!texto) { texto = "Vim pelo site do Sistema Venda Direta."; }
      texto = texto.replace(/^\s*Ol[aá][!,.]?\s*/i, "");
      texto = texto.charAt(0).toUpperCase() + texto.slice(1);
      if (texto.indexOf("[ref") === -1) { texto += " [ref " + zapRef() + "]"; }
      // a mensagem do formulario ja traz o nome ("Meu nome e {nome}"): nao repete
      var jaTemNome = texto.toLowerCase().indexOf(nome.trim().toLowerCase()) !== -1;
      return base + "?text=" + encodeURIComponent((jaTemNome ? "Olá! " : "Olá! Sou " + nome.trim() + ". ") + texto);
    }

    function abrirWhatsapp(url, novaAba) {
      var janela = null;
      if (novaAba) {
        try { janela = window.open(url, "_blank"); } catch (e) {}
        if (janela) { try { janela.opener = null; } catch (e) {} }
      }
      if (!janela) { window.location.href = url; }
    }

    function atribuicao(fd) {
      var guardado = {};
      try { guardado = JSON.parse(window.sessionStorage.getItem("svd-attribution") || "{}"); } catch (e) {}
      var daUrl = new URLSearchParams(window.location.search);
      ["gclid", "utm_source", "utm_medium", "utm_campaign", "utm_content"].forEach(function (k) {
        var v = daUrl.get(k) || guardado[k];
        if (v) { fd.append(k, v); }
      });
      var ga = document.cookie.match(/(?:^|;\s*)_ga=GA\d+\.\d+\.(\d+\.\d+)/);
      if (ga) { fd.append("ga_client_id", ga[1]); }
      if (window.__svdSimBucket) { fd.append("sim_faturamento", window.__svdSimBucket); }
      // rede de seguranca do WebView: o servidor extrai gclid/UTM daqui
      fd.append("page_url", window.location.href.split("#")[0]);
    }

    function erro(campo, msg) {
      var p = document.getElementById(campo.id + "-erro");
      if (p) { p.textContent = msg || ""; }
      if (msg) { campo.setAttribute("aria-invalid", "true"); } else { campo.removeAttribute("aria-invalid"); }
      return !msg;
    }

    function preencher() {
      var anterior = identificado() || {};
      var formNome = document.getElementById("contact-nome");
      var formTel = document.getElementById("contact-whatsapp");
      if (!campoNome.value) { campoNome.value = (formNome && formNome.value) || anterior.nome || ""; }
      if (!campoTel.value) { campoTel.value = (formTel && formTel.value) || anterior.whatsapp || ""; }
    }

    function abrirGate(link) {
      pendente = link;
      focoAntes = document.activeElement;
      preencher();
      caixa.hidden = false;
      document.documentElement.style.overflow = "hidden";
      var alvo = !campoNome.value ? campoNome : (!campoTel.value ? campoTel : campoEmail);
      setTimeout(function () { try { alvo.focus(); } catch (e) {} }, 30);
      evento("whatsapp_gate_aberto", { page: ORIGEM });
    }

    function fecharGate() {
      caixa.hidden = true;
      document.documentElement.style.overflow = "";
      pendente = null;
      if (focoAntes && focoAntes.focus) { try { focoAntes.focus(); } catch (e) {} }
    }

    // Captura na JANELA, antes dos handlers antigos (registrados no document):
    // eles mandavam o beacon do lead anonimo "zap", que este cadastro substitui.
    window.addEventListener("click", function (ev) {
      var a = ev.target && ev.target.closest ? ev.target.closest('a[href*="wa.me"]') : null;
      if (!a || ev.defaultPrevented) { return; }
      ev.preventDefault();
      ev.stopImmediatePropagation();
      var quem = identificado();
      if (quem) {
        evento("whatsapp_click", { page: ORIGEM });
        abrirWhatsapp(montarUrl(a.href, quem.nome), a.target === "_blank");
        return;
      }
      abrirGate(a);
    }, true);

    // Quem manda o formulario da pagina ja se identificou: guarda aqui, no envio.
    // O js/scripts.js apaga o "svd-contact-lead" da sessao assim que a pagina volta
    // com ?mail=ok — sem isto, o "Abrir WhatsApp agora" da tela de sucesso cairia
    // no cadastro de novo, pedindo o que a pessoa acabou de digitar.
    document.addEventListener("submit", function (ev) {
      var f = ev.target;
      if (!f || f.id !== "contact-lead-form") { return; }
      var n = f.querySelector('[name="nome"]');
      var t = f.querySelector('[name="whatsapp"], [name="telefone"]');
      var em = f.querySelector('[name="email"]');
      var nome = n ? n.value.trim() : "";
      var tel = t ? t.value.trim() : "";
      if (nome.length >= 2 && telValido(tel)) {
        try { window.localStorage.setItem(CHAVE, JSON.stringify({ nome: nome, whatsapp: tel, email: em ? em.value.trim() : "" })); } catch (e) {}
      }
    }, true);

    caixa.addEventListener("click", function (ev) {
      if (ev.target === caixa || (ev.target.closest && ev.target.closest("[data-zg-fechar]"))) { fecharGate(); }
    });
    document.addEventListener("keydown", function (ev) {
      if (ev.key === "Escape" && !caixa.hidden) { fecharGate(); }
    });

    form.addEventListener("submit", function (ev) {
      ev.preventDefault();
      var nome = campoNome.value.trim();
      var tel = campoTel.value.trim();
      var email = campoEmail.value.trim();
      var ok = erro(campoNome, nome.length >= 2 ? "" : "Informe seu nome.");
      ok = erro(campoTel, telValido(tel) ? "" : "Confira o número com DDD — ex.: (11) 99999-9999.") && ok;
      ok = erro(campoEmail, !email || /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email) ? "" : "E-mail inválido — ou deixe em branco.") && ok;
      if (!ok) {
        var primeiro = form.querySelector('[aria-invalid="true"]');
        if (primeiro) { primeiro.focus(); }
        return;
      }

      var ref = zapRef();
      var link = pendente;
      var url = montarUrl(link ? link.href : "https://wa.me/5511994566726", nome);

      var fd = new FormData();
      fd.append("nome", nome);
      fd.append("whatsapp", tel);
      if (email) { fd.append("email", email); }
      fd.append("origem", ORIGEM);
      fd.append("canal", "whatsapp");
      fd.append("resposta", "json");
      fd.append("servico", "WhatsApp — cadastro antes da conversa");
      fd.append("mensagem", "Pediu para falar no WhatsApp pela página " + ORIGEM + " [ref " + ref + "]. Se não chegou mensagem, retornar pelo número informado.");
      fd.append("website", document.getElementById("zg-website").value);
      atribuicao(fd);
      try {
        fetch("/enviar-contato.php", { method: "POST", body: fd, keepalive: true, credentials: "same-origin" })
          .catch(function () { try { navigator.sendBeacon("/enviar-contato.php", fd); } catch (e) {} });
      } catch (e) {
        try { navigator.sendBeacon("/enviar-contato.php", fd); } catch (e2) {}
      }

      if (typeof window.gtag === "function") {
        var dados = { phone_number: e164(tel) };
        if (email) { dados.email = email.toLowerCase(); }
        window.gtag("set", "user_data", dados);
      }
      evento("generate_lead", { page: ORIGEM, metodo: "whatsapp" });
      evento("whatsapp_click", { page: ORIGEM });

      try { window.localStorage.setItem(CHAVE, JSON.stringify({ nome: nome, whatsapp: tel, email: email })); } catch (e) {}

      var novaAba = !link || link.target === "_blank";
      fecharGate();
      abrirWhatsapp(url, novaAba);
    });
  })();
</script>
