# Documentação do Robô de Automação de Blog

## Objetivo

O robô publica posts semanais gerados por IA para quatro sites do repositório:

- `coderush` na raiz
- `codafacil/`
- `fluxointeligenteia/`
- `sistemavendadireta/`

O fluxo é multi-site de verdade: gera o draft, publica o post em `YYYY/MM/DD/slug/`, atualiza os cards da home, mantém o índice `/blog/`, atualiza `sitemap.xml` e garante `robots.txt`.

## Arquitetura atual

### Configuração

- `automation/blog-bot/config/sites.json`
  - fonte de verdade dos sites
  - diretório raiz, home, blog, extensão de render, base URL, branding, lint e SEO
- `automation/blog-bot/run.js`
  - aceita `--mode=`, `--sites=` e `--date=`
  - carrega `.env` da raiz e `.env` dos sites conhecidos
  - reaproveita draft existente quando a mesma data/slug já existe
- `automation/blog-bot/lib/site-strategy.js`
  - tema por site com escolha determinística por `site + data`
- `automation/blog-bot/lib/ai-writer.js`
  - usa `API_OPENROUTER` como provedor principal
  - usa `OPENAI_API_KEY` como fallback opcional
  - mantém fallback local quando nenhuma IA estiver disponível
- `automation/blog-bot/lib/publisher.js`
  - publisher compartilhado para todos os sites
  - atualiza post, home, índice, sitemap, robots e capa

### Publicação por site

- `coderush`
  - home: [`index.php`](/Users/acidcode/data/coderush-sites/index.php)
  - índice: [`blog/index.php`](/Users/acidcode/data/coderush-sites/blog/index.php)
- `codafacil`
  - home: [`codafacil/index.php`](/Users/acidcode/data/coderush-sites/codafacil/index.php)
  - índice: [`codafacil/blog/index.php`](/Users/acidcode/data/coderush-sites/codafacil/blog/index.php)
- `fluxointeligenteia`
  - home: [`fluxointeligenteia/index.html`](/Users/acidcode/data/coderush-sites/fluxointeligenteia/index.html)
  - índice: [`fluxointeligenteia/blog/index.html`](/Users/acidcode/data/coderush-sites/fluxointeligenteia/blog/index.html)
- `sistemavendadireta`
  - home: [`sistemavendadireta/index.php`](/Users/acidcode/data/coderush-sites/sistemavendadireta/index.php)
  - índice: [`sistemavendadireta/blog/index.php`](/Users/acidcode/data/coderush-sites/sistemavendadireta/blog/index.php)

## Agendamento

O workflow versionado está em [`.github/workflows/blog-automation.yml`](/Users/acidcode/data/coderush-sites/.github/workflows/blog-automation.yml).

- frequência: semanal
- dia e hora: segunda-feira às 07:00 em `America/Sao_Paulo`
- cron do GitHub Actions: `0 10 * * 1`
- execução manual: `workflow_dispatch`

Esse job roda `npm run blogbot:publish`, abre PR automática e envia o e-mail de aprovação.

## Variáveis de ambiente

### Prioridade de IA

1. `API_OPENROUTER`
2. `OPENROUTER_API_KEY`
3. `OPENAI_API_KEY`
4. fallback local sem IA

### Variáveis suportadas

- `API_OPENROUTER`
- `BLOG_BOT_OPENROUTER_MODEL`
- `BLOG_BOT_OPENROUTER_IMAGE_MODEL`
- `OPENAI_API_KEY`
- `BLOG_BOT_OPENAI_MODEL`

Exemplo base: [`.env.example`](/Users/acidcode/data/coderush-sites/.env.example)

Observação importante:

- o robô agora também lê `sistemavendadireta/.env`, `codafacil/.env` e `fluxointeligenteia/.env` quando esses arquivos existirem
- isso permite reaproveitar uma chave já cadastrada no projeto, sem exigir um `.env` novo na raiz

## Comandos

### Simular todos os sites

```bash
npm run blogbot:dry-run
```

### Publicar todos os sites

```bash
npm run blogbot:publish
```

### Publicar subconjunto de sites

```bash
npm run blogbot:publish -- --sites=coderush,codafacil,fluxointeligenteia
```

### Reproduzir uma data específica

```bash
npm run blogbot:publish -- --date=2026-04-14
```

## Saídas geradas

- drafts por site:
  - `automation/blog-bot/out/<site>/<data>/`
- relatórios:
  - `automation/blog-bot/reports/run-<data>-<mode>.json`

No modo `publish`, cada site registra:

- `postPath`
- `homeUpdated`
- `blogUpdated`
- `sitemapUpdated`
- `robotsUpdated`

## Validação aplicada

Validações já executadas nesta implementação:

- `npm run blogbot:publish -- --sites=coderush,codafacil,fluxointeligenteia --date=2026-04-14`
- `npm run blogbot:publish -- --date=2026-04-14`
- `npm run blogbot:publish -- --date=2026-04-14` novamente para checar idempotência
- `npm run blogbot:dry-run`
- `npm run build:css`
- `php -l` automático em todos os arquivos PHP alterados pelo publisher

Validação HTTP local confirmada dentro do container:

- `http://127.0.0.1/blog/`
- `http://127.0.0.1/codafacil/blog/`
- `http://127.0.0.1/fluxointeligenteia/blog/`

Os três endpoints responderam `HTTP 200` no `nginx` do compose.

## Seed inicial publicado

O seed inicial definido para a primeira entrega foi `2026-04-14`. Os seguintes posts já existem:

- [`2026/04/14/coderush-ia-2026-04-14/index.php`](/Users/acidcode/data/coderush-sites/2026/04/14/coderush-ia-2026-04-14/index.php)
- [`codafacil/2026/04/14/codafacil-ia-2026-04-14/index.php`](/Users/acidcode/data/coderush-sites/codafacil/2026/04/14/codafacil-ia-2026-04-14/index.php)
- [`fluxointeligenteia/2026/04/14/fluxointeligenteia-ia-2026-04-14/index.html`](/Users/acidcode/data/coderush-sites/fluxointeligenteia/2026/04/14/fluxointeligenteia-ia-2026-04-14/index.html)
- [`sistemavendadireta/2026/04/14/sistemavendadireta-ia-2026-04-14/index.php`](/Users/acidcode/data/coderush-sites/sistemavendadireta/2026/04/14/sistemavendadireta-ia-2026-04-14/index.php)

## Limitações atuais

- a geração de capa por IA depende de conectividade e crédito do provedor configurado
- quando a IA falha, o texto continua via fallback local e a capa usa a imagem-base do site
- a publicação automática continua passando por revisão humana via PR antes do merge
