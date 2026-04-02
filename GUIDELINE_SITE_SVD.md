<!--
/*
[Modulo Documentacao Operacional — CodeRush Multi-site]
@Author: André Gomes ( @acidcode )
@since 2026-02-10
@updated 2026-04-02
Guia tecnico para manutencao, expansao e atualizacao do hub CodeRush e seus sub-sites.
*/
-->

# Guideline Operacional — CodeRush Multi-site

## 1) Visão geral do projeto

O **CodeRush** é um hub central de tecnologia que reúne múltiplas empresas. Cada empresa tem seu próprio diretório, domínio e assets auto-contidos. O Nginx aponta cada domínio diretamente para o diretório correspondente.

### Empresas e domínios

| Empresa | Domínio | Diretório | Stack |
|---|---|---|---|
| CodeRush Hub | coderush.com.br | `/` (raiz) | PHP + Tailwind CDN |
| Sistema Venda Direta | sistemavendadireta.com.br | `sistemavendadireta/` | PHP + Tailwind compilado |
| Codafacil.dev | codafacil.dev | `codafacil/` | PHP + Tailwind compilado |
| WordPress Consultoria | wordpressconsultoria.com.br | `wordpressconsultoria/` | HTML + Tailwind CDN |
| FluxoInteligente IA | fluxointeligenteia.com.br | `fluxointeligenteia/` | HTML + Tailwind CDN |

Empresas em stand-by (sem site ativo):
- **Traço Creative Lab** — design e UX (diretório ainda não criado)
- **No Sobrado Tech** — imobiliário (diretório ainda não criado)

---

## 2) Infraestrutura Docker

### Containers
- `coderush-app` — PHP 8.3-FPM Alpine (porta interna 9000)
- `coderush-nginx` — Nginx Alpine (porta `8081` local → `80` interno)

### Comandos úteis
```bash
docker compose up -d --build   # subir/rebuildar
docker compose restart nginx   # recarregar config nginx
docker compose ps              # status dos containers
docker logs coderush-nginx     # logs do nginx
```

### Virtual hosts — `docker/nginx/default.conf`
Cada domínio tem seu próprio `server {}` block com `root` apontando para o diretório da empresa. Em dev local, todos os sites são acessíveis via subdirectório em `localhost:8081/nome-do-site/`.

---

## 3) Regra de assets — isolamento por site

**Cada site deve ser 100% auto-contido.** Assets (CSS, JS, imagens, fontes) devem usar paths relativos dentro do próprio diretório:

```html
<!-- Correto: relativo ao diretório do site -->
<link rel="stylesheet" href="./assets/style.css" />
<img src="./assets/logo.webp" />

<!-- Correto para SVD (padrão legado) -->
<link rel="stylesheet" href="index_svd_files/site-tailwind.css" />
```

Isso garante que o site funcione tanto via subdiretório (`localhost:8081/sistemavendadireta/`) quanto via domínio próprio (`sistemavendadireta.com.br`).

---

## 4) Sistema Venda Direta (`sistemavendadireta/`)

### 4.1 Mapa de rotas
- `/` → `sistemavendadireta/index.php` (home SVD)
- `/blog/` → `sistemavendadireta/blog/index.php`
- `/wordpress/` → `sistemavendadireta/wordpress/index.php` (serviços WP do SVD)
- `/inteligencia-artificial/` → `sistemavendadireta/inteligencia-artificial/index.php`
- `/2023/.../` e `/2026/.../` → posts individuais
- `/enviar-contato.php` — handler de formulários
- `/enviar-telefone.php` — wrapper de compatibilidade

### 4.2 Estrutura de componentes PHP
```
sistemavendadireta/
├── index.php
├── components/
│   ├── layout/
│   │   ├── head.php       (meta, CSS, fontes)
│   │   ├── header.php     (nav sticky)
│   │   ├── footer.php     (rodapé institucional)
│   │   └── scripts.php    (JS inline, WhatsApp, Lottie)
│   ├── sections/          (14 seções da home)
│   └── ui/                (cta-button, section-title)
├── index_svd_files/       (CSS, JS, logos, imagens, lottie, posts/)
├── .env                   (credenciais SMTP)
└── ...
```

### 4.3 Identidade visual SVD
- Tailwind compilado localmente (`npm run build:css`)
- Fontes: Montserrat + Roboto
- Cor principal: `--color-brand: #004AAD`
- Build: `tailwind.config.cjs` + `index_svd_files/site-tailwind.input.css` → `site-tailwind.css`

**Regra:** sempre rodar `npm run build:css` após alterar classes Tailwind em arquivos `.php` do SVD.

### 4.4 Formulários e e-mail
- Arquivo central: `sistemavendadireta/enviar-contato.php`
- Lê credenciais de `sistemavendadireta/.env`
- Fallback SMTP → `mail()`
- Campos: nome, e-mail, telefone/whatsapp, serviço, mensagem, origem, redirect

