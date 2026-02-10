<?php

declare(strict_types=1);

/*
[Modulo Landing Page Comercial SVD]
@Author: André Gomes ( @acidcode )
@since 2026-02-06
Envia telefone/whatsapp do formulario por SMTP usando credenciais do arquivo .env.
*/

const RESPONSE_TEMPLATE = <<<'HTML'
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{TITLE}}</title>
  <style>
    body {
      margin: 0;
      min-height: 100vh;
      display: grid;
      place-items: center;
      background: #004aad;
      color: #ffffff;
      font-family: Arial, sans-serif;
      padding: 24px;
      box-sizing: border-box;
    }
    .box {
      width: min(560px, 100%);
      border: 1px solid rgba(255, 255, 255, 0.35);
      border-radius: 16px;
      padding: 24px;
      background: rgba(255, 255, 255, 0.08);
      text-align: center;
    }
    a {
      color: #ffffff;
      text-decoration: underline;
      font-weight: 600;
    }
  </style>
</head>
<body>
  <section class="box">
    <h1>{{HEADING}}</h1>
    <p>{{MESSAGE}}</p>
    <p><a href="index.html#contato">Voltar para o formulario</a></p>
  </section>
</body>
</html>
HTML;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Metodo nao permitido.', 405);
}

$env = loadEnv(__DIR__ . DIRECTORY_SEPARATOR . '.env');
$phone = sanitizePhone((string) ($_POST['whatsapp'] ?? ''));

if ($phone === '') {
    respond(false, 'Informe um telefone ou WhatsApp valido.', 422);
}

$smtpHost = $env['MAIL_HOST'] ?? '';
$smtpPort = (int) ($env['MAIL_PORT'] ?? 0);
$smtpEncryption = strtolower(trim((string) ($env['MAIL_ENCRYPTION'] ?? '')));
$smtpUsername = trim((string) ($env['MAIL_USERNAME'] ?? ''));
$smtpPassword = (string) ($env['MAIL_PASSWORD'] ?? '');
$fromAddress = trim((string) ($env['MAIL_FROM_ADDRESS'] ?? ''));
$fromName = trim((string) ($env['MAIL_FROM_NAME'] ?? 'Sistema Venda Direta'));
$replyToAddress = trim((string) ($env['MAIL_REPLY_TO_ADDRESS'] ?? ''));
$replyToName = trim((string) ($env['MAIL_REPLY_TO_NAME'] ?? $fromName));

$toAddress = trim((string) (
    $env['MAIL_TO_ADDRESS']
    ?? $env['MAIL_REPLY_TO_ADDRESS']
    ?? $env['MAIL_CC_TO_ADDRESS']
    ?? $env['MAIL_FROM_ADDRESS']
    ?? ''
));

if ($smtpHost === '' || $smtpPort <= 0 || $fromAddress === '' || $toAddress === '') {
    respond(false, 'Configuracao de e-mail incompleta no arquivo .env.', 500);
}

$timestamp = (new DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d H:i:s');
$remoteIp = (string) ($_SERVER['REMOTE_ADDR'] ?? 'desconhecido');
$userAgent = (string) ($_SERVER['HTTP_USER_AGENT'] ?? 'desconhecido');
$subject = 'Novo contato de telefone - Sistema Venda Direta';
$body = implode("\r\n", [
    'Novo telefone recebido pelo formulario do site.',
    '',
    'Telefone/WhatsApp: ' . $phone,
    'Data (America/Sao_Paulo): ' . $timestamp,
    'IP: ' . $remoteIp,
    'User-Agent: ' . $userAgent,
]);

try {
    sendViaSmtp(
        host: $smtpHost,
        port: $smtpPort,
        encryption: $smtpEncryption,
        username: $smtpUsername,
        password: $smtpPassword,
        fromAddress: $fromAddress,
        fromName: $fromName,
        toAddress: $toAddress,
        replyToAddress: $replyToAddress,
        replyToName: $replyToName,
        subject: $subject,
        body: $body,
    );
} catch (Throwable $throwable) {
    respond(false, 'Nao foi possivel enviar o e-mail. Tente novamente em alguns minutos.', 500);
}

respond(true, 'Telefone enviado com sucesso. Nossa equipe entrara em contato.');

function loadEnv(string $filePath): array
{
    if (!is_file($filePath)) {
        return [];
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return [];
    }

    $environment = [];

    foreach ($lines as $line) {
        $trimmed = trim($line);

        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }

        $separatorPosition = strpos($line, '=');
        if ($separatorPosition === false) {
            continue;
        }

        $key = trim(substr($line, 0, $separatorPosition));
        $value = trim(substr($line, $separatorPosition + 1));

        if ($key === '') {
            continue;
        }

        $environment[$key] = normalizeEnvValue($value);
    }

    return $environment;
}

function normalizeEnvValue(string $value): string
{
    if ($value === '') {
        return '';
    }

    $first = $value[0];
    $last = $value[strlen($value) - 1];

    if (($first === '"' || $first === "'") && $first === $last) {
        $value = substr($value, 1, -1);
    }

    return str_replace(
        ['\\n', '\\r', '\\t'],
        ["\n", "\r", "\t"],
        $value
    );
}

function sanitizePhone(string $phone): string
{
    $trimmed = trim($phone);

    if ($trimmed === '') {
        return '';
    }

    $normalized = preg_replace('/[^0-9\+\-\(\)\s]/', '', $trimmed);
    if (!is_string($normalized)) {
        return '';
    }

    return trim($normalized);
}

