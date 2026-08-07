<!--
/*
[Modulo Documentacao Operacional — Campanha Google Ads /oferta/]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-02
Pacote pronto pra colar na interface do Google Ads (sem API — requer developer token).
Landing: https://www.sistemavendadireta.com.br/oferta/ (instalacao R$3.500 em 2x ou R$3.000 a vista, expira 31/08/2026).
*/
-->

# Campanha Google Ads — Promoção 10 Anos SVD

## Estrutura

- **Campanha**: `SVD - Instalacao 50OFF` — Rede de Pesquisa, somente Brasil, português
- **Orçamento sugerido**: R$ 40/dia pra validar (ajustar após 2 semanas de dado)
- **Lances**: começar em "Maximizar cliques" com teto de CPC R$ 6; migrar pra
  "Maximizar conversões" quando tiver ≥15 conversões `generate_lead` em 30 dias
- **Conversão**: importar do GA4 o key event `generate_lead` (Ferramentas → Conversões →
  Importar → Propriedades do Google Analytics 4). Secundária: `whatsapp_click`
- **Extensões**: sitelinks (Cases → `/cases/`, Blog → `/blog/`, IA para MMN →
  `/inteligencia-artificial/`), frase de destaque ("10 anos de mercado",
  "Clientes em 5 países", "Mensalidade desde R$ 500"), chamada 11 99456-6726

## URL final (todas os grupos)

```
https://www.sistemavendadireta.com.br/oferta/?utm_source=google&utm_medium=cpc&utm_campaign=instalacao-50off&utm_content={_grupo}
```

Trocar `{_grupo}` por `mmn`, `venda-direta` ou `oferta` conforme o grupo abaixo.

## Grupo 1 — Sistema MMN

Palavras-chave (frase/exata):
```
"sistema mmn"
"software mmn"
"sistema para mmn"
"sistema marketing multinivel"
"software marketing multinivel"
"plataforma mmn"
"sistema plano binario"
"sistema plano unilevel"
[sistema de marketing multinivel]
[software para mmn]
```

## Grupo 2 — Sistema Venda Direta

```
"sistema venda direta"
"sistema de venda direta"
"software venda direta"
"plataforma de venda direta"
"sistema para vendas diretas"
"escritorio virtual mmn"
"escritorio virtual venda direta"
[software de venda direta]
```

## Grupo 3 — Oferta / fundo de funil

```
"quanto custa sistema mmn"
"preco software mmn"
"sistema mmn preco"
"contratar sistema venda direta"
"empresa de software para mmn"
```

## Negativações (lista da campanha)

```
gratis, gratuito, emprego, vaga, vagas, curso, como funciona, o que é,
piramide, golpe, reclame aqui, download, crack, planilha, excel,
hinode, mary kay, natura, herbalife, jeunesse, catalogo
```

(Marcas de MMN entram como negativa porque quem busca a marca quer ser consultor
dela, não comprar sistema.)

## Anúncios RSA (um por grupo, mesmos ativos)

**Títulos** (≤30 chars — todos conferidos):

| # | Título | chars |
|---|---|---|
| 1 | Sistema para Venda Direta | 25 |
| 2 | Software MMN Completo | 21 |
| 3 | Até 40% OFF na Instalação | 25 |
| 4 | Plano Binário e Unilevel | 24 |
| 5 | Escritório Virtual Incluso | 26 |
| 6 | Mensalidade desde R$ 500 | 24 |
| 7 | Implantação Assistida por IA | 28 |
| 8 | Clientes em 5 Países | 20 |
| 9 | Loja Virtual Integrada | 22 |
| 10 | Comissões Automáticas | 21 |
| 11 | À Vista por R$ 3.000 | 20 |
| 12 | Migramos seu Sistema Atual | 26 |
| 13 | Promoção 10 Anos SVD | 20 |
| 14 | Sua Marca e seu Domínio | 23 |
| 15 | Orçamento pelo WhatsApp | 23 |

Fixar título 3 na posição 1 durante a promoção (é a oferta).

**Descrições** (≤90 chars):

| # | Descrição | chars |
|---|---|---|
| 1 | Plataforma completa: escritório virtual, rede binária e unilevel, loja e financeiro. | 84 |
| 2 | Promoção 10 Anos: R$ 3.500 em 2x ou R$ 3.000 à vista até 31/08. Mensalidade sob medida. | 87 |
| 3 | Clientes em 5 países: Brasil, Paraguai, Bolívia, EUA e Portugal. Multimoeda real. | 81 |
| 4 | Parametrizamos seu plano de negócio: binário, unilevel ou comissão por cargo. | 77 |

**Caminho de exibição**: `/oferta/50-off`

## Pós-lançamento (primeiras 2 semanas)

