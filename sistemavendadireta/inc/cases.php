<?php
/*
[Modulo Cases SVD]
@Author: André Gomes ( @acidcode )
@since 2026-08-02
@updated 2026-08-04 (copy enxuta pra escaneabilidade; ForOne detalhado; MauaSinc com contexto real;
Planeta removido; Emergency SaaS como case proprio; Ecotrend 10+ anos; ERP com modulos; Gordon renomeado)
Fonte unica dos cases exibidos na home (#cases) e na pagina /cases/.

Flags:
- 'featured' => true  : aparece na secao #cases da home
- 'hidden'   => true  : nao aparece em lugar nenhum (embargo) — ex.: MedPlant
- 'url'      => null  : card sem link externo (sistema interno)
*/

return [
    [
        'slug' => 'new-professionals',
        'name' => "New Professional's",
        'segment' => 'Cosmética capilar profissional • Paraguai',
        'period' => 'Plataforma em produção',
        'featured' => true,
        'logo' => 'imagens/clientes/new-professionals.webp',
        'logoFallback' => 'imagens/clientes/new-professionals.png',
        'logoWidth' => 480,
        'logoHeight' => 130,
        'url' => 'https://newprofessional.com.py/',
        'shots' => [
            ['src' => 'imagens/clientes/telas/new-professionals.webp', 'alt' => "Loja da New Professional's no ar, com domínio próprio", 'legenda' => "Loja da New Professional's no ar, em domínio próprio."],
        ],
        'summary' => 'Plataforma internacional de venda direta com loja, escritório virtual e gestão comercial em português, espanhol e inglês. Além da adaptação ao Paraguai, a operação agora possui uma estrutura mais completa de catálogo, estoque, hierarquia comercial e integração com ERP.',
        'highlights' => [
            'Cadastro de catálogo físico com SKU e composição dos produtos',
            'Estoque inicial, reservas e baixa automática após as vendas',
            'API de ERP com leitura e gravação auditadas',
            'Controle de lastro e movimentação logística',
            'Ranking por nível e consolidação das vendas das gestoras',
            'Gestão separada de salões, gestoras e consultoras',
            'Relatórios comerciais e de atendimento',
        ],
        'details' => [
            [
                'title' => 'Internacionalização além da tradução',
                'body' => 'A internacionalização não ficou limitada à tradução. Moeda, documentos, endereços, catálogo, estoque e hierarquia comercial foram adaptados à operação paraguaia. A evolução mais recente conecta produto, composição, reserva e baixa de estoque dentro de um fluxo auditável, integrado ao ERP.',
            ],
        ],
        'metrics' => [
            ['value' => '170', 'label' => 'SKUs na carga inicial'],
            ['value' => '3', 'label' => 'idiomas na plataforma'],
            ['value' => '10', 'label' => 'dias até entrar no ar'],
        ],
        'highlightsLabel' => 'Implementações recentes',
    ],
    [
        'slug' => 'forone',
        'name' => 'ForOne',
        'segment' => 'Venda direta multinível • Bolívia',
        'period' => 'Operação no ar',
        'featured' => true,
        'logo' => 'imagens/clientes/forone.webp',
        'logoFallback' => 'imagens/clientes/forone.png',
        'logoWidth' => 480,
        'logoHeight' => 224,
        'url' => 'https://oficina.foroneglobal.com/',
        'summary' => 'Operação boliviana de multinível: moeda adaptada pro boliviano, novo plano de negócios e novo layout.',
        'highlights' => [
            'Adaptação da plataforma pra moeda boliviana',
            'Novo plano de negócios: 6 tipos de bônus, incluindo matching',
            'Novo layout do escritório e da plataforma',
            'Alto volume de vendas direto pelo escritório virtual',
        ],
        'details' => [
            [
                'title' => 'Plano de compensação composto',
                'body' => 'Rede binária e unilevel em paralelo, com carreira em cinco níveis — da adesão à franquia — '
                    . 'e fechamento de bônus automatizado por rotinas programadas.',
            ],
            [
                'title' => 'Venda concentrada no escritório',
                'body' => 'A operação não usa loja virtual pública: o volume roda dentro do escritório do consultor, '
                    . 'com pedidos, recompra e ativação mensal no mesmo ambiente.',
            ],
        ],
        'metrics' => [
            ['value' => '6', 'label' => 'tipos de bônus no plano'],
            ['value' => '5', 'label' => 'níveis de carreira'],
            ['value' => 'Bs', 'label' => 'moeda local adaptada'],
        ],
    ],
    [
        'slug' => 'mauasinc',
        'name' => 'MauaSinc',
        'segment' => 'ERP de gestão multi-lojas • Vestuário e produção',
        'period' => 'Em evolução contínua',
        // 26/11/2026: saiu da home — ERP de vestuario sem link publico prova menos pra quem busca sistema de venda direta; segue em /cases/
        'featured' => false,
        'logo' => 'imagens/clientes/mauasinc.webp',
        'logoFallback' => 'imagens/clientes/mauasinc.png',
        'logoWidth' => 154,
        'logoHeight' => 82,
        'url' => null,
        'summary' => 'ERP que faz a gestão e o espelhamento de várias lojas do setor de vestuário e produção — '
            . 'atendendo operações de marcas como Lojas Leader e Vila Romana no norte do país.',
        'highlights' => [
            'Gestão e espelhamento de múltiplas lojas',
            'Sincronização com o TOTVS: produtos, clientes e vendas',
            'Financeiro multi-empresa, folha e CRM comercial',
            'Permissões em duas camadas com auditoria de tudo',
        ],
        'details' => [
            [
                'title' => 'Uma plataforma no lugar de planilhas',
                'body' => 'Pipeline comercial, integração TOTVS, contas a pagar/receber por empresa, desconto em folha '
                    . 'e dashboards analíticos — a operação inteira num só lugar, com dado confiável.',
            ],
            [
                'title' => 'Governança nativa',
                'body' => 'Papel define o que a pessoa faz; módulo define onde. Toda alteração registrada em log de '
                    . 'atividade, com changelog versionado e comunicado de release por e-mail a cada entrega.',
            ],
        ],
        'metrics' => [
            ['value' => '7', 'label' => 'módulos integrados'],
            ['value' => 'TOTVS', 'label' => 'ERP sincronizado'],
        ],
    ],
    [
        'slug' => 'ecotrend-afiliados',
        'name' => 'Ecotrend Afiliados',
        'segment' => 'Plataforma completa de rede • Brasil',
        'period' => 'Mais de 10 anos de operação',
        'featured' => true,
        'logo' => 'imagens/clientes/ecotrend-afiliados.webp',
        'logoFallback' => 'imagens/clientes/ecotrend-afiliados.png',
        'logoWidth' => 480,
        'logoHeight' => 149,
        'url' => 'https://escritorio.ecotrend.com.br/',
        'summary' => 'A operação em que mais atuamos: plataforma completa de rede há mais de 10 anos — loja, escritório, '
            . 'gestão de estoque e centro de distribuição.',
        'highlights' => [
            'Loja virtual + escritório + painel de gestão completos',
            'Controle de estoque e centro de distribuição',
            'Emissão fiscal integrada (NF-e e DANFE)',
            'Campanhas e integrações com o Google',
        ],
        'details' => [
            [
                'title' => 'Plataforma de ponta a ponta',
                'body' => 'Loja virtual, escritório do associado, painel administrativo, treinamento da rede, estoque e '
                    . 'centro de distribuição — tudo desenvolvido e evoluído pela nossa equipe ao longo de uma década.',
            ],
            [
                'title' => 'Fiscal e marketing integrados',
                'body' => 'NF-e e DANFE emitidos dentro do próprio sistema, e diversas campanhas e integrações com o '
                    . 'Google rodadas em conjunto com o cliente.',
            ],
        ],
        'metrics' => [
            ['value' => '10+', 'label' => 'anos de operação contínua'],
            ['value' => 'NF-e', 'label' => 'emissão fiscal integrada'],
            ['value' => 'CD', 'label' => 'centro de distribuição'],
        ],
    ],
    [
        'slug' => 'avig360',
        'name' => 'AVIG 360',
        'segment' => 'Saúde mental corporativa e NR-01 • Brasil',
        'period' => 'Programa de parceiros no ar',
        'featured' => false,
        'logo' => 'imagens/clientes/avig360.webp',
        'logoFallback' => 'imagens/clientes/avig360.png',
        'logoWidth' => 464,
        'logoHeight' => 88,
        'url' => 'https://avig360.com/',
        'shots' => [
            ['src' => 'imagens/clientes/telas/avig360.webp', 'alt' => 'Site da AVIG 360 no ar', 'legenda' => 'Site da AVIG 360 no ar, em vários idiomas.'],
        ],
        'summary' => 'Plataforma de conformidade com a NR-01 que usa o SVD como canal de parceiros de negócio.',
        'highlights' => [
            'Programa de parceiros com painel próprio',
            'Indicação e comissão sobre contratos fechados',
            'Cadastro e acompanhamento do parceiro',
            'Operação B2B, fora do varejo tradicional',
        ],
        'details' => [
            [
                'title' => 'Venda direta não é só produto físico',
                'body' => 'A AVIG 360 vende software corporativo por meio de parceiros que indicam contratos. '
                    . 'A mesma mecânica de rede, comissão e escritório do parceiro atende um ciclo de venda '
                    . 'B2B — sem catálogo, sem estoque, sem frete.',
            ],
        ],
        'metrics' => [],
    ],
    [
        // 16/11/2026: loja fora do ar (Cloudflare 522). Volta quando a loja responder.
        'slug' => 'accenti',
        'name' => 'Accenti',
        'segment' => 'Aromaterapia e bem-estar • Brasil',
        'period' => 'No ar desde junho de 2026',
        'featured' => true,
        'hidden' => true,
        'logo' => 'imagens/clientes/accenti.webp',
        'logoFallback' => 'imagens/clientes/accenti.png',
        'logoWidth' => 842,
        'logoHeight' => 461,
        'url' => 'https://accenti.sistemavendadireta.com.br/loja',
        'summary' => 'Operação de aromaterapia com cinco linhas de produto e venda por rede de consultores.',
        'highlights' => [
            'Cinco linhas: blends, essências, aromakit, óleos essenciais e vegetais',
            'Loja com catálogo por categoria e busca',
            'Cadastro de revendedor integrado à rede',
            'Escritório virtual do consultor',
        ],
        'details' => [
            [
                'title' => 'Catálogo que respeita o produto',
                'body' => 'Aromaterapia vende por composição e volume, não por foto genérica. O catálogo foi '
                    . 'estruturado em cinco linhas com apresentação por mililitragem, do jeito que o cliente '
                    . 'do segmento procura.',
            ],
        ],
        'metrics' => [
            ['value' => '5', 'label' => 'linhas de produto'],
        ],
    ],
    [
        // Primeira venda vinda da campanha do Google Ads (R$ 3.500, 26/08/2026).
        // NAO e instalacao do SVD: e sistema B2B proprio, feito em Laravel sobre o
        // CodaFacil. Fica junto da MauaSinc na leitura — sistema sob medida — e nao
        // junto das lojas de rede.
        'slug' => 'velaro',
        'name' => 'Velaro Alianças',
        'segment' => 'Atacado de alianças B2B • Brasil',
        'period' => 'Plataforma em produção',
        'featured' => true,
        'logo' => 'imagens/clientes/velaro.webp',
        'logoFallback' => 'imagens/clientes/velaro.png',
        'logoWidth' => 520,
        'logoHeight' => 126,
        'url' => 'https://velaroaliancas.com.br/',
        'shots' => [
            ['src' => 'imagens/clientes/telas/velaro.webp', 'alt' => 'Plataforma B2B da Velaro para lojistas e revendedores', 'legenda' => 'Plataforma exclusiva para lojistas e revendedores da Velaro.'],
        ],
        'summary' => 'Plataforma B2B para lojistas e revendedores, com catálogo público sem exposição de preços e acesso comercial liberado somente após análise do cadastro.',
        'highlights' => [
            'Consulta empresarial e triagem de elegibilidade por CNPJ e CNAE',
            'Aprovação e verificação de revendedores',
            'Tabelas e condições comerciais por revendedor',
            'Pedidos agrupados em lotes',
            'Pagamento pelo Mercado Pago',
            'Webhooks assinados, histórico de tentativas e auditoria dos pagamentos',
            'Portal financeiro e administrativo',
            'Cadastro de usuários internos com controle de acesso',
        ],
        'details' => [
            [
                'title' => 'Jornada B2B de ponta a ponta',
                'body' => 'O sistema controla toda a jornada B2B: atração pelo catálogo público, análise empresarial, aprovação do lojista, liberação da condição comercial, montagem do pedido e pagamento. As integrações financeiras registram eventos e tentativas, permitindo acompanhar problemas sem perder o histórico da operação.',
            ],
        ],
        'metrics' => [
            ['value' => '5', 'label' => 'coleções no catálogo'],
            ['value' => 'B2B', 'label' => 'sem venda ao consumidor'],
        ],
        'highlightsLabel' => 'Implementações recentes',
    ],
    [
        // Marca de colchoes que usa o SVD como escritorio virtual da rede de
        // revendedores; o site institucional (WordPress) segue separado.
        'slug' => 'haiflex',
        'name' => 'Haiflex',
        'segment' => 'Colchões tecnológicos • Brasil',
        'period' => 'Escritório virtual no ar',
        'featured' => false,
        // logo so existe em versao branca util; no card branco sumia (so aparecia
        // o "J" verde). 'logoBg' => 'escuro' resolve sem depender de arte nova.
        'logo' => 'imagens/clientes/haiflex-branca.webp',
        'logoFallback' => 'imagens/clientes/haiflex-branca.png',
        'logoBg' => 'escuro',
        'logoWidth' => 350,
        'logoHeight' => 100,
        'url' => 'https://escritoriovirtual.haiflex.com.br/',
        'summary' => 'Escritório virtual da rede de revendedores de uma indústria de colchões, separado do site institucional.',
        'highlights' => [
            'Escritório virtual do revendedor com acesso próprio',
            'Cadastro e primeiro acesso pelo próprio revendedor',
            'Convive com o site institucional em WordPress',
            'Venda direta da fábrica ao revendedor',
        ],
        'details' => [
            [
                'title' => 'Cada coisa no seu lugar',
                'body' => 'O site institucional vende colchão para quem compra; o escritório virtual '
                    . 'atende quem revende. São públicos e objetivos diferentes, e forçar os dois no '
                    . 'mesmo sistema costuma piorar os dois. Aqui o WordPress seguiu como vitrine e a '
                    . 'operação de rede entrou em subdomínio próprio.',
            ],
        ],
        'metrics' => [
            ['value' => 'Fábrica', 'label' => 'venda direta ao revendedor'],
        ],
    ],
    [
        // Fechado em 05/09/2026 pela campanha do Google Ads (grupo Sistema MMN).
        // Catalogo ainda com produtos de exemplo na instalacao — por isso o case
        // fala de ESTRUTURA e plano, nao de sortimento. Quando o catalogo real
        // subir, vale revisar highlights e metrics.
        'slug' => 'henovar',
        'name' => 'Henovar Energy',
        'segment' => 'Energia renovável, serviços e marketplace • Brasil',
        'period' => 'Plataforma em produção',
        'featured' => true,
        'logo' => 'imagens/clientes/henovar.webp',
        'logoFallback' => 'imagens/clientes/henovar.png',
        'logoWidth' => 520,
        'logoHeight' => 126,
        'url' => 'https://henovar.com.br/loja',
        'shots' => [
            ['src' => 'imagens/clientes/telas/henovar.webp', 'alt' => 'Site da Henovar Energy com loja e escritório virtual', 'legenda' => 'Henovar Energy: site, loja e escritório virtual no mesmo ecossistema.'],
        ],
        'summary' => 'Ecossistema de venda direta que combina contratação de energia, serviços financeiros, marketplace e plano de carreira em um único cadastro e extrato.',
        'highlights' => [
            'Jornada completa de contratação de energia',
            'Cadastro e validação da unidade consumidora',
            'Geração de proposta e contrato',
            'Bases específicas para cálculo das comissões de energia',
            'Separação entre comissão provisória e liberada',
            'Graduação automática na virada do período',
            'Saques com validação documental',
            'Tratamento de estornos e correções financeiras',
            'Integração dos serviços Limpa Nome e Clube Certo',
            'Pagamentos via PIX',
        ],
        'details' => [
            [
                'title' => 'Contrato recorrente virando rede rastreável',
                'body' => 'O desafio não era apenas vender energia, mas transformar contratos recorrentes em uma operação de rede rastreável. A plataforma acompanha a unidade consumidora, proposta, contrato, comissão, carreira e saque, enquanto mantém marketplace e serviços adicionais dentro do mesmo ecossistema.',
            ],
        ],
        'metrics' => [
            ['value' => '12', 'label' => 'níveis de carreira'],
            ['value' => '9', 'label' => 'níveis de rede'],
        ],
        'highlightsLabel' => 'Implementações recentes',
    ],
    [
        'slug' => 'protech-nutritional',
        'name' => 'Protech Nutritional',
        'segment' => 'Suplementos de alta performance • Brasil',
        'period' => 'Plataforma em produção',
        // 26/11/2026: entrou na home no lugar do MauaSinc — rede de suplementos com loja no ar (e tem LP propria de suplementos)
        'featured' => true,
        'logo' => 'imagens/clientes/protech-nutritional.webp',
        'logoFallback' => 'imagens/clientes/protech-nutritional.png',
        'logoWidth' => 480,
        'logoHeight' => 102,
        'url' => 'https://loja.protechnutritional.com.br/loja',
        'shots' => [
            ['src' => 'imagens/clientes/telas/protech-nutritional.webp', 'alt' => 'Loja virtual da Protech Nutritional', 'legenda' => 'Loja da Protech Nutritional no ar.'],
        ],
        'summary' => 'Operação de venda direta exclusiva por consultores, com loja, escritório virtual, centros de distribuição e integração com o ERP Bling.',
        'highlights' => [
            'Integração dos pedidos de loja e escritório com a API do Bling',
            'Sincronização e associação segura dos produtos do ERP',
            'Tratamento dos erros devolvidos pelo Bling',
            'Romaneio imprimível para separação de produtos',
            'Controle de estoque por centro de distribuição',
            'Proteção contra saldo negativo durante vendas simultâneas',
            'Centro de distribuição padrão configurável pelo consultor',
            'Retirada gratuita na matriz',
            'Confirmação do Mercado Pago por webhook, retorno e reconciliação automática',
            'Desconto configurável para compras do escritório',
        ],
        'details' => [
            [
                'title' => 'Exclusividade da indicação à expedição',
                'body' => 'A regra de exclusividade da rede começa na indicação do consultor e continua até a expedição. Pedido, pagamento, centro de distribuição, estoque, envio ao Bling e romaneio de separação fazem parte do mesmo fluxo operacional, com proteção contra estoque negativo e tratamento de erros de integração.',
            ],
        ],
        'metrics' => [
            ['value' => '9', 'label' => 'linhas de produto'],
            ['value' => '3', 'label' => 'formas de ganho no plano'],
        ],
        'highlightsLabel' => 'Implementações recentes',
    ],
    [
        // 26/11/2026: embargo levantado pelo usuario (briefing dos cases: "MEDPLANT — ADICIONAR NOVO CASE").
        'slug' => 'medplant',
        'name' => 'MedPlant',
        'segment' => 'Saúde e bem-estar • Venda direta • Brasil',
        'period' => 'Plataforma em produção',
        'featured' => false,
        'logo' => 'imagens/clientes/medplant.webp',
        'logoFallback' => 'imagens/clientes/medplant.png',
        'logoWidth' => 480,
        'logoHeight' => 123,
        'url' => 'https://medplant.sistemavendadireta.com.br/loja',
        'summary' => 'Plataforma de venda direta com loja virtual, escritório do consultor, consumidores, plano de carreira, bonificações, estoque e pontos de distribuição.',
        'highlights' => [
            'Loja virtual e escritório do consultor integrados',
            'Rede unilevel, carreira, qualificação e bonificações',
            'Fluxos separados para consumidor e consultor',
            'Conversão automática em consultor após o pagamento da adesão',
            'Controle de desconto ao longo do ciclo anual',
            'Campanhas de pontos e promoções',
            'Gestão de estoque, matriz, CDs e pontos de distribuição',
            'Registro da origem dos saldos utilizados nas compras dos CDs',
            'Ranking de vendas dos pontos de distribuição',
            'Integração com o Bling para pedidos, rastreio e informações fiscais',
            'Carteira, extrato, saques e movimentações auditáveis',
            'Med Club Prime e benefícios exclusivos para consultores',
        ],
        'details' => [
            [
                'title' => 'Venda, relacionamento e distribuição',
                'body' => 'A MedPlant reúne venda, relacionamento e distribuição em uma única plataforma. O sistema diferencia consumidor, consultor e ponto de distribuição, acompanha adesão e carreira, calcula bonificações e mantém o fluxo de estoque e expedição integrado ao ERP.',
            ],
            [
                'title' => 'Modernização estrutural',
                'body' => 'A operação também recebeu uma modernização estrutural para versões atuais de PHP e MySQL, preservando os cadastros, a rede e o histórico da empresa.',
            ],
        ],
        'metrics' => [],
        'shots' => [
            [
                'src' => 'imagens/clientes/telas/medplant.webp',
                'alt' => 'Loja virtual da MedPlant com banner do MedShake, menu de categorias e acesso ao escritório',
                'legenda' => 'Loja e escritório virtual da MedPlant',
            ],
        ],
        'highlightsLabel' => 'Destaques',
    ],
    [
        'slug' => 'emergency-saas',
        'name' => 'Emergency',
        'segment' => 'SaaS de certidões e documentos • Brasil',
        'period' => 'Plataforma em produção',
        'featured' => false,
        'logo' => 'imagens/clientes/planeta-emergency.webp',
        'logoFallback' => 'imagens/clientes/planeta-emergency.png',
        'logoWidth' => 420,
        'logoHeight' => 322,
        'url' => 'https://emergency.com.br/',
        'shots' => [
            ['src' => 'imagens/clientes/telas/emergency-saas.webp', 'alt' => 'Página da Emergency com certidões à venda online', 'legenda' => 'Emergency: catálogo de certidões com compra e entrega digital.'],
        ],
        'summary' => 'Plataforma de compra, emissão, acompanhamento e entrega digital de certidões, com automação comercial, pagamentos, assinaturas e recursos de inteligência artificial.',
        'highlights' => [
            'Vitrine pública com certidões, pacotes e jogos de diligência',
            'Compra rápida e pedidos com múltiplos titulares',
            'Recuperação de carrinho abandonado',
            'Relatório do funil para acompanhamento das campanhas de marketing',
            'Pagamentos e assinaturas pelo Mercado Pago',
            'Crédito mensal de assinatura aplicado aos serviços',
            'Pedidos internos liberados pela equipe, sem exibição de valores ao cliente',
            'Acesso temporário do cliente aos documentos',
            'Rastreamento de envio e abertura dos e-mails',
            'Pré-análise de certidões com inteligência artificial',
            'API mobile para pedidos, pagamentos, clientes, carteira e suporte',
        ],
        'details' => [
            [
                'title' => 'De fluxo de pedidos a ecossistema comercial',
                'body' => 'A plataforma evoluiu de um fluxo de pedidos para um ecossistema comercial completo. O cliente pode comprar certidões avulsas ou pacotes, pagar, acompanhar e receber digitalmente. A operação possui automações de recuperação, funil de marketing, pedidos internos e pré-análise assistida por IA, mantendo controle administrativo sobre cada etapa.',
            ],
        ],
        'metrics' => [
            ['value' => '2FA', 'label' => 'autenticação reforçada'],
            ['value' => '3', 'label' => 'etapas no wizard de pedido'],
            ['value' => '24/7', 'label' => 'acesso aos documentos'],
        ],
        'highlightsLabel' => 'Implementações recentes',
    ],
    [
        'slug' => 'emergency-erp',
        'name' => 'Emergency ERP',
        'segment' => 'ERP de gestão • Documentação imobiliária',
        'period' => 'Evoluído continuamente desde 2006',
        'featured' => false,
        'logo' => 'imagens/clientes/emergency-erp.webp',
        'logoFallback' => 'imagens/clientes/emergency-erp.png',
        'logoWidth' => 86,
        'logoHeight' => 98,
        'url' => 'https://sistemaemergency.com.br/',
        'summary' => 'ERP que sustenta a operação financeira, comercial, jurídica e fiscal da Emergency, modernizado continuamente sem interromper a empresa.',
        'highlights' => [
            'Emissão de NFS-e no Padrão Nacional',
            'Geração e consulta de DANFSe',
            'Integração operacional com Salesforce',
            'Integração de contratos e recebimentos com Kamino',
            'Auditoria do status dos contratos',
            'Relatórios financeiros por departamento',
            'Exibição de valor bruto, retenções e CNPJ nos relatórios',
            'Emissão e conciliação de recibos de adiantamento',
            'Evolução dos boletos e rotinas bancárias do Itaú',
        ],
        'details' => [
            [
                'title' => 'Modernização com a operação rodando',
                'body' => 'A modernização acontece enquanto a operação continua funcionando. Além dos módulos históricos, o ERP agora conversa com plataformas comerciais e financeiras modernas, atende ao Padrão Nacional da NFS-e e mantém auditoria dos contratos, pagamentos, retenções e documentos fiscais.',
            ],
        ],
        'metrics' => [
            [
                'value' => '2006',
                'label' => 'ano de origem do sistema',
            ],
            [
                'value' => '15+',
                'label' => 'anos de sustentação contínua',
            ],
            [
                'value' => 'Itaú',
                'label' => 'integração bancária',
            ],
        ],
        'shots' => [
            [
                'src' => 'imagens/clientes/telas/emergency-erp-marca.webp',
                'alt' => 'Marca da Emergency Documentação',
                'legenda' => 'Emergency Documentação: o ERP sustenta a operação interna da empresa.',
            ],
        ],
        'highlightsLabel' => 'Implementações recentes',
    ],
    [
        'slug' => 'gordon-codafacil',
        'name' => 'Agente Gordon',
        'segment' => 'Inteligência artificial corporativa • Agentes, documentos e integrações',
        'period' => 'Produto interno em uso',
        'featured' => false,
        'logo' => 'imagens/clientes/gordon-codafacil.webp',
        'logoFallback' => 'imagens/clientes/gordon-codafacil.png',
        'logoWidth' => 480,
        'logoHeight' => 175,
        // gordon.emergency.com.br respondia HTTP 500 em 26/11/2026 — sem link ate voltar
        'url' => null,
        'summary' => 'Ambiente de agentes de IA com regras de negócio, bases de conhecimento, ferramentas internas, canais de atendimento e governança de uso.',
        'highlights' => [
            'Bases de conhecimento com documentos e busca RAG',
            'Respostas com fontes e controle de prioridade das referências',
            'Pesquisa de jurisprudência com indicação da origem',
            'Upload e análise de documentos dentro da conversa',
            'Canais web, mobile, e-mail e WhatsApp',
            'Ferramentas integradas a sistemas internos e ERPs',
            'Permissões por usuário, empresa e agente',
            'Templates, skills, versões e experimentos de agentes',
            'Orçamento, consumo e custo auditáveis',
            'Insights automáticos e fluxos em segundo plano',
        ],
        'details' => [
            [
                'title' => 'IA corporativa governada',
                'body' => 'O Gordon transforma modelos de IA em agentes corporativos governados. Cada agente recebe permissões, conhecimento, ferramentas e orçamento próprios. Documentos, integrações e conversas permanecem rastreáveis, permitindo automação sem perder controle, contexto ou auditoria.',
            ],
        ],
        'metrics' => [
            ['value' => 'IA', 'label' => 'modelos de ponta'],
            ['value' => 'API', 'label' => 'mobile nativa'],
            ['value' => '100%', 'label' => 'auditável'],
        ],
        'shots' => [
            [
                'src' => 'imagens/clientes/telas/gordon-agentes.webp',
                'alt' => 'Base de conhecimento de um agente de IA com documentos indexados e prontos para consulta',
                'legenda' => 'Ecossistema de agentes do grupo: base de conhecimento de um agente (tela do AgenticHUB).',
            ],
        ],
        'highlightsLabel' => 'Implementações recentes',
    ],
    // ---- 26/11/2026: cases do grupo trazidos de coderush.com.br, codafacil.dev e dos produtos proprios
    // (pedido do usuario: "copia os clientes e resultados desses sites todos ... vamos por bastante case la").
    // Nenhum numero aqui e resultado inventado: so o que os proprios sites publicam.
    [
        'slug' => 'agentichub',
        'name' => 'AgenticHUB',
        'segment' => 'Plataforma corporativa de agentes de IA • BFR Intelligence',
        'period' => 'Produto do grupo em operação',
        'featured' => false,
        'logo' => 'imagens/clientes/agentichub.webp',
        'logoFallback' => 'imagens/clientes/agentichub.png',
        'logoWidth' => 480,
        'logoHeight' => 132,
        'url' => 'https://bfrintelligence.com.br/',
        'shots' => [
            ['src' => 'imagens/clientes/telas/agentichub-integracoes.webp', 'alt' => 'Tela de integrações do AgenticHUB com Google Drive, OneDrive, Slack, SMTP, Webhook e ERP', 'legenda' => 'Integrações: cada conexão com escopo e permissão próprios.'],
            ['src' => 'imagens/clientes/telas/agentichub-chat.webp', 'alt' => 'Conversa com um agente do AgenticHUB ajudando a criar um novo agente', 'legenda' => 'Agente conversando e guiando a criação de outro agente.'],
        ],
        'summary' => 'Harness corporativo de agentes de IA com governança: a empresa cria, opera e controla agentes '
            . 'conectados aos sistemas internos, à base de conhecimento e aos canais de atendimento — com custo e '
            . 'permissão sob controle.',
        'highlights' => [
            'Multiempresa: dados isolados e limite de custo por filial',
            'Whitelabel: logo, cores e domínio do cliente',
            'Canais Web, Mobile e WhatsApp com o contexto preservado',
            'Base de conhecimento com busca RAG (PDF, Word, planilhas, Drive)',
        ],
        'details' => [
            [
                'title' => 'Governança de verdade',
                'body' => 'Permissões por usuário, orçamento e métricas por agente, logs auditáveis e conformidade com a '
                    . 'LGPD. A empresa pode usar a própria chave de modelo (BYOK) — GPT-4o, Claude, Gemini ou modelo '
                    . 'privado — e há opção on-premise.',
            ],
            [
                'title' => 'Do pedido ao agente no ar',
                'body' => 'Criação de agente guiada por IA, com o prompt gerado a partir de poucas perguntas, teste A/B '
                    . 'entre versões e integração com APIs, bancos de dados, documentos, CRM/ERP e BI.',
            ],
            [
                'title' => 'Selecionada por Microsoft e AWS',
                'body' => 'A BFR Intelligence foi selecionada para os programas de startups Microsoft for Startups e '
                    . 'AWS Activate.',
            ],
        ],
        'metrics' => [
            ['value' => 'BYOK', 'label' => 'chave própria de modelo'],
            ['value' => 'RAG', 'label' => 'base de conhecimento dedicada'],
            ['value' => 'LGPD', 'label' => 'logs auditáveis'],
        ],
    ],
    [
        'slug' => 'wellby',
        'name' => 'Wellby',
        'segment' => 'Aplicativo de cuidado de idosos • iPhone e Apple Watch',
        'period' => 'Lançamento: novembro de 2026',
        'featured' => false,
        // sem logo em arquivo: o card mostra o nome em texto (ver cases/index.php)
        'logo' => '',
        'logoFallback' => '',
        'logoWidth' => 480,
        'logoHeight' => 130,
        'url' => 'https://wellby.com.br/',
        'shots' => [
            ['src' => 'imagens/clientes/telas/wellby.webp', 'alt' => 'Página do aplicativo Wellby com o check-in diário no iPhone e no Apple Watch', 'legenda' => 'Wellby: check-in no celular e no relógio, aviso à família quando algo foge do normal.'],
        ],
        'summary' => 'Aplicativo próprio do grupo para a família acompanhar à distância, sem câmera, o idoso que mora '
            . 'sozinho: check-in nos horários combinados e aviso em minutos quando algo foge do normal.',
        'highlights' => [
            'Check-in diário nos horários que a família combina',
            'Confirmação silenciosa por movimento e rotina',
            'Família avisada por WhatsApp, SMS e ligação',
            'Detecção de queda no celular e no Apple Watch',
        ],
        'details' => [
            [
                'title' => 'Feito para quem não usa aplicativo',
                'body' => 'O idoso responde pelo app, pelo relógio, por SMS, ligação ou WhatsApp. O alarme insiste até '
                    . 'ter resposta e respeita o horário de descanso, das 22h às 7h.',
            ],
            [
                'title' => 'Saúde no mesmo painel',
                'body' => 'Lembrete de remédio com aviso de quando vai acabar, relatório de batimento, oxigenação, sono e '
                    . 'passos, e integração com Oura, Whoop, Garmin e Fitbit.',
            ],
            [
                'title' => 'Assinatura e lançamento por etapas',
                'body' => 'Planos por assinatura com teste grátis, site em português, inglês e alemão, iPhone primeiro e '
                    . 'Android na sequência.',
            ],
        ],
        'metrics' => [
            ['value' => '3', 'label' => 'canais de aviso: WhatsApp, SMS e ligação'],
            ['value' => '14 dias', 'label' => 'de teste no plano completo'],
            ['value' => 'Watch', 'label' => 'app próprio no relógio'],
        ],
    ],
    [
        'slug' => 'isy-middleware',
        'name' => 'Isy Middleware',
        'segment' => 'Integração de lojas online com estoque centralizado',
        'period' => 'Em operação',
        'featured' => false,
        'logo' => '',
        'logoFallback' => '',
        'logoWidth' => 480,
        'logoHeight' => 130,
        // o sistema so tem tela de login publica
        'url' => null,
        'summary' => 'Middleware que integra os sistemas de vendas, estoque e financeiro de lojas online distribuídas, '
            . 'com estoque centralizado e processamento de grandes volumes de dados em tempo real.',
        'highlights' => [
            'Lojas online distribuídas, estoque centralizado',
            'Vendas, estoque e financeiro no mesmo fluxo',
            'Grandes volumes de dados em tempo real',
            'Múltiplas integrações e rotinas numa plataforma só',
        ],
        'details' => [
            [
                'title' => 'Integração no centro da operação',
                'body' => 'As integrações e rotinas da operação passam pelo middleware: os sistemas de vendas, estoque e '
                    . 'financeiro conversam por ele, com processamento em tempo real mesmo em volume alto.',
            ],
        ],
        'metrics' => [],
    ],
    [
        'slug' => 'hub-payments',
        'name' => 'Hub Payments',
        'segment' => 'Gateway de pagamentos e conciliação financeira',
        'period' => 'Em operação',
        'featured' => false,
        'logo' => 'imagens/clientes/hub-payments.webp',
        'logoFallback' => 'imagens/clientes/hub-payments.png',
        'logoBg' => 'escuro',
        'logoWidth' => 480,
        'logoHeight' => 156,
        'url' => null,
        'summary' => 'Sistema de gestão de pagamentos e conciliação financeira para empresas de médio e grande porte.',
        'highlights' => [
            'Gestão de pagamentos num só lugar',
            'Conciliação financeira integrada',
            'Feito para empresas de médio e grande porte',
            'Múltiplas tecnologias financeiras conectadas',
        ],
        'details' => [
            [
                'title' => 'Pagamento e conciliação juntos',
                'body' => 'Gateway de pagamentos com a conciliação financeira no mesmo sistema, para empresas que operam '
                    . 'com várias tecnologias financeiras ao mesmo tempo.',
            ],
        ],
        'metrics' => [],
    ],
    [
        // OCULTO ate confirmar: em 26/11/2026 liquitudo.com.br respondia 403 pro nosso servidor e pra
        // ferramenta de leitura, e a ultima versao legivel (Wayback, 17/05/2026) era pagina de outlet
        // fisico, nao loja online. O usuario descreve como e-commerce — liberar com prints/confirmacao.
        'slug' => 'liquitudo',
        'name' => 'Liquitudo',
        'segment' => 'Outlet das principais marcas • E-commerce',
        'period' => 'Em operação',
        'featured' => false,
        'hidden' => true,
        'logo' => '',
        'logoFallback' => '',
        'logoWidth' => 480,
        'logoHeight' => 130,
        'url' => 'https://liquitudo.com.br/',
        'summary' => 'Outlet das principais marcas — eletrônicos, eletrodomésticos e móveis — com cada produto '
            . 'classificado pelo estado de conservação.',
        'highlights' => [],
        'details' => [],
        'metrics' => [],
    ],
    // 26/11/2026: produto em desenvolvimento — vai em secao propria de /cases/, nunca na lista de producao
    [
        'slug' => 'monittora-pgr',
        'name' => 'Monittora PGR',
        'status' => 'desenvolvimento',
        'badge' => 'Protótipo funcional / prova de conceito',
        'segment' => 'Gestão de risco no transporte • Inteligência artificial',
        'period' => 'Prova de conceito em evolução',
        'featured' => false,
        'logo' => '',
        'logoFallback' => '',
        'logoWidth' => 480,
        'logoHeight' => 130,
        'url' => null,
        'semLinkLabel' => 'Acesso restrito ao piloto',
        'summary' => 'Plataforma que transforma apólices de seguro de transporte em Planos de Gerenciamento de Risco estruturados, revisáveis e aplicáveis à avaliação das viagens.',
        'highlightsLabel' => 'Destaques',
        'highlights' => [
            'Leitura de apólices em PDF com OCR e inteligência artificial',
            'Extração estruturada das regras de gerenciamento de risco',
            'Revisão humana de cada regra antes da publicação',
            'Indicação da cláusula e da página que originaram a regra',
            'Motor determinístico para liberar, alertar ou bloquear viagens',
            'Score de risco explicado em português',
            'Avaliação da viagem antes da saída e durante o trajeto',
            'Simulação de telemetria, perda de sinal, parada, atraso e jammer',
            'Visualização do trajeto e dos eventos em mapa',
            'Registro do resultado e geração de relatório auditável',
        ],
        'detailsLabel' => 'O que está sendo desenvolvido',
        'details' => [
            [
                'title' => 'Do documento de seguro à regra operacional',
                'body' => 'A proposta é transformar documentos extensos de seguro em regras operacionais utilizáveis. A IA ajuda a localizar e estruturar as exigências, mas a decisão permanece com o gestor. Depois de aprovado, o PGR passa a alimentar o motor que avalia cada viagem e revalida as condições durante o trajeto.',
            ],
        ],
        'metrics' => [],
    ],
];
