<?php
declare(strict_types=1);

/*
[Modulo Leads SVD — registrar venda (purchase offline)]
@Author: André Gomes ( @acidcode )
@since 2026-08-03
Fecha o funil de ROI: encontra o lead no leads.sqlite e dispara um evento `purchase`
pro GA4 via Measurement Protocol, amarrado ao client_id capturado no formulario.
A receita cai na campanha/grupo de origem do clique (Ads <-> GA4 vinculados).

Uso (no host):
  docker exec coderush-app php /var/www/html/automation/leads/registrar-venda.php \
    --whatsapp=11999998888 --valor=3000 [--descricao="Instalacao SVD"] [--dry-run]

  --whatsapp   telefone do lead (qualquer formato; casa pelos ultimos 8 digitos)
  --lead-id    alternativa ao whatsapp: id direto da tabela leads
  --valor      valor fechado em reais (ex.: 3000 ou 3500)
  --descricao  rotulo do item no purchase (default "Instalacao Sistema Venda Direta")
  --dry-run    valida o evento no endpoint de debug do GA sem gravar nada
Requer GA4_MP_SECRET_SVD no .env da raiz do repo.
*/

const MEASUREMENT_ID = 'G-4107EVTE0Q';
const DB_PATH = __DIR__ . '/../../sistemavendadireta/storage/leads.sqlite';
const ENV_PATH = __DIR__ . '/../../.env';

function argValue(array $argv, string $name): ?string
{
    foreach ($argv as $arg) {
        if (str_starts_with($arg, "--{$name}=")) {
            return substr($arg, strlen($name) + 3);
        }
        if ($arg === "--{$name}") {
            return '1';
        }
    }
    return null;
}

function envSecret(): string
{
    if (!is_file(ENV_PATH)) {
        fwrite(STDERR, ".env nao encontrado em " . ENV_PATH . "\n");
        exit(1);
    }
    foreach (file(ENV_PATH, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), 'GA4_MP_SECRET_SVD=')) {
            return trim(trim(explode('=', $line, 2)[1]), "\"'");
        }
    }
    fwrite(STDERR, "GA4_MP_SECRET_SVD ausente no .env\n");
    exit(1);
}

$whatsapp = argValue($argv, 'whatsapp');
$leadId = argValue($argv, 'lead-id');
$refCode = argValue($argv, 'ref');
$valor = argValue($argv, 'valor');
$descricao = argValue($argv, 'descricao') ?? 'Instalacao Sistema Venda Direta';
$dryRun = argValue($argv, 'dry-run') !== null;

if (($whatsapp === null && $leadId === null && $refCode === null) || $valor === null || !is_numeric($valor)) {
    fwrite(STDERR, "Uso: --whatsapp=DDDNUMERO (ou --lead-id=N, ou --ref=XXXXX do WhatsApp) --valor=3000 [--descricao=...] [--dry-run]\n");
    exit(1);
}
$valorFloat = (float) $valor;

$pdo = new PDO('sqlite:' . DB_PATH);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($leadId !== null) {
    $stmt = $pdo->prepare('SELECT * FROM leads WHERE id = ?');
    $stmt->execute([(int) $leadId]);
} elseif ($refCode !== null) {
    $stmt = $pdo->prepare('SELECT * FROM leads WHERE mensagem = ? ORDER BY id DESC LIMIT 1');
    $stmt->execute(['ref zap: ' . strtoupper(trim($refCode))]);
} else {
    $digits = preg_replace('/\D+/', '', $whatsapp);
    $suffix = substr($digits, -8);
    $stmt = $pdo->prepare(
        "SELECT * FROM leads WHERE telefone_digits LIKE ? ORDER BY id DESC LIMIT 1"
    );
    $stmt->execute(['%' . $suffix]);
}
$lead = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$lead) {
    fwrite(STDERR, "Nenhum lead encontrado com esse criterio.\n");
    exit(1);
}

echo "Lead #{$lead['id']} — {$lead['nome']} ({$lead['telefone']}) em {$lead['created_at']}\n";
echo "  origem: {$lead['origem']} | campanha: " . ($lead['utm_campaign'] ?? '-') . " | faixa simulada: " . ($lead['sim_faturamento'] ?? '-') . "\n";

if (!empty($lead['transaction_id']) && !$dryRun) {
    fwrite(STDERR, "Lead ja tem venda registrada (transaction {$lead['transaction_id']}). Abortando.\n");
    exit(1);
}

$clientId = $lead['ga_client_id'] ?? '';
$transactionId = 'svd-' . $lead['id'] . '-' . date('Ymd');

$eventPayload = [
    'client_id' => $clientId !== '' ? $clientId : ('offline.' . $lead['id']),
    'events' => [[
        'name' => 'purchase',
        'params' => [
            'transaction_id' => $transactionId,
            'value' => $valorFloat,
            'currency' => 'BRL',
            'items' => [[
                'item_id' => 'instalacao-svd',
                'item_name' => $descricao,
                'price' => $valorFloat,
                'quantity' => 1,
            ]],
            'origem_lead' => $lead['origem'] ?? '',
        ],
    ]],
];

if ($clientId === '') {
    echo "AVISO: lead sem ga_client_id (form antigo ou cookie bloqueado) — o purchase sera\n";
    echo "       gravado no GA sem amarrar a campanha de origem (aparece como offline).\n";
}

$secret = envSecret();
$host = 'https://www.google-analytics.com';
$paths = $dryRun ? ['/debug/mp/collect'] : ['/debug/mp/collect', '/mp/collect'];

foreach ($paths as $path) {
    $url = $host . $path . '?measurement_id=' . MEASUREMENT_ID . '&api_secret=' . urlencode($secret);
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($eventPayload, JSON_UNESCAPED_UNICODE),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
    ]);
    $body = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (str_contains($path, 'debug')) {
        $decoded = json_decode((string) $body, true);
        $messages = $decoded['validationMessages'] ?? null;
        if (!empty($messages)) {
            fwrite(STDERR, "Validacao GA falhou: " . json_encode($messages, JSON_UNESCAPED_UNICODE) . "\n");
            exit(1);
        }
        echo $dryRun ? "DRY-RUN ok — evento valido, nada gravado.\n" : "validacao ok\n";
    } else {
        if ($status < 200 || $status >= 300) {
            fwrite(STDERR, "MP retornou HTTP {$status}\n");
            exit(1);
        }
        echo "purchase ENVIADO: {$transactionId} | R$ " . number_format($valorFloat, 2, ',', '.') . "\n";
    }
}

if (!$dryRun) {
    $upd = $pdo->prepare('UPDATE leads SET status = ?, closed_at = ?, close_value = ?, transaction_id = ? WHERE id = ?');
    $upd->execute(['fechado', date('c'), $valorFloat, $transactionId, $lead['id']]);
    echo "lead #{$lead['id']} marcado como fechado no leads.sqlite\n";
    echo "A receita aparece no GA4 (Monetizacao / campanha de origem) em ate 24h.\n";
}
