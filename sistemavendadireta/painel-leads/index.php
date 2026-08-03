<?php
declare(strict_types=1);

/*
[Modulo Painel de Leads SVD]
@Author: André Gomes ( @acidcode )
@since 2026-08-03
Tela protegida por senha simples pra acompanhar o log de leads do leads.sqlite:
origem, campanha (UTM/gclid), faixa simulada e status de fechamento (ROI).
Senha em LEADS_PANEL_PASSWORD no .env da raiz. Sem senha configurada, nega acesso.
*/

session_start();

function envPassword(): string
{
    $path = __DIR__ . '/../../.env';
    if (!is_file($path)) {
        return '';
    }
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), 'LEADS_PANEL_PASSWORD=')) {
            return trim(trim(explode('=', $line, 2)[1]), "\"'");
        }
    }
    return '';
}

$expected = envPassword();
$authed = ($_SESSION['svd_leads_auth'] ?? false) === true;
$error = '';

if (isset($_POST['senha'])) {
    if ($expected !== '' && hash_equals($expected, (string) $_POST['senha'])) {
        $_SESSION['svd_leads_auth'] = true;
        header('Location: ./', true, 303);
        exit;
    }
    sleep(1); // desacelera forca bruta
    $error = 'Senha incorreta.';
}

if (isset($_GET['sair'])) {
    unset($_SESSION['svd_leads_auth']);
    header('Location: ./', true, 303);
    exit;
}

$authed = ($_SESSION['svd_leads_auth'] ?? false) === true;

$leads = [];
$totals = ['total' => 0, 'ultimos7' => 0, 'fechados' => 0, 'receita' => 0.0, 'zap' => 0];
$porOrigem = [];
$dbAviso = '';

