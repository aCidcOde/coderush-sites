# Aprovação de PR por e-mail (Blog Bot)

## Objetivo

Quando o workflow semanal abre a PR automática, os aprovadores recebem um e-mail com dois links:

- Aprovar PR
- Reprovar PR

Cada link chama `pr-approval.php` com token assinado e de uso único.

## Contrato de evento enviado no e-mail

Payload assinado no token:

```json
{
  "repo": "owner/repo",
  "pr_number": 123,
  "pr_url": "https://github.com/owner/repo/pull/123",
  "run_id": "123456789",
  "branch": "automation/blog-weekly-123456789",
  "sha": "abcdef123456...",
  "iat": 1760000000,
  "exp": 1760086400,
  "action": "approve",
  "nonce": "uuid-v4"
}
```

Campos mínimos usados para rastreio/auditoria:

- `repo`, `pr_number`, `run_id`, `branch`, `sha`
- `action` (approve/reject)
- `iat`, `exp`, `nonce`

## Variáveis necessárias

### GitHub Secrets (workflow)

- `PR_APPROVAL_BASE_URL` (URL pública do endpoint, ex.: `https://seu-dominio/pr-approval.php`)
- `PR_APPROVAL_SIGNING_SECRET` (segredo HMAC compartilhado)
- `PR_APPROVAL_RESEND_API_KEY` (token da API Resend)
- `PR_APPROVAL_FROM_EMAIL` (remetente validado no Resend)
- `PR_APPROVAL_TO_EMAILS` (lista separada por vírgula)

### GitHub Variables (opcional)

- `PR_APPROVAL_TTL_HOURS` (padrão: 24)

### Variáveis no servidor (arquivo `.env`)

- `PR_APPROVAL_SIGNING_SECRET` (mesmo segredo do workflow)
- `PR_APPROVAL_GITHUB_REPO` (formato `owner/repo`)
- `GITHUB_APPROVAL_BOT_TOKEN` (token com permissão de review em PR)

## Segurança aplicada

- Token assinado com HMAC SHA-256
- Expiração (`exp`) e emissão (`iat`)
- Uso único por `nonce` (anti-replay)
- Validação de repositório esperado
- Log de auditoria em `storage/pr-approval-audit.log`

## Arquivos envolvidos

- Workflow: `.github/workflows/blog-automation.yml`
- Envio e-mail: `automation/pr-approval/send-approval-email.js`
- Endpoint de decisão: `pr-approval.php`
