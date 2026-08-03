<?php
declare(strict_types=1);

/*
[Modulo Leads SVD — clique de WhatsApp]
@Author: André Gomes ( @acidcode )
@since 2026-08-03
Recebe um beacon no clique dos botoes de WhatsApp das LPs e grava o lead de
intencao no leads.sqlite (status "zap"), com atribuicao completa e um codigo
de referencia [ref XXXXX] que tambem vai embutido na mensagem do wa.me —
permitindo casar a conversa do WhatsApp com a campanha de origem.
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$ref = strtoupper(trim((string) ($_POST['ref'] ?? '')));
if (!preg_match('/^[A-Z0-9]{5}$/', $ref)) {
    http_response_code(422);
    exit;
}

$field = static function (string $key, int $max = 300): ?string {
    $v = trim((string) ($_POST[$key] ?? ''));
    return $v === '' ? null : mb_substr($v, 0, $max);
};

$storageDir = __DIR__ . '/storage';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0775, true);
}

try {
    $pdo = new PDO('sqlite:' . $storageDir . '/leads.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // mesma tabela do enviar-contato.php (cria se o form nunca rodou)
    $pdo->exec('CREATE TABLE IF NOT EXISTS leads (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        created_at TEXT NOT NULL,
        nome TEXT, email TEXT, telefone TEXT, telefone_digits TEXT,
        origem TEXT, servico TEXT, mensagem TEXT,
        ga_client_id TEXT, gclid TEXT,
        utm_source TEXT, utm_medium TEXT, utm_campaign TEXT, utm_content TEXT,
        sim_faturamento TEXT, page_url TEXT, ip TEXT, user_agent TEXT,
        status TEXT NOT NULL DEFAULT "novo",
        closed_at TEXT, close_value REAL, transaction_id TEXT
    )');

    // 1 registro por ref (cliques repetidos na mesma sessao nao duplicam)
    $exists = $pdo->prepare('SELECT id FROM leads WHERE mensagem = ? LIMIT 1');
    $refTag = 'ref zap: ' . $ref;
    $exists->execute([$refTag]);
    if ($exists->fetch()) {
        http_response_code(200);
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO leads (
        created_at, nome, origem, servico, mensagem,
        ga_client_id, gclid, utm_source, utm_medium, utm_campaign, utm_content,
        sim_faturamento, page_url, ip, user_agent, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "zap")');
    $stmt->execute([
        date('c'),
        '(clique WhatsApp)',
        $field('origem', 100) ?? 'site',
        'WhatsApp direto',
        $refTag,
        $field('ga_client_id', 64),
        $field('gclid'),
        $field('utm_source', 100),
        $field('utm_medium', 100),
        $field('utm_campaign', 150),
        $field('utm_content', 150),
        $field('sim_faturamento', 40),
        $field('page_url', 500),
        $_SERVER['REMOTE_ADDR'] ?? null,
        mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 300),
    ]);
    http_response_code(201);
} catch (Throwable $e) {
    http_response_code(500);
}
