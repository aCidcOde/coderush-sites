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
        'data' => '2026-10-27',
        'area' => 'medicao',
        'titulo' => 'Primeiro lead em 21 dias veio do anúncio — e entrou no banco como "home"',
        'porque' => 'Lead #50, 26/10: clique no anúncio do grupo Sistema MMN às 15h21, 9 minutos na '
            . '/sistema-mmn/, foi pra home e chamou no WhatsApp às 15h30 (o log do nginx mostra a '
            . 'sequência pelo IP). Gravou sem gclid e sem campanha porque quem guardava a atribuição na '
            . 'sessão era o js/scripts.js, e a /sistema-mmn/ não carrega esse arquivo. O Google Ads '
            . 'contou o clique de WhatsApp (pelo GA4); o painel contou orgânico.',
        'efeito' => 'A captura foi pro inc/analytics.php, que está no head de toda página. Testado no '
            . 'Chrome: entrada na /sistema-mmn/ com gclid, clique de zap na home, e o beacon leva gclid '
            . 'e UTMs. O lead #50 continua sem origem no banco — a correção retroativa ficou pendente.',
    ],
    [
        'data' => '2026-10-23',
        'area' => 'ads',
        'titulo' => 'Leitura do teto de R$ 9: o topo voltou e o clique subiu, mas devagar — mantido até 31/10',
        'porque' => 'Leitura combinada em 19/10. Quatro dias fechados (19-22/10) contra os sete anteriores, '
            . 'só no Sistema MMN: topo de 18-44% para 30-56% (meta 40% batida desde o dia 21), cliques '
            . 'de 1,4 para 2,5 por dia, gasto de R$ 8,40 para R$ 15,10 por dia. O CPC subiu todo dia — '
            . 'R$ 1,42, 5,23, 8,12, 8,58 — e passou de R$ 8 nos dois últimos.',
        'efeito' => 'O critério de reversão era "CPC acima de R$ 8 SEM o clique subir". O clique subiu 75%, '
            . 'abaixo da meta de 3 por dia, e cada clique a mais custou ~R$ 6, perto do CPC médio: não é '
            . 'a exposição marginal cara de 12/09. Mantido até o fim da campanha em 31/10 — mexer a oito '
            . 'dias do fim zera a calibração por pouco ganho. CTR do MMN foi de 4,7% para 5,4% com o pin, '
            . 'dentro do ruído. Zero lead nos quatro dias: 18 dias secos.',
    ],
    [
        'data' => '2026-10-22',
        'area' => 'medicao',
        'titulo' => '16 dias sem lead: o funil está de pé, e a seca tem precedente do mesmo tamanho',
        'porque' => 'Último lead real em 05/10. Desde 06/10, 70 cliques pagos (fora o robô de 11/10) e '
            . 'R$ 301 sem lead nenhum, nem pago nem orgânico. Conferido ponta a ponta: o anúncio traz '
            . 'gente (3 a 19 chegadas com gclid por dia no log), o clique simulado no Chrome dispara '
            . 'whatsapp_click e o beacon pro /zap-lead.php nas duas LPs, sem erro de JavaScript, e o '
            . 'endpoint responde. Não é medição quebrada: a pessoa chega e não chama.',
        'efeito' => 'Na taxa histórica (11 leads em ~270 cliques, ~4%) eram esperados 2-3 leads; zero por '
            . 'azar acontece ~7% das vezes. Improvável, mas já aconteceu: de 05/09 a 21/09 foram 71 '
            . 'cliques, R$ 400 e zero lead, e de 22/09 a 05/10 vieram 7. Achado no caminho: 2 dos 7 '
            . 'links da vitrine "quem já fechou" estão quebrados — parceiroaccenti.com.br expirou em '
            . '07/10 e não resolve, e a loja da MedPlant pede senha. É a prova que a página manda o '
            . 'visitante abrir pra ver o sistema rodando. Accenti passou pra '
            . 'accenti.sistemavendadireta.com.br no mesmo dia; a senha da MedPlant é de pré-lançamento '
            . 'e fica, porque a loja abre em poucos dias.',
    ],
    [
        'data' => '2026-10-19',
        'area' => 'ads',
        'titulo' => 'Pin do "Até 40% de Desconto" de volta na posição 1, nos 9 anúncios',
        'porque' => 'Na criação (10/08) o título da oferta foi fixado na posição 1: é o argumento que '
            . 'separa o anúncio dos concorrentes na mesma página. As trocas de data pelo anuncios.py '
            . 'remontavam os títulos só com o texto e soltavam a fixação. Em 19/10 nenhum anúncio tinha '
            . 'pin, e o título da oferta era sorteado como qualquer um dos 15.',
        'efeito' => 'Refixado com o --fixar novo, que manda a lista inteira de títulos e preserva fixação '
            . 'existente. Conferido depois: 15 títulos e 4 descrições em cada anúncio, os 9 aprovados. A '
            . 'promoção vai até 31/10 sem extensão (decidido em 19/10); depois entra oferta nova. Quebra '
            . 'a regra de uma variável por vez — pin e teto mudaram no mesmo dia. A separação na leitura '
            . 'de 22-23/10 é pelo indicador: o teto mexe na parcela de topo, o pin no CTR.',
    ],
    [
        'data' => '2026-10-19',
        'area' => 'ads',
        'titulo' => 'A queda não veio do nada: o leilão do Sistema MMN encareceu e o teto de R$ 7 virou freio',
        'porque' => 'As impressões caíram de ~100/dia para 24-54 a partir de 12/10, com duas causas '
            . 'sobrepostas. No grupo Venda Direta, a pausa de 10/10 tirou 90% do volume (de 30-74 para '
            . '2-9 impressões/dia) — esperado, e sem custo de lead. No Sistema MMN, de onde vieram 8 dos '
            . '11 leads pagos, a procura não mudou: ~75 buscas elegíveis por dia nas duas semanas '
            . '(impressões divididas pela parcela). O que caiu foi a nossa fatia do topo, de 40-75% '
            . '(28/09 a 10/10) para 18-44% (11 a 18/10), com o CPC médio encostado no teto: R$ 5,97 a '
            . 'R$ 6,96 entre 13 e 16/10. A meta da estratégia é 40% de topo; abaixo dela e pagando o '
            . 'teto, quem freia é o teto. Descartados antes: índice de qualidade estável no período (5 a '
            . '8 nas palavras que vendem), extensões todas aprovadas e veiculando, AdsBot recebendo 200 '
            . 'nas duas páginas de destino.',
        'efeito' => 'Teto de R$ 7 para R$ 9, meta mantida em 40%. É diferente de 12/09, quando subi meta '
            . '(20% para 70%) E teto (R$ 6 para R$ 12) e o CPC dobrou sem trazer clique: lá não havia '
            . 'volume sobrando; aqui sobram ~40 buscas/dia no MMN, e com a meta parada o Google só paga '
            . 'mais até recuperar os 40%. Leitura em 22-23/10, com dias úteis fechados: topo do MMN de '
            . 'volta a 40% ou mais e 3+ cliques/dia. Se o CPC passar de R$ 8 sem os cliques subirem — a '
            . 'assinatura de 12/09 — volta para R$ 7.',
    ],
    [
        'data' => '2026-10-19',
        'area' => 'ads',
        'titulo' => 'Os 11 leads pagos vieram de 7 palavras — "plataforma de venda direta" segue pausada',
        'porque' => 'Diante da queda de volume, a tentação era religar o que trazia impressão. Antes, '
            . 'cruzei o gclid de cada lead do banco com o click_view do Ads, que diz qual palavra gerou '
            . 'aquele clique exato — a pergunta é "o que vendeu", não "o que tinha volume".',
        'efeito' => 'sistema de venda direta (3 leads, um fechado), sistema multinivel (2), plataforma de '
            . 'mmn (2, um fechado), sistema mmn, software mmn, script mmn e site de marketing multinivel. '
            . 'Nenhum veio de "plataforma de venda direta" nem de "plataforma venda direta" desde agosto. '
            . 'Religá-las devolveria impressão, não lead. O esforço vai para a parcela das sete que '
            . 'vendem — por isso o teto, na entrada acima.',
    ],
    [
        'data' => '2026-10-19',
        'area' => 'ads',
        'titulo' => '"Comissões Automáticas" estava reprovado como conteúdo enganoso no anúncio do Venda Direta',
        'porque' => 'O anúncio aparecia como "aprovado (limitado)" com MISLEADING_CONTENT e ninguém tinha '
            . 'notado — o status do anúncio não diz qual texto caiu. Lendo título a título, era um só: '
            . '"Comissões Automáticas". Em anúncio de MMN o Google lê como promessa de ganho fácil. O '
            . 'mesmo título está APROVADO no anúncio do Sistema MMN.',
        'efeito' => 'Trocado por "Cálculo de Comissão por Cargo" só nesse anúncio — "comissão por cargo" '
            . 'já passava numa descrição aprovada. O do Sistema MMN não foi tocado: é o que gera lead, e '
            . 'editar manda o anúncio pra revisão. O anúncio segue no ar com os outros 14 títulos '
            . 'enquanto o novo é revisado — aprovado no mesmo dia, e o anúncio saiu de "aprovado '
            . '(limitado)" para aprovado sem restrição. De quebra: o pin do "Até 40% de Desconto" tinha sumido de '
            . 'todos os anúncios, porque o anuncios.py --trocar remontava os títulos só com o texto e '
            . 'soltava a fixação a cada troca de data. Corrigido no script.',
    ],
    [
        'data' => '2026-10-19',
        'area' => 'ads',
        'titulo' => 'Negativas entrar, login e amway — gente procurando acessar outro site',
        'porque' => '"amway entrar", "www amway do brasil entrar", "clubemultinivel login", "mmn login": '
            . 'buscas de navegação, a pessoa já sabe aonde quer ir. Em 69 dias somaram 34 impressões, 2 '
            . 'cliques e R$ 9,59, nenhuma conversão. Impressão sem clique não custa, mas puxa o CTR para '
            . 'baixo, e o CTR esperado entra no índice de qualidade.',
        'efeito' => 'Em frase, total de 102 negativas na campanha. Conferido antes que nenhuma busca com '
            . 'essas três palavras trouxe lead.',
    ],
    [
        'data' => '2026-10-16',
        'area' => 'ads',
        'titulo' => 'Teto voltou de R$ 4 para R$ 7 — o corte de 15/10 tirou o MMN do topo',
        'porque' => 'Em 15/10 o Sistema MMN fez 21 impressões, 0 clique e 20% de parcela: o leilão dele '
            . 'cobrava R$ 5,73-6,96 e o teto de R$ 4 deixou o anúncio fora das posições que recebem '
            . 'clique. Revertido às 16h43 de 16/10. Esta entrada só foi escrita em 19/10: a reversão '
            . 'apareceu no log de alterações da conta sem registro aqui, e o painel continuou dizendo '
            . 'R$ 4 por três dias.',
        'efeito' => 'Depois da reversão vieram 4 cliques entre 19h e 23h de 16/10. Mas o fim de semana '
            . 'fechou em 0 clique (29 e 24 impressões) já com R$ 7, contra 3 a 10 cliques nos fins de '
            . 'semana anteriores. O teto de R$ 4 explicava o dia 15, não a queda — ver 19/10.',
    ],
    [
        'data' => '2026-10-15',
        'area' => 'site',
        'titulo' => 'A queda no orgânico é migração de endereço, não perda — e eu subestimei o efeito',
        'porque' => 'As impressões caíram de ~120/dia para 42 no dia 12/10, batendo exatamente na '
            . 'troca de canonical do dia 10. Fui ver qual versão o Google tinha indexada: entre 25/09 '
            . 'e 09/10, 66% das impressões vinham do endereço COM www. Ou seja, ele estava mesmo '
            . 'indexando o www, apesar do 301 que mandava pra non-www.',
        'efeito' => 'Em quatro dias o www caiu de 66% para 18% das impressões — a migração está '
            . 'rápida e no sentido certo. Mas é troca de endereço de dois terços do site, e durante '
            . 'ela as páginas rendem menos. Quando fiz a correção eu tratei como "só um sinal '
            . 'contraditório, o sitemap já aponta pra non-www" e não avisei que haveria queda '
            . 'temporária. Estava certo no diagnóstico e errado no tamanho. Leitura só em 24/10: '
            . 'o que acompanhar é a proporção www/non-www chegar perto de 0/100, as impressões '
            . 'voltarem pra faixa de 100-140 e a posição média voltar pra ~8-9.',
    ],
    [
        'data' => '2026-10-13',
        'area' => 'site',
        'titulo' => 'Espaço de URL infinito queimava o orçamento de rastreamento: 13.998 respostas 200 em um dia',
        'porque' => 'Em 10/10 corrigi o try_files do sistemavendadireta pra devolver 404 em caminho '
            . 'inexistente, mas deixei os blocos do coderush.com.br e do codafacil.dev com o fallback '
            . 'antigo, que serve a HOME com status 200 pra qualquer endereço. Sozinho seria soft-404. '
            . 'Combinado com os links relativos da home (2026/07/18/x/, blog/, sem barra inicial) vira '
            . 'recursão infinita: caminho inexistente devolve a home, a home tem link relativo, o link '
            . 'vira o próximo nível do mesmo caminho, e assim por diante.',
        'efeito' => 'Em um único dia foram 13.908 respostas 200 nesse padrão — 12.065 do '
            . 'SERankingBacklinksBot e 1.886 do MJ12bot seguindo as próprias pegadas. Nenhuma pessoa. '
            . 'Provavelmente é parte da explicação de 26 dos 43 posts não terem uma impressão sequer: '
            . 'o Google gastava o orçamento de rastreamento no labirinto em vez das páginas reais. '
            . 'Depois do 404 e do bloqueio desses crawlers, caiu de 13.998 para 57 por dia. Googlebot '
            . 'e Bingbot não foram bloqueados — é o tráfego deles que paga a conta.',
    ],
    [
        'data' => '2026-10-13',
        'area' => 'medicao',
        'titulo' => 'Robô fez 59 cliques e queimou R$ 53,75 em seis horas',
        'porque' => 'Entre 21h de 11/10 e 02h50 de 12/10 entraram 18 cliques de WhatsApp, um a cada '
            . '1 a 40 minutos, madrugada adentro, e pararam de vez. Nenhum virou mensagem nem e-mail. '
            . 'O GA4 daqueles dias: 70 usos do simulador para 79 pageviews, 6 scrolls, 16 segundos de '
            . 'sessão média e visitante novo em 67 das 74 sessões. Pessoa real faz o contrário.',
        'efeito' => 'O Google filtrou ZERO cliques inválidos nesse dia — no dia 05/10 ele tinha pego '
            . '7 de 24. A diferença é que este tráfego usava navegador de verdade, cookie novo a cada '
            . 'visita e user agents variados de celular mediano: foi montado pra passar. Os 18 ficaram '
            . 'gravados com status "robo" em vez de apagados — descartar a única amostra do padrão '
            . 'seria perder a base da próxima detecção. A trava automática barra a partir do terceiro '
            . 'clique de zap em 60 minutos; o limiar veio do histórico, onde o máximo legítimo em uma '
            . 'hora sempre foi 2, contra 8 no pico.',
    ],
    [
        'data' => '2026-10-13',
        'area' => 'medicao',
        'titulo' => 'IP real do visitante nunca chegava ao sistema — corrigido nas duas pontas',
        'porque' => 'Todo lead do banco gravava 172.21.0.1 desde agosto, o gateway do Docker. A cadeia '
            . 'é Cloudflare -> nginx do host -> container, e nenhuma das duas pontas resolvia o endereço '
            . 'de origem. Sem isso não dá pra bloquear abuso nem pra abrir reclamação de clique inválido '
            . 'no Google com evidência de onde os cliques vieram.',
        'efeito' => 'A primeira tentativa configurou só o container e não funcionou — ele enxerga o IP '
            . 'do host, nunca o do Cloudflare. Precisava das duas pontas, cada uma confiando numa fonte '
            . 'diferente: o host lê CF-Connecting-IP das faixas do Cloudflare, o container lê X-Real-IP '
            . 'só da rede do Docker. X-Real-IP e não X-Forwarded-For de propósito: o primeiro sobrescreve '
            . 'o que o cliente enviar, o segundo acumula e deixaria alguém se passar pelo IP de origem. '
            . 'Conferido ponta a ponta: log do host e campo ip do lead passaram a gravar o endereço real.',
    ],
    [
        'data' => '2026-10-15',
        'area' => 'ads',
        'titulo' => 'Teto de CPC de R$ 7 para R$ 4 — a previsão do corte anterior não se confirmou',
        'porque' => 'Ao pausar as palavras com "plataforma venda direta" em 10/10, eu previ gasto de '
            . 'R$ 20-25/dia e CPC caindo pra faixa de R$ 2-3, porque "sistema de venda direta" entregava '
            . 'clique a R$ 1,06. O gasto caiu mais do que eu disse (R$ 13/dia) mas o CPC quase não se '
            . 'mexeu: de R$ 3,94 pra R$ 3,31, e nos dias 13 e 14 subiu pra R$ 6,55 e R$ 6,11.',
        'efeito' => 'O erro foi de leitura: assumi que o CPC histórico da palavra barata representaria '
            . 'o novo mix. Não representa. Quem sobrou gastando é o grupo Sistema MMN a R$ 5,73-6,96 o '
            . 'clique — a palavra barata trouxe 6 cliques, as caras trouxeram 5 por R$ 33. O teto em '
            . 'R$ 4 corta as caras sem mexer em palavra nenhuma, e é reversível num comando. Se em uma '
            . 'semana o volume continuar em 2-4 cliques/dia, o problema é que cortei demanda demais e '
            . 'alguma das palavras pausadas precisa voltar.',
    ],
    [
        'data' => '2026-10-10',
        'area' => 'ads',
        'titulo' => 'Não era "plataforma": era "plataforma + venda direta". Duas palavras pausadas',
        'porque' => 'As 5 palavras com "plataforma" gastaram R$ 156,79 em 7 dias com ZERO conversão, '
            . 'contra R$ 85,47 e 3 conversões das com "sistema". A tentação era pausar as cinco. Antes '
            . 'disso, olhei QUE BUSCA cada uma capturou em 30 dias — dado que já estava pago e respondia '
            . 'sem precisar de teste.',
        'efeito' => 'A separação é limpa. "plataforma de venda direta": R$ 148,36, 35 cliques, zero '
            . 'conversão, e 70% do gasto em marca ou marketplace (vdi R$ 46,71, shopee R$ 41,81, kaiross, '
            . 'dealers club, vendadireta.com.br). O resto é "venda direta online", pesquisa sobre o '
            . 'conceito. Nenhuma busca de produto, nenhuma. "plataforma venda direta": R$ 18,19, 100% '
            . 'marca. Já "plataforma mmn", "plataforma de mmn" e "plataforma marketing multinivel" '
            . 'capturaram só busca de produto ("sistema de mmn pronto", "erp mmn", "sites para mmn") e '
            . 'uma delas converteu. Pausadas as duas de venda direta; as três de MMN seguem. Em '
            . 'português, "plataforma de venda direta" é onde a pessoa VENDE (marketplace); "sistema de '
            . 'venda direta" é o software que ela OPERA — e esta última entrega clique a R$ 1,06 e foi '
            . 'de onde vieram as 2 conversas de 05/10.',
    ],
    [
        'data' => '2026-10-10',
        'area' => 'ads',
        'titulo' => '"vdi venda direta" era o termo que mais gastava — e eu tinha decidido não negativar',
        'porque' => 'R$ 46,71 em 10 cliques nos últimos 30 dias, zero lead. É busca de marca: alguém '
            . 'procurando a VDI, uma empresa de venda direta. Em 05/10 eu vi esse termo no relatório '
            . '(26 impressões, 4 cliques, R$ 14,19), considerei negativar e decidi NÃO, anotando que era '
            . '"arguably intenção relevante". Era navegacional. A decisão errada custou mais R$ 32 em '
            . 'quatro dias e puxou o dia 09/10 para R$ 74,81, o maior da campanha.',
        'efeito' => 'Negativadas vdi, dealers club, wedrop e atommn. A lição é de método: termo que é '
            . 'nome de empresa não se avalia por "parece relevante" — se a busca nomeia um concorrente '
            . 'ou um portal, a pessoa já sabe aonde quer ir e não é a nós. No mesmo dia o Google filtrou '
            . 'ZERO cliques inválidos, então o gasto foi todo de tráfego legítimo e mal mirado.',
    ],
    [
        'data' => '2026-10-05',
        'area' => 'site',
        'titulo' => 'Canonical de 13 páginas apontava para www, que redireciona para non-www',
        'porque' => 'Cada página escrita à mão declarava "a versão oficial é www.sistemavendadireta.com.br/..." '
            . 'e o servidor responde 301 de www para non-www. O sitemap sempre listou non-www. Os dois '
            . 'sinais que mais pesam na escolha da URL canônica se contradiziam em todo o site, e a home '
            . 'estava em posição média 14,4 com 894 impressões em 28 dias.',
        'efeito' => 'Os 41 posts do blog nunca tiveram o problema — o publisher usa o baseUrl do '
            . 'sites.json, que é non-www. O erro estava só no que foi escrito à mão, e justamente nas '
            . 'páginas comerciais: home, LP da promoção, /sistema-mmn/, /cases/, /simulador/, /blog/, '
            . '/inteligencia-artificial/, /wordpress/ e as 4 LPs de segmento. Leitura em ~3 semanas, '
            . 'que é o tempo de recrawl.',
    ],
    [
        'data' => '2026-10-05',
        'area' => 'medicao',
        'titulo' => '/sistema-mmn/ não disparava generate_lead — 30 dias de "0 conversões" falso',
        'porque' => 'A página tem cópia própria do script de rastreamento (não usa inc/zap-tracking.php) '
            . 'e só mandava whatsapp_click. O envio do formulário gravava no banco e não emitia evento '
            . 'nenhum. Como ela recebe o grupo de anúncio mais caro, o Ads marcou 0 conversões por 30 '
            . 'dias enquanto Karina (22/09), Francisco (23/09) e Alex Shimoda (01/10) entravam pelo form.',
        'efeito' => 'Quem lia o painel do Ads concluía que a campanha não converte; quem lia o banco via '
            . 'três leads. A ação generate_lead do GA4 está marcada como primária na conta — o lugar que '
            . 'deveria contar esperava um evento que ninguém enviava. Segue pendente na conta: a ação '
            . 'whatsapp_click está como NÃO primária, então os 6 cliques de zap de setembro caem em '
            . '"todas as conversões" e não na coluna que o relatório diário lê.',
    ],
    [
        'data' => '2026-10-05',
        'area' => 'site',
        'titulo' => 'LP da promoção era a única página indexável com preço e sem dado estruturado',
        'porque' => 'A /sistema-mmn/ emite SoftwareApplication, Offer e FAQPage desde que nasceu. A LP da '
            . 'promoção, destino de 3 dos 4 grupos de anúncio, não emitia nada — o preço e o prazo '
            . 'estavam escritos no corpo e no título do anúncio, mas em nenhum lugar que o Google leia '
            . 'como dado. Eloss e Aliadus, os dois concorrentes que mais aparecem na busca, publicam '
            . 'FAQPage e Offer.',
        'efeito' => 'As 7 perguntas do FAQ já existiam em HTML; faltava marcar. O priceValidUntil lê de '
            . 'PROMO_DEADLINE em vez de data cravada — foi data cravada em seis lugares que deixou o '
            . '"até 31/08" sobreviver 12 dias vencido. Fora da promoção a marcação volta ao valor cheio '
            . 'em vez de calar o preço.',
    ],
    [
        'data' => '2026-10-05',
        'area' => 'ads',
        'titulo' => '14 negativas novas; "shopee" era o termo que mais gastava',
        'porque' => '"sistema de venda direta na shopee" e "sistema de venda direta shopee" somaram 69 '
            . 'impressões, 11 cliques e R$ 41,81 em 14 dias — o maior gasto de termo da campanha, e é '
            . 'gente querendo vender dentro do marketplace, não empresa querendo montar operação de '
            . 'rede. Mesma raiz do erro da campanha Afiliados, pausada em 14/09.',
        'efeito' => 'Entraram em frase: shopee, kaiross, kairos, compradiretaempresas, dealerclub, '
            . 'polishop, pre lancamento/lançamento, clube multinivel/multinível. Em exata: marketing de '
            . 'rede, rede de marketing, multi seller, mlm network. Exata nos dois primeiros de propósito '
            . '— em frase matariam a palavra "sistema de marketing de rede", que é nossa e tem clique.',
    ],
    [
        'data' => '2026-10-05',
        'area' => 'site',
        'titulo' => 'Formulário das páginas do include não enviava atribuição',
        'porque' => 'O clique de WhatsApp manda gclid e UTM desde 14/09; o formulário das páginas que '
            . 'usam inc/zap-tracking.php (home, /cases/, /simulador/, /blog/, /inteligencia-artificial/) '
            . 'mandava só o que a pessoa digitou. Foi assim que o lead #24 (27/09) entrou pela home sem '
            . 'gclid, sem campanha e sem page_url.',
        'efeito' => 'Mesma rede de segurança do beacon: lê a URL primeiro e o sessionStorage depois, e '
            . 'manda a page_url sempre, porque em WebView o storage volta vazio. Não sobrescreve campo '
            . 'que a página já declara — a home continua marcando origem "home-svd".',
    ],
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
