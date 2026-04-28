<!--
/*
[Modulo Documentacao Operacional — CodeRush Multi-site]
@Author: Andre Gomes ( @acidcode )
@since 2026-02-10
@updated 2026-04-28
Guia tecnico para manutencao, expansao e atualizacao do hub CodeRush e seus sub-sites.
*/
-->

# Guideline Operacional — CodeRush Multi-site

Este documento e a fonte canonica para qualquer alteracao no repositorio `coderush-sites`. Cobre os 4 sites publicados, a infra Docker, o pipeline automatizado de blog (blog-bot), as regras de branding/SEO por site e o workflow esperado quando o assistente de IA (Claude) executa tarefas no repo.

> O nome do arquivo permanece `GUIDELINE_SITE_SVD.md` por compatibilidade com referencias externas. O escopo, porem, e o repo inteiro — nao apenas o site SVD.

---

## 1) Cenario atual

O repositorio hospeda o hub CodeRush e os sites ativos do ecossistema. Cada site mantem HTML/PHP nas paginas, CSS em `css/` e JavaScript em `js/`. Dados estruturados `application/ld+json` permanecem inline por serem SEO, nao comportamento de interface.

### Sites ativos

| Site | Dominio | Diretorio | Stack atual |
|---|---|---|---|
| CodeRush Hub | `coderush.com.br` | `/` | PHP + Tailwind compilado + `css/styles.css` + `js/scripts.js` |
| Sistema Venda Direta | `sistemavendadireta.com.br` | `sistemavendadireta/` | PHP + Tailwind compilado + `css/styles.css` + JS local |
| Codafacil.dev | `codafacil.dev` | `codafacil/` | PHP + Tailwind compilado + `css/styles.css` + `js/scripts.js` |
| FluxoInteligente IA | `fluxointeligenteia.com.br` | `fluxointeligenteia/` | HTML + Tailwind compilado + `css/styles.css` + `js/scripts.js` |

### Projetos encerrados ou fora do hub

- WordPress Consultoria foi removido dos links publicos e do build Tailwind; o Nginx deve responder `410 Gone` para o dominio e para `/wordpressconsultoria/`.
- A landing `sistemavendadireta/wordpress/` continua ativa como pagina de servicos WordPress dentro do SVD; ela nao e o projeto externo WordPress Consultoria.
- Traco Creative Lab aparece apenas como iniciativa futura quando houver escopo.

---

## 2) Infraestrutura Docker

### Containers
- `coderush-app` — PHP 8.3-FPM Alpine, porta interna `9000`
- `coderush-nginx` — Nginx Alpine, porta local `8081` para `80` interno

### Comandos uteis
```bash
docker compose up -d --build
docker compose restart nginx
docker compose ps
docker logs coderush-nginx
```

### Virtual hosts
O arquivo `docker/nginx/default.conf` deve conter apenas dominios ativos. Em desenvolvimento, todos os sites tambem devem funcionar por subdiretorio em `http://localhost:8081/`.

---

## 3) Regra de assets

Cada site deve ser auto-contido:

- CSS Tailwind compilado: `css/site-tailwind.css`
- CSS manual extraido das paginas: `css/styles.css`
- CSS adicional de otimizacao, quando existir: `css/site-optimizations.css`
- JS extraido das paginas: `js/scripts.js` ou arquivo com nome da pagina quando for mais claro
- Imagens: `imagens/` dentro do site
- Lottie/JSON visual: `imagens/` ou `js/` conforme o tipo de asset

Exemplos:

```html
<link rel="stylesheet" href="css/site-tailwind.css" />
<link rel="stylesheet" href="css/styles.css" />
<script src="js/scripts.js" defer></script>
```

Regra importante: nao criar CSS ou JS inline em `index.php`/`index.html`, exceto JSON-LD de SEO.

---

## 4) Build Tailwind

