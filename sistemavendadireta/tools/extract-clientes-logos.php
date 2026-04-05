<?php
/**
 * One-off: slice Clientes.jpg into a uniform grid for the "empresas" section.
 * Run from repo: php sistemavendadireta/tools/extract-clientes-logos.php
 */
$root = dirname(__DIR__);
$srcPath = $root . '/index_svd_files/Clientes.jpg';
$outDir = $root . '/index_svd_files/clientes-logos';
if (!is_dir($outDir)) {
    mkdir($outDir, 0755, true);
}

$im = imagecreatefromjpeg($srcPath);
if (!$im) {
    fwrite(STDERR, "Failed to load JPEG\n");
    exit(1);
}
$w = imagesx($im);
$h = imagesy($im);
$cols = 5;
$rows = 5;
$cellW = (int) floor($w / $cols);
$cellH = (int) floor($h / $rows);
$max = 23;
for ($i = 0; $i < $max; $i++) {
    $x = ($i % $cols) * $cellW;
    $y = (int) floor($i / $cols) * $cellH;
    $crop = imagecrop($im, ['x' => $x, 'y' => $y, 'width' => $cellW, 'height' => $cellH]);
    if (!$crop) {
        continue;
    }
    $n = str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
    $out = $outDir . '/cliente-' . $n . '.webp';
    imagewebp($crop, $out, 88);
    imagedestroy($crop);
}
imagedestroy($im);
echo "Wrote $max files to $outDir\n";
