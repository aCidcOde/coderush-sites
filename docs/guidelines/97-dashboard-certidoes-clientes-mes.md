# 97 - Dashboard admin: certidoes mais emitidas e clientes do mes

Este guideline registra os indicadores adicionados ao dashboard admin (`/backend`).

---

## 1. Objetivo

- Exibir um grafico de certidoes mais emitidas no mes atual.
- Mostrar ao lado os clientes com mais pedidos no mes atual.

---

## 2. Regras

- Periodo: mes atual, baseado em `created_at`.
- Certidoes: contar `order_items` por certidao.
- Clientes: contar pedidos por usuario (exclui admins).
- Lista limitada aos 5 primeiros resultados.

---

## 3. Criterios de aceitacao

- Dashboard admin mostra o grafico com as certidoes mais emitidas.
- Dashboard admin mostra os clientes com mais pedidos no mes atual.
- Quando nao houver dados no mes, exibir estado vazio informativo.
