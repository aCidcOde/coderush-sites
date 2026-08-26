<?php
declare(strict_types=1);

/*
[Modulo Promo SVD — fonte unica da campanha vigente]
@Author: André Gomes ( @acidcode )
@since 2026-08-17

Governa a faixa de promocao que aparece nos posts do blog e no indice. Existe pra
resolver um risco concreto: a promocao tem prazo (31/08) e o blog tem 34 posts.
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

const PROMO_DEADLINE = '2026-08-31';
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
 * ATENCAO comercial: MedPlant e Zohr fecharam mas ainda nao pagaram. Aparecem
 * aqui como argumento de venda apenas — nao entram em receita, conversao nem
 * orcamento de midia enquanto o pagamento nao cair.
 */
function promoClientes(): array
{
    return [
        ['Accenti', 'https://parceiroaccenti.com.br/loja'],
        ["New Professional's", 'https://newprofessional.com.py/loja'],
        ['Protech', 'https://protech.sistemavendadireta.com.br/loja'],
        ['MedPlant', 'https://medplant.sistemavendadireta.com.br/loja'],
        ['Zohr Parfums', 'https://zohr.sistemavendadireta.com.br/loja'],
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
            'loja' => 'https://protech.sistemavendadireta.com.br/loja',
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
            'logo' => 'zohr', 'alt' => 'Zohr Parfums',
            'w' => 800, 'h' => 277, 'data' => 'No ar desde agosto de 2026',
            'loja' => 'https://zohr.sistemavendadireta.com.br/loja',
            'texto' => 'Perfumaria fina em três categorias — eau de parfum, fragrâncias para ambiente e corpo & banho — '
                . 'com dois planos de carreira independentes para consultor e distribuidor.',
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
            . '<div class="flex items-center rounded-xl bg-white px-4 py-3">'
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
    $destino = promoAtiva() ? '/oferta/' : '/';
    return $destino . '?utm_source=blog&utm_medium=conteudo&utm_campaign='
        . (promoAtiva() ? 'promo-10-anos' : 'organico-blog')
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
