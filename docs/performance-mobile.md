# Performance Mobile (Deploy)

## 1) Build e publish

```bash
npm ci
npm run build:css
```

Arquivos que precisam subir junto:
- `index_svd_files/site-tailwind.css`
- `index_svd_files/site-optimizations.css`
- imagens `.webp` novas em `index_svd_files/` e `index_svd_files/posts/`
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
