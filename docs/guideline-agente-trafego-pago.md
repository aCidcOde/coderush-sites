# Guideline — Agente de monitoramento de tráfego pago

Manual operacional destilado da campanha **SVD - Promoção 10 Anos** (Google Ads,
agosto/2026). Cada regra aqui nasceu de um erro que custou dinheiro ou de um
diagnóstico que levou dias para aparecer. Documento vivo: toda leitura diária que
revelar algo novo entra aqui.

> Princípio que organiza tudo: **melhorar um pouco todo dia**. A campanha não é
> montada e esquecida — é lida, corrigida e registrada. O ganho vem do acúmulo.

---

## 1. Antes de ligar qualquer campanha

Checklist que teria evitado cinco dias de retrabalho:

| Verificação | Por quê |
|---|---|
| **Faturamento ativo na conta** | O Google deixa ativar campanha sem cartão válido e ela simplesmente não veicula. Não avisa em lugar nenhum óbvio. Foi o que travou tudo por semanas. |
| **Conversões ativas e principais** | Ação importada do GA4 chega com status `HIDDEN`. Nesse estado o Google não conta conversão nenhuma e a campanha roda cega. |
| **`primary_for_goal` não é editável por ação** | Quem decide o que entra na coluna "Conversões" é a meta da **categoria** (`customer_conversion_goal`), no nível da conta. |
| **Extensões criadas junto com a campanha** | Sem extensão a campanha entra no leilão em desvantagem. Elas não encarecem o clique. Criar depois custou 10% de parcela de impressões por dois dias. |
| **Rede de Display desmarcada** | Em campanha de Pesquisa, Display significa clique acidental em app. Uma campanha antiga queimou 84% da verba de um dia assim. |
| **Data de término definida** | Oferta com prazo precisa de freio de mão. Campanha não pode sobreviver à promoção que anuncia. |
| **Search Console conectado** | Sem ele, a escolha de palavra é palpite. Ver seção 4. |

## 2. Como ler os números — a ordem importa

Ler na ordem errada leva a conclusão errada. A sequência correta:

1. **Impressões** — está entrando em leilão? Se zero, o problema é elegibilidade,
   lance ou volume do nicho. Não adianta olhar mais nada.
2. **Parcela de impressões e o motivo da perda** — a métrica mais subestimada.
   Distingue os dois mundos:
   - perda por **orçamento** → falta verba, é decisão de investimento;
   - perda por **ranking** → o problema é lance, qualidade ou extensão.
   São diagnósticos opostos e a métrica bruta de gasto não os separa.
3. **Termos de busca** — o que a pessoa digitou de verdade. É aqui que mora quase
   todo o desperdício. Ver seção 3.
4. **CTR** — só faz sentido depois de saber que o tráfego é o certo. CTR alto em
   público errado é armadilha: parece bom e gasta igual.
5. **Custo por clique qualificado** — não o CPC médio. Divida o gasto pelos cliques
   que realmente tinham intenção de compra. Na SVD o CPC era R$ 5,28 e o custo por
   clique útil, ~R$ 18.
6. **Conversões** — por último, e só com amostra. Abaixo de ~15 cliques
   qualificados, zero lead não significa nada.

### Armadilha: a estratégia de lance manda mais que o lance

**Antes de concluir qualquer coisa sobre lance, leia
`campaign.target_impression_share`.** Sob `TARGET_IMPRESSION_SHARE` os lances por
palavra viram enfeite: o relatório continua exibindo
`effective_cpc_bid_micros` com o valor que você digitou, mas quem decide o leilão
é o teto da estratégia, no nível da campanha.

Isso custou semanas de diagnóstico errado. A leitura recorrente era "quase não
aparecemos e o concorrente aparece toda hora", e a hipótese natural era lance
baixo. A campanha estava configurada com **meta de 20% no topo e teto de R$ 6** —
e entregava 31% de topo. Ela já estava *acima* da meta que nós mesmos demos a
ela; o Google segurava por instrução, com 0,0% de perda por orçamento.

