# 90 – Dados Necessários por Certidão

Este documento define a estrutura e o módulo administrativo para **dados necessários** que serão vinculados a cada certidão.

---

## 1. Objetivo

- Centralizar o catálogo de **dados necessários** em uma tabela própria.
- Permitir vincular **múltiplos dados necessários** a **cada certidão** (relação N:N).
- Manter compatibilidade com a base legada (`sgidadosnec`) usando os mesmos IDs.

---

## 2. Modelagem de banco

### 2.1 Tabela `required_data_fields`

Campos:
- `id` – bigint, PK, auto-increment **(aceita inserção manual do ID)**.
- `descricao` – string, obrigatório.
- `created_at`, `updated_at`.

> O formulário de criação/edição deve permitir informar o `id` manualmente, para manter compatibilidade com a base legada.

### 2.2 Pivot com `certificate_types`

Criar tabela `certificate_type_required_data`:
- `id` – bigint, PK.
- `certificate_type_id` – FK para `certificate_types.id`.
- `required_data_field_id` – FK para `required_data_fields.id`.
- `created_at`, `updated_at`.
- Índice único para (`certificate_type_id`, `required_data_field_id`).

### 2.3 Eloquent

- `CertificateType`:
  - `requiredDataFields(): BelongsToMany` (com `withTimestamps()`).
- `RequiredDataField` (novo model):
  - `certificateTypes(): BelongsToMany` (com `withTimestamps()`).

---

## 3. Seed inicial (base legada)

Criar um seeder (ex.: `RequiredDataFieldsSeeder`) com os registros abaixo, preservando o `id`:

