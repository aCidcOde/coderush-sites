/*
[docs/PRD.md]
@Author: André Gomes ( @acidcode )
@since 2026-01-23
PRD consolidado do produto Emergency/Planeta Certidoes com requisitos e regras do negocio.
*/

# PRD - Emergency (Planeta Certidoes)

## 1. Visao geral
Produto SaaS para emissao e gestao de certidoes (PF, PJ e Imoveis), com fluxo guiado de pedido, pagamento e acompanhamento do status, incluindo painel administrativo e agente de atendimento (Gordon AI).

## 2. Objetivos
- Facilitar a emissao de certidoes com um fluxo claro e seguro.
- Reduzir erros de dados via revisao obrigatoria de dados necessarios.
- Permitir pagamentos por saldo e por gateway externo (Mercado Pago).
- Garantir rastreabilidade administrativa (auditoria) e operacao escalavel.

## 3. Publico-alvo e perfis
- Cliente final (PF/PJ/Imovel): cria pedidos, escolhe certidoes, revisa dados e paga.
- Admin: opera o catalogo, revisa usuarios, ajusta saldo e acompanha indicadores.
- Financeiro/Operacoes: acompanha pagamentos e status.
- Visitante: pode usar o agente Gordon e iniciar cadastro.

## 4. Fluxos principais (cliente)

### 4.1 Autenticacao e onboarding
- Cadastro, login, reset de senha e verificacao de e-mail (Fortify).
- Verificacao de e-mail obrigatoria para acessar areas protegidas.

### 4.2 Pedido v2 (telas separadas)
Rotas principais:
- `GET /orders/new` (Clientes)
- `GET /orders/{order}/certidoes` (Certidoes)
- `GET /orders/{order}/revisao` (Revisao)
- `GET /orders/{order}/payment` (Pagamento)

#### Fase 1 - Clientes
- Titulo do pedido obrigatorio.
- Dados do solicitante no topo: `requester_name`, `requester_email`, `requester_phone` (obrigatorios), `requester_document` (opcional).
- Multiplos clientes (PF/PJ/Imovel) no mesmo pedido.
- Campo RG no card "Novo Cliente": opcional, aceito com ate 20 caracteres, salvo apenas numeros.
- UF e cidade carregados via IBGE (UF -> municipios).
- CNPJ com preenchimento automatico via API publica (BrasilAPI ou equivalente), com TODO se indisponivel.

#### Fase 2 - Certidoes
- Selecao por cliente (cards na coluna esquerda; lista de certidoes na direita).
- Tabs estaticas: Todos, Federal, Estadual, Municipal.
- Busca por nome ou codigo sobrepoe o filtro da tab.
- "Desmarcar tudo" por cliente.
- Bloquear avancar se nenhum cliente tiver certidao selecionada.

#### Fase 3 - Revisao
- Revisao por cliente em accordion.
- Itens por cliente com dados necessarios pre-preenchidos.
- Nao permitir avancar se houver dados obrigatorios pendentes.
- Resumo com totais por cliente e total geral.

### 4.3 Pagamento
- Pagamento por saldo (wallet) e por Mercado Pago (Checkout Pro).
- Pedido considerado pago quando soma de `payments` com status `paid` >= `orders.total_amount`.
- Pagamento por saldo: debito na carteira, cria `payment` e atualiza status do pedido.

## 5. Modulos e requisitos funcionais

### 5.1 Catalogo de certidoes
- Tabela `certificate_types` como fonte de verdade.
- Campos obrigatorios: `code`, `name`, `provider`, `base_price`, `is_active`.
- Novos campos de integracao: `endpoint`, `api_cost`, `requires_download`, `endpoint_download`, `finalidade`, `login_provider`.
- Seeds iniciais obrigatorios com a lista de certidoes base.
- `base_price` copiado para `order_items.unit_price` na selecao.

### 5.2 Dados necessarios por certidao
- Catalogo `required_data_fields` (com `id` manual para compatibilidade legada).
- Pivot `certificate_type_required_data` (N:N).
- Para cada `order_item`, gerar `order_item_required_data` com pre-preenchimento quando possivel.
- Fonte de verdade do dado do item apos revisao: valor do item, nao do cadastro.

### 5.3 Pedido e itens
- `orders`: status `draft`, `awaiting_payment`, `paid`, `in_progress`, `completed`, `canceled`, `error`.
- `orders.title` obrigatorio.
- Relacao multipla `order_subjects` (pedido x clientes).
- `order_items` ligados ao `order_subject_id`.

### 5.4 Pagamentos
- `payments` com `method`, `amount`, `status`, `gateway_transaction_id`, `metadata`.
- Mercado Pago:
  - criar preference, redirecionar para Checkout Pro.
  - webhook como fonte de verdade.
  - validar `external_reference` e `amount`.
  - idempotencia na criacao.

