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
