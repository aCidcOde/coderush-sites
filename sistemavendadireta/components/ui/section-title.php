<?php
/**
 * Uso:
 * $title = "Titulo";
 * require __DIR__ . "/section-title.php";
 */
?>
<h2 class="font-[var(--font-heading)] text-3xl font-semibold sm:text-[42px]"><?php echo htmlspecialchars($title ?? "", ENT_QUOTES, "UTF-8"); ?></h2>
<div class="mt-2 h-1 w-[72px] rounded-full bg-white"></div>
