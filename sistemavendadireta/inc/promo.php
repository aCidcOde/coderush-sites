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
const DEMO_URL = 'https://zohr.sistemavendadireta.com.br/primeiros-passos';

const PROMO_DEADLINE = '2026-08-31';
const PROMO_NOME = 'Promoção 10 Anos';
const PROMO_INSTALL_DE = 5000;
const PROMO_INSTALL_PARCELADO = 3500;
const PROMO_INSTALL_AVISTA = 3000;

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
