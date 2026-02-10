# 10 – Catálogo de Certidões

Este documento define o **catálogo fixo de certidões** que o Emergency comercializa e como ele deve ser modelado no banco de dados.

---

## 1. Objetivo

- Centralizar todos os tipos de certidões em uma única tabela de catálogo.
- Permitir que o sistema:
  - Liste certidões por código, nome e órgão emissor.
  - Use o **preço base** como referência para calcular o valor dos pedidos.
- Substituir qualquer regra antiga de certidões (modelo atual de `Certidao`) por este catálogo.

---

## 2. Modelagem – Tabela `certificate_types`

Criar tabela `certificate_types` com:

- `id` – bigint, PK, auto-increment.
- `code` – string, **único**, identificação curta (ex.: `PGU`, `PGFN`, `CECT`).
- `name` – string, nome curto para exibição.
- `provider` – string, órgão / fonte principal (ex.: "Procuradoria Geral da União").
- `description` – text, nullable. Detalhes adicionais para UI ou uso interno.
- `base_price` – decimal(10,2), preço base da certidão.
- `is_active` – boolean, default `true`. Controle para ativar/desativar certidões sem apagar.
- `created_at`, `updated_at`.

### 2.1 Regras de uso

- Qualquer cálculo financeiro de pedido deve:
  - Copiar `base_price` para `order_items.unit_price` no momento da criação da linha de pedido;
  - Não depender do valor atual do catálogo para pedidos antigos.
- Certidões com `base_price = 0.00`:
  - Continuam disponíveis para seleção.
  - Geram itens com valor 0, mas contam para emissão/laudo.

---

## 3. Seeds iniciais

Criar um seeder para popular a tabela `certificate_types` com os registros abaixo.

| code        | name (sugestão)                                             | provider / descrição curta                                              | base_price |
|------------|--------------------------------------------------------------|-------------------------------------------------------------------------|-----------:|
| PGU        | Procuradoria Geral da União                                  | Procuradoria Geral da União                                            |   40.00 |
| PGFN       | CND Federal – Receita / PGFN                                 | Receita Federal / PGFN (CND Federal) – Nova                            |   60.00 |
| CECT       | CEAT – Ações Trabalhistas (eletrônica)                       | Certidão Eletrônica de Ações Trabalhistas                              |   40.00 |
| CEATPF     | CEAT – Ações Trabalhistas (processos físicos)                | Certidão Eletrônica de Ações Trabalhistas – Processos Físicos          |   40.00 |
| CNDT       | CNDT – TST                                                   | Tribunal / TST / CNDT                                                  |   40.00 |
| ISS        | Certidão Tributária Mobiliária – SP                          | Prefeitura de São Paulo – ISS                                          |   40.00 |
| SEFAZ      | CND – SEFAZ SP                                               | SEFAZ / SP / Certidão Negativa de Débitos                              |   40.00 |
| NEGATIVA I | Certidão de Débitos – IBAMA                                  | IBAMA / Certidão de Débitos                                            |   60.00 |
| EMBARGOS I | Certidão de Embargos – IBAMA                                 | IBAMA / Certidão de Embargos (Nada Consta)                             |   60.00 |
| CRF        | CRF – Regularidade do Empregador                             | Caixa / FGTS                                                            |   60.00 |
| CNPJ       | Comprovante de Inscrição CNPJ                                | Receita Federal / CNPJ                                                 |   40.00 |
| JUNTA SIMP | Certidão Simplificada – Junta Comercial SP                   | Junta Comercial / SP                                                   |   60.00 |
| JUNTA FICH | Ficha Cadastral Completa – Junta Comercial SP                | Junta Comercial / SP                                                   |   60.00 |
| DADOS CADA | Dados Cadastrais do Imóvel – SP                              | Prefeitura / SP / São Paulo / Dados Cadastrais do Imóvel               |    0.00 |
| IMOBILIARI | Certidão Tributária de IPTU – SP                             | Prefeitura / SP / São Paulo / IPTU                                     |   40.00 |
| MPT        | Certidão Negativa – MPT SP                                   | MPT / SP / Certidão Negativa de Feitos                                 |   40.00 |
| TJSP-1     | Falências / Concordatas / Recuperações – TJSP               | TJSP Falências, Concordatas e Recuperações                             |   40.00 |
| TJSP-4     | Distribuição Cível – TJSP (SAJ SGC)                          | TJSP Distribuição Cível em geral – SAJ SGC                             |   40.00 |
| TJSP-6     | Distribuição Ações Criminais – TJSP                          | TJSP Distribuição de Ações Criminais                                   |   40.00 |
| TJSP-7     | Execução Criminal – TJSP                                     | TJSP Certidão de Execução Criminal                                     |   40.00 |
| DIVIDA ATI | Dívida Ativa – São Paulo                                     | Prefeitura / SP / São Paulo / Dívida Ativa                             |    0.00 |

---

## 4. Integração com o restante do sistema

- Todos os módulos que trabalham com certidões (pedidos, wizard, cálculo de valores, etc.) devem usar `certificate_types` como fonte de verdade.
- O modelo legado `App\Models\Certidao` será descontinuado progressivamente (ver `60-integration-existing-system.md` para o plano de transição).
