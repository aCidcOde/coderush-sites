<?php
declare(strict_types=1);

/*
[Modulo Painel de Leads SVD — dashboard]
@Author: André Gomes ( @acidcode )
@since 2026-08-03
@updated 2026-08-03 (dashboard: icones, barras, efeitos, responsivo; GA so SVD, snapshot 3/3h)
Cockpit protegido por senha: leads do formulario, cliques de WhatsApp com ref,
receita registrada e estatisticas do GA4 (storage/ga-stats.json via cron no host).
Senha em LEADS_PANEL_PASSWORD no .env da raiz.
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
    sleep(1);
    $error = 'Senha incorreta.';
}

// Pedido de atualizacao do snapshot do GA: o container so cria o arquivo-gatilho;
// quem tem credencial e roda a Data API e o watcher no host (a cada minuto).
if ($authed && isset($_POST['atualizar_ga'])) {
    $flag = __DIR__ . '/../storage/ga-refresh.request';
    @file_put_contents($flag, date('c'));
    header('Location: ./?atualizando=1', true, 303);
    exit;
}

if (isset($_GET['sair'])) {
    unset($_SESSION['svd_leads_auth']);
    header('Location: ./', true, 303);
    exit;
}

$authed = ($_SESSION['svd_leads_auth'] ?? false) === true;

// filtros da visao de audiencia
$diasFiltro = (int) ($_GET['dias'] ?? 28);
if (!in_array($diasFiltro, [7, 28, 90], true)) {
    $diasFiltro = 28;
}
$paginaSel = isset($_GET['pagina']) ? (string) $_GET['pagina'] : '';

$leads = [];
$totals = ['total' => 0, 'ultimos7' => 0, 'fechados' => 0, 'receita' => 0.0, 'zap' => 0];
$porOrigem = [];
$dbAviso = '';
$ga = null;
$gaSite = null;

if ($authed) {
    $dbPath = __DIR__ . '/../storage/leads.sqlite';
    if (!is_file($dbPath)) {
        $dbAviso = 'Ainda não há leads registrados.';
    } else {
        try {
            $pdo = new PDO('sqlite:' . $dbPath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $leads = $pdo->query('SELECT * FROM leads ORDER BY id DESC LIMIT 200')->fetchAll(PDO::FETCH_ASSOC);
            $totals['total'] = (int) $pdo->query('SELECT COUNT(*) FROM leads WHERE status NOT IN ("teste","zap")')->fetchColumn();
            $totals['zap'] = (int) $pdo->query('SELECT COUNT(*) FROM leads WHERE status = "zap"')->fetchColumn();
            $totals['ultimos7'] = (int) $pdo->query('SELECT COUNT(*) FROM leads WHERE status NOT IN ("teste","zap") AND created_at >= datetime("now", "-7 days")')->fetchColumn();
            $totals['fechados'] = (int) $pdo->query('SELECT COUNT(*) FROM leads WHERE status = "fechado"')->fetchColumn();
            $totals['receita'] = (float) $pdo->query('SELECT COALESCE(SUM(close_value), 0) FROM leads WHERE status = "fechado"')->fetchColumn();
            foreach ($pdo->query('SELECT COALESCE(origem, "sem origem") AS origem, COUNT(*) AS qtd FROM leads WHERE status != "teste" GROUP BY origem ORDER BY qtd DESC LIMIT 8') as $row) {
                $porOrigem[] = $row;
            }
        } catch (Throwable $e) {
            $dbAviso = 'Erro ao ler o banco de leads: ' . $e->getMessage();
        }
    }

    $gaStatsPath = __DIR__ . '/../storage/ga-stats.json';
    $ga = is_file($gaStatsPath) ? json_decode((string) file_get_contents($gaStatsPath), true) : null;
    $gaSite = is_array($ga) ? ($ga['sites'][0] ?? null) : null;
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


/** Recorta uma serie diaria pelos ultimos N dias. */
function ultimosDias(array $serie, int $dias): array
{
    if (count($serie) <= $dias) {
        return $serie;
    }
    return array_slice($serie, -$dias);
}

