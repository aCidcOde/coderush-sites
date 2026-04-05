<?php
/**
 * Fragmento de título de secção (padrão .svd-section-head).
 * Opcional: $eyebrow, $subtitle (lead).
 *
 * $title = "Título";
 * require __DIR__ . "/section-title.php";
 */
?>
<header class="svd-section-head">
  <?php if (!empty($eyebrow)) : ?>
    <p class="svd-section-head__eyebrow"><?php echo htmlspecialchars($eyebrow, ENT_QUOTES, 'UTF-8'); ?></p>
  <?php endif; ?>
  <h2 class="svd-section-head__title"><?php echo htmlspecialchars($title ?? '', ENT_QUOTES, 'UTF-8'); ?></h2>
  <?php if (!empty($subtitle)) : ?>
    <p class="svd-section-head__lead"><?php echo htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8'); ?></p>
  <?php endif; ?>
</header>
