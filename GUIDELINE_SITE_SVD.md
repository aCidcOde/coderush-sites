<!--
/*
[Modulo Documentacao Operacional — CodeRush Multi-site]
@Author: Andre Gomes ( @acidcode )
@since 2026-02-10
@updated 2026-04-10
Guia tecnico para manutencao, expansao e atualizacao do hub CodeRush e seus sub-sites.
*/
-->

# Guideline Operacional — CodeRush Multi-site

## 1) Cenario atual

O repositorio `coderush-sites` hospeda o hub CodeRush e os sites ativos do ecossistema. Cada site deve manter HTML/PHP nas paginas, CSS em `css/` e JavaScript em `js/`. Dados estruturados `application/ld+json` permanecem inline por serem SEO, nao comportamento de interface.

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

---

## 7) Blog SVD

Para novo post:

1. Criar `sistemavendadireta/YYYY/MM/DD/slug/index.php`
2. Salvar capa em `sistemavendadireta/imagens/posts/{slug}.jpg`
3. Atualizar `sistemavendadireta/blog/index.php`
4. Atualizar a home se o post entrar nos 3 destaques
5. Atualizar `sistemavendadireta/sitemap.xml`, se aplicavel
6. Validar PHP e existencia da capa

Posts legados podem ter HTML importado; refatorar somente quando houver escopo claro.

---

## 8) Regras para alteracoes por prompt

Sempre que pedir alteracao, informe:

1. Site alvo: `hub`, `sistemavendadireta`, `codafacil` ou `fluxointeligenteia`
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

---

## 9) O que foi considerado sobra

- `index_svd_files/`: duplicava `sistemavendadireta/imagens/` e nao era mais referenciado por paginas publicas.
- Pacote Tailwind dentro de `sistemavendadireta/`: duplicava o build central da raiz.
- Docs duplicadas em `sistemavendadireta/docs/`: a fonte canonica passa a ser `docs/`.
- `sistemavendadireta/components/`: estava orfao, duplicava trechos da home e ainda tinha CSS/JS inline.
- Config/build de WordPress Consultoria: removido porque o projeto acabou. Manter apenas regra Nginx de `410 Gone` para evitar fallback acidental no hub.

O Tailwind da raiz nao e sobra: o hub usa `css/site-tailwind.css`.
Os inputs Tailwind simples e `site-optimizations.css` iguais entre sites foram mantidos por isolamento operacional: cada dominio deve continuar publicavel com seus proprios assets relativos.
