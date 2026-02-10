# 60 – Integração com o Sistema Existente

Este documento descreve como o novo modelo (pedidos, catálogo de certidões, pagamentos, saldo) deve ser integrado ao sistema atual, minimizando ruptura.

---

## 1. Situação atual

- Stack já configurada:
  - Laravel 12 + Fortify + Livewire/Volt + Flux UI + Tailwind 4 + Vite.
- Rotas/páginas existentes:
  - `/` → `welcome.blade.php` (landing de emissão automática de certidões).
  - `/dashboard` → exige auth/verified, lista certidões do usuário:
    - `auth()->user()->certidoes()->orderByDesc('data_inclusao')->orderByDesc('created_at')`.
- Modelo atual:
  - `App\Models\Certidao` (estrutura legada para certidões).
- Testes:
  - `DashboardTest`, `Settings/*`.

---

## 2. Objetivo da integração

- Introduzir o novo domínio:
  - `certificate_types`, `subjects`, `orders`, `order_items`, `payments`, `wallets`, `wallet_transactions`, `support_tickets`.
- Migrar gradualmente o uso do modelo antigo `Certidao` para o novo modelo de pedidos e catálogo.
- Manter o dashboard funcionando durante a transição.

---

## 3. Plano de transição (sugestão)

### Fase 1 – Introdução silenciosa do novo modelo

- Criar migrations e models para:
  - `certificate_types` (com seeder) – ver `10-certidoes-catalogo.md`.
  - `subjects`, `orders`, `order_items` – ver `20-pedidos-wizard.md`.
- Não alterar ainda o fluxo atual do dashboard.
- Criar uma rota protegida para o wizard "Novo Pedido" (ex.: `/pedidos/novo`) sem expor em produção (feature flag/guard simples se necessário).

### Fase 2 – Novo fluxo de pedidos funcional

- Implementar o wizard completo, do passo 1 ao 3.
- Implementar tela de pagamento básica, ao menos com suporte a pagamento por saldo.
- Implementar módulo de carteira (`wallets`, `wallet_transactions`) – ver `40-saldo-carteira.md`.
- Implementar listagem de pedidos do usuário autenticado (nova página "Pedidos").

### Fase 3 – Atualização do Dashboard

- Adaptar o `/dashboard` para:
  - Listar **pedidos** e/ou certidões emitidas a partir de `orders` e `order_items` em vez de `Certidao`.
  - Em uma primeira etapa, pode-se:
    - Mostrar uma nova seção "Meus Pedidos" usando o novo modelo.
    - Manter a seção antiga (baseada em `Certidao`) para histórico.
- Atualizar `DashboardTest` para cobrir o novo comportamento.

### Fase 4 – Desativação do modelo antigo

- Após garantia de que o novo fluxo está estável:
  - Marcar o modelo `App\Models\Certidao` como legado.
  - Remover referências a ele em controllers, views e testes.
  - Se necessário, criar scripts de migração de dados antigos para os novos modelos (não detalhado aqui).

---

## 4. Cuidados gerais

- **Backward compatibility:**
  - Evitar alterações em `/` e `/dashboard` que quebrem testes existentes antes de atualizar os testes.
- **Nomenclatura:**
  - Manter nomes em inglês para tabelas e modelos (`orders`, `payment`, etc.), deixando o português apenas para textos de interface.
- **Feature flags (opcional):**
  - Controlar a exibição do novo wizard / novos menus por env/flag simples, permitindo ativar/desativar durante o desenvolvimento.

---

## 5. Testes de integração

- Criar testes que cubram o fluxo completo:
  1. Usuário registra/login (via Fortify).
  2. Cria um novo pedido pelo wizard.
  3. Conclui o pedido (status `awaiting_payment`).
  4. Paga com saldo (quando o módulo estiver pronto).
  5. Verifica que o pedido aparece no dashboard.

- Garantir que:
  - Testes antigos de dashboard continuam passando ou são atualizados de forma controlada.
