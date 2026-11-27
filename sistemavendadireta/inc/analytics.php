<?php
/*
[Modulo Analytics SVD]
@Author: André Gomes ( @acidcode )
@since 2026-08-02
Snippet Google Analytics 4 compartilhado por todas as paginas do SVD.
Enquanto SVD_GA4_ID estiver vazio, nao emite nada — seguro pra deploy antes de criar a property.
Preencher com o Measurement ID (formato G-XXXXXXXXXX) quando a property GA4 for criada.
*/

const SVD_GA4_ID = 'G-4107EVTE0Q';

if (SVD_GA4_ID !== ''): ?>
<!-- Google tag (gtag.js) -->
<?php /*
gtag.js e o maior arquivo isolado do site: 187 KB, mais que todas as imagens
somadas depois da conversao pra WebP. Nao da pra remover — e ele que registra a
conversao que a campanha usa pra aprender.

O que da pra fazer e nao pagar o handshake do zero. preconnect abre DNS, TCP e
TLS com o googletagmanager antes de o script ser pedido; sem isso a conexao so
comeca quando o parser chega na tag. Em 3G/4G isso costuma valer algumas centenas
de ms no carregamento, e velocidade entra na nota de experiencia da pagina do
Indice de Qualidade — que esta abaixo da media em quase toda palavra da campanha.

crossorigin no dominio que serve o script: sem isso o navegador abre DUAS
conexoes, uma anonima pro preconnect e outra pro download, e o ganho vira zero.
*/ ?>
<link rel="preconnect" href="https://www.googletagmanager.com" crossorigin />
<link rel="dns-prefetch" href="https://www.googletagmanager.com" />
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars(SVD_GA4_ID, ENT_QUOTES, 'UTF-8') ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '<?= htmlspecialchars(SVD_GA4_ID, ENT_QUOTES, 'UTF-8') ?>');
</script>
<?php endif; ?>
<?php /*
ATRIBUICAO GUARDADA NA ENTRADA, em qualquer pagina — 27/10/2026.

Quem gravava gclid e UTMs na sessao era o js/scripts.js, que a /sistema-mmn/ nao
carrega. Resultado: o lead #50 (26/10) clicou no anuncio do grupo MMN, leu a
pagina por 9 minutos, foi pra home e chamou no WhatsApp — e entrou no banco como
"home", sem gclid, sem campanha. Primeiro lead em 21 dias, contado como organico.

Aqui porque este include esta no head de TODA pagina; o scripts.js continua
fazendo o mesmo, e repetir e inofensivo (so acrescenta chave que veio na URL).
*/ ?>
<script>
  (function () {
    try {
      var p = new URLSearchParams(location.search), chave = "svd-attribution", s = {}, mudou = false;
      try { s = JSON.parse(sessionStorage.getItem(chave) || "{}"); } catch (e) {}
      // utm_source=site|blog e etiqueta de link INTERNO antigo (cache, post compartilhado):
      // nao pode sobrescrever a campanha de quem chegou pelo anuncio.
      var interno = /^(site|blog)$/.test(p.get("utm_source") || "");
      ["gclid", "utm_source", "utm_medium", "utm_campaign", "utm_content"].forEach(function (k) {
        var v = p.get(k);
        if (v && !(interno && k !== "gclid")) { s[k] = v; mudou = true; }
      });
      if (mudou) sessionStorage.setItem(chave, JSON.stringify(s));
    } catch (e) {}
  })();
</script>
<?php /*
CLIQUE NA DEMONSTRACAO, em qualquer pagina — 27/10/2026.

A Promocao de Fim de Ano poe a demonstracao sem cadastro como chamada principal
do anuncio. Ela abre em outro dominio (painel.), onde o GA4 do site nao existe:
sem este evento, o clique que a campanha nova promete seria invisivel, e a
leitura de novembro voltaria a contar so WhatsApp. Delegado no document porque
o link aparece em lugar diferente em cada pagina (cabecalho, hero, faixa do blog).

27/11/2026: a demonstracao passou a pedir nome e WhatsApp antes de abrir. Nas
paginas com o inc/zap-gate.php (todas as que tem link da demo), o cadastro
intercepta o clique antes deste handler e ele mesmo manda o demo_click quando a
demonstracao abre. Este fica de rede de seguranca pra pagina sem o cadastro.
*/ ?>
<?php /*
CLIQUE EM CHAMADA INTERNA — 26/11/2026.

Os links do site e dos posts pra /sistema-mmn/, /sistema-venda-direta/ e
/simulador/ levavam UTM (utm_source=site|blog). UTM em link interno abre sessao
nova no GA4 com a origem trocada e, no svd-attribution acima, sobrescrevia a
campanha de quem veio do anuncio — o lead do Google Ads entrava como "simulador"
ou "cta-artigo". Os links ficaram limpos; quem mede o clique agora e este evento.
*/ ?>
<script>
  document.addEventListener("click", function (ev) {
    var a = ev.target.closest && ev.target.closest("a[href]");
    if (!a || typeof window.gtag !== "function" || a.host !== location.host) return;
    var destino = (a.pathname.match(/^\/(sistema-mmn|sistema-venda-direta|simulador|cases|blog)\//) || [])[1];
    if (!destino || a.pathname === location.pathname) return;
    window.gtag("event", "cta_interno", { page: location.pathname, destino: destino });
  });
</script>
<script>
  document.addEventListener("click", function (ev) {
    var a = ev.target.closest && ev.target.closest('a[href*="painel.sistemavendadireta.com.br/primeiros-passos"]');
    if (!a || typeof window.gtag !== "function") return;
    window.gtag("event", "demo_click", { page: location.pathname, link_url: a.href.split("?")[0] });
  });
</script>
