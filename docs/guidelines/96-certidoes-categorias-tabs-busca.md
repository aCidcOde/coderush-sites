# 96 - Certidoes: categorias e busca no card Documentos

Este guideline registra a regra do card Documentos no endpoint `/orders/{order}/certidoes`.

---

## 1. Objetivo

- Exibir certidoes por categoria no card Documentos do fluxo de pedidos.
- Permitir busca por nome da categoria.

---

## 2. Regras

- Tabs sao estaticas: Todos, Federal, Estadual, Municipal.
- Aba "Todos" lista todas as certidoes ativas.
- Clique na tab filtra os itens daquela categoria.
- Campo de busca filtra certidoes por nome ou codigo e sobrepoe o filtro da tab.
- Checkbox de selecao de certidoes mantem o comportamento atual.

---

## 3. Criterios de aceitacao

- Tabs sempre exibem Todos, Federal, Estadual e Municipal.
- Aba "Todos" exibe todas as certidoes ativas.
- Com busca preenchida, a lista mostra apenas certidoes que casam por nome ou codigo, ignorando a tab ativa.
- Com busca vazia, a tab ativa volta a controlar a lista.
