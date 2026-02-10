# 20 – Pedidos e Wizard "Novo Pedido"

Este documento define a modelagem e o fluxo do **Novo Pedido** em 3 etapas, seguindo o layout já existente nos prints de referência.

---

## 1. Objetivo

- Permitir ao usuário criar um **pedido de certidões** para um sujeito (PF, PJ ou Imóvel).
- O fluxo é guiado por um wizard em 3 passos:
  1. Solicitações – dados do sujeito (PF/PJ/Imóvel).
  2. Certidões – seleção das certidões do catálogo.
  3. Configuração – informações complementares + resumo financeiro + conclusão (envio ao pagamento).

---

## 2. Modelagem de dados

### 2.1 Tabela `subjects`

Representa o “alvo” das certidões: pessoa física, pessoa jurídica ou imóvel.

Campos:

- `id` – bigint, PK.
- `type` – string/enum (`pf`, `pj`, `imovel`).
- `name` – string (nome da pessoa / identificação principal).
- `document` – string, nullable (CPF, CNPJ, inscrição, etc.).
- `state` – string, nullable (UF, ex.: `SP`).
- `city` – string, nullable.
- `extra_data` – json, nullable (RG, matrícula, inscrição municipal, etc.).
- `created_at`, `updated_at`.

### 2.2 Tabela `orders`

Representa o pedido como um todo.

- `id` – bigint, PK.
- `user_id` – bigint, FK → `users`.
- `subject_id` – bigint, FK → `subjects`.
- `status` – string/enum:
  - `draft`
  - `awaiting_payment`
  - `paid`
  - `in_progress`
  - `completed`
  - `canceled`
  - `error`
- `total_amount` – decimal(10,2), valor total das certidões no momento em que o pedido é concluído (preparado para pagamento).
- `currency` – string, default `BRL`.
- `requester_name` – string, nullable (dados do solicitante).
- `requester_document` – string, nullable.
- `requester_email` – string, nullable.
- `requester_phone` – string, nullable.
- `meta` – json, nullable (configurações extras, observações).
- `created_at`, `updated_at`.

### 2.3 Tabela `order_items`

Cada linha representa um tipo de certidão selecionado no pedido.

- `id` – bigint, PK.
- `order_id` – bigint, FK → `orders`.
- `certificate_type_id` – bigint, FK → `certificate_types`.
- `quantity` – int, default 1.
- `unit_price` – decimal(10,2), copiado de `certificate_types.base_price` no momento da seleção.
- `total_price` – decimal(10,2), `quantity * unit_price`.
- `status` – string/enum, opcional:
  - `pending`
  - `in_request`
  - `ready`
  - `error`
- `meta` – json, nullable (protocolo, URLs de download, etc.).
- `created_at`, `updated_at`.

---

## 3. Fluxo do Wizard "Novo Pedido"

O wizard deve ser construído com **Livewire v3 / Volt**, respeitando:

- Um componente principal de wizard, com controle do **step atual**.
- Cada etapa valida seus dados antes de permitir o avanço.

### 3.1 Etapa 1 – Solicitações (Dados do sujeito)

**Objetivo:** preencher ou selecionar os dados do sujeito para o qual as certidões serão emitidas.

Layout (inspirado no print):

- Coluna esquerda – Card com resumo:
  - Nome
  - CPF/CNPJ
  - Estado
  - Cidade
  - Perfil (ex.: proprietário)
- Coluna direita – Formulário:
  - Tipo de sujeito: PF / PJ / Imóvel.
  - Para PF:
    - Nome completo (obrigatório).
    - CPF (obrigatório).
    - Estado (UF, obrigatório).
    - Cidade (obrigatório).
    - Data de nascimento (opcional).
    - Perfil (proprietário / sócio / representante / etc.).
  - Para PJ:
    - Razão social.
    - CNPJ.
    - Estado, cidade.
    - Perfil (empresa alvo, sócia, etc.).
  - Para Imóvel:
    - Endereço completo.
    - Cidade, UF.
    - Matrícula, cartório (em `extra_data`).
- Botões:
  - "Voltar" (para sair do wizard ou ir para página anterior, se houver).
  - "Avançar" (vai para Etapa 2).

Regras:

- Não permitir avançar sem:
  - Nome + documento + UF + cidade (mínimo, ajustar por tipo).
