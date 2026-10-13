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

    // A atribuicao chega do JS lendo o sessionStorage — mas isso falha em WebView
    // (link aberto dentro de app), onde o sessionStorage e isolado ou bloqueado.
    // Foi o que aconteceu com o lead #6 em 26/08: veio de clique pago, com gclid
    // na URL, e gravou tudo vazio. Sem gclid nao da pra mandar a conversao de
    // volta ao Google — a venda deixa de ensinar qual busca gerou negocio.
    //
    // A page_url sempre chega e carrega os mesmos parametros. Extrair dela e o
    // caminho que nao depende de JS, de storage nem de navegador.
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

    /*
     * Deteccao de rajada — 13/10/2026.
     *
     * Entre 21h de 11/10 e 02h50 de 12/10 entraram 18 cliques de WhatsApp, um a
     * cada 1 a 40 minutos, madrugada adentro. Nenhum virou mensagem ou e-mail.
     * No GA4 daqueles dias: 70 usos do simulador para 79 pageviews, 6 scrolls,
     * 16 segundos de sessao media e visitante novo em 67 das 74 sessoes. E
     * navegador de verdade dirigido por script, nao gente.
     *
     * O limiar vem do historico, nao de palpite. Nos 18 leads legitimos
     * anteriores ao pico, o maximo em uma hora foi 2. No pico chegou a 8.
     * Barrar a partir do terceiro em 60 minutos nao teria dado um falso
     * positivo sequer — e teria cortado a rajada pela metade.
     *
     * NAO DESCARTA O REGISTRO. Grava com status "robo": sai do funil e dos
     * totais do painel, mas fica para estudar o padrao. Jogar fora a unica
     * amostra que temos seria perder a base da proxima deteccao.
     *
     * O IP seria o sinal mais forte e nao esta disponivel: o nginx registra o
     * endereco do Cloudflare e o PHP recebe o gateway do Docker (172.21.0.1 em
     * todo lead desde agosto). Enquanto isso nao for corrigido, a frequencia e
     * o que sobra.
     */
    $janela = $pdo->prepare(
        "SELECT COUNT(*) FROM leads
          WHERE status IN ('zap','robo')
            AND created_at >= datetime('now', '-60 minutes')"
    );
    $janela->execute();
    $statusLead = ((int) $janela->fetchColumn()) >= 3 ? 'robo' : 'zap';

    $stmt = $pdo->prepare('INSERT INTO leads (
        created_at, nome, origem, servico, mensagem,
        ga_client_id, gclid, utm_source, utm_medium, utm_campaign, utm_content,
        sim_faturamento, page_url, ip, user_agent, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        date('c'),
        '(clique WhatsApp)',
        $field('origem', 100) ?? 'site',
        'WhatsApp direto',
        $refTag,
        $field('ga_client_id', 64),
        $attr('gclid'),
        $attr('utm_source', 100),
        $attr('utm_medium', 100),
        $attr('utm_campaign', 150),
        $attr('utm_content', 150),
        $field('sim_faturamento', 40),
        $field('page_url', 500),
        $_SERVER['REMOTE_ADDR'] ?? null,
        mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 300),
        $statusLead,
    ]);
    http_response_code(201);
} catch (Throwable $e) {
    http_response_code(500);
}
