<?php
declare(strict_types=1);

/*
[Modulo Contato — CodeRush Hub]
Endpoint na raiz do hub: recebe o formulario de contato e envia e-mail via SMTP do .env (mesmo padrao do SVD).
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

function smtpCommand($socket, string $command, array $expectedCodes, ?string &$failureReason = null): bool
{
    fwrite($socket, $command . "\r\n");
    $response = smtpReadResponse($socket);
    $code = (int) substr($response, 0, 3);

    if (in_array($code, $expectedCodes, true)) {
        return true;
    }

    $failureReason = sprintf(
        'SMTP command failed [%s], expected %s, got %d (%s)',
        preg_replace('/\s+/', ' ', $command) ?? $command,
        implode(',', $expectedCodes),
        $code,
        trim($response)
    );

    return false;
}

function sendMailViaSmtp(array $smtpConfig, string $fromEmail, string $fromName, string $toEmail, string $replyTo, string $subject, string $body, ?string &$failureReason = null): bool
{
    $host = $smtpConfig['host'];
    $port = (int) $smtpConfig['port'];
    $username = $smtpConfig['username'];
    $password = $smtpConfig['password'];
    $encryption = strtolower($smtpConfig['encryption']);

    if ($host === '' || $port <= 0 || $username === '' || $password === '') {
        $failureReason = 'SMTP config incomplete.';

        return false;
    }

    $scheme = $encryption === 'ssl' ? 'ssl://' : 'tcp://';
    $socket = @stream_socket_client($scheme . $host . ':' . $port, $errorCode, $errorMessage, 15);
    if ($socket === false) {
        $failureReason = sprintf('SMTP connection failed: [%s] %s', (string) $errorCode, (string) $errorMessage);

        return false;
    }

    stream_set_timeout($socket, 15);

    $greeting = smtpReadResponse($socket);
    if ((int) substr($greeting, 0, 3) !== 220) {
        $failureReason = 'SMTP greeting invalid: ' . trim($greeting);
        fclose($socket);

        return false;
    }

    $hostname = gethostname() ?: 'localhost';
    if (!smtpCommand($socket, 'EHLO ' . $hostname, [250], $failureReason)) {
        fclose($socket);

        return false;
    }

    if ($encryption === 'tls') {
        if (!smtpCommand($socket, 'STARTTLS', [220], $failureReason)) {
            fclose($socket);

            return false;
        }

        $cryptoEnabled = stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        if ($cryptoEnabled !== true) {
            $failureReason = 'SMTP STARTTLS negotiation failed.';
            fclose($socket);

            return false;
        }

        if (!smtpCommand($socket, 'EHLO ' . $hostname, [250], $failureReason)) {
            fclose($socket);

            return false;
        }
    }

    if (!smtpCommand($socket, 'AUTH LOGIN', [334], $failureReason)) {
        fclose($socket);

        return false;
    }
    if (!smtpCommand($socket, base64_encode($username), [334], $failureReason)) {
        fclose($socket);

        return false;
    }
    if (!smtpCommand($socket, base64_encode($password), [235], $failureReason)) {
        fclose($socket);

        return false;
    }

    if (!smtpCommand($socket, 'MAIL FROM:<' . $fromEmail . '>', [250], $failureReason)) {
        fclose($socket);

        return false;
    }
    if (!smtpCommand($socket, 'RCPT TO:<' . $toEmail . '>', [250, 251], $failureReason)) {
        fclose($socket);

        return false;
    }
    if (!smtpCommand($socket, 'DATA', [354], $failureReason)) {
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
    if ($ok !== true) {
        $failureReason = 'SMTP DATA failed: ' . trim($dataResponse);
    }

    smtpCommand($socket, 'QUIT', [221, 250], $failureReason);
    fclose($socket);

    return $ok;
}

function sendMailViaPhpMail(string $toEmail, string $fromEmail, string $fromName, string $replyTo, string $subject, string $body, ?string &$failureReason = null): bool
{
    $subjectLine = function_exists('mb_encode_mimeheader')
        ? mb_encode_mimeheader($subject, 'UTF-8')
        : $subject;

    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';
    $headers[] = 'From: ' . $fromName . ' <' . $fromEmail . '>';
    $headers[] = 'Reply-To: ' . $replyTo;

    if (function_exists('error_clear_last')) {
        error_clear_last();
    }

    $sent = mail($toEmail, $subjectLine, $body, implode("\r\n", $headers));
    if ($sent !== true) {
        $lastError = error_get_last();
        $failureReason = 'mail() fallback failed' . ($lastError !== null && isset($lastError['message']) ? ': ' . $lastError['message'] : '.');
    }

    return $sent;
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

$consent = isset($_POST['consent']) && (string) $_POST['consent'] === '1';
if ($consent !== true) {
    safeRedirect($redirect, false);
}

$nome = trim((string) ($_POST['nome'] ?? ''));
$emailRaw = trim((string) ($_POST['email'] ?? ''));
$email = filter_var($emailRaw, FILTER_VALIDATE_EMAIL) ? $emailRaw : '';
$telefone = trim((string) ($_POST['telefone'] ?? ''));
$empresa = trim((string) ($_POST['empresa'] ?? ''));
$interesse = trim((string) ($_POST['interesse'] ?? 'Nao informado'));
$mensagem = trim((string) ($_POST['mensagem'] ?? ''));
$origem = trim((string) ($_POST['origem'] ?? 'coderush-hub'));

if ($nome === '' || $email === '') {
    safeRedirect($redirect, false);
}

$len = function_exists('mb_strlen') ? mb_strlen($mensagem) : strlen($mensagem);
if ($len < 10) {
    safeRedirect($redirect, false);
}

$toEmail = envValue($env, ['MAIL_TO_ADDRESS', 'MAIL_NEW_ORDER_INTERNAL_TO', 'HUB_MAIL_TO'], 'contato@coderush.com.br');

$defaultHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
$fromEmail = envValue($env, ['MAIL_FROM_ADDRESS', 'CONTACT_EMAIL_FROM'], 'no-reply@' . $defaultHost);
$fromName = envValue($env, ['MAIL_FROM_NAME', 'APP_NAME'], 'CodeRush Hub');

$subject = '[CodeRush Hub] ' . $interesse . ' — ' . $nome;
$body = implode("\n", [
    'Novo lead — CodeRush Hub',
    '',
    'Origem: ' . $origem,
    'Interesse: ' . $interesse,
    'Nome: ' . $nome,
    'Empresa: ' . ($empresa !== '' ? $empresa : 'Nao informado'),
    'Email: ' . $email,
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
    'email' => $email,
    'telefone' => $telefone !== '' ? $telefone : null,
    'empresa' => $empresa !== '' ? $empresa : null,
    'interesse' => $interesse,
    'mensagem' => $mensagem,
    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'desconhecido',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'desconhecido',
];

$transportFailureReason = '';
$sent = sendMailViaSmtp($smtpConfig, $fromEmail, $fromName, $toEmail, $replyTo, $subject, $body, $transportFailureReason);
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
