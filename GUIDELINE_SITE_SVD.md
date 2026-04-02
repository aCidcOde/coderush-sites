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

## 1) Visão geral

O **CodeRush** é um hub central de tecnologia que reúne múltiplas empresas. Cada empresa tem seu próprio diretório, domínio e assets auto-contidos. O Nginx aponta cada domínio diretamente para o diretório correspondente.

### Empresas ativas

| Empresa | Domínio | Diretório | Stack |
|---|---|---|---|
| CodeRush Hub | coderush.com.br | `/` (raiz) | PHP + Tailwind CDN |
| Sistema Venda Direta | sistemavendadireta.com.br | `sistemavendadireta/` | PHP + Tailwind compilado |
| Codafacil.dev | codafacil.dev | `codafacil/` | PHP + Tailwind compilado |
| WordPress Consultoria | wordpressconsultoria.com.br | `wordpressconsultoria/` | HTML + Tailwind CDN |
| FluxoInteligente IA | fluxointeligenteia.com.br | `fluxointeligenteia/` | HTML + Tailwind CDN |

### Empresas em stand-by

- **Traço Creative Lab** — design e UX (card no hub, sem site ainda)

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
Cada domínio tem seu próprio `server {}` com `root` apontando para o diretório da empresa. Em dev, todos acessíveis via `localhost:8081/nome-do-diretorio/`.

---

## 3) Regra de assets — isolamento por site

**Cada site é 100% auto-contido.** Assets devem usar paths relativos dentro do próprio diretório. Isso garante funcionamento tanto via subdiretório (dev) quanto via domínio próprio (produção).

```html
<!-- Sites com Tailwind CDN (hub, wordpressconsultoria, fluxointeligenteia) -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- SVD: Tailwind compilado localmente -->
<link rel="stylesheet" href="index_svd_files/site-tailwind.css" />

<!-- Codafacil: Tailwind compilado localmente -->
<link rel="stylesheet" href="site-tailwind.css" />
```

---

## 4) Identidade visual

### Paleta de cores base

| Site | Background | Acento |
|---|---|---|
| CodeRush Hub | `#020b1a` | Azul `#004AAD` |
| SVD | `bg-brand` (`#004AAD`) | Azul (é o brand) |
| Codafacil | `#04110d` via `body,main,footer { background: #04110d !important }` | Violeta/azul |
| WP Consultoria | `#06111a` | Sky `#21759b` |
| FluxoInteligente IA | `#04110d` | Emerald `#059669` |

### Fontes
Todos os sites usam:
- **Body:** Inter (via Google Fonts)
- **Headings:** Montserrat (via Google Fonts)

SVD: Inter declarado via `--font-body` em `site-optimizations.css`. Codafacil: declarado via `<style>` inline no head.

**Importante para Codafacil:** o fundo dark é aplicado via CSS inline `body, main, footer { background: #04110d !important }` porque o Tailwind está compilado (classes arbitrárias `bg-[#04110d]` não existem no bundle). Não usar classes Tailwind arbitrárias de cor no Codafacil sem recompilar.

### Rodapé — Ecossistema CodeRush
Todos os sites têm seção no rodapé com links pill para os outros domínios do grupo:
- Links estilo `rounded-full border border-white/15 px-3 py-1`
- Hover: `hover:border-white/35 hover:text-white/80`
- Cada site omite seu próprio domínio da lista

---

## 5) Sistema Venda Direta (`sistemavendadireta/`)

### 5.1 Mapa de rotas
- `/` → `index.php` (home SVD)
- `/blog/` → `blog/index.php`
- `/wordpress/` → `wordpress/index.php` (landing WP services do SVD)
- `/inteligencia-artificial/` → `inteligencia-artificial/index.php`
- `/2023/.../` e `/2026/.../` → posts individuais
- `/enviar-contato.php` — handler de formulários
- `/enviar-telefone.php` — wrapper de compatibilidade

### 5.2 Estrutura de componentes PHP
```
sistemavendadireta/
├── index.php
├── components/
│   ├── layout/
│   │   ├── head.php       (meta, CSS, fontes — Inter + Montserrat + Roboto)
│   │   ├── header.php     (nav sticky com scroll compacto)
│   │   ├── footer.php     (rodapé institucional + Ecossistema CodeRush)
│   │   └── scripts.php    (JS inline, WhatsApp, Lottie)
│   ├── sections/          (14 seções da home)
│   └── ui/                (cta-button, section-title)
├── index_svd_files/       (CSS compilado, JS, logos, imagens, lottie, posts/)
├── .env                   (credenciais SMTP)
└── ...
```