O sintoma que parecia confirmar folga de qualidade — `sistema mmn` com lance de
R$ 12 pagando R$ 1,87 — era o mesmo fato visto por outro ângulo: o teto de R$ 6
mandava, não o lance de R$ 12.

Meta e teto são coisas diferentes e andam juntos:

| Campo | O que diz | Sozinho |
|---|---|---|
| `location_fraction` | quanto **tentar** aparecer | sem teto, quer e não pode pagar |
| `cpc_bid_ceiling` | quanto pode **pagar** para conseguir | sem meta, pode pagar e não quer |

Subir um sem o outro não produz efeito. Use `estrategia.py --ver` antes de
qualquer análise de lance.

### Meta de parcela alta não cria demanda — só encarece a que existe

Em 12/09/2026 subi a meta de topo de 20% para 70% e o teto de R$ 6 para R$ 12,
porque a campanha estava entregando menos do que podia. O diagnóstico do freio
estava certo; a conclusão sobre o remédio, não.

| | Gasto | Cliques | CPC |
|---|---|---|---|
| 11 dias antes | R$ 89,97 | 24 | R$ 3,75 |
| 4 dias depois | R$ 168,63 | 23 | **R$ 7,33** |

**Praticamente o mesmo número de cliques por quase o dobro do dinheiro**, e zero
lead nos quatro dias.

O que faltou pesar: **o volume de busca é pequeno e fixo** — cerca de 35
impressões por dia no nicho inteiro. Parcela de impressão redistribui quem
aparece dentro de um bolo que não cresce. Ir de 30% para 55% de topo compra um
punhado de exposições marginais, e são justamente as mais caras: as que a gente
perdia por ser a oferta menos competitiva naquele leilão.

**Regra:** meta de parcela alta só se paga quando há volume não atendido. Antes
de subir, olhe o total de impressões elegíveis (`impressions / impression_share`).
Se o mercado inteiro tem dezenas de buscas por dia, o teto de crescimento é
baixo e o custo marginal sobe rápido — o dinheiro rende mais em palavra nova,
página melhor ou outro canal.

**Sintoma de que passou do ponto:** a perda migra de "ranking" para "orçamento"
sem que os cliques aumentem. Foi o que aconteceu em 14/09 — 50% de perda por
orçamento com os mesmos 6 cliques do dia anterior.

Revertido em 16/09 para meta 40% / teto R$ 7.

### Armadilha: dado do dia corrente é incompleto

Relatar número de "hoje" antes do fechamento gera correção no dia seguinte. Em
14/08 a leitura às 17h mostrou R$ 11,87 e o dia fechou em R$ 23,70 — o dobro.
**Analisar sempre o dia anterior fechado**; o dia corrente serve só para detectar
anomalia grosseira (zero impressão, gasto explodindo).

## 3. Termos de busca — o campo de batalha

Concentre 80% do tempo aqui.

### Frase vaza, exata não

Correspondência de frase hoje é elástica: casa "mesmo significado", não apenas a
sequência. Observado na prática:

- `sistema marketing multinivel` (frase) trouxe `marketing multinivel`,
  `empresas de marketing multinivel`, `mmn no brasil`, `multinivel moderno`;
- `sistema multinivel` (frase) trouxe **`mlm`** — o Google traduziu a sigla.

Consequência: em nicho de vocabulário ambíguo, cada palavra em frase eventualmente
pesca o público errado, e cada pescaria custa um clique cheio.

### Negativa exata preserva a palavra qualificada

Para barrar o termo genérico sem matar a versão que vende:

- negativar `marketing multinivel` **em frase** derrubaria também
  `sistema de marketing multinivel`, que é comprador;
- negativar `[marketing multinivel]` **em exata** mata só a busca literal.

