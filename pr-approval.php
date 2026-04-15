<?php
declare(strict_types=1);

function loadEnvFile(string $filePath): array
{
    if (!is_file($filePath)) {
        return [];
    }

    $env = [];
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return [];
    }

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }

        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }

        $key = trim($parts[0]);
        $value = trim($parts[1]);
        $value = trim($value, "\"'");
        $env[$key] = $value;
    }

    return $env;
}

function envValue(array $env, string $key, string $default = ''): string
{
    $runtime = getenv($key);
    if ($runtime !== false && trim((string) $runtime) !== '') {
        return trim((string) $runtime);
    }

    if (isset($env[$key]) && trim((string) $env[$key]) !== '') {
        return trim((string) $env[$key]);
    }

    return $default;
}

function base64UrlDecode(string $value): string|false
{
    $padding = strlen($value) % 4;
    if ($padding > 0) {
        $value .= str_repeat('=', 4 - $padding);
    }

    $decoded = base64_decode(strtr($value, '-_', '+/'), true);
    return $decoded === false ? false : $decoded;
}

function respondHtml(int $statusCode, string $title, string $message): void
{
    http_response_code($statusCode);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!doctype html><html lang="pt-BR"><head><meta charset="UTF-8"><title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title></head><body style="font-family:Arial,sans-serif;max-width:760px;margin:32px auto;padding:0 16px;">';
    echo '<h2>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h2>';
    echo '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
    echo '</body></html>';
    exit;
}

