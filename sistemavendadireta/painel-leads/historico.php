<?php
/*
[Modulo Painel de Leads SVD — historico de otimizacoes]
@Author: André Gomes ( @acidcode )
@since 2026-08-14

Registro do que foi mudado na operacao, por que, e o que aconteceu depois.
Serve pra dois fins: nao repetir decisao ja tomada (nem desfazer sem querer uma
que funcionou) e mostrar o trabalho acumulado.

Arquivo .php de proposito: se fosse .json ficaria legivel pela web.

Campos:
  data      AAAA-MM-DD
  area      ads | medicao | painel | site
  titulo    o que foi feito, em uma linha
  porque    o problema que motivou — a parte que se esquece primeiro
  efeito    resultado observado depois (preencher quando houver leitura)
*/

return [
    [
        'data' => '2026-09-16',
        'area' => 'medicao',
        'titulo' => 'Relatório diário da campanha por e-mail, às 7h',
        'porque' => 'A leitura diária era feita à mão, e o valor dela é justamente não depender de '
            . 'alguém lembrar. Dois erros que isso teria pego cedo: o "até 31/08" ficou 12 dias no ar '
            . 'depois de vencido, e a meta de parcela em 70% levou quatro dias para mostrar que dobrava '
            . 'o CPC sem trazer clique.',
        'efeito' => 'Só alerta quando há motivo — perda por orçamento acima de 30%, cliques sem '
            . 'conversão ou CPC acima de R$ 8. Canal separado do conteúdo: quando houver provedor de '
            . 'WhatsApp, entra só a chamada HTTP e a análise não muda. Descoberto no caminho que o '
            . 'msmtp da máquina estava com a senha revogada; passou a usar o SMTP do site.',
    ],
    [
        'data' => '2026-09-16',
        'area' => 'ads',
        'titulo' => 'Meta de parcela revertida de 70% para 40%, teto de R$ 12 para R$ 7',
        'porque' => 'A subida para 70% em 12/09 não se pagou. Em 11 dias antes: R$ 89,97 e 24 cliques '
            . '(CPC R$ 3,75). Nos 4 dias depois: R$ 168,63 e 23 cliques (CPC R$ 7,33). Praticamente o '
            . 'mesmo número de cliques por quase o dobro do dinheiro, e zero lead. O que faltou pesar é '
            . 'que o volume de busca é pequeno e FIXO — cerca de 35 impressões por dia no nicho inteiro. '
            . 'Parcela de impressão redistribui quem aparece dentro de um bolo que não cresce; ir de 30% '
            . 'para 55% de topo compra exposição marginal, e justamente a mais cara.',
        'efeito' => 'Baseline congelado nos dois períodos para medir. Leitura em 19/09. O sintoma de que '
            . 'havia passado do ponto apareceu em 14/09: a perda migrou de "ranking" para "orçamento" '
            . '(50%) sem os cliques aumentarem.',
    ],
    [
        'data' => '2026-09-14',
        'area' => 'ads',
        'titulo' => 'Pausadas as 4 campanhas por segmento; só a Promoção 10 Anos no ar',
        'porque' => 'Parceiros, Cosméticos e Suplementos tiveram ZERO impressão desde que subiram — o '
            . 'nicho não é buscado, e isso não é problema de anúncio. A Afiliados tinha volume real (137 '
            . 'impressões em 2 dias) mas era gente querendo SER afiliado da Shopee e da Amazon, não '
            . 'empresa querendo montar programa: R$ 25 gastos e zero lead.',
        'efeito' => 'A palavra "plataforma de afiliados" foi pausada antes, não filtrada: nenhuma '
            . 'negativa conserta palavra cuja raiz mira o público oposto. A campanha estava 90% limitada '
            . 'por orçamento — se a leitura tivesse sido "tem volume, sobe a verba", teria multiplicado o '
            . 'desperdício por dez.',
    ],
    [
        'data' => '2026-09-14',
        'area' => 'site',
        'titulo' => '/oferta/ virou /sistema-venda-direta/, com 301 da árvore inteira',
        'porque' => 'O endereço antigo nomeava a promoção, e a promoção acaba em 30/09 — a URL não pode '
            . 'acabar junto. O novo nomeia o produto, que é o que a pessoa busca. A página também passou a '
            . 'ser indexável: enquanto era "oferta", manter fora do índice fazia sentido.',
        'efeito' => 'Atualizados 31 links internos, sitemap, 8 anúncios e 3 sitelinks — apontar para o '
            . 'endereço velho custaria um salto extra em cada clique pago. As 4 LPs por segmento seguem '
            . 'noindex: são 91% a 97% idênticas entre si, e indexar cinco páginas quase iguais é pedir '
            . 'para o Google escolher uma e descartar as outras.',
    ],
    [
        'data' => '2026-09-14',
        'area' => 'medicao',
        'titulo' => 'Cinco páginas tinham WhatsApp e nenhuma medição',
        'porque' => 'Home, /cases/, /simulador/, /inteligencia-artificial/ e /blog/ tinham link de zap sem '
            . 'evento, sem beacon, sem nada. A home é a maior porta de entrada orgânica e a /cases/ é '
            . 'destino de sitelink dos anúncios — ou seja, tráfego pago também caía ali e sumia.',
        'efeito' => 'Não dava para concluir que o orgânico não converte, porque a conversão do orgânico '
            . 'nunca tinha sido contada. O script virou include único (inc/zap-tracking.php), já com a '
            . 'leitura da atribuição pela URL — a lição do WebView que custou o gclid da venda de R$ 3.500.',
    ],
    [
        'data' => '2026-09-12',
        'area' => 'ads',
        'titulo' => 'Meta de parcela de impressão subiu de 20% para 70% (revertido em 16/09)',
        'porque' => 'Durante semanas a leitura foi "quase não aparecemos e o concorrente aparece toda '
            . 'hora", e a hipótese era lance baixo. Era falso: sob TARGET_IMPRESSION_SHARE os lances por '
            . 'palavra viram enfeite, e a campanha estava configurada com meta de 20% no topo — entregando '
            . '31%, ou seja, ACIMA da própria meta. O Google segurava por instrução nossa, com 0% de perda '
            . 'por orçamento.',
        'efeito' => 'A parcela de topo subiu de 30% para 53%, mas o CPC dobrou (R$ 3,75 para R$ 7,33) sem '
            . 'aumentar cliques. Revertido em 16/09. O diagnóstico do freio estava certo; a conclusão sobre '
            . 'o remédio, não — ver a entrada de 16/09.',
    ],
    [
        'data' => '2026-09-12',
        'area' => 'ads',
        'titulo' => 'Prazo vencido nos 9 anúncios da conta, cinco deles no ar',
        'porque' => 'Todos anunciavam "até 31/08" — vencido havia 12 dias — enquanto o site vendia com '
            . 'prazo 30/09. Ninguém tinha olhado desde a criação. Prazo vencido não é só constrangimento: '
            . 'quem clica, compara com a página e desiste, e o clique a gente paga igual.',
        'efeito' => 'É desperdício que não aparece em nenhum relatório de termo de busca, porque o tráfego '
            . 'estava certo. Corrigido preservando o histórico dos anúncios, e a varredura virou rotina '
            . '(anuncios.py --auditar). O mesmo prazo estava cravado à mão nas 5 páginas de oferta; agora '
            . 'deriva da constante PROMO_DEADLINE.',
    ],
    [
        'data' => '2026-09-09',
        'area' => 'ads',
        'titulo' => 'Lance de R$ 9 para R$ 12 em "sistema mmn" e "sistema de venda direta"',
        'porque' => 'Duas palavras ainda estavam em R$ 9 com 65% de perda por ranking — as que '
            . 'mais tinham espaço. As outras ficaram como estavam de propósito: "sistema multinivel" '
            . 'tem lance de R$ 12 e paga R$ 2,45 de CPC real, ou seja, o lance não é o gargalo dela; '
            . 'e "software para mmn" já está com 92% de parcela, não há mais leilão para comprar.',
        'efeito' => 'O aumento anterior (31/08) levou uma semana para assentar e a parcela saiu de '
            . '28% para 67%, com o CPC caindo de R$ 4,90 para R$ 3,76. Orçamento segue sobrando: '
            . 'R$ 66,78 gastos de R$ 350 disponíveis na semana, perda por orçamento em zero.',
    ],
    [
        'data' => '2026-08-31',
        'area' => 'ads',
        'titulo' => 'Lances subiram: R$ 6 para R$ 10–12 nos grupos principais',
        'porque' => 'A operação ganhou um vendedor e precisa de mais volume. O dado mostrou que '
            . 'aumentar orçamento não resolveria: gastávamos R$ 49 de R$ 350 disponíveis por semana, '
            . 'com perda por orçamento em ZERO. A perda era 62,7% por RANKING — posição no leilão, '
            . 'que se compra com lance, não com verba. O mercado comporta ~935 impressões por semana '
            . 'e estávamos pegando 37%.',
        'efeito' => 'Grupo Sistema MMN de R$ 6 para R$ 10, Venda Direta para R$ 9, e lance próprio '
            . 'de R$ 11–12 nas cinco palavras com maior perda por ranking. Orçamento mantido em '
            . 'R$ 50/dia, que estava sobrando. Meta: parcela de impressões de 38% para 55–65%.',
    ],
    [
        'data' => '2026-08-17',
        'area' => 'medicao',
        'titulo' => 'Primeiro lead da campanha, com atribuição completa',
        'porque' => 'Marco da operação: a cadeia inteira fechou o ciclo pela primeira vez — '
            . 'clique no anúncio, landing page, uso do simulador, clique no WhatsApp, lead '
            . 'gravado com gclid, evento no GA4 e conversão importada de volta no Google Ads.',
        'efeito' => 'Lead de 14/08 19h, grupo Sistema MMN, faturamento simulado até R$ 50 mil '
            . '(faixa de R$ 500/mês), referência R94KZ. Custo por lead de R$ 36,99 — todo o '
            . 'investido até então. Revelou também uma falha de leitura: a coluna de conversões '
            . 'do Ads mostrava zero por atraso de importação, enquanto o lead já estava no banco. '
            . 'Cruzar as duas fontes daqui pra frente.',
    ],
    [
        'data' => '2026-08-17',
        'area' => 'site',
        'titulo' => 'LP /oferta/ passou a falar "marketing multinível"',
        'porque' => 'O Índice de Qualidade formou e deu nota 5/10 nas duas palavras pontuadas, '
            . 'com a experiência da página de destino marcada como ABAIXO DA MÉDIA (o anúncio, '
            . 'em contraste, ficou acima da média). Investigando: a palavra "multinível" não '
            . 'aparecia nenhuma vez na página, embora a campanha compre "sistema multinivel" e '
            . '"sistema marketing multinivel". Quem buscava o termo caía numa página que nunca '
            . 'o usava, e cujo título falava de desconto em vez do produto.',
        'efeito' => 'Title, description, H1, subtítulo e seção de recursos reescritos nomeando '
            . 'o produto. Nota baixa encarece o clique de todas as palavras, então a correção '
            . 'vale para a campanha inteira. Velocidade estava ok (0,09s, 46 KB) — não era isso.',
    ],
    [
        'data' => '2026-08-17',
        'area' => 'ads',
        'titulo' => '3 palavras do fim de semana e negativa de dropshipping',
        'porque' => 'O relatório de termos do fim de semana veio limpo pela primeira vez — '
            . 'nenhum recrutador, nenhum "mlm". Sobraram buscas qualificadas que não tínhamos '
            . '("site mmn", "site de marketing multinivel", "plataforma marketing multinivel") '
            . 'e um vazamento de outro mercado ("nuvemshop dropshipping").',
        'efeito' => '44 palavras positivas e 54 negativas.',
    ],
    [
        'data' => '2026-08-15',
        'area' => 'ads',
        'titulo' => 'Negativas por intenção: o cluster "quero entrar na rede"',
        'porque' => 'Dos 4 cliques de 14/08 (R$ 23,70), três eram público errado: '
            . '"empresas de marketing multinivel", "mlm" e "recrutador mmn". Ficou claro que '
            . 'não era azar — o vocabulário de MMN é dominado por quem quer ENTRAR numa rede, '
            . 'não por quem quer COMPRAR software. Bloquear termo a termo virou enxuga-gelo: '
            . 'a cada dia surgia variante nova a R$ 6 o clique.',
        'efeito' => '11 negativas de uma vez atacando a intenção (recrutador, recrutamento, '
            . 'ganhar dinheiro, renda extra, quero entrar, ser consultor...), totalizando 52.',
    ],
    [
        'data' => '2026-08-15',
        'area' => 'painel',
        'titulo' => 'Guideline do agente de tráfego pago',
        'porque' => 'O aprendizado estava espalhado por commits e conversas. Cada regra do '
            . 'documento nasceu de um erro que custou dinheiro ou de um diagnóstico que levou '
            . 'dias pra aparecer — a ordem certa de ler métricas, quando frase vaza e exata '
            . 'resolve, por que negativa não casa acento, quais alavancas mexer primeiro.',
        'efeito' => 'docs/guideline-agente-trafego-pago.md, atualizado a cada aprendizado novo.',
    ],
    [
        'data' => '2026-08-14',
        'area' => 'ads',
        'titulo' => '7 palavras novas e lance concentrado, escolhidos pelo Search Console',
        'porque' => 'Com o dado real de busca deu pra ver o que faltava: "sistema multinivel" '
            . 'tinha 77 impressões orgânicas e nem estava na campanha, e "software para marketing '
            . 'multinivel" tinha 84. Também ficou claro que "sistema mmn" é a maior demanda '
            . 'comercial (176 impressões) com a pior posição orgânica (25ª) — ou seja, o lugar '
            . 'onde a busca natural não nos salva e o anúncio precisa cobrir.',
        'efeito' => 'Campanha passou de 34 para 41 palavras. Lance de "sistema mmn" isolado em '
            . 'R$ 9 contra R$ 6 do resto do grupo. Mais 4 negativas informacionais '
            . '(340 impressões orgânicas com zero clique), totalizando 41.',
    ],
    [
        'data' => '2026-08-14',
        'area' => 'medicao',
        'titulo' => 'Search Console conectado — escolha de palavra deixou de ser palpite',
        'porque' => 'O GA4 não entrega termo de busca orgânica desde 2011 ("not provided"), '
            . 'então as 34 palavras da campanha tinham sido escolhidas por intuição — e 19 delas '
            . 'vieram marcadas pelo Google como "raramente veiculada". O Search Console mostra a '
            . 'busca real, o volume e em que posição estamos.',
        'efeito' => '90 dias de histórico já disponíveis: 7.320 impressões, 125 cliques, posição '
            . 'média 12,9. Revelou que "sistema mmn" tem 176 impressões com a gente na posição 25, '
            . 'e que "sistema multinivel" (77 impressões) nem estava na campanha.',
    ],
    [
        'data' => '2026-08-14',
        'area' => 'ads',
        'titulo' => '"sistema marketing multinivel" trocada de frase para exata',
        'porque' => 'Em correspondência de frase ela captava busca informativa: trouxe '
            . '"marketing multinivel" num dia e "empresas de marketing multinivel" no outro. '
            . 'Bloquear variante por variante era enxugar gelo — cada nova forma custava um '
            . 'clique de ~R$ 6 antes de dar pra negativar. Essa palavra respondeu por 75 das '
            . '120 impressões e pela maior parte do desperdício.',
        'efeito' => '',
    ],
    [
        'data' => '2026-08-14',
        'area' => 'ads',
        'titulo' => 'Negativa "empresas de marketing multinivel"',
        'porque' => 'O clique de R$ 5,97 do dia veio desse termo — gente procurando empresa '
            . 'para entrar numa rede, não software para vender. Em frase, pega também '
            . '"melhores empresas de marketing multinivel".',
        'efeito' => '',
    ],
    [
        'data' => '2026-08-13',
        'area' => 'ads',
        'titulo' => '14 negativas em correspondência exata',
        'porque' => 'Nos 3 primeiros dias, R$ 7,47 de R$ 13,29 (56%) foram numa única palavra '
            . 'captando busca genérica: "mmn no brasil", "multinivel moderno", "marketing '
            . 'multinivel funciona". Exata em vez de frase porque negativar "marketing '
            . 'multinivel" derrubaria também "sistema de marketing multinivel", que é comprador.',
        'efeito' => 'Impressões caíram de 57 para 16 no dia seguinte e o CTR subiu de 1,8% '
            . 'para 12,5% — menos gente vendo, gente mais certa.',
    ],
    [
        'data' => '2026-08-13',
        'area' => 'ads',
        'titulo' => 'Negativa "grátis" com acento',
        'porque' => 'A negativa "gratis" existia desde o início, mas a busca "mmn grátis" '
            . 'passou mesmo assim: negativa do Google não casa acento nem variação, cada '
            . 'forma precisa ser cadastrada.',
        'efeito' => '',
    ],
    [
        'data' => '2026-08-12',
        'area' => 'ads',
        'titulo' => '11 extensões: 4 sitelinks, 6 frases de destaque e telefone',
        'porque' => 'No primeiro dia a campanha pegava só 10% dos leilões elegíveis e perdia '
            . '90% por ranking (0% por orçamento — ou seja, não era falta de verba). A campanha '
            . 'tinha sido criada sem nenhuma extensão. Cada sitelink levou UTM própria pra '
            . 'saber qual puxa clique.',
        'efeito' => 'Parcela de impressões subiu de 10% para 60% em três dias; a perda por '
            . 'ranking caiu de 90% para 40%.',
    ],
    [
        'data' => '2026-08-11',
        'area' => 'ads',
        'titulo' => 'Campanha Promoção 10 Anos no ar',
        'porque' => 'Primeira campanha de Pesquisa do SVD: 5 grupos, 34 palavras, 20 negativas, '
            . 'R$ 50/dia, só Rede de Pesquisa, término em 31/08 junto com a promoção. As outras '
            . '4 campanhas (verticais) ficaram pausadas de propósito — dividir o pouco volume do '
            . 'nicho em cinco frentes impediria qualquer uma de aprender.',
        'efeito' => '',
    ],
    [
        'data' => '2026-08-11',
        'area' => 'medicao',
        'titulo' => 'Conversões do GA4 ativadas no Google Ads',
        'porque' => 'generate_lead, whatsapp_click e purchase estavam importadas mas ocultas — '
            . 'nesse estado o Google não conta conversão nenhuma, e a campanha rodaria cega. '
            . 'As ações marcadas como principais eram herdadas de outra conta (oficina mecânica), '
            . 'sujando a coluna de conversões com evento que nunca dispararia aqui.',
        'efeito' => 'generate_lead virou a conversão principal; whatsapp_click e purchase, '
            . 'secundárias. A categoria "obter rota" deixou de contar.',
    ],
    [
        'data' => '2026-08-11',
        'area' => 'ads',
        'titulo' => 'Campanha antiga "Sistema Venda Direta" removida',
        'porque' => 'Ela voltou a veicular sozinha quando o cartão foi cadastrado e gastou '
            . 'R$ 16,09 em um dia — 84% em Display, com clique acidental de celular indo pra '
            . 'home, 100% de rejeição e 2,1s de permanência.',
        'efeito' => 'Histórico de métricas preservado (remover no Google não apaga dado).',
    ],
    [
        'data' => '2026-08-10',
        'area' => 'painel',
        'titulo' => 'Painel reorganizado em abas e integrado ao Google Ads',
        'porque' => 'Filtro de período, drill-down de página e etapa do funil disputavam a mesma '
            . 'barra — ilegível. E não dava pra ver gasto e leads lado a lado sem abrir o Ads.',
        'efeito' => 'Quatro abas com filtro próprio, e a aba Investimento com as mesmas colunas '
            . 'do Google Ads (CTR, CPC, custo/conv., taxa de conversão), atualizada a cada 3h.',
    ],
    [
        'data' => '2026-08-10',
        'area' => 'medicao',
        'titulo' => 'Descoberto que a conta estava sem faturamento ativo',
        'porque' => 'Nenhuma campanha veiculava e a causa não aparecia em lugar nenhum: o último '
            . 'pagamento tinha sido em abril de 2024. Sem cartão válido o Google aceita ativar '
            . 'campanha, mas não veicula.',
        'efeito' => 'Cartão cadastrado; a veiculação voltou no mesmo dia.',
    ],
];
