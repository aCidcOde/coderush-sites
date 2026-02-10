# 30 – Pagamentos do Pedido

Este documento define como serão modelados e processados os pagamentos dos pedidos.

---

## 1. Objetivo

- Permitir que um **pedido** criado pelo wizard seja pago.
- Separar a responsabilidade de:
  - Pedidos (`orders`)
  - Pagamentos (`payments`)
- Preparar o sistema para suportar múltiplos meios de pagamento (saldo, PIX, cartão, boleto).

---

## 2. Modelagem – Tabela `payments`

Criar a tabela `payments`:

- `id` – bigint, PK.
- `order_id` – bigint, FK → `orders`.
- `user_id` – bigint, FK → `users` (quem iniciou o pagamento).
- `method` – string/enum:
  - `wallet`
  - `pix`
  - `credit_card`
  - `boleto`
  - (outros métodos futuros).
- `amount` – decimal(10,2), valor do pagamento.
- `status` – string/enum:
  - `pending`
  - `authorized`
  - `paid`
  - `canceled`
  - `failed`
- `gateway_transaction_id` – string, nullable (ID retornado por PSP/gateway).
- `metadata` – json, nullable (payload de retorno, logs).
- `created_at`, `updated_at`.

### 2.1 Regras de negócio

- Um pedido é considerado **pago** quando:
  - Soma dos `payments` com `status = 'paid'` é >= `orders.total_amount`.
- Em v1, pode-se assumir **um único pagamento por pedido** (para simplificar).
- Caso haja falha de pagamento:
  - `status = 'failed'` no payment.
  - `orders.status` permanece `awaiting_payment`.

---

## 3. Tela de Pagamento do Pedido

Após o usuário concluir o wizard (Etapa 3), ele é redirecionado para a tela de pagamento.

### 3.1 Layout sugerido

- **Cabeçalho:**
  - "Pagamento do Pedido #ID"
  - Status atual do pedido (ex.: `Aguardando pagamento`).
- **Resumo do pedido:**
  - Nome do sujeito.
  - Lista curta de certidões (ou link para detalhes).
  - Valor total ( `order.total_amount` ).
- **Seção "Pagar com saldo":**
  - Exibe saldo atual do usuário (se o módulo de carteira estiver ativo).
  - Se saldo >= total:
    - Botão "Pagar com saldo".
  - Caso contrário:
    - Mensagem "Saldo insuficiente".

- **Seção "Outros meios de pagamento":**
  - V1 pode ser apenas um stub com mensagem:
    - "Em breve: pagamento via PIX/cartão/boleto."
  - Ou já estruturar botões/abas sem implementar integração real.

---

## 4. Pagamento com Saldo (integração com carteira)

Quando o usuário clica em "Pagar com saldo":

1. Verificar:
   - `wallet.balance >= orders.total_amount`.
2. Criar `payment` com:
   - `method = 'wallet'`.
   - `amount = orders.total_amount`.
   - `status = 'paid'`.
3. Criar um lançamento de débito em `wallet_transactions` (ver `40-saldo-carteira.md`):
   - `type = 'debit'`, `source = 'order_payment'`.
4. Atualizar:
   - `wallet.balance` (se armazenado).
   - `orders.status` para `paid` ou `in_progress` (conforme regra de negócio: se o próximo passo já for iniciar a emissão das certidões).

---

## 5. Testes (alto nível)

1. **Pagamento bem-sucedido com saldo**
   - Dado um usuário com saldo suficiente e um pedido `awaiting_payment`,
   - Quando ele aciona "Pagar com saldo",
   - Então:
     - Deve existir um `payment` com `status = 'paid'` e `method = 'wallet'`,
     - O saldo do usuário é reduzido,
     - O `order.status` muda para `paid` (ou `in_progress`).

2. **Saldo insuficiente**
   - Dado um usuário com saldo < total do pedido,
   - Quando ele tenta pagar com saldo,
   - O sistema deve:
     - Impedir o pagamento,
     - Exibir mensagem de erro amigável.

3. **Pagamento parcial (futuro)**
   - Em versões futuras, permitir mais de um pagamento por pedido (ex.: parte saldo, parte cartão).
   - Regra: pedido só muda para `paid` quando a soma dos `payments.paid` >= `orders.total_amount`.
