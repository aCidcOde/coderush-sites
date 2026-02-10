# 25 – Processo de Pedido v2 (3 telas + múltiplos clientes)

Este documento define o **novo fluxo de pedido** em 3 telas independentes, com breadcrumb e seleção de certidões por cliente. Ele complementa e, quando necessário, **substitui** o fluxo descrito em `20-pedidos-wizard.md`.

---

## 1. Objetivo

- Separar o pedido em **3 telas independentes**, cada uma com responsabilidade única.
- Permitir **vários clientes/sujeitos** no mesmo pedido.
- Selecionar certidões **por cliente**.
- Exibir **breadcrumb** acima do fluxo, com navegação clara entre fases.
- Manter a experiência fiel aos layouts de referência.

---

## 2. Referências visuais

Imagens base para o layout e comportamento (ver pasta `docs/imagens/`):

- `docs/imagens/step-1-inicio.png`
- `docs/imagens/step-1-preenchido-1-cliente.png`
- `docs/imagens/step-2-selecionar cliente e certidoes.png`
- `docs/imagens/step 3 resumo e valor.png`

---

## 3. Estrutura de telas (separadas)

### 3.1 Breadcrumb e stepper (todas as fases)

Sempre acima do conteúdo:

- Breadcrumb: `Pedidos / Novo Pedido / {Fase Atual}`.
- Stepper visual com 3 etapas e indicador de etapa atual.
- Botões de navegação (voltar/avançar) alinhados à direita, como nas referências.

### 3.2 Roteamento sugerido

Separar as fases em **telas distintas**, mantendo estado por `order_id`.

- Fase 1: `GET /orders/new` → cria rascunho e coleta clientes.
- Fase 2: `GET /orders/{order}/certidoes` → seleção de certidões por cliente.
- Fase 3: `GET /orders/{order}/revisao` → revisão de dados necessários e resumo por cliente.
- Pagamento: `GET /orders/{order}/payment`.

> Observação: o formato exato das rotas pode ajustar ao padrão atual, mas as telas devem ser separadas.

---

## 4. Fase 1 – Solicitações

### 4.1 Objetivo

- Definir **título do pedido**.
- Adicionar **um ou mais clientes/sujeitos** antes da seleção de certidões.

### 4.2 Layout base (referência)

De acordo com `step-1-inicio.png` e `step-1-preenchido-1-cliente.png`:

- Seção "Identificador" com campo **Título**.
- Abas de tipo de cliente: **Pessoa Física**, **Pessoa Jurídica**, **Matrícula/Imóvel**.
- Formulário à esquerda, preview/resultado à direita.
- Botão **+ Adicionar** (aparece ativo quando formulário está válido).
- Lista de clientes já adicionados em cards na coluna direita.
- Estado vazio com instruções quando não há clientes adicionados.

### 4.3 Campos (mesmos já existentes)

Os campos seguem os definidos em `20-pedidos-wizard.md` para cada tipo (PF/PJ/Imóvel), reaproveitando nomes e validações já utilizadas.

### 4.4 Múltiplos clientes

- O usuário pode adicionar **mais de um cliente**.
- Ao clicar em **+ Adicionar**, o cliente é salvo na lista e o formulário limpa para novo cadastro.
- Permitir remover um cliente da lista (ícone de lixeira no card).

### 4.5 CNPJ com preenchimento automático

- Ao digitar CNPJ válido, o sistema deve **consultar uma API pública** para preencher o nome da empresa automaticamente.
- Sugestão de API: **BrasilAPI** (endpoint CNPJ) ou equivalente.
- Se a integração não estiver pronta, criar um **TODO** explícito no código e na UI (mensagem discreta).

---

## 5. Fase 2 – Certidões

### 5.1 Objetivo

- Selecionar certidões **para cada cliente**.

### 5.2 Layout base

Conforme `step-2-selecionar cliente e certidoes.png`:

- **Coluna esquerda**: lista de clientes (cards).
  - Seleção ativa destaca o card.
  - Exibe nome, documento, estado e perfil.
- **Coluna direita**: lista de certidões para o cliente selecionado.
  - Campo “Estado” preenchido com o estado do cliente.
  - Abas de categoria (Sugestões, Federal, Estadual, Municipal, Pesquisa, etc.) podem ser stub no v1.
  - Botão “Desmarcar tudo” por cliente.
- A seleção afeta **somente o cliente ativo**.

### 5.3 Regras de seleção

- Cada cliente possui sua própria lista de certidões escolhidas.
- Ao trocar de cliente:
  - A lista deve refletir as seleções anteriores daquele cliente.
- Não permitir avançar se **nenhum cliente** tiver certidão selecionada.

---

## 6. Fase 3 – Revisao

### 6.1 Objetivo

- Manter o bloco de solicitante como esta.
- Revisar dados necessarios por cliente e por servico selecionado.
- Exibir o resumo do pedido e permitir o avancar para pagamento.

### 6.2 Layout base

Conforme `step 3 resumo e valor.png`, com as seguintes regras:

- O bloco de solicitante permanece como esta hoje.
- A revisao por cliente deve ser um accordion.
  - Titulo do accordion: nome do cliente.
  - Ao expandir o cliente, listar os servicos selecionados.
  - Cada servico aparece em um card.
    - Card header: nome do servico/ certidao.
    - Card body: dados necessarios daquele servico.
- Os dados necessarios devem refletir o catalogo definido em `90-dados-necessarios.md` e o vinculo do servico.
- Dados ja presentes no cadastro do cliente devem vir pre-preenchidos.
- Dados obrigatorios nao preenchidos devem aparecer vazios para completar.