Regra: **lixo óbvio** (curso, vaga, grátis, pirâmide) → frase.
**Termo genérico com versão qualificada irmã** → exata.

### Negativa não casa acento nem variação

`gratis` **não** bloqueia `grátis`. Cada forma precisa ser cadastrada. Vale o
oposto para palavra positiva, que casa variante próxima automaticamente — então
`sistema multinivel` já cobre `sistema multinível`.

### Bloqueie intenção, não termos

Tapar buraco individual é enxugar gelo: a cada dia surge uma variante nova. Quando
o padrão fica claro, negative o **cluster de intenção** inteiro.

Na SVD o padrão era: o vocabulário de MMN é dominado por quem quer **entrar** numa
rede, não por quem quer **comprar** software. Daí o cluster:
`recrutador`, `recrutamento`, `ganhar dinheiro`, `renda extra`, `quero entrar`,
`como entrar`, `trabalhar com`, `ser consultor`, `ser revendedor`.

Pergunta que separa os dois públicos: *quem digita isso quer comprar meu produto
ou quer ser meu cliente do meu cliente?*

## 4. Escolher palavra com dado, não com intuição

O GA4 **não** entrega termo de busca orgânica desde 2011 ("not provided"). Quem
tem esse dado é o **Search Console** — e ele costuma ter meses de histórico já
acumulado no momento da conexão.

A leitura que importa cruza **volume** com **posição orgânica**:

| Situação | Ação | Raciocínio |
|---|---|---|
| Volume alto + posição **> 10** | **Comprar** | A demanda existe e o orgânico não entrega. É exatamente o buraco que anúncio existe para tapar. |
| Volume alto + posição **≤ 5** | **Avaliar** | Já ganhamos de graça; pagar canibaliza o próprio clique. Só vale se concorrente anunciar em cima. |
| Volume alto + **CTR pífio** | **Negativar** | Intenção errada: o Google exibe por proximidade de tema, a pessoa quer outra coisa. |

Isso revelou pontos cegos que a intuição não pegou: `sistema multinivel` tinha 77
impressões orgânicas e nem estava na campanha. E mostrou a maior oportunidade da
conta — `sistema mmn`, 176 impressões com a gente na 25ª posição.

Também confirmou decisões: `mmn` puro tinha 1.165 impressões com **0,3% de CTR**,
provando que negativar foi acerto.

**Lance no nível da palavra** é a ferramenta para essa assimetria: concentre verba
onde o orgânico não salva, não onde ele já entrega.

## 5. Quando mexer no quê

Ordem de alavancas, da mais barata para a mais cara:

1. **Negativas** — tiram desperdício sem custo nenhum. Sempre primeiro.
2. **Extensões** — melhoram posição sem encarecer o clique.
3. **Correspondência** — fechar de frase para exata corta vazamento na origem.
4. **Lance** — só depois que o tráfego já é o certo. Subir lance com vazamento
   aberto é comprar tráfego ruim mais caro.
5. **Texto do anúncio** — por último, e só com dado. Ver seção 6.

### Uma variável por vez (e quando quebrar a regra)

Mexer em três coisas no mesmo dia impede saber o que causou o resultado. Quebre a
regra conscientemente quando as mudanças vêm do mesmo diagnóstico e se reforçam —
e registre que quebrou. Mitigação: dê lance próprio à palavra que você quer
isolar, assim ela fica mensurável separadamente.

### Auditar o texto do anúncio é rotina, não arqueologia

Em 12/09/2026 os **9 anúncios da conta** — cinco deles no ar — anunciavam "até
31/08", prazo vencido havia 12 dias, enquanto o site vendia com prazo 30/09.
Ninguém tinha olhado desde a criação.

Prazo vencido não é só constrangimento: quem clica, compara com a página e
desiste — e o clique a gente paga igual. É desperdício que não aparece em nenhum
relatório de termo de busca, porque o tráfego estava *certo*.

