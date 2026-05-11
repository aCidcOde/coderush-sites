<?php
// Webhook chamado pelo GH Actions (.github/workflows/deploy.yml).
// Pull do repo + rebuild assets + restart container, com detecção honesta de falha.

define('DEPLOY_TOKEN', 'ec8c4d6f4961b6b26fbb3556c90ecea6f36f39cf39c317f2b9ccc676b5b42844');
define('REPO_ROOT', '/data/coderush-sites');

$token = $_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? '';
if (!hash_equals(DEPLOY_TOKEN, $token)) {
    http_response_code(403);
    exit('Forbidden');
}

function logLine(string $msg): void {
    static $path = null;
    if ($path === null) {
        foreach (['/var/log/deploy-coderush.log', REPO_ROOT . '/deploy.log', '/tmp/deploy-coderush.log'] as $candidate) {
            $dir = dirname($candidate);
            if (is_writable($candidate) || (is_writable($dir) && (!file_exists($candidate) || is_writable($candidate)))) {
                $path = $candidate;
                break;
            }
        }
        if ($path === null) { $path = false; }
    }
    if ($path) {
        @file_put_contents($path, $msg . "\n", FILE_APPEND);
    }
}

function runStep(string $name, string $cmd): array {
    $full = 'cd ' . escapeshellarg(REPO_ROOT) . ' && ' . $cmd . ' 2>&1';
    $output = [];
    $exitCode = 0;
    exec($full, $output, $exitCode);
    $body = implode("\n", $output);
    return ['name' => $name, 'cmd' => $cmd, 'exit' => $exitCode, 'output' => $body];
}

$steps = [
    ['git pull',        'git pull origin main'],
    ['composer install', 'composer install --no-dev --no-interaction --optimize-autoloader --no-progress'],
    ['npm build:css',   'npm run build:css'],
    ['docker restart',  'docker compose restart'],
];

$results = [];
$failed = null;
foreach ($steps as [$name, $cmd]) {
    $res = runStep($name, $cmd);
    $results[] = $res;
    if ($res['exit'] !== 0) {
        $failed = $res;
        break;
    }
}

$ts = date('Y-m-d H:i:s');
logLine("=== deploy $ts ===");
foreach ($results as $r) {
    logLine("[" . $r['name'] . "] exit=" . $r['exit']);
    logLine($r['output']);
}
if ($failed) {
    logLine("DEPLOY FAILED at step: " . $failed['name']);
}
logLine("---");

if ($failed) {
    http_response_code(500);
    echo "DEPLOY FAILED at: " . $failed['name'] . " (exit " . $failed['exit'] . ")\n\n";
    echo $failed['output'] . "\n";
    exit;
}

http_response_code(200);
echo "OK\n";
foreach ($results as $r) {
    echo "[" . $r['name'] . "] exit=" . $r['exit'] . "\n";
}
