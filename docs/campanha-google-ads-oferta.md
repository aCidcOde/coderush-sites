<!--
/*
[Modulo Documentacao Operacional — Campanha Google Ads /oferta/]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-02
Pacote pronto pra colar na interface do Google Ads (sem API — requer developer token).
Landing: https://www.sistemavendadireta.com.br/oferta/ (50% off instalacao, expira 31/08/2026).
*/
-->

# Campanha Google Ads — Oferta 50% Instalação SVD

## Estrutura

- **Campanha**: `SVD - Instalacao 50OFF` — Rede de Pesquisa, somente Brasil, português
- **Orçamento sugerido**: R$ 40/dia pra validar (ajustar após 2 semanas de dado)
- **Lances**: começar em "Maximizar cliques" com teto de CPC R$ 6; migrar pra
  "Maximizar conversões" quando tiver ≥15 conversões `generate_lead` em 30 dias
- **Conversão**: importar do GA4 o key event `generate_lead` (Ferramentas → Conversões →
  Importar → Propriedades do Google Analytics 4). Secundária: `whatsapp_click`
- **Extensões**: sitelinks (Cases → `/cases/`, Blog → `/blog/`, IA para MMN →
  `/inteligencia-artificial/`), frase de destaque ("25 anos de experiência",
  "Operação no Brasil e Paraguai", "Mensalidade desde R$ 500"), chamada 11 99456-6726

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
| 3 | 50% OFF na Instalação | 21 |
| 4 | Plano Binário e Unilevel | 24 |
| 5 | Escritório Virtual Incluso | 26 |
| 6 | Mensalidade desde R$ 500 | 24 |
| 7 | Implantação Assistida por IA | 28 |
| 8 | No Ar no Brasil e Paraguai | 26 |
| 9 | Loja Virtual Integrada | 22 |
| 10 | Comissões Automáticas | 21 |
| 11 | Instalação por R$ 2.500 | 23 |
| 12 | Migramos seu Sistema Atual | 26 |
| 13 | 25 Anos de Experiência | 22 |
| 14 | Sua Marca e seu Domínio | 23 |
| 15 | Orçamento pelo WhatsApp | 23 |

Fixar título 3 na posição 1 durante a promoção (é a oferta).

**Descrições** (≤90 chars):

| # | Descrição | chars |
|---|---|---|
| 1 | Plataforma completa: escritório virtual, rede binária e unilevel, loja e financeiro. | 84 |
| 2 | Instalação com 50% de desconto até 31/08. Mensalidade proporcional ao faturamento. | 82 |
| 3 | Operações reais no Brasil e Paraguai. Multi-idioma, multimoeda e comissão por cargo. | 84 |
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
