# CodeRush Sites

Hub multi-site do ecossistema **CodeRush** — conjunto de empresas de tecnologia operando sob um mesmo repositório, cada uma com seu próprio domínio, diretório e identidade visual.

---

## Empresas

| Empresa | Domínio | Diretório | Stack |
|---|---|---|---|
| **CodeRush Hub** | coderush.com.br | `/` | PHP + Tailwind CDN |
| **Sistema Venda Direta** | sistemavendadireta.com.br | `sistemavendadireta/` | PHP + Tailwind compilado |
| **Codafacil.dev** | codafacil.dev | `codafacil/` | PHP + Tailwind compilado |
| **WordPress Consultoria** | wordpressconsultoria.com.br | `wordpressconsultoria/` | HTML + Tailwind CDN |
| **FluxoInteligente IA** | fluxointeligenteia.com.br | `fluxointeligenteia/` | HTML + Tailwind CDN |

---

## Rodando localmente

**Pré-requisitos:** Docker e Docker Compose.

```bash
# Subir todos os containers
docker compose up -d --build

# Acessar
http://localhost:8081/                      # CodeRush Hub
http://localhost:8081/sistemavendadireta/   # Sistema Venda Direta
http://localhost:8081/codafacil/            # Codafacil.dev
http://localhost:8081/wordpressconsultoria/ # WordPress Consultoria
http://localhost:8081/fluxointeligenteia/   # FluxoInteligente IA
```

**Containers:**
- `coderush-app` — PHP 8.3-FPM Alpine
- `coderush-nginx` — Nginx Alpine com virtual hosts por domínio

---

## Estrutura

```
coderush-sites/
├── index.php                     # Hub CodeRush (raiz)
├── docker-compose.yml
├── Dockerfile
├── docker/nginx/default.conf     # Virtual hosts
│
├── sistemavendadireta/           # sistemavendadireta.com.br
│   ├── index.php
│   ├── components/               # Componentes PHP (layout, sections, ui)
│   ├── index_svd_files/          # CSS compilado, JS, imagens, lottie
│   ├── blog/                     # Índice do blog
│   ├── 2023/ e 2026/             # Posts do blog
│   ├── wordpress/                # Landing WP services
│   ├── inteligencia-artificial/  # Landing IA
│   ├── enviar-contato.php        # Handler de formulários
│   └── .env                      # Credenciais SMTP
│
├── codafacil/                    # codafacil.dev
│   └── index.php                 # Landing page standalone
│
├── wordpressconsultoria/         # wordpressconsultoria.com.br
│   └── index.html
│
└── fluxointeligenteia/           # fluxointeligenteia.com.br
    └── index.html
```

---

## Build de CSS (SVD)

O Sistema Venda Direta usa Tailwind compilado. Após alterar classes nos arquivos PHP:

```bash
npm install
npm run build:css
```

Os demais sites usam Tailwind CDN e não precisam de build.

---

## Variáveis de ambiente

Copie `.env.example` para `.env` dentro de `sistemavendadireta/` e configure:

```
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=
```

---

## Documentação técnica

Ver `GUIDELINE_SITE_SVD.md` para guia completo de manutenção, padrões de SEO, estrutura de componentes e regras de atualização por prompt.
