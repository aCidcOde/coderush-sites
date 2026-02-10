/*
docs/guidelines
@Author: André Gomes ( @acidcode )
@since 2026-01-26
Guideline do modulo InfoSimples para orientar implementacao por fases.
*/

# 100 - InfoSimples (guideline de desenvolvimento)

## 1. Objetivo
Integrar o servico InfoSimples para emissao automatica das certidoes vendidas no Emergency, com rastreabilidade, audit logs e fluxo controlado por jobs.

## 2. Escopo
- Criar as tabelas novas necessarias para a integracao InfoSimples.
- Mapear certidoes do catalogo para endpoints da InfoSimples.
- Orquestrar emissao via jobs e downloads quando aplicavel.
- Registrar logs tecnicos e auditoria de operacoes admin.
- Nao implementar UI final de cliente nesta etapa, apenas o necessario para o dev operacional.

## 3. Referencias internas (base legada)
- `docs/infosimples/database.sql`
- `docs/infosimples/apiCertidoesJobModel.php`
- `docs/infosimples/apiCertidoesModel.php`
- `docs/infosimples/api-certidoes.php`
- `docs/infosimples/api-certidoes-download.php`

## 4. Relacionamento entre guidelines
- `10-certidoes-catalogo.md`: `certificate_types` e fonte de verdade do catalogo.
- `95-certidoes-extended.md`: campos de integracao (endpoint, custo, login, download).
- `90-dados-necessarios.md`: catalogo de dados necessarios (`required_data_fields`) e pivot.
- `92-revisao-dados-necessarios.md`: origem do valor (revisado por item do pedido).
- `60-integration-existing-system.md`: transicao do legado, sem quebrar dashboard.

## 5. Entidades e tabelas (novas)
Criar somente as tabelas que nao existem no sistema atual:

### 5.1 certificate_types (extensao)
- Nao criar `api_certidoes`.
- Usar os campos de integracao descritos em `95-certidoes-extended.md`.
- `login_provider`: 0 (sem login), 1 (gov.br), 2 (prefeitura SP).
- `requires_download`: 1 quando o fluxo exige etapa adicional de download.

### 5.2 required_data_fields + certificate_type_required_data
- Usar o catalogo definido em `90-dados-necessarios.md`.
- O vinculo N:N entre certidao e dados necessarios deve usar `certificate_type_required_data`.
- Manter IDs legados para compatibilidade.

### 5.3 infosimples_jobs
Tabela de orquestracao da emissao.
- Referencias: `id_item`, `certificate_type_id`, `usuario`.
- Controle de status e armazenamento de arquivo.
- Campos do pedido para etapas especiais (ex.: TJSP).

### 5.4 infosimples_job_logs
Log tecnico detalhado das chamadas da API.
- Deve registrar code, mensagem, endpoint, url e retorno.

## 5. Mapeamento minimo de status (job)
Usar os codigos legados como base de transicao:
- 0: removido/inativo
- 1: pendente (aguardando processamento)
- 4: em processamento (lock de execucao)
- 2: pronto (arquivo salvo)
- 5: aguardando download (ex.: TJSP pedido-certidao)
- 3: erro definitivo (falha nao recuperavel)

## 6. Fases de implementacao

### Fase 1 - Base de dados e modelos
Entregaveis:
- Migrations e Models Eloquent para todas as tabelas novas.
- Factories e seeders minimos para `api_certidoes` (quando necessario).
- Relacoes basicas com dados necessarios.
Notas:
- Nao alterar tabelas existentes do dominio principal.
- Seguir o padrao de casts e naming do projeto.

### Fase 2 - Mapeamento de certidoes e dados necessarios
Entregaveis:
- Cadastro inicial das certidoes InfoSimples em `certificate_types` (campos de integracao).
- Vinculo com dados necessarios em `certificate_type_required_data`.
Regras:
- O mapeamento deve respeitar IDs ja usados pelo dominio (`dados_necessarios`).
- Certidoes com `finalidade` precisam enviar `modelo`, `email_envio` e `nome_completo`.