`anuncios.py --auditar` varre data vencida, estouro de caractere e caixa alta.
Rodar sempre que a oferta mudar de prazo, e no fechamento de cada mês.

Para corrigir, `--trocar "de" "para"` usa `AdService.mutate_ads` com FieldMask,
preservando o histórico do anúncio. **Remover e recriar zera o aprendizado
acumulado** — nunca faça isso só para trocar uma data.

### Antes de ligar campanha parada há tempo

Campanha criada e nunca veiculada acumula defeito silencioso. Checklist do que
estava errado nas 4 do SVD, paradas desde 10/08:

- **extensões: zero** (a campanha ativa tinha 12). Entram direto no Ad Rank e não
  custam clique — subir sem elas repete a perda de 90% por ranking do primeiro dia;
- **texto com prazo vencido** (acima);
- **estratégia de lance defasada** em relação à campanha que já aprendeu.

Ao replicar extensão, **vincule o asset existente, não clone**. `asset` é
reaproveitável entre campanhas e consolida métrica; rodar `extensoes.py` de novo
cria cópias e fatia o histórico entre clones. Use `vincular-extensoes.py`.

## 6. O que NÃO fazer

- **Não pesquisar o próprio anúncio no Google.** Cada busca sem clique derruba o
  CTR, que define quanto você paga. Com volume baixo, meia dúzia de buscas estraga
  a média por semanas. Use **Visualização e diagnóstico de anúncios**.
- **Não trocar texto que está performando.** CTR alto significa que o texto
  convence. Trocar joga fora o único indicador bom e não há dado que diga qual
  texto novo seria melhor.
- **Não confundir "eficácia do anúncio" com fator de leilão.** Ela é métrica de
  diagnóstico e **não entra no Ad Rank** — o Google é explícito. Anúncio marcado
  como `POOR` pode ter CTR excelente.
- **Não ligar todas as campanhas de uma vez.** Em nicho de baixo volume, dividir o
  pouco tráfego em cinco frentes impede qualquer uma de acumular histórico.
- **Não subir lance para resolver perda por ranking sem antes checar extensões e
  qualidade.** Costuma ser o mais caro dos três.

## 7. Rotina

**Diária** — dia anterior fechado: impressões, parcela e motivo da perda, termos
de busca novos, custo por clique qualificado. Negativar o que apareceu de errado.

**Semanal** — relatório completo de termos (`negativas.py --termos`), revisão de
palavras sem impressão (candidatas a remoção ou lance), leitura do Search Console
para novas oportunidades.

**Sempre** — registrar no histórico **o que** foi feito, **por quê** e **o efeito
medido**. O "por quê" é o que se esquece primeiro e o que impede desfazer sem
querer uma decisão que funcionou.

## 8. Ferramentas

Em `automation/ads/`, todas idempotentes:

| Script | Função |
|---|---|
| `criar-campanhas.py` | Cria as campanhas pausadas. Tem guarda de política (caixa alta reprova RSA). |
| `preparar-lancamento.py` | Saneia conversões e remove campanha antiga. |
| `ligar-campanha.py` | Liga/pausa campanha + grupos + anúncios de uma vez. `--status`, `--fim`. |
| `extensoes.py` | Sitelinks, frases de destaque e telefone. |
| `negativas.py` | `--termos` lista buscas reais; `--add` (frase) e `--exatas` (exata). |
| `palavras.py` | `--ver`, `--adicionar`, `--trocar` (correspondência), `--lance-de`. |
| `lance.py` | Lance por grupo, com parcela de impressões lado a lado. Teto de segurança. |
| `search-console.py` | Classifica consultas em comprar / avaliar / ignorar. |
| `renovar-token.py` | OAuth do Ads. Ver armadilha abaixo. |
| `_conta.py` | Credencial compartilhada e `--conta=<apelido>`. Evita ID cravado no topo de cada script. |
| `estrategia.py` | Meta e teto de parcela de impressão. **Leia antes de analisar lance.** |
| `anuncios.py` | `--auditar` (data vencida, limite, caixa alta) e `--trocar` preservando histórico. |
| `vincular-extensoes.py` | Replica assets de uma campanha em outras sem clonar. |
| `conversoes-bfr.py` | Ações de conversão da BFR e o `send_to` da tag AW. |
| `criar-campanha-bfr.py` | Primeira campanha da BFR. Valida RSA antes de enviar. |

