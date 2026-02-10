<!--
/*
[Modulo Documentacao Operacional do Site SVD]
@Author: André Gomes ( @acidcode )
@since 2026-02-10
Guia tecnico para manutencao, expansao e atualizacao do site institucional SVD em PHP estatico com foco em SEO.
*/
-->

# Guideline Operacional do Site SVD

## 1) Objetivo
Este guia consolida a implementação atual do site para facilitar atualizações futuras por prompt, mantendo:
- Identidade visual
- Estrutura de rotas por diretório
- SEO técnico consistente
- Padrão de conteúdo entre páginas principais, blog e landing pages

---

## 2) Mapa de rotas (produção)
Todas as páginas usam padrão SEO com diretório (`/rota/`) e arquivo físico `index.php`.

- `/` → `index.php` (site principal SVD)
- `/blog/` → `blog/index.php` (lista completa de posts)
- `/codafacil/` → `codafacil/index.php` (subproduto de desenvolvimento com IA)
- `/inteligencia-artificial/` → `inteligencia-artificial/index.php` (IA para MMN/Vendas Diretas)
- `/wordpress/` → `wordpress/index.php` (serviços WordPress/WooCommerce/Laravel)
- `/2023/.../` e `/2026/.../` → posts individuais

Endpoints de formulário:
- `/enviar-contato.php`
- `/enviar-telefone.php` (wrapper de compatibilidade que redireciona para `enviar-contato.php`)

Arquivos SEO globais:
- `/robots.txt`
- `/sitemap.xml`

---

## 3) Estrutura de arquivos relevante

- `index.php`: home principal, seções comerciais, bloco IA, 3 últimos posts, FAQ, formulário de telefone
- `blog/index.php`: catálogo com todos os posts locais
- `codafacil/index.php`: landing de desenvolvimento orientado por IA (contato completo)
- `inteligencia-artificial/index.php`: landing de IA para multinível (contato completo)
- `wordpress/index.php`: landing de serviços WordPress
- `2023/**/index.php` e `2026/**/index.php`: posts
- `index_svd_files/`: assets visuais (logos, imagens, lottie e imagens dos posts)
- `index_svd_files/posts/`: capas dos posts

---

## 4) Identidade visual e frontend

### 4.1 Home, Blog, IA, WordPress e Posts
Stack principal:
- Tailwind via CDN: `https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4`
- Fontes Google (Montserrat + Roboto)
- Tema com variáveis em `@theme` minificado dentro de `<style type="text/tailwindcss">`

Tokens de cor principais:
- `--color-brand: #004AAD`
- `--color-brand-dark: #003F91` (varia em alguns arquivos com caixa baixa)
- `--color-brand-soft: #215BA8` (onde aplicável)

### 4.2 Codafácil
Stack específico:
- Tailwind via CDN: `https://cdn.tailwindcss.com`
- CSS custom minificado em `<style>` para hero, cards e utilitários
- Cor base visual: faixa de azul `#0b4db6` a `#083e9a`

Observação:
- Manter o padrão atual por página (não misturar stack da home com codafacil sem migração planejada).

---

## 5) Menus e blocos-chave por página

### 5.1 Home (`/`)
Menu desktop/mobile:
- Funcionalidades
- Porque
- Vantagens
- Desenvolvimento com IA
- Clientes
- CTA orçamento

Blocos principais (IDs úteis para edição):
- `#funcionalidades`
- `#porque`
- `#vantagens`
- `#desenvolvimento-ia`
- `#ia-mmn`
- `#clientes`
- `#form`

Blog na home:
- Exibe apenas 3 posts mais recentes
- Link para ver todos: `/blog/`

Form da home:
- Action: `enviar-telefone.php`
- Campos: nome + whatsapp

### 5.2 Inteligência Artificial (`/inteligencia-artificial/`)
Menu:
- IA na Operação
- Planos
- Método
- CodaFácil
- Contato

Blocos (IDs):
- `#ia-operacao`
- `#ofertas`
- `#metodo`
- `#codafacil`
- `#contato`

Form:
- Action: `../enviar-contato.php`
- Hidden comum: origem + redirect

### 5.3 CodaFácil (`/codafacil/`)
Menu:
- Serviços
- Processo
- Vantagens
- Tecnologia
- Clientes
- Link para SVD

Blocos (IDs):
- `#servicos`
- `#processo`
- `#vantagens`
- `#stack`
- `#clientes`
- `#contato`

Clientes atuais no bloco:
- Emergency SAAS
- Orçamento Fácil
- Card “Em breve pode ser você”

