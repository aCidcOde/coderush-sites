# Planeta Certidoes - Regras de Negocio

> Documento consolidado das regras de negocio do sistema. Para detalhes, consulte `docs/guidelines/`.

---

## 1. Visao Geral do Produto

**Produto:** Planeta Certidoes SaaS
**Objetivo:** Plataforma para emissao e gestao de certidoes brasileiras (PF, PJ e Imoveis).

### Fluxo Macro

1. Usuario cadastra / faz login
2. Cria **Novo Pedido** (wizard 3 etapas)
3. Seleciona certidoes do **Catalogo**
4. Revisa resumo e dados do solicitante
5. Paga (saldo carteira ou MercadoPago)
6. Acompanha status do pedido
7. Recebe certidoes emitidas

---

## 2. Entidades Principais

### 2.1 Subject (Cliente/Alvo da Certidao)

Representa quem sera certificado.

| Campo | Tipo | Regra |
|-------|------|-------|
| type | enum | `pf`, `pj`, `imovel` |
| name | string | Obrigatorio |
| document | string | CPF (PF), CNPJ (PJ), ou matricula (imovel) |
| rg | string | Opcional, usado em algumas certidoes |
| state | string | UF, obrigatorio |
| city | string | Obrigatorio |
| extra_data | json | birthdate, profile, extra info |

**Regras:**
- Um pedido pode ter **multiplos subjects** (clientes)
- Cada subject tem suas proprias certidoes selecionadas
- CNPJ deve buscar dados automaticamente via BrasilAPI

### 2.2 Order (Pedido)

| Campo | Tipo | Regra |
|-------|------|-------|
| title | string | Obrigatorio, identificador do pedido |
| user_id | FK | Usuario dono do pedido |
| status | enum | Ver estados abaixo |
| total_amount | decimal | Calculado da soma dos itens |
| requester_* | string | Dados do solicitante (nome, doc, email, phone) |

**Estados do Pedido:**

```
draft -> awaiting_payment -> paid -> in_progress -> completed
                 |                        |
                 v                        v
            canceled                   error
```

| Estado | Descricao |
|--------|-----------|
| `draft` | Rascunho, pedido em criacao |
| `awaiting_payment` | Aguardando pagamento |
| `paid` | Pago, aguardando processamento |
| `in_progress` | Em processamento pelo admin |
| `completed` | Certidoes emitidas |
| `canceled` | Cancelado |
| `error` | Erro no processamento |

### 2.3 OrderItem (Item do Pedido)

| Campo | Tipo | Regra |
|-------|------|-------|
| order_id | FK | Pedido pai |
| order_subject_id | FK | Cliente especifico |
| certificate_type_id | FK | Certidao do catalogo |
| unit_price | decimal | Copiado do catalogo no momento da criacao |
| quantity | int | Default 1 |
| status | enum | `pending`, `in_request`, `ready`, `error` |

**Regra critica:** `unit_price` eh copiado de `certificate_types.base_price` no momento da selecao. Pedidos antigos NAO sao afetados por mudancas de preco.

### 2.4 CertificateType (Catalogo)

| Campo | Tipo | Regra |
|-------|------|-------|
| code | string | Unico, identificador curto (ex: `PGFN`, `TJSP-1`) |
| name | string | Nome para exibicao |
| provider | string | Orgao emissor |
| base_price | decimal | Preco atual (pode ser 0) |
| is_active | boolean | Se aparece no catalogo |

**Regras:**
- Certidoes com `base_price = 0` sao validas
- Desativar (`is_active = false`) ao inves de deletar
- Nao editar `code` se ja houver pedidos vinculados

### 2.5 RequiredDataField (Dados Necessarios)

Campos dinamicos exigidos por cada tipo de certidao.

- Vinculados via `certificate_type_required_data`
- Pre-preenchidos com dados do Subject quando possiveis
- Armazenados em `order_item_required_data` por item

---

## 3. Fluxo de Pedido (3 Fases)

### Fase 1 - Solicitacoes (`/orders/new`)

**Objetivo:** Definir titulo e adicionar clientes.