O Tailwind da raiz continua fazendo sentido porque o hub usa `css/site-tailwind.css`. Nao remover `tailwind.root.cjs` enquanto `index.php` carregar esse CSS.

Scripts oficiais no `package.json` da raiz:

```bash
npm run build:root
npm run build:svd
npm run build:codafacil
npm run build:fluxointeligenteia
npm run build:css
```

Mapeamento:

| Script | Config | Input | Output |
|---|---|---|---|
| `build:root` | `tailwind.root.cjs` | `css/site-tailwind.input.css` | `css/site-tailwind.css` |
| `build:svd` | `tailwind.config.cjs` | `sistemavendadireta/css/site-tailwind.input.css` | `sistemavendadireta/css/site-tailwind.css` |
| `build:codafacil` | `tailwind.codafacil.cjs` | `codafacil/site-tailwind.input.css` | `codafacil/css/site-tailwind.css` |
| `build:fluxointeligenteia` | `tailwind.fluxointeligenteia.cjs` | `fluxointeligenteia/site-tailwind.input.css` | `fluxointeligenteia/css/site-tailwind.css` |

Removidos como obsoletos:
- `tailwind.wordpressconsultoria.cjs`
- `build:wordpressconsultoria`
- `sistemavendadireta/package.json`, `sistemavendadireta/package-lock.json` e `sistemavendadireta/tailwind.config.cjs`

---

## 5) Estrutura por site

### CodeRush Hub (`/`)
- Entrada: `index.php`
- CSS: `css/site-tailwind.css`, `css/styles.css`
- JS: `js/scripts.js`
- Cards ativos: SVD, Codafacil, FluxoInteligente IA
- Nao apontar para `wordpressconsultoria/`

### Sistema Venda Direta (`sistemavendadireta/`)
- Home: `index.php`
- Blog: `blog/index.php`
- Landing IA: `inteligencia-artificial/index.php`
- Landing WordPress interna: `wordpress/index.php`
- Posts: `2023/**/index.php` e `2026/**/index.php`
- CSS: `css/site-tailwind.css`, `css/site-optimizations.css`, `css/styles.css`
- JS: `js/scripts.js`, `js/inteligencia-artificial.js`, `js/lottie.min.js`
- Imagens e capas: `imagens/` e `imagens/posts/`

`index_svd_files/` nao deve voltar. Ele foi substituido por `imagens/` e `css/`.

### Codafacil (`codafacil/`)
- Entrada: `index.php`
- CSS: `css/site-tailwind.css`, `css/site-optimizations.css`, `css/styles.css`
- JS: `js/scripts.js`
- Imagens: `imagens/`

### FluxoInteligente IA (`fluxointeligenteia/`)
- Entrada: `index.html`
- CSS: `css/site-tailwind.css`, `css/hub-parity.css`, `css/styles.css`
- JS: `js/scripts.js`

---

## 6) SEO e dados estruturados

Todas as paginas publicas devem manter:
- `title`
- `meta description`
- `meta robots`
- `canonical`
- Open Graph
- Twitter Cards
- `application/ld+json` quando ja houver schema

JSON-LD pode ficar inline. Scripts de UI, formularios, chat, Lottie, menu e animacoes devem ficar em `js/`.

Cada site possui um conjunto curado de palavras-chave SEO em `automation/blog-bot/lib/site-strategy.js` (`SITE_PROFILES[siteId].keywords`). Detalhes na secao 8.

---

## 7) Sistema de Blog Automatizado (blog-bot)

A geracao de posts semanais e automatizada para os 4 sites e roda no GitHub Actions.

### Pipeline em uma frase
> Para cada site, o bot busca itens em feeds RSS curados, ranqueia por recencia + relevancia ao foco semanal, gera o post via OpenRouter usando o perfil do site (persona, voz, palavras banidas, keywords, CTAs), publica no diretorio do site, atualiza home/blog/sitemap e registra um relatorio. Se a IA falhar, ha fallback site-aware. Se todos os sites cairem em fallback, o job aborta para nao publicar conteudo generico.

