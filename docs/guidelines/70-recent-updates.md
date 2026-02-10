# 70 – Anotações rápidas (últimas mudanças)

Referência breve para ajustes recentes já implementados no app.

## Pedidos
- Rota listagem: `GET /orders` (`orders.index`) com busca por ID ou nome do sujeito.
- Rota detalhe: `GET /orders/{order}` (`orders.show`) mostrando sujeito, itens, solicitante, links para pagamento e suporte.
- Menu superior inclui links para "Pedidos" e "Novo Pedido".
- Dashboard exibe últimos pedidos, total de pedidos recentes e saldo em carteira, com link "Detalhes" por linha.

## Suporte
- Contato: `GET /contato` (`support.contact`) usa componente Livewire; aceita query `?order=` para pré-preencher o pedido; usuários autenticados preenchem nome/e-mail automaticamente.
- Pagamento do pedido inclui botão para abrir suporte do pedido.

## Saldo / Carteira
- Rota saldo: `GET /saldo` (`wallet.balance`) mostra saldo e últimas transações.
- Recarga simples: `POST /saldo` (`wallet.add`) cria transação `credit/recharge` e incrementa `wallet.balance`.
- Pagamento com saldo: `POST /orders/{order}/payment/wallet` debita saldo, cria payment `wallet/paid` e transaction `debit/order_payment`.

## Landing / Marca
- Landing destaca o Gordon ao lado do título principal "Emita certidões fácil e receba no email"; bloco `Gordon-hero` com selo "Gordon · IA das certidões" em `resources/views/welcome.blade.php`.
- Favicons centralizados em `resources/views/partials/favicons.blade.php`, incluídos nos layouts (tabler, app, auth e welcome); arquivos em `public/images/icons/` e manifest em `public/images/icons/manifest.json`.

## Backend / Layout & Navegação
- Layout exclusivo do admin em `resources/views/layouts/backend.blade.php` (favicon no header, menu admin, botão de volta para o painel do cliente).
- Telas backend refatoradas para Blade + controllers (sem Volt): `backend.users.*`, `backend.certificate-types.*`, dashboard em `backend.dashboard`.
- Rotas backend permanecem em `routes/web.php` sob prefixo `/backend` com middlewares `auth`, `verified`, `can:access-backend`; controllers em `App\Http\Controllers\Backend\*`.

## Frontend ↔ Admin Toggle
- Usuários `is_admin` enxergam link “Admin” no menu do cliente (`resources/views/layouts/partials/dashboard-nav.blade.php`); layout admin tem link de volta ao painel do cliente.

## Rotas / Controllers
- `routes/web.php` agora só define rotas; regras foram movidas para controllers (`HomeController`, `DashboardController`, `OrdersController`, `WalletController`, e controllers backend).
- Padrão a seguir: lógica de negócio e queries ficam em controllers/serviços; `web.php` e `api.php` só registram rotas.
