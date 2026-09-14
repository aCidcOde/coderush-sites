<?php
declare(strict_types=1);

/*
[Modulo Promo SVD — fonte unica da campanha vigente]
@Author: André Gomes ( @acidcode )
@since 2026-08-17

Governa a faixa de promocao que aparece nos posts do blog e no indice. Existe pra
resolver um risco concreto: a promocao tem prazo (30/09) e o blog tem 34 posts.
Cravar o link em cada post significaria, no dia seguinte ao fim, 34 paginas
apontando pra uma oferta que nao existe mais.

Aqui a data manda. Passou do prazo, a faixa troca sozinha para uma chamada
neutra que continua convertendo — nenhum post precisa ser tocado.

ATENCAO: as 5 LPs em /oferta/* mantem a propria copia desses valores no topo do
arquivo. Ao mudar preco ou prazo, alinhar tambem la.
*/

// Ambiente de demonstracao navegavel (loja, escritorio do parceiro e admin), com
// credenciais publicadas e SEM cadastro. E a oferta de menor atrito que temos:
// concorrente dá "teste gratis 14 dias" exigindo cadastro; aqui a pessoa entra e
// usa. Estava no ar desde sempre e linkada em lugar nenhum.
const DEMO_URL = 'https://painel.sistemavendadireta.com.br/primeiros-passos';

const PROMO_DEADLINE = '2026-09-30';
const PROMO_NOME = 'Promoção 10 Anos';
const PROMO_INSTALL_DE = 5000;
const PROMO_INSTALL_PARCELADO = 3500;
const PROMO_INSTALL_AVISTA = 3000;

/**
 * Vagas ja fechadas da promocao, com a loja de cada uma no ar.
 *
 * E prova social de verdade: em vez de dizer "4 de 10 vagas preenchidas", mostra
 * QUEM preencheu e deixa a pessoa abrir a loja e ver o sistema rodando de
 * verdade, em cliente real. Vale mais que qualquer selo.
 *
 * ATENCAO comercial (atualizado 14/09/2026): a Zohr FOI REMOVIDA — nao fechou.
 * A MedPlant segue sem pagar; continua aqui como argumento de venda, com a loja
 * no ar, mas NAO entra em receita, conversao nem orcamento de midia.
 * Ja pagaram e estao no funil do painel: Velaro (R$ 3.500, 26/08) e Henovar
 * (R$ 5.000, 05/09) — as duas vindas da campanha do Google Ads.
 */
function promoClientes(): array
{
    return [
        ['Henovar Energy', 'https://henovar.sistemavendadireta.com.br/loja'],
        ['Velaro Alianças', 'https://velaro.sistemavendadireta.com.br/'],
        ['Accenti', 'https://parceiroaccenti.com.br/loja'],
        ["New Professional's", 'https://newprofessional.com.py/loja'],
        ['Protech', 'https://loja.protechnutritional.com.br/loja'],
        ['MedPlant', 'https://medplant.sistemavendadireta.com.br/loja'],
        ['AVIG 360', 'https://avig360.com/'],
    ];
}

/** Os nomes das vagas fechadas, cada um linkando pra loja do cliente. */
function promoClientesHtml(string $classe = 'underline decoration-white/40 underline-offset-2 hover:text-amber-300'): string
{
    $links = [];
    foreach (promoClientes() as [$nome, $url]) {
        $links[] = '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener"'
            . ' class="' . $classe . '">' . htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') . '</a>';
    }
    $ultimo = array_pop($links);
    return $links ? implode(', ', $links) . ' e ' . $ultimo : $ultimo;
}

/**
 * Cards da secao "Não é promessa — é operação rodando" das LPs.
 *
 * Ficava como HTML repetido nos 5 arquivos de /oferta/*; cada cliente novo dava
 * cinco edicoes. Agora entra aqui uma vez.
 *
 * 'data' e o que separa prova de promessa: dizer QUANDO entrou no ar cria
 * verificabilidade. 'loja' deixa a pessoa abrir e ver rodando.
 */
