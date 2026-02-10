Guideline: Backend user details (clientes) com abas e saldo admin

Contexto
- Tela alvo: /backend/users (lista) precisa abrir detalhes do cliente com abas.
- Layout: usar layouts/backend (visual atual do admin).
- MCP Laravel Boost: nao ha recursos MCP disponiveis nesta execucao; seguir estrutura do projeto.

Objetivo
- Criar pagina de detalhes do usuario no backend com abas:
  - Dados do cliente (edicao)
  - Pedidos (somente leitura)
  - Extrato (todas transacoes de saldo)
  - Saldo (admin pode adicionar/remover)
- Remover manipulacao de saldo do frontend do usuario (menu e formulario).

Decisoes registradas (para nao perguntar novamente)
- Campos editaveis em "Dados do cliente": name, email, phone, document.
- Pedidos: listar pedidos do cliente e permitir ver detalhes em pagina admin somente leitura.
- Extrato: listar todas as transacoes do wallet (credit/debit) com data, valor, descricao, fonte, admin (quando houver).
- Saldo: permitir credito e debito por admin; registrar admin_id e created_at. Debito nao pode gerar saldo negativo.
- Saldo do usuario nao pode ser manipulado no frontend: remover links do menu e ocultar/invalidar formulario.
- Validacao via Form Request (sem validacao inline).
- Eager loading nas listas para evitar N+1.

Implementacao planejada
- Rotas backend:
  - GET /backend/users/{user} -> UserController@show
  - PUT /backend/users/{user} -> UserController@update (dados cliente)
  - POST /backend/users/{user}/wallet -> BackendUserWalletController@store (credito/debito)
  - GET /backend/orders/{order} -> BackendOrderController@show (somente leitura)
- Views:
  - resources/views/backend/users/show.blade.php com tabs e formularios.
  - resources/views/backend/orders/show.blade.php (reuse de dados do pedido, sem acoes).
- Models/migrations:
  - Adicionar admin_id (nullable) em wallet_transactions + FK users.id.
- Controllers:
  - Usar Form Requests para update do usuario e ajuste de saldo.
  - Registrar admin_id quando credito/debito no backend.
- Frontend user:
  - Remover links para route('wallet.balance') no menu.
  - Remover formulario de adicionar saldo em resources/views/wallet/balance.blade.php ou bloquear POST para usuarios.

Testes
- Feature test para update de usuario no backend.
- Feature test para ajuste de saldo por admin (credit/debit e admin_id).
- Ajustar testes existentes se necessario.
- Rodar apenas testes afetados com php artisan test [arquivo].

Prompt para executar a alteracao (use no Codex)
"Implemente a tela de detalhes do cliente no backend com abas (dados, pedidos, extrato, saldo), permita editar dados do usuario, listar pedidos (somente leitura) com link para detalhe admin, mostrar extrato, permitir credito/debito registrando admin_id e created_at, e remova a manipulacao de saldo do frontend. Siga a guideline em docs/backend-user-details-guideline.md e rode os testes afetados."
