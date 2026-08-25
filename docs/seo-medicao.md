# Medição SEO — dominar "sistema MMN" e "sistema venda direta"

Registro do experimento iniciado em **25/08/2026**, com o baseline congelado antes
de qualquer mudança. Sem isso não há como saber se o que fizemos funcionou.

## Por que não há teste A/B clássico

Teste A/B de SEO não funciona do jeito que funciona em landing page. O Google
indexa **uma** URL, e servir conteúdo diferente para o mesmo endereço dependendo
de quem pede é *cloaking* — penalizável. Também não dá para dividir tráfego
orgânico: quem decide quem vê o quê é o buscador, não nós.

O que se usa no lugar:

| Método | Quando cabe | Limite |
|---|---|---|
| **Temporal** (antes/depois) | é o nosso caso | sazonalidade e mudanças de algoritmo contaminam |
| **Por grupo de páginas** | muitas páginas equivalentes | precisa de dezenas de URLs similares; não temos |
| **Por consulta** | comparar termos mexidos vs. intocados | é o controle que dá pra fazer aqui |

**Nosso desenho:** temporal, com grupo de controle por consulta. As buscas que
não recebem página nova servem de referência — se elas também subirem, o ganho
foi de mercado ou de algoritmo, não das mudanças.

## Baseline — 28 dias até 25/08/2026

Capturado ANTES das alterações. Fonte: Search Console, propriedade
`sc-domain:sistemavendadireta.com.br`.

| Consulta | Impressões | Cliques | CTR | Posição | Grupo |
|---|---|---|---|---|---|
| sistema mmn | 47 | 0 | 0% | 25,9 | **teste** (página nova) |
| sistema de marketing multinivel | 29 | 0 | 0% | 5,4 | teste (snippet) |
| sistema marketing multinivel | 28 | 0 | 0% | 7,1 | teste (snippet) |
| plataforma mmn | 20 | 0 | 0% | 10,7 | teste |
| plataforma venda direta | 14 | 0 | 0% | 9,3 | controle |
| sistema de mmn | 5 | 0 | 0% | 5,6 | teste |

Agregado de 90 dias nas 14 consultas do núcleo comercial, para referência:
**873 impressões, 12 cliques, CTR 1,37%.**

O CTR é o número que denuncia o problema. Em 3ª–4ª posição, o CTR esperado fica
entre 8% e 12%. Estar em 1,37% significa que **aparecemos e não somos escolhidos**
— problema de snippet e de oferta visível, não de posicionamento.

## O que foi alterado em 25/08

1. **Página nova `/sistema-mmn/`** — `sistema mmn` tinha a maior demanda do nicho
   (182 impressões em 90 dias) e a pior posição (26,9). Nenhuma página do site era
   *sobre* isso: quem buscava caía na home, que fala de "venda direta" primeiro.
   Artigo de blog rankeia para dúvida; página de produto rankeia para produto.
   Inclui `SoftwareApplication` e `FAQPage` em JSON-LD.
2. **Sitemap** — `/sistema-mmn/` e `/simulador/` foram incluídos. O simulador
   estava no ar desde 18/08 e fora do sitemap.
3. **Menu** — as duas páginas entraram na navegação da home, desktop e mobile.
   Link interno da home é o sinal mais forte que se pode dar ao rastreador.

## O que medir, e quando

**Não olhe antes de 7 dias.** O Google precisa rastrear, indexar e acumular
impressões. Antes disso, ruído.

| Prazo | O que já dá para ler |
|---|---|
| 3–5 dias | a página foi indexada? (`site:sistemavendadireta.com.br/sistema-mmn/`) |
| 7–10 dias | primeiras impressões da página nova; efeito de snippet nas antigas |
| 21–28 dias | comparação honesta com o baseline |

Comando da leitura:

```bash
python3 automation/ads/search-console.py --dias=28
```

### Critério de sucesso

- **`sistema mmn` sai da posição 26 para a primeira página** (≤10) em 28 dias.
- **CTR do núcleo comercial sai de 1,37% para ≥4%** — ainda abaixo do ideal, mas
  triplicaria os cliques com o mesmo posicionamento.
- **Controle não sobe junto.** Se `plataforma venda direta` melhorar na mesma
  proporção sem ter recebido mudança, o ganho não foi nosso.

### Critério de fracasso

Se em 28 dias `sistema mmn` continuar acima da posição 15, a hipótese "faltava
página dedicada" está errada, e o problema é autoridade de domínio — o que se
resolve com links e volume de conteúdo, não com uma página.

## O que fica de fora deste teste

- **Mídia paga** roda em paralelo e não interfere no orgânico (o Google trata os
  dois de forma independente), mas pode confundir a leitura de *leads*: um lead
  pode ter visto o anúncio e voltado pelo orgânico. Por isso o critério de
  sucesso aqui é posição e CTR, não lead.
- **Citação em IA** (ChatGPT, Perplexity) não aparece no Search Console. Hoje são
  4 sessões em 30 dias, medidas no GA4 pela origem `chatgpt.com / ai-assistant`.
  O `robots.txt` está liberado para todos os agentes — conferido em 25/08.

## Histórico de leituras

| Data | `sistema mmn` | CTR do núcleo | Observação |
|---|---|---|---|
| 25/08/2026 | pos 25,9 · 0 cliques | 1,37% | baseline, antes das mudanças |