### 6.3 Regras de revisao

- A revisao e feita apenas pelo cliente, antes do pagamento.
- O pedido nao pode avancar para pagamento enquanto houver item com dados obrigatorios pendentes.
- A revisao sobrescreve os dados pre-preenchidos do cadastro do cliente.

---

## 7. Impacto de dados (ajustes necessários)

O fluxo com múltiplos clientes exige ajustes na modelagem definida em `20-pedidos-wizard.md`.

### 7.1 Novos/ajustados campos

- `orders.title` (string, obrigatório): título do pedido.

### 7.2 Relacionamento múltiplo pedido ↔ clientes

Substituir `orders.subject_id` por uma relação **1:N**:

- Nova tabela sugerida: `order_subjects`
  - `id`
  - `order_id`
  - `subject_id`
  - `position` (int, opcional para ordenação)
  - timestamps

### 7.3 Certidões por cliente

Cada seleção deve estar vinculada ao cliente específico:

- Ajuste em `order_items`:
  - adicionar `order_subject_id`
  - manter `order_id` para consultas agregadas do pedido

---

## 8. Regras gerais

- **Cada fase é uma tela separada** (não usar único wizard em uma página).
- Fluxo deve respeitar layouts de referência e manter consistência com o dashboard atual.
- UX com foco em clareza: preview de clientes e estados vazios devem ser informativos.

---

## 9. Camada tecnica (banco, relacoes e dados)

### 9.1 Tabelas existentes relevantes

- `subjects`
  - Campos: `id`, `type`, `name`, `document`, `rg`, `state`, `city`, `extra_data`, `created_at`, `updated_at`.
  - `extra_data` armazena dados adicionais (ex.: `birthdate`, `profile`, `extra`).
- `orders`
  - Campos: `id`, `user_id`, `subject_id` (nullable), `title`, `status`, `total_amount`, `currency`,
    `requester_name`, `requester_document`, `requester_email`, `requester_phone`, `meta`, timestamps.
- `order_subjects`
  - Campos: `order_id`, `subject_id`, `position`, timestamps.
- `order_items`
  - Campos: `order_id`, `order_subject_id`, `certificate_type_id`, `quantity`, `unit_price`, `total_price`, `status`, `meta`, timestamps.
- `certificate_types`
  - Catalogo de servicos/certidoes.
- `required_data_fields`
  - Catalogo de dados necessarios.
- `certificate_type_required_data`
  - Pivot entre servicos e dados necessarios.

### 9.2 Nova tabela necessaria (por item de pedido)

Criar uma tabela para armazenar os dados necessarios por item:

- `order_item_required_data`
  - `id`
  - `order_item_id` (FK `order_items.id`, cascade)
  - `required_data_field_id` (FK `required_data_fields.id`, cascade)
  - `value` (text, nullable)
  - `source` (string, nullable) -> `subject` quando vier do cadastro, `manual` quando editado.
  - timestamps
  - indice unico: (`order_item_id`, `required_data_field_id`)

### 9.3 Relacoes esperadas

- `OrderItem` -> `hasMany(OrderItemRequiredData)`
- `OrderItemRequiredData` -> `belongsTo(OrderItem)`
- `RequiredDataField` -> `hasMany(OrderItemRequiredData)`
- `OrderItemRequiredData` -> `belongsTo(RequiredDataField)`
- `CertificateType` -> `belongsToMany(RequiredDataField)` (ja existe)

### 9.4 Geracao automatica dos dados por item

Quando um item for criado (selecionando certidao para um cliente):
- Buscar os dados necessarios via `certificate_type_required_data`.
- Para cada dado necessario, criar um registro em `order_item_required_data`.
- Pre-preencher `value` quando houver correspondencia com dados do cliente.
- Se nao houver valor, deixar `value` nulo.

### 9.5 Mapeamento tecnico de pre-preenchimento

Dados do cliente (tela `/orders/new`) devem preencher automaticamente quando houver correspondencia:
- `Nome` (id 15) -> `subjects.name`
- `Numero do CPF` (id 32) -> `subjects.document` quando `subjects.type = 'pf'`
- `Numero do CNPJ` (id 31) -> `subjects.document` quando `subjects.type = 'pj'`
- `Numero do RG` (id 42) -> `subjects.rg`
- `Estado` (id 328) -> `subjects.state`
- `Cidade` -> `subjects.city` (se existir no catalogo, mapear pelo `descricao`)
- `Data de Nascimento` (id 11) -> `subjects.extra_data->birthdate`
- `Perfil` -> `subjects.extra_data->profile` (mapear pelo `descricao`)
- `Informacao extra / Inscricao / Matricula` -> `subjects.extra_data->extra` (mapear pelo `descricao`)

### 9.6 Regras tecnicas de fluxo

- Na revisao (`/orders/{order}/revisao`), carregar:
  - `order_subjects` + `subjects`
  - `order_items` por cliente + `certificate_type`
  - `order_item_required_data` + `required_data_field`
- Evitar N+1 carregando relacoes de forma antecipada.
- Bloquear edicao e avancar para pagamento quando `orders.status = 'paid'`.
- Remover dados necessarios ao remover um item do pedido.

### 9.7 Backfill para pedidos existentes

- Ao criar a nova tabela, criar um backfill para itens ja existentes:
  - Para cada `order_item`, gerar os registros de `order_item_required_data` com base no servico.
  - Respeitar o pre-preenchimento se houver dados do cliente.

---

## 10. Pendencias intencionais

- Integração real de pagamento (apenas link/placeholder por enquanto).
- API de CNPJ: definir provedor definitivo e política de fallback (TODO se necessário).