```sql
INSERT INTO `sgidadosnec` (`id`, `descricao`)
VALUES
    ('2', 'Área Construida'),
    ('3', 'Área de Terreno'),
    ('4', 'Área Total'),
    ('5', 'Autor'),
    ('6', 'Contribuinte Anterior'),
    ('7', 'Custo Global da Obra'),
    ('8', 'Data de Casamento'),
    ('9', 'Data de Distribuição'),
    ('10', 'Data de Emissão do Documento'),
    ('11', 'Data de Nascimento'),
    ('12', 'Data de Registro'),
    ('13', 'Endereço'),
    ('14', 'Endereço do Imóvel'),
    ('15', 'Nome'),
    ('16', 'Nome da Mãe'),
    ('17', 'Nome do Conjuge'),
    ('18', 'Nome do Edifício / Condomínio / Loteamento'),
    ('19', 'Nome do Incorporador'),
    ('20', 'Nome do Pai'),
    ('21', 'Número da Inscrição Estadual'),
    ('22', 'Número da Inscrição Municipal'),
    ('23', 'Número da Matrícula'),
    ('24', 'Número da Microfilmagem'),
    ('25', 'Número da Transcrição'),
    ('26', 'Número da(s) Folha (s)'),
    ('27', 'Número de Contribuinte'),
    ('28', 'Número de Inscrição da Dívida'),
    ('29', 'Número de Ordem do Processo'),
    ('30', 'Número de Unidade Autônomas'),
    ('31', 'Número do C.N.P.J.'),
    ('32', 'Número do C.P.F.'),
    ('33', 'Número do Livro'),
    ('34', 'Número do Lote'),
    ('35', 'Número do NIRE'),
    ('36', 'Número do Parcelamento'),
    ('37', 'Número do Processo'),
    ('38', 'Número do R.I.P.'),
    ('39', 'Número do Registro'),
    ('40', 'Profissão'),
    ('41', 'Quadra'),
    ('42', 'Número do R.G.'),
    ('43', 'Razão Social'),
    ('45', 'Réu'),
    ('46', 'Tipo da Ação'),
    ('47', 'Título de Eleitor'),
    ('48', 'Valor da Dívida'),
    ('49', 'Valor da Transação / Transmissão'),
    ('50', 'Valor de Avaliação'),
    ('51', 'Valor do Capital Social'),
    ('52', 'Valor do Parcelamento'),
    ('53', 'Valor Venal'),
    ('54', 'IPTU'),
    ('55', 'Número da O.A.B.'),
    ('56', 'Nome do Contador'),
    ('57', 'Número do CRC'),
    ('58', 'Nome do Proprietário'),
    ('59', 'Participação do Contrato Social'),
    ('60', 'Número do Processo da CETESB'),
    ('61', 'Data do Processo da CETESB'),
    ('62', 'Número do Protocolo da CETESB'),
    ('63', 'Qualificação do Contador'),
    ('64', 'Código CNAE Fiscal'),
    ('65', 'Código de Evento'),
    ('244', 'Registro de Imóveis'),
    ('245', 'Cartório Cívil'),
    ('246', 'Cartório de Protestos'),
    ('247', 'Cartório de Notas e Tabelião'),
    ('248', 'Croqui de localização das unidades'),
    ('249', 'Vara de Origem'),
    ('250', 'Local de Nascimento'),
    ('251', 'DATA DO ÓBITO'),
    ('252', 'Número de Inscrição do Loteamento'),
    ('253', 'Nome do Loteamento'),
    ('254', 'Data da Inscrição do Loteamento'),
    ('255', 'Endereço do Loteamento'),
    ('257', 'Prazo de carência'),
    ('258', 'Nome do Empreendimento'),
    ('259', 'Áreas das unidades autonomas'),
    ('260', 'Valor da construção'),
    ('261', 'Qualificação dos sócios'),
    ('262', 'Sede da Sociedade'),
    ('263', 'Capital Social'),
    ('264', 'Objetivo da Sociedade'),
    ('265', 'Distribuição de capital social'),
    ('266', 'Forma de administração'),
    ('267', 'Nomeação de administrador'),
    ('268', 'Deliberação em Assembléia'),
    ('269', 'Forma de extinção do contrato'),
    ('270', 'Alteração do contrato'),
    ('271', 'Qualificação das partes'),
    ('272', 'Dados do imóvel'),
    ('273', 'Valor e condições para pagamento'),
    ('274', 'Cláusulas penais'),
    ('275', 'Irrevogabilidade'),
    ('276', 'Foro de Eleição'),
    ('277', 'Qualificação do Requerente'),
    ('278', 'Homogeneidade de Titularidade'),
    ('279', 'Contiguidade dos imóveis'),
    ('280', 'Confrontação atualizada'),
    ('281', 'Qualificação do inventariado'),
    ('282', 'Qualificação dos herdeiros'),
    ('283', 'Valor do bem (se móveis ou direitos)'),
    ('284', 'Indicação de Menores'),
    ('285', 'Descrição do lote'),
    ('286', 'Dados da Demolição'),
    ('287', 'Dados do Memorial'),
    ('288', 'Projeto Modificativo'),
    ('289', 'Dados da Especificação'),
    ('290', 'Projeto de Reforma'),
    ('291', 'CND de reforma'),
    ('292', 'Valor de referência'),
    ('294', 'Plano de partilha'),
    ('295', 'Bens e Direitos do Espólio'),
    ('297', 'Nome dos Representantes Legais'),
    ('298', 'Doação de área parcial a Municipalidade'),
    ('299', 'Posto Fiscal'),
    ('300', 'Advogado Assistente'),
    ('301', 'Número do NIRF'),
    ('302', 'Endereço dos Confrontantes para Notificação'),
    ('303', 'Existência de Ocupantes nos Imóveis Confrontantes'),
    ('304', 'Quantos Imóveis serão Retificados'),
    ('305', 'Haverá Cumulação com Unificação'),
    ('306', 'Foro competente'),
    ('307', 'NÚMERO DA CEI'),
    ('308', 'ATA DE ASSEMBLÉIA'),
    ('309', 'PROTOCOLO DE JUSTIFICAÇÃO DA INCORPORAÇÃO'),
    ('310', 'LAUDO DE AVALIAÇÃO'),
    ('311', 'QUITAÇÃO CONDOMINIAL'),
    ('312', 'ATA DE ELEIÇÃO DO SINDICO'),
    ('313', 'PROVA DE REPRESENTAÇÃO'),
    ('314', 'Outras Informações'),
    ('315', 'TIPO DE CONTRATO'),
    ('316', 'Número do Título de Eleitoral'),
    ('317', 'ADQUIRENTE'),
    ('318', 'TRANSMITENTE'),
    ('319', 'FRAÇÃO IDEAL'),
    ('320', 'Tipo do Instrumento'),
    ('321', 'Número do CREA'),
    ('322', 'Loteador'),
    ('323', 'Tratado Internacional - numero e artigo'),
    ('324', 'Código de Atividade Econômica'),
    ('325', 'Ramo de Atividade'),
    ('326', 'Finalidade da Certidão'),
    ('327', 'Órgão Emissor'),
    ('328', 'Estado'),
    ('329', 'GENERO (M/F)'),
    ('330', 'Documento Solicitado'),
    ('331', 'Faturar para'),
    ('332', 'Condomínio'),
    ('333', 'Serviço'),
    ('334', 'Matrícula ou Transcrição'),
    ('335', 'Ano Exercicio'),
    ('337', 'Cadastro Imovel');
```

Recomendação:
- Usar `upsert` pelo `id` para permitir reexecução do seeder.
- Registrar o seeder no `DatabaseSeeder`.

---

## 4. Admin – módulo Dados Necessários

### 4.1 Rotas

- `GET /backend/required-data-fields` → `backend.required-data-fields.index`
- `GET /backend/required-data-fields/create` → `backend.required-data-fields.create`
- `POST /backend/required-data-fields` → `backend.required-data-fields.store`
- `GET /backend/required-data-fields/{requiredDataField}/edit` → `backend.required-data-fields.edit`
- `PUT/PATCH /backend/required-data-fields/{requiredDataField}` → `backend.required-data-fields.update`

### 4.2 Listagem

- Layout e visual seguindo o padrão de `certificate-types`.
- Busca por `id` e `descricao`.
- Paginação dentro do card.

### 4.3 Formulários (create/edit)

- Campos obrigatórios: `id`, `descricao`.
- Exibir `created_at` / `updated_at` no modo edição (somente leitura).
- Após salvar, **permanecer na tela de edição** com mensagem de sucesso (ver regra em `00-master-guideline.md`).

---

## 5. Navegação do backend

No menu **Certidões**, criar submenu:

- **Listar** → `backend.certificate-types.index`
- **Dados Necessários** → `backend.required-data-fields.index`
- **Categorias** → placeholder (por enquanto sem implementação)