### 5.3 Build de CSS
```bash
npm run build:css
# Lê: tailwind.config.cjs + index_svd_files/site-tailwind.input.css
# Gera: sistemavendadireta/index_svd_files/site-tailwind.css
```
Rodar sempre que adicionar classes Tailwind novas nos arquivos PHP do SVD.

### 5.4 Formulários e e-mail
- Arquivo: `sistemavendadireta/enviar-contato.php`
- Config em `sistemavendadireta/.env`
- Campos: nome, e-mail, telefone/whatsapp, serviço, mensagem, origem, redirect

### 5.5 Blog — adicionar novo post
1. Criar diretório `2026/MM/DD/slug-do-post/index.php`
2. Salvar capa em `index_svd_files/posts/`
3. Atualizar card no `blog/index.php`
4. Atualizar `components/sections/blog-destaques.php` (se for um dos 3 mais recentes)
5. Incluir SEO completo e dados estruturados `BlogPosting`

### 5.6 SEO
Todas as páginas públicas: `title`, `meta description`, `meta robots`, `canonical`, `hreflang`, Open Graph, Twitter Cards, dados estruturados.

---

## 6) CodeRush Hub (`/` raiz)

### Arquivo principal
`index.php` — hub com cards de todas as empresas

### Grid de empresas
- **Linha 1** (3 cards): SVD, Codafacil, WP Consultoria
- **Linha 2** (2 cards centralizados, `lg:w-2/3 lg:mx-auto`): FluxoInteligente IA, Traço Creative Lab (em breve)

### Ao adicionar nova empresa ao hub
1. Criar diretório com `index.html` auto-contido
2. Adicionar virtual host no `docker/nginx/default.conf`
3. Adicionar card no `index.php` (ajustar grid conforme número de empresas)
4. Adicionar link pill no rodapé de todos os outros sites
5. Adicionar link pill no rodapé do novo site apontando para todos os outros

---

## 7) Codafacil (`codafacil/`)

Arquivo único: `codafacil/index.php`

Assets locais (não alterar sem recompilar):
- `codafacil/site-tailwind.css`
- `codafacil/site-optimizations.css`
- `codafacil/logo.webp` / `logo.png`
- `codafacil/Codafacil.mp4`

Background dark aplicado via `<style>` inline — ver seção 4.

---

## 8) WordPress Consultoria (`wordpressconsultoria/`)

`wordpressconsultoria/index.html` — HTML puro + Tailwind CDN  
Cor: `#21759b` (azul WordPress)  
Seções: hero, serviços (6 cards), diferenciais (4 itens), contato

---

## 9) FluxoInteligente IA (`fluxointeligenteia/`)

`fluxointeligenteia/index.html` — HTML puro + Tailwind CDN  
Cor: `#059669` (emerald)  
Seções: hero, como funciona (timeline 4 etapas), soluções (6 cards), resultados, contato

---

## 10) Fluxo de publicação

```
1. Alterar arquivos do site correspondente
2. SVD: validar com `php -l arquivo.php`
3. SVD com classes novas: `npm run build:css`
4. Testar: http://localhost:8081/[diretorio]/
5. Commit: prefixo [FEAT] / [FIX] / [CHORE]
6. Push → develop → PR → main
```

---

## 11) Regras para alterações por prompt

Sempre informar:
1. **Site alvo** — ex.: `sistemavendadireta`, `wordpressconsultoria`
2. **Arquivo** — ex.: `sistemavendadireta/components/sections/hero.php`
3. **Bloco alvo** — ex.: `#contato`, nome da seção
4. **Objetivo** — ex.: "trocar copy", "adicionar card"
5. **Restrições** — ex.: "não alterar layout", "manter SEO"

Checklist mínimo por alteração:
- Assets com paths relativos ao diretório do site
- Não usar classes Tailwind arbitrárias no Codafacil sem recompilar
- Preservar tags SEO em páginas PHP
- Manter rodapé Ecossistema CodeRush em todas as páginas públicas
- Validar sintaxe PHP após edições

---

## 12) Observações

- Posts legados em `sistemavendadireta/2023/` têm conteúdo importado — evitar refatorações globais sem escopo explícito.
- `tailwind.config.cjs` e `package.json` ficam na raiz mas o build é para `sistemavendadireta/index_svd_files/`.
- Sites com Tailwind CDN não precisam de build local.
- SVD mantém fundo azul brand (`#004AAD`) como identidade própria do produto.