if ($authed) {
    $dbPath = __DIR__ . '/../storage/leads.sqlite';
    if (!is_file($dbPath)) {
        $dbAviso = 'Ainda não há leads registrados (leads.sqlite não existe).';
    } else {
        try {
            $pdo = new PDO('sqlite:' . $dbPath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $leads = $pdo->query(
                'SELECT * FROM leads ORDER BY id DESC LIMIT 200'
            )->fetchAll(PDO::FETCH_ASSOC);

            $totals['total'] = (int) $pdo->query('SELECT COUNT(*) FROM leads WHERE status NOT IN ("teste","zap")')->fetchColumn();
            $totals['zap'] = (int) $pdo->query('SELECT COUNT(*) FROM leads WHERE status = "zap"')->fetchColumn();
            $totals['ultimos7'] = (int) $pdo->query(
                'SELECT COUNT(*) FROM leads WHERE status NOT IN ("teste","zap") AND created_at >= datetime("now", "-7 days")'
            )->fetchColumn();
            $totals['fechados'] = (int) $pdo->query('SELECT COUNT(*) FROM leads WHERE status = "fechado"')->fetchColumn();
            $totals['receita'] = (float) $pdo->query(
                'SELECT COALESCE(SUM(close_value), 0) FROM leads WHERE status = "fechado"'
            )->fetchColumn();

            foreach ($pdo->query(
                'SELECT COALESCE(origem, "sem origem") AS origem, COUNT(*) AS qtd
                 FROM leads WHERE status != "teste" GROUP BY origem ORDER BY qtd DESC'
            ) as $row) {
                $porOrigem[] = $row;
            }
        } catch (Throwable $e) {
            $dbAviso = 'Erro ao ler o banco de leads: ' . $e->getMessage();
        }
    }
}

function e(?string $v): string
{
    return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
}

function brDateTime(?string $iso): string
{
    if (!$iso) {
        return '-';
    }
    try {
        $dt = new DateTimeImmutable($iso);
        return $dt->setTimezone(new DateTimeZone('America/Sao_Paulo'))->format('d/m/Y H:i');
    } catch (Throwable $e) {
        return $iso;
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Painel de Leads | Sistema Venda Direta</title>
  <meta name="robots" content="noindex, nofollow" />
  <style>
    :root { color-scheme: dark; }
    * { box-sizing: border-box; margin: 0; }
    body { font-family: system-ui, -apple-system, sans-serif; background: #04173a; color: #fff; min-height: 100vh; }
    .wrap { max-width: 1240px; margin: 0 auto; padding: 24px 16px 60px; }
    h1 { font-size: 22px; margin-bottom: 4px; }
    .sub { color: rgba(255,255,255,.6); font-size: 13px; margin-bottom: 24px; }
    .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 24px; }
    .card { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.15); border-radius: 14px; padding: 14px 16px; }
    .card b { display: block; font-size: 26px; color: #fcd34d; }
    .card span { font-size: 12px; color: rgba(255,255,255,.65); text-transform: uppercase; letter-spacing: .08em; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    th, td { text-align: left; padding: 8px 10px; border-bottom: 1px solid rgba(255,255,255,.1); vertical-align: top; }
    th { color: rgba(255,255,255,.55); font-size: 11px; text-transform: uppercase; letter-spacing: .08em; position: sticky; top: 0; background: #04173a; }
    tr:hover td { background: rgba(255,255,255,.04); }
    .tag { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 11px; font-weight: 600; }
    .tag.novo { background: rgba(96,165,250,.2); color: #93c5fd; }
    .tag.fechado { background: rgba(52,211,153,.2); color: #6ee7b7; }
    .tag.teste { background: rgba(255,255,255,.12); color: rgba(255,255,255,.6); }
    .tag.zap { background: rgba(52,211,153,.15); color: #6ee7b7; }
    .muted { color: rgba(255,255,255,.5); }
    .scroll { overflow-x: auto; border: 1px solid rgba(255,255,255,.15); border-radius: 14px; }
    .login { max-width: 360px; margin: 12vh auto 0; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.2); border-radius: 18px; padding: 28px; }
    .login h1 { text-align: center; margin-bottom: 18px; }
    input[type=password] { width: 100%; padding: 12px 14px; border-radius: 10px; border: 1px solid rgba(255,255,255,.25); background: rgba(255,255,255,.08); color: #fff; font-size: 15px; }
    button { width: 100%; margin-top: 12px; padding: 12px; border: 0; border-radius: 10px; background: #fcd34d; color: #04173a; font-weight: 700; font-size: 14px; cursor: pointer; }
    .err { color: #fca5a5; font-size: 13px; margin-top: 10px; text-align: center; }
    .topbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
    .sair { color: rgba(255,255,255,.6); font-size: 13px; text-decoration: none; border: 1px solid rgba(255,255,255,.25); padding: 6px 14px; border-radius: 99px; }
    h2 { font-size: 15px; margin: 26px 0 10px; color: rgba(255,255,255,.85); }
  </style>
</head>
<body>
<?php if (!$authed): ?>
  <div class="login">
    <h1>Painel de Leads</h1>
    <form method="post">
      <input type="password" name="senha" placeholder="Senha" autofocus required />
      <button type="submit">Entrar</button>
      <?php if ($error !== ''): ?><p class="err"><?= e($error) ?></p><?php endif; ?>
      <?php if ($expected === ''): ?><p class="err">LEADS_PANEL_PASSWORD não configurada no .env.</p><?php endif; ?>
    </form>
  </div>
<?php else: ?>
  <div class="wrap">
    <div class="topbar">
      <div>
        <h1>Painel de Leads — Sistema Venda Direta</h1>
        <p class="sub">Fonte: storage/leads.sqlite · atualizado em tempo real a cada lead · horário de Brasília</p>
      </div>
      <a class="sair" href="?sair=1">Sair</a>
    </div>

    <div class="cards">
      <div class="card"><b><?= $totals['total'] ?></b><span>Leads no total</span></div>
      <div class="card"><b><?= $totals['ultimos7'] ?></b><span>Últimos 7 dias</span></div>
      <div class="card"><b><?= $totals['fechados'] ?></b><span>Vendas fechadas</span></div>
      <div class="card"><b><?= $totals['zap'] ?></b><span>Cliques WhatsApp</span></div>
      <div class="card"><b>R$ <?= number_format($totals['receita'], 0, ',', '.') ?></b><span>Receita registrada</span></div>
    </div>

    <?php if ($porOrigem): ?>
      <h2>Leads por origem</h2>
      <div class="scroll" style="margin-bottom:8px;">
        <table>
          <tr><th>Origem (LP/página)</th><th>Leads</th></tr>
          <?php foreach ($porOrigem as $row): ?>
            <tr><td><?= e($row['origem']) ?></td><td><?= (int) $row['qtd'] ?></td></tr>
          <?php endforeach; ?>
        </table>
      </div>
    <?php endif; ?>

    <h2>Últimos leads (até 200)</h2>
    <?php if ($dbAviso !== ''): ?>
      <p class="muted"><?= e($dbAviso) ?></p>
    <?php else: ?>
      <div class="scroll">
        <table>
          <tr>
            <th>#</th><th>Data</th><th>Nome</th><th>WhatsApp</th><th>Origem</th>
            <th>Campanha / conteúdo</th><th>Fonte</th><th>Faixa simulada</th>
            <th>Google Ads?</th><th>Status</th><th>Valor</th>
          </tr>
          <?php foreach ($leads as $lead): ?>
            <tr>
              <td class="muted"><?= (int) $lead['id'] ?></td>
              <td><?= e(brDateTime($lead['created_at'])) ?></td>
              <td><?= e($lead['nome']) ?><?= $lead['status']==='zap' && $lead['mensagem'] ? '<br /><span class="muted">' . e($lead['mensagem']) . '</span>' : '' ?></td>
              <td><?= e($lead['telefone']) ?></td>
              <td><?= e($lead['origem']) ?></td>
              <td><?= e($lead['utm_campaign'] ?: '-') ?><?= $lead['utm_content'] ? ' / ' . e($lead['utm_content']) : '' ?></td>
              <td><?= e($lead['utm_source'] ?: 'direto/orgânico') ?></td>
              <td><?= e($lead['sim_faturamento'] ?: '-') ?></td>
              <td><?= $lead['gclid'] ? 'sim' : '-' ?></td>
              <td><span class="tag <?= e($lead['status']) ?>"><?= e($lead['status']) ?></span></td>
              <td><?= $lead['close_value'] !== null ? 'R$ ' . number_format((float) $lead['close_value'], 0, ',', '.') : '-' ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>
</body>
</html>