function promoVitrine(string $prefixo = '../'): string
{
    $cards = [
        [
            'logo' => 'henovar', 'alt' => 'Henovar Energy',
            'w' => 520, 'h' => 126, 'data' => 'No ar desde setembro de 2026',
            'loja' => 'https://henovar.sistemavendadireta.com.br/loja',
            'texto' => 'Assinatura de energia renovável e marketplace na mesma rede: plano de carreira em 12 níveis, '
                . 'venda direta de 30% e desconto de afiliado aplicado pelo próprio sistema.',
        ],
        [
            'logo' => 'velaro', 'alt' => 'Velaro Alianças',
            'w' => 520, 'h' => 126, 'data' => 'No ar desde agosto de 2026',
            'loja' => 'https://velaro.sistemavendadireta.com.br/',
            'texto' => 'Atacado de alianças exclusivo para lojistas: catálogo público sem preço interno, com custo e '
                . 'ferramenta de pedido liberados só depois que o cadastro do revendedor é aprovado.',
        ],
        [
            'logo' => 'haiflex-branca', 'alt' => 'Haiflex', 'fundo' => 'escuro',
            'w' => 350, 'h' => 100, 'data' => 'Escritório virtual no ar',
            'loja' => 'https://escritoriovirtual.haiflex.com.br/',
            'texto' => 'Indústria de colchões que usa o escritório virtual para a rede de revendedores, '
                . 'em subdomínio próprio e separado do site institucional.',
        ],
        [
            'logo' => 'accenti', 'alt' => 'Accenti',
            'w' => 842, 'h' => 461, 'data' => 'No ar desde junho de 2026',
            'loja' => 'https://parceiroaccenti.com.br/loja',
            'texto' => 'Aromaterapia em cinco linhas — blends, essências concentradas, óleos essenciais e '
                . 'vegetais — com loja do consultor, cadastro de revendedor e rede integrada.',
        ],
        [
            'logo' => 'new-professionals', 'alt' => "New Professional's",
            'w' => 480, 'h' => 130, 'data' => 'No ar desde julho de 2026',
            'loja' => 'https://newprofessional.com.py/loja',
            'texto' => 'Operação no Paraguai em três idiomas, preço em guarani, comissão por cargo editável no '
                . 'administrativo e endereço resolvido pela base oficial de código postal do país. No ar em 10 dias.',
        ],
        [
            'logo' => 'protech-nutritional', 'alt' => 'Protech Nutritional',
            'w' => 480, 'h' => 102, 'data' => 'No ar desde julho de 2026',
            'loja' => 'https://loja.protechnutritional.com.br/loja',
            'texto' => 'Suplementos com distribuição exclusiva por consultor: entrada na loja pelo fluxo de indicação, '
                . 'catálogo em 9 linhas, escritório virtual e plano com três formas de ganho.',
        ],
        [
            'logo' => 'medplant', 'alt' => 'MedPlant',
            'w' => 600, 'h' => 153, 'data' => 'No ar desde agosto de 2026',
            'loja' => 'https://medplant.sistemavendadireta.com.br/loja',
            'texto' => 'Cosméticos e suplementos naturais em quatro linhas — encapsulados, óleos, chás e cosméticos — '
                . 'com rede de consultores, recompra e centro de distribuição integrados.',
        ],
        [
            'logo' => 'avig360', 'alt' => 'AVIG 360',
            'w' => 464, 'h' => 88, 'data' => 'Programa de parceiros no ar',
            'loja' => 'https://avig360.com/',
            'texto' => 'Plataforma de saúde mental corporativa e conformidade com a NR-01 que usa o SVD '
                . 'como canal de parceiros: indicação, comissão e painel próprio do parceiro de negócios.',
        ],
        [
            'logo' => 'ecotrend-afiliados', 'alt' => 'Ecotrend Afiliados',
            'w' => 480, 'h' => 130, 'data' => 'Mais de 10 anos de operação',
            'loja' => null,
            'texto' => 'Programa de afiliados com link e cupom próprios por parceiro, rastreio de indicação e '
                . 'pagamento de bônus sem planilha.',
        ],
    ];

    $html = '<div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">';
    foreach ($cards as $c) {
        $e = static fn ($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
        $html .= '<article class="flex flex-col rounded-2xl border border-white/20 bg-white/5 p-5">'
            // justify-center: sem isso o logo cola na esquerda da caixa e a fileira de
            // cards fica desalinhada. 'fundo' => 'escuro' existe pra logo em versao
            // branca, que sumiria no branco — caso da Haiflex.
            . '<div class="flex items-center justify-center rounded-xl px-4 py-3 '
                . (($c['fundo'] ?? '') === 'escuro' ? 'bg-slate-900' : 'bg-white') . '">'
            // so aponta o webp se o arquivo existir — medplant e zohr vieram so em png
            . (is_file(__DIR__ . '/../imagens/clientes/' . $c['logo'] . '.webp')
                ? '<picture><source srcset="' . $e($prefixo . 'imagens/clientes/' . $c['logo'] . '.webp') . '" type="image/webp" />'
                : '<picture>')
            . '<img src="' . $e($prefixo . 'imagens/clientes/' . $c['logo'] . '.png') . '" alt="' . $e($c['alt'])
            . '" class="h-9 w-auto object-contain sm:h-11" width="' . (int) $c['w'] . '" height="' . (int) $c['h']
            . '" loading="lazy" /></picture></div>'
            . '<p class="mt-3 text-xs font-semibold uppercase tracking-[0.14em] text-amber-300">' . $e($c['data']) . '</p>'
            . '<p class="mt-2 flex-1 text-sm leading-relaxed text-white/90">' . $e($c['texto']) . '</p>';
        if ($c['loja']) {
            $html .= '<a href="' . $e($c['loja']) . '" target="_blank" rel="noopener"'
                . ' class="mt-4 inline-flex text-sm font-semibold text-amber-300 underline decoration-amber-300/40'
                . ' underline-offset-4 hover:text-white">Abrir a loja &rarr;</a>';
        }
        $html .= '</article>';
    }
    return $html . '</div>';
}

/** A promocao ainda esta valendo hoje? */
function promoAtiva(): bool
{
    return promoDiasRestantes() >= 0;
}

/** Dias ate o fim (negativo se ja passou). */
function promoDiasRestantes(): int
{
    $tz = new DateTimeZone('America/Sao_Paulo');
    $fim = new DateTimeImmutable(PROMO_DEADLINE . ' 23:59:59', $tz);
    $hoje = new DateTimeImmutable('now', $tz);
    return (int) $hoje->diff($fim)->format('%r%a');
}

/**
 * Prazo em dd/mm, para uso em texto de SEO e anuncio.
 *
 * Existe porque a data estava digitada a mao em cinco paginas de oferta e nos
 * nove anuncios da conta. Quando a promocao foi de 31/08 pra 30/09, tudo isso
 * ficou para tras: em 12/09 o site vendia com prazo 30/09 enquanto as meta
 * descriptions e os anuncios ainda diziam "ate 31/08". Prazo vencido nao e so
 * constrangimento — quem clica, compara e desiste, e no anuncio o clique a gente
 * paga do mesmo jeito. Derivar da constante e o que impede repetir.
 */
function promoPrazoCurto(): string
{
    return (new DateTimeImmutable(PROMO_DEADLINE))->format('d/m');
}

/** Desconto a vista, em % inteiro — calculado, nunca digitado. */
function promoDescontoPct(): int
{
    return (int) round((1 - PROMO_INSTALL_AVISTA / PROMO_INSTALL_DE) * 100);
}

/**
 * Link do destino com UTM, pra separar no painel o que veio de conteudo.
 * $origem identifica QUAL post trouxe (ex.: "post-governanca").
 */
function promoLink(string $origem = 'blog'): string
{
    // A home usa a mesma faixa dos posts, mas nao e blog: cravar utm_source=blog
    // faria o painel contar visita da home como se viesse de conteudo, e a
    // leitura de origem de lead deixaria de valer.
    $naHome = $origem === 'home';
    $fonte = $naHome ? 'site' : 'blog';
    $meio = $naHome ? 'interno' : 'conteudo';

    if (promoAtiva()) {
        $destino = '/sistema-venda-direta/';
        $campanha = 'promo-10-anos';
    } else {
        // Sem promocao, mandar pra "/" seria autolink quando a faixa esta NA
        // home. A demonstracao e o destino que sempre faz sentido: e o convite
        // concreto que a faixa ja anuncia no texto.
        $destino = $naHome ? DEMO_URL : '/';
        $campanha = $naHome ? 'organico-site' : 'organico-blog';
    }

    return $destino . '?utm_source=' . $fonte . '&utm_medium=' . $meio
        . '&utm_campaign=' . $campanha
        . '&utm_content=' . rawurlencode($origem);
}

/**
 * Faixa que os posts exibem. Enquanto a promocao vale, fala de prazo e preco;
 * depois, vira convite neutro pro produto — o post nunca fica com chamada morta.
 */
function promoStrip(string $origem = 'blog'): string
{
    $href = htmlspecialchars(promoLink($origem), ENT_QUOTES, 'UTF-8');
    $dias = promoDiasRestantes();

    if (promoAtiva()) {
        $prazo = $dias === 0 ? 'último dia' : ($dias === 1 ? 'último dia amanhã' : "faltam {$dias} dias");
        $titulo = 'Sistema de marketing multinível e venda direta por R$ '
            . number_format(PROMO_INSTALL_AVISTA, 0, ',', '.');
        $texto = PROMO_NOME . ': até ' . promoDescontoPct() . '% de desconto na instalação — ' . $prazo . '.';
        $rotulo = 'Ver a promoção';
    } else {
        $titulo = 'Sistema de marketing multinível e venda direta pronto para operar';
        $texto = 'Rede binária e unilevel, escritório do consultor, loja e financeiro integrados. '
            . 'Rodando no Brasil, Paraguai e Bolívia.';
        $rotulo = 'Conhecer o sistema';
    }

    // mt-6 separa do botao "Voltar para o site principal", que fica logo acima;
    // embaixo quem da o respiro e o mt-5 do <article>, entao nao leva mb aqui
    // O link da demonstracao anda junto: quem le um artigo tecnico costuma querer
    // VER o sistema antes de falar com alguem. Sem cadastro, o atrito e zero.
    $demo = htmlspecialchars(DEMO_URL . '?utm_source=blog&utm_medium=conteudo&utm_campaign=demo&utm_content='
        . rawurlencode($origem), ENT_QUOTES, 'UTF-8');

    return '<aside class="mt-6 flex flex-col gap-3 rounded-2xl border border-amber-300/40 bg-amber-400/10 p-4 sm:flex-row sm:items-center sm:justify-between">'
        . '<div><p class="font-semibold text-amber-200">' . htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') . '</p>'
        . '<p class="mt-1 text-sm text-white/80">' . htmlspecialchars($texto, ENT_QUOTES, 'UTF-8') . '</p>'
        . '<p class="mt-2 text-sm"><a href="' . $demo . '" class="font-semibold text-amber-200 underline decoration-amber-300/50 underline-offset-4 hover:text-white">'
        . 'Ou entre na demonstração agora</a> <span class="text-white/60">— loja, escritório e painel, sem cadastro.</span></p></div>'
        . '<a href="' . $href . '" class="inline-flex flex-shrink-0 items-center justify-center rounded-full bg-amber-300 px-5 py-2.5 text-sm font-bold uppercase tracking-[0.12em] text-brand hover:bg-amber-200">'
        . htmlspecialchars($rotulo, ENT_QUOTES, 'UTF-8') . '</a>'
        . '</aside>';
}
