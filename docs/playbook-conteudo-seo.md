# Playbook — conteúdo e SEO orientados por busca real

Metodologia destilada da auditoria do blog do Sistema Venda Direta (agosto/2026),
escrita para ser reaplicada em qualquer site do hub. **A BFR Intelligence é a
próxima da fila** — ver seção 7, que já mapeia o que fazer lá quando for a hora.

> A tese central: **posição boa em busca que ninguém faz não vale nada, e posição
> boa sem clique vale menos ainda.** Antes de escrever qualquer coisa nova,
> descubra o que já existe e não está rendendo.

---

## 1. O erro que este documento existe para evitar

O blog do SVD tinha 34 posts, execução técnica correta — ~950 palavras, 9
subtítulos, JSON-LD de `BlogPosting` e `FAQPage`, imagens, links internos, meta
description. E as posições provavam a qualidade: **4 a 8**.

Resultado em 90 dias: **144 impressões e 2 cliques.**

A causa não era SEO nem escrita. Era **escolha de pauta**. Os temas tinham sido
definidos à mão, em linguagem de consultoria — "governança comercial com dados e
IA", "previsibilidade de receita em MMN", "onboarding e ativação de distribuidor".
Corretos, bem escritos e sem nenhuma demanda de busca.

Enquanto isso, o público real digitava `sistema mmn`, `multinivel brasil`,
`plataforma mmn`, `marketing multinivel funciona`.

**Regra:** pauta se escolhe com dado de busca, não com o que a empresa acha
interessante falar. É o mesmo erro que a campanha de Ads cometeu ao escolher
palavras por intuição — 19 das 34 vieram marcadas como "raramente veiculada".

## 2. A ordem de prioridade (do mais barato ao mais caro)

Sempre nesta sequência. As duas primeiras dão retorno em dias; a terceira, em meses.

### Prioridade 1 — Já rankeia e não leva clique

O ganho mais rápido que existe. Filtro: **posição ≤ 8, impressões ≥ 10, CTR < 2%**.

No SVD isso revelou ~390 impressões em posição 2–7 com **1 clique**. Na terceira
posição o CTR esperado é ~10%; deveriam ser ~39 cliques.

Nesse cenário o conteúdo já está validado pelo Google — o que falha é **title e
meta description**. Corrigir é editar duas linhas por página.

Sinais de title fraco:
- nomeia a oferta em vez do produto ("Promoção 10 Anos" para quem buscou "sistema mmn");
- não contém o termo buscado, ou contém só a sigla quando a pessoa escreve por extenso;
- promete menos do que a concorrência exibida ao lado.

### Prioridade 2 — Volume real onde estamos mal posicionados

Filtro: **posição > 10, impressões ≥ 4**. Há demanda comprovada e espaço para subir.

Agrupe variantes na mesma pauta: `plataforma venda direta`, `plataforma de mmn`,
`plataforma para mmn` e `plataforma de multinivel` são um post só, não quatro.
A soma é o que dimensiona a prioridade.

### Prioridade 3 — Pautas sem histórico

Só depois das duas anteriores. Sem dado próprio, use as buscas da concorrência e
o vocabulário que aparece nos termos de busca do Ads.

## 3. Comercial x educacional — e por que o Ads não decide isso

Distinção que só ficou clara cruzando as duas fontes: **o que é ruim para anúncio
costuma ser bom para conteúdo.**

No Ads, tráfego informativo é desperdício puro — cada clique custa dinheiro e a
pessoa não compra. Foram negativados `marketing multinivel funciona`,
`empresas de marketing multinivel`, `multinivel brasil`.

No orgânico, o clique é grátis. Essas mesmas buscas são as de maior volume do
nicho, constroem autoridade, alimentam remarketing e capturam quem ainda vai
decidir. `multinivel brasil` sozinho tinha 143 impressões com a gente na posição 60.

Portanto:

| Trilha | Serve para | Métrica |
|---|---|---|
| **Topo de funil** (educacional) | volume, autoridade, remarketing | impressões e posição |
| **Fundo de funil** (comercial) | intenção de compra | cliques e leads |

Um blog só com pauta de fundo de funil não tem volume. Só com topo, não converte.
Precisa das duas, e a proporção depende de quanto tempo se pode esperar.

### Quando NÃO é post

Busca com volume alto **e** intenção comercial direta merece **página de produto**,
não artigo. `sistema mmn` tinha 180 impressões e posição 27 — isso é página
institucional otimizada, não post de blog. Artigo rankeia para dúvida; página
rankeia para produto.

## 4. Como executar a auditoria

```bash
python3 automation/ads/search-console.py --dias=90
```