### Fase 3 - Orquestracao de jobs
Entregaveis:
- Jobs para processamento e download das certidoes.
- Agendamentos com lock (nao concorrente).
Regras:
- Cada item/certidao gera um registro em `api_certidoes_job`.
- Nao reprocessar se ja existir job ativo para o mesmo item/certidao.
- Persistir logs tecnicos em `api_certidoes_job_log`.

### Fase 4 - Persistencia de arquivos e notificacoes
Entregaveis:
- Download e persistencia do PDF (ou arquivo retornado) em storage.
- Atualizacao de status e caminho do arquivo.
- Notificacao ao cliente/admin quando houver conclusao.
Regras:
- Padrao de armazenamento: `/infosimples/YYYY/MM/`.
- Se nao houver extensao, salvar como `pdf`.

### Fase 5 - Admin e auditoria
Entregaveis:
- CRUD minimo de `api_certidoes` para admin.
- Auditoria obrigatoria para create/update/delete (before/after).
Regras:
- Toda operacao admin deve gerar audit log conforme guideline 99.

### Fase 6 - Testes e validacao
Entregaveis:
- Testes PHPUnit cobrindo:
  - Criacao de certidao e vinculos de dados necessarios.
  - Criacao de job e transicoes de status.
  - Log tecnico de chamadas (persistencia).
  - Auditoria em operacoes admin.
Regras:
- Rodar `php artisan test --compact` com o arquivo afetado.

## 7. Regras de integracao com pedidos
- A emissao so deve iniciar apos pagamento confirmado.
- Vincular `infosimples_jobs.order_item_id` ao item do pedido (order item).
- Manter compatibilidade com o fluxo atual de pedidos.
- Os valores enviados para a API devem vir da revisao por item (`92-revisao-dados-necessarios.md`), e nao do cadastro do cliente.
 - Pagamento aprovado (hook ou job de reconciliacao) dispara o enfileiramento do job InfoSimples.
 - No frontend, status do pedido para cliente permanece como "Em processamento" durante toda a emissao.
 - No admin, a fila exibe status real (erro, motivo, tentativas, etc.).
 - Deve existir acao admin para "forcar execucao" de um job pendente.

## 7.1 Fluxo operacional (resumo)
1. Cliente cria pedido e seleciona certidoes.
2. Cliente preenche e revisa dados necessarios por item.
3. Cliente paga (Mercado Pago).
4. Webhook/job confirma pagamento.
5. Sistema enfileira jobs InfoSimples por item/certidao.
6. Jobs processam emissao e (quando aplicavel) download.
7. Frontend mostra pedido "Em processamento" ate conclusao.
8. Admin acompanha fila com status real e pode forcar execucao.

## 7.2 Integracao com pagamento (Mercado Pago)
- O webhook deve registrar o evento de pagamento aprovado e acionar o fluxo de emissao.
- Se o webhook falhar, deve existir job de reconciliacao para confirmar pagamento e liberar a fila.
- Somente pagamentos aprovados iniciam emissao.
- Registros de auditoria devem guardar before/after das transicoes de status.

## 7.3 Fila e jobs (Laravel)
- Criar jobs de emissao e download separados (quando aplicavel).
- Cada job deve registrar:
  - status atual
  - tentativas
  - ultimo erro
  - payload enviado
  - resposta recebida
- Implementar lock para evitar concorrencia por item/certidao.
- Jobs devem ser idempotentes (nao duplicar emissao).

## 7.4 Admin - operacao e observabilidade
- Tela de fila deve listar:
  - item/pedido
  - certidao
  - status real
  - motivo do erro
  - tentativas
  - timestamps
- Acao admin: "forcar execucao" (requeue) e "marcar como erro definitivo".
- Toda acao admin deve gerar log de auditoria.