### Estrutura de arquivos

```
automation/blog-bot/
├── config/
│   └── sites.json          # config por site (research.feeds, seo, assets, lint)
├── lib/
│   ├── ai-writer.js        # OpenAI/OpenRouter + fallback site-aware + cover via OpenRouter
│   ├── cover-agent.js      # geracao de capa (modelo premium GPT-5/Gemini-3 com fallback)
│   ├── env-loader.js       # carrega .env/.env.local
│   ├── publisher.js        # template HTML/PHP do post + atualiza home/blog/sitemap/robots
│   ├── research.js         # fetch RSS/Atom, ranking por focus + recencia
│   └── site-strategy.js    # SITE_PROFILES (persona, voz, keywords, themes, CTAs, banidas)
├── out/                    # artefatos JSON/MD por site/data
├── reports/                # relatorios JSON por execucao
└── run.js                  # entrypoint, orquestra tudo
```

### Como rodar localmente

```bash
npm run blogbot:dry-run                          # dry-run para todos os sites
npm run blogbot:dry-run -- --sites=codafacil     # apenas um site
npm run blogbot:publish                          # publica de fato (PR pelo CI)
node automation/blog-bot/run.js --mode=dry-run --date=2026-04-28
```

### Foco semanal e angulo
- `config/sites.json:rotation` define a rotacao de foco: `["ia","php","tecnologia"]`. O foco da semana e escolhido por hash da data (deterministico).
- `pickAngle(siteId, date)` escolhe um angulo entre os `angleBias` do perfil do site, tambem por hash. Garante variacao semanal sem repetir tema.

### Research RSS
- Cada site em `config/sites.json` tem um array `research.feeds` com 5 feeds gratuitos.
- `research.maxItems` (default 6) e `research.sinceDays` (default 21) controlam quanto entra no prompt.
- O ranqueador usa word-boundary para tokens curtos (`ia`, `ai`) e expansao de sinonimos via `FOCUS_SYNONYMS` em `run.js` para evitar falso-positivo.

### Provider de IA
- Apenas **OpenRouter**. Variaveis: `API_OPENROUTER` (chave), `BLOG_BOT_OPENROUTER_MODEL` (texto, default `openai/gpt-4o-mini`), `BLOG_BOT_OPENROUTER_IMAGE_MODEL` (capa, default `google/gemini-2.5-flash-image`).
- O secret `OPENAI_API_KEY` foi removido e nao deve ser reintroduzido sem autorizacao explicita. Detalhes na secao 11.

### Anti-duplicacao e fail-loud
- Apos gerar os 4 posts, o run.js compara similaridade par-a-par via Jaccard de bigrams. Pares com `>= 0.6` aparecem em `report.duplications` para revisao.
- Se TODOS os sites cairem em fallback (ex: chave invalida ou OpenRouter offline), o job termina com `process.exit(1)` e o GitHub Actions nao abre o PR semanal — evita publicar conteudo generico.

### Workflow
`.github/workflows/blog-automation.yml` roda cron `0 10 * * 1` (segunda 07:00 SP) e `workflow_dispatch`. Apos o run, abre PR via `peter-evans/create-pull-request@v6`. Aprovacao manual antes do merge.

---

## 8) Branding, voz e keywords por site

Fonte de verdade: `automation/blog-bot/lib/site-strategy.js` (`SITE_PROFILES`). Quando alterar tom/posicionamento, alterar la — o blog-bot e qualquer outro consumidor pegam dali.

Cada perfil contem:
- `persona` (descricao do publico)
- `personaShort` (versao curta para headlines)
- `offering` (oferta da empresa)
- `differentiators` (diferenciais)
- `voice` (tom narrativo)
- `bannedWords` (nao usar nos textos gerados)
- `angleBias` (angulos rotativos)
- `cta` (`{ label, path }`)
- `keywords` (`{ primary, secondary, longTail }`)
- `themes` (temas rotativos)

