<!--
/*
[Modulo Documentacao Operacional — Runbook de lancamento: promocao de instalacao SVD]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-04
Revisao geral pre-lancamento executada em 2026-08-04 (0 problemas na bateria automatizada).
Companion: campanha-google-ads-oferta.md (conteudo das campanhas) e
pesquisa-mercado-concorrentes.md (posicionamento e roadmap de produto).
*/
-->

# Runbook — Lançamento da promoção de instalação (até 31/08/2026)

## 1) Estado verificado em 2026-08-04 (bateria automatizada: 0 problemas)

| Área | Status | Verificação |
|---|---|---|
| Páginas (home, cases, 3 LPs, blog, post, IA, wordpress, painel) | ✅ 200 | HTTP + conteúdo |
| Links internos | ✅ zero quebrados | crawl das 5 páginas principais |
| Preços/datas da promo (3.500 / 3.000 / 31-08) | ✅ consistentes | home+modal, 3 LPs, banner OG, doc de campanha |
| Valores antigos (2.500 / "50%") | ✅ ausentes | grep em todas as páginas |
| SEO (title ≤70, description ≤160, canonical, robots) | ✅ | LPs e painel noindex; resto index |
| JSON-LD | ✅ válido | parse em todas as páginas |
| GA4 (G-4107EVTE0Q) | ✅ em todas as páginas públicas | inclusive 30 posts |
| Eventos (generate_lead, whatsapp_click, simulator_use, form_start, promo_modal_*) | ✅ | key events marcados; dimensões sim_faturamento e page |
| Ads ↔ GA4 | ✅ vinculado (SourceNET 357-892-7161) | conversões importadas; generate_lead principal |
| Público de remarketing | ✅ acumulando | "Visitou /oferta/ sem converter (30d)" |
| Leads: form → sqlite + e-mail | ✅ testado ponta a ponta | atribuição client_id/gclid/UTM/faixa |
| Cliques WhatsApp → lead com [ref XXXXX] | ✅ testado | dedupe por sessão, 422 pra ref inválido |
| Purchase offline (Measurement Protocol) | ✅ validado no endpoint de debug | registrar-venda.php (--whatsapp/--ref/--lead-id) |
| Painel /painel-leads/ | ✅ | senha .env, GA snapshot 3/3h via cron |
| Segurança | ✅ | /storage/ 403 nos 3 sites, zap-lead 405 p/ GET, .env inacessível |
| Peso (CSS 60KB, hero webp, OG 100KB) | ✅ dentro dos alvos | imagens com loading/priority |
| Modal da promo na home | ✅ | 1x/sessão, expira 31/08 sozinho, UTM próprio |

## 2) Sequência de lançamento (ordem exata)

1. **Google Ads** (interface, conta SourceNET):
   - Criar as 3 campanhas do `campanha-google-ads-oferta.md` (geral R$30/dia + 2 verticais R$10/dia)
   - Conferir: `generate_lead` = ação PRINCIPAL, `whatsapp_click` = secundária
   - Marcação automática (auto-tagging) ativa
2. **Divulgação própria** (WhatsApp/redes): compartilhar `https://www.sistemavendadireta.com.br/oferta/?promo`
   (o `?promo` fura o cache de preview e mostra o banner novo)
3. **Primeiro lead**: conferir chegada tripla — e-mail, painel e GA4 (Tempo real)

## 3) Operação diária durante a campanha

- **Responder lead em minutos** — o e-mail/painel entrega campanha + faixa simulada de cada lead
- **Fechou venda**: `docker exec coderush-app php /var/www/html/automation/leads/registrar-venda.php --ref=XXXXX --valor=3000`
  (ou `--whatsapp=...`; o [ref] chega na 1ª mensagem do WhatsApp do cliente)
- **Painel** `https://sistemavendadireta.com.br/painel-leads/` — leads, cliques zap, campanhas, faixas (GA a cada 3h)
- **Semanal no Ads**: relatório de termos de pesquisa → negativar "emprego/vaga/renda/como entrar"

## 4) Gatilhos de decisão (resumo; detalhes no doc da campanha)

- CTR < 3% → trocar títulos, não verba · Clique bom + lead zero → problema de LP, chamar o Claude
- Vertical com CPA < geral (2-3 semanas) → verba dedicada
- ≥15 conversões/30d → lance passa a "maximizar conversões"
- Tráfego sem conversão → ativar o público de remarketing como Observação (+20-30% lance)

## 5) Encerramento/renovação (31/08)

- Banner da barra, badge do hero e modal da home **expiram sozinhos** (checagem de data em PHP)
- Renovar: editar `$promoDeadline`/`$promoSlots`/valores no topo de `oferta/index.php` (e das 2 verticais)
  e `$promoModal` no `index.php` da home; regenerar o banner OG (pedir ao Claude)
- Pausar campanhas no Ads na mesma data (não expira sozinho lá!)

## 6) Pendências conhecidas (não bloqueiam o lançamento)

- Search Console: cadastrar sistemavendadireta.com.br (e BFR) — organico sem visibilidade até lá
- Diretórios gratuitos: Capterra, GetApp, comparasoftware.com.py
- Fase 2: LP /oferta/es/ + campanha Paraguai/Bolívia (aguarda preço USD + atendimento em espanhol)
- Produto (backlog da pesquisa de mercado): link de indicação rastreável, página do consultor,
  e-wallet, matriz forçada, PWA
- MedPlant: case pronto com `hidden => true` — publicar quando o cliente liberar
- Vitrine `www.foroneglobal.com/lander` com erro 520 (origem externa) — o case aponta pro escritório