## 8. Seguranca e configuracoes
- Tokens e credenciais devem estar em `config/` e `.env`.
- Nunca hardcode de token/senha no codigo.
- Mascarar dados sensiveis nos logs de auditoria.
 - Credenciais de login (gov.br / prefeitura SP) devem ser configuradas por ambiente.

## 9. Tradutor de dados necessarios (IDs -> parametros da API)
Mapeamento legada usado para converter IDs de `required_data_fields` em parametros:
- 11 -> `birthdate`
- 15 -> `nome`
- 16 -> `nome_mae`
- 31 -> `cnpj`
- 32 -> `cpf`
- 42 -> `rg`
- 43 -> `razao_social`
- 314 -> `others`
- 329 -> `genero`
- 337 -> `cadastro_imovel`
- 27 -> `cadastro_imovel`
- 335 -> `ano_exercicio`
Regras:
- Datas devem ser enviadas em `YYYY-MM-DD`.
- CPF/CNPJ/RG sem pontuacao.
- `cadastro_imovel` deve ser enviado como `sql`.
- `cnpj` deve gerar `cnpj_raiz` quando exigido.
- Certidoes TJ nao devem enviar `nome` quando houver `cnpj`.
- IDs nao mapeados exigem definicao explicita antes de ativar a certidao.

## 10. Diretrizes da API InfoSimples (essenciais)
- Base URL: `https://api.infosimples.com/api/v2/consultas/{servico}` (POST).
- Parametros comuns: `token` (obrigatorio), `timeout` (15-600), `ignore_site_receipt=1` (opcional).
- Retorno sempre em JSON com `data` como array e `errors` como array.

## 11. Atualizacoes recentes (Jan/2026)
- Timeout de chamadas InfoSimples passou a ser configuravel via `services.infosimples.timeout` (padrao 300s) para cobrir casos de TJSP com resposta lenta.
- Jobs `ProcessInfoSimplesJob` e `DownloadInfoSimplesJob` agora possuem `timeout` local (360s) para nao estourar o tempo de execucao do worker.
- Teste `InfoSimplesJobTimeoutTest` garante que o timeout dos jobs >= timeout configurado do servico.
- Backend do pedido exibe status detalhado por item com estados `processing`, `awaiting_download`, `issued`, `error` e permite acao de download quando o arquivo esta disponivel.
- Download do arquivo de certidao foi integrado ao fluxo de pedidos quando `infosimples_jobs.file_path` estiver preenchido.

Observacoes operacionais:
- Ajustar o `queue:work --timeout` e `*_QUEUE_RETRY_AFTER` para valores maiores que 360s quando houver emissao TJSP.
- Se necessario, definir `INFOSIMPLES_TIMEOUT=300` (ou maior) no `.env` por ambiente.
- `site_receipts` expiram em 7 dias; baixar e armazenar em infraestrutura propria.
- Nao chamar a API do frontend (CORS); sempre via backend.
- Ajustar timeout do cliente HTTP para nao ser menor que o timeout enviado.
 - Campo `header` inclui: `service`, `parameters`, `price`, `billable`, `requested_at`, `elapsed_time_in_milliseconds`.
 - Codigo 200 sempre indica sucesso (v2 nao usa 201).
 - Quando nao quiser arquivos, usar `ignore_site_receipt=1`.
 - Links oficiais uteis:
   - Assincrona: https://api.infosimples.com/consultas/docs/assincrona
   - Status de servico: https://api.infosimples.com/consultas/docs/api-status
   - OpenAPI/Swagger/Postman: https://api.infosimples.com/consultas/docs/openapi

