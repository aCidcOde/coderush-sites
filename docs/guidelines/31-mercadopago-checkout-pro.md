# 31 – Mercado Pago (Checkout Pro)

Este guideline define as regras de negócio e o fluxo para pagamento via Mercado Pago usando Checkout Pro, com base no SDK PHP oficial.

Referências:
- SDK PHP: https://github.com/mercadopago/sdk-php
- Docs Checkout Pro: https://www.mercadopago.com.br/developers/pt/docs/checkout-pro/landing

---

## 1. Objetivo

- Habilitar pagamento de pedidos via Mercado Pago (Checkout Pro), com suporte a PIX, cartão e boleto no mesmo fluxo.
- Manter a modelagem de `payments` definida em `30-pagamentos-pedido.md`.
- Garantir rastreabilidade por pedido, com atualização de status via retorno do usuário e notificações do Mercado Pago.

---

## 2. Fluxo principal (alto nível)

1. Usuário escolhe Mercado Pago na tela de pagamento do pedido.
2. Sistema cria um `payment` com `status = 'pending'`.
3. Sistema cria uma **preference** no Mercado Pago e recebe o `init_point`.
4. Usuário é redirecionado para o Checkout Pro.
5. Mercado Pago envia notificações (webhook) sobre o status do pagamento.
6. Sistema atualiza `payments` e `orders` conforme o status recebido.

---

## 3. Entidades e campos envolvidos

### 3.1 `payments`

- `method`:
  - `mercadopago` (canal de pagamento externo).
  - O submétodo real (pix/cartão/boleto) deve ser persistido em `metadata`.
- `gateway_transaction_id`: salvar o `payment_id` retornado pelo Mercado Pago.
- `metadata` (json):
  - `preference_id`
  - `payment_id`
  - `payment_status`
  - `payment_status_detail`
  - `payment_method_id`
  - `payment_type_id`
  - `payer_email`
  - `raw_payload` (opcional, para auditoria)

### 3.2 `orders`

- `status` deve mudar para:
  - `paid` ou `in_progress` quando o pagamento for aprovado.
  - permanecer em `awaiting_payment` se `pending` ou `in_process`.
  - `canceled` ou `failed` quando o pagamento for rejeitado/estornado.

---

## 4. Regras de negócio

- Só permitir iniciar Mercado Pago se `orders.status = 'awaiting_payment'`.
- O valor do pagamento deve ser igual a `orders.total_amount`.
- O `external_reference` do Mercado Pago deve apontar para o `orders.id` (ou UUID).
- Sempre usar **idempotency key** na criação de preferência, para evitar duplicidade.
- Pagamento aprovado deve:
  - Atualizar `payments.status` para `paid`.
  - Atualizar `orders.status` para `paid`.
- Pagamento rejeitado/cancelado deve:
  - Atualizar `payments.status` para `failed` ou `canceled`.
  - Manter `orders.status = 'awaiting_payment'` (ou `canceled` se regra assim definir).
- Notificações devem ser tratadas como fonte de verdade para status final.
- Se o `amount` ou `external_reference` da notificação não bater, registrar erro e não aprovar o pedido.
- Parcelamento segue o que o Checkout Pro permitir no momento do pagamento.

---

## 5. Validações e consistência

- `payer.email` é obrigatório.
- `currency_id` sempre `BRL`.
- `items[]` deve refletir o pedido:
  - `title`: "Pedido #ID"
  - `quantity`: 1
  - `unit_price`: `orders.total_amount`
- Webhook deve validar autenticidade (assinatura/token) e rejeitar payload inválido.

---

## 6. Permissões e responsabilidades

- Usuário só pode pagar pedidos próprios.
- Admin pode visualizar pagamentos e seus status.
- Endpoint de webhook é público, mas protegido por validação de assinatura/token.

---

## 7. Casos de borda

- Usuário abandona o checkout (pagamento fica `pending`).
- Webhook duplicado: atualizar por idempotência e não criar novo payment.
- Pagamento aprovado com valor divergente (bloquear aprovação e alertar).
- Chargeback/estorno posterior: definir regra de reversão do pedido.
- Pagamento aprovado após pedido ter sido cancelado manualmente.

---

## 8. Critérios de aceitação

1. Ao escolher Mercado Pago, um `payment` com `status = 'pending'` é criado e o usuário é redirecionado para o `init_point`.
2. Quando o Mercado Pago retorna pagamento aprovado, o `payment` vira `paid` e o `order` muda para `paid`.
3. Quando o Mercado Pago retorna rejeição/cancelamento, o `payment` vira `failed` ou `canceled` e o pedido permanece aguardando pagamento.
4. Notificações duplicadas não criam novos registros e não mudam o status indevidamente.
5. O sistema valida `external_reference` e `amount` antes de confirmar o pagamento.

---

## 9. Observações de implementação (para destravar)

- O SDK PHP oficial usa `mercadopago/dx-php` (>= 3.x). Requer PHP 8.2+.
- Fluxo recomendado para Checkout Pro:
  - `MercadoPagoConfig::setAccessToken(...)`.
  - `PreferenceClient` para criar a preference e obter `init_point`.
- O Mercado Pago aceita `back_urls` para retorno do usuário e `notification_url` para webhooks.

---

## 10. Perguntas em aberto

- Qual política para chargeback/estorno (reverte pedido ou só marca payment)?