### 4.5 Assets de imagens
- Logos e assets gerais: `sistemavendadireta/index_svd_files/`
- Capas de posts: `sistemavendadireta/index_svd_files/posts/`

Ao adicionar novo post:
1. Salvar capa em `index_svd_files/posts/`
2. Atualizar card no `blog/index.php`
3. Atualizar seção `blog-destaques.php` na home (se for um dos 3 mais recentes)
4. Adicionar OG image no post

### 4.6 SEO
Todas as páginas públicas devem ter:
- `title`, `meta description`, `meta robots` com diretivas completas
- `canonical`, `hreflang` (pt-BR + x-default)
- Open Graph e Twitter Cards completos
- Dados estruturados (ver padrão por tipo de página na seção 4.7)

### 4.7 Dados estruturados por página
- Home: `Organization`, `WebSite`, `FAQPage`
- Blog index: `Blog`
- Inteligência Artificial: `Organization`, `Service`, `FAQPage`
- WordPress: `ProfessionalService`
- CodaFácil: `Organization`, `Service`
- Posts: `BlogPosting` + `article:published_time` + `article:modified_time`

---

## 5) CodeRush Hub (`/` raiz)

### 5.1 Arquivo
- `index.php` — hub com cards de todas as empresas

### 5.2 Stack
- PHP (para `date('Y')` no footer)
- Tailwind CDN Play (sem build local)
- Fontes: Inter + Montserrat via Google Fonts

### 5.3 Seções
- Hero com CTA
- Cards das empresas (`#empresas`) — 6 cards (4 ativos + 2 em breve)
- Sobre o hub (`#sobre`)
- Contato (`#contato`)

### 5.4 Links dos cards
Em dev: paths relativos (`/sistemavendadireta/`, `/codafacil/`, etc.)
Em produção: atualizar para domínios reais quando disponíveis.

---

## 6) Codafacil (`codafacil/`)

Site auto-contido. **Não modificar** sem escopo explícito.

Assets locais:
- `codafacil/site-tailwind.css`
- `codafacil/site-optimizations.css`
- `codafacil/logo.webp`
- `codafacil/Codafacil.mp4`

---

## 7) WordPress Consultoria (`wordpressconsultoria/`)

### 7.1 Arquivo
- `wordpressconsultoria/index.html`

### 7.2 Stack
- HTML puro + Tailwind CDN Play
- Fontes: Inter + Montserrat via Google Fonts
- Cor principal: `#21759b` (azul WordPress)

### 7.3 Seções
- Hero com CTAs (`#servicos`, `#contato`)
- Serviços em grid 6 cards (`#servicos`)
- Diferenciais (`#diferenciais`)
- Contato (`#contato`)

---

## 8) FluxoInteligente IA (`fluxointeligenteia/`)

### 8.1 Arquivo
- `fluxointeligenteia/index.html`

### 8.2 Stack
- HTML puro + Tailwind CDN Play
- Fontes: Inter + Montserrat via Google Fonts
- Cor principal: `#059669` (verde emerald)

### 8.3 Seções
- Hero com CTAs
- Como funciona — 4 etapas em timeline (`#como-funciona`)
- Soluções em grid 6 cards (`#solucoes`)
- Resultados (métricas)
- Contato (`#contato`)

---

## 9) Fluxo de publicação

```
1. Alterar arquivos do site correspondente
2. Para SVD: validar PHP com `php -l arquivo.php`
3. Para SVD com classes novas: `npm run build:css`
4. Testar localmente via localhost:8081/[diretorio]/
5. Commit: [FEAT], [FIX], [CHORE] + descrição
6. Push → branch develop → PR → main
```

---

## 10) Regras para alterações por prompt

Sempre informar:
1. **Site alvo** — ex.: `sistemavendadireta`, `wordpressconsultoria`
2. **Página/arquivo** — ex.: `sistemavendadireta/components/sections/hero.php`
3. **Bloco alvo** — ex.: `#contato`, título da seção
4. **Objetivo** — ex.: "trocar copy", "adicionar card"
5. **Restrições** — ex.: "não alterar layout", "manter SEO"

Checklist mínimo:
- Links em formato de diretório (`/rota/`)
- Assets com paths relativos ao diretório do site
- Preservar tags SEO em páginas PHP
- Manter rodapé institucional consistente
- Validar sintaxe PHP após edições

---

## 11) Observações

- Posts legados em `sistemavendadireta/2023/` e `sistemavendadireta/2026/` têm conteúdo importado — evitar refatorações globais sem escopo explícito.
- `tailwind.config.cjs` e `package.json` ficam na raiz do projeto mas o build é para o SVD (`sistemavendadireta/index_svd_files/`).
- Sites com Tailwind CDN (hub, WP Consultoria, FluxoIA) não precisam de build local.
- Ao criar novo site no hub: criar diretório, `index.html` auto-contido, adicionar virtual host no nginx, adicionar card no `index.php` da raiz.
