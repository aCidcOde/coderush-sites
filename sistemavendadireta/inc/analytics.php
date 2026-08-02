<?php
/*
[Modulo Analytics SVD]
@Author: André Gomes ( @acidcode )
@since 2026-08-02
Snippet Google Analytics 4 compartilhado por todas as paginas do SVD.
Enquanto SVD_GA4_ID estiver vazio, nao emite nada — seguro pra deploy antes de criar a property.
Preencher com o Measurement ID (formato G-XXXXXXXXXX) quando a property GA4 for criada.
*/

const SVD_GA4_ID = '';

if (SVD_GA4_ID !== ''): ?>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars(SVD_GA4_ID, ENT_QUOTES, 'UTF-8') ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '<?= htmlspecialchars(SVD_GA4_ID, ENT_QUOTES, 'UTF-8') ?>');
</script>
<?php endif; ?>
