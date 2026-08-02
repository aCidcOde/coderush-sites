<?php
/*
[Modulo Cases SVD]
@Author: André Gomes ( @acidcode )
@since 2026-08-02
Fonte unica dos cases de clientes exibidos na home (#cases) e na pagina /cases/.
Caminhos de imagem sao relativos a raiz do site SVD; cada pagina aplica seu proprio prefixo.
*/

return [
    [
        'slug' => 'new-professionals',
        'name' => "New Professional's",
        'segment' => 'Cosmética capilar profissional • Paraguai',
        'period' => 'Entrega: julho de 2026',
        'logo' => 'imagens/clientes/new-professionals.webp',
        'logoFallback' => 'imagens/clientes/new-professionals.png',
        'logoWidth' => 480,
        'logoHeight' => 131,
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
        'slug' => 'protech-nutritional',
        'name' => 'Protech Nutritional',
        'segment' => 'Suplementos de alta performance • Brasil',
        'period' => 'Operação no ar',
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
];
