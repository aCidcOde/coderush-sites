<?php

declare(strict_types=1);

const CONTENT_MAX_BODY_BYTES = 200000;
const CONTENT_MAX_REQUEST_BYTES = 240000;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, private');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

$dataDirectory = __DIR__.'/data';
$storagePath = $dataDirectory.'/.posts.json';
$tokenPath = $dataDirectory.'/.content-token';
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($method === 'GET') {
    handleGet($storagePath);
}

if ($method === 'POST') {
    handlePost($storagePath, $tokenPath, $dataDirectory);
}

respond(['message' => 'Método não permitido.'], 405, ['Allow' => 'GET, POST, OPTIONS']);

function handleGet(string $storagePath): never
{
    $posts = array_values(array_filter(
        readPosts($storagePath),
        static fn (array $post): bool => isPublished($post),
    ));

    usort($posts, static fn (array $left, array $right): int => strcmp(
        (string) ($right['published_at'] ?? ''),
        (string) ($left['published_at'] ?? ''),
    ));

    $slug = trim((string) ($_GET['slug'] ?? ''));
    if ($slug !== '') {
        foreach ($posts as $post) {
            if (hash_equals((string) ($post['slug'] ?? ''), $slug)) {
                $related = array_values(array_filter(
                    $posts,
                    static fn (array $candidate): bool => ($candidate['slug'] ?? null) !== $slug
                        && ($candidate['category'] ?? null) === ($post['category'] ?? null),
                ));

                respond([
                    'data' => publicPost($post, true),
                    'related' => array_map(
                        static fn (array $relatedPost): array => publicPost($relatedPost),
                        array_slice($related, 0, 3),
                    ),
                ]);
            }
        }

        respond(['message' => 'Conteúdo não encontrado.'], 404);
    }

    $limit = max(1, min(50, (int) ($_GET['limit'] ?? 12)));
    $offset = max(0, (int) ($_GET['offset'] ?? 0));
    $page = array_slice($posts, $offset, $limit);

    respond([
        'data' => array_map(static fn (array $post): array => publicPost($post), $page),
        'meta' => [
            'total' => count($posts),
            'limit' => $limit,
            'offset' => $offset,
        ],
    ]);
}

