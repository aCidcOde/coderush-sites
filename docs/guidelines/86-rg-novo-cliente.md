# 86 – RG no card "Novo Cliente" (Abertura de Pedido)

Este documento define a regra de negocio para incluir o campo RG no card "Novo Cliente" durante a abertura do pedido.

---

## 1. Objetivo

- Capturar o RG do cliente no momento de abertura do pedido para enriquecer o cadastro e facilitar identificacao.

---

## 2. Entidade e campo

- Entidade: Cliente.
- Campo: RG.
- Fonte de verdade: cadastro do cliente associado ao pedido.

---

## 3. Regras de negocio

- O campo RG aparece no card "Novo Cliente" durante a abertura de pedido.
- O RG e opcional.
- O RG nao precisa ser unico; pode se repetir em clientes diferentes.
- O valor e normalizado e armazenado apenas com numeros.
- O RG informado fica salvo no cadastro do cliente e disponivel nas telas de consulta/edicao do cliente.

---

## 4. Validacoes

- A interface aceita entrada alfanumerica com ate 20 caracteres.
- A persistencia deve guardar apenas numeros (remover letras, pontos, hifen e espacos).
- Se o campo ficar em branco, o cadastro e permitido normalmente.

---

## 5. Criterios de aceitacao

- O campo RG aparece no card "Novo Cliente" na abertura de pedido.
- O RG pode ser deixado vazio sem bloquear o fluxo.
- O RG digitado e aceito ate 20 caracteres na interface.
- O RG e salvo somente com numeros.
- Ao editar o cliente, o RG aparece ja normalizado (somente numeros).

---

## 6. Casos de borda

- Entrada com letras e simbolos deve ser aceita na interface, mas salva apenas com numeros.
- RG vazio nao bloqueia a criacao do cliente.
- RG repetido em outro cliente nao gera bloqueio.