/** Soma um campo da serie. */
function somaSerie(array $serie, string $campo): int
{
    return array_sum(array_map(static fn ($d) => (int) ($d[$campo] ?? 0), $serie));
}

/** Grafico SVG de evolucao diaria (area = sessoes, linha = usuarios). Sem libs externas. */
function dailyChart(array $dias, string $campoA = 'sessoes', string $campoB = 'usuarios', string $rotuloA = 'sessões', string $rotuloB = 'usuários'): string
{
    $n = count($dias);
    if ($n < 2) {
        return '<p class="muted">Ainda não há dias suficientes pra desenhar a evolução — volta amanhã.</p>';
    }
    $W = 860; $H = 250; $padL = 34; $padR = 12; $padT = 14; $padB = 30;
    $iw = $W - $padL - $padR; $ih = $H - $padT - $padB;
    $max = 1;
    foreach ($dias as $d) { $max = max($max, (int) ($d[$campoA] ?? 0), (int) ($d[$campoB] ?? 0)); }
    $x = static fn (int $i): float => $padL + ($n > 1 ? $i * $iw / ($n - 1) : 0);
    $y = static fn (int $v): float => $padT + $ih - ($v / $max * $ih);

    $ptsS = $ptsU = [];
    foreach ($dias as $i => $d) {
        $ptsS[] = round($x($i), 1) . ',' . round($y((int) ($d[$campoA] ?? 0)), 1);
        $ptsU[] = round($x($i), 1) . ',' . round($y((int) ($d[$campoB] ?? 0)), 1);
    }
    $area = 'M' . str_replace(',', ' ', $ptsS[0]) . ' L' . implode(' L', array_map(static fn ($p) => str_replace(',', ' ', $p), $ptsS))
        . ' L' . round($x($n - 1), 1) . ' ' . ($padT + $ih) . ' L' . $padL . ' ' . ($padT + $ih) . ' Z';

    $grid = '';
    for ($g = 0; $g <= 4; $g++) {
        $gy = round($padT + $ih - $g * $ih / 4, 1);
        $gv = (int) round($max * $g / 4);
        $grid .= '<line x1="' . $padL . '" y1="' . $gy . '" x2="' . ($W - $padR) . '" y2="' . $gy . '" stroke="rgba(255,255,255,.08)" />'
            . '<text x="' . ($padL - 7) . '" y="' . ($gy + 4) . '" text-anchor="end" font-size="11" fill="rgba(255,255,255,.45)">' . $gv . '</text>';
    }
    $labels = '';
    $step = max(1, (int) ceil($n / 7));
    foreach ($dias as $i => $d) {
        if ($i % $step !== 0 && $i !== $n - 1) continue;
        $dt = $d['data'];
        $lbl = substr($dt, 6, 2) . '/' . substr($dt, 4, 2);
        $labels .= '<text x="' . round($x($i), 1) . '" y="' . ($H - 8) . '" text-anchor="middle" font-size="11" fill="rgba(255,255,255,.5)">' . $lbl . '</text>';
    }
    $dots = '';
    foreach ($dias as $i => $d) {
        $dots .= '<circle cx="' . round($x($i), 1) . '" cy="' . round($y((int) ($d[$campoA] ?? 0)), 1) . '" r="3.5" fill="#fcd34d">'
            . '<title>' . substr($d['data'], 6, 2) . '/' . substr($d['data'], 4, 2) . ' — ' . (int) ($d[$campoA] ?? 0) . ' ' . $rotuloA . ' · ' . (int) ($d[$campoB] ?? 0) . ' ' . $rotuloB . '</title></circle>';
    }
    return '<svg viewBox="0 0 ' . $W . ' ' . $H . '" style="width:100%;height:auto;display:block;" role="img" aria-label="Evolução diária de sessões e usuários">'
        . '<defs><linearGradient id="gArea" x1="0" y1="0" x2="0" y2="1">'
        . '<stop offset="0" stop-color="#fcd34d" stop-opacity=".35"/><stop offset="1" stop-color="#fcd34d" stop-opacity="0"/></linearGradient></defs>'
        . $grid
        . '<path d="' . $area . '" fill="url(#gArea)" />'
        . '<polyline points="' . implode(' ', $ptsS) . '" fill="none" stroke="#fcd34d" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" />'
        . '<polyline points="' . implode(' ', $ptsU) . '" fill="none" stroke="#60a5fa" stroke-width="2" stroke-dasharray="5 4" stroke-linejoin="round" stroke-linecap="round" />'
        . $dots . $labels
        . '</svg>'
        . '<p class="muted" style="margin-top:8px;font-size:12px;">'
        . '<span style="color:#fcd34d;font-weight:700;">━</span> ' . $rotuloA . ' &nbsp;&nbsp;'
        . '<span style="color:#60a5fa;font-weight:700;">┄</span> ' . $rotuloB . ' · ' . count($dias) . ' dias</p>';
}