1. Termos de pesquisa: negativar todo termo de "emprego/renda/como entrar" que passar
2. Conferir no GA4 (Aquisição → aquisição de tráfego) se `google/cpc` está chegando na `/oferta/`
3. Se CTR < 3% no grupo, revisar títulos; se CTR ok e lead zero, o problema é a LP — me chamar
4. Encerrar a campanha em 31/08 ou renovar o prazo na LP (`$promoDeadline` em `oferta/index.php`)

## Limitações conhecidas

- Sem API do Ads (requer developer token aprovado em conta Manager/MCC) — este pacote é
  pra colar na interface. Com MCC + token futuramente, automação de lances e relatórios.
- O campo `origem=lp-oferta-instalacao` no e-mail de lead identifica a LP; o UTM identifica
  a campanha/grupo no GA4.

---

# Adendo (2026-08-02) — Expansão: aluguel, concorrentes e verticais

## Estratégia de fases

- **Fase A (agora)**: campanha principal + grupos abaixo. Orçamento total R$ 40–60/dia concentrado.
- **Fase B (gatilhos, 2–4 semanas)**: vertical com CPA < geral → campanha própria com orçamento
  dedicado; tráfego sem conversão → remarketing (público GA4 já coletando); termos de pesquisa
  novos → grupo novo.
- **Fase C (mapeado, aguardando dado)**: consignado/sacoleiras, afiliados white label, clube de
  assinatura, Paraguai em espanhol (precisa LP es), YouTube/PMax.
- **Paralelo (manual, gratuito)**: cadastrar nos diretórios Capterra Brasil, GetApp Brasil e
  comparasoftware.com.py — as páginas "melhores sistemas MMN" deles rankeiam acima de todos.

## Grupo 4 — Aluguel / sistema pronto (→ /oferta/)

```
"aluguel sistema mmn"
"alugar sistema mmn"
"sistema mmn pronto"
"sistema mmn mensalidade"
"plataforma mmn pronta"
```
utm_content=aluguel

## Grupo 5 — Concorrentes (→ /oferta/)

Lance nos termos de marca (permitido; NUNCA usar a marca no texto do anúncio):
```
"aliadus mmn"
"mmnweb"
"maxnivel sistema"
"m2n sistema"
"sistema mmn eloss"
"embraton mmn"
```
Anúncio comparativo genérico: "Compare Antes de Fechar" / "Operação em 2 Países" /
"Mensalidade Proporcional". utm_content=concorrentes

## Campanha vertical 1 — Suplementos (→ /oferta/suplementos/)

```
"sistema para distribuidora de suplementos"
"software distribuidora suplementos"
"sistema para venda de suplementos por consultores"
"sistema revenda suplementos"
"plataforma venda direta suplementos"
```
Títulos extras (≤30): "Sistema p/ Suplementos" (22) · "Rede de Consultores Pronta" (25) ·
"Case Real: Protech" (18)
utm_campaign=instalacao-50off-suplementos

## Campanha vertical 2 — Cosméticos (→ /oferta/cosmeticos/)

```
"sistema para revenda de cosmeticos"
"sistema para consultoras"
"software revenda por catalogo"
"sistema venda direta cosmeticos"
"plataforma para marca de cosmeticos revenda"
```
Títulos extras (≤30): "Sistema p/ Cosméticos" (21) · "Escritório da Consultora" (24) ·
"Case Internacional Real" (23)
utm_campaign=instalacao-50off-cosmeticos

## Leads por origem (campo do e-mail)

- `lp-oferta-instalacao` — LP geral
- `lp-oferta-suplementos` — vertical suplementos
- `lp-oferta-cosmeticos` — vertical cosméticos

---

# Adendo 2 (2026-08-04) — 5 LPs prontas e lancamento escalonado

## Ordem de lancamento

1. **Semana 1**: só `SVD - Promocao 10 Anos` → `/oferta/` com R$ 50/dia (baseline limpo)
2. **Semana 2+**: liberar as verticais UMA por vez conforme o dado (CTR ≥3% e CPA da geral
   como régua). Cada LP tem `origem` próprio no lead — o painel diz qual frente paga a conta.

| LP | origem do lead | utm_campaign |
|---|---|---|
| /oferta/ | lp-oferta-instalacao | instalacao-50off |
| /oferta/suplementos/ | lp-oferta-suplementos | instalacao-50off-suplementos |
| /oferta/cosmeticos/ | lp-oferta-cosmeticos | instalacao-50off-cosmeticos |
| /oferta/afiliados/ | lp-oferta-afiliados | instalacao-50off-afiliados |
| /oferta/parceiros/ | lp-oferta-parceiros | instalacao-50off-parceiros |

## Campanha vertical 3 — Afiliados/influenciadores (→ /oferta/afiliados/)

```
"plataforma de afiliados"
"sistema para programa de afiliados"
"software programa de afiliados"
"sistema de afiliados white label"
"plataforma de afiliados para influenciadores"
"sistema cupom de influenciador"
"programa de afiliados para minha empresa"
```
Títulos extras (≤30): "Sistema p/ Afiliados" (20) · "Link e Cupom por Afiliado" (24) ·
"Bônus Pagos Sem Planilha" (23) · "Afiliados há 10 Anos" (20)
Descrição: "Cadastre afiliados com link e cupom próprios. O sistema rastreia, calcula e paga os bônus." (89)

