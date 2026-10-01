<?php
declare(strict_types=1);

/*
[Modulo Contato - CodeRush Hub]
Endpoint da raiz: recebe o formulario de contato e envia e-mail via SMTP usando PHPMailer.
Aceita tanto o formato completo (nome + email + mensagem + consent) quanto o formato
simplificado (nome + whatsapp), seguindo o mesmo padrao usado no SVD.
*/

require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

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
        $value = trim(trim($parts[1]), "\"'");
        $env[$key] = $value;
    }

    return $env;
}

function envValue(array $env, array $keys, string $default = ''): string
{
    foreach ($keys as $key) {
        $runtimeValue = getenv($key);
        if ($runtimeValue !== false && trim((string) $runtimeValue) !== '') {
            return trim((string) $runtimeValue);
        }

        if (isset($env[$key]) && trim((string) $env[$key]) !== '') {
            return trim((string) $env[$key]);
        }
    }

    return $default;
}

function wantsJsonResponse(): bool
{
    $requestedWith = strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''));
    $accept = strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? ''));

    return $requestedWith === 'xmlhttprequest' || str_contains($accept, 'application/json');
}

function safeRedirect(string $location, bool $success): void
{
    if (wantsJsonResponse()) {
        http_response_code($success ? 200 : 422);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'ok' => $success,
            'status' => $success ? 'ok' : 'erro',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    $target = $location;
    if ($target === '' || !str_starts_with($target, '/')) {
        $target = '/';
    }

    $fragment = '';
    $hashPosition = strpos($target, '#');
    if ($hashPosition !== false) {
        $fragment = substr($target, $hashPosition);
        $target = substr($target, 0, $hashPosition);
    }

    $separator = str_contains($target, '?') ? '&' : '?';
    $status = $success ? 'ok' : 'erro';
    header('Location: ' . $target . $separator . 'mail=' . $status . $fragment, true, 303);
    exit;
}

function appendLineToFile(string $filePath, string $line): bool
{
    $directory = dirname($filePath);
    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        return false;
    }

    return file_put_contents($filePath, $line . PHP_EOL, FILE_APPEND | LOCK_EX) !== false;
}

function persistLeadLocally(string $baseDir, array $payload, string $reason): bool
{
    $storageDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'storage';
    $timestamp = date('c');

    $record = [
        'saved_at' => $timestamp,
        'reason' => $reason,
        'payload' => $payload,
    ];

    $jsonLine = json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($jsonLine === false) {
        return false;
    }

    $leadSaved = appendLineToFile($storageDir . DIRECTORY_SEPARATOR . 'contact-leads.ndjson', $jsonLine);
    $logSaved = appendLineToFile(
        $storageDir . DIRECTORY_SEPARATOR . 'contact-errors.log',
        sprintf('[%s] %s', $timestamp, $reason)
    );

    return $leadSaved && $logSaved;
}

