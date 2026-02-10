# 92 - Revisao de Dados Necessarios

Esta guideline define as regras de negocio para revisar e completar os dados necessarios exigidos por cada certidao dentro de um pedido.

---

## 1. Objetivo

- Garantir que cada item de pedido possua todos os dados exigidos pelo servico.
- Reaproveitar dados coletados no cadastro do cliente (tela de criacao do pedido) como preenchimento padrao.
- Evitar inconsistencias antes de seguir para revisao final e pagamento.

---

## 2. Escopo e entidades

- Pedido
- Cliente (sujeito)
- Item do pedido (cliente x certidao/servico)
- Certidao/servico
- Dado necessario (catalogo definido em `90-dados-necessarios.md`)
- Revisao de dados necessarios por item

---

## 3. Regras de negocio

### 3.1 Geracao automatica por item

- Ao criar um item de pedido (quando uma certidao e selecionada para um cliente), o sistema deve gerar a lista de dados necessarios exigidos por aquele servico.
- Se a certidao nao exigir dados, a lista fica vazia e nao bloqueia o fluxo.
- Cada item tem sua propria lista, mesmo quando a mesma certidao aparece para clientes diferentes.

### 3.2 Preenchimento padrao (dados do cliente)

Quando um dado necessario existir no cadastro do cliente, ele deve ser pre-preenchido automaticamente.

Campos padrao capturados no cadastro do cliente:
- nome
- documento (CPF/CNPJ/inscricao, conforme o tipo)
- RG
- estado (UF)
- cidade
- data de nascimento
- perfil
- informacao extra

Mapeamento inicial (a confirmar):
- Nome -> nome do cliente
- Numero do CPF -> documento quando tipo PF
- Numero do CNPJ -> documento quando tipo PJ
- Numero do RG -> RG quando tipo PF
- Estado -> estado (UF)
- Cidade -> cidade
- Data de Nascimento -> data de nascimento (PF)
- Perfil -> perfil
- Informacao extra / Inscricao / Matricula -> informacao extra (ou documento, quando tipo "imovel")

### 3.3 Revisao e completude

- A revisao e feita apenas pelo cliente.
- O cliente revisa os dados por item e complementa os campos faltantes.
- Um item so pode ser considerado completo quando todos os dados obrigatorios estiverem preenchidos e validos.
- O cliente pode editar os dados necessarios enquanto o pedido nao estiver pago.
- Para certidoes que possuem **CPF e CNPJ como entradas simultaneas**, a tela deve exibir apenas os campos coerentes com o tipo do cliente:
  - Cliente PF: exibir **Nome** e **CPF** (ocultar CNPJ e Razao Social).
  - Cliente PJ: exibir **CNPJ** e **Razao Social** (ocultar Nome e CPF).
  - Os demais campos obrigatorios continuam visiveis normalmente.

### 3.4 Bloqueio de fluxo

- O pedido nao deve avancar para a etapa final (revisao/pagamento) enquanto houver item com dados obrigatorios pendentes.
- Mensagens devem indicar quais campos estao faltando e em qual item/cliente.

### 3.5 Consistencia e fonte de verdade

- Os valores preenchidos na revisao sao a fonte de verdade para o item, sobrescrevendo o pre-preenchimento.
- Alteracoes posteriores no cadastro do cliente nao devem sobrescrever automaticamente o que ja foi revisado.

### 3.6 Mudancas na configuracao de dados necessarios

- Nao existe mudanca apos o pedido pago.

---

## 4. Tela de revisao (fluxo e estrutura)

Etapas do fluxo:
1. Abertura do pedido (`/orders/new`): cadastrar solicitante e clientes.
2. Selecao de certidoes (`/orders/{order}/certidoes`): escolher certidoes por cliente. Nenhuma mudanca neste passo.
3. Revisao (`/orders/{order}/revisao`): revisar dados necessarios por item e cliente.

Regras da tela de revisao:
- O bloco de solicitante permanece como esta hoje.
- A revisao por cliente aparece em formato de accordion.
- O titulo do accordion e o nome do cliente.
- Ao expandir um cliente, os servicos (certidoes) aparecem listados por item:
  - Card header: nome do servico/ certidao selecionada para aquele cliente.
  - Card body: lista de dados necessarios daquele servico com seus valores.
- Os dados necessarios devem ser buscados do banco e apresentados por item.
- Os valores ja preenchidos no cadastro do cliente devem vir pre-preenchidos.
- Campos nao preenchidos, mas obrigatorios, devem aparecer vazios para o usuario completar.

---

## 5. Campos e dependencias (negocio)

Para cada item de pedido, registrar:
- quais dados sao exigidos (referencia ao catalogo)
- valor informado
- origem do valor (padrao do cadastro ou preenchido manualmente)
- status de completude (pendente / completo)
- quem revisou e quando (se aplicavel)

---

## 6. Fluxo principal (happy path)

1. Cliente cria pedido e cadastra o(s) cliente(s).
2. Cliente seleciona certidoes para cada cliente.
3. Sistema gera a revisao de dados necessarios por item e preenche o que ja existir no cadastro.
4. Cliente acessa a revisao e completa os dados faltantes em cada item.
5. Sistema valida completude e libera o avancar para pagamento.

---

## 7. Casos de borda

- Certidao sem dados necessarios: item segue sem pendencias.
- Cliente tipo PJ com certidao que exige CPF: campo fica vazio e deve ser preenchido manualmente (confirmar regra).
- Cliente tipo PF com certidao que exige CNPJ: campo fica vazio e deve ser preenchido manualmente (confirmar regra).
- Itens removidos do pedido devem remover suas pendencias.
- Alteracao de cliente apos criar itens: definir se revisoes existentes devem ou nao ser atualizadas.

---

## 8. Perguntas pendentes

1. Nao ha perguntas pendentes no momento.