Ele já classifica em comprar / avaliar / ignorar. Para conteúdo, a leitura muda um
pouco em relação ao Ads:

1. **Liste as páginas** com dimensão `page` e filtre as de blog. Quantas das
   publicadas sequer aparecem? No SVD eram 13 de 34.
2. **Cruze `query` + `page`** para saber qual busca cai em qual post. Revela post
   rankeando para termo que não era o alvo.
3. **Aplique os dois filtros** da seção 2.
4. **Agrupe variantes** por intenção antes de contar volume.
5. **Cruze com o GA4** — impressão sem clique é problema de title; clique com
   permanência baixa é problema de conteúdo. São diagnósticos diferentes.

## 5. Padrão de qualidade do post

O que o blog-bot já acerta e deve ser mantido:

- 900–1.100 palavras, 8–10 subtítulos;
- `answerBox` no topo (resposta direta, para AEO) e TL;DR em 3 bullets;
- FAQ com JSON-LD `FAQPage`;
- JSON-LD `BlogPosting` com `datePublished` em BRT explícito (`-03:00`);
- links internos ("Leia também") e capa própria;
- CTAs sutis, no máximo 2 menções ao serviço por post.

O que faltava e passa a ser obrigatório:

- **o termo de busca alvo no title, no H1 e no primeiro parágrafo** — o post do
  SVD sobre governança não continha "multinível" em lugar nenhum;
- **title escrito para clique**, não para descrever o arquivo;
- **uma pauta = um grupo de variantes de busca**, definido antes de escrever.

## 6. Armadilhas

- **O GA4 não entrega termo de busca orgânica** desde 2011 ("not provided"). Só o
  Search Console tem. Sem ele, escolher pauta é adivinhar.
- **Search Console só coleta a partir da verificação** — não há retroativo. Cada
  dia sem configurar é um dia de dado perdido para sempre.
- **Posição média engana**: uma consulta na posição 3 com 0 clique parece boa no
  relatório e é um vazamento. Sempre olhe CTR junto.
- **Marca de cliente polui a análise.** No SVD, `medplant` tinha 566 impressões e
  `avig360` 189 — tráfego dos subdomínios, não demanda pelo produto. Filtre.
- **Não confundir volume com valor.** `mmn` puro tinha 1.165 impressões e CTR de
  0,3%: volume enorme, intenção nenhuma.

## 7. BFR Intelligence — o que fazer quando for a hora

Situação em 17/08/2026: **35 posts publicados, nenhum Search Console configurado.**
Estamos completamente cegos — não há como saber se algum traz alguém.

Sequência recomendada, sem pular etapa:

1. **Verificar a propriedade** em `search.google.com/search-console` por DNS
   (Cloudflare), que cobre o domínio inteiro.
2. **Habilitar a API** do Search Console no projeto GCP e **adicionar a conta de
   serviço** `svd-analytics@svd-analytics-2026.iam.gserviceaccount.com` como
   usuária restrita. Atenção: o projeto está na conta
   `andre@sistemavendadireta.com.br`, não na `andre.kernelpanic@gmail.com`.
3. **Esperar acumular** — o Search Console pode entregar histórico no momento da
   conexão, mas se a propriedade for nova, conte 2–3 semanas até dado utilizável.
4. **Rodar a auditoria** das seções 2 e 4 antes de publicar qualquer post novo.
5. **Só então** revisar a lista de temas em `lib/site-strategy.js`.

Hipótese a testar quando o dado chegar: a BFR herdou 28 posts do
FluxoInteligente, escritos para outro posicionamento. É provável que se repita o
padrão do SVD — bem escritos, bem estruturados, mirando vocabulário que ninguém
busca ("agentes corporativos", "governança de agentes"). O público real
provavelmente digita coisas mais simples: "automatizar atendimento com IA",
"chatbot para empresa", "agente de IA para WhatsApp".

**Não mexer antes de medir.** Reescrever 35 posts por intuição repetiria
exatamente o erro que este documento existe para evitar.

## 8. Rotina

**Mensal** — auditoria completa: páginas indexadas, o filtro de "rankeia e não
clica", novas buscas com volume, e revisão da fila de pautas.

**A cada publicação** — confirmar que o post tem um grupo de buscas alvo definido
antes de escrever, e que o termo aparece no title, H1 e primeiro parágrafo.

**Trimestral** — revisar títulos de posts antigos que subiram de posição desde a
publicação: o que hoje está em 5º pode virar prioridade 1.

---

*Baseado na auditoria do blog do SVD em 17/08/2026: 34 posts, 13 indexados,
144 impressões e 2 cliques em 90 dias, com posições entre 4 e 8.*
