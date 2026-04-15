# Documentação do Robô de Automação de Blog

## Objetivo

Este documento descreve tudo que foi implementado no robô de automação de posts, com foco inicial no site `sistemavendadireta`, mantendo arquitetura preparada para expansão aos demais sites.

---

## Escopo Implementado

- Motor de automação com execução por modo:
  - `dry-run` (simulação)
  - `publish` (publicação)
- Configuração multi-site com 3 alvos:
  - `coderush`
  - `fluxointeligenteia`
  - `sistemavendadireta`
- Geração de conteúdo por IA (quando houver chave) com fallback local.
- Workflow semanal no GitHub Actions com abertura de PR automática.
- Publicação real já ativa para `sistemavendadireta`.

---

## Arquivos Criados/Atualizados

### Scripts e configuração

- `package.json`
  - `blogbot:dry-run`
  - `blogbot:publish`
- `automation/blog-bot/config/sites.json`
  - sites habilitados
  - rotação de foco semanal
  - parâmetros por site

### Motor principal

- `automation/blog-bot/run.js`
  - carrega config
  - define foco semanal
  - gera contrato de post por site
  - chama IA ou fallback
  - gera artefatos e relatório
  - em modo `publish`, publica SVD

### Estratégia editorial

- `automation/blog-bot/lib/site-strategy.js`
  - temas sugeridos por site
  - estilo de prompt por site (`postType`, tom e restrições)

### Geração de conteúdo com IA

- `automation/blog-bot/lib/ai-writer.js`
  - integração com API OpenAI (`chat/completions`)
  - retorno estruturado em JSON
  - fallback local para manter o fluxo robusto

### Publicação SVD

- `automation/blog-bot/lib/svd-publisher.js`
  - cria novo post em `YYYY/MM/DD/slug/index.php`
  - atualiza cards da home (`sistemavendadireta/index.php`)
  - atualiza listagem do blog (`sistemavendadireta/blog/index.php`)
  - atualiza `sitemap.xml` com nova URL e `lastmod`
  - garante imagem de capa `.jpg` para o slug

### Automação CI/CD

- `.github/workflows/blog-automation.yml`
  - execução semanal (quarta 09:00 BRT)
  - execução manual via `workflow_dispatch`
  - uso de `OPENAI_API_KEY`
  - criação de PR automática para revisão/aprovação manual

---

## Fluxo Atual do Robô

## 1) Execução

- `npm run blogbot:dry-run` -> apenas simula e gera artefatos.
- `npm run blogbot:publish` -> simula + publica no SVD.

## 2) Planejamento de conteúdo

- escolhe foco semanal da rotação (`ia`, `php`, `tecnologia`)
- escolhe tema por site
- cria contrato de post (`slug`, `title`, `description`, `sources`, etc.)

## 3) Escrita de conteúdo

- com `OPENAI_API_KEY`: gera conteúdo com IA em JSON estruturado.
- sem chave (ou erro): usa fallback local para não interromper execução.

## 4) Artefatos gerados

- JSON e Markdown por site em:
  - `automation/blog-bot/out/<site>/<data>/`
- relatório da execução em:
  - `automation/blog-bot/reports/run-<data>-<mode>.json`

## 5) Publicação (SVD)

No modo `publish`, para `sistemavendadireta`:

- cria post novo
- insere novo card no topo da listagem do blog
- insere novo card na home e mantém apenas os 3 mais recentes
- atualiza sitemap

---

## Exemplo de Resultado já Gerado

Na execução de teste em `2026-04-14`:

- relatório:
  - `automation/blog-bot/reports/run-2026-04-14-publish.json`
- novo post:
  - `sistemavendadireta/2026/04/14/sistemavendadireta-ia-2026-04-14/index.php`
- nova imagem:
  - `sistemavendadireta/imagens/posts/sistemavendadireta-ia-2026-04-14.jpg`
- arquivos atualizados:
  - `sistemavendadireta/index.php`
  - `sistemavendadireta/blog/index.php`
  - `sistemavendadireta/sitemap.xml`

---

## Como Testar

## Teste local (simulação)

```bash
npm run blogbot:dry-run
```

Validar:

- `automation/blog-bot/reports/`
- `automation/blog-bot/out/`

## Teste local (publicação)

```bash
npm run blogbot:publish
```

Validar:

- novo post em `sistemavendadireta/YYYY/MM/DD/slug/`
- cards atualizados em home/blog
- `sitemap.xml` atualizado

## Teste CI (GitHub Actions)

1. Abrir Actions.
2. Executar workflow `Blog Automation Weekly` manualmente.
3. Confirmar abertura de PR automática.
4. Revisar e aprovar manualmente.

---

## Variáveis e Segredos

- `OPENAI_API_KEY` (GitHub Secret): habilita geração por IA.
- `BLOG_BOT_OPENAI_MODEL` (opcional): modelo usado pelo robô.

Sem `OPENAI_API_KEY`, o robô continua rodando via fallback local.

---

## Validações já aplicadas

- Sintaxe PHP (`php -l`) em arquivos críticos do SVD.
- Relatório consolidado por execução.
- Publicação apenas com fluxo completo sem falha fatal.
- Solicitação de aprovação por e-mail com links assinados para aprovar/reprovar PR automática.

---

## Limitações atuais

- Publicação real pronta apenas para `sistemavendadireta`.
- `coderush` e `fluxointeligenteia` ainda estão no modo de artefato/rascunho.
- Geração de capa ainda usa fallback de arquivo base quando necessário.
- Aprovação por e-mail depende de endpoint público (`pr-approval.php`) e segredos corretamente configurados.

---

## Aprovação da PR por e-mail

Após a etapa de criação da PR automática, o workflow envia e-mail para os aprovadores configurados.

- Botão **Aprovar PR** -> registra review `APPROVE` no GitHub.
- Botão **Reprovar PR** -> registra review `REQUEST_CHANGES` no GitHub.

Detalhes técnicos e contrato do payload:

- `docs/aprovacao-pr-email.md`

---

## Próximos Passos Recomendados

1. Evoluir template do post SVD para ficar ainda mais aderente ao layout completo atual (header/footer/leia também dinâmico).
2. Implementar descoberta real de tema em alta com fontes externas e score.
3. Implementar geração de capa dedicada por IA no padrão visual oficial.
4. Replicar publishers completos para `coderush` e `fluxointeligenteia`.
5. Adicionar validação de links e XML como gate bloqueante antes da abertura do PR.

---

## Resumo Executivo

O robô já está operacional para iniciar publicação automática semanal com revisão humana via PR, com geração de conteúdo por IA quando disponível e fallback robusto. O piloto no `sistemavendadireta` foi implementado e validado, formando a base para escalar para os demais sites.
