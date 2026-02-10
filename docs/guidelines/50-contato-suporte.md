# 50 – Contato e Suporte

Este documento descreve o módulo de **contato/suporte** do Emergency.

---

## 1. Objetivo

- Oferecer um canal interno para o usuário:
  - Tirar dúvidas sobre pedidos/certidões.
  - Reportar problemas técnicos.
  - Enviar sugestões.

---

## 2. Modelagem – Tabela `support_tickets`

- `id` – bigint, PK.
- `user_id` – bigint, FK → `users`, nullable (para futuros casos de contato público/sem login).
- `order_id` – bigint, FK → `orders`, nullable (se o contato estiver relacionado a um pedido).
- `subject` – string, assunto resumido.
- `name` – string, nome do remetente.
- `email` – string, e-mail do remetente.
- `phone` – string, nullable.
- `message` – text, corpo do contato.
- `status` – string/enum:
  - `open`
  - `in_progress`
  - `closed`
- `created_at`, `updated_at`.

---

## 3. Tela "Contato"

Pode ser:

- Uma rota aberta `/contato`, ou
- Uma página protegida dentro do dashboard (ex.: `/dashboard/contato`), dependendo da estratégia.

### 3.1 Formulário de contato

Campos:

- Assunto (`subject`) – select ou input:
  - "Dúvida sobre pedido"
  - "Erro no sistema"
  - "Sugestão"
  - "Outro"
- Nome (`name`) – obrigatório.
- E-mail (`email`) – obrigatório.
- Telefone (`phone`) – opcional.
- Nº do pedido (`order_id`) – opcional:
  - Se o usuário vier de um link em um pedido, esse campo pode ser pré-preenchido.
- Mensagem (`message`) – obrigatório.

Regras:

- Validar presença de `name`, `email`, `subject`, `message`.
- Se usuário estiver autenticado:
  - Preencher por padrão `name` e `email` com dados do usuário.
  - Vincular `user_id` no ticket.

---

## 4. Fluxo contextual (contato a partir de um pedido)

- Na tela de detalhes de pedido, incluir um botão:
  - "Falar com suporte sobre este pedido".
- Ao clicar:
  - Redirecionar para a página de contato com:
    - Campo nº do pedido (`order_id`) já preenchido.
    - Opcionalmente, `subject` sugerido ("Dúvida sobre pedido").

---

## 5. Testes (alto nível)

1. **Criação de ticket autenticado**
   - Usuário logado acessa a página de contato.
   - Envia formulário com dados válidos.
   - Deve ser criado um `support_ticket` com:
     - `user_id` preenchido,
     - `status = 'open'`.

2. **Criação de ticket a partir de pedido**
   - Usuário acessa um pedido.
   - Clica em "Falar com suporte".
   - Abre página com `order_id` já setado.
   - Ao enviar, ticket deve vir vinculado ao `order_id`.

3. **Validações**
   - Envio sem `name` ou `email` ou `message` deve falhar com mensagens amigáveis.