- Ao clicar em "Avançar":
  - Criar ou atualizar `subject`.
  - Criar `order` com:
    - `user_id = auth()->id()`.
    - `subject_id` apontando para o sujeito criado/atualizado.
    - `status = 'draft'`.

---

### 3.2 Etapa 2 – Certidões

**Objetivo:** selecionar quais certidões do catálogo serão incluídas no pedido.

Layout (baseado no print “Documentos”):

- Topo:
  - Botão "DESMARCAR TUDO".
  - Abas (opcionais, podem ser implementadas em v2):
    - Sugestões
    - Federal
    - Estadual
    - Municipal
    - Pesquisa
    - Pesquisa de bens
    - Reputacional
- Alert box:
  - Texto informando que a responsabilidade pela escolha correta das certidões é do usuário.
- Campo **Estado**:
  - Preenchido por padrão com o estado do sujeito (`SP`, etc.).
- Lista de certidões:
  - V1 (mais simples): lista plana de `certificate_types` filtrados por contexto (ex.: estado = SP).
  - Para cada certidão:
    - Nome curto + órgão.
    - Preço unitário.
    - Checkbox para selecionar.
- Botões:
  - "Voltar" (retorna para Etapa 1).
  - "Avançar" (vai para Etapa 3).

Regras:

- Pelo menos **uma** certidão deve ser selecionada para permitir avanço.
- Quando uma certidão é marcada:
  - Criar ou atualizar `order_items` para o `order` em andamento.
  - `unit_price = certificate_types.base_price`.
  - `total_price = quantity * unit_price`.
- O valor parcial do pedido pode ser mostrado na UI (soma dos `total_price`).

---

### 3.3 Etapa 3 – Configuração e Resumo

**Objetivo:** revisar o resumo financeiro, preencher dados do solicitante e concluir o pedido (mudando-o para `awaiting_payment` e redirecionando para o pagamento).

Layout (baseado no segundo print):

- Divisão em duas colunas:

#### Coluna esquerda – Tipos de Certidão / Resumo

- Tabela "Tipos de Certidão":
  - Colunas: Tipo, Quantidade, Valor.
  - Linhas: agregação dos `order_items` (por categoria se desejado).
- Linha de **VALOR TOTAL**:
  - Soma de todos os `total_price` associados ao `order`.

#### Coluna direita – Informações Complementares + Dados do Solicitante

- Bloco "Informações Complementares":
  - Para cada pessoa ligada (no v1, apenas o `subject`):
    - Nome
    - CPF/CNPJ
    - Botão/alerta "PREENCHER INFORMAÇÕES" se faltar algo obrigatório.
- Bloco "Dados do solicitante":
  - Campos:
    - Nome (obrigatório).
    - CPF/CNPJ (opcional ou obrigatório conforme decisão).
    - E-mail (obrigatório).
    - Telefone (obrigatório ou recomendado).
  - Esses dados são salvos diretamente em `orders`:
    - `requester_name`, `requester_document`, `requester_email`, `requester_phone`.

- Botões:
  - "Voltar" (retorna para Etapa 2).
  - "Concluir" (finaliza o wizard).

Regras ao clicar em "Concluir":

1. Validar:
   - Pelo menos um `order_item` existente.
   - Campos obrigatórios do solicitante.
2. Calcular `total_amount`:
   - `orders.total_amount = soma(order_items.total_price)`.
3. Atualizar `status` do pedido para `awaiting_payment`.
4. Redirecionar para tela de pagamento do pedido (ver `30-pagamentos-pedido.md`).

---

## 4. Testes (alto nível)

Criar testes de feature para o wizard:

1. **Criar pedido completo**
   - Dado um usuário autenticado,
   - Quando ele preenche Etapa 1 com dados válidos,
   - Seleciona uma ou mais certidões na Etapa 2,
   - Preenche solicitante na Etapa 3 e conclui,
   - Então deve existir um `order` com:
     - `status = 'awaiting_payment'`,
     - `total_amount` calculado corretamente,
     - `order_items` relacionados às certidões selecionadas.

2. **Validações de navegação**
   - Não permite avançar Etapa 1 → 2 sem campos mínimos.
   - Não permite avançar Etapa 2 → 3 sem certidão selecionada.
   - Não permite concluir Etapa 3 sem `requester_name` e `requester_email`.

3. **Cálculo de valor**
   - Garantir que o total do pedido corresponde à soma do `unit_price` copiado de `certificate_types.base_price`, respeitando certidões de preço zero.
