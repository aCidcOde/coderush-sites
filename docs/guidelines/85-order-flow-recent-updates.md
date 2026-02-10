# 85 – Atualizações recentes do fluxo de pedidos

Este documento resume as mudanças recentes para manter o time alinhado com o fluxo atual de pedidos.

---

## 1. Fluxo de pedido (v2) – telas e rotas

- Telas separadas com layout do dashboard (Blade + Livewire):
  - `GET /orders/new` → `orders.create` (Clientes)
  - `GET /orders/{order}/certidoes` → `orders.certificates` (Certidões)
  - `GET /orders/{order}/revisao` → `orders.review` (Revisão)
  - `GET /orders/{order}/payment` → `orders.payment` (Pagamento)

---

## 2. Solicitações (Fase 1)

- Título do pedido é **obrigatório**.
- Dados do solicitante ficam **logo abaixo do título**:
  - `requester_name` (obrigatório)
  - `requester_email` (obrigatório)
  - `requester_phone` (obrigatório)
  - `requester_document` (opcional)
- Valores preenchem automaticamente com o usuário logado.
- Ao clicar em “Adicionar cliente”, validar título + solicitante e exibir aviso quando faltar.

---

## 3. UF + Cidade (IBGE)

- UF é um select carregado via IBGE:
  - `https://servicodados.ibge.gov.br/api/v1/localidades/estados`
- Ao selecionar UF:
  - busca municípios em `https://servicodados.ibge.gov.br/api/v1/localidades/estados/{UF}/municipios`
  - habilita o select de cidades.
- Sem dependências externas (Select2 não é usado).

---

## 4. Certidões (Fase 2)

- Lista de clientes em cards clicáveis (nome + CPF/CNPJ + estado + cidade).
- Seleção de certidões é **por cliente**.

---

## 5. Revisão (Fase 3)

- Lista por cliente:
  - Cliente
  - Certidões (abaixo)
  - Total por cliente
- Total geral do pedido no final.
- Solicitante aparece no topo da revisão.

---

## 6. UI/UX

- Stepper do fluxo virou **botões 25%** com cor dourada, ícones e estado ativo.
  - Clientes → Certidões → Revisão → Pagamento
- Títulos das páginas seguem o padrão:
  - `Pedidos` (caps) → título → descrição.
- Ícones adicionados em menus, botões e headers principais.

---

## 7. Status de pedidos

- Badges de status agora têm **cores por status**.
- Componente único: `resources/views/components/order-status-badge.blade.php`.

---

## 8. Detalhe do pedido

- Mostra o **título do pedido** no topo.
- Blocos:
  1. Solicitante
  2. Clientes
  3. Itens por cliente
