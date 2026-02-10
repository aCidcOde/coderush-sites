# 95 – Cadastro Avançado de Certidões (API)

Este documento amplia o cadastro de certidões para incluir informações de integração com APIs externas, com base no dump de `api_certidoes`.

---

## 1. Objetivo

- Incluir campos técnicos de integração no cadastro de certidões.
- Manter `certificate_types` como fonte principal, evitando nova tabela paralela.
- Atualizar seeds sem quebrar pedidos existentes.

---

## 2. Modelagem (extensão de `certificate_types`)

Adicionar colunas:

- `endpoint` (text, nullable)
- `api_cost` (decimal(10,2), default 0)
- `requires_download` (boolean, default false)
- `endpoint_download` (text, nullable)
- `finalidade` (string, nullable)
- `login_provider` (tinyint, default 0)

Mapeamentos:

- `endpoint` ← `endpoint` (dump)
- `api_cost` ← `custo`
- `requires_download` ← `download` (0/1)
- `endpoint_download` ← `endpoint_download`
- `is_active` ← `status` (0/1)
- `finalidade` ← `finalidade`
- `login_provider` ← `login`
- `created_at` ← `data_criacao`

### 2.1 Login Provider

`login_provider` é um inteiro com o seguinte significado:

| Valor | Label |
|------:|-------|
| 0 | Sem login |
| 1 | GOV.BR |
| 2 | Prefeitura-SP |

---

## 3. Seeds

Criar o seeder `CertificateTypeApiSeeder` com os registros do dump.

Regras:

- Usar `upsert` por `code` para atualizar os novos campos sem apagar registros.
- Atualizar `name` conforme o dump.
- Não alterar `provider` e `base_price` nesse seeder.
- Se for necessário recriar do zero, somente com aprovação e confirmando que não há pedidos vinculados.

---

## 4. Admin – Formulário de Certidões

Adicionar os novos campos no formulário de criação/edição:

- Nome
- Código
- Endpoint
- Endpoint Download (habilitar quando `requires_download`)
- Status (Ativo/Inativo)
- Custo (api_cost)
- Finalidade
- Login (Sem login / GOV.BR / Prefeitura-SP)

Organização sugerida (base no layout):

- **Informações sobre a Certidão**: nome, código, endpoint, endpoint_download, status, custo, finalidade, login.
- **Dados Necessários**: multi-seleção de `required_data_fields`, com busca e lista de selecionados (pivot `certificate_type_required_data`).

---

## 5. Regras gerais

- Campos de ID auto-increment não aparecem no create.
- Após salvar edição, permanecer na tela e mostrar mensagem de sucesso.
