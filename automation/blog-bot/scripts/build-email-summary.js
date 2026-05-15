#!/usr/bin/env node
const fs = require('fs');
const path = require('path');

const REPORTS_DIR = path.join(__dirname, '..', 'reports');
const BODY_OUT = path.join(REPORTS_DIR, 'last-email-body.txt');

const blogbotOutcome = process.env.BLOGBOT_OUTCOME || 'unknown';
const commitOutcome = process.env.COMMIT_OUTCOME || 'unknown';
const runId = process.env.RUN_ID || '';
const runUrl = process.env.RUN_URL || '';

function nowBrtIso() {
  const d = new Date();
  const tzOffset = -3 * 60;
  const local = new Date(d.getTime() + tzOffset * 60 * 1000);
  return local.toISOString().replace('Z', '-03:00');
}

function findLatestReport() {
  if (!fs.existsSync(REPORTS_DIR)) return null;
  const files = fs
    .readdirSync(REPORTS_DIR)
    .filter((f) => /^run-\d{4}-\d{2}-\d{2}-publish\.json$/.test(f))
    .sort();
  return files.length ? path.join(REPORTS_DIR, files[files.length - 1]) : null;
}

function summarize(report) {
  const lines = [];
  lines.push(`Modo: ${report.mode}`);
  lines.push(`Data: ${report.date} (${report.timezone})`);
  lines.push(`Iniciado: ${report.startedAt}`);
  lines.push(`Finalizado: ${report.finishedAt}`);
  lines.push(`Status geral: ${report.ok ? 'OK' : 'FALHA'}`);
  lines.push('');
  lines.push('=== POR SITE ===');
  for (const s of report.sites || []) {
    const status = s.ok ? 'OK' : 'FALHA';
    lines.push('');
    lines.push(`[${status}] ${s.siteId}`);
    lines.push(`  Tema: ${s.theme || '-'}`);
    lines.push(`  Ângulo: ${s.angle || '-'}`);
    const pr = s.publishResult || {};
    if (pr.target === 'api') {
      lines.push(`  Target: API (status ${pr.apiStatus})`);
      if (pr.apiUrl) lines.push(`  URL: ${pr.apiUrl}`);
    } else if (pr.target === 'files') {
      lines.push(`  Target: arquivos`);
      if (pr.postPath) lines.push(`  Path: ${pr.postPath}`);
    }
    if (s.warning) lines.push(`  Aviso: ${s.warning}`);
    if (s.research && s.research.errors && s.research.errors.length) {
      lines.push(`  Erros de research: ${s.research.errors.length}`);
    }
  }
  if (report.duplications && report.duplications.length) {
    lines.push('');
    lines.push(`Duplicações detectadas: ${report.duplications.length}`);
  }
  return lines.join('\n');
}

function main() {
  const reportPath = findLatestReport();
  let body;
  let subject;
  const dateStr = new Date().toISOString().slice(0, 10);

  if (blogbotOutcome === 'failure' || commitOutcome === 'failure') {
    subject = `[FALHA] Blog Bot ${dateStr} — run ${runId}`;
    body = [
      `Execução do blog-bot FALHOU.`,
      ``,
      `blog-bot outcome: ${blogbotOutcome}`,
      `commit outcome: ${commitOutcome}`,
      `Run ID: ${runId}`,
      `Logs: ${runUrl}`,
      ``,
    ].join('\n');
    if (reportPath && fs.existsSync(reportPath)) {
      try {
        const report = JSON.parse(fs.readFileSync(reportPath, 'utf8'));
        body += '\n' + summarize(report);
      } catch (err) {
        body += `\nNão foi possível ler o report mais recente: ${err.message}\n`;
      }
    } else {
      body += '\nNenhum report encontrado em automation/blog-bot/reports/.\n';
    }
  } else if (!reportPath) {
    subject = `[?] Blog Bot ${dateStr} — sem report (run ${runId})`;
    body = [
      `Execução concluiu mas nenhum report foi encontrado em automation/blog-bot/reports/.`,
      ``,
      `Run ID: ${runId}`,
      `Logs: ${runUrl}`,
    ].join('\n');
  } else {
    const report = JSON.parse(fs.readFileSync(reportPath, 'utf8'));
    const tag = report.ok ? 'OK' : 'PARCIAL';
    subject = `[${tag}] Blog Bot ${report.date} — ${(report.sites || []).length} sites`;
    body = [
      `Blog bot rodou em ${nowBrtIso()}.`,
      ``,
      `Run ID: ${runId}`,
      `Logs: ${runUrl}`,
      ``,
      summarize(report),
    ].join('\n');
  }

  fs.mkdirSync(path.dirname(BODY_OUT), { recursive: true });
  fs.writeFileSync(BODY_OUT, body, 'utf8');

  const githubOutput = process.env.GITHUB_OUTPUT;
  if (githubOutput) {
    fs.appendFileSync(githubOutput, `subject=${subject}\n`);
  } else {
    console.log(`subject=${subject}`);
  }
  console.log('Email body gerado em', BODY_OUT);
}

main();
