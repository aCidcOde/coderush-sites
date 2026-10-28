<?php
declare(strict_types=1);

/*
[Modulo Promo SVD — fonte unica da campanha vigente]
@Author: André Gomes ( @acidcode )
@since 2026-08-17

Governa a faixa de promocao que aparece nos posts do blog e no indice. Existe pra
resolver um risco concreto: a promocao tem prazo e o blog tem 41 posts.
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

/*
 * CALENDARIO DE PROMOCOES — 27/10/2026.
 *
 * Ate aqui havia UMA promocao por vez, com nome e prazo em constante. Trocar de
 * promocao significava deploy no dia exato da virada; esquecer era repetir o
 * "ate 31/08" que ficou 12 dias vencido no ar. Agora a fila fica escrita e a
 * data escolhe: a vigente e a primeira cujo 'fim' ainda nao passou. A 10 Anos
 * vira Fim de Ano a meia-noite de 01/11 sem ninguem tocar no servidor.
 *
 * Depois da ultima, PROMO_DEADLINE fica com o 'fim' dela e promoAtiva() volta
 * false — o comportamento de "promocao encerrada" de sempre.
 *
 * 'vagas' => [total, ja fechadas] liga o contador "7 das 10 vagas". null desliga:
 * as vagas fechadas na 10 Anos NAO sao da promocao seguinte, e repetir o numero
 * seria escassez inventada. Sem vagas, a LP mostra quem ja opera, sem contagem.
 *
 * O relatorio diario (automation/ads/relatorio-diario.py) le o 'fim' daqui por
 * regex — manter o formato 'fim' => 'AAAA-MM-DD'.
 */
const PROMO_CALENDARIO = [
    ['fim' => '2026-10-31', 'nome' => 'Promoção 10 Anos', 'slug' => 'promo-10-anos',
     'selo' => 'Celebrando 10 anos', 'vagas' => [10, 7]],
    ['fim' => '2026-12-31', 'nome' => 'Promoção de Fim de Ano', 'slug' => 'promo-fim-de-ano',
     'selo' => 'Condição de fim de ano', 'vagas' => null],
];

/** A promocao que vale hoje — ou a ultima, ja vencida, se o calendario acabou. */
function promoVigente(): array
{
    /*
     * PREVIA — 28/10/2026. A promocao seguinte so aparece no dia da virada, e
     * validar texto e preco na meia-noite de 01/11 nao e validar. Com
     * ?previa=fim-de-ano (ou o slug inteiro) a pagina monta como se ja fosse a
     * vez dela. So aceita slug do calendario; noindex pra a URL com parametro
     * nao concorrer com a canonica.
     */
    $previa = (string) ($_GET['previa'] ?? '');
    foreach (PROMO_CALENDARIO as $p) {
        if ($previa !== '' && ($p['slug'] === $previa || $p['slug'] === 'promo-' . $previa)) {
            if (!headers_sent()) {
                header('X-Robots-Tag: noindex');
            }
            return $p;
        }
    }

    $hoje = (new DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d');
    foreach (PROMO_CALENDARIO as $p) {
        if ($p['fim'] >= $hoje) {
            return $p;
        }
    }
    return PROMO_CALENDARIO[array_key_last(PROMO_CALENDARIO)];
}

define('PROMO_DEADLINE', promoVigente()['fim']);
define('PROMO_NOME', promoVigente()['nome']);
// vai no utm_campaign dos links internos e do anuncio: separa no painel o lead
// de cada promocao sem depender da data do clique
define('PROMO_SLUG', promoVigente()['slug']);
define('PROMO_SELO', promoVigente()['selo']);
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
        ['Henovar Energy', 'https://henovar.com.br/loja'],
        ['Velaro Alianças', 'https://velaroaliancas.com.br/'],
        ['Accenti', 'https://accenti.sistemavendadireta.com.br/loja'],
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
            'loja' => 'https://henovar.com.br/loja',
            'texto' => 'Assinatura de energia renovável e marketplace na mesma rede: plano de carreira em 12 níveis, '
                . 'venda direta de 30% e desconto de afiliado aplicado pelo próprio sistema.',
        ],
        [
            'logo' => 'velaro', 'alt' => 'Velaro Alianças',
            'w' => 520, 'h' => 126, 'data' => 'No ar desde agosto de 2026',
            'loja' => 'https://velaroaliancas.com.br/',
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
            'loja' => 'https://accenti.sistemavendadireta.com.br/loja',
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

/** [total, ja fechadas] da promocao vigente, ou null quando ela nao tem contador. */
function promoVagas(): ?array
{
    return promoVigente()['vagas'];
}

/**
 * Nome da promocao sem acento, pra mensagem pre-preenchida do WhatsApp — as
 * mensagens do site sempre foram escritas assim ("Ola, vim pela Promocao...").
 */
function promoNomeAscii(): string
{
    return strtr(PROMO_NOME, ['ç' => 'c', 'ã' => 'a', 'á' => 'a', 'â' => 'a', 'é' => 'e', 'ê' => 'e',
        'í' => 'i', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ú' => 'u']);
}

/**
 * Imagem de compartilhamento (og:image) da promocao vigente.
 *
 * Era um og-oferta.jpg so, com o prazo DESENHADO na imagem — e o desenho dizia
 * "ATE 31/08" ate 27/10, dois meses depois de vencer: todo link da oferta mandado
 * no WhatsApp abria com promocao morta no card. Agora cada promocao tem a sua
 * (imagens/og-oferta-<slug>.jpg) e a troca acompanha o calendario. Sem o arquivo,
 * volta pra imagem antiga em vez de quebrar o card.
 */
function promoOgImagem(): string
{
    $arquivo = 'og-oferta-' . PROMO_SLUG . '.jpg';
    return is_file(__DIR__ . '/../imagens/' . $arquivo) ? '/imagens/' . $arquivo : '/imagens/og-oferta.jpg?v=3';
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
        $campanha = PROMO_SLUG;
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
