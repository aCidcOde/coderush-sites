#!/usr/bin/env node

const crypto = require("node:crypto");

function base64UrlEncode(input) {
  return Buffer.from(input)
    .toString("base64")
    .replace(/\+/g, "-")
    .replace(/\//g, "_")
    .replace(/=+$/g, "");
}

function signPayload(payloadJson, secret) {
  const payloadPart = base64UrlEncode(payloadJson);
  const signaturePart = base64UrlEncode(
    crypto.createHmac("sha256", secret).update(payloadPart).digest()
  );
  return `${payloadPart}.${signaturePart}`;
}

function toInt(value, fallback = 0) {
  const parsed = Number.parseInt(String(value ?? ""), 10);
  return Number.isFinite(parsed) ? parsed : fallback;
}

function requiredEnv(name) {
  const value = process.env[name];
  if (!value || String(value).trim() === "") {
    throw new Error(`Variavel obrigatoria ausente: ${name}`);
  }
  return String(value).trim();
}

function normalizeEmailList(rawValue) {
  return String(rawValue)
    .split(",")
    .map((entry) => entry.trim())
    .filter(Boolean);
}

function buildActionLink(baseUrl, token) {
  const separator = baseUrl.includes("?") ? "&" : "?";
  return `${baseUrl}${separator}token=${encodeURIComponent(token)}`;
}

async function sendWithResend({ apiKey, from, to, subject, html, text }) {
  const response = await fetch("https://api.resend.com/emails", {
    method: "POST",
    headers: {
      Authorization: `Bearer ${apiKey}`,
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      from,
      to,
      subject,
      html,
      text
    })
  });

  if (!response.ok) {
    const body = await response.text();
    throw new Error(`Falha ao enviar via Resend (${response.status}): ${body}`);
  }

  return response.json();
}

async function run() {
  const dryRun = process.argv.includes("--dry-run");

  const baseUrl = requiredEnv("PR_APPROVAL_BASE_URL");
  const signingSecret = requiredEnv("PR_APPROVAL_SIGNING_SECRET");
  const prNumber = toInt(requiredEnv("PR_NUMBER"));
  const prUrl = requiredEnv("PR_URL");
  const repo = requiredEnv("REPO");
  const runId = requiredEnv("RUN_ID");
  const branch = requiredEnv("BRANCH_NAME");
  const commitSha = requiredEnv("COMMIT_SHA");
  const prTitle = process.env.PR_TITLE?.trim() || "PR automatica do blog";
  const ttlHours = Math.max(1, toInt(process.env.PR_APPROVAL_TTL_HOURS, 24));
  const now = Math.floor(Date.now() / 1000);
  const exp = now + ttlHours * 3600;
  const toEmails = normalizeEmailList(requiredEnv("PR_APPROVAL_TO_EMAILS"));

  if (toEmails.length === 0) {
    throw new Error("PR_APPROVAL_TO_EMAILS nao possui nenhum destinatario valido.");
  }

  const commonPayload = {
    repo,
    pr_number: prNumber,
    pr_url: prUrl,
    run_id: runId,
    branch,
    sha: commitSha,
    iat: now,
    exp
  };

  const approveToken = signPayload(
    JSON.stringify({
      ...commonPayload,
      action: "approve",
      nonce: crypto.randomUUID()
    }),
    signingSecret
  );
  const rejectToken = signPayload(
    JSON.stringify({
      ...commonPayload,
      action: "reject",
      nonce: crypto.randomUUID()
    }),
    signingSecret
  );

  const approveUrl = buildActionLink(baseUrl, approveToken);
  const rejectUrl = buildActionLink(baseUrl, rejectToken);

  const subject = `[Blog Bot] Revisao pendente PR #${prNumber}`;
  const text = [
    "A automacao do blog abriu uma PR e precisa da sua decisao.",
    "",
    `Repositorio: ${repo}`,
    `PR: #${prNumber} - ${prTitle}`,
    `URL: ${prUrl}`,
    `Branch: ${branch}`,
    `Commit: ${commitSha}`,
    `Workflow run: ${runId}`,
    `Expira em: ${new Date(exp * 1000).toISOString()}`,
    "",
    `Aprovar: ${approveUrl}`,
    `Reprovar: ${rejectUrl}`
  ].join("\n");

  const html = [
    "<h2>PR automatica aguardando decisao</h2>",
    "<p>O robo do blog abriu uma PR e esta aguardando aprovacao ou reprovacao.</p>",
    `<p><strong>Repositorio:</strong> ${repo}</p>`,
    `<p><strong>PR:</strong> #${prNumber} - ${prTitle}</p>`,
    `<p><strong>Link da PR:</strong> <a href="${prUrl}">${prUrl}</a></p>`,
    `<p><strong>Branch:</strong> ${branch}</p>`,
    `<p><strong>Run:</strong> ${runId}</p>`,
    `<p><strong>Expira em:</strong> ${new Date(exp * 1000).toISOString()}</p>`,
    `<p><a href="${approveUrl}" style="padding:10px 14px;background:#166534;color:#fff;text-decoration:none;border-radius:6px;">Aprovar PR</a></p>`,
    `<p><a href="${rejectUrl}" style="padding:10px 14px;background:#991b1b;color:#fff;text-decoration:none;border-radius:6px;">Reprovar PR</a></p>`,
    "<p>Se o link expirar, rode novamente o workflow para gerar nova solicitacao.</p>"
  ].join("\n");

  if (dryRun) {
    console.log(
      JSON.stringify(
        {
          subject,
          to: toEmails,
          payload: commonPayload,
          approveUrl,
          rejectUrl
        },
        null,
        2
      )
    );
    return;
  }

  const resendApiKey = requiredEnv("PR_APPROVAL_RESEND_API_KEY");
  const fromEmail = requiredEnv("PR_APPROVAL_FROM_EMAIL");
  const result = await sendWithResend({
    apiKey: resendApiKey,
    from: fromEmail,
    to: toEmails,
    subject,
    html,
    text
  });

  console.log("Email de aprovacao enviado com sucesso.");
  console.log(JSON.stringify(result, null, 2));
}

run().catch((error) => {
  console.error("Falha no envio de aprovacao por email:", error.message);
  process.exit(1);
});