### 5.5 Carteira (saldo)
- `wallets` e `wallet_transactions`.
- Nunca permitir saldo negativo (exceto ajustes permitidos).
- Cliente visualiza saldo e extrato.
- Credito/debito de saldo feito no backend admin (com `admin_id`).
- Remover/manter indisponivel a recarga pelo frontend do cliente.

### 5.6 Suporte
- `support_tickets` com status `open`, `in_progress`, `closed`.
- Formulario com assunto, nome, email, mensagem e opcional `order_id`.
- Botao contextual de suporte no pedido.

### 5.7 Emails transacionais
- Cadastro concluido, troca de senha, recuperacao de senha, novo pedido, pedido pago.
- Layout unico em `emails/layouts/base`.
- Envio ao cliente e ao time interno onde aplicavel.

### 5.8 Agente OpenAI (Gordon AI)
- Endpoint `POST /agente/mensagem` com rate limit.
- Resposta com `message`, `conversation_id`, `metadata`.
- Persistencia recomendada (conversas e mensagens).
- Mascarar documentos e evitar logar PII sensivel.

### 5.9 Dashboard cliente
- Mostrar ultimos pedidos, total de pedidos recentes e saldo.
- Manter rotas existentes `/` e `/dashboard` sem quebra.

### 5.10 Backend admin
- Prefixo `/backend`, login dedicado, gate `access-backend` via `is_admin`.
- Modulos:
  - Dashboard admin (metricas, certidoes mais emitidas e clientes do mes).
  - Usuarios (listagem e detalhe com abas).
  - Certidoes (CRUD) e Dados Necessarios (CRUD).
  - Logs de auditoria.
- Tela de usuario com abas:
  - Dados do cliente (edicao: name, email, phone, document).
  - Pedidos (somente leitura + link admin).
  - Extrato (todas transacoes).
  - Saldo (credito/debito por admin, com `admin_id`).

### 5.11 Auditoria
- Todas operacoes administrativas devem registrar before/after.
- Tela de consulta no backend.

## 6. Integracoes externas
- IBGE (UF e municipios).
- BrasilAPI (CNPJ) ou equivalente.
- Mercado Pago (Checkout Pro).
- OpenAI API (Gordon AI).

## 7. Regras de UX/UI
- Fluxo com breadcrumb e stepper acima do conteudo.
- Stepper com 4 etapas visuais: Clientes, Certidoes, Revisao, Pagamento.
- Botao de voltar: texto "Voltar" com icone a esquerda.
- Tabs e busca no card de certidoes conforme guideline.
- Badges de status com cores padronizadas.

## 8. Requisitos nao funcionais
- Seguranca: autorizacao por role (`is_admin`), rate limit no agente.
- Auditoria: registrar operacoes admin com before/after.
- Observabilidade: guardar status de pagamentos, webhooks e falhas.
- Performance: evitar N+1 com eager loading.

## 9. Dados e modelo (alto nivel)
Tabelas principais:
- `users`, `subjects`, `orders`, `order_subjects`, `order_items`.
- `certificate_types`, `required_data_fields`, `certificate_type_required_data`, `order_item_required_data`.
- `payments`, `wallets`, `wallet_transactions`.
- `support_tickets`.
- `agent_conversations`, `agent_messages` (se persistencia ativa).
- `audit_logs`.

## 10. Metricas de sucesso
- Taxa de conversao de pedido iniciado -> pago.
- Tempo medio para completar pedido.
- Reducao de retrabalho por dados incompletos.
- Volume de pedidos por cliente e por certidao.

## 11. Dependencias e riscos
- Disponibilidade de APIs externas (IBGE, BrasilAPI, Mercado Pago, OpenAI).
- Consistencia entre modelo legado e novo (transicao gradual).
- Qualidade de dados do catalogo e seeds.

## 12. Plano de fases (transicao)
1. Introducao do novo modelo e seeds sem quebrar o legado.
2. Fluxo de pedido v2 completo + pagamento por saldo.
3. Atualizacao do dashboard para pedidos.
4. Desativacao gradual do modelo legado.

## 13. Requisitos de engenharia e processo
- Manter rotas existentes criticas (`/` e `/dashboard`).
- Atualizar comentario de cabecalho em arquivos criados quando o comportamento mudar.
- Mensagens de commit sempre com tag (ex.: `[FEAT]`, `[FIX]`, `[CHORE]`).

## 14. Perguntas em aberto
- Politica de chargeback/estorno para Mercado Pago.
- Persistencia obrigatoria do historico do agente ou somente sessao.
- Qual data de corte para desativacao do modelo legado.
