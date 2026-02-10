<?php

declare(strict_types=1);

/*
[Modulo Infraestrutura SEO SVD]
@Author: André Gomes ( @acidcode )
@since 2026-02-10
Bootstrap para canonicalizacao de host/URL e cabecalhos HTTP basicos das paginas publicas.
*/

if (PHP_SAPI === 'cli') {
    return;
}

$host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
$host = preg_replace('/:\\d+$/', '', $host) ?? $host;
$requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
$queryString = (string) ($_SERVER['QUERY_STRING'] ?? '');

if ($host === 'sistemavendadireta.com.br') {
    header('Location: https://www.sistemavendadireta.com.br' . $requestUri, true, 301);
    exit;
}

if ($host === 'www.sistemavendadireta.com.br') {
    $path = (string) (parse_url($requestUri, PHP_URL_PATH) ?? '/');

    if ($path === '/index.php' || str_ends_with($path, '/index.php')) {
        $normalizedPath = preg_replace('#/index\\.php$#', '/', $path) ?? '/';
        if ($normalizedPath === '') {
            $normalizedPath = '/';
        }

        $target = 'https://www.sistemavendadireta.com.br' . $normalizedPath;
        if ($queryString !== '') {
            $target .= '?' . $queryString;
        }

        header('Location: ' . $target, true, 301);
        exit;
    }
}

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
