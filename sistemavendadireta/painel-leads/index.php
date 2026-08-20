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
$diasFiltro = (int) ($_GET['dias'] ?? 28);
if (!in_array($diasFiltro, [7, 28, 90], true)) { $diasFiltro = 28; }
$paginaSel = isset($_GET['pagina']) ? (string) $_GET['pagina'] : '';
$etapaSel = (string) ($_GET['etapa'] ?? '');

// Abas: cada assunto na sua tela, com o filtro que pertence a ela. Misturar
// periodo de audiencia com etapa do funil na mesma barra confundia a leitura.
const ABAS = ['visao' => 'Visão geral', 'trafego' => 'Tráfego', 'ads' => 'Investimento',
              'funil' => 'Funil de leads', 'historico' => 'Histórico'];
$aba = (string) ($_GET['aba'] ?? 'visao');
if (!array_key_exists($aba, ABAS)) { $aba = 'visao'; }

/** URL da propria pagina preservando o estado relevante da aba. */
function abaUrl(string $aba, array $extra = []): string
{
    return '?' . http_build_query(array_filter(array_merge(['aba' => $aba], $extra),
        static fn ($v) => $v !== '' && $v !== null));
}

if (isset($_POST['senha'])) {
    if ($expected !== '' && hash_equals($expected, (string) $_POST['senha'])) {
        $_SESSION['svd_leads_auth'] = true;
        header('Location: ./', true, 303);
        exit;
    }
    sleep(1);
    $error = 'Senha incorreta.';
}