### Resumo por site

**CodeRush** — consultoria/arquitetura/IA para empresas medias
- Voz: consultivo, direto, com clareza de trade-offs e sem hype
- Keywords primarias: `software sob medida`, `consultoria de tecnologia`, `arquitetura de software`
- CTA: "Fale com a CodeRush" → `#contato`

**Codafacil.dev** — fabrica de software com IA aplicada (PHP/Laravel/eng com IA)
- Voz: tecnico, pragmatico, com referencias de codigo, processo e DX
- Keywords primarias: `desenvolvimento de software sob medida`, `fabrica de software`, `desenvolvimento Laravel`
- CTA: "Fale com a Codafacil.dev" → `#contato`

**FluxoInteligente IA** — agentes inteligentes (LangChain, n8n) e automacao com IA
- Voz: operacional, focado em processo, custo e SLA
- Keywords primarias: `automacao com IA`, `agentes inteligentes`, `automacao de processos`
- CTA: "Fale com a FluxoInteligente IA" → `#contato`

**Sistema Venda Direta** — software para vendas diretas e MMN
- Voz: executivo, focado em resultado comercial e previsibilidade
- Keywords primarias: `sistema de venda direta`, `software para MMN`, `marketing multinivel`
- CTA: "Solicite um orcamento" → `#contato`

### Diretrizes editoriais (todos os sites)

- Conteudo dos posts e 90% educacional/tecnico sobre o tema. CTAs sao secundarios.
- Maximo 1 mencao inline ao servico no corpo (secao 2 ou 3) como transicao natural.
- 1 fechamento leve em markdown na ultima secao apontando para `cta.path`.
- Proibido copy agressivo: `"transforme ja"`, `"nao perca"`, `"clique aqui"`, `"ultima chance"`.
- Headlines sao informativos sobre o tema, nao headline-pitch.
- Texto em pt-BR. Evitar promessas absolutas. Trazer aplicacao pratica com exemplo concreto.

### Feeds RSS por site

| Site | Feeds (5) |
|---|---|
| CodeRush | Martin Fowler, AWS Architecture, ThoughtWorks, Stack Overflow, Google Cloud |
| Codafacil | Laravel News, Stitcher (Brent Roose), GitHub Blog, LangChain, Codeium |
| FluxoInteligente | LangChain, n8n, Hugging Face, CrewAI, OpenAI |
| SVD | Direct Selling News, RD Station, HubSpot Sales, Salesforce, WFDSA |

Mistura PT/EN. Apenas fontes gratuitas. Lista atual em `automation/blog-bot/config/sites.json:research.feeds`.

---

## 9) Regras para alteracoes por prompt

Sempre que pedir alteracao, informe:

1. Site alvo: `hub`, `sistemavendadireta`, `codafacil`, `fluxointeligenteia` ou `blog-bot`
2. Arquivo ou rota
3. Bloco alvo ou texto exato
4. Objetivo
5. Restricoes

Checklist minimo:
- Nao recriar `wordpressconsultoria`
- Nao usar `index_svd_files`
- Nao criar CSS/JS inline fora de JSON-LD
- Manter paths relativos ao site
- Rodar `npm run build:css` quando mudar classes Tailwind
- Rodar `php -l` nos PHP alterados
- Verificar `localhost:8081` para paginas e assets tocados
- Para mudancas no blog-bot: `node --check` nos arquivos editados e, quando possivel, `npm run blogbot:dry-run -- --sites=<site>`

---

## 10) O que foi considerado sobra

- `index_svd_files/`: duplicava `sistemavendadireta/imagens/` e nao era mais referenciado por paginas publicas.
- Pacote Tailwind dentro de `sistemavendadireta/`: duplicava o build central da raiz.
- Docs duplicadas em `sistemavendadireta/docs/`: a fonte canonica passa a ser `docs/`.
- `sistemavendadireta/components/`: estava orfao, duplicava trechos da home e ainda tinha CSS/JS inline.
- Config/build de WordPress Consultoria: removido porque o projeto acabou. Manter apenas regra Nginx de `410 Gone` para evitar fallback acidental no hub.

