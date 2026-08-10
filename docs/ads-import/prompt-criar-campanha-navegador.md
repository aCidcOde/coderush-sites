# Prompt — criar a campanha no Google Ads pelo navegador

Cole o conteúdo abaixo (a partir de "TAREFA") numa sessão do Claude com acesso ao Chrome,
já logado em ads.google.com com a conta `andre.kernelpanic@gmail.com`.

---

TAREFA: criar uma campanha de Pesquisa no Google Ads pela interface web.

## Contexto e regras de segurança (leia antes de agir)

- Conta: **SourceNET Tecnologia — ID 357-892-7161** (selecione essa conta no seletor do topo antes de qualquer coisa).
- **NÃO** altere, pause ou remova nenhuma campanha existente. Já existe uma campanha chamada
  "Sistema Venda Direta" na conta: **não mexa nela**. Apenas, ao final, me informe o status,
  o orçamento e quantos grupos/anúncios ativos ela tem.
- **NÃO** mexa em faturamento, formas de pagamento, usuários ou configurações da conta.
- Ao terminar, deixe a campanha nova **PAUSADA** e me apresente um resumo para revisão.
  Não ative nada sem eu pedir.
- Se qualquer tela pedir confirmação de gasto, cobrança ou aceite de termos: **pare e me pergunte**.
- Se algum campo não aceitar o valor indicado, não improvise: relate o campo e o erro.

## 1. Criar a campanha

Campanhas → botão **+** → Nova campanha →
**"Criar uma campanha sem meta"** (não escolher "Leads"/"Vendas") → tipo **Pesquisa** → Continuar.

- Nome: `SVD - Promocao 10 Anos`
- Estratégia de lances: **Cliques** (manual), marcando "Definir um limite máximo de custo por clique" = **6,00**
- Redes: **desmarcar** "Rede de Display" e **desmarcar** "Incluir parceiros de pesquisa do Google"
- Locais: **Brasil**
- Idiomas: **Português**
- Orçamento diário: **50,00**
- Data de término: **31/08/2026**
- Se aparecer "Ampliação de palavras-chave" / "Segmentação otimizada": **desativar**

## 2. Grupos de anúncios, palavras-chave e anúncios

Crie **3 grupos**. Em todos, o padrão de URL, caminho e textos do anúncio está na seção 3.

### Grupo 1 — `Sistema MMN`
```
"sistema mmn"
"software mmn"
"sistema para mmn"
"sistema marketing multinivel"
"software marketing multinivel"
"plataforma mmn"
[sistema de marketing multinivel]
[software para mmn]
```
URL final do anúncio:
`https://sistemavendadireta.com.br/oferta/?utm_source=google&utm_medium=cpc&utm_campaign=promo-10-anos&utm_content=mmn`

### Grupo 2 — `Venda Direta`
```
"sistema venda direta"
"sistema de venda direta"
"software venda direta"
"plataforma de venda direta"
"escritorio virtual mmn"
[software de venda direta]
```
URL final:
`https://sistemavendadireta.com.br/oferta/?utm_source=google&utm_medium=cpc&utm_campaign=promo-10-anos&utm_content=venda-direta`

### Grupo 3 — `Preco e Contratacao`
```
"quanto custa sistema mmn"
"preco software mmn"
"sistema mmn preco"
"contratar sistema venda direta"
"empresa de software para mmn"
```
URL final:
`https://sistemavendadireta.com.br/oferta/?utm_source=google&utm_medium=cpc&utm_campaign=promo-10-anos&utm_content=preco`

> As aspas indicam correspondência de **frase** e os colchetes, correspondência **exata**.
> Cole exatamente assim: o Google interpreta os símbolos automaticamente.

## 3. Anúncio responsivo (o mesmo nos 3 grupos)

**Caminho de exibição:** `oferta` / `10-anos`

**Títulos** (15, todos ≤30 caracteres):
```
Até 40% OFF na Instalação
Instalação por R$ 3.000
Sistema para Venda Direta
Software MMN Completo
Plano Binário e Unilevel
Escritório do Consultor
Mensalidade desde R$ 500
Loja Virtual Integrada
Comissões Automáticas
Migração do Sistema Atual
Clientes em 5 Países
Promoção 10 Anos SVD
Sua Marca e seu Domínio
Implantação Assistida por IA
Agende uma Demonstração
```
**Fixar na posição 1** (ícone de alfinete): os títulos "Até 40% OFF na Instalação" **e**
"Instalação por R$ 3.000" — os dois, para o Google alternar entre eles.

**Descrições** (4, todas ≤90 caracteres):
```
Plataforma completa: escritório virtual, rede binária e unilevel, loja e financeiro.
Promoção 10 Anos: R$ 3.500 em 2x ou R$ 3.000 à vista até 31/08. Mensalidade sob medida.
Rodando no Brasil, Paraguai e Bolívia. Multi-idioma, multimoeda e comissão por cargo.
Parametrizamos seu plano de negócio: binário, unilevel ou comissão por cargo.
```

## 4. Palavras-chave negativas (nível campanha)

Configurações da campanha → Palavras-chave negativas → adicionar (uma por linha):
```
gratis
gratuito
emprego
vaga
vagas
curso
como funciona
o que é
piramide
golpe
reclame aqui
download
crack
planilha
excel
hinode
mary kay
natura
herbalife
jeunesse
```

## 5. Recursos/extensões (nível campanha)

**Sitelinks** (4):
| Texto | URL |
|---|---|
| Cases reais | https://sistemavendadireta.com.br/cases/ |
| Simular minha operação | https://sistemavendadireta.com.br/oferta/#simulador |
| Planos e mensalidade | https://sistemavendadireta.com.br/oferta/#garantir |
| Falar com especialista | https://sistemavendadireta.com.br/oferta/#garantir |

**Frases de destaque:** `10 anos de mercado` · `Clientes em 5 países` ·
`Mensalidade desde R$ 500` · `Implantação assistida por IA`

**Chamada (telefone):** `11 99456-6726`

## 6. Verificações finais (relate o resultado de cada uma)

1. A campanha ficou **pausada**?
2. Em Metas → Conversões: existe a ação **`generate_lead`** e ela está como **ação principal**
   (usada para otimização de lances)? E **`whatsapp_click`** como **secundária**? Se estiverem
   diferentes, **apenas relate** — não altere.
3. Em Admin → Configurações da conta: a **marcação automática (auto-tagging)** está ativada?
   Se estiver desativada, **relate** (não ative sem confirmação).
4. Qual o status/orçamento/nº de grupos da campanha existente "Sistema Venda Direta"?
5. Há avisos de faturamento, verificação pendente ou anúncios reprovados na conta? Quais?

Ao final, me dê um resumo: campanha criada, grupos, quantidade de palavras-chave por grupo,
anúncios criados e o resultado dos 5 itens acima.