// Marcar estagio do lead: grava no sqlite e enfileira o evento pro GA4
// (o watcher no host e quem tem credencial e dispara via Measurement Protocol).
if ($authed && isset($_POST['marcar_lead'])) {
    $leadId = (int) ($_POST['lead_id'] ?? 0);
    $novo = (string) ($_POST['marcar_lead'] ?? '');
    $mapa = [
        'qualificado' => 'qualify_lead',
        'demo' => 'schedule_demo',
        'fechado' => 'purchase',
        'perdido' => null,
        'novo' => null,
    ];
    $valorVenda = (float) str_replace(',', '.', (string) ($_POST['valor'] ?? 0));
    if ($leadId > 0 && array_key_exists($novo, $mapa)) {
        try {
            $db = new PDO('sqlite:' . __DIR__ . '/../storage/leads.sqlite');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sel = $db->prepare('SELECT ga_client_id, gclid, origem FROM leads WHERE id = ?');
            $sel->execute([$leadId]);
            $lead = $sel->fetch(PDO::FETCH_ASSOC);
            if ($novo === 'fechado') {
                $db->prepare('UPDATE leads SET status = ?, closed_at = ?, close_value = ?, transaction_id = ? WHERE id = ?')
                   ->execute(['fechado', date('c'), $valorVenda, 'svd-' . $leadId . '-' . date('Ymd'), $leadId]);
            } else {
                $db->prepare('UPDATE leads SET status = ? WHERE id = ?')->execute([$novo, $leadId]);
            }
            if ($lead && $mapa[$novo]) {
                $fila = __DIR__ . '/../storage/ga-events.queue';
                @file_put_contents($fila, json_encode([
                    'lead_id' => $leadId,
                    'event' => $mapa[$novo],
                    'client_id' => $lead['ga_client_id'] ?: null,
                    'origem' => $lead['origem'] ?? '',
                    'valor' => $novo === 'fechado' ? $valorVenda : null,
                    'ts' => date('c'),
                ], JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND | LOCK_EX);
            }
        } catch (Throwable $e) {
            // silencioso: o painel nao pode quebrar por causa do marcador
        }
    }
    header('Location: ./' . abaUrl('funil', ['etapa' => $etapaSel, 'marcado' => 1]), true, 303);
    exit;
}

// Exportacao das conversoes offline no formato de upload do Google Ads
if ($authed && isset($_GET['exportar']) && $_GET['exportar'] === 'ads') {
    $db = new PDO('sqlite:' . __DIR__ . '/../storage/leads.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $q = $db->query("SELECT gclid, status, created_at, closed_at, close_value FROM leads
                     WHERE gclid IS NOT NULL AND gclid != '' AND status IN ('qualificado','demo','fechado')
                     ORDER BY id DESC");
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="conversoes-offline-svd.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "Parameters:TimeZone=America/Sao_Paulo\n");
    fputcsv($out, ['Google Click ID', 'Conversion Name', 'Conversion Time', 'Conversion Value', 'Conversion Currency']);
    $nomes = ['qualificado' => 'Lead qualificado', 'demo' => 'Demonstração agendada', 'fechado' => 'Venda fechada'];
    foreach ($q as $r) {
        $quando = $r['status'] === 'fechado' && $r['closed_at'] ? $r['closed_at'] : $r['created_at'];
        $dt = new DateTimeImmutable($quando);
        fputcsv($out, [
            $r['gclid'],
            $nomes[$r['status']] ?? 'Lead qualificado',
            $dt->setTimezone(new DateTimeZone('America/Sao_Paulo'))->format('Y-m-d H:i:s'),
            $r['status'] === 'fechado' ? (float) $r['close_value'] : 0,
            'BRL',
        ]);
    }
    fclose($out);
    exit;
}

// Pedido de atualizacao do snapshot do GA: o container so cria o arquivo-gatilho;
// quem tem credencial e roda a Data API e o watcher no host (a cada minuto).
if ($authed && isset($_POST['atualizar_ga'])) {
    $flag = __DIR__ . '/../storage/ga-refresh.request';
    @file_put_contents($flag, date('c'));
    header('Location: ./' . abaUrl($aba, ['atualizando' => 1]), true, 303);
    exit;
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
$funil = [];
$dbAviso = '';
$ga = null;
$gaSite = null;
$ads = null;

if ($authed) {
    $dbPath = __DIR__ . '/../storage/leads.sqlite';
    if (!is_file($dbPath)) {
        $dbAviso = 'Ainda não há leads registrados.';
    } else {
        try {
            $pdo = new PDO('sqlite:' . $dbPath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if ($etapaSel !== '' && $etapaSel !== 'todos') {
                $st = $pdo->prepare('SELECT * FROM leads WHERE status = ? ORDER BY id DESC LIMIT 200');
                $st->execute([$etapaSel]);
                $leads = $st->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $leads = $pdo->query('SELECT * FROM leads ORDER BY id DESC LIMIT 200')->fetchAll(PDO::FETCH_ASSOC);
            }
            $totals['total'] = (int) $pdo->query('SELECT COUNT(*) FROM leads WHERE status NOT IN ("teste","zap")')->fetchColumn();
            $totals['zap'] = (int) $pdo->query('SELECT COUNT(*) FROM leads WHERE status = "zap"')->fetchColumn();
            $totals['ultimos7'] = (int) $pdo->query('SELECT COUNT(*) FROM leads WHERE status NOT IN ("teste","zap") AND created_at >= datetime("now", "-7 days")')->fetchColumn();
            $totals['fechados'] = (int) $pdo->query('SELECT COUNT(*) FROM leads WHERE status = "fechado"')->fetchColumn();
            foreach ($pdo->query('SELECT status, COUNT(*) AS qtd FROM leads WHERE status != "teste" GROUP BY status') as $r) {
                $funil[$r['status']] = (int) $r['qtd'];
            }
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
    $ads = is_array($ga) ? ($ga['ads'] ?? null) : null;
}

/** Formata numero em pt-BR sem repetir number_format em toda linha. */
function num($v, int $casas = 0): string
{
    return number_format((float) $v, $casas, ',', '.');
}

/** Custo por lead: quanto do investimento foi preciso pra cada lead que entrou. */
function custoPorLead(float $custo, int $leads): string
{
    return $leads > 0 ? 'R$ ' . num($custo / $leads, 2) : '—';
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


/** AAAA-MM-DD -> "14 de agosto de 2026" */
function brDate(string $iso): string
{
    $meses = [1 => 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho',
              'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
    $p = explode('-', $iso);
    if (count($p) !== 3) {
        return $iso;
    }
    return (int) $p[2] . ' de ' . ($meses[(int) $p[1]] ?? '?') . ' de ' . $p[0];
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
function dailyChart(array $dias, string $campoA = 'sessoes', string $campoB = 'usuarios', string $rotuloA = 'sessões', string $rotuloB = 'usuários', int $casas = 0): string
{
    $n = count($dias);
    if ($n < 2) {
        return '<p class="muted">Ainda não há dias suficientes pra desenhar a evolução — volta amanhã.</p>';
    }
    $W = 860; $H = 250; $padL = 34; $padR = 12; $padT = 14; $padB = 30;
    $iw = $W - $padL - $padR; $ih = $H - $padT - $padB;
    $max = 0.0;
    foreach ($dias as $d) { $max = max($max, (float) ($d[$campoA] ?? 0), (float) ($d[$campoB] ?? 0)); }
    $max = max($max, $casas > 0 ? 0.01 : 1);
    $x = static fn (int $i): float => $padL + ($n > 1 ? $i * $iw / ($n - 1) : 0);
    $y = static fn (float $v): float => $padT + $ih - ($v / $max * $ih);

    $ptsS = $ptsU = [];
    foreach ($dias as $i => $d) {
        $ptsS[] = round($x($i), 1) . ',' . round($y((float) ($d[$campoA] ?? 0)), 1);
        $ptsU[] = round($x($i), 1) . ',' . round($y((float) ($d[$campoB] ?? 0)), 1);
    }
    $area = 'M' . str_replace(',', ' ', $ptsS[0]) . ' L' . implode(' L', array_map(static fn ($p) => str_replace(',', ' ', $p), $ptsS))
        . ' L' . round($x($n - 1), 1) . ' ' . ($padT + $ih) . ' L' . $padL . ' ' . ($padT + $ih) . ' Z';

    $grid = '';
    for ($g = 0; $g <= 4; $g++) {
        $gy = round($padT + $ih - $g * $ih / 4, 1);
        $gv = number_format($max * $g / 4, $casas, ',', '.');
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
        $dots .= '<circle cx="' . round($x($i), 1) . '" cy="' . round($y((float) ($d[$campoA] ?? 0)), 1) . '" r="3.5" fill="#fcd34d">'
            . '<title>' . substr($d['data'], 6, 2) . '/' . substr($d['data'], 4, 2) . ' — '
            . number_format((float) ($d[$campoA] ?? 0), $casas, ',', '.') . ' ' . $rotuloA . ' · '
            . number_format((float) ($d[$campoB] ?? 0), $casas, ',', '.') . ' ' . $rotuloB . '</title></circle>';
    }
    return '<svg class="chart-svg" viewBox="0 0 ' . $W . ' ' . $H . '" style="width:100%;height:auto;display:block;" role="img" aria-label="Evolução diária de sessões e usuários">'
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

    /* abas — cada assunto na sua tela */
    .tabs { display: flex; gap: 4px; margin: 0 0 26px; border-bottom: 1px solid var(--line);
      overflow-x: auto; scrollbar-width: none; }
    .tabs::-webkit-scrollbar { display: none; }
    .tab { flex-shrink: 0; display: inline-flex; align-items: center; gap: 8px; padding: 11px 18px;
      font-size: 13.5px; font-weight: 600; color: var(--text-soft); text-decoration: none;
      border-bottom: 2px solid transparent; margin-bottom: -1px; transition: .15s; white-space: nowrap; }
    .tab svg { width: 15px; height: 15px; }
    .tab:hover { color: #fff; }
    .tab.ativo { color: var(--amber); border-bottom-color: var(--amber); }
    .tab .badge { font-size: 11px; font-weight: 700; padding: 1px 8px; border-radius: 99px;
      background: rgba(255,255,255,.1); color: rgba(255,255,255,.75); }
    .tab.ativo .badge { background: rgba(252,211,77,.18); color: var(--amber); }

    /* barra de filtro: rotulo fixo a esquerda, opcoes agrupadas — nao mistura com o resto */
    .barra-filtro { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin: 0 0 18px;
      padding: 11px 14px; background: rgba(255,255,255,.03); border: 1px solid var(--line); border-radius: 14px; }
    .barra-filtro + .barra-filtro { margin-top: -8px; }
    .bf-label { font-size: 11px; color: var(--text-soft); text-transform: uppercase;
      letter-spacing: .07em; font-weight: 700; flex-shrink: 0; }
    .bf-grupo { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
    .bf-sep { flex: 1 1 auto; }

    /* linha do tempo do historico de otimizacoes */
    .linha-tempo { list-style: none; position: relative; padding: 4px 0 4px 26px; }
    .linha-tempo::before { content: ""; position: absolute; left: 5px; top: 12px; bottom: 12px;
      width: 2px; background: linear-gradient(180deg, var(--amber), rgba(252,211,77,.12)); }
    .linha-tempo li { position: relative; margin-bottom: 14px; }
    .lt-marca { position: absolute; left: -26px; top: 20px; width: 12px; height: 12px;
      border-radius: 50%; background: var(--amber); box-shadow: 0 0 0 4px rgba(252,211,77,.16); }
    .lt-corpo { background: var(--card); border: 1px solid var(--line); border-radius: 14px;
      padding: 15px 17px; transition: .18s; }
    .lt-corpo:hover { border-color: rgba(255,255,255,.28); transform: translateX(2px); }
    .lt-topo { display: flex; align-items: center; gap: 10px; margin-bottom: 7px; flex-wrap: wrap; }
    .lt-data { font-size: 11.5px; color: var(--text-soft); text-transform: uppercase; letter-spacing: .06em; }
    .lt-corpo h4 { font-size: 14.5px; margin-bottom: 8px; letter-spacing: -.01em; }
    .lt-porque, .lt-efeito, .lt-pendente { font-size: 13px; line-height: 1.6; margin-top: 6px; }
    .lt-porque { color: var(--text-soft); }
    .lt-porque b { color: rgba(255,255,255,.8); }
    .lt-efeito { color: #a7f3d0; background: rgba(52,211,153,.08); border-left: 3px solid var(--green);
      padding: 8px 11px; border-radius: 0 8px 8px 0; }
    .lt-efeito b { color: var(--green); }
    .lt-pendente { color: rgba(255,255,255,.35); font-style: italic; font-size: 12px; }
    .tag.area-ads { background: rgba(252,211,77,.18); color: var(--amber); }
    .tag.area-medicao { background: rgba(96,165,250,.18); color: #93c5fd; }
    .tag.area-painel { background: rgba(52,211,153,.18); color: #6ee7b7; }
    .tag.area-site { background: rgba(255,255,255,.12); color: rgba(255,255,255,.75); }

    /* tabela de metricas de midia (mesmas colunas do Google Ads) */
    .metricas th { text-align: right; }
    .metricas th:first-child, .metricas td:first-child { text-align: left; white-space: normal; min-width: 180px; }
    .metricas td { text-align: right; font-variant-numeric: tabular-nums; }
    .metricas tfoot td { font-weight: 800; border-top: 1px solid var(--line); border-bottom: 0; color: var(--amber); }
    .m-alerta { color: #fca5a5; }
    .m-bom { color: var(--green); }
    .nota { font-size: 12.5px; line-height: 1.55; color: var(--text-soft); margin-top: 12px;
      padding: 11px 13px; border-left: 3px solid rgba(252,211,77,.5); background: rgba(252,211,77,.06);
      border-radius: 0 10px 10px 0; }
    .nota b { color: var(--amber); }

    /* KPI cards */
    .kpis { display: grid; grid-template-columns: repeat(auto-fit, minmax(168px, 1fr)); gap: 12px; margin-bottom: 26px; }
    /* 8 metricas: auto-fit deixaria 7+1 orfao — trava em 4+4 e desce de forma limpa */
    .kpis-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    @media (max-width: 1080px) { .kpis-4 { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    @media (max-width: 780px)  { .kpis-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 460px)  { .kpis-4 { grid-template-columns: minmax(0, 1fr); } }
    .kpis-4 .kpi b { white-space: nowrap; }
    .kpi { position: relative; overflow: hidden; background: var(--card); border: 1px solid var(--line); border-radius: 16px;
      padding: 16px; display: flex; gap: 13px; align-items: center; backdrop-filter: blur(6px);
      transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; animation: rise .45s ease both; }
    .kpi:hover { transform: translateY(-3px); box-shadow: 0 14px 30px rgba(0,0,0,.35); border-color: rgba(255,255,255,.28); }
    .kpi .chip { width: 42px; height: 42px; border-radius: 12px; display: grid; place-items: center; flex-shrink: 0; }
    .kpi .chip svg { width: 21px; height: 21px; }
    .box svg:not([viewBox='0 0 860 250']) { max-width: 22px; max-height: 22px; }
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
    .chip-filtro { display: inline-flex; align-items: center; gap: 6px; }
    .chip-filtro svg { width: 14px; height: 14px; flex: 0 0 14px; }
    /* trava global: nenhum icone escapa do tamanho, exceto o grafico */
    svg:not(.chart-svg) { max-width: 24px; max-height: 24px; }
    .brand-icon svg { max-width: 22px; max-height: 22px; }
    .bar-link { color: inherit; text-decoration: none; border-bottom: 1px dotted rgba(255,255,255,.25); }
    .bar-link:hover { color: var(--amber); border-color: var(--amber); }
    .funil { display: grid; grid-template-columns: repeat(auto-fit, minmax(128px, 1fr)); gap: 10px; margin-bottom: 18px; }
    .funil-etapa { background: var(--card); border: 1px solid var(--line); border-radius: 14px; padding: 12px 14px; text-align: center; }
    .funil-etapa b { display: block; font-size: 22px; margin-bottom: 6px; }
    .acoes { display: flex; gap: 5px; margin: 0; align-items: center; flex-wrap: wrap; }
    .venda-wrap { display: inline-flex; align-items: center; gap: 4px; }
    .inp-valor { width: 62px; padding: 4px 7px; font-size: 11px; border-radius: 8px;
      border: 1px solid var(--line); background: rgba(255,255,255,.07); color: #fff; }
    .btn-etapa.e-fechado:hover { border-color: var(--green); color: var(--green); }
    a.funil-etapa { text-decoration: none; color: inherit; display: block; transition: .15s; }
    a.funil-etapa:hover { border-color: var(--amber); transform: translateY(-2px); }
    .btn-etapa { width: auto; margin: 0; padding: 4px 10px; font-size: 11px; font-weight: 700; border-radius: 99px;
      cursor: pointer; border: 1px solid var(--line); background: rgba(255,255,255,.06); color: rgba(255,255,255,.8); transition: .15s; }
    .btn-etapa:hover { transform: none; box-shadow: none; }
    .btn-etapa.e-qualificado:hover { border-color: var(--amber); color: var(--amber); }
    .btn-etapa.e-demo:hover { border-color: var(--blue); color: var(--blue); }
    .btn-etapa.e-perdido:hover { border-color: #fca5a5; color: #fca5a5; }
    .tag.qualificado { background: rgba(252,211,77,.18); color: var(--amber); }
    .tag.demo { background: rgba(96,165,250,.18); color: #93c5fd; }
    .tag.perdido { background: rgba(248,113,113,.15); color: #fca5a5; }
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

    <?php
    $totalLeads = $totals['total'] + $totals['zap'];
    $abaIcones = ['visao' => 'gauge', 'trafego' => 'globe', 'ads' => 'money',
                  'funil' => 'users', 'historico' => 'clock'];
    $historico = is_file(__DIR__ . '/historico.php') ? (require __DIR__ . '/historico.php') : [];
    $abaBadges = ['funil' => (string) $totalLeads, 'historico' => (string) count($historico)];
    if ($ads && empty($ads['erro'])) {
        $abaBadges['ads'] = 'R$ ' . num($ads['periodos']['d28']['custo'] ?? 0);
    }
    ?>
    <nav class="tabs">
      <?php foreach (ABAS as $chave => $rot): ?>
        <a class="tab <?= $aba === $chave ? 'ativo' : '' ?>" href="<?= e(abaUrl($chave)) ?>">
          <?= icon($abaIcones[$chave]) ?> <?= e($rot) ?>
          <?php if (!empty($abaBadges[$chave])): ?><span class="badge"><?= e($abaBadges[$chave]) ?></span><?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <?php if ($aba === 'visao'): ?>
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
    <?php endif; ?>

    <?php if ($ads && empty($ads['erro'])): $p28 = $ads['periodos']['d28']; $pHoje = $ads['periodos']['hoje']; ?>
      <div class="sec">
        <h2><?= icon('money') ?> Investimento em anúncios · 28 dias</h2>
        <span class="muted">hoje: R$ <?= num($pHoje['custo'], 2) ?></span>
      </div>
      <div class="kpis">
        <div class="kpi"><div class="chip c-amber"><?= icon('money') ?></div><div><b>R$ <?= num($p28['custo'], 2) ?></b><span>Investido</span></div></div>
        <div class="kpi"><div class="chip c-blue"><?= icon('target') ?></div><div><b><?= num($p28['cliques']) ?></b><span>Cliques</span></div></div>
        <div class="kpi"><div class="chip c-blue"><?= icon('money') ?></div><div><b>R$ <?= num($p28['cpc_medio'], 2) ?></b><span>CPC médio</span></div></div>
        <div class="kpi"><div class="chip c-green"><?= icon('check') ?></div><div><b><?= num($p28['conversoes'], 1) ?></b><span>Conversões</span></div></div>
        <div class="kpi"><div class="chip c-green"><?= icon('users') ?></div><div><b><?= custoPorLead((float) $p28['custo'], $totals['ultimos7']) ?></b><span>Custo por lead</span></div></div>
      </div>
    <?php endif; ?>

    <?php $serieGeral = ultimosDias($gaSite['diario'] ?? [], 28); ?>
    <?php if (!empty($serieGeral)): ?>
      <div class="box" style="margin-bottom:22px;">
        <h3><?= icon('chart') ?> Evolução de acessos · últimos 28 dias</h3>
        <?= dailyChart($serieGeral) ?>
      </div>
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
    <?php endif; /* fim da aba visao */ ?>

    <?php if ($aba === 'trafego' && $gaSite): ?>
      <div class="barra-filtro">
        <span class="bf-label">Período</span>
        <span class="bf-grupo">
          <?php foreach ([7 => '7 dias', 28 => '28 dias', 90 => '90 dias'] as $d => $rot): ?>
            <a class="chip-filtro <?= $diasFiltro === $d ? 'ativo' : '' ?>"
               href="<?= e(abaUrl('trafego', ['dias' => $d, 'pagina' => $paginaSel])) ?>"><?= $rot ?></a>
          <?php endforeach; ?>
        </span>
        <?php if ($paginaSel !== ''): ?>
          <span class="bf-sep"></span>
          <span class="bf-label">Página</span>
          <span class="bf-grupo">
            <a class="chip-filtro ativo" href="<?= e(abaUrl('trafego', ['dias' => $diasFiltro])) ?>">✕ <?= e($paginaSel) ?></a>
          </span>
        <?php endif; ?>
      </div>

      <?php
      $serieGeral = ultimosDias($gaSite['diario'] ?? [], $diasFiltro);
      $seriePagina = $paginaSel !== '' ? ultimosDias($gaSite['paginas_diario'][$paginaSel] ?? [], $diasFiltro) : [];

      // Periodo anterior de mesmo tamanho, pra responder "esta crescendo?".
      // Numero solto nao diz nada: 40 usuarios e bom ou ruim depende do que veio antes.
      $todos = $gaSite['diario'] ?? [];
      $anterior = count($todos) > $diasFiltro
          ? array_slice($todos, max(0, count($todos) - $diasFiltro * 2), $diasFiltro)
          : [];
      $usuarios = somaSerie($serieGeral, 'usuarios');
      $sessoes = somaSerie($serieGeral, 'sessoes');
      $usuariosAntes = somaSerie($anterior, 'usuarios');
      $sessoesAntes = somaSerie($anterior, 'sessoes');
      $variacao = static function (int $agora, int $antes): array {
          if ($antes <= 0) {
              return ['—', 'muted'];
          }
          $pct = ($agora - $antes) / $antes * 100;
          $seta = $pct > 0 ? '▲' : ($pct < 0 ? '▼' : '=');
          return [$seta . ' ' . number_format(abs($pct), 0, ',', '.') . '%',
                  $pct >= 0 ? 'm-bom' : 'm-alerta'];
      };
      [$varUsuarios, $corUsuarios] = $variacao($usuarios, $usuariosAntes);
      [$varSessoes, $corSessoes] = $variacao($sessoes, $sessoesAntes);
      $mediaDia = $serieGeral ? $usuarios / count($serieGeral) : 0;
      ?>

      <div class="kpis kpis-4">
        <div class="kpi"><div class="chip c-blue"><?= icon('users') ?></div>
          <div><b><?= num($usuarios) ?></b><span>Usuários únicos · <?= $diasFiltro ?>d</span></div></div>
        <div class="kpi"><div class="chip c-blue"><?= icon('eye') ?></div>
          <div><b><?= num($sessoes) ?></b><span>Sessões</span></div></div>
        <div class="kpi"><div class="chip c-amber"><?= icon('chart') ?></div>
          <div><b><?= num($mediaDia, 1) ?></b><span>Usuários por dia</span></div></div>
        <div class="kpi"><div class="chip c-green"><?= icon('gauge') ?></div>
          <div><b class="<?= $corUsuarios ?>"><?= e($varUsuarios) ?></b><span>vs. <?= $diasFiltro ?>d anteriores</span></div></div>
      </div>

      <?php if ($anterior): ?>
        <p class="nota">
          Nos <?= $diasFiltro ?> dias anteriores foram <b><?= num($usuariosAntes) ?></b> usuários e
          <b><?= num($sessoesAntes) ?></b> sessões. Agora são <b><?= num($usuarios) ?></b> e
          <b><?= num($sessoes) ?></b> — <?= e($varUsuarios) ?> em usuários e <?= e($varSessoes) ?> em sessões.
        </p>
      <?php endif; ?>

      <?php if ($paginaSel !== ''): ?>
        <div class="box" style="margin-bottom:14px;">
          <h3><?= icon('file') ?> <?= e($paginaSel) ?> · últimos <?= $diasFiltro ?> dias</h3>
          <?php if ($seriePagina): ?>
            <div class="kpis" style="margin:6px 0 18px;">
              <div class="kpi"><div class="chip c-amber"><?= icon('eye') ?></div><div><b><?= number_format(somaSerie($seriePagina, 'views'), 0, ',', '.') ?></b><span>Visualizações</span></div></div>
              <div class="kpi"><div class="chip c-blue"><?= icon('users') ?></div><div><b><?= number_format(somaSerie($seriePagina, 'usuarios'), 0, ',', '.') ?></b><span>Usuários</span></div></div>
              <div class="kpi"><div class="chip c-green"><?= icon('chart') ?></div><div><b><?= number_format(somaSerie($seriePagina, 'views') / max(1, count($seriePagina)), 1, ',', '.') ?></b><span>Média por dia</span></div></div>
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
                  ? abaUrl('trafego', ['dias' => $diasFiltro, 'pagina' => $r['pagina']])
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
    <?php elseif ($aba === 'trafego'): ?>
      <p class="muted">Snapshot do Google Analytics ainda não gerado. Use “Atualizar dados” no topo.</p>
    <?php endif; /* fim da aba trafego */ ?>

    <?php if ($aba === 'ads'): ?>
      <?php if (!$ads || !empty($ads['erro'])): ?>
        <p class="aviso-refresh">Dados do Google Ads indisponíveis<?= $ads && !empty($ads['erro']) ? ': ' . e($ads['erro']) : '' ?>.
           <?php if ($ads && str_contains((string) $ads['erro'], 'invalid_grant')): ?>
             <br />O acesso à API do Google Ads expirou — é preciso gerar um novo token
             (<code>python3 automation/ads/renovar-token.py</code>).
           <?php else: ?>
             Use “Atualizar dados” no topo.
           <?php endif; ?>
        </p>
      <?php else:
        if (!empty($ads['aviso'])) {
            echo '<p class="aviso-refresh">' . e($ads['aviso']) . '</p>';
        }
        $periodoAds = (string) ($_GET['p'] ?? 'd28');
        if (!in_array($periodoAds, ['hoje', 'd7', 'd28'], true)) { $periodoAds = 'd28'; }
        $rotPeriodo = ['hoje' => 'Hoje', 'd7' => '7 dias', 'd28' => '28 dias'][$periodoAds];
        $p = $ads['periodos'][$periodoAds];
        $linhas = $ads['campanhas'][$periodoAds];
        $leadsNoPeriodo = $periodoAds === 'd28' ? $totals['total'] : $totals['ultimos7'];
      ?>
      <div class="barra-filtro">
        <span class="bf-label">Período</span>
        <span class="bf-grupo">
          <?php foreach (['hoje' => 'Hoje', 'd7' => '7 dias', 'd28' => '28 dias'] as $k => $rot): ?>
            <a class="chip-filtro <?= $periodoAds === $k ? 'ativo' : '' ?>"
               href="<?= e(abaUrl('ads', ['p' => $k])) ?>"><?= $rot ?></a>
          <?php endforeach; ?>
        </span>
        <span class="bf-sep"></span>
        <a class="chip-filtro" href="<?= e(abaUrl('funil', ['exportar' => 'ads'])) ?>"
           title="CSV no formato de upload de conversões offline do Google Ads">
          <?= icon('chart') ?> Exportar conversões
        </a>
      </div>

      <div class="kpis kpis-4">
        <div class="kpi"><div class="chip c-amber"><?= icon('money') ?></div><div><b>R$ <?= num($p['custo'], 2) ?></b><span>Custo · <?= e($rotPeriodo) ?></span></div></div>
        <div class="kpi"><div class="chip c-blue"><?= icon('target') ?></div><div><b><?= num($p['cliques']) ?></b><span>Cliques</span></div></div>
        <div class="kpi"><div class="chip c-blue"><?= icon('eye') ?></div><div><b><?= num($p['impressoes']) ?></b><span>Impressões</span></div></div>
        <div class="kpi"><div class="chip c-blue"><?= icon('chart') ?></div><div><b><?= num($p['ctr'], 2) ?>%</b><span>CTR</span></div></div>
        <div class="kpi"><div class="chip c-amber"><?= icon('money') ?></div><div><b>R$ <?= num($p['cpc_medio'], 2) ?></b><span>CPC médio</span></div></div>
        <div class="kpi"><div class="chip c-green"><?= icon('check') ?></div><div><b><?= num($p['conversoes'], 1) ?></b><span>Conversões</span></div></div>
        <div class="kpi"><div class="chip c-green"><?= icon('money') ?></div><div><b><?= $p['conversoes'] > 0 ? 'R$ ' . num($p['custo_por_conversao'], 2) : '—' ?></b><span>Custo / conv.</span></div></div>
        <div class="kpi"><div class="chip c-green"><?= icon('target') ?></div><div><b><?= num($p['taxa_conversao'], 2) ?>%</b><span>Taxa de conv.</span></div></div>
      </div>

      <div class="box" style="margin-bottom:14px;">
        <h3><?= icon('megaphone') ?> Desempenho por campanha · <?= e($rotPeriodo) ?></h3>
        <?php if ($linhas): ?>
          <div class="tbl-scroll"><table class="metricas">
            <thead>
              <tr>
                <th>Campanha</th><th>Status</th><th>Cliques</th><th>Impr.</th><th>CTR</th>
                <th>CPC méd.</th><th>Custo</th><th>Conv.</th><th>Custo/conv.</th><th>Taxa conv.</th><th>Impr. perdidas</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($linhas as $row): ?>
                <tr>
                  <td><?= e($row['nome']) ?></td>
                  <td><span class="tag <?= $row['status'] === 'ENABLED' ? 'fechado' : 'teste' ?>"><?= $row['status'] === 'ENABLED' ? 'ativa' : 'pausada' ?></span></td>
                  <td><?= num($row['cliques']) ?></td>
                  <td><?= num($row['impressoes']) ?></td>
                  <td><?= num($row['ctr'], 2) ?>%</td>
                  <td>R$ <?= num($row['cpc_medio'], 2) ?></td>
                  <td><b>R$ <?= num($row['custo'], 2) ?></b></td>
                  <td class="<?= $row['conversoes'] > 0 ? 'm-bom' : 'm-alerta' ?>"><?= num($row['conversoes'], 1) ?></td>
                  <td><?= $row['conversoes'] > 0 ? 'R$ ' . num($row['custo_por_conversao'], 2) : '—' ?></td>
                  <td><?= num($row['taxa_conversao'], 2) ?>%</td>
                  <td class="muted"><?php
                    $perdas = [];
                    if (!empty($row['perdida_orcamento'])) { $perdas[] = num($row['perdida_orcamento'], 1) . '% orç.'; }
                    if (!empty($row['perdida_ranking'])) { $perdas[] = num($row['perdida_ranking'], 1) . '% rank'; }
                    echo $perdas ? e(implode(' · ', $perdas)) : '—';
                  ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
                <td>Total</td><td></td>
                <td><?= num($p['cliques']) ?></td><td><?= num($p['impressoes']) ?></td>
                <td><?= num($p['ctr'], 2) ?>%</td><td>R$ <?= num($p['cpc_medio'], 2) ?></td>
                <td>R$ <?= num($p['custo'], 2) ?></td><td><?= num($p['conversoes'], 1) ?></td>
                <td><?= $p['conversoes'] > 0 ? 'R$ ' . num($p['custo_por_conversao'], 2) : '—' ?></td>
                <td><?= num($p['taxa_conversao'], 2) ?>%</td><td></td>
              </tr>
            </tfoot>
          </table></div>
          <p class="nota">
            No período: <b>R$ <?= num($p['custo'], 2) ?></b> investidos e <b><?= num($p['cliques']) ?></b> cliques.
            Entraram <b><?= (int) $leadsNoPeriodo ?></b> leads no site, o que dá
            <b><?= custoPorLead((float) $p['custo'], (int) $leadsNoPeriodo) ?></b> por lead.
            <?php if ($p['cliques'] > 0 && $p['conversoes'] == 0): ?>
              <br />Nenhuma conversão registrada pelo Google ainda — confira abaixo se as ações de conversão estão ativas.
            <?php endif; ?>
          </p>
        <?php else: ?>
          <p class="muted">Nenhuma campanha com atividade nesse período.</p>
        <?php endif; ?>
      </div>

      <?php $serieAds = ultimosDias($ads['diario'] ?? [], $periodoAds === 'hoje' ? 7 : ($periodoAds === 'd7' ? 7 : 28)); ?>
      <?php if (count($serieAds) > 1): ?>
        <div class="box" style="margin-bottom:14px;">
          <h3><?= icon('chart') ?> Investimento diário</h3>
          <?= dailyChart($serieAds, 'custo', 'cliques', 'reais', 'cliques', 2) ?>
        </div>
      <?php endif; ?>

      <div class="boxes">
        <?php $campHoje = $gaSite['campanhas_periodo'][$periodoAds] ?? []; ?>
        <div class="box">
          <h3><?= icon('globe') ?> Acessos por campanha (Analytics) · <?= e($rotPeriodo) ?></h3>
          <?php if ($campHoje): ?>
            <div class="tbl-scroll"><table class="metricas">
              <thead><tr><th>Campanha</th><th>Sessões</th><th>Conv.</th><th>Rejeição</th><th>Tempo</th></tr></thead>
              <tbody>
                <?php foreach ($campHoje as $row): ?>
                  <tr>
                    <td><?= e($row['campanha']) ?><br /><span class="muted" style="font-size:11px;"><?= e($row['origem']) ?></span></td>
                    <td><?= (int) $row['sessoes'] ?></td>
                    <td><?= num($row['conversoes'], 1) ?></td>
                    <td class="<?= $row['rejeicao'] >= 90 ? 'm-alerta' : '' ?>"><?= num($row['rejeicao'], 0) ?>%</td>
                    <td><?= num($row['duracao'], 0) ?>s</td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table></div>
            <p class="nota">Cliques cobrados pelo Google costumam ser <b>mais</b> que as sessões daqui: quem
              fecha antes da página carregar não vira sessão. Diferença muito grande indica clique acidental.</p>
          <?php else: ?>
            <p class="muted">Nenhuma sessão de campanha no período.</p>
          <?php endif; ?>
        </div>

        <div class="box">
          <h3><?= icon('file') ?> Onde o clique de campanha cai · 28d</h3>
          <?php $lpPagas = $gaSite['landing_pagas'] ?? []; ?>
          <?php if ($lpPagas): ?>
            <div class="tbl-scroll"><table class="metricas">
              <thead><tr><th>Página de entrada</th><th>Sessões</th><th>Rejeição</th></tr></thead>
              <tbody>
                <?php foreach ($lpPagas as $row): ?>
                  <tr>
                    <td><?= e($row['pagina'] !== '' ? $row['pagina'] : '(não informada)') ?><br />
                        <span class="muted" style="font-size:11px;"><?= e($row['campanha']) ?></span></td>
                    <td><?= (int) $row['sessoes'] ?></td>
                    <td class="<?= $row['rejeicao'] >= 90 ? 'm-alerta' : '' ?>"><?= num($row['rejeicao'], 0) ?>%</td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table></div>
          <?php else: ?>
            <p class="muted">Sem dados de página de entrada por campanha.</p>
          <?php endif; ?>
        </div>

        <?php $convCfg = $ads['conversoes_config'] ?? []; ?>
        <?php if ($convCfg): ?>
          <div class="box" style="grid-column:1/-1;">
            <h3><?= icon('target') ?> Ações de conversão configuradas no Google Ads</h3>
            <div class="tbl-scroll"><table class="metricas">
              <thead><tr><th>Ação</th><th>Status</th><th>Categoria</th><th>Usada para lances</th></tr></thead>
              <tbody>
                <?php foreach ($convCfg as $row): ?>
                  <tr>
                    <td><?= e($row['nome']) ?></td>
                    <td class="<?= $row['status'] !== 'ENABLED' ? 'm-alerta' : 'm-bom' ?>"><?= e(strtolower($row['status'])) ?></td>
                    <td class="muted"><?= e(strtolower($row['categoria'])) ?></td>
                    <td><?= $row['principal'] ? 'sim' : '—' ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table></div>
            <p class="nota">As ações vindas do GA4 (<b>generate_lead</b>, <b>whatsapp_click</b>, <b>purchase</b>)
              precisam estar <b>ativas</b> para o Google contar conversão. Se aparecerem como “hidden”,
              nenhuma conversão será registrada mesmo com lead entrando no site.</p>
          </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    <?php endif; /* fim da aba ads */ ?>

    <?php if ($aba === 'funil'): ?>
    <div class="kpis">
      <div class="kpi"><div class="chip c-amber"><?= icon('users') ?></div><div><b><?= $totals['total'] ?></b><span>Leads (form)</span></div></div>
      <div class="kpi"><div class="chip c-blue"><?= icon('clock') ?></div><div><b><?= $totals['ultimos7'] ?></b><span>Últimos 7 dias</span></div></div>
      <div class="kpi"><div class="chip c-green"><?= icon('zap') ?></div><div><b><?= $totals['zap'] ?></b><span>Cliques WhatsApp</span></div></div>
      <div class="kpi"><div class="chip c-green"><?= icon('check') ?></div><div><b><?= $totals['fechados'] ?></b><span>Vendas fechadas</span></div></div>
      <div class="kpi"><div class="chip c-amber"><?= icon('money') ?></div><div><b>R$ <?= num($totals['receita']) ?></b><span>Receita registrada</span></div></div>
    </div>

    <div class="sec"><h2><?= icon('target') ?> Etapas <span class="muted" style="font-weight:400;font-size:12px;">· clique para filtrar a lista</span></h2></div>
    <div class="funil">
      <?php
      $etapasFunil = [
          'novo' => ['Novos', 'novo'],
          'zap' => ['Cliques WhatsApp', 'zap'],
          'qualificado' => ['Qualificados', 'qualificado'],
          'demo' => ['Demo agendada', 'demo'],
          'fechado' => ['Vendas', 'fechado'],
          'perdido' => ['Perdidos', 'perdido'],
      ];
      foreach ($etapasFunil as $chave => [$rot, $cls]): ?>
        <a class="funil-etapa" href="<?= e(abaUrl('funil', ['etapa' => $chave])) ?>">
          <b><?= (int) ($funil[$chave] ?? 0) ?></b>
          <span class="tag <?= $cls ?>"><?= $rot ?></span>
        </a>
      <?php endforeach; ?>
    </div>

    <div class="barra-filtro">
      <span class="bf-label">Mostrar</span>
      <span class="bf-grupo">
        <?php
        $filtrosEtapa = ['' => 'Todos', 'novo' => 'Novos', 'zap' => 'WhatsApp', 'qualificado' => 'Qualificados',
                         'demo' => 'Reunião', 'fechado' => 'Vendas', 'perdido' => 'Perdidos', 'teste' => 'Testes'];
        foreach ($filtrosEtapa as $chave => $rot): ?>
          <a class="chip-filtro <?= $etapaSel === $chave ? 'ativo' : '' ?>"
             href="<?= e(abaUrl('funil', ['etapa' => $chave])) ?>"><?= $rot ?></a>
        <?php endforeach; ?>
      </span>
      <span class="bf-sep"></span>
      <a class="chip-filtro" href="?exportar=ads" title="CSV no formato de upload de conversões offline do Google Ads">
        <?= icon('chart') ?> Exportar conversões
      </a>
    </div>

    <?php if (!empty($_GET['marcado'])): ?>
      <p class="aviso-refresh">Etapa registrada — o evento correspondente vai pro Google em até 1 minuto.</p>
    <?php endif; ?>
    <?php if ($dbAviso !== ''): ?>
      <p class="muted"><?= e($dbAviso) ?></p>
    <?php else: ?>
      <div class="box tbl-scroll">
        <table>
          <tr>
            <th>#</th><th>Data</th><th>Nome</th><th>WhatsApp</th><th>Origem</th>
            <th>Campanha</th><th>Fonte</th><th>Ads?</th><th>Etapa</th><th>Avançar</th><th>Valor</th>
          </tr>
          <?php foreach ($leads as $lead): ?>
            <tr>
              <td class="muted"><?= (int) $lead['id'] ?></td>
              <td><?= e(brDateTime($lead['created_at'])) ?></td>
              <td><?= e($lead['nome']) ?><?= $lead['status'] === 'zap' && $lead['mensagem'] ? '<br /><span class="muted">' . e($lead['mensagem']) . '</span>' : '' ?></td>
              <td><?= e($lead['telefone']) ?></td>
              <td><?= e($lead['origem']) ?></td>
              <td><?= e($lead['utm_campaign'] ?: '-') ?><?= $lead['utm_content'] ? ' / ' . e($lead['utm_content']) : '' ?></td>
              <td><?= e($lead['utm_source'] ?: 'direto') ?><?= $lead['sim_faturamento'] ? '<br /><span class="muted">simulou ' . e($lead['sim_faturamento']) . '</span>' : '' ?></td>
              <td><?= $lead['gclid'] ? '<span title="veio de anúncio">sim</span>' : '-' ?></td>
              <td><span class="tag <?= e($lead['status']) ?>"><?= e($lead['status']) ?></span></td>
              <td>
                <form method="post" class="acoes">
                  <input type="hidden" name="lead_id" value="<?= (int) $lead['id'] ?>" />
                  <?php
                  $etapas = [
                      'qualificado' => ['Qualificar', 'Falei com ele: é oportunidade real'],
                      'demo' => ['Reunião', 'Demonstração/reunião agendada'],
                      'perdido' => ['Perdido', 'Não seguiu'],
                  ];
                  foreach ($etapas as $chave => [$rot, $titulo]):
                      if ($lead['status'] === $chave || $lead['status'] === 'fechado') continue; ?>
                    <button type="submit" name="marcar_lead" value="<?= $chave ?>" class="btn-etapa e-<?= $chave ?>" title="<?= e($titulo) ?>"><?= $rot ?></button>
                  <?php endforeach; ?>
                  <?php if ($lead['status'] !== 'fechado'): ?>
                    <span class="venda-wrap">
                      <input type="text" name="valor" value="3000" class="inp-valor" title="Valor fechado (R$)" inputmode="decimal" />
                      <button type="submit" name="marcar_lead" value="fechado" class="btn-etapa e-fechado" title="Comprou: registra a venda e manda o valor pro Google">Comprou</button>
                    </span>
                  <?php else: ?>
                    <button type="submit" name="marcar_lead" value="novo" class="btn-etapa" title="Reabrir o lead">↺ reabrir</button>
                  <?php endif; ?>
                </form>
              </td>
              <td><?= $lead['close_value'] !== null ? 'R$ ' . number_format((float) $lead['close_value'], 0, ',', '.') : '-' ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    <?php endif; ?>
    <?php endif; /* fim da aba funil */ ?>

    <?php if ($aba === 'historico'): ?>
      <?php
      $areaSel = (string) ($_GET['area'] ?? '');
      $areas = ['ads' => 'Anúncios', 'medicao' => 'Medição', 'painel' => 'Painel', 'site' => 'Site'];
      $lista = $historico;
      if ($areaSel !== '' && isset($areas[$areaSel])) {
          $lista = array_values(array_filter($lista, static fn ($h) => ($h['area'] ?? '') === $areaSel));
      }
      $porArea = [];
      foreach ($historico as $h) { $porArea[$h['area'] ?? '?'] = ($porArea[$h['area'] ?? '?'] ?? 0) + 1; }
      $comEfeito = count(array_filter($historico, static fn ($h) => trim((string) ($h['efeito'] ?? '')) !== ''));
      ?>
      <div class="kpis">
        <div class="kpi"><div class="chip c-amber"><?= icon('check') ?></div><div><b><?= count($historico) ?></b><span>Melhorias aplicadas</span></div></div>
        <div class="kpi"><div class="chip c-green"><?= icon('chart') ?></div><div><b><?= $comEfeito ?></b><span>Com efeito medido</span></div></div>
        <div class="kpi"><div class="chip c-blue"><?= icon('megaphone') ?></div><div><b><?= $porArea['ads'] ?? 0 ?></b><span>Em anúncios</span></div></div>
        <div class="kpi"><div class="chip c-blue"><?= icon('target') ?></div><div><b><?= $porArea['medicao'] ?? 0 ?></b><span>Em medição</span></div></div>
      </div>

      <div class="barra-filtro">
        <span class="bf-label">Área</span>
        <span class="bf-grupo">
          <a class="chip-filtro <?= $areaSel === '' ? 'ativo' : '' ?>" href="<?= e(abaUrl('historico')) ?>">Todas</a>
          <?php foreach ($areas as $k => $rot): if (empty($porArea[$k])) continue; ?>
            <a class="chip-filtro <?= $areaSel === $k ? 'ativo' : '' ?>"
               href="<?= e(abaUrl('historico', ['area' => $k])) ?>"><?= e($rot) ?> <?= (int) $porArea[$k] ?></a>
          <?php endforeach; ?>
        </span>
      </div>

      <?php if (!$lista): ?>
        <p class="muted">Nenhum registro nessa área.</p>
      <?php else: ?>
        <ol class="linha-tempo">
          <?php foreach ($lista as $h): ?>
            <li>
              <div class="lt-marca"></div>
              <div class="lt-corpo">
                <div class="lt-topo">
                  <span class="lt-data"><?= e(brDate($h['data'] ?? '')) ?></span>
                  <span class="tag area-<?= e($h['area'] ?? '') ?>"><?= e($areas[$h['area'] ?? ''] ?? $h['area'] ?? '-') ?></span>
                </div>
                <h4><?= e($h['titulo'] ?? '') ?></h4>
                <?php if (!empty($h['porque'])): ?>
                  <p class="lt-porque"><b>Por quê:</b> <?= e($h['porque']) ?></p>
                <?php endif; ?>
                <?php if (!empty($h['efeito'])): ?>
                  <p class="lt-efeito"><b>Resultado:</b> <?= e($h['efeito']) ?></p>
                <?php else: ?>
                  <p class="lt-pendente">Efeito ainda em observação.</p>
                <?php endif; ?>
              </div>
            </li>
          <?php endforeach; ?>
        </ol>
      <?php endif; ?>
    <?php endif; /* fim da aba historico */ ?>
  </div>
<?php endif; ?>
</body>
</html>
