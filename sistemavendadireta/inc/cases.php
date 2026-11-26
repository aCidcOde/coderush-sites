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
        'period' => 'Entrega: julho de 2026',
        'featured' => true,
        'logo' => 'imagens/clientes/new-professionals.webp',
        'logoFallback' => 'imagens/clientes/new-professionals.png',
        'logoWidth' => 480,
        'logoHeight' => 130,
        'url' => 'https://newprofessional.com.py/',
        'shots' => [
            ['src' => 'imagens/clientes/telas/new-professionals.webp', 'alt' => "Loja da New Professional's no ar, com domínio próprio", 'legenda' => "Loja da New Professional's no ar, em domínio próprio."],
        ],
        'summary' => 'Plataforma de venda direta no Paraguai: três idiomas, preço em guarani e comissões por cargo.',
        'highlights' => [
            'Loja, escritório e cadastro em PT / ES / EN',
            '170 SKUs em guarani, catálogo por linhas técnicas',
            'Comissões por cargo editáveis no admin',
            'Endereço via base postal oficial do país (8.644 zonas)',
        ],
        'details' => [
            [
                'title' => 'Internacionalização de verdade',
                'body' => 'Moeda sem casas decimais, documento por país (C.I./RUC, CPF/CNPJ, SSN/EIN) e ~900 chaves de '
                    . 'tradução — a plataforma inteira operando no padrão local.',
            ],
            [
                'title' => 'Identidade e experiência',
                'body' => 'Reskin completo na identidade da marca, escritório da consultora com metas Bronze a Diamante, '
                    . 'ranking mensal e recompra em padrão e-commerce.',
            ],
            [
                'title' => 'Segurança de lançamento',
                'body' => 'Domínio próprio com certificado automático e auditoria pré-lançamento: cookies seguros, '
                    . 'formulários blindados e redirecionamentos corrigidos. No ar em 10 dias.',
            ],
        ],
        'metrics' => [
            ['value' => '170', 'label' => 'SKUs na carga inicial'],
            ['value' => '3', 'label' => 'idiomas na plataforma'],
            ['value' => '10', 'label' => 'dias até entrar no ar'],
        ],
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
        'period' => 'No ar desde agosto de 2026',
        'featured' => true,
        'logo' => 'imagens/clientes/velaro.webp',
        'logoFallback' => 'imagens/clientes/velaro.png',
        'logoWidth' => 520,
        'logoHeight' => 126,
        'url' => 'https://velaroaliancas.com.br/',
        'shots' => [
            ['src' => 'imagens/clientes/telas/velaro.webp', 'alt' => 'Plataforma B2B da Velaro para lojistas e revendedores', 'legenda' => 'Plataforma exclusiva para lojistas e revendedores da Velaro.'],
        ],
        'summary' => 'Plataforma B2B de alianças para lojistas: catálogo público sem preço e condição comercial liberada só após aprovação do cadastro.',
        'highlights' => [
            'Exclusivo para lojistas — sem venda ao consumidor final',
            'Catálogo público sem preço interno exposto',
            'Preço e pedido liberados após aprovação do cadastro',
            'Cinco coleções, incluindo personalizadas',
        ],
        'details' => [
            [
                'title' => 'O catálogo mostra sem entregar o preço',
                'body' => 'Atacado tem um problema que varejo não tem: o preço de lojista não pode ficar '
                    . 'visível para o consumidor final, nem para o concorrente. A vitrine é pública para '
                    . 'atrair o revendedor, mas custo, condição e ferramenta de pedido só aparecem depois '
                    . 'que o cadastro é aprovado — a régua é do sistema, não do vendedor.',
            ],
            [
                'title' => 'Sob medida, não instalação',
                'body' => 'Diferente das operações de rede deste portfólio, a Velaro é sistema próprio em '
                    . 'Laravel construído sobre o CodaFácil. O modelo de negócio é atacado com cadastro '
                    . 'aprovado, não plano de carreira — e a plataforma foi feita para isso.',
            ],
        ],
        'metrics' => [
            ['value' => '5', 'label' => 'coleções no catálogo'],
            ['value' => 'B2B', 'label' => 'sem venda ao consumidor'],
        ],
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
        'segment' => 'Energia renovável e marketplace • Brasil',
        'period' => 'No ar desde setembro de 2026',
        'featured' => true,
        'logo' => 'imagens/clientes/henovar.webp',
        'logoFallback' => 'imagens/clientes/henovar.png',
        'logoWidth' => 520,
        'logoHeight' => 126,
        'url' => 'https://henovar.com.br/loja',
        'shots' => [
            ['src' => 'imagens/clientes/telas/henovar.webp', 'alt' => 'Site da Henovar Energy com loja e escritório virtual', 'legenda' => 'Henovar Energy: site, loja e escritório virtual no mesmo ecossistema.'],
        ],
        'summary' => 'Assinatura de energia renovável e marketplace na mesma rede, com plano de carreira de 12 níveis.',
        'highlights' => [
            'Assinatura de energia e venda de produto no mesmo cadastro',
            'Plano de carreira em 12 níveis',
            'Venda direta de 30% e rede em até nove níveis',
            'Escritório virtual com treinamento e páginas de negócio',
        ],
        'details' => [
            [
                'title' => 'Duas naturezas de receita na mesma rede',
                'body' => 'Assinatura de energia é recorrente e contínua; produto de marketplace é avulso e '
                    . 'pontual. Remunerar as duas no mesmo plano, sem dois sistemas e sem planilha de '
                    . 'conciliação, foi o que o projeto resolveu — o afiliado vê um extrato só.',
            ],
            [
                'title' => 'Desconto do afiliado como regra, não como cupom',
                'body' => 'Os 50% na compra de produto físico valem para quem é afiliado, aplicados pelo '
                    . 'próprio sistema no momento da compra. Evita a gambiarra de cupom manual, que sempre '
                    . 'vaza para quem não deveria ter.',
            ],
        ],
        'metrics' => [
            ['value' => '12', 'label' => 'níveis de carreira'],
            ['value' => '9', 'label' => 'níveis de rede'],
        ],
    ],
    [
        'slug' => 'protech-nutritional',
        'name' => 'Protech Nutritional',
        'segment' => 'Suplementos de alta performance • Brasil',
        'period' => 'Operação no ar',
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
        'summary' => 'Venda direta com distribuição exclusiva por consultores: toda compra nasce vinculada a um patrocinador.',
        'highlights' => [
            'Fluxo de indicação obrigatório na entrada da loja',
            'Catálogo em 9 linhas de produto',
            'Escritório virtual e pontos de entrega',
            'Plano com 3 formas de ganho',
        ],
        'details' => [
            [
                'title' => 'A regra do negócio no software',
                'body' => 'Quem chega sem patrocinador é direcionado a encontrar um consultor antes de comprar — '
                    . 'a exclusividade da rede garantida pelo próprio fluxo da loja.',
            ],
        ],
        'metrics' => [
            ['value' => '9', 'label' => 'linhas de produto'],
            ['value' => '3', 'label' => 'formas de ganho no plano'],
        ],
    ],
    [
        'slug' => 'emergency-saas',
        'name' => 'Emergency',
        'segment' => 'SaaS de certidões e documentos • Laravel',
        'period' => 'Em produção',
        'featured' => false,
        'logo' => 'imagens/clientes/planeta-emergency.webp',
        'logoFallback' => 'imagens/clientes/planeta-emergency.png',
        'logoWidth' => 420,
        'logoHeight' => 322,
        'url' => 'https://emergency.com.br/',
        'shots' => [
            ['src' => 'imagens/clientes/telas/emergency-saas.webp', 'alt' => 'Página da Emergency com certidões à venda online', 'legenda' => 'Emergency: catálogo de certidões com compra e entrega digital.'],
        ],
        'summary' => 'Desenvolvimento do SaaS emergency.com.br em Laravel, com a infraestrutura montada por nós — '
            . 'do servidor à produção.',
        'highlights' => [
            'SaaS em Laravel + Livewire com login 2FA',
            'Wizard de pedido em 3 etapas e carteira com saldo',
            'Infraestrutura completa: do servidor ao deploy',
            'Suporte por ticket dentro da plataforma',
        ],
        'details' => [
            [
                'title' => 'Tecnologia de ponta, operação real',
                'body' => 'Catálogo de certidões, pedidos guiados, pagamentos por carteira e autenticação reforçada — '
                    . 'construído em Laravel moderno e publicado numa infraestrutura que também montamos e operamos.',
            ],
        ],
        'metrics' => [
            ['value' => '2FA', 'label' => 'autenticação reforçada'],
            ['value' => '3', 'label' => 'etapas no wizard de pedido'],
            ['value' => '24/7', 'label' => 'acesso aos documentos'],
        ],
    ],
    [
        'slug' => 'emergency-erp',
        'name' => 'Emergency ERP',
        'segment' => 'ERP de gestão • Documentação imobiliária',
        'period' => 'Mantido e evoluído desde 2006',
        'featured' => false,
        'logo' => 'imagens/clientes/emergency-erp.webp',
        'logoFallback' => 'imagens/clientes/emergency-erp.png',
        'logoWidth' => 86,
        'logoHeight' => 98,
        'url' => 'https://sistemaemergency.com.br/',
        'summary' => 'ERP que roda a operação de ponta a ponta há quase 20 anos — refatorado e evoluído sem parar a empresa.',
        'highlights' => [
            'Refatoração do código-fonte sem parar a operação',
            'NFS-e de São Paulo no layout novo, com IBS e CBS',
            'Faturamento e boletos com integração bancária (Itaú)',
            'Financeiro integrado à Kamino e ao Salesforce',
            'Módulos financeiro, jurídico e fiscal',
            'Estoque, expedição e centro de custo',
        ],
        'details' => [
            [
                'title' => 'Legado que virou vantagem',
                'body' => 'Sistema de 2006 refatorado para versões modernas de PHP e MySQL, ganhando módulos novos '
                    . '— do comercial ao faturamento — sem um dia de operação parada.',
            ],
            [
                // 26/11/2026, pedido do usuario: "fala tb das integracoes das novas notas fiscais".
                // Conferido no codigo (classes/lib/NFP, nfpModel): layout 2 da PMSP com NBS, cClassTrib e cIndOp.
                'title' => 'Notas fiscais da reforma tributária',
                'body' => 'A NFS-e de São Paulo sai direto do ERP no layout 2, o que já traz o IBS e a CBS: código NBS, '
                    . 'classificação tributária e indicador de operação vêm do cadastro de cada serviço, e a nota é assinada e '
                    . 'enviada à prefeitura sem passar pelo portal. O recebimento segue para o financeiro em fluxo separado da nota.',
            ],
        ],
        'metrics' => [
            ['value' => '2006', 'label' => 'ano de origem do sistema'],
            ['value' => '15+', 'label' => 'anos de sustentação contínua'],
            ['value' => 'Itaú', 'label' => 'integração bancária'],
            ['value' => 'IBS/CBS', 'label' => 'na NFS-e de São Paulo'],
        ],
    ],
    [
        'slug' => 'gordon-codafacil',
        'name' => 'Agente Gordon',
        'segment' => 'Harness de IA com regras de negócio',
        'period' => 'Produto interno em uso',
        'featured' => false,
        'logo' => 'imagens/clientes/gordon-codafacil.webp',
        'logoFallback' => 'imagens/clientes/gordon-codafacil.png',
        'logoWidth' => 480,
        'logoHeight' => 175,
        // gordon.emergency.com.br respondia HTTP 500 em 26/11/2026 — sem link ate voltar
        'url' => null,
        'summary' => 'Harness de acesso a modelos de IA de ponta, com as regras do negócio no controle: fluxo, '
            . 'organização, mobile e auditoria completa.',
        'highlights' => [
            'Acesso a modelos de ponta sob regras de negócio',
            'Controle de fluxo e organização das conversas',
            'API mobile nativa desde o primeiro dia',
            'Auditoria e governança completas',
        ],
        'details' => [
            [
                'title' => 'IA com rédea curta',
                'body' => 'O agente não improvisa: cada acesso a modelo passa pelo harness, que aplica as regras do '
                    . 'negócio, registra tudo pra auditoria e mantém a governança — com conversas persistidas, uploads '
                    . 'e processamento assíncrono.',
            ],
        ],
        'metrics' => [
            ['value' => 'IA', 'label' => 'modelos de ponta'],
            ['value' => 'API', 'label' => 'mobile nativa'],
            ['value' => '100%', 'label' => 'auditável'],
        ],
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
    [
        // Embargo do cliente: NAO publicar ate liberacao. Trocar 'hidden' para false quando autorizado.
        'slug' => 'medplant',
        'name' => 'MedPlant',
        'segment' => 'Produtos naturais / saúde e bem-estar • Venda direta',
        'period' => 'No ar desde agosto de 2026',
        'featured' => false,
        'hidden' => true,
        'logo' => 'imagens/clientes/medplant.webp',
        'logoFallback' => 'imagens/clientes/medplant.png',
        'logoWidth' => 480,
        'logoHeight' => 123,
        'url' => 'https://medplant.sistemavendadireta.com.br/',
        'summary' => 'Escritório virtual e loja para venda direta: catálogo, pedidos, rede, extrato e comissões integrados.',
        'highlights' => [
            'Loja com catálogo e carrinho integrados',
            'Escritório virtual com extrato e comissões',
            'Gestão de rede de consultores',
        ],
        'details' => [],
        'metrics' => [],
    ],
];
