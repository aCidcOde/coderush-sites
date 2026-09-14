# Performance Mobile (Deploy)

## 0) Estado medido — 14/09/2026

Por que isto importa em dinheiro: o Índice de Qualidade do Google marcava
**"experiência da página" abaixo da média em quase toda palavra** da campanha, e
velocidade entra nessa nota. Nota baixa custa posição pelo mesmo lance — ou o
mesmo lugar pagando mais caro. Não é otimização de vaidade.

| Página | Total | Maior ativo |
|---|---|---|
| `/` | 549 KB | gtag.js (187 KB) |
| `/oferta/` | 355 KB | gtag.js (187 KB) |
| `/sistema-mmn/` | 347 KB | gtag.js (187 KB) |
| `/cases/` | 414 KB | gtag.js (187 KB) |
| `/blog/` | 1.535 KB* | gtag.js (187 KB) |

\* soma de tudo que a página referencia. Na prática os cards têm `loading="lazy"`,
então o navegador só baixa o que entra na tela.

**gtag.js é hoje o maior peso de toda página** — sozinho, mais que todas as
imagens somadas. Não sai: é ele que registra a conversão que a campanha usa para
aprender. Mitigado com `preconnect` + `crossorigin` em `inc/analytics.php`.

### Duas medições erradas que quase viraram decisão

- **"gzip está desligado no nginx"** — está, e não importa: o Cloudflare comprime
  na borda com Brotli (HTML de 76 KB → 14 KB). Mexer no nginx não mudaria um byte
  para o visitante. **Sempre medir na URL pública, nunca na config.**
- **"gtag.js pesa 567 KB"** — a requisição de teste não negociou compressão. Com
  `Accept-Encoding` de navegador são 187 KB. Medir com User-Agent e
  `Accept-Encoding` reais, senão o número não é o que o usuário baixa.

### Imagens — o que estava errado

Estado híbrido que não aparece olhando a página, só pesando ela: metade dos logos
de cliente já tinha `.webp` gerado e o site servia o `.png` do mesmo jeito. A
conversão tinha sido feita uma vez, na mão; os clientes adicionados depois
entraram só em PNG. As 39 capas de post somavam 5,9 MB sem nenhum WebP.

| | Antes | Depois |
|---|---|---|
| Logos de cliente | 111 KB | 46 KB (−58%) |
| Capas de post | 5,9 MB | 1,3 MB (−78%) |

Rodar de novo quando entrar imagem nova:

```bash
python3 automation/seo/converter-webp.py --dir=sistemavendadireta/imagens/clientes
python3 automation/seo/converter-webp.py --dir=sistemavendadireta/imagens/posts
```

O `publisher.js` já gera o `.webp` junto da capa de cada post novo — converter à
mão só é preciso para imagem que não passa pelo blog-bot.

**Armadilha do `<picture>`:** `<source>` apontando para arquivo inexistente **não**
cai no `<img>`. O navegador confia no `srcset`, tenta baixar e mostra imagem
quebrada. Por isso o `source` só é emitido quando o `.webp` existe no disco —
checado a cada render, nunca presumido.

O `og:image` continua em JPG de propósito: crawler de rede social costuma não ler
WebP.

## 1) Build e publish

```bash
npm ci
npm run build:css
```

Arquivos que precisam subir junto:
- `css/site-tailwind.css`
- `css/site-optimizations.css`
- imagens `.webp` novas em `imagens/` e `imagens/posts/`
- páginas PHP atualizadas

## 2) Cache de estáticos (Nginx)

Use cache longo para assets estáticos:

```nginx
location ~* \.(css|js|webp|png|jpg|jpeg|gif|svg|ico|woff|woff2)$ {
    expires 30d;
    add_header Cache-Control "public, max-age=2592000, immutable";
    try_files $uri =404;
}
```

Para HTML/PHP, mantenha cache curto:

```nginx
location ~* \.(php|html)$ {
    add_header Cache-Control "no-cache, no-store, must-revalidate";
}
```

## 3) CDN

Cloudflare recomendado:
- `Caching Level`: Standard
- `Browser Cache TTL`: 1 month (para estáticos)
- `Auto Minify`: CSS/JS/HTML
- `Polish`: Lossy (se disponível)
- `WebP`: ligado

## 4) Checklist GTmetrix / PSI

Revalidar em:
- Home `/`
- Blog `/blog/`
- CodaFácil `/codafacil/`
- IA `/inteligencia-artificial/`
- 1 post `/2026/...`

Métricas alvo:
- LCP < 2.5s
- CLS < 0.1
- TBT < 200ms

## 5) AMP

Para este projeto (site institucional + landing + blog estático), AMP não é prioridade.
O ganho principal já vem de: WebP + `srcset` + lazy loading + cache + CDN.