## Campanha vertical 4 — Parceiros/indicacoes (→ /oferta/parceiros/)

```
"sistema programa de parceiros"
"software programa de indicacao"
"sistema de indicacao de clientes"
"plataforma member get member"
"sistema para gestao de parceiros"
"programa de indicacao para empresa"
```
Títulos extras (≤30): "Sistema p/ Parceiros" (20) · "Indicação com Comissão" (22) ·
"Cupom por Indicador" (19) · "Comissões Automáticas" (21)
Descrição: "Parceiros com link e cupom próprios: o sistema rastreia indicações e paga as comissões." (86)

## Negativações extras (afiliados/parceiros)

`hotmart, monetizze, eduzz, kiwify, braip, "como ser afiliado", "ganhar dinheiro como afiliado"`
(quem busca isso quer SER afiliado de infoproduto, não montar programa proprio)

## Links identificados — social (organico e pago)

**Organico (compartilhar no perfil/pagina):**
- Facebook:  https://www.sistemavendadireta.com.br/oferta/?utm_source=facebook&utm_medium=social&utm_campaign=instalacao-50off
- LinkedIn:  https://www.sistemavendadireta.com.br/oferta/?utm_source=linkedin&utm_medium=social&utm_campaign=instalacao-50off
- WhatsApp/grupos: https://www.sistemavendadireta.com.br/oferta/?utm_source=whatsapp&utm_medium=social&utm_campaign=instalacao-50off

**Pago (usar nos anuncios quando patrocinar):**
- Facebook/Instagram Ads: .../oferta/?utm_source=facebook&utm_medium=paid_social&utm_campaign=instalacao-50off&utm_content={{ad.name}}
- LinkedIn Ads: .../oferta/?utm_source=linkedin&utm_medium=paid_social&utm_campaign=instalacao-50off

Regra: organico = utm_medium=social; pago = utm_medium=paid_social. Assim o painel/GA
separa alcance proprio de midia comprada. Preview: forcar re-scrape no
developers.facebook.com/tools/debug e linkedin.com/post-inspector se o banner nao aparecer.

---

# Passo a passo — criar a campanha geral na interface (sem API)

Use enquanto o developer token nao sai. ~15 minutos. Conta SourceNET (357-892-7161).

## 1. Nova campanha
`Campanhas` → botao **+** → **Nova campanha**
- Objetivo: **Criar uma campanha sem meta** (evita o assistente empurrar Performance Max)
- Tipo: **Pesquisa** → Continuar
- Nome: `SVD - Promocao 10 Anos`

## 2. Lances
- Estrategia: **Cliques** (manual) → marcar **"Definir um limite máximo de custo por clique"** → `R$ 6,00`
  *(quando houver 15+ conversoes/30d, trocar pra "Conversoes")*

## 3. Configuracoes da campanha
- Redes: **desmarcar** "Rede de Display" e **desmarcar** "Parceiros de pesquisa"
- Locais: **Brasil**
- Idiomas: **Português**
- Orcamento: **R$ 50,00/dia**
- Data de termino: **31/08/2026** (a promo expira junto)

## 4. Grupo de anuncios 1 — "Sistema MMN"
Colar as palavras-chave (uma por linha, com aspas/colchetes como estao):
```
"sistema mmn"
"software mmn"
"sistema para mmn"
"sistema marketing multinivel"
"software marketing multinivel"
"plataforma mmn"
"sistema plano binario"
"sistema plano unilevel"
[sistema de marketing multinivel]
[software para mmn]
```

## 5. Anuncio (RSA)
- URL final:
  `https://www.sistemavendadireta.com.br/oferta/?utm_source=google&utm_medium=cpc&utm_campaign=instalacao-50off&utm_content=mmn`
- Caminho: `oferta` / `10-anos`
- Titulos e descricoes: copiar da secao "Anuncios RSA" deste documento
- **Fixar** "Ate 40% OFF na Instalacao" na **posicao 1**

## 6. Antes de publicar
- **Palavras-chave negativas** (nivel campanha): colar a lista da secao "Negativacoes"
- **Extensoes**: sitelinks (Cases, Blog, IA para MMN), frases de destaque, chamada 11 99456-6726
- **Conversoes**: conferir em Metas → Conversoes que `generate_lead` esta como **acao principal**
  e `whatsapp_click` como **secundaria**

## 7. Depois de publicar (dia 1 e 2)
- `Palavras-chave` → **Termos de pesquisa**: negativar tudo que for emprego/renda/curso
- Conferir no painel (`/painel-leads/`) se aparece sessao `google / cpc`
- Os outros 4 grupos (Venda Direta, Fundo de Funil, Aluguel, Concorrentes) podem entrar
  na mesma campanha depois — ou esperar a API pra eu criar tudo de uma vez