## 11. Regras de retentativa
- Definir limite maximo de tentativas por certidao/job.
- Codigo 605 (timeout) normalmente permite retentativa e nao e cobrado.
- Codigo 620 tende a nao mudar em curto prazo e e cobrado; evitar retentativa imediata.
- Nao permitir loop infinito de reprocessamento.
 - Tratar 613/614/615/618 como transitorios (retentar com backoff).
 - Tratar 606/607/608 como erro de validacao (nao retentar sem correcao de dados).
 - Tratar 601/602/603 como erro de configuracao (bloquear jobs ate ajuste).
 - Tratar 621 como erro de geracao de arquivo (retentar download quando aplicavel).
 - Tratar 622 como excesso de repeticao (throttle/backoff agressivo).

## 12. Mapeamento tecnico recomendado (API -> status do job)
- 200: `status=2` (pronto) quando `site_receipt` retornado e arquivo salvo.
- 200 + servico com etapa de download (ex.: TJSP pedido-certidao): `status=5` ate baixar.
- 605/613/614/615/618: manter pendente com retentativa controlada.
- 606/607/608/619: `status=3` (erro definitivo por dados/parametros).
- 620: `status=3` (erro definitivo, evitar retentativa curta).
- 601/602/603: `status=3` + alerta operacional.

## 13. Padrao de payload e transformacoes
- Datas no padrao `YYYY-MM-DD` (ex.: birthdate).
- CPF/CNPJ/RG sempre sem pontuacao.
- `finalidade` exige `modelo`, `email_envio` e `nome_completo`.
- `cadastro_imovel` deve ser enviado como `sql`.
- `cnpj` deve gerar `cnpj_raiz` quando exigido.
- Certidoes TJ (IDs conhecidos) nao devem enviar `nome` quando houver `cnpj`.

## 14. Consideracoes sobre catalogo de servicos
- O servico InfoSimples deve ser cadastrado por `endpoint` (path do servico) em `certificate_types`.
- Selecionar apenas os servicos usados pelo catalogo Emergency (nao cadastrar toda a lista da InfoSimples).
- Validar no painel InfoSimples se o token possui acesso ao servico antes de ativar.

## 15. Criterios de aceite (minimos)
- Certidao cadastrada com dados necessarios corretos.
- Job cria log tecnico por chamada.
- Status evolui ate pronto ou erro definitivo.
- Arquivo salvo e acessivel quando pronto.
- Operacoes admin registram auditoria com before/after.

## 16. Manutencao e consulta rapida
### Arquivos principais
- Jobs: `app/Jobs/ProcessInfoSimplesJob.php`, `app/Jobs/DownloadInfoSimplesJob.php`
- Servicos: `app/Services/InfoSimplesService.php`, `app/Services/InfoSimplesPayloadBuilder.php`, `app/Services/InfoSimplesJobDispatcher.php`
- Modelos: `app/Models/InfoSimplesJob.php`, `app/Models/InfoSimplesJobLog.php`
- Enum: `app/Enums/InfoSimplesJobStatus.php`
- Admin (fila): `resources/views/livewire/backend/infosimples-jobs/index.blade.php`
- Rota admin: `routes/web.php` (`backend.infosimples-jobs.index`)

### Variaveis de ambiente
- `INFOSIMPLES_BASE_URL`
- `INFOSIMPLES_TOKEN`
- `INFOSIMPLES_TIMEOUT`
- `INFOSIMPLES_MAX_ATTEMPTS`
- `INFOSIMPLES_EMAIL_DOMAIN`
- `INFOSIMPLES_TJSP_NO_NAME_CODES`
- `INFOSIMPLES_GOVBR_CPF`
- `INFOSIMPLES_GOVBR_SENHA`
- `INFOSIMPLES_PREFEITURA_SP_CNPJ`
- `INFOSIMPLES_PREFEITURA_SP_SENHA`

### Status do job
- 0 Inactive
- 1 Pending
- 2 Completed
- 3 Failed
- 4 Processing
- 5 AwaitingDownload

### Observabilidade
- Logs tecnicos em `infosimples_job_logs`.
- Auditoria admin via `AdminAuditLogger`.

### Fila
- Jobs devem rodar via queue worker (`php artisan queue:work`).