function handlePost(string $storagePath, string $tokenPath, string $dataDirectory): never
{
    $expectedToken = trim((string) (getenv('MARKETING_CONTENT_API_TOKEN') ?: readToken($tokenPath)));
    $providedToken = bearerToken();

    if ($expectedToken === '') {
        respond(['message' => 'A API de conteúdo ainda não possui token configurado.'], 503);
    }

    if ($providedToken === null || ! hash_equals($expectedToken, $providedToken)) {
        respond(['message' => 'Token de ingestão inválido.'], 401);
    }

    $contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
    if ($contentLength > CONTENT_MAX_REQUEST_BYTES) {
        respond(['message' => 'O conteúdo excede o limite permitido.'], 413);
    }

    $rawBody = file_get_contents('php://input');
    if (! is_string($rawBody) || $rawBody === '') {
        respond(['message' => 'Envie um objeto JSON no corpo da requisição.'], 422);
    }

    try {
        $payload = json_decode($rawBody, true, flags: JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        respond(['message' => 'JSON inválido.'], 400);
    }

    if (! is_array($payload)) {
        respond(['message' => 'O corpo deve ser um objeto JSON.'], 422);
    }

    $errors = validatePayload($payload);
    if ($errors !== []) {
        respond(['message' => 'Revise os campos enviados.', 'errors' => $errors], 422);
    }

    if (! is_dir($dataDirectory) && ! mkdir($dataDirectory, 0775, true) && ! is_dir($dataDirectory)) {
        respond(['message' => 'Não foi possível preparar o diretório de conteúdo.'], 500);
    }

    $lockPath = $dataDirectory.'/.posts.lock';
    $lockHandle = fopen($lockPath, 'c+');
    if ($lockHandle === false || ! flock($lockHandle, LOCK_EX)) {
        respond(['message' => 'Não foi possível bloquear o armazenamento para escrita.'], 500);
    }

    try {
        $posts = readPosts($storagePath);
        $externalId = trim((string) $payload['external_id']);
        $existingIndex = null;

        foreach ($posts as $index => $post) {
            if (($post['external_id'] ?? null) === $externalId) {
                $existingIndex = $index;
                break;
            }
        }

        $existing = $existingIndex !== null ? $posts[$existingIndex] : [];
        $slug = resolveSlug($payload, $posts, $existingIndex, $existing);
        $now = gmdate(DATE_ATOM);
        $status = (string) ($payload['status'] ?? 'draft');
        $post = [
            'external_id' => $externalId,
            'slug' => $slug,
            'title' => trim((string) $payload['title']),
            'excerpt' => excerpt($payload),
            'body' => trim((string) $payload['body']),
            'category' => trim((string) ($payload['category'] ?? 'Insights')) ?: 'Insights',
            'author_name' => trim((string) ($payload['author_name'] ?? 'BFR Intelligence')) ?: 'BFR Intelligence',
            'featured_image_url' => nullableString($payload['featured_image_url'] ?? null),
            'featured_image_alt' => nullableString($payload['featured_image_alt'] ?? null),
            'meta_title' => nullableString($payload['meta_title'] ?? null),
            'meta_description' => nullableString($payload['meta_description'] ?? null),
            'tags' => array_values(array_map('strval', $payload['tags'] ?? [])),
            'status' => $status,
            'published_at' => $status === 'published'
                ? (nullableString($payload['published_at'] ?? null) ?? ($existing['published_at'] ?? $now))
                : null,
            'source_url' => nullableString($payload['source_url'] ?? null),
            'created_at' => $existing['created_at'] ?? $now,
            'updated_at' => $now,
        ];

        if ($existingIndex === null) {
            $posts[] = $post;
        } else {
            $posts[$existingIndex] = $post;
        }

        writePosts($storagePath, $posts, $dataDirectory);
    } finally {
        flock($lockHandle, LOCK_UN);
        fclose($lockHandle);
    }

    respond(['data' => publicPost($post, true)], $existingIndex === null ? 201 : 200);
}

/** @return list<array<string, mixed>> */
function readPosts(string $storagePath): array
{
    if (! is_file($storagePath)) {
        return [];
    }

    $json = file_get_contents($storagePath);
    if (! is_string($json) || trim($json) === '') {
        return [];
    }

    try {
        $posts = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        respond(['message' => 'O armazenamento de conteúdo está corrompido.'], 500);
    }

    return is_array($posts) ? array_values(array_filter($posts, 'is_array')) : [];
}

/** @param list<array<string, mixed>> $posts */
function writePosts(string $storagePath, array $posts, string $dataDirectory): void
{
    $temporaryPath = tempnam($dataDirectory, '.posts-');
    if ($temporaryPath === false) {
        respond(['message' => 'Não foi possível preparar a escrita do conteúdo.'], 500);
    }

    try {
        $json = json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        if (file_put_contents($temporaryPath, $json."\n", LOCK_EX) === false || ! rename($temporaryPath, $storagePath)) {
            respond(['message' => 'Não foi possível salvar o conteúdo.'], 500);
        }
        @chmod($storagePath, 0664);
    } catch (JsonException) {
        respond(['message' => 'Não foi possível serializar o conteúdo.'], 500);
    } finally {
        if (is_file($temporaryPath)) {
            @unlink($temporaryPath);
        }
    }
}

/** @param array<string, mixed> $payload @return array<string, list<string>> */
function validatePayload(array $payload): array
{
    $errors = [];
    validateRequiredString($payload, 'external_id', 255, $errors);
    validateRequiredString($payload, 'title', 200, $errors);
    validateRequiredString($payload, 'body', CONTENT_MAX_BODY_BYTES, $errors);

    foreach (['slug' => 200, 'excerpt' => 600, 'category' => 80, 'author_name' => 120, 'featured_image_alt' => 255, 'meta_title' => 200, 'meta_description' => 320] as $field => $limit) {
        validateOptionalString($payload, $field, $limit, $errors);
    }

    foreach (['featured_image_url', 'source_url'] as $field) {
        if (isset($payload[$field]) && $payload[$field] !== '' && ! isHttpUrl($payload[$field])) {
            $errors[$field][] = 'Informe uma URL HTTP ou HTTPS válida.';
        }
    }

    if (isset($payload['featured_image_url']) && trim((string) $payload['featured_image_url']) !== '' && empty($payload['featured_image_alt'])) {
        $errors['featured_image_alt'][] = 'Informe o texto alternativo da imagem.';
    }

    if (isset($payload['status']) && ! in_array($payload['status'], ['draft', 'published'], true)) {
        $errors['status'][] = 'Use draft ou published.';
    }

    if (isset($payload['published_at']) && $payload['published_at'] !== '' && strtotime((string) $payload['published_at']) === false) {
        $errors['published_at'][] = 'Informe uma data válida.';
    }

    if (isset($payload['tags'])) {
        if (! is_array($payload['tags']) || count($payload['tags']) > 10) {
            $errors['tags'][] = 'Envie no máximo 10 tags.';
        } else {
            foreach ($payload['tags'] as $tag) {
                if (! is_string($tag) || mb_strlen($tag) > 50) {
                    $errors['tags'][] = 'Cada tag deve ser um texto de até 50 caracteres.';
                    break;
                }
            }
        }
    }

    return $errors;
}

/** @param array<string, mixed> $payload @param array<string, list<string>> $errors */
function validateRequiredString(array $payload, string $field, int $limit, array &$errors): void
{
    if (! isset($payload[$field]) || ! is_string($payload[$field]) || trim($payload[$field]) === '') {
        $errors[$field][] = 'Campo obrigatório.';

        return;
    }

    if (strlen($payload[$field]) > $limit) {
        $errors[$field][] = "Use no máximo {$limit} caracteres.";
    }
}

/** @param array<string, mixed> $payload @param array<string, list<string>> $errors */
function validateOptionalString(array $payload, string $field, int $limit, array &$errors): void
{
    if (! isset($payload[$field]) || $payload[$field] === null || $payload[$field] === '') {
        return;
    }

    if (! is_string($payload[$field]) || mb_strlen($payload[$field]) > $limit) {
        $errors[$field][] = "Use um texto de até {$limit} caracteres.";
    }
}

/** @param array<string, mixed> $payload @param list<array<string, mixed>> $posts @param array<string, mixed> $existing */
function resolveSlug(array $payload, array $posts, ?int $existingIndex, array $existing): string
{
    if (! array_key_exists('slug', $payload) && isset($existing['slug'])) {
        return (string) $existing['slug'];
    }

    $baseSlug = slugify((string) ($payload['slug'] ?? $payload['title'] ?? 'conteudo')) ?: 'conteudo';
    $slug = $baseSlug;
    $suffix = 2;

    while (slugExists($posts, $slug, $existingIndex)) {
        $slug = $baseSlug.'-'.$suffix;
        $suffix++;
    }

    return $slug;
}

/** @param list<array<string, mixed>> $posts */
function slugExists(array $posts, string $slug, ?int $ignoredIndex): bool
{
    foreach ($posts as $index => $post) {
        if ($index !== $ignoredIndex && ($post['slug'] ?? null) === $slug) {
            return true;
        }
    }

    return false;
}

function slugify(string $value): string
{
    $value = strtr(mb_strtolower($value), [
        'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
        'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o',
        'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
        'ç' => 'c', 'ñ' => 'n',
    ]);
    $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    $normalized = strtolower(is_string($ascii) ? $ascii : $value);
    $normalized = preg_replace('/[^a-z0-9]+/', '-', $normalized) ?? '';

    return trim($normalized, '-');
}

/** @param array<string, mixed> $payload */
function excerpt(array $payload): string
{
    $provided = trim((string) ($payload['excerpt'] ?? ''));
    if ($provided !== '') {
        return $provided;
    }

    $plain = preg_replace('/[`#>*_\[\]()!-]+/', ' ', (string) ($payload['body'] ?? '')) ?? '';
    $plain = preg_replace('/\s+/', ' ', $plain) ?? '';

    return rtrim(mb_substr(trim($plain), 0, 180)).(mb_strlen(trim($plain)) > 180 ? '…' : '');
}

/** @param array<string, mixed> $post */
function isPublished(array $post): bool
{
    return ($post['status'] ?? null) === 'published'
        && isset($post['published_at'])
        && strtotime((string) $post['published_at']) !== false
        && strtotime((string) $post['published_at']) <= time();
}

/** @param array<string, mixed> $post @return array<string, mixed> */
function publicPost(array $post, bool $withBody = false): array
{
    $body = (string) ($post['body'] ?? '');
    $wordCount = count(preg_split('/\s+/u', trim($body), -1, PREG_SPLIT_NO_EMPTY) ?: []);
    $public = [
        'slug' => (string) ($post['slug'] ?? ''),
        'title' => (string) ($post['title'] ?? ''),
        'excerpt' => (string) ($post['excerpt'] ?? ''),
        'category' => (string) ($post['category'] ?? 'Insights'),
        'author_name' => (string) ($post['author_name'] ?? 'BFR Intelligence'),
        'featured_image_url' => nullableString($post['featured_image_url'] ?? null),
        'featured_image_alt' => nullableString($post['featured_image_alt'] ?? null),
        'meta_title' => nullableString($post['meta_title'] ?? null),
        'meta_description' => nullableString($post['meta_description'] ?? null),
        'tags' => array_values(array_filter($post['tags'] ?? [], 'is_string')),
        'published_at' => (string) ($post['published_at'] ?? ''),
        'reading_time_minutes' => max(1, (int) ceil($wordCount / 220)),
    ];

    if ($withBody) {
        $public['body'] = $body;
        $public['source_url'] = nullableString($post['source_url'] ?? null);
    }

    return $public;
}

function bearerToken(): ?string
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? null;
    if (! is_string($header) && function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        $header = $headers['Authorization'] ?? $headers['authorization'] ?? null;
    }

    if (! is_string($header) || preg_match('/^Bearer\s+(.+)$/i', trim($header), $matches) !== 1) {
        return null;
    }

    return trim($matches[1]);
}

function readToken(string $tokenPath): string
{
    if (! is_file($tokenPath)) {
        return '';
    }

    $token = file_get_contents($tokenPath);

    return is_string($token) ? trim($token) : '';
}

function nullableString(mixed $value): ?string
{
    return is_string($value) && trim($value) !== '' ? trim($value) : null;
}

function isHttpUrl(mixed $value): bool
{
    if (! is_string($value) || filter_var($value, FILTER_VALIDATE_URL) === false) {
        return false;
    }

    $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));

    return in_array($scheme, ['http', 'https'], true);
}

/** @param array<string, mixed> $payload @param array<string, string> $headers */
function respond(array $payload, int $status = 200, array $headers = []): never
{
    http_response_code($status);
    foreach ($headers as $name => $value) {
        header($name.': '.$value);
    }

    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}
