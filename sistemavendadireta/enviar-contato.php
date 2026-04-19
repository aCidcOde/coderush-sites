<?php
declare(strict_types=1);

/*
[Modulo Contato SVD]
@Author: Andre Gomes ( @acidcode )
@since 2026-02-10
Endpoint para receber formularios de contato do site SVD e enviar e-mail via SMTP
usando PHPMailer. Reaproveita o autoload do Composer instalado na raiz do monorepo.
*/

require_once __DIR__ . '/../vendor/autoload.php';

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

function safeRedirect(string $location, bool $success): void
{
    $target = $location;
    if ($target === '' || !str_starts_with($target, '/')) {
        $target = '/';
    }

    $separator = str_contains($target, '?') ? '&' : '?';
    $status = $success ? 'ok' : 'erro';
    header('Location: ' . $target . $separator . 'mail=' . $status, true, 303);
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

$env = loadEnvFile(__DIR__ . '/../.env');
$redirect = trim((string) ($_POST['redirect'] ?? '/'));

$nome = trim((string) ($_POST['nome'] ?? ''));
$emailRaw = trim((string) ($_POST['email'] ?? ''));
$email = filter_var($emailRaw, FILTER_VALIDATE_EMAIL) ? $emailRaw : '';
$telefone = trim((string) ($_POST['telefone'] ?? ($_POST['whatsapp'] ?? '')));
$servico = trim((string) ($_POST['servico'] ?? 'Nao informado'));
$mensagem = trim((string) ($_POST['mensagem'] ?? ''));
$origem = trim((string) ($_POST['origem'] ?? 'site'));

if ($nome === '') {
    $nome = 'Nao informado';
}

if ($mensagem === '') {
    $mensagem = 'Contato enviado pelo formulario sem mensagem detalhada.';
}

if ($telefone === '' && $email === '') {
    safeRedirect($redirect, false);
}

$toEmail = envValue($env, ['MAIL_TO_ADDRESS', 'CONTACT_EMAIL_TO'], 'contato@coderush.com.br');

$defaultHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
$fromEmail = envValue($env, ['MAIL_FROM_ADDRESS', 'CONTACT_EMAIL_FROM'], 'no-reply@' . $defaultHost);
$fromName = envValue($env, ['MAIL_FROM_NAME', 'APP_NAME'], 'Site SVD');

$subject = '[SVD] Novo contato via formulario - ' . $origem;
$body = implode("\n", [
    'Novo contato recebido:',
    '',
    'Origem: ' . $origem,
    'Nome: ' . $nome,
    'Email: ' . ($email !== '' ? $email : 'Nao informado'),
    'Telefone: ' . ($telefone !== '' ? $telefone : 'Nao informado'),
    'Servico: ' . $servico,
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
    'servico' => $servico,
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