/** Lista com barra proporcional: $rows = [['label' =>, 'value' => int]] */
function barList(array $rows): string
{
    if (!$rows) {
        return '<p class="muted">Sem dados ainda.</p>';
    }
    $max = max(array_map(static fn ($r) => (int) $r['value'], $rows));
    $max = max($max, 1);
    $html = '<ul class="bars">';
    foreach ($rows as $r) {
        $pct = (int) round((int) $r['value'] / $max * 100);
        $rot = e((string) $r['label']);
        if (!empty($r['href'])) {
            $rot = '<a class="bar-link" href="' . e((string) $r['href']) . '">' . $rot . '</a>';
        }
        $html .= '<li><div class="bar-top"><span class="bar-label">' . $rot
            . '</span><span class="bar-value">' . number_format((int) $r['value'], 0, ',', '.')
            . '</span></div><div class="bar-track"><div class="bar-fill" style="width:' . $pct . '%"></div></div></li>';
    }
    return $html . '</ul>';
}

/** Icones SVG inline (stroke herda a cor do chip) */
function icon(string $name): string
{
    $paths = [
        'users' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'clock' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'zap' => '<path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>',
        'check' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
        'money' => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
        'target' => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>',
        'globe' => '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
        'file' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>',
        'chart' => '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>',
        'gauge' => '<path d="M12 2a10 10 0 1 0 10 10"/><polyline points="12 6 12 12 16 14"/>',
        'eye' => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
        'megaphone' => '<path d="M3 11h3l9-7v16l-9-7H3z"/><path d="M18 8a5 5 0 0 1 0 8"/>',
        'refresh' => '<polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>',
        'lock' => '<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
    ];
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($paths[$name] ?? '') . '</svg>';
}

