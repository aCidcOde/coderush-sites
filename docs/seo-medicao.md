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

## Snapshot de 29/08/2026 — fim do primeiro ciclo

Fechamento do mês para comparar com setembro. Números congelados aqui porque
relatório vivo muda embaixo do pé: em 30 dias o "últimos 14 dias" já é outro
período.

### Tráfego no site (GA4)

| Canal | 02–15/08 | 16–29/08 | Variação |
|---|---|---|---|
| **Pago** | 34 sessões | **43** | ▲ 26% |
| **Orgânico** | 19 | **7** | ▼ 63% |
| Direto | 92 | 66 | ▼ 28% |
| Outros | 26 | 21 | ▼ 19% |
| **Total** | **175** | **137** | ▼ 22% |

### Busca orgânica (Search Console)

| | 02–15/08 | 16–29/08 |
|---|---|---|
| Impressões | 1.430 | 950 |
| Cliques | 29 | 28 |
| **CTR** | 2,03% | **2,95%** |
| Posição média | 11,0 | 11,8 |

Impressões caíram 34%, cliques ficaram iguais e o CTR subiu 45%. Aparecemos
menos e somos escolhidos mais — coerente com os títulos e descriptions
reescritos entre 17 e 25/08.

**A queda de impressões não tem explicação confirmada.** Sazonalidade de fim de
mês, flutuação de algoritmo ou efeito de alguma mudança nossa — duas semanas não
separam as hipóteses. É a pergunta principal para setembro.

Nota metodológica: o Search Console conta 28 cliques e o GA4, 7 sessões
orgânicas. Não é divergência — parte dos cliques cai em subdomínio de cliente
(medplant, avig360), que tem medição própria.

### Mídia paga (Google Ads)

| | |
|---|---|
| Investido (11–29/08) | R$ 190 |
| Cliques | 39 |
| CPC — fase de calibração (11–20/08) | R$ 5,55 |
| CPC — fase afinada (24–29/08) | **R$ 2,19** |
| Leads | 3 |
| **Vendas** | **1 — VELARO, R$ 3.500** |
| Retorno sobre a mídia | **18×** |

### Sinal a confirmar

`sistema mmn` saiu da posição 25,1 (baseline de 28 dias) para 8,0 nos quatro dias
após a criação da `/sistema-mmn/`. **É uma única impressão** — pode ser o Google
testando página nova. Confirmar em setembro; se sustentar, a hipótese "faltava
página dedicada" está validada.

## Como dividir recurso a partir de setembro

A tentação é comparar custo por lead de cada canal e mover verba para o melhor.
Isso funciona entre duas campanhas pagas — não funciona entre pago e orgânico,
por três motivos:

1. **Orgânico não se compra no curto prazo.** Tirar dinheiro do Ads não acelera
   SEO. O que acelera é tempo e conteúdo, e o efeito aparece em 3–6 meses.
2. **A escala atual não sustenta a conta.** 7 a 43 sessões por canal em duas
   semanas é ruído; uma venda a mais inverte qualquer ranking.
3. **Eles se alimentam.** A página que rankeia é a mesma que recebe o clique
   pago, e a nota de qualidade do Ads depende da qualidade da página.

O critério que faz sentido é por **função**, não por eficiência comparada:

| Canal | Papel | Como medir |
|---|---|---|
| **Pago** | volume previsível agora | custo por lead qualificado, retorno sobre a mídia |
| **Orgânico** | custo marginal zero no futuro | posição nos termos do núcleo, CTR |
| **Conteúdo** | alimenta orgânico e IA | impressões dos posts, citações |

Setembro deveria responder duas perguntas, não uma:

- **O pago escala mantendo o CPC de R$ 2,19?** Se sim, aumentar orçamento é a
  decisão óbvia — foi o canal que trouxe a única venda.
- **A queda de impressões orgânicas foi ponto fora da curva?** Se as impressões
  voltarem sem que a gente faça nada, era sazonalidade. Se continuarem caindo com
  o CTR alto, é perda de posicionamento que nenhuma melhoria de snippet resolve.

## Histórico de leituras

| Data | `sistema mmn` | CTR do núcleo | Observação |
|---|---|---|---|
| 25/08/2026 | pos 25,9 · 0 cliques | 1,37% | baseline, antes das mudanças |
| 29/08/2026 | pos 8,0 · 1 impressão | 2,95% (site todo) | página nova ainda não indexada |
