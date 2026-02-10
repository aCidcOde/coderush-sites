# 35 – Emails do Sistema

Este documento descreve a estrutura e os gatilhos de emails transacionais do sistema.

---

## 1. Objetivo

- Garantir comunicação automática e consistente com clientes e time interno.
- Padronizar o layout das mensagens e a configuração de SMTP.

---

## 2. Eventos e destinatários

1. **Cadastro concluído**
   - Dispara após criar o usuário.
   - Destinatário: usuário.
   - Mailable: `WelcomeMail`.

2. **Troca de senha**
   - Dispara após salvar a nova senha nas configurações.
   - Destinatário: usuário.
   - Mailable: `PasswordChangedMail`.

3. **Recuperação de senha**
   - Dispara somente na solicitação (não enviar confirmação pós-reset).
   - Destinatário: usuário.
   - Notification: `PasswordResetRequested` (usa template customizado).

4. **Novo pedido**
   - Dispara quando o pedido muda para `awaiting_payment` no wizard.
   - Destinatários:
     - Usuário (requester_email ou email do usuário).
     - Time interno (config `MAIL_NEW_ORDER_INTERNAL_TO`).
   - Mailable: `OrderCreatedMail`.

5. **Pedido aprovado pelo financeiro**
   - Dispara no webhook do Mercado Pago quando o status resolve para `paid`.
   - Destinatário: usuário.
   - Mailable: `OrderPaidMail`.

6. **Pedido finalizado**
   - **Não implementado** (processo ainda não definido).

---

## 3. Template base

- Layout único em `resources/views/emails/layouts/base.blade.php`.
- Todos os emails devem usar `@extends('emails.layouts.base')`.
- Cores e tipografia alinhadas ao site (logo e cores primárias).
- Idioma: **PT-BR**.

Templates atuais:
- `resources/views/emails/welcome.blade.php`
- `resources/views/emails/password-changed.blade.php`
- `resources/views/emails/password-reset-requested.blade.php`
- `resources/views/emails/order-created.blade.php`
- `resources/views/emails/order-paid.blade.php`
- `resources/views/emails/contact-message.blade.php` (usa o mesmo layout)

---

## 4. Configuração (SMTP e headers)

Configuração SMTP via Gmail (TLS):

```
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=747a9e005@smtp-brevo.com
MAIL_PASSWORD=[]
MAIL_FROM_ADDRESS=emergency@sistemavendadireta.com.br
MAIL_FROM_NAME="${APP_NAME}"
MAIL_REPLY_TO_ADDRESS="sistema@emergency.com.br"
MAIL_REPLY_TO_NAME="${APP_NAME}"
MAIL_NEW_ORDER_INTERNAL_TO="andre@sistemavendadireta.com.br"
```

No `config/mail.php`:
- `reply_to` configurado globalmente.
- `new_order_internal_to` lido via env.

---

## 5. Pontos de integração (código)

- Cadastro:
  - `app/Actions/Fortify/CreateNewUser.php` envia `WelcomeMail`.
- Troca de senha:
  - `resources/views/livewire/settings/password.blade.php` envia `PasswordChangedMail`.
- Recuperação de senha:
  - `app/Models/User.php` sobrescreve `sendPasswordResetNotification`.
  - `app/Notifications/PasswordResetRequested.php` usa view customizada.
- Novo pedido:
  - `app/Livewire/Orders/NewOrderWizard.php` envia `OrderCreatedMail` ao finalizar.
- Pagamento aprovado:
  - `app/Http/Controllers/MercadoPagoController.php` envia `OrderPaidMail` no webhook.

---

## 6. Regras de negócio e validações

- Evitar envio duplicado:
  - Novo pedido só envia quando muda para `awaiting_payment`.
  - Pagamento aprovado só envia quando status muda para `paid`.
- Destinatário do cliente:
  - Usar `requester_email` quando disponível; fallback para `user->email`.
- Email interno:
  - Usar `MAIL_NEW_ORDER_INTERNAL_TO` se estiver definido.

---

## 7. Testes (referência)

Testes com `Mail::fake()`/`Notification::fake()`:

- `tests/Feature/Auth/RegistrationTest.php`
- `tests/Feature/Settings/PasswordUpdateTest.php`
- `tests/Feature/Auth/PasswordResetTest.php`
- `tests/Feature/NewOrderWizardTest.php`
- `tests/Feature/MercadoPagoCheckoutTest.php`

---

## 8. Pendências futuras

- Definir gatilho e conteúdo do email de **pedido finalizado**.
- Se necessário, mover envios para filas (queue) para alto volume.
