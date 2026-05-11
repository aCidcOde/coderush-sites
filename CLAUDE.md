# Coderush Sites — Guidelines

Repositório multi-site (5 marcas) com automação de blog via OpenRouter + GitHub Actions. Trabalho direto na branch `main` — cada push dispara deploy webhook.

## Arquitetura do blog-bot

`automation/blog-bot/` gera posts editoriais e publica em 5 marcas com 2 tipos de destino:

**File-based** (escreve PHP/HTML local + atualiza home/blog index/sitemap, deploy via push):
- `coderush` (CodeRush hub)
- `codafacil` (Codafacil.dev)
- `fluxointeligenteia` (FluxoInteligente IA)
- `sistemavendadireta` (Sistema Venda Direta)

**API target** (POST multipart cover+payload pra API externa):
- `emergency` (Emergency Documentação) → `https://app-hml.emergency.com.br/api/blog/posts`

Pipeline: pickTheme → research RSS → AI prompt → generate JSON contract → generate cover (gpt-5-image / gemini-3-pro fallback) → publish.

## Posicionamento FluxoInteligente IA

FluxoInteligente IA deve ser tratada como empresa de **agentes corporativos integrados ao negócio**, com automação, RAG, tools, canais, permissões, auditoria, observabilidade e governança.

- Rotas principais (v2.0): `/`, `/blog/`
- Páginas `/agentes-corporativos/` e `/link/` e o mascote **Link** foram descontinuados na migração v2.0 — o posicionamento agora vive nas sections da LP modular (value, features, interactive)
- Evitar: vender apenas `n8n`, chatbot simples ou automação genérica
- Usar: agentes corporativos, base de conhecimento, execução segura, permissões, logs, auditoria, canais integrados e evolução contínua
- Stack do site: HTML/CSS/JS vanilla modular em `fluxointeligenteia/pages/` (componentes + sections). LP final regerada por `python3 fluxointeligenteia/tools/build-index.py`. Header/footer globais injetados por `assets/js/site-layout.js` via `data-site` + slots `#flux-slot-header` / `#flux-slot-footer`. Tailwind continua ativo só pros cards/posts gerados pelo blog-bot.

## Cadência

Cron a cada 3 dias (`0 10 3,6,9,12,15,18,21,24,27,30 * *` UTC = 07:00 SP) em `.github/workflows/blog-automation.yml`. 1 batch publica em todos os 5 sites. Manual via `workflow_dispatch`.

## Modelo editorial (todos os sites)

Schema JSON do contract:
- `eyebrow`, `headline`, `seoTitle` (≤70 chars), `summary` (≤300)
- `answerBox: { question, answer }` — resposta direta no topo (AEO)
- `tldr: string[3]` — 3 bullets resumo
- `sections: { type, title?, body?, items?, ctaLabel?, ctaHref? }[]` — types: `prose | list | callout | cta-inline`
- `faq: { q, a }[]` — pelo menos 1, vira JSON-LD FAQPage

Renderer: emite JSON-LD `BlogPosting` + `FAQPage` quando aplicável.

## Regras críticas

1. **Timezone**: `generatedAt` SEMPRE em BRT explícito (`-03:00`). NUNCA UTC com `Z`. Carbon do Laravel salva `published_at` 3h adiantado se receber UTC. Helper: `nowInBrtIso()` em `run.js` e `lib/api-publisher.js`.

2. **Tom**: consultivo, levemente descontraído, foco educacional. CTAs sutis (máx 2 menções inline ao serviço por post). Ver `feedback_blog_content_tone` na memória.

3. **Sources**: bot envia URLs reais (API valida como required), mas o post final no front NÃO deve listar fontes — quem decide é o template Laravel.

4. **Tailwind safelist**: classes com opacity arbitrária (`bg-blue-400/10`, `border-l-4`) e cores não-default precisam de safelist por site (`tailwind.*.cjs`). Após editar, rodar `npm run build:css`.

5. **API target — adicionar novo site (roteiro)**:
   - `sites.json`: `target: "api"`, `api: { baseUrl, endpoint, tokenEnv }`, sem `siteRoot`/`lintTargets`/`assets`
   - Profile em `lib/site-strategy.js` (voice, persona, themes, casualVoice, coverArt)
   - Token em `.env` local
   - **`gh secret set <NAME> --repo aCidcOde/coderush-sites --body "<token>"`** — sem isso o cron CI falha silencioso
   - Declarar env var no workflow (step "Run blog bot")
   - Smoke: `node automation/blog-bot/run.js --mode=publish --sites=<id>` esperando 201

## Scripts úteis

- `node automation/blog-bot/run.js --mode=publish --sites=<id1>,<id2>` — gera + publica
- `node automation/blog-bot/run.js --mode=dry-run` — gera contracts sem publicar
- `node automation/blog-bot/scripts/preview-cover.js --site=<id> --headline="..."` — gera 1 capa local em /tmp/, sem postar
- `node automation/blog-bot/scripts/render-preview.js --site=<id>` — renderiza 1 contract em HTML local

## Design system Emergency (referência pros próximos sites API)

Paleta deep-navy + gold extraída do CSS real do emergency.com.br + tokens.css do planetacertidoes-saas. Personagem mascote robótico opcional aparece quando o ângulo do post é analítico (DD imobiliária, auditoria, checklist). **Regra absoluta**: ZERO pessoas reais fotorealísticas; só mascote estilizado quando regra de uso permite, ou abstração pura.

`gpt-5-image` respeita melhor a regra. `gemini-3-pro` (fallback) às vezes ignora — quando isso ocorrer, regerar.

## Não fazer

- Não criar PRs neste repo (work direto na main, deploy automático por push)
- Não usar `--no-verify` em git ou pular hooks
- Não citar fontes no post final (front decide; bot envia por causa da validação)
- Não enviar `generatedAt` em UTC
- Não esquecer `gh secret set` ao adicionar novo site API
