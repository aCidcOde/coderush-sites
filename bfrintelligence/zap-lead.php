<?php
declare(strict_types=1);

/*
[Modulo Leads BFR — clique de WhatsApp]
@Author: André Gomes ( @acidcode )
@since 2026-09-12

Portado do zap-lead.php do SVD ao ligar a primeira campanha paga da BFR.

POR QUE: ate 12/09/2026 a home da BFR nao tinha WhatsApp nenhum (o numero so
aparecia nas paginas de termos e privacidade) e o unico caminho de conversao era
o formulario. No SVD as duas vendas fechadas vieram de clique no zap, nao de
formulario — mandar trafego pago pra uma pagina sem esse caminho seria pagar
clique caro e estreitar a conversao de proposito.

O codigo [ref XXXXX] vai embutido na mensagem do wa.me e gravado aqui: e o que
permite casar uma conversa de WhatsApp com a campanha que a originou. Sem ele o
lead chega dizendo "vi voces no Google" e a atribuicao morre ali.

ATRIBUICAO VEM DA URL, NAO DO STORAGE: o JS da home guarda gclid/UTM em
sessionStorage ('bfr-attribution'), mas em WebView — link aberto dentro do
Instagram, LinkedIn ou do proprio WhatsApp — o storage e isolado e volta vazio.
Foi exatamente assim que o lead #6 do SVD (venda de R$ 3.500, 26/08) gravou
atribuicao em branco vindo de clique pago. A page_url sempre chega e carrega os
mesmos parametros; extrair dela nao depende de JS, storage nem navegador.
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
    return $v === '' ? null : mb_substr(strip_tags($v), 0, $max);
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
        empresa TEXT, origem TEXT, mensagem TEXT,
        ga_client_id TEXT, gclid TEXT,
        utm_source TEXT, utm_medium TEXT, utm_campaign TEXT, utm_content TEXT,
        page_url TEXT, ip TEXT, user_agent TEXT,
        status TEXT NOT NULL DEFAULT "novo",
        closed_at TEXT, close_value REAL, transaction_id TEXT
    )');

    // 1 registro por ref: clique repetido na mesma sessao nao vira lead novo
    $refTag = 'ref zap: ' . $ref;
    $exists = $pdo->prepare('SELECT id FROM leads WHERE mensagem = ? LIMIT 1');
    $exists->execute([$refTag]);
    if ($exists->fetch()) {
        http_response_code(200);
        exit;
    }

    $daUrl = [];
    $urlBruta = $field('page_url', 500);
    if ($urlBruta) {
        $query = parse_url($urlBruta, PHP_URL_QUERY);
        if (is_string($query) && $query !== '') {
            parse_str($query, $daUrl);
        }
    }
    /** Valor enviado pelo JS; se vier vazio, cai pro que estava na URL. */
    $attr = static function (string $chave, int $max = 300) use ($field, $daUrl): ?string {
        $v = $field($chave, $max);
        if ($v !== null && $v !== '') {
            return $v;
        }
        $u = $daUrl[$chave] ?? '';
        return is_string($u) && $u !== '' ? mb_substr(trim(strip_tags($u)), 0, $max) : null;
    };

    $stmt = $pdo->prepare('INSERT INTO leads (
        created_at, nome, origem, mensagem,
        ga_client_id, gclid, utm_source, utm_medium, utm_campaign, utm_content,
        page_url, ip, user_agent, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "zap")');
    $stmt->execute([
        date('c'),
        '(clique WhatsApp)',
        $field('origem', 100) ?? 'site-bfr',
        $refTag,
        $field('ga_client_id', 64),
        $attr('gclid'),
        $attr('utm_source', 100),
        $attr('utm_medium', 100),
        $attr('utm_campaign', 150),
        $attr('utm_content', 150),
        $urlBruta,
        $_SERVER['REMOTE_ADDR'] ?? null,
        mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 300),
    ]);
    http_response_code(201);
} catch (Throwable $e) {
    http_response_code(500);
}