### Armadilhas técnicas da API

- **`protobuf_helpers.field_mask(None, pb)` ignora campos com valor padrão.**
  `primary_for_goal=False` e `biddable=False` eram silenciosamente descartados do
  update — a API respondia sucesso e nada mudava. Use máscara explícita.
- **`REMOVED` não pode ser gravado via update**; exige a operação `remove`.
- **`match_type` não é editável**: remove e recria, o que zera o histórico da
  palavra. Aceitável quando o histórico é de tráfego errado.
- **Caixa alta reprova o anúncio inteiro** (`policy topic CAPITALIZATION`).
  Siglas passam; palavra inteira em maiúscula, não. `OFF` derrubou um RSA.
- **`campaign.primary_status` fica defasado** por até 24h após ativar. O campo
  autoritativo é `serving_status`.
- **Refresh token expira em 7 dias** enquanto o app OAuth estiver em modo "Teste"
  no Cloud Console. Publicar o app resolve em definitivo.
- **Keyword Planner exige acesso Standard**; token Basic recebe `PERMISSION_DENIED`.
  Em 12/09/2026 o token ainda estava em *explorer access* — sem Planner e sem
  Search Console na BFR, palavra nova é hipótese, e a compensação é negativa
  grande na largada e leitura de termos em 48h, não no fim do teste.
- **`contains_eu_political_advertising` é obrigatório na v25** ao criar campanha.
  Omitir devolve `The required field was not present` apontando para o campo.
- **Criação de campanha falha *depois* do orçamento.** O orçamento é um mutate
  separado e anterior; se a campanha quebrar, ele fica órfão e uma nova execução
  cria outro. Reaproveite por nome.
- **`campaign.start_date`/`end_date` não existem na v25** — são
  `start_date_time`/`end_date_time`.
- **Filtrar por `campaign.name` exige `campaign.name` no SELECT** (GAQL), senão
  `EXPECTED_REFERENCED_FIELD_IN_SELECT_CLAUSE`.
- **`segments.conversion_action_name` não convive com `metrics.cost_micros`** na
  mesma consulta.

### Conta compartilhada: o que é de conta e o que é de campanha

A `3578927161` hospeda SVD, BFR, Elibell e kernelpanic. Isso muda o alcance de
algumas mudanças:

- **ação de conversão é recurso de conta** — criar uma para a BFR aparece no
  relatório de todos. Hoje é tolerável porque nenhuma campanha usa lance por
  conversão (todas em `TARGET_IMPRESSION_SHARE`), então misturar não envenena
  algoritmo nenhum; exige apenas segmentar por nome na leitura. **No dia em que
  alguma campanha migrar para Maximize Conversions, isto vira problema real e a
  BFR precisa de conta própria.** Prefixe o nome da ação com a marca;
- **extensão de conta (`customer_asset`) vaza entre marcas** — sitelink do SVD
  apareceria em anúncio de oficina mecânica. Vincule sempre por campanha.

### Sitelink: query antes do fragmento

`https://site.com/#contato?utm_source=...` joga tudo para dentro do fragmento —
`location.search` fica vazio e o `gclid` nunca chega ao rastreamento. A forma
correta é `https://site.com/?utm_source=...#contato`.

---

*Última atualização: 12/09/2026 — 6 campanhas ativas (5 SVD + 1 BFR), R$ 101/dia
nominal. Marco: descoberto que a meta de parcela de impressão, não o lance, era o
freio da visibilidade.*
