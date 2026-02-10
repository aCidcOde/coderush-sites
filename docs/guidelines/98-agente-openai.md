# 98 - Agente OpenAI (Gordon AI)

Este guideline define o fluxo e os requisitos para integrar o agente no endpoint `/agente`.

---

## 1. Objetivo

- Habilitar atendimento automatico com OpenAI no chat do `/agente`.
- Manter historico, controle de custo e seguranca de dados.

---

## 2. Escopo

- Frontend: enviar mensagens via `POST /agente/mensagem` e renderizar a resposta.
- Backend: validar entrada, compor contexto, chamar OpenAI e retornar payload padrao.
- Persistencia: opcional, mas recomendada para auditoria e contexto.

---

## 3. Configuracao

- `config/services.php` deve conter:
  - `openai.api_key` (env `OPENAI_API_KEY`)
  - `openai.model` (env `OPENAI_MODEL`, default `gpt-4o-mini`)
  - `openai.base_url` (env `OPENAI_BASE_URL`, default `https://api.openai.com/v1`)
- Nunca usar `env()` fora de arquivos de configuracao.

---

## 4. Fluxo de mensagens

1. Cliente envia `message` e `conversation_id` (opcional) para `POST /agente/mensagem`.
2. Backend valida input com Form Request.
3. Backend aplica rate limiting e carrega contexto (sessao ou banco).
4. Backend chama OpenAI e retorna:
   - `message` (texto do agente)
   - `conversation_id` (para manter contexto)
   - `metadata` (opcional: modelo, tokens, tempo)

---

## 5. Prompt e contexto

- Separar `system` (regras do agente) e `user` (mensagem atual).
- Manter janela de contexto limitada (ex: ultimas 10 mensagens).
- Se faltar dado critico (UF, cartorio, tipo de certidao), perguntar antes de prosseguir.
- Evitar respostas inventadas: se nao souber, pedir esclarecimento ou escalar.

---

## 6. Persistencia recomendada

- Tabelas sugeridas:
  - `agent_conversations` (id, user_id nullable, session_id, status, created_at, updated_at)
  - `agent_messages` (conversation_id, role, content, metadata, created_at)
- `user_id` pode ser nulo para visitantes; usar `session_id` para correlacao.

---

## 7. Seguranca e compliance

- Endpoint publico deve ter `throttle` por IP.
- Nao logar PII nem textos completos em logs de infraestrutura.
- Mascarar documentos (CPF, RG) quando persistir ou logar.
- Evitar anexos por enquanto (somente UI).

---

## 8. Observabilidade e custo

- Registrar `request_id`, modelo, tempo de resposta e tokens.
- Guardar custo estimado por conversa quando necessario.

---

## 9. Erros e fallback

- Timeout ou falha da OpenAI: responder com mensagem curta e sugerir humano.
- Resposta vazia: reenviar uma vez, depois fallback.
- Se baixa confianca: pedir mais contexto antes de orientar.

---

## 10. Criterios de aceitacao

- `POST /agente/mensagem` responde 200 com `message` valido.
- Convidado consegue conversar sem login.
- Rate limit bloqueia abuso sem quebrar o fluxo normal.
- Historico mantem contexto entre mensagens da mesma conversa.

---

## 11. Perguntas abertas

- Persistencia obrigatoria ou apenas sessao?
- Streaming de respostas ou resposta unica?
- Quais dados do pedido podem ser consultados pelo agente?
