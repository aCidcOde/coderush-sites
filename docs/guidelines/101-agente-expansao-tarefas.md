/*
[Modulo: docs/guidelines]
@Author: André Gomes ( @acidcode )
@since 2026-01-31
guideline de negocio para expansao de tarefas do agente na rota /agente.
*/
# 101 - Agente /agente: Expansao de tarefas

Este guideline define o escopo de negocio para evoluir o agente do endpoint `/agente`,
com foco em novas tarefas e fluxos guiados. Nao detalha implementacao tecnica; apenas
define regras, dados e criterios de aceitacao.

---

## 1. Objetivo

- Ampliar o que o agente faz no `/agente` alem de respostas livres.
- Guiar o usuario em tarefas tipicas: solicitar certidao, acompanhar pedido,
  consultar taxas/prazos e validar dados necessarios.
- Preservar seguranca, privacidade e confiabilidade das respostas.

---

## 2. Escopo

- UI `/agente`: direcionar o usuario por intencao e passos (sem forcar cadastro).
- Backend: usar ferramentas internas para dados reais (certidoes e pedidos).
- Persistencia: manter historico por sessao/usuario para continuidade.
- Sem automacao de criacao de pedido ate definicao explicita.

Fora de escopo (por enquanto):
- Upload de documentos.
- Assinatura digital ou validacao juridica.
- Alteracao de dados sensiveis sem autenticacao.

---

## 3. Personas e permissoes

- Visitante (nao autenticado):
  - Pode consultar informacoes publicas e tipos de certidao.
  - Nao pode ver pedidos nem dados pessoais.
- Cliente autenticado:
  - Pode consultar pedidos proprios e status.
  - Pode receber resumo de pedido (sem dados sensiveis expostos).
- Admin/atendente:
  - Nao definido neste fluxo (abrir pergunta).

---

## 4. Entidades e dados principais

### 4.1 Conversa
- `conversation_id` (persistencia da conversa)
- `session_id` (visitantes)
- `user_id` (clientes autenticados)

### 4.2 Mensagens
- `role`: user | assistant | tool
- `content`
- `metadata` (ex: tool_call_id, uso de tokens)

### 4.3 Dados de negocio (minimo esperado)
- Tipo de certidao: id, nome, codigo, preco base.
- Pedido: id, status, valor, data, itens.
- Itens do pedido: certidao, quantidade, status, total.

Obs.: campos detalhados exigidos por certidao devem seguir `docs/guidelines/90-dados-necessarios.md`
e correlatos.

---

## 5. Fluxo principal (alto nivel)

1. Usuario acessa `/agente`.
2. Agente detecta intencao (ou pergunta se nao houver).
3. Agente executa a tarefa com base em dados internos:
   - Listar certidoes disponiveis.
   - Consultar pedidos do usuario autenticado.
4. Se faltarem dados essenciais, agente pergunta antes de prosseguir.
5. Agente entrega resposta e sugere proximo passo (ex: iniciar pedido, falar com humano).

---

## 6. Regras de negocio e validacoes

- Nao inventar informacoes: se nao houver acesso, declarar limitacao.
- Dados pessoais devem ser mascarados quando persistidos ou exibidos em logs.
- Acesso a pedidos somente para usuario autenticado e dono do pedido.
- Resposta deve ser curta, objetiva e orientar proximo passo.
- Se a intencao for "falar com humano", responder com handoff claro.
- Em caso de erro/timeout, responder fallback padrao e indicar suporte.

---

## 7. Fontes de dados e prioridade

Permitidas (ordem de preferencia):
1. Tabelas internas (certidoes, pedidos, itens).
2. Regras dos guidelines internos.
3. Perguntas diretas ao usuario quando houver ambiguidade.

Proibidas:
- Respostas sem base em dados internos para valores, prazos e status.
- Expor dados de terceiros.

---

## 8. Casos de erro e edge cases

- `conversation_id` invalido ou de outro usuario.
- Resposta vazia do agente.
- Falha de ferramenta interna (tool error).
- Usuario pede algo fora do escopo (ex: recurso juridico).
- Usuario mistura intents (solicitar certidao + acompanhamento no mesmo pedido).
- Usuario nao fornece dados minimos para o tipo de certidao.

---

## 9. Criterios de aceitacao (objetivos e verificaveis)

- O agente identifica a intencao principal ou pergunta quando nao houver.
- Visitante recebe apenas informacoes publicas.
- Cliente autenticado consegue listar pedidos proprios.
- Quando faltarem dados obrigatorios, o agente pergunta antes de orientar.
- Em falha da OpenAI, resposta de fallback aparece sem quebrar o fluxo.

---

## 10. Perguntas abertas

- Quais novas tarefas especificas entram neste ciclo?
- O agente pode iniciar a criacao de pedido ou apenas orientar?
- Haverá handoff direto para humano (chat/WhatsApp/email)?
- Quais dados de taxas e prazos sao fontes oficiais e atualizadas?
- Admin deve ter visao/acao diferente no agente?