function sendMailWithPHPMailer(array $smtpConfig, string $fromEmail, string $fromName, string $toEmail, string $replyTo, string $subject, string $body, ?string &$failureReason = null): bool
{
    $host = $smtpConfig['host'];
    $port = (int) $smtpConfig['port'];
    $username = $smtpConfig['username'];
    $password = $smtpConfig['password'];
    $encryption = strtolower($smtpConfig['encryption']);

    if ($host === '' || $port <= 0 || $username === '' || $password === '') {
        $failureReason = 'SMTP config incompleto.';
        return false;
    }

    $mailer = new PHPMailer(true);

    try {
        $mailer->isSMTP();
        $mailer->Host = $host;
        $mailer->Port = $port;
        $mailer->SMTPAuth = true;
        $mailer->Username = $username;
        $mailer->Password = $password;
        $mailer->CharSet = PHPMailer::CHARSET_UTF8;
        $mailer->Encoding = PHPMailer::ENCODING_8BIT;
        $mailer->Timeout = 15;

        if ($encryption === 'ssl') {
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($encryption === 'tls' || $encryption === 'starttls') {
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mailer->SMTPSecure = '';
            $mailer->SMTPAutoTLS = false;
        }

        $mailer->setFrom($fromEmail, $fromName);
        $mailer->addAddress($toEmail);
        $mailer->addReplyTo($replyTo);

        $mailer->Subject = $subject;
        $mailer->Body = $body;
        $mailer->isHTML(false);

        return $mailer->send();
    } catch (PHPMailerException $exception) {
        $failureReason = 'PHPMailer SMTP error: ' . trim($mailer->ErrorInfo !== '' ? $mailer->ErrorInfo : $exception->getMessage());
        return false;
    }
}

function sendMailViaPhpMail(string $toEmail, string $fromEmail, string $fromName, string $replyTo, string $subject, string $body, ?string &$failureReason = null): bool
{
    $mailer = new PHPMailer(true);

    try {
        $mailer->isMail();
        $mailer->CharSet = PHPMailer::CHARSET_UTF8;
        $mailer->Encoding = PHPMailer::ENCODING_8BIT;

        $mailer->setFrom($fromEmail, $fromName);
        $mailer->addAddress($toEmail);
        $mailer->addReplyTo($replyTo);

        $mailer->Subject = $subject;
        $mailer->Body = $body;
        $mailer->isHTML(false);

        return $mailer->send();
    } catch (PHPMailerException $exception) {
        $failureReason = 'PHPMailer mail() fallback error: ' . trim($mailer->ErrorInfo !== '' ? $mailer->ErrorInfo : $exception->getMessage());
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Metodo nao permitido.';
    exit;
}

$env = loadEnvFile(__DIR__ . '/.env');
$redirect = trim((string) ($_POST['redirect'] ?? '/'));
if ($redirect === '' || !str_starts_with($redirect, '/')) {
    $redirect = '/';
}

$honeypot = trim((string) ($_POST['website'] ?? ''));
if ($honeypot !== '') {
    safeRedirect($redirect, true);
}

$nome = trim((string) ($_POST['nome'] ?? ''));
$emailRaw = trim((string) ($_POST['email'] ?? ''));
$email = filter_var($emailRaw, FILTER_VALIDATE_EMAIL) ? $emailRaw : '';
$telefone = trim((string) ($_POST['telefone'] ?? ($_POST['whatsapp'] ?? '')));
$empresa = trim((string) ($_POST['empresa'] ?? ''));
$interesse = trim((string) ($_POST['interesse'] ?? ($_POST['servico'] ?? 'Nao informado')));
$mensagem = trim((string) ($_POST['mensagem'] ?? ''));
$origem = trim((string) ($_POST['origem'] ?? 'coderush-hub'));

/*
 * Barreiras portadas do enviar-contato.php do SVD em 01/10/2026, durante ataque.
 *
 * Este arquivo atende a home do CodeRush, o CodaFacil e o FluxoInteligente, e
 * tinha so o honeypot — as outras duas barreiras foram adicionadas ao SVD em
 * 10/09 e nunca vieram pra ca. Resultado: uma varredura de SQL injection mandou
 * 848 requisicoes e 43 e-mails passaram, porque o payload nao preenche o campo
 * invisivel e nao parece link.
 *
 * Nenhuma injecao funcionou (nada aqui monta SQL), mas o canal de e-mail virou
 * megafone do atacante. A licao e que endurecer um formulario e nao os irmaos
 * so muda por onde entram.
 *
 * Responde sucesso em vez de erro de proposito: bot que recebe 4xx tenta de novo
 * com variacao; recebendo 200, acha que funcionou e vai embora.
 */
$registraBloqueio = static function (string $motivo, string $amostra): void {
    @file_put_contents(__DIR__ . '/storage/spam-bloqueado.log',
        sprintf("[%s] %s: %s | %s | %s\n", date('c'), $motivo, mb_substr($amostra, 0, 80),
            $_SERVER['REMOTE_ADDR'] ?? '-',
            mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 60)),
        FILE_APPEND);
};

// 1. Link onde vai o nome: nenhum cliente escreve URL ali.
$pareceLink = static fn (string $v): bool =>
    (bool) preg_match('~(https?://|www\.|\.com|\.org|\.net|\.ru|\.xyz|t\.me/|bit\.ly)~i', $v);

if ($pareceLink($nome) || ($pareceLink($mensagem) && mb_strlen($mensagem) < 40)) {
    $registraBloqueio('link no nome', $nome);
    safeRedirect($redirect, true);
}

// 2. Sintaxe de SQL/script em campo de texto. Cliente nenhum escreve SELECT no
//    nome; foi exatamente o que chegou nos 43 e-mails de 01/10.
$pareceAtaque = static fn (string $v): bool =>
    (bool) preg_match('~(\bselect\b|\bunion\b|\bdrop\b|\binsert\s+into\b|\bupdate\b.{0,20}\bset\b'
        . '|\bor\b[\s(\'"]*\w+[\s\'")]*=[\s(\'"]*\w+'
        . '|;\s*\w+\s*\(|\bsleep\s*\(|\bbenchmark\s*\(|\bwaitfor\s+delay\b|\bpg_sleep\b'
        . '|<script|javascript:|\bonerror\s*=|--\s*$|/\*.*\*/)~i', $v);

foreach ([$nome, $email, $telefone, $empresa, $mensagem] as $campo) {
    if ($campo !== '' && $pareceAtaque($campo)) {
        $registraBloqueio('sintaxe de ataque', $campo);
        safeRedirect($redirect, true);
    }
}

// 3. Telefone brasileiro: 10 ou 11 digitos com DDD, ou 12-13 comecando por 55.
$telDigits = preg_replace('/\D+/', '', $telefone);
$telValido = $telDigits === ''
    || strlen($telDigits) === 10 || strlen($telDigits) === 11
    || (str_starts_with($telDigits, '55') && strlen($telDigits) >= 12 && strlen($telDigits) <= 13);
if (!$telValido) {
    $registraBloqueio('telefone invalido', $telDigits);
    safeRedirect($redirect, true);
}

if ($nome === '') {
    $nome = 'Nao informado';
}

if ($mensagem === '') {
    $mensagem = 'Lead enviado pelo formulario de contato.';
}

if ($telefone === '' && $email === '') {
    safeRedirect($redirect, false);
}

$toEmail = envValue($env, ['MAIL_TO_ADDRESS', 'CONTACT_EMAIL_TO'], 'contato@coderush.com.br');

$defaultHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
$fromEmail = envValue($env, ['MAIL_FROM_ADDRESS', 'CONTACT_EMAIL_FROM'], 'no-reply@' . $defaultHost);
$fromName = envValue($env, ['MAIL_FROM_NAME', 'APP_NAME'], 'CodeRush Hub');

$subject = '[CodeRush Hub] ' . $interesse . ' - ' . $nome;
$body = implode("\n", [
    'Novo lead - CodeRush Hub',
    '',
    'Origem: ' . $origem,
    'Interesse: ' . $interesse,
    'Nome: ' . $nome,
    'Empresa: ' . ($empresa !== '' ? $empresa : 'Nao informado'),
    'Email: ' . ($email !== '' ? $email : 'Nao informado'),
    'Telefone/WhatsApp: ' . ($telefone !== '' ? $telefone : 'Nao informado'),
    '',
    'Mensagem:',
    $mensagem,
    '',
    'IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'desconhecido'),
    'Data: ' . date('Y-m-d H:i:s'),
]);

$replyTo = $email !== '' ? $email : $fromEmail;

$smtpConfig = [
    'host' => envValue($env, ['MAIL_HOST', 'SMTP_HOST']),
    'port' => envValue($env, ['MAIL_PORT', 'SMTP_PORT'], '587'),
    'username' => envValue($env, ['MAIL_USERNAME', 'SMTP_USERNAME']),
    'password' => envValue($env, ['MAIL_PASSWORD', 'SMTP_PASSWORD']),
    'encryption' => envValue($env, ['MAIL_ENCRYPTION', 'SMTP_ENCRYPTION'], 'tls'),
];

$leadPayload = [
    'origem' => $origem,
    'nome' => $nome,
    'email' => $email !== '' ? $email : null,
    'telefone' => $telefone !== '' ? $telefone : null,
    'empresa' => $empresa !== '' ? $empresa : null,
    'interesse' => $interesse,
    'mensagem' => $mensagem,
    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'desconhecido',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'desconhecido',
];

$transportFailureReason = '';
$sent = sendMailWithPHPMailer($smtpConfig, $fromEmail, $fromName, $toEmail, $replyTo, $subject, $body, $transportFailureReason);
$savedLocally = false;
if ($sent === false) {
    $savedLocally = persistLeadLocally(
        __DIR__,
        $leadPayload,
        'SMTP failed: ' . ($transportFailureReason !== '' ? $transportFailureReason : 'Unknown mail transport failure.')
    );

    $phpMailFailureReason = '';
    $sent = sendMailViaPhpMail($toEmail, $fromEmail, $fromName, $replyTo, $subject, $body, $phpMailFailureReason);

    if ($sent === false && $phpMailFailureReason !== '') {
        if ($savedLocally === true) {
            appendLineToFile(
                __DIR__ . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'contact-errors.log',
                sprintf('[%s] %s', date('c'), $phpMailFailureReason)
            );
        } else {
            $savedLocally = persistLeadLocally(__DIR__, $leadPayload, $phpMailFailureReason);
        }
    }
}

safeRedirect($redirect, $sent || $savedLocally);
