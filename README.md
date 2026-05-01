# CodeRush Sites

Hub multi-site do ecossistema **CodeRush** — conjunto de empresas de tecnologia operando sob um mesmo repositório, cada uma com seu próprio domínio, diretório e identidade visual.

---

## Empresas

| Empresa | Domínio | Diretório | Stack |
|---|---|---|---|
| **CodeRush Hub** | coderush.com.br | `/` | PHP + Tailwind compilado + CSS/JS externos |
| **Sistema Venda Direta** | sistemavendadireta.com.br | `sistemavendadireta/` | PHP + Tailwind compilado |
| **Codafacil.dev** | codafacil.dev | `codafacil/` | PHP + Tailwind compilado |
| **FluxoInteligente IA** | fluxointeligenteia.com.br | `fluxointeligenteia/` | HTML + Tailwind compilado + CSS/JS externos |

### Posicionamento da FluxoInteligente IA

A FluxoInteligente IA vende **agentes corporativos integrados ao negócio**, não apenas fluxos de automação. A narrativa pública deve priorizar:

- agentes corporativos com RAG, tools, canais e integrações;
- base de conhecimento privada, documentos e busca semântica;
- permissões, logs, auditoria, observabilidade e LGPD;
- execução segura em CRM, ERP, APIs, planilhas, bancos e e-mail/WhatsApp;
- **Link** como plataforma/base para agentes inteligentes corporativos.

Evitar posicionar a marca como apenas `n8n`, chatbot simples ou automação genérica.

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
│   ├── css/                      # Tailwind compilado, otimizacoes e CSS extraido
│   ├── js/                       # JS extraido das paginas
│   ├── imagens/                  # Logos, imagens, lottie e capas do blog
│   ├── blog/                     # Índice do blog
│   ├── 2023/ e 2026/             # Posts do blog
│   ├── wordpress/                # Landing WP services
│   ├── inteligencia-artificial/  # Landing IA
│   ├── enviar-contato.php        # Handler de formulários
│   └── .env                      # Credenciais SMTP
│
├── codafacil/                    # codafacil.dev
│   ├── index.php                 # Landing page standalone
│   ├── css/
│   ├── js/
│   └── imagens/
│
└── fluxointeligenteia/           # fluxointeligenteia.com.br
    ├── index.html
    ├── agentes-corporativos/
    ├── link/
    ├── css/
    └── js/
```

---

## Build de CSS

Todos os sites públicos ativos usam Tailwind compilado localmente. Após alterar classes Tailwind em qualquer página:

```bash
npm install
npm run build:css
```

O CSS manual extraído das páginas fica em `css/styles.css` dentro de cada site. O JS extraído fica em `js/scripts.js` quando a página tem comportamento próprio.

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

Ver `GUIDELINE_SITE_SVD.md` para o guia operacional atualizado do multi-site e `docs/` para rotinas de blog/performance.
