# 40 – Saldo e Carteira

Este documento define como será o módulo de **saldo** (carteira) do usuário no Emergency.

---

## 1. Objetivo

- Permitir que o usuário mantenha um **saldo em carteira** para pagar pedidos.
- Registrar todas as movimentações de saldo de forma auditável.

---

## 2. Modelagem

### 2.1 Tabela `wallets`

- `id` – bigint, PK.
- `user_id` – bigint, FK → `users`, único (uma carteira por usuário).
- `balance` – decimal(10,2), saldo atual.
- `created_at`, `updated_at`.

> Alternativamente, o saldo poderia ser calculado apenas pela soma de transações. Para simplificar o v1, manteremos uma coluna `balance` atualizada a cada transação.

### 2.2 Tabela `wallet_transactions`

Histórico de movimentações de saldo.

- `id` – bigint, PK.
- `wallet_id` – bigint, FK → `wallets`.
- `type` – string/enum:
  - `credit`
  - `debit`
  - `adjustment`
- `source` – string/enum:
  - `recharge` (adição de saldo)
  - `order_payment` (pagamento de pedido)
  - `refund` (estorno)
  - `manual` (lanço manual administrativo)
- `amount` – decimal(10,2), valor positivo (a direção é definida por `type`).
- `description` – string, nullable.
- `related_order_id` – bigint, nullable.
- `related_payment_id` – bigint, nullable.
- `created_at`, `updated_at`.

### 2.3 Regras de atualização de saldo

- Ao criar uma `wallet_transaction`:
  - Se `type = 'credit'` → `wallet.balance += amount`.
  - Se `type = 'debit'` → `wallet.balance -= amount`.
- Garantir que o saldo **nunca fique negativo**, exceto em casos de `adjustment` explicitamente permitidos.

---

## 3. Tela "Saldo"

Rota protegida por `auth` (ex.: `/saldo` ou dentro de `/dashboard`).

### Layout sugerido

- Card com saldo atual:
  - "Seu saldo atual: R$ X,XX".
- Botão "Adicionar saldo" (abre tela ou modal de recarga).
- Tabela com histórico:
  - Data/hora.
  - Tipo (crédito/débito/ajuste).
  - Origem (recharge, order_payment...).
  - Descrição.
  - Valor.
  - Saldo após operação (opcional).

---

## 4. Tela "Adicionar Saldo"

V1 pode ser bem simples (dependendo da etapa de implementação do pagamento externo).

### Layout

- Formulário com:
  - Valor a adicionar (R$).
  - Meio de pagamento (em v1, opcional / stub).
- Ao enviar:
  - Criar uma `wallet_transaction` de `type = 'credit'`, `source = 'recharge'`.
  - Atualizar saldo.

> Em versões futuras, a adição de saldo deve passar pelo fluxo de `payments` e/ou integração com gateway (PIX/cartão/boleto). Neste caso, a `wallet_transaction` só é criada quando o `payment` for confirmado.

---

## 5. Integração com pagamento de pedidos

- Ver `30-pagamentos-pedido.md` para detalhes.
- Resumo:
  - Ao pagar pedido com saldo:
    - Criar `wallet_transaction` `debit` com `source = 'order_payment'`.
    - Registrar `related_order_id` e `related_payment_id`.
    - Atualizar `wallet.balance`.

---

## 6. Testes (alto nível)

1. **Crédito de saldo**
   - Criar uma carteira para o usuário.
   - Criar uma transação de crédito.
   - Verificar que o saldo aumentou corretamente.

2. **Débito de saldo por pagamento**
   - Dado um saldo inicial suficiente,
   - Criar uma transação de débito vinculada a um pedido,
   - Verificar que o saldo final é o saldo inicial menos o valor debitado.

3. **Proteção contra saldo negativo**
   - Tentativa de débito maior que o saldo deve ser bloqueada (ou tratada com erro válido na camada de aplicação).
