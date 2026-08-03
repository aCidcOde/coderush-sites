<?php
/*
[Modulo Cases SVD]
@Author: André Gomes ( @acidcode )
@since 2026-08-02
@updated 2026-08-02 (expansao: ecotrend, planeta/emergency, emergency erp, mauasinc, gordon; medplant oculto)
Fonte unica dos cases de clientes exibidos na home (#cases) e na pagina /cases/.
Caminhos de imagem sao relativos a raiz do site SVD; cada pagina aplica seu proprio prefixo.

Flags:
- 'featured' => true  : aparece na secao #cases da home (a pagina /cases/ mostra todos)
- 'hidden'   => true  : nao aparece em lugar nenhum (embargo do cliente) — ex.: MedPlant
- 'url'      => null  : card sem botao "Visitar operacao" (sistema interno)
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
        'summary' => 'Plataforma de venda direta no Paraguai: três idiomas, preço em guarani, plano de comissões '
            . 'por cargo e endereço resolvido pelo dataset oficial de código postal do país.',
        'highlights' => [
            'Loja, escritório e cadastro em PT / ES / EN',
            'Catálogo com 170 SKUs em guarani',
            'Motor de comissões por cargo, editável no admin',
            'Domínio próprio com certificado renovado automaticamente',
        ],
        'details' => [
            [
                'title' => 'Identidade e experiência',
                'body' => 'Reskin completo da plataforma na identidade da marca — dourado, marrom e off-white — na loja, '
                    . 'no escritório virtual e no administrativo. Logotipo horizontal, tema claro/escuro e revisão de contraste '
                    . 'e comportamento em celular. Os textos institucionais genéricos deram lugar ao que a marca realmente entrega, '
                    . 'escritos a partir do manual técnico.',
            ],
            [
                'title' => 'Catálogo',
                'body' => 'Carga inicial de 170 SKUs em guarani. As 31 linhas comerciais foram reagrupadas em 6 famílias com as '
                    . 'linhas em dropdown — antes o menu tinha 37 itens soltos. 131 produtos ganharam foto real e o restante usa '
                    . 'um placeholder próprio, em vez de imagem quebrada.',
            ],
            [
                'title' => 'Três idiomas',
                'body' => 'Português, espanhol e inglês em toda a plataforma — loja, cadastro, escritório e login —, com detecção '
                    . 'automática pelo navegador e seletor com bandeiras. São cerca de 900 chaves de tradução. O administrativo '
                    . 'ficou em português por decisão do cliente.',
            ],
            [
                'title' => 'Moeda',
                'body' => 'Conversão de real para guarani, incluindo a correção do formatador: a lógica antiga assumia centavos e '
                    . 'corrompia valores numa moeda que não tem casas decimais.',
            ],
            [
                'title' => 'Plano de negócio novo',
                'body' => 'Substituição do plano de pontos por um motor de comissões por cargo — 30% de venda pessoal, 10% e 5% de '
                    . 'rede e 5% de diretoria —, com percentuais editáveis pelo administrativo e auditoria de quem alterou o quê. '
                    . 'O cadastro internacional usa o documento definido pelo país: C.I. e RUC no Paraguai, CPF/CNPJ no Brasil, '
                    . 'SSN/EIN nos Estados Unidos.',
            ],
            [
                'title' => 'Loja e pagamento',
                'body' => 'Pagamento por transferência bancária com os dados do banco e botão de copiar, e o comprovante anexado na '
                    . 'própria tela do pedido. A operação não calcula frete: a entrega é combinada e paga no recebimento, e as telas '
                    . 'dizem isso. O cadastro do cliente foi refeito no padrão e-commerce.',
            ],
            [
                'title' => 'Endereço do Paraguai',
                'body' => 'Busca por código postal usando o dataset oficial da DINACOPA, com 8.644 zonas: o cliente digita o código e '
                    . 'departamento, cidade e bairro se preenchem sozinhos. Não existe API pública para isso no país.',
            ],
            [
                'title' => 'Escritório da consultora',
                'body' => 'Painel novo com faturamento do mês, evolução de vendas, produtos mais vendidos, ranking mensal em quatro '
                    . 'categorias e metas de Bronze a Diamante configuráveis pelo administrativo. O catálogo de recompra foi refeito '
                    . 'no padrão e-commerce.',
            ],
            [
                'title' => 'Infraestrutura e segurança',
                'body' => 'Migração para domínio próprio com certificado Let\'s Encrypt e renovação automática, sem depender de CDN de '
                    . 'terceiros. Junto vieram correções de segurança encontradas em auditoria: cookies de autenticação passaram a '
                    . 'exigir conexão segura, formulários públicos foram blindados contra injeção e uma rota de troca de idioma que '
                    . 'permitia redirecionamento para sites externos foi corrigida.',
            ],
        ],
        'metrics' => [
            ['value' => '170', 'label' => 'SKUs na carga inicial'],
            ['value' => '3', 'label' => 'idiomas na plataforma'],
            ['value' => '~900', 'label' => 'chaves de tradução'],
            ['value' => '8.644', 'label' => 'zonas de código postal'],
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
        'summary' => 'Escritório virtual da operação internacional da ForOne: rede binária e unilevel simultâneas, '
            . 'seis tipos de bônus — incluindo matching — e plano de carreira que vai da adesão à franquia.',
        'highlights' => [
            'Rede binária e unilevel rodando em paralelo',
            'Seis tipos de bônus, incluindo matching de equipe',
            'Plano de carreira em cinco níveis, até franquia',
            'Fechamento de bônus automatizado por rotinas programadas',
        ],
        'details' => [
            [
                'title' => 'Plano de compensação composto',
                'body' => 'A operação combina rede binária e unilevel na mesma árvore de consultores, com seis tipos de bônus '
                    . 'parametrizados — binário, fundador, indicação, indicação de franquia, matching e ativação mensal. '
                    . 'É o plano mais completo rodando sobre a plataforma.',
            ],
            [
                'title' => 'Carreira até a franquia',
                'body' => 'Cinco níveis de plano — da taxa de adesão a Bronze, Prata, Ouro e Franquia — com regras de qualificação '
                    . 'e bonificação próprias por nível, configuradas no administrativo.',
            ],
            [
                'title' => 'Fechamento automatizado',
                'body' => 'Rotinas programadas de 5 minutos a mensais processam volumes, ativações e fechamentos de bônus sem '
                    . 'intervenção manual — o consultor acompanha tudo no escritório virtual, com loja e treinamento integrados.',
            ],
        ],
        'metrics' => [
            ['value' => '6', 'label' => 'tipos de bônus no plano'],
            ['value' => '5', 'label' => 'níveis de carreira'],
            ['value' => '2', 'label' => 'redes em paralelo (binária + unilevel)'],
        ],
    ],
    [
        'slug' => 'mauasinc',
        'name' => 'MauaSinc',
        'segment' => 'Plataforma de operação corporativa • Grupo Mauá',
        'period' => 'Em evolução contínua',
        'featured' => true,
        'logo' => 'imagens/clientes/mauasinc.webp',
        'logoFallback' => 'imagens/clientes/mauasinc.png',
        'logoWidth' => 154,
        'logoHeight' => 82,
        'url' => null,
        'summary' => 'Plataforma única de operação do Grupo Mauá: CRM comercial, sincronização com o ERP TOTVS, financeiro '
            . 'multi-empresa, folha, produto de proteção Bem-Estar e dashboards analíticos — no lugar de planilhas e sistemas dispersos.',
        'highlights' => [
            'CRM com funil comercial e metas',
            'Sincronização de produtos, clientes e vendas com o TOTVS',
            'Financeiro escopado por empresa + desconto em folha',
            'Permissão em duas camadas: papel e módulo, com auditoria',
        ],
        'details' => [
            [
                'title' => 'Um lugar só para a operação',
                'body' => 'Processos que viviam em planilhas e sistemas dispersos foram unificados numa plataforma única: pipeline '
                    . 'comercial, integração com o ERP TOTVS (produtos, clientes e relatórios de venda), contas a pagar e receber '
                    . 'escopadas por empresa, desconto em folha, o produto de proteção Bem-Estar — contratos, comissões e metas — '
                    . 'e dashboards analíticos de vendas.',
            ],
            [
                'title' => 'Permissão e auditoria',
                'body' => 'Acesso em duas camadas: o papel (admin, gerente, vendedor, RH, funcionário) define o que a pessoa pode fazer '
                    . 'e o módulo define onde ela atua. Toda alteração relevante fica registrada em log de atividade — quem mudou o quê '
                    . 'e quando.',
            ],
            [
                'title' => 'Stack e ritmo',
                'body' => 'Laravel e Livewire, com relatórios em Excel, notificações push e tempo real via WebSocket. O produto tem '
                    . 'changelog versionado e comunicado de release por e-mail para os usuários a cada entrega.',
            ],
        ],
        'metrics' => [
            ['value' => '7', 'label' => 'módulos integrados'],
            ['value' => '2', 'label' => 'camadas de permissão (papel + módulo)'],
            ['value' => 'TOTVS', 'label' => 'ERP sincronizado'],
        ],
    ],
    [
        'slug' => 'planeta-emergency',
        'name' => 'Planeta Certidões → Emergency',
        'segment' => 'Documentos e certidões digitais • SaaS',
        'period' => 'Modernização em produção',
        'featured' => false,
        'logo' => 'imagens/clientes/planeta-emergency.webp',
        'logoFallback' => 'imagens/clientes/planeta-emergency.png',
        'logoWidth' => 420,
        'logoHeight' => 322,
        'url' => 'https://emergency.com.br/',
        'summary' => 'Serviço de solicitação e hospedagem de documentos digitais — certidões imobiliárias, fiscais e contratos. '
            . 'O legado em PHP virou um SaaS moderno com wizard de pedido, carteira com saldo e suporte por ticket.',
        'highlights' => [
            'Wizard de pedido em 3 etapas',
            'Catálogo de tipos de certidão',
            'Carteira com saldo e pagamento integrado',
            'Login com duplo fator (2FA)',
        ],
        'details' => [
            [
                'title' => 'Do legado ao SaaS',
                'body' => 'O Planeta Certidões nasceu como plataforma PHP de hospedagem de documentos com acesso 24/7. A nova geração, '
                    . 'no domínio Emergency, foi reescrita como SaaS em Laravel e Livewire, mantendo o serviço no ar durante a transição.',
            ],
            [
                'title' => 'Fluxo de pedido',
                'body' => 'O cliente escolhe o tipo de certidão num catálogo, preenche o pedido num wizard de três etapas e acompanha '
                    . 'tudo pela plataforma — com carteira de saldo para pagamento e suporte por ticket dentro do próprio sistema.',
            ],
            [
                'title' => 'Segurança',
                'body' => 'Autenticação com duplo fator (2FA) via Fortify e front em Tailwind. Documentos ficam hospedados com acesso '
                    . 'controlado por conta.',
            ],
        ],
        'metrics' => [
            ['value' => '3', 'label' => 'etapas no wizard de pedido'],
            ['value' => '2FA', 'label' => 'autenticação reforçada'],
            ['value' => '24/7', 'label' => 'acesso aos documentos'],
        ],
    ],
    [
        'slug' => 'ecotrend-afiliados',
        'name' => 'Ecotrend Afiliados',
        'segment' => 'Venda direta e afiliados • Brasil',
        'period' => 'Mais de 8 anos de operação',
        'featured' => true,
        'logo' => 'imagens/clientes/ecotrend-afiliados.webp',
        'logoFallback' => 'imagens/clientes/ecotrend-afiliados.png',
        'logoWidth' => 480,
        'logoHeight' => 149,
        'url' => 'https://escritorio.ecotrend.com.br/',
        'summary' => 'Operação de venda direta com escritório do associado, administrativo, loja e sistema de treinamento — '
            . 'incluindo emissão fiscal integrada (NF-e e DANFE) dentro da própria plataforma.',
        'highlights' => [
            'Escritório do associado e administrativo completos',
            'Emissão de NF-e e DANFE integrada (SPED)',
            'Sistema de treinamento para a rede',
            'Mais de 8 anos rodando a operação',
        ],
        'details' => [
            [
                'title' => 'Plataforma completa da rede',
                'body' => 'Escritório do associado, painel administrativo, loja virtual e sistema de treinamento numa única base. '
                    . 'É a operação mais longeva da plataforma: são mais de 8 anos gerenciando a rede de distribuidores da Ecotrend.',
            ],
            [
                'title' => 'Emissão fiscal integrada',
                'body' => 'Módulos SPED NF-e e DANFE dentro do próprio sistema: a operação emite nota fiscal eletrônica sem depender '
                    . 'de ferramenta externa — um requisito que elimina redigitação e erro de conciliação.',
            ],
            [
                'title' => 'Arquitetura por subdomínio',
                'body' => 'Escritório, loja e cadastro respondem em subdomínios próprios da marca, com a loja B2C atual integrada à '
                    . 'plataforma de e-commerce escolhida pelo cliente e o back-office da rede seguindo na base SVD.',
            ],
        ],
        'metrics' => [
            ['value' => '8+', 'label' => 'anos de operação contínua'],
            ['value' => 'NF-e', 'label' => 'emissão fiscal integrada'],
            ['value' => '4', 'label' => 'módulos: escritório, admin, loja, treinamento'],
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
        'summary' => 'Operação de venda direta com distribuição exclusiva por consultores: a entrada na loja passa pelo '
            . 'fluxo de indicação, garantindo que toda compra fique vinculada a um patrocinador.',
        'highlights' => [
            'Loja virtual com catálogo em 9 linhas de produto',
            'Escritório virtual do consultor',
            'Fluxo de indicação e vínculo com patrocinador',
            'Pontos de entrega e cadastro de novos consultores',
        ],
        'details' => [
            [
                'title' => 'Distribuição por consultor',
                'body' => 'A loja abre com o fluxo de indicação: quem chega sem patrocinador é direcionado para encontrar um consultor '
                    . 'antes de comprar. Isso mantém toda venda vinculada à rede, que é a regra do modelo de venda direta da marca.',
            ],
            [
                'title' => 'Catálogo e vitrine',
                'body' => 'Catálogo organizado em 9 linhas — creatina, multivitamínico, boost, termogênico, pré-treino, imunológico, '
                    . 'colágeno, hipercalórico, vegano e whey protein — com vitrine de destaques e navegação por categoria.',
            ],
            [
                'title' => 'Escritório virtual e oportunidade',
                'body' => 'Escritório virtual do consultor, página de cadastro na oportunidade, pontos de entrega e apresentação do plano '
                    . 'com as formas de ganho: lucro sobre vendas, bônus por equipe e bônus de performance.',
            ],
        ],
        'metrics' => [
            ['value' => '9', 'label' => 'linhas de produto'],
            ['value' => '3', 'label' => 'formas de ganho no plano'],
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
        'summary' => 'ERP que gerencia a operação da Emergency Documentação de ponta a ponta — do comercial ao faturamento, '
            . 'com cobrança por boletos e financeiro. Um legado de 2006 mantido, reestruturado e em produção até hoje.',
        'highlights' => [
            'Operação completa: comercial ao faturamento',
            'Cobrança com emissão de boletos',
            'Legado de 2006 reestruturado para novas versões',
            'Sustentação contínua há mais de 15 anos',
        ],
        'details' => [
            [
                'title' => 'Sustentação de legado que funciona',
                'body' => 'O sistema nasceu em 2006 e segue em produção: a plataforma foi reestruturada para novas versões de PHP e '
                    . 'MySQL sem parar a operação, ganhando novas funcionalidades ao longo do caminho. É o caso típico em que trocar '
                    . 'tudo custaria mais do que evoluir bem.',
            ],
            [
                'title' => 'Gestão de ponta a ponta',
                'body' => 'Do pedido comercial ao faturamento, passando por cobrança com boletos e controle financeiro — o ERP é o '
                    . 'sistema central da operação de documentação imobiliária da Emergency.',
            ],
        ],
        'metrics' => [
            ['value' => '2006', 'label' => 'ano de origem do sistema'],
            ['value' => '15+', 'label' => 'anos de sustentação contínua'],
        ],
    ],
    [
        'slug' => 'gordon-codafacil',
        'name' => 'Agente Gordon · CodaFácil',
        'segment' => 'Framework de aceleração + agente de IA',
        'period' => 'Produto interno em uso',
        'featured' => false,
        'logo' => 'imagens/clientes/gordon-codafacil.webp',
        'logoFallback' => 'imagens/clientes/gordon-codafacil.png',
        'logoWidth' => 480,
        'logoHeight' => 175,
        'url' => 'https://gordon.emergency.com.br/',
        'summary' => 'Scaffold Laravel reutilizável que acelera novos sistemas com base operacional pronta — site, painel, ACL, '
            . 'auditoria e API mobile — mais um módulo de agente de IA com conversas persistidas e processamento assíncrono.',
        'highlights' => [
            'Base pronta: site público + painel autenticado + backend com ACL',
            'CRM de clientes, produtos e pedidos',
            'Auditoria e changelog integrados',
            'Agente de IA com conversas persistidas e uploads',
        ],
        'details' => [
            [
                'title' => 'Por que um scaffold próprio',
                'body' => 'Todo sistema novo repetia as mesmas fundações: autenticação, permissões, CRUD de clientes e pedidos, '
                    . 'auditoria. O CodaFácil empacota isso num scaffold Laravel reutilizável — cada projeto novo começa com a base '
                    . 'operacional pronta e testada, e o tempo vai para a regra de negócio do cliente.',
            ],
            [
                'title' => 'O agente Gordon',
                'body' => 'Módulo de agente de IA embarcado no framework: conversas persistidas, upload de arquivos e processamento '
                    . 'assíncrono. É a mesma fundação que usamos para levar IA aos sistemas dos clientes, incluindo a implantação '
                    . 'assistida por IA da própria plataforma de venda direta.',
            ],
            [
                'title' => 'Pronto para mobile',
                'body' => 'API dedicada para aplicativos móveis desde o primeiro dia, com o mesmo controle de acesso do painel web.',
            ],
        ],
        'metrics' => [
            ['value' => 'Laravel', 'label' => 'base do framework'],
            ['value' => 'IA', 'label' => 'agente embarcado'],
            ['value' => 'API', 'label' => 'mobile nativa'],
        ],
    ],
    [
        // Embargo do cliente: NAO publicar ate liberacao. Trocar 'hidden' para false quando autorizado.
        'slug' => 'medplant',
        'name' => 'MedPlant',
        'segment' => 'Produtos naturais / saúde e bem-estar • Venda direta',
        'period' => 'Operação no ar',
        'featured' => false,
        'hidden' => true,
        'logo' => 'imagens/clientes/medplant.webp',
        'logoFallback' => 'imagens/clientes/medplant.png',
        'logoWidth' => 480,
        'logoHeight' => 123,
        'url' => 'https://medplant.sistemavendadireta.com.br/',
        'summary' => 'Escritório virtual e loja para operação de venda direta: catálogo, carrinho, pedidos, rede de consultores, '
            . 'extrato e comissões — tudo integrado na mesma plataforma.',
        'highlights' => [
            'Loja com catálogo e carrinho integrados',
            'Escritório virtual com extrato e comissões',
            'Gestão de rede de consultores',
        ],
        'details' => [
            [
                'title' => 'Operação integrada',
                'body' => 'Catálogo, carrinho, pedidos, rede de consultores, extrato e comissões numa única plataforma — o mesmo '
                    . 'núcleo de venda direta parametrizado para a marca.',
            ],
        ],
        'metrics' => [],
    ],
];