- Campo "Titulo" obrigatorio
- Abas: Pessoa Fisica | Pessoa Juridica | Imovel
- Permite adicionar multiplos clientes
- Cada cliente vira um card na lista
- Botao "+ Adicionar" salva e limpa formulario
- Pode remover cliente da lista

**Validacoes:**
- Nome + documento + UF + cidade obrigatorios
- CNPJ valido busca dados automaticamente

### Fase 2 - Certidoes (`/orders/{order}/certidoes`)

**Objetivo:** Selecionar certidoes POR CLIENTE.

- Coluna esquerda: lista de clientes (cards)
- Coluna direita: certidoes filtradas por estado
- Abas de categoria (Federal, Estadual, Municipal, etc.)
- Botao "Desmarcar tudo" por cliente
- Selecao afeta SOMENTE cliente ativo

**Validacoes:**
- Pelo menos 1 certidao em pelo menos 1 cliente
- Nao avancar se todos clientes sem certidao

### Fase 3 - Revisao (`/orders/{order}/revisao`)

**Objetivo:** Revisar dados necessarios e confirmar.

- Bloco de dados do solicitante (requester_*)
- Accordion por cliente
  - Dentro: cards por certidao selecionada
  - Cada card: campos de dados necessarios
- Dados pre-preenchidos do cadastro do cliente
- Dados obrigatorios vazios devem ser completados

**Validacoes:**
- Todos dados obrigatorios preenchidos
- Nao avancar com pendencias
- Ao concluir: `status = awaiting_payment`

---

## 4. Pagamento

### Opcoes

1. **Saldo em Carteira** - Debito imediato se saldo suficiente
2. **MercadoPago** - Checkout Pro externo

### Fluxo Carteira

1. Verificar `wallet.balance >= order.total_amount`
2. Criar `wallet_transaction` tipo `debit`, source `order_payment`
3. Atualizar `wallet.balance`
4. Marcar `order.status = paid`

### Fluxo MercadoPago

1. Criar preference no MercadoPago
2. Redirecionar usuario pro checkout
3. Webhook processa notificacao
4. Confirmar pagamento -> `order.status = paid`

**Regra critica:** Saldo NUNCA pode ficar negativo.

---

## 5. Carteira (Wallet)

| Operacao | type | source | Efeito |
|----------|------|--------|--------|
| Adicionar saldo | credit | recharge | +balance |
| Pagar pedido | debit | order_payment | -balance |
| Estorno | credit | refund | +balance |
| Ajuste manual | adjustment | manual | +/- balance |

---

## 6. Backend Admin (`/backend`)

### Acesso

- Mesmo usuario (`users` table)
- Flag `is_admin = true`
- Gate `can:access-backend`
- Login separado em `/backend/login`

### Modulos V1

1. **Dashboard** - Metricas basicas
2. **Usuarios** - Listagem (somente leitura)
3. **Catalogo** - CRUD de `certificate_types`
4. **Pedidos** - Gestao e processamento

### Regras de UI Admin

- Edicao: salvar redireciona pra mesma tela + msg sucesso
- Campos largura `col-md-6`, inputs `w-100`
- ID auto-increment: so aparece na edicao (read-only)

---

## 7. Permissoes

| Role | Frontend | Backend | Acoes |
|------|----------|---------|-------|
| Cliente | Sim | Nao | Criar pedidos, pagar, ver historico |
| Admin | Sim | Sim | Tudo + processar pedidos + CRUD catalogo |

---

## 8. Referencias Detalhadas

Para implementacao, consultar:

| Arquivo | Conteudo |
|---------|----------|
| `docs/guidelines/00-master-guideline.md` | Visao geral e stack |
| `docs/guidelines/10-certidoes-catalogo.md` | Modelagem catalogo |
| `docs/guidelines/20-pedidos-wizard.md` | Wizard original |
| `docs/guidelines/25-pedidos-processo-v2.md` | Fluxo v2 multiplos clientes |
| `docs/guidelines/30-pagamentos-pedido.md` | Fluxo de pagamento |
| `docs/guidelines/40-saldo-carteira.md` | Sistema de carteira |
| `docs/guidelines/80-backend-admin-guidelines.md` | Painel admin |
| `docs/guidelines/90-dados-necessarios.md` | Campos dinamicos |
