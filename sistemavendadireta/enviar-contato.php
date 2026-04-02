<?php
declare(strict_types=1);

/*
[Modulo Contato SVD]
@Author: Andre Gomes ( @acidcode )
@since 2026-02-10
Endpoint para receber formularios de contato e enviar email usando credenciais do .env.
*/

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

function smtpReadResponse($socket): string
{
    $response = '';
    while (($line = fgets($socket, 515)) !== false) {
        $response .= $line;
        if (strlen($line) < 4) {
            break;
        }

        if ($line[3] === ' ') {
            break;
        }
    }

    return $response;
}

function smtpCommand($socket, string $command, array $expectedCodes): bool
{
    fwrite($socket, $command . "\r\n");
    $response = smtpReadResponse($socket);
    $code = (int) substr($response, 0, 3);

    return in_array($code, $expectedCodes, true);
}

function sendMailViaSmtp(array $smtpConfig, string $fromEmail, string $fromName, string $toEmail, string $replyTo, string $subject, string $body): bool
{
    $host = $smtpConfig['host'];
    $port = (int) $smtpConfig['port'];
    $username = $smtpConfig['username'];
    $password = $smtpConfig['password'];
    $encryption = strtolower($smtpConfig['encryption']);

    if ($host === '' || $port <= 0 || $username === '' || $password === '') {
        return false;
    }

    $scheme = $encryption === 'ssl' ? 'ssl://' : 'tcp://';
    $socket = @stream_socket_client($scheme . $host . ':' . $port, $errorCode, $errorMessage, 15);
    if ($socket === false) {
        return false;
    }

    stream_set_timeout($socket, 15);

    $greeting = smtpReadResponse($socket);
    if ((int) substr($greeting, 0, 3) !== 220) {
        fclose($socket);

        return false;
    }

    $hostname = gethostname() ?: 'localhost';
    if (!smtpCommand($socket, 'EHLO ' . $hostname, [250])) {
        fclose($socket);

        return false;
    }

    if ($encryption === 'tls') {
        if (!smtpCommand($socket, 'STARTTLS', [220])) {
            fclose($socket);

            return false;
        }

        $cryptoEnabled = stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        if ($cryptoEnabled !== true) {
            fclose($socket);

            return false;
        }

        if (!smtpCommand($socket, 'EHLO ' . $hostname, [250])) {
            fclose($socket);

            return false;
        }
    }

    if (!smtpCommand($socket, 'AUTH LOGIN', [334])) {
        fclose($socket);

        return false;
    }
    if (!smtpCommand($socket, base64_encode($username), [334])) {
        fclose($socket);

        return false;
    }
    if (!smtpCommand($socket, base64_encode($password), [235])) {
        fclose($socket);

        return false;
    }

    if (!smtpCommand($socket, 'MAIL FROM:<' . $fromEmail . '>', [250])) {
        fclose($socket);

        return false;
    }
    if (!smtpCommand($socket, 'RCPT TO:<' . $toEmail . '>', [250, 251])) {
        fclose($socket);

        return false;
    }
    if (!smtpCommand($socket, 'DATA', [354])) {
        fclose($socket);

        return false;
    }

    $subjectLine = function_exists('mb_encode_mimeheader')
        ? mb_encode_mimeheader($subject, 'UTF-8')
        : $subject;

    $headers = [];
    $headers[] = 'Date: ' . date(DATE_RFC2822);
    $headers[] = 'From: ' . $fromName . ' <' . $fromEmail . '>';
    $headers[] = 'Reply-To: ' . $replyTo;
    $headers[] = 'To: <' . $toEmail . '>';
    $headers[] = 'Subject: ' . $subjectLine;
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';
    $headers[] = 'Content-Transfer-Encoding: 8bit';

    $dotSafeBody = preg_replace('/^\./m', '..', $body) ?? $body;
    $message = implode("\r\n", $headers) . "\r\n\r\n" . $dotSafeBody . "\r\n.";
    fwrite($socket, $message . "\r\n");

    $dataResponse = smtpReadResponse($socket);
    $ok = (int) substr($dataResponse, 0, 3) === 250;

    smtpCommand($socket, 'QUIT', [221, 250]);
    fclose($socket);

    return $ok;
}

function sendMailViaPhpMail(string $toEmail, string $fromEmail, string $fromName, string $replyTo, string $subject, string $body): bool
{
    $subjectLine = function_exists('mb_encode_mimeheader')
        ? mb_encode_mimeheader($subject, 'UTF-8')
        : $subject;

    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';
    $headers[] = 'From: ' . $fromName . ' <' . $fromEmail . '>';
    $headers[] = 'Reply-To: ' . $replyTo;

    return mail($toEmail, $subjectLine, $body, implode("\r\n", $headers));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Metodo nao permitido.';
    exit;
}

$env = loadEnvFile(__DIR__ . '/.env');
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

$toEmail = envValue(
    $env,
    ['CONTACT_EMAIL_TO', 'MAIL_TO_ADDRESS', 'MAIL_FROM_ADDRESS', 'MAIL_USERNAME'],
    'contato@sistemavendadireta.com.br'
);

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

$sent = sendMailViaSmtp($smtpConfig, $fromEmail, $fromName, $toEmail, $replyTo, $subject, $body);
if ($sent === false) {
    $sent = sendMailViaPhpMail($toEmail, $fromEmail, $fromName, $replyTo, $subject, $body);
}

safeRedirect($redirect, $sent);