function appendLog(string $path, string $line): void
{
    $directory = dirname($path);
    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }

    file_put_contents($path, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function nonceWasUsed(string $noncePath, string $nonce): bool
{
    if (!is_file($noncePath)) {
        return false;
    }

    $content = file($noncePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($content === false) {
        return false;
    }

    foreach ($content as $line) {
        if (trim($line) === $nonce) {
            return true;
        }
    }

    return false;
}

function markNonceAsUsed(string $noncePath, string $nonce): void
{
    appendLog($noncePath, $nonce);
}

function githubRequest(string $method, string $url, string $token, ?array $payload = null): array
{
    $headers = [
        'Accept: application/vnd.github+json',
        'Authorization: Bearer ' . $token,
        'X-GitHub-Api-Version: 2022-11-28',
        'User-Agent: coderush-pr-approval-bot'
    ];

    $context = [
        'http' => [
            'method' => $method,
            'header' => implode("\r\n", $headers),
            'ignore_errors' => true,
            'timeout' => 20
        ]
    ];

    if ($payload !== null) {
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            return ['ok' => false, 'status' => 0, 'body' => 'Falha ao serializar payload JSON'];
        }

        $context['http']['header'] .= "\r\nContent-Type: application/json";
        $context['http']['content'] = $json;
    }

    $streamContext = stream_context_create($context);
    $body = @file_get_contents($url, false, $streamContext);
    $responseHeaders = $http_response_header ?? [];
    $status = 0;
    if (isset($responseHeaders[0]) && preg_match('/\s(\d{3})\s/', $responseHeaders[0], $matches) === 1) {
        $status = (int) $matches[1];
    }

    return [
        'ok' => $status >= 200 && $status < 300,
        'status' => $status,
        'body' => $body === false ? '' : $body
    ];
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    respondHtml(405, 'Metodo nao permitido', 'Use o link recebido por e-mail para aprovar ou reprovar a PR.');
}

$env = loadEnvFile(__DIR__ . '/.env');
$token = trim((string) ($_GET['token'] ?? ''));
$signingSecret = envValue($env, 'PR_APPROVAL_SIGNING_SECRET');
$githubToken = envValue($env, 'GITHUB_APPROVAL_BOT_TOKEN');
$expectedRepo = envValue($env, 'PR_APPROVAL_GITHUB_REPO');
$storageDir = __DIR__ . DIRECTORY_SEPARATOR . 'storage';
$noncePath = $storageDir . DIRECTORY_SEPARATOR . 'pr-approval-used-nonces.log';
$auditPath = $storageDir . DIRECTORY_SEPARATOR . 'pr-approval-audit.log';

if ($token === '' || $signingSecret === '' || $githubToken === '') {
    respondHtml(500, 'Configuracao incompleta', 'Variaveis de ambiente obrigatorias nao foram configuradas.');
}

$parts = explode('.', $token, 2);
if (count($parts) !== 2) {
    respondHtml(400, 'Token invalido', 'O token de aprovacao nao esta no formato esperado.');
}

[$payloadPart, $signaturePart] = $parts;
$expectedSignature = rtrim(strtr(base64_encode(hash_hmac('sha256', $payloadPart, $signingSecret, true)), '+/', '-_'), '=');
if (!hash_equals($expectedSignature, $signaturePart)) {
    appendLog($auditPath, sprintf('[%s] invalid_signature ip=%s', date('c'), $_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    respondHtml(403, 'Assinatura invalida', 'O link de aprovacao nao e valido.');
}

$payloadJson = base64UrlDecode($payloadPart);
if ($payloadJson === false) {
    respondHtml(400, 'Payload invalido', 'Nao foi possivel ler o token recebido.');
}

$payload = json_decode($payloadJson, true);
if (!is_array($payload)) {
    respondHtml(400, 'Payload invalido', 'Os dados do token estao corrompidos.');
}

$requiredKeys = ['repo', 'pr_number', 'action', 'exp', 'nonce', 'run_id', 'sha'];
foreach ($requiredKeys as $key) {
    if (!array_key_exists($key, $payload)) {
        respondHtml(400, 'Payload incompleto', 'Faltam campos obrigatorios no token de aprovacao.');
    }
}

$repo = (string) $payload['repo'];
$prNumber = (int) $payload['pr_number'];
$action = (string) $payload['action'];
$exp = (int) $payload['exp'];
$nonce = (string) $payload['nonce'];
$runId = (string) $payload['run_id'];
$sha = (string) $payload['sha'];

if ($expectedRepo !== '' && !hash_equals($expectedRepo, $repo)) {
    appendLog($auditPath, sprintf('[%s] repo_mismatch repo=%s expected=%s', date('c'), $repo, $expectedRepo));
    respondHtml(403, 'Repositorio nao autorizado', 'Este link nao pode atuar neste repositorio.');
}

if (!in_array($action, ['approve', 'reject'], true)) {
    respondHtml(400, 'Acao invalida', 'Acao solicitada nao e reconhecida.');
}

if ($prNumber <= 0 || $nonce === '') {
    respondHtml(400, 'Dados invalidos', 'Numero da PR ou nonce invalido.');
}

if ($exp < time()) {
    appendLog($auditPath, sprintf('[%s] expired_token pr=%d action=%s', date('c'), $prNumber, $action));
    respondHtml(410, 'Link expirado', 'Este link ja expirou. Execute novamente o workflow para gerar novo e-mail.');
}

if (nonceWasUsed($noncePath, $nonce)) {
    appendLog($auditPath, sprintf('[%s] replay_detected pr=%d nonce=%s', date('c'), $prNumber, $nonce));
    respondHtml(409, 'Link ja utilizado', 'Este link ja foi usado anteriormente.');
}

$prEndpoint = sprintf('https://api.github.com/repos/%s/pulls/%d', $repo, $prNumber);
$prCheck = githubRequest('GET', $prEndpoint, $githubToken);
if (!$prCheck['ok']) {
    appendLog($auditPath, sprintf('[%s] pr_check_failed pr=%d status=%d body=%s', date('c'), $prNumber, $prCheck['status'], $prCheck['body']));
    respondHtml(502, 'Falha ao consultar PR', 'Nao foi possivel validar o estado atual da PR no GitHub.');
}

$prData = json_decode($prCheck['body'], true);
if (!is_array($prData) || ($prData['state'] ?? '') !== 'open') {
    appendLog($auditPath, sprintf('[%s] pr_not_open pr=%d', date('c'), $prNumber));
    respondHtml(409, 'PR nao aberta', 'A PR nao esta aberta e nao pode receber revisao.');
}

$reviewEvent = $action === 'approve' ? 'APPROVE' : 'REQUEST_CHANGES';
$reviewBody = $action === 'approve'
    ? sprintf('Aprovacao registrada via fluxo de e-mail. Run: %s, Commit: %s', $runId, $sha)
    : sprintf('Reprovacao registrada via fluxo de e-mail. Run: %s, Commit: %s', $runId, $sha);

$reviewEndpoint = sprintf('https://api.github.com/repos/%s/pulls/%d/reviews', $repo, $prNumber);
$reviewResult = githubRequest('POST', $reviewEndpoint, $githubToken, [
    'event' => $reviewEvent,
    'body' => $reviewBody
]);

if (!$reviewResult['ok']) {
    appendLog($auditPath, sprintf('[%s] review_failed pr=%d action=%s status=%d body=%s', date('c'), $prNumber, $action, $reviewResult['status'], $reviewResult['body']));
    respondHtml(502, 'Falha ao registrar review', 'Nao foi possivel aplicar a decisao no GitHub.');
}

markNonceAsUsed($noncePath, $nonce);
appendLog(
    $auditPath,
    sprintf(
        '[%s] success pr=%d action=%s run=%s ip=%s',
        date('c'),
        $prNumber,
        $action,
        $runId,
        $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    )
);

if ($action === 'approve') {
    respondHtml(200, 'PR aprovada com sucesso', sprintf('A PR #%d foi aprovada no GitHub.', $prNumber));
}

respondHtml(200, 'PR reprovada com sucesso', sprintf('A PR #%d foi reprovada no GitHub (solicitacao de ajustes).', $prNumber));
