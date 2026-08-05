<?php
declare(strict_types=1);

/*
[Modulo Contato BFR]
@Author: André Gomes ( @acidcode )
@since 2026-08-05
Recebe o formulario da home da BFR: grava o lead em storage/leads.sqlite (mesma
estrutura do SVD, com atribuicao de campanha) e dispara e-mail via PHPMailer.
Responde JSON — o front usa fetch e mantem a tela de sucesso.
*/

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Método não permitido.']);
    exit;
}

function envValue(array $env, array $keys, string $default = ''): string
{
    foreach ($keys as $key) {
        $runtime = getenv($key);
        if ($runtime !== false && trim((string) $runtime) !== '') {
            return trim((string) $runtime);
        }
        if (isset($env[$key]) && trim((string) $env[$key]) !== '') {
            return trim((string) $env[$key]);
        }
    }
    return $default;
}

function loadEnv(string $path): array
{
    if (!is_file($path)) {
        return [];
    }
    $env = [];
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$k, $v] = explode('=', $line, 2);
        $env[trim($k)] = trim(trim($v), "\"'");
    }
    return $env;
}

$field = static function (string $key, int $max = 300): string {
    $raw = $_POST[$key] ?? '';
    if (!is_string($raw)) {
        return '';
    }
    return mb_substr(trim(strip_tags($raw)), 0, $max);
};

$nome = $field('nome', 120);
$emailRaw = $field('email', 160);
$email = filter_var($emailRaw, FILTER_VALIDATE_EMAIL) ? $emailRaw : '';
$telefone = $field('telefone', 40);
$empresa = $field('empresa', 160);

if ($nome === '' || $email === '') {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => 'Informe nome e e-mail válidos.']);
    exit;
}

// honeypot simples contra bot
if (($_POST['website'] ?? '') !== '') {
    echo json_encode(['ok' => true]);
    exit;
}

$storageDir = __DIR__ . '/storage';
if (!is_dir($storageDir)) {
    @mkdir($storageDir, 0775, true);
}

$saved = false;
try {
    $pdo = new PDO('sqlite:' . $storageDir . '/leads.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('CREATE TABLE IF NOT EXISTS leads (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        created_at TEXT NOT NULL,
        nome TEXT, email TEXT, telefone TEXT, telefone_digits TEXT, empresa TEXT,
        origem TEXT, mensagem TEXT,
        ga_client_id TEXT, gclid TEXT,
        utm_source TEXT, utm_medium TEXT, utm_campaign TEXT, utm_content TEXT,
        page_url TEXT, ip TEXT, user_agent TEXT,
        status TEXT NOT NULL DEFAULT "novo",
        closed_at TEXT, close_value REAL, transaction_id TEXT
    )');
    $stmt = $pdo->prepare('INSERT INTO leads (
        created_at, nome, email, telefone, telefone_digits, empresa, origem, mensagem,
        ga_client_id, gclid, utm_source, utm_medium, utm_campaign, utm_content, page_url, ip, user_agent
    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $stmt->execute([
        date('c'), $nome, $email, $telefone ?: null,
        preg_replace('/\D+/', '', $telefone) ?: null,
        $empresa ?: null,
        $field('origem', 100) ?: 'site-bfr',
        'Lead do formulário da home BFR',
        $field('ga_client_id', 64) ?: null,
        $field('gclid') ?: null,
        $field('utm_source', 100) ?: null,
        $field('utm_medium', 100) ?: null,
        $field('utm_campaign', 150) ?: null,
        $field('utm_content', 150) ?: null,
        $field('page_url', 500) ?: null,
        $_SERVER['REMOTE_ADDR'] ?? null,
        mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 300),
    ]);
    $saved = true;
} catch (Throwable $e) {
    @file_put_contents($storageDir . '/contact-errors.log',
        sprintf("[%s] sqlite: %s\n", date('c'), $e->getMessage()), FILE_APPEND);
}

$env = loadEnv(__DIR__ . '/../.env');
$toEmail = envValue($env, ['BFR_MAIL_TO', 'MAIL_TO_ADDRESS'], 'contato@bfrintelligence.com.br');
$fromEmail = envValue($env, ['MAIL_FROM_ADDRESS'], 'no-reply@bfrintelligence.com.br');
$fromName = 'Site BFR Intelligence';

$body = implode("\n", [
    'Novo lead pelo site da BFR Intelligence:',
    '',
    'Nome: ' . $nome,
    'E-mail: ' . $email,
    'Telefone: ' . ($telefone !== '' ? $telefone : 'Não informado'),
    'Empresa: ' . ($empresa !== '' ? $empresa : 'Não informada'),
    'Campanha: ' . ($field('utm_campaign', 150) ?: 'tráfego direto/orgânico'),
    'Página: ' . ($field('page_url', 500) ?: '-'),
    '',
    'IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'desconhecido'),
    'Data: ' . date('d/m/Y H:i:s'),
]);

$sent = false;
try {
    $mailer = new PHPMailer(true);
    $host = envValue($env, ['MAIL_HOST', 'SMTP_HOST']);
    if ($host !== '') {
        $mailer->isSMTP();
        $mailer->Host = $host;
        $mailer->Port = (int) envValue($env, ['MAIL_PORT', 'SMTP_PORT'], '587');
        $mailer->SMTPAuth = true;
        $mailer->Username = envValue($env, ['MAIL_USERNAME', 'SMTP_USERNAME']);
        $mailer->Password = envValue($env, ['MAIL_PASSWORD', 'SMTP_PASSWORD']);
        $enc = strtolower(envValue($env, ['MAIL_ENCRYPTION', 'SMTP_ENCRYPTION'], 'tls'));
        $mailer->SMTPSecure = $enc === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    } else {
        $mailer->isMail();
    }
    $mailer->CharSet = PHPMailer::CHARSET_UTF8;
    $mailer->Timeout = 15;
    $mailer->setFrom($fromEmail, $fromName);
    $mailer->addAddress($toEmail);
    $mailer->addReplyTo($email, $nome);
    $mailer->Subject = '[BFR] Novo lead do site — ' . $nome;
    $mailer->Body = $body;
    $mailer->isHTML(false);
    $sent = $mailer->send();
} catch (PHPMailerException $e) {
    @file_put_contents($storageDir . '/contact-errors.log',
        sprintf("[%s] mail: %s\n", date('c'), $e->getMessage()), FILE_APPEND);
}

if (!$saved && !$sent) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Não foi possível registrar seu contato. Tente pelo e-mail contato@bfrintelligence.com.br.']);
    exit;
}

echo json_encode(['ok' => true]);
