/*
[docs]
@Author: André Gomes ( @acidcode )
@since 2026-01-23
Resumo executivo do produto para apresentacao a diretoria.
*/

# PRD Executivo - Emergency (Planeta Certidoes)

## 1. Resumo executivo
O Emergency e uma plataforma SaaS para emissao e gestao de certidoes (PF, PJ e Imoveis). O produto reduz tempo operacional, padroniza dados e melhora a conversao de pedidos com um fluxo guiado, pagamentos integrados e acompanhamento em tempo real.

## 2. Problema e oportunidade
- Emissao manual e fragmentada aumenta erros e retrabalho.
- Falta de padronizacao de dados atrasa a operacao.
- Pagamentos e conciliacao pouco integrados elevam o custo operacional.

## 3. Solucao
- Fluxo de pedido em 3 fases (clientes, certidoes, revisao) com validacao obrigatoria.
- Catalogo unico de certidoes como fonte de verdade.
- Revisao de dados necessarios por certidao antes do pagamento.
- Pagamento por saldo e gateway externo (Mercado Pago).
- Painel admin para operacao e auditoria.
- Gordon AI para suporte e triagem inicial.

## 4. Diferenciais competitivos
- Menos erros: dados obrigatorios por certidao com revisao guiada.
- Escalabilidade: separacao clara entre cliente e admin.
- Rastreabilidade: auditoria completa de acoes administrativas.
- Experiencia do cliente: fluxo simples e status claros.
- Atendimento: agente Gordon com contexto e rate limit.

## 5. Principais funcionalidades
- Pedido v2 em telas separadas com breadcrumb e stepper.
- Multiplos clientes por pedido e selecao de certidoes por cliente.
- Pagamento com saldo e Mercado Pago (PIX/cartao/boleto via Checkout Pro).
- Carteira com extrato e regras de saldo.
- Suporte integrado por ticket e contato contextual ao pedido.
- Emails transacionais (cadastro, pedido criado, pedido pago).
- Backend admin: usuarios, catalogo, dados necessarios, indicadores e auditoria.

## 6. Roadmap em fases
1. Base: catalogo, pedidos v2, dados necessarios, dashboard e listagens.
2. Pagamentos: saldo e Mercado Pago + conciliacao.
3. Admin: catalogo completo, dados necessarios e auditoria.
4. IA: agente Gordon com historico e custos monitorados.
5. Migracao: reduzir dependencia do modelo legado.

## 7. Metricas de sucesso (KPIs)
- Conversao de pedidos iniciados -> pagos.
- Tempo medio para concluir pedido.
- Reducao de retrabalho por dados incompletos.
- Volume de pedidos por cliente e por certidao.
- Taxa de resolucao via autoatendimento (Gordon AI).

## 8. Riscos e mitigacoes
- APIs externas instaveis: fallback e cache onde possivel.
- Qualidade do catalogo: seeds e validacoes centralizadas.
- Transicao do legado: fases com convivencia controlada.
- Fraude/abuso: rate limit e auditoria.

## 9. Recursos e dependencias
- Backend: Laravel 12, Fortify, Livewire/Volt.
- UI: Flux UI + Tailwind v4.
- Integracoes: IBGE, BrasilAPI, Mercado Pago, OpenAI.

## 10. Proximos passos
- Validar com diretoria o foco do MVP e o cronograma.
- Priorizar integracoes criticas (pagamento e dados necessarios).
- Definir politica de chargeback e estorno.