Form:
- Action: `../enviar-contato.php`
- Hidden comum: origem + redirect

### 5.4 WordPress (`/wordpress/`)
Âncoras:
- `#servicos`, `#processo`, `#cases`, `#planos`, `#faq`, `#contato`

Foco:
- Página institucional comercial baseada na guideline de serviços

### 5.5 Blog (`/blog/`)
- Lista todos os posts locais (2023 + 2026)
- Header simplificado com links para SVD, IA e CodaFácil

### 5.6 Posts individuais (`/2023/...` e `/2026/...`)
Padrão implementado:
- Botão “Veja mais no blog” para `/blog/`
- Bloco “Leia também” com mix de 3 posts
- Rodapé institucional replicado
- Botão flutuante WhatsApp

---

## 6) Rodapé padrão institucional
Rodapé comum usado nas páginas institucionais e posts contém 3 colunas:

1. Identidade e contato:
- Logo
- Descrição curta
- Telefone
- E-mail

2. Menu institucional:
- Sistema Venda Direta
- WordPress
- Desenvolvimento com IA
- Multinível com IA
- Blog

3. Autoridade comercial:
- "25 anos de experiência desenvolvendo sistemas"
- Botão orçamento
- Redes sociais

Regra:
- Em novas páginas públicas, replicar esse rodapé para manter consistência.

---

## 7) SEO implementado

### 7.1 Em todas as páginas públicas
- `title`
- `meta description`
- `meta robots` com diretivas completas
- `canonical`
- `hreflang` (`pt-BR` e `x-default`)
- Open Graph completo
- Twitter Cards completos
- `theme-color`, `author`, `referrer`

### 7.2 Dados estruturados
- Home: `Organization`, `WebSite`, `FAQPage`
- Blog index: `Blog`
- Inteligência Artificial: `Organization`, `Service`, `FAQPage`
- WordPress: `ProfessionalService`
- CodaFácil: `Organization`, `Service`
- Posts: `BlogPosting` + `article:published_time` e `article:modified_time`

### 7.3 Indexação técnica
- `robots.txt` publicado
- `sitemap.xml` com 15 URLs públicas

---

## 8) Formulários e e-mail

Arquivo central:
- `enviar-contato.php`

Comportamento:
- Lê credenciais de `.env`
- Tenta envio via SMTP (TLS/SSL ou sem)
- Fallback opcional para `mail()`
- Redireciona com status `?mail=ok` ou `?mail=erro`

Compatibilidade:
- `enviar-telefone.php` apenas inclui `enviar-contato.php`

Campos aceitos:
- Nome, e-mail, telefone/whatsapp, serviço, mensagem, origem, redirect

---

## 9) Assets e imagens

Pasta principal:
- `index_svd_files/`

Posts:
- `index_svd_files/posts/`

Regra para novas capas de post:
- Salvar na pasta `index_svd_files/posts/`
- Atualizar imagem na home (se post for dos 3 últimos)
- Atualizar card no `/blog/`
- Atualizar OG image no post correspondente

---

## 10) Regras para futuras atualizações por prompt
Use sempre este formato ao pedir alterações:

1. Página alvo
Ex.: `/inteligencia-artificial/`

2. Bloco alvo
Ex.: `#ofertas` ou título exato da seção

3. Objetivo da mudança
Ex.: “trocar copy para foco em redução de custo logístico”

4. Restrições
Ex.: “não alterar layout”, “manter rodapé padrão”, “não mudar SEO atual”

5. Saída esperada
Ex.: “apenas texto”, “texto + imagem”, “novo card”, “novo post completo”

Checklist mínimo por alteração:
- Manter links em formato de diretório (`/rota/`)
- Não voltar para `index.html`
- Preservar tags SEO existentes
- Preservar consistência de menu e rodapé
- Validar sintaxe PHP (`php -l`)

---

## 11) Fluxo de publicação (Git)
Padrão recomendado:
1. Ajustar arquivos necessários
2. Validar (`php -l` nos arquivos alterados)
3. Revisar links internos
4. Commit com tag no início da mensagem (`[FEAT]`, `[FIX]`, `[CHORE]`)
5. Push na `main`

---

## 12) Observações importantes
- O projeto foi limpo para manter foco em páginas públicas PHP e assets usados.
- Existem posts com conteúdo legado originalmente importado; evitar refatorações globais nesses posts sem escopo explícito.
- Para novas páginas, seguir padrão de `index.php` por diretório e incluir SEO completo desde o início.