$gaEventos = [];
if ($gaSite && !empty($gaSite['eventos'])) {
    $nomes = [
        'generate_lead' => 'Leads (formulário)',
        'whatsapp_click' => 'Cliques WhatsApp',
        'simulator_use' => 'Uso do simulador',
        'form_start' => 'Começou o formulário',
        'purchase' => 'Vendas (purchase)',
    ];
    foreach ($nomes as $key => $label) {
        if (isset($gaSite['eventos'][$key])) {
            $gaEventos[] = ['label' => $label, 'value' => (int) $gaSite['eventos'][$key]];
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Painel SVD | Leads &amp; Audiência</title>
  <meta name="robots" content="noindex, nofollow" />
  <style>
    :root {
      color-scheme: dark;
      --bg: #041233; --bg2: #06215c;
      --card: rgba(255,255,255,.055); --line: rgba(255,255,255,.13);
      --amber: #fcd34d; --green: #34d399; --blue: #60a5fa; --text-soft: rgba(255,255,255,.62);
    }
    * { box-sizing: border-box; margin: 0; }
    body {
      font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
      background: radial-gradient(1200px 600px at 85% -10%, rgba(96,165,250,.16), transparent 60%),
                  radial-gradient(900px 500px at -10% 110%, rgba(252,211,77,.08), transparent 55%),
                  linear-gradient(160deg, var(--bg) 0%, var(--bg2) 100%);
      background-attachment: fixed;
      color: #fff; min-height: 100vh;
    }
    .wrap { max-width: 1280px; margin: 0 auto; padding: 22px 16px 70px; }

    /* topo */
    .topbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 22px; }
    .brand { display: flex; align-items: center; gap: 12px; }
    .brand-icon { width: 42px; height: 42px; border-radius: 12px; display: grid; place-items: center;
      background: linear-gradient(135deg, var(--amber), #f59e0b); color: #04173a; }
    .brand-icon svg { width: 22px; height: 22px; }
    h1 { font-size: 19px; letter-spacing: -.01em; }
    .sub { color: var(--text-soft); font-size: 12px; margin-top: 2px; }
    .btn-refresh {
      display: inline-flex; align-items: center; gap: 7px; width: auto; margin: 0;
      padding: 7px 15px; font-size: 13px; border-radius: 99px; cursor: pointer;
      background: rgba(255,255,255,.06); border: 1px solid var(--line); color: rgba(255,255,255,.85);
      font-weight: 600; transition: .18s;
    }
    .btn-refresh:hover:not(:disabled) { border-color: var(--amber); color: var(--amber); transform: none; box-shadow: none; }
    .btn-refresh:disabled { opacity: .55; cursor: progress; }
    .btn-refresh svg { width: 14px; height: 14px; }
    .aviso-refresh {
      margin: -10px 0 18px; padding: 10px 14px; border-radius: 12px; font-size: 13px;
      background: rgba(252,211,77,.12); border: 1px solid rgba(252,211,77,.3); color: #fcd34d;
    }
    .sair { display: inline-flex; align-items: center; gap: 7px; color: var(--text-soft); font-size: 13px;
      text-decoration: none; border: 1px solid var(--line); padding: 7px 15px; border-radius: 99px; transition: .18s; }
    .sair:hover { color: #fff; border-color: rgba(255,255,255,.4); }
    .sair svg { width: 14px; height: 14px; }

    /* KPI cards */
    .kpis { display: grid; grid-template-columns: repeat(auto-fit, minmax(168px, 1fr)); gap: 12px; margin-bottom: 26px; }
    .kpi { position: relative; overflow: hidden; background: var(--card); border: 1px solid var(--line); border-radius: 16px;
      padding: 16px; display: flex; gap: 13px; align-items: center; backdrop-filter: blur(6px);
      transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; animation: rise .45s ease both; }
    .kpi:hover { transform: translateY(-3px); box-shadow: 0 14px 30px rgba(0,0,0,.35); border-color: rgba(255,255,255,.28); }
    .kpi .chip { width: 42px; height: 42px; border-radius: 12px; display: grid; place-items: center; flex-shrink: 0; }
    .kpi .chip svg { width: 21px; height: 21px; }
    .kpi b { display: block; font-size: 24px; line-height: 1.1; letter-spacing: -.02em; }
    .kpi span { font-size: 11px; color: var(--text-soft); text-transform: uppercase; letter-spacing: .07em; }
    .c-amber { background: rgba(252,211,77,.16); color: var(--amber); }
    .c-green { background: rgba(52,211,153,.16); color: var(--green); }
    .c-blue  { background: rgba(96,165,250,.16); color: var(--blue); }
    @keyframes rise { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    .kpi:nth-child(2) { animation-delay: .05s; } .kpi:nth-child(3) { animation-delay: .1s; }
    .kpi:nth-child(4) { animation-delay: .15s; } .kpi:nth-child(5) { animation-delay: .2s; }

    /* section headers */
    .sec { display: flex; align-items: baseline; gap: 10px; margin: 30px 0 14px; }
    .sec h2 { font-size: 16px; display: flex; align-items: center; gap: 9px; }
    .sec h2 svg { width: 17px; height: 17px; color: var(--amber); }
    .sec .muted { font-size: 12px; }
    .quicklinks { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin: -8px 0 22px; }
    .ql-label { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-soft); text-transform: uppercase; letter-spacing: .07em; }
    .ql-label svg { width: 14px; height: 14px; color: var(--amber); }
    .ql { font-size: 12.5px; font-weight: 600; color: rgba(255,255,255,.85); text-decoration: none;
      border: 1px solid var(--line); border-radius: 99px; padding: 5px 13px; transition: .15s; }
    .ql:hover { border-color: var(--amber); color: var(--amber); transform: translateY(-1px); }
    .filtros { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin: 4px 0 16px; }
    .chip-filtro { font-size: 12.5px; font-weight: 600; color: rgba(255,255,255,.8); text-decoration: none;
      border: 1px solid var(--line); border-radius: 99px; padding: 5px 14px; transition: .15s; }
    .chip-filtro:hover { border-color: var(--amber); color: var(--amber); }
    .chip-filtro.ativo { background: rgba(252,211,77,.16); border-color: var(--amber); color: var(--amber); }
    .chip-filtro.limpar { color: var(--text-soft); }
    .bar-link { color: inherit; text-decoration: none; border-bottom: 1px dotted rgba(255,255,255,.25); }
    .bar-link:hover { color: var(--amber); border-color: var(--amber); }
    .muted { color: var(--text-soft); }

    /* boxes grid */
    .boxes { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
    @media (max-width: 900px) { .boxes { grid-template-columns: minmax(0, 1fr); } }
    .box { min-width: 0; overflow: hidden; background: var(--card); border: 1px solid var(--line); border-radius: 16px; padding: 18px;
      backdrop-filter: blur(6px); animation: rise .5s ease both; }
    .box h3 { font-size: 13px; text-transform: uppercase; letter-spacing: .07em; color: var(--text-soft);
      display: flex; align-items: center; gap: 8px; margin-bottom: 14px; }
    .box h3 svg { width: 15px; height: 15px; color: var(--amber); }

    /* bar lists */
    .bars { list-style: none; display: grid; gap: 11px; }
    .bar-top { display: flex; justify-content: space-between; gap: 10px; font-size: 13px; margin-bottom: 4px; min-width: 0; }
    .bar-label { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .bar-value { flex-shrink: 0; font-weight: 700; color: var(--amber); }
    .bar-track { height: 6px; border-radius: 99px; background: rgba(255,255,255,.08); overflow: hidden; }
    .bar-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, #f59e0b, var(--amber));
      box-shadow: 0 0 10px rgba(252,211,77,.35); transition: width .6s ease; }

    /* tables */
    .tbl-scroll { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    th, td { text-align: left; padding: 9px 10px; border-bottom: 1px solid rgba(255,255,255,.08); vertical-align: top; white-space: nowrap; }
    td:nth-child(3), td:nth-child(6) { white-space: normal; }
    th { color: var(--text-soft); font-size: 10.5px; text-transform: uppercase; letter-spacing: .07em; }
    tr:hover td { background: rgba(255,255,255,.04); }
    .tag { display: inline-block; padding: 2px 9px; border-radius: 99px; font-size: 11px; font-weight: 600; }
    .tag.novo { background: rgba(96,165,250,.18); color: #93c5fd; }
    .tag.fechado { background: rgba(52,211,153,.18); color: #6ee7b7; }
    .tag.teste { background: rgba(255,255,255,.1); color: var(--text-soft); }
    .tag.zap { background: rgba(52,211,153,.13); color: #6ee7b7; }

    /* login */
    .login { max-width: 370px; margin: 14vh auto 0; background: var(--card); border: 1px solid var(--line);
      border-radius: 20px; padding: 32px; backdrop-filter: blur(8px); animation: rise .4s ease both; }
    .login .brand { justify-content: center; margin-bottom: 20px; }
    input[type=password] { width: 100%; padding: 13px 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,.25);
      background: rgba(255,255,255,.08); color: #fff; font-size: 15px; outline: none; transition: border-color .15s; }
    input[type=password]:focus { border-color: var(--amber); }
    button { width: 100%; margin-top: 13px; padding: 13px; border: 0; border-radius: 12px;
      background: linear-gradient(135deg, var(--amber), #f59e0b); color: #04173a; font-weight: 800; font-size: 14px;
      cursor: pointer; transition: transform .15s, box-shadow .15s; }
    button:hover { transform: translateY(-1px); box-shadow: 0 10px 22px rgba(245,158,11,.3); }
    .err { color: #fca5a5; font-size: 13px; margin-top: 12px; text-align: center; }

    @media (max-width: 640px) {
      .kpi b { font-size: 20px; }
      .wrap { padding-top: 16px; }
    }
  </style>
</head>
<body>
<?php if (!$authed): ?>
  <div class="login">
    <div class="brand">
      <div class="brand-icon"><?= icon('lock') ?></div>
      <div><h1>Painel SVD</h1><p class="sub">Leads &amp; Audiência</p></div>
    </div>
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
      <div class="brand">
        <div class="brand-icon"><?= icon('chart') ?></div>
        <div>
          <h1>Painel SVD — Leads &amp; Audiência</h1>
          <p class="sub">sistemavendadireta.com.br · horário de Brasília<?= $ga ? ' · Google atualizado ' . e(brDateTime($ga['gerado_em'] ?? null)) . ' (a cada 3h)' : '' ?></p>
        </div>
      </div>
      <div style="display:flex;gap:9px;align-items:center;">
        <?php $pendente = is_file(__DIR__ . '/../storage/ga-refresh.request'); ?>
        <form method="post" style="margin:0;">
          <button type="submit" name="atualizar_ga" value="1" class="btn-refresh" <?= $pendente ? 'disabled' : '' ?>>
            <?= icon('refresh') ?> <?= $pendente ? 'Atualizando…' : 'Atualizar dados' ?>
          </button>
        </form>
        <a class="sair" href="?sair=1"><?= icon('lock') ?> Sair</a>
      </div>
    </div>

    <?php if (!empty($_GET['atualizando']) || (isset($pendente) && $pendente)): ?>
      <p class="aviso-refresh">Atualização solicitada — os dados do Google chegam em até 1 minuto. Recarregue a página.</p>
    <?php endif; ?>

    <div class="quicklinks">
      <span class="ql-label"><?= icon('megaphone') ?> LPs da campanha:</span>
      <?php
      $lps = [
          ['Geral', '/oferta/'],
          ['Suplementos', '/oferta/suplementos/'],
          ['Cosméticos', '/oferta/cosmeticos/'],
          ['Afiliados', '/oferta/afiliados/'],
          ['Parceiros', '/oferta/parceiros/'],
          ['Cases', '/cases/'],
          ['Site', '/'],
      ];
      foreach ($lps as [$nome, $path]): ?>
        <a class="ql" href="https://www.sistemavendadireta.com.br<?= e($path) ?>" target="_blank" rel="noopener"><?= e($nome) ?></a>
      <?php endforeach; ?>
    </div>

    <div class="kpis">
      <div class="kpi"><div class="chip c-amber"><?= icon('users') ?></div><div><b><?= $totals['total'] ?></b><span>Leads (form)</span></div></div>
      <div class="kpi"><div class="chip c-blue"><?= icon('clock') ?></div><div><b><?= $totals['ultimos7'] ?></b><span>Últimos 7 dias</span></div></div>
      <div class="kpi"><div class="chip c-green"><?= icon('zap') ?></div><div><b><?= $totals['zap'] ?></b><span>Cliques WhatsApp</span></div></div>
      <div class="kpi"><div class="chip c-green"><?= icon('check') ?></div><div><b><?= $totals['fechados'] ?></b><span>Vendas fechadas</span></div></div>
      <div class="kpi"><div class="chip c-amber"><?= icon('money') ?></div><div><b>R$ <?= number_format($totals['receita'], 0, ',', '.') ?></b><span>Receita registrada</span></div></div>
    </div>

    <?php if ($gaSite): ?>
      <div class="sec"><h2><?= icon('gauge') ?> Audiência (Google Analytics)</h2></div>
      <div class="kpis">
        <div class="kpi"><div class="chip c-blue"><?= icon('users') ?></div><div><b><?= (int) ($gaSite['d7']['usuarios'] ?? 0) ?></b><span>Usuários · 7 dias</span></div></div>
        <div class="kpi"><div class="chip c-blue"><?= icon('eye') ?></div><div><b><?= (int) ($gaSite['d7']['sessoes'] ?? 0) ?></b><span>Sessões · 7 dias</span></div></div>
        <div class="kpi"><div class="chip c-amber"><?= icon('users') ?></div><div><b><?= (int) ($gaSite['d28']['usuarios'] ?? 0) ?></b><span>Usuários · 28 dias</span></div></div>
        <div class="kpi"><div class="chip c-amber"><?= icon('eye') ?></div><div><b><?= (int) ($gaSite['d28']['pageviews'] ?? 0) ?></b><span>Pageviews · 28 dias</span></div></div>
      </div>

      <div class="filtros">
        <span class="muted" style="font-size:12px;text-transform:uppercase;letter-spacing:.07em;">Período:</span>
        <?php foreach ([7 => '7 dias', 28 => '28 dias', 90 => '90 dias'] as $d => $rot): ?>
          <a class="chip-filtro <?= $diasFiltro === $d ? 'ativo' : '' ?>"
             href="?dias=<?= $d ?><?= $paginaSel !== '' ? '&pagina=' . urlencode($paginaSel) : '' ?>"><?= $rot ?></a>
        <?php endforeach; ?>
        <?php if ($paginaSel !== ''): ?>
          <a class="chip-filtro limpar" href="?dias=<?= $diasFiltro ?>">✕ ver todas as páginas</a>
        <?php endif; ?>
      </div>

      <?php
      $serieGeral = ultimosDias($gaSite['diario'] ?? [], $diasFiltro);
      $seriePagina = $paginaSel !== '' ? ultimosDias($gaSite['paginas_diario'][$paginaSel] ?? [], $diasFiltro) : [];
      ?>

      <?php if ($paginaSel !== ''): ?>
        <div class="box" style="margin-bottom:14px;">
          <h3><?= icon('file') ?> <?= e($paginaSel) ?> · últimos <?= $diasFiltro ?> dias</h3>
          <?php if ($seriePagina): ?>
            <div class="cards" style="margin:6px 0 18px;">
              <div class="card"><div class="chip c-amber"><?= icon('eye') ?></div><div><b><?= number_format(somaSerie($seriePagina, 'views'), 0, ',', '.') ?></b><span>Visualizações</span></div></div>
              <div class="card"><div class="chip c-blue"><?= icon('users') ?></div><div><b><?= number_format(somaSerie($seriePagina, 'usuarios'), 0, ',', '.') ?></b><span>Usuários</span></div></div>
              <div class="card"><div class="chip c-green"><?= icon('chart') ?></div><div><b><?= number_format(somaSerie($seriePagina, 'views') / max(1, count($seriePagina)), 1, ',', '.') ?></b><span>Média por dia</span></div></div>
            </div>
            <?= dailyChart($seriePagina, 'views', 'usuarios', 'visualizações', 'usuários') ?>
            <div class="tbl-scroll" style="margin-top:18px;max-height:320px;overflow-y:auto;">
              <table>
                <tr><th>Dia</th><th>Visualizações</th><th>Usuários</th></tr>
                <?php foreach (array_reverse($seriePagina) as $d): ?>
                  <tr>
                    <td><?= e(substr($d['data'], 6, 2) . '/' . substr($d['data'], 4, 2) . '/' . substr($d['data'], 0, 4)) ?></td>
                    <td><?= (int) $d['views'] ?></td>
                    <td><?= (int) $d['usuarios'] ?></td>
                  </tr>
                <?php endforeach; ?>
              </table>
            </div>
          <?php else: ?>
            <p class="muted">Sem acessos registrados nessa página no período — experimente ampliar para 90 dias.</p>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($serieGeral)): ?>
        <div class="box" style="margin-bottom:14px;">
          <h3><?= icon('chart') ?> Evolução de acessos do site · últimos <?= $diasFiltro ?> dias</h3>
          <?= dailyChart($serieGeral) ?>
        </div>
      <?php endif; ?>

      <div class="boxes">
        <div class="box">
          <h3><?= icon('target') ?> Eventos-chave · 28d</h3>
          <?= barList($gaEventos) ?>
        </div>
        <div class="box">
          <h3><?= icon('globe') ?> Origem do tráfego · 28d</h3>
          <?= barList(array_map(static fn ($r) => ['label' => $r['fonte'], 'value' => $r['sessoes']], $gaSite['fontes'] ?? [])) ?>
        </div>
        <div class="box">
          <h3><?= icon('file') ?> Páginas acessadas · 28d <span class="muted" style="font-weight:400;text-transform:none;letter-spacing:0;">· clique para ver a evolução</span></h3>
          <?= barList(array_map(static fn ($r) => [
              'label' => $r['pagina'],
              'value' => $r['views'],
              'href' => isset($gaSite['paginas_diario'][$r['pagina']])
                  ? '?dias=' . $diasFiltro . '&pagina=' . urlencode($r['pagina'])
                  : null,
          ], $gaSite['paginas'] ?? [])) ?>
        </div>
        <?php $campanhas = array_values(array_filter($gaSite['campanhas'] ?? [], static fn ($r) => !in_array($r['campanha'], ['(not set)', '(direct)'], true))); ?>
        <div class="box">
          <h3><?= icon('megaphone') ?> Campanhas · 28d</h3>
          <?php if ($campanhas): ?>
            <div class="tbl-scroll"><table>
              <tr><th>Campanha</th><th>Sessões</th><th>Conversões</th></tr>
              <?php foreach ($campanhas as $row): ?>
                <tr><td><?= e($row['campanha']) ?></td><td><?= (int) $row['sessoes'] ?></td><td><?= e((string) $row['conversoes']) ?></td></tr>
              <?php endforeach; ?>
            </table></div>
          <?php else: ?>
            <p class="muted">Nenhuma sessão de campanha ainda — os UTMs aparecem aqui assim que os anúncios rodarem.</p>
          <?php endif; ?>
        </div>
        <?php if (!empty($gaSite['faixas_simuladas'])): ?>
          <div class="box">
            <h3><?= icon('chart') ?> Faixas simuladas · 28d</h3>
            <?= barList(array_map(static fn ($r) => ['label' => $r['faixa'], 'value' => $r['eventos']], $gaSite['faixas_simuladas'])) ?>
          </div>
        <?php endif; ?>
        <?php if ($porOrigem): ?>
          <div class="box">
            <h3><?= icon('target') ?> Leads por LP de origem</h3>
            <?= barList(array_map(static fn ($r) => ['label' => $r['origem'], 'value' => $r['qtd']], $porOrigem)) ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <div class="sec"><h2><?= icon('users') ?> Últimos leads</h2><span class="muted">até 200 · formulário e WhatsApp</span></div>
    <?php if ($dbAviso !== ''): ?>
      <p class="muted"><?= e($dbAviso) ?></p>
    <?php else: ?>
      <div class="box tbl-scroll">
        <table>
          <tr>
            <th>#</th><th>Data</th><th>Nome</th><th>WhatsApp</th><th>Origem</th>
            <th>Campanha</th><th>Fonte</th><th>Faixa simulada</th><th>Ads?</th><th>Status</th><th>Valor</th>
          </tr>
          <?php foreach ($leads as $lead): ?>
            <tr>
              <td class="muted"><?= (int) $lead['id'] ?></td>
              <td><?= e(brDateTime($lead['created_at'])) ?></td>
              <td><?= e($lead['nome']) ?><?= $lead['status'] === 'zap' && $lead['mensagem'] ? '<br /><span class="muted">' . e($lead['mensagem']) . '</span>' : '' ?></td>
              <td><?= e($lead['telefone']) ?></td>
              <td><?= e($lead['origem']) ?></td>
              <td><?= e($lead['utm_campaign'] ?: '-') ?><?= $lead['utm_content'] ? ' / ' . e($lead['utm_content']) : '' ?></td>
              <td><?= e($lead['utm_source'] ?: 'direto') ?></td>
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