function respond(bool $success, string $message, int $statusCode = 200): never
{
    http_response_code($statusCode);

    $acceptHeader = strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? ''));
    $wantsJson = str_contains($acceptHeader, 'application/json');

    if ($wantsJson) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(
            [
                'success' => $success,
                'message' => $message,
            ],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        exit;
    }

    header('Content-Type: text/html; charset=UTF-8');

    $title = $success ? 'Envio confirmado' : 'Falha no envio';
    $heading = $success ? 'Contato recebido' : 'Falha ao enviar';

    echo strtr(RESPONSE_TEMPLATE, [
        '{{TITLE}}' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
        '{{HEADING}}' => htmlspecialchars($heading, ENT_QUOTES, 'UTF-8'),
        '{{MESSAGE}}' => htmlspecialchars($message, ENT_QUOTES, 'UTF-8'),
    ]);

    exit;
}

function sendViaSmtp(
    string $host,
    int $port,
    string $encryption,
    string $username,
    string $password,
    string $fromAddress,
    string $fromName,
    string $toAddress,
    string $replyToAddress,
    string $replyToName,
    string $subject,
    string $body
): void {
    $transportHost = $encryption === 'ssl' ? 'ssl://' . $host : $host;

    $socket = @stream_socket_client(
        $transportHost . ':' . $port,
        $errorCode,
        $errorMessage,
        15,
        STREAM_CLIENT_CONNECT
    );

    if (!is_resource($socket)) {
        throw new RuntimeException(
            'Falha na conexao SMTP (' . $errorCode . '): ' . $errorMessage
        );
    }

    stream_set_timeout($socket, 15);

    smtpExpect($socket, [220]);

    $clientName = gethostname();
    if (!is_string($clientName) || $clientName === '') {
        $clientName = 'localhost';
    }

    smtpWrite($socket, 'EHLO ' . $clientName);
    smtpExpect($socket, [250]);

    if ($encryption === 'tls') {
        smtpWrite($socket, 'STARTTLS');
        smtpExpect($socket, [220]);

        $cryptoEnabled = stream_socket_enable_crypto(
            $socket,
            true,
            STREAM_CRYPTO_METHOD_TLS_CLIENT
        );

        if ($cryptoEnabled !== true) {
            throw new RuntimeException('Nao foi possivel iniciar TLS no SMTP.');
        }

        smtpWrite($socket, 'EHLO ' . $clientName);
        smtpExpect($socket, [250]);
    }

    if ($username !== '') {
        smtpWrite($socket, 'AUTH LOGIN');
        smtpExpect($socket, [334]);

        smtpWrite($socket, base64_encode($username));
        smtpExpect($socket, [334]);

        smtpWrite($socket, base64_encode($password));
        smtpExpect($socket, [235]);
    }

    smtpWrite($socket, 'MAIL FROM:<' . $fromAddress . '>');
    smtpExpect($socket, [250]);

    smtpWrite($socket, 'RCPT TO:<' . $toAddress . '>');
    smtpExpect($socket, [250, 251]);

    smtpWrite($socket, 'DATA');
    smtpExpect($socket, [354]);

    $encodedSubject = encodeHeader($subject);
    $encodedFromName = encodeHeader($fromName === '' ? 'Site' : $fromName);
    $encodedReplyToName = encodeHeader($replyToName === '' ? $fromName : $replyToName);

    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
        'From: ' . $encodedFromName . ' <' . $fromAddress . '>',
        'To: <' . $toAddress . '>',
        'Subject: ' . $encodedSubject,
    ];

    if ($replyToAddress !== '') {
        $headers[] = 'Reply-To: ' . $encodedReplyToName . ' <' . $replyToAddress . '>';
    }

    $payload = implode("\r\n", $headers) . "\r\n\r\n" . normalizeBody($body);
    $payload = preg_replace('/(?m)^\./', '..', $payload);

    if (!is_string($payload)) {
        throw new RuntimeException('Falha ao preparar payload do e-mail.');
    }

    fwrite($socket, $payload . "\r\n.\r\n");
    smtpExpect($socket, [250]);

    smtpWrite($socket, 'QUIT');
    smtpExpect($socket, [221]);

    fclose($socket);
}

function normalizeBody(string $body): string
{
    $withUnixLineBreaks = str_replace(["\r\n", "\r"], "\n", $body);

    return str_replace("\n", "\r\n", $withUnixLineBreaks);
}

function encodeHeader(string $value): string
{
    if (function_exists('mb_encode_mimeheader')) {
        return mb_encode_mimeheader($value, 'UTF-8', 'B', "\r\n");
    }

    return $value;
}

function smtpWrite($socket, string $command): void
{
    fwrite($socket, $command . "\r\n");
}

function smtpExpect($socket, array $expectedCodes): string
{
    $response = smtpRead($socket);
    $statusCode = (int) substr($response, 0, 3);

    if (!in_array($statusCode, $expectedCodes, true)) {
        throw new RuntimeException(
            'Resposta SMTP inesperada: ' . $response
        );
    }

    return $response;
}

function smtpRead($socket): string
{
    $response = '';

    while (($line = fgets($socket, 515)) !== false) {
        $response .= $line;

        if (strlen($line) < 4 || $line[3] === ' ') {
            break;
        }
    }

    if ($response === '') {
        throw new RuntimeException('Servidor SMTP nao respondeu.');
    }

    return trim($response);
}