O Tailwind da raiz nao e sobra: o hub usa `css/site-tailwind.css`.
Os inputs Tailwind simples e `site-optimizations.css` iguais entre sites foram mantidos por isolamento operacional: cada dominio deve continuar publicavel com seus proprios assets relativos.

---

## 11) Secrets, CI e deploy

### Secrets do GitHub Actions (repo `aCidcOde/coderush-sites`)

| Secret | Uso | Obrigatorio |
|---|---|---|
| `API_OPENROUTER` | Provider de IA do blog-bot (texto + capa) | Sim, para `blog-automation.yml` |
| `DEPLOY_TOKEN` | Token do webhook em `coderush.com.br/deploy.php` | Sim, para `deploy.yml` |
| `SSH_HOST`, `SSH_USER`, `SSH_KEY` | Reservados para SSH (nao usados pelo deploy atual) | Nao |

> **Nao reintroduzir** `OPENAI_API_KEY` sem pedido explicito. O blog-bot usa apenas OpenRouter desde o PR #23, e a confusao OpenAI/OpenRouter foi a causa do incidente do PR #22 (todos os sites caindo em fallback com texto identico).

### Workflows

- `.github/workflows/blog-automation.yml` — cron semanal segunda 07:00 SP. Roda `npm run blogbot:publish`, abre PR automatizado.
- `.github/workflows/deploy.yml` — em push na `main`, dispara `POST https://coderush.com.br/deploy.php` com `X-Deploy-Token: ${{ secrets.DEPLOY_TOKEN }}`. Esse webhook foi corrigido no PR #19 e aponta para o dominio do hub, nao para SVD.

### Local: variaveis sensiveis

- `.env` na raiz contem `API_OPENROUTER` e SMTP. Nao commitar.
- `.env.example` reflete a forma esperada (sem valores reais).

---

## 12) Regras para o assistente de IA (Claude)

### Pull Requests
- **Nao abrir PR sem pedido explicito.** Comite localmente e empurre a branch quando fizer sentido, mas so rode `gh pr create` quando o usuario pedir ("abre PR", "manda PR", "faz o PR").
- Apos empurrar uma branch, sinalize: "branch X empurrada, pronta para PR quando voce quiser".
- Nunca usar `--no-verify` ou `--force` sem pedido explicito.

### Conteudo do blog-bot
- Aplicar as diretrizes editoriais da secao 8.
- Quando alterar prompt, perfil de site ou pipeline de research, sempre rodar `npm run blogbot:dry-run` ou `node --check` antes de commitar.
- Se quiser mudar tom/keywords/CTAs de um site, edite `automation/blog-bot/lib/site-strategy.js` (`SITE_PROFILES`) — fonte de verdade unica.

### Secrets e infraestrutura
- Confirmar antes de criar/deletar secrets via `gh secret set/delete`.
- Nao reintroduzir secrets removidos (`OPENAI_API_KEY`) sem autorizacao.
- Ao mexer em CI, manter o webhook de deploy em `coderush.com.br`.

### Branches e commits
- Branches descritivas, em ingles ou portugues consistente: `feat/`, `fix/`, `chore/`, `docs/`.
- Mensagens de commit em pt-BR no imperativo, com escopo explicito (`feat(blog-bot):`, `fix(ci):`, `docs:`).
- Ao terminar uma branch local: empurrar e sinalizar; nao abrir PR ate o usuario pedir.

### Risco e reversibilidade
- Acoes locais reversiveis (edits, branches): pode executar.
- Acoes irreversiveis ou compartilhadas (push em `main`, force push, secret delete, deploy hooks, mensagens externas): confirmar antes.
