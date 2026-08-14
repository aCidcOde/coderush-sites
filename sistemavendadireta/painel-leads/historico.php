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
