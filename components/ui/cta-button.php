<?php
/**
 * Uso:
 * $href = "#contato";
 * $label = "Solicite um Orçamento";
 * $variant = "outline"; // outline|solid
 * require __DIR__ . "/cta-button.php";
 */
$href = $href ?? "#";
$label = $label ?? "Acessar";
$variant = $variant ?? "outline";
$classes = $variant === "solid"
  ? "inline-flex items-center justify-center rounded-full bg-white px-5 py-2.5 text-sm font-semibold uppercase tracking-wide text-brand transition hover:bg-white/90"
  : "inline-flex items-center justify-center rounded-full border border-white/70 px-5 py-2.5 text-sm font-semibold uppercase tracking-wide hover:bg-white/10";
?>
<a href="<?php echo htmlspecialchars($href, ENT_QUOTES, "UTF-8"); ?>" class="<?php echo $classes; ?>">
  <?php echo htmlspecialchars($label, ENT_QUOTES, "UTF-8"); ?>
</a>
