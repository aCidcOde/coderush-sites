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
        'featured' => true,
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
        'slug' => 'accenti',
        'name' => 'Accenti',
        'segment' => 'Aromaterapia e bem-estar • Brasil',
        'period' => 'No ar desde junho de 2026',
        'featured' => true,
        'logo' => 'imagens/clientes/accenti.png',
        'logoFallback' => 'imagens/clientes/accenti.png',
        'logoWidth' => 842,
        'logoHeight' => 461,
        'url' => 'https://parceiroaccenti.com.br/loja',
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
        'slug' => 'protech-nutritional',
        'name' => 'Protech Nutritional',
        'segment' => 'Suplementos de alta performance • Brasil',
        'period' => 'Operação no ar',
        'featured' => false,
        'logo' => 'imagens/clientes/protech-nutritional.webp',
        'logoFallback' => 'imagens/clientes/protech-nutritional.png',
        'logoWidth' => 480,
        'logoHeight' => 102,
        'url' => 'https://protech.sistemavendadireta.com.br/',
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
            'Módulos financeiro, jurídico e fiscal',
            'Faturamento e boletos com integração bancária (Itaú)',
            'Estoque, expedição e centro de custo',
        ],
        'details' => [
            [
                'title' => 'Legado que virou vantagem',
                'body' => 'Sistema de 2006 refatorado para versões modernas de PHP e MySQL, ganhando módulos novos '
                    . '— do comercial ao faturamento — sem um dia de operação parada.',
            ],
        ],
        'metrics' => [
            ['value' => '2006', 'label' => 'ano de origem do sistema'],
            ['value' => '15+', 'label' => 'anos de sustentação contínua'],
            ['value' => 'Itaú', 'label' => 'integração bancária'],
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
        'url' => 'https://gordon.emergency.com.br/',
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
