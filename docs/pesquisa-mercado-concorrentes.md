<!--
/*
[Modulo Documentacao Operacional — Pesquisa de mercado: concorrentes MMN]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-02
Levantamento de conteudo e features dos concorrentes vs plataforma SVD.
Fontes: paginas publicas em 2026-08-02 (maxnivel.com.br, mmnweb.com, sistemammn.com.br,
sinergiared.com, embratonhost.com.br). Aliadus/M2N/Epixel bloquearam acesso automatizado.
*/
-->

# Pesquisa de mercado — Concorrentes de sistema MMN

## O que cada um mostra

| Concorrente | Prova social | Oferta/preco | Destaques |
|---|---|---|---|
| MaxNível | +500 clientes, 20+ logos, 3 depoimentos | só "Solicitar demonstração" | 168+ módulos, 4 planos de compensação (binária, unilevel, matriz forçada, australiana), NF-e integrada, AWS |
| MMNWEB | 25 anos, +280 empresas, +50k distribuidores | **teste grátis 14 dias**, demo guiada | **simulador de plano de comissão grátis sem cadastro**, 15 bônus configuráveis, compressão dinâmica, antifraude/multicadastro, migração com "valor fechado antes" |
| SistemaMMN (LM) | 200+ clientes, +30 anos do consultor | **preço público**: R$ 2.847 a R$ 13.288 + R$ 365/mês | 17 níveis de comissão, matriz aberta/fechada/cíclica, marketing de doação, +23 formas de pagamento, **apps Android/iOS**, **URL personalizada por associado**, implantação em 15 dias úteis |
| Sinergia Red (ES) | 15 anos, +18 países, +1M transações | demo ao vivo via WhatsApp, **migração garantida** | "no ar em 10 dias", e-wallet, app nativo, chat privado, árvore em tempo real |
| Embraton | nenhuma | aluguel (sem preço) | simulador de matriz |

## A) Conteúdo pra adotar nas LPs de oferta (sem mexer no produto)

1. **Prazo de implantação como manchete** — concorrentes prometem 10-15 dias; nós temos
   *fato*: 5 dias no Paraguai. Usar "no ar em dias, não meses".
2. **Garantia de migração** — Sinergia tem "migração garantida", MMNWEB "valor fechado antes".
   Nós já migramos (FAQ) — elevar a card de garantia.
3. **NF-e integrada** — MaxNível anuncia; nós TEMOS (Ecotrend/SPED) e não falávamos. Incluído.
4. **Depoimento nominal na LP** — todos usam; nossos 4 da home não apareciam nas LPs.
5. **Vídeo de demonstração** — MaxNível/MMNWEB usam. Temos vídeo no YouTube (docs V3) —
   PENDENTE: URL do vídeo pra embutir.
6. **Demo navegável** — MMNWEB dá teste 14 dias. Temos demo em painel.sistemavendadireta.com.br —
   PENDENTE: decidir gate (form libera acesso) e trocar senhas padrão antes.
7. **Números agressivos** — eles inflam (+500 clientes). Nós: 25 anos, 24+ marcas atendidas,
   2 países, sistema em produção desde 2006. Honesto e suficiente.

## B) Gaps de produto (o que o mercado tem e o SVD não anuncia/não tem)

### Rápidos (semanas, tech já existe em casa)
1. **Simulador de plano de comissão** (MMNWEB cobra atenção com isso; é pré-venda, não produto —
   JS na LP simulando ganhos por rede/percentual; vira ímã de lead)
2. **Link de indicação rastreável por consultor** (member-get-member; hoje temos convite por e-mail)
3. **URL/página replicada por consultor** (sistemammn tem; padrão do mercado internacional)
4. **Notificações push** no escritório virtual (MauaSinc já tem essa stack — portar)

### Médios (1-2 meses)
5. **E-wallet completa** (saldo já existe; falta transferência entre consultores e extrato tipo carteira)
6. **Antifraude/multicadastro nativo** (temos módulos custom no e-commerce; empacotar pro MMN)
7. **Compressão dinâmica e bônus configuráveis nomeados** (MMNWEB anuncia 15 tipos; motor por
   cargo do Paraguai já é metade do caminho)

### Maiores (roadmap)
8. **Planos matriz forçada / australiana / doação** (MaxNível e sistemammn cobrem; nós: binário,
   unilevel, cargo)
9. **App nativo ou PWA** (sistemammn e Sinergia têm apps; nosso é web responsivo — PWA é atalho barato)
10. **Árvore de rede com visualização em tempo real** (Sinergia destaca; MauaSinc já usa cytoscape —
    portar visualização)

## Recomendação de sequência

- Conteúdo: itens A1-A4 aplicados em 2026-08-02; A5/A6 aguardam URL do vídeo e decisão do gate da demo.
- Produto: começar por B1 (simulador — pré-venda pura, mede interesse via GA4) e B2/B3 (indicação
  rastreável + página do consultor, argumentos de paridade que os anúncios podem citar depois).
- PWA (B9) antes de app nativo: 90% do benefício, fração do custo.
