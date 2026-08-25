<?php
declare(strict_types=1);

/*
[Pagina de produto — Sistema MMN]
@Author: André Gomes ( @acidcode )
@since 2026-08-25

POR QUE ESTA PAGINA EXISTE: "sistema mmn" e a maior demanda comercial do nicho —
182 impressoes em 90 dias — e estavamos na posicao 26,9, ou seja, pagina 3.
Nenhuma pagina do site era SOBRE isso: quem buscava caia na home, que fala de
"venda direta" primeiro e trata MMN como sinonimo secundario.

Artigo de blog rankeia pra duvida; pagina de produto rankeia pra produto. Esta e
de produto: o termo esta na URL, no title, no H1, no primeiro paragrafo e nos
subtitulos, sem repeticao forcada.

MEDICAO: baseline de 28 dias em docs/seo-medicao.md. Sem teste A/B classico —
o Google indexa uma URL so, e servir versao diferente pro mesmo endereco e
cloaking. O teste aqui e temporal, com baseline congelado.
*/

require_once __DIR__ . '/../inc/promo.php';

$seoBase = 'https://www.sistemavendadireta.com.br';
$seoUrl = $seoBase . '/sistema-mmn/';
$seoTitle = 'Sistema MMN: plataforma de marketing multinível pronta para operar';
$seoDescription = 'Sistema MMN completo com rede unilevel, escritório do consultor, loja virtual e '
    . 'financeiro integrados. Instalação a partir de R$ 3.000. Veja a demonstração sem cadastro.';

$whatsappHref = 'https://wa.me/5511994566726?text=' . rawurlencode(
    'Ola! Vi a pagina de Sistema MMN no site e quero saber mais.');
$demoHref = DEMO_URL . '?utm_source=site&utm_medium=sistema-mmn&utm_campaign=demo';

// FAQ em JSON-LD: e o formato que a busca e as IAs extraem como resposta direta.
$faq = [
    ['O que é um sistema MMN?',
     'É a plataforma que administra uma operação de marketing multinível: cadastro e rede de '
     . 'consultores, cálculo automático de comissões e bônus, escritório virtual de cada '
     . 'distribuidor, loja para venda ao consumidor e o financeiro que paga tudo isso. Sem ele, '
     . 'a rede é controlada em planilha — o que funciona até a primeira dezena de consultores.'],
    ['Quanto custa um sistema MMN?',
     'No Sistema Venda Direta a instalação parte de R$ 3.000 e a mensalidade começa em R$ 500, '
     . 'escalando conforme o faturamento da operação. A instalação inclui a parametrização do seu '
     . 'plano de negócio: a regra de comissão é configurada, não improvisada.'],
    ['Quanto tempo leva para colocar no ar?',
     'A New Professional\'s, no Paraguai, entrou em operação em 10 dias — com três idiomas, preço '
     . 'em guarani e 170 SKUs carregados. O prazo depende do tamanho do catálogo e da complexidade '
     . 'do plano, mas a plataforma já existe: não se constrói do zero.'],
    ['O sistema suporta plano unilevel e binário?',
     'Sim. A plataforma suporta rede unilevel, binária com troca de perna preferencial e comissão '
     . 'por cargo. Hoje recomendamos planos baseados em unilevel e carreira — o simulador de plano '
     . 'do site mostra por que a matemática costuma fechar melhor assim.'],
    ['Dá para testar antes de contratar?',
     'Sim, e sem cadastro. O ambiente de demonstração tem loja com produtos reais, escritório do '
     . 'consultor e painel administrativo, com as credenciais publicadas na própria página.'],
    ['O sistema funciona em outros países?',
     'Sim. Há operações rodando no Brasil, no Paraguai e na Bolívia, com múltiplos idiomas, '
     . 'múltiplas moedas e documento fiscal por país (CPF/CNPJ, C.I./RUC).'],
];
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>" />
  <link rel="canonical" href="<?= htmlspecialchars($seoUrl, ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="theme-color" content="#004AAD" />
  <link rel="icon" type="image/svg+xml" href="../favicon.svg" />
  <link rel="alternate icon" href="../favicon.ico" />

  <meta property="og:locale" content="pt_BR" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="<?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:url" content="<?= htmlspecialchars($seoUrl, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:site_name" content="Sistema Venda Direta" />
  <meta property="og:image" content="<?= htmlspecialchars($seoBase . '/imagens/og-oferta.jpg?v=3', ENT_QUOTES, 'UTF-8') ?>" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&amp;family=Roboto:wght@300;400;500;700&amp;display=swap" />
  <?php $cssVersion = (string) @filemtime(__DIR__ . '/../css/site-tailwind.css'); ?>
  <link rel="stylesheet" href="../css/site-tailwind.css?v=<?= htmlspecialchars($cssVersion, ENT_QUOTES, 'UTF-8') ?>" />
  <link rel="stylesheet" href="../css/site-optimizations.css?v=<?= htmlspecialchars($cssVersion, ENT_QUOTES, 'UTF-8') ?>" />
  <link rel="stylesheet" href="../css/styles.css?v=<?= htmlspecialchars($cssVersion, ENT_QUOTES, 'UTF-8') ?>" />

  <script type="application/ld+json">
  <?= json_encode([
      '@context' => 'https://schema.org',
      '@type' => 'SoftwareApplication',
      'name' => 'Sistema Venda Direta — Sistema MMN',
      'applicationCategory' => 'BusinessApplication',
      'operatingSystem' => 'Web',
      'description' => $seoDescription,
      'url' => $seoUrl,
      'offers' => [
          '@type' => 'Offer',
          'price' => (string) PROMO_INSTALL_AVISTA,
          'priceCurrency' => 'BRL',
          'description' => 'Instalação a partir de R$ ' . number_format(PROMO_INSTALL_AVISTA, 0, ',', '.')
              . '; mensalidade a partir de R$ 500.',
      ],
      'provider' => ['@type' => 'Organization', 'name' => 'Sistema Venda Direta', 'url' => $seoBase],
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
  </script>

  <script type="application/ld+json">
  <?= json_encode([
      '@context' => 'https://schema.org',
      '@type' => 'FAQPage',
      'mainEntity' => array_map(static fn ($p) => [
          '@type' => 'Question',
          'name' => $p[0],
          'acceptedAnswer' => ['@type' => 'Answer', 'text' => $p[1]],
      ], $faq),
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
  </script>

  <?php include __DIR__ . '/../inc/analytics.php'; ?>
</head>
<body class="bg-brand text-white antialiased font-[var(--font-body)] site-optimized">

  <header class="border-b border-white/10 bg-brand/95">
    <div class="mx-auto flex w-full items-center justify-between gap-4 px-4 py-3 sm:px-8">
      <a href="../"><img decoding="async" src="../imagens/Logo-Branco-1.webp" alt="Sistema Venda Direta" class="h-auto w-[150px] sm:w-[190px]" width="1000" height="300" loading="eager" /></a>
      <div class="flex items-center gap-4">
        <a href="<?= htmlspecialchars($demoHref, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="hidden text-sm font-semibold text-amber-300 hover:text-amber-200 sm:inline">Ver demonstração</a>
        <a href="../oferta/?utm_source=site&amp;utm_medium=sistema-mmn&amp;utm_campaign=promo-10-anos" class="rounded-full bg-amber-400 px-4 py-2 text-sm font-bold text-brand hover:bg-amber-300">Ver a promoção</a>
      </div>
    </div>
  </header>

  <main class="mx-auto max-w-[1000px] px-4 sm:px-6">

    <section class="grid items-center gap-8 py-10 lg:grid-cols-[1.1fr_1fr] lg:py-14">
      <div>
        <p class="inline-flex rounded-full border border-amber-300/50 bg-amber-400/15 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-amber-200">
          10 anos · Brasil, Paraguai e Bolívia
        </p>
        <h1 class="mt-4 font-[var(--font-heading)] text-3xl font-bold leading-[1.15] sm:text-4xl lg:text-[42px]">
          Sistema MMN pronto<br />para operar
        </h1>
        <p class="mt-4 text-base leading-relaxed text-white/90 sm:text-lg">
          Um sistema MMN administra o que a planilha não dá conta: a rede de consultores, o cálculo
          automático das comissões, o escritório virtual de cada distribuidor, a loja e o financeiro.
          O Sistema Venda Direta faz isso há 10 anos — a plataforma já existe, roda hoje e é
          parametrizada para o seu plano de negócio.
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
          <a href="<?= htmlspecialchars($demoHref, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"
             class="inline-flex items-center justify-center rounded-full bg-amber-400 px-6 py-3.5 text-sm font-bold uppercase tracking-wide text-brand hover:bg-amber-300">
            Entrar na demonstração
          </a>
          <a href="<?= htmlspecialchars($whatsappHref, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"
             class="inline-flex items-center justify-center rounded-full border border-white/50 px-6 py-3.5 text-sm font-bold uppercase tracking-wide hover:bg-white/10">
            Falar com especialista
          </a>
        </div>
        <p class="mt-3 text-xs text-white/60">A demonstração é um ambiente completo e não pede cadastro.</p>
      </div>
      <div class="overflow-hidden rounded-3xl border border-white/15 bg-white/5">
        <picture>
          <source srcset="../imagens/lp/hero-oferta.webp" type="image/webp" />
          <img src="../imagens/lp/hero-oferta.jpg"
               alt="Painel de um sistema MMN com a rede de consultores e os indicadores de comissão"
               class="h-full w-full object-cover" width="900" height="720" loading="eager" decoding="async" />
        </picture>
      </div>
    </section>

    <section class="border-t border-white/15 py-10">
      <h2 class="font-[var(--font-heading)] text-2xl font-bold sm:text-[30px]">O que o sistema MMN já traz pronto</h2>
      <div class="mt-2 h-1 w-[72px] rounded-full bg-amber-300"></div>
      <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        <?php
        $recursos = [
            ['Rede e genealogia', 'Árvore unilevel, binário com troca de perna preferencial e comissão por cargo — a regra é configurada na implantação.'],
            ['Escritório do consultor', 'Dashboard de ganhos, saldo, ativação, metas e ranking. É a tela em que o distribuidor vive.'],
            ['Comissões automáticas', 'Cálculo, fechamento e pagamento sem planilha. Bônus de indicação, níveis e graduação.'],
            ['Loja virtual integrada', 'Vitrine própria por consultor, carrinho com lucro calculado, frete e recompra em padrão e-commerce.'],
            ['Financeiro completo', 'Extrato, saque, aprovação de pedido com saldo e pagamento com bônus.'],
            ['Multi-idioma e multimoeda', 'PT, EN e ES, moeda local e documento fiscal por país. Operações no Paraguai e na Bolívia comprovam.'],
        ];
        foreach ($recursos as [$titulo, $texto]): ?>
          <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
            <h3 class="font-semibold text-amber-300"><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h3>
            <p class="mt-2 text-sm leading-relaxed text-white/85"><?= htmlspecialchars($texto, ENT_QUOTES, 'UTF-8') ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="border-t border-white/15 py-10">
      <h2 class="font-[var(--font-heading)] text-2xl font-bold sm:text-[30px]">Sistemas MMN rodando hoje</h2>
      <div class="mt-2 h-1 w-[72px] rounded-full bg-amber-300"></div>
      <p class="mt-3 text-base text-white/85">Cada uma dessas operações usa a plataforma. As lojas estão no ar — abra e confira.</p>
      <?= promoVitrine('../') ?>
    </section>

    <section class="border-t border-white/15 py-10">
      <h2 class="font-[var(--font-heading)] text-2xl font-bold sm:text-[30px]">Quanto custa um sistema MMN</h2>
      <div class="mt-2 h-1 w-[72px] rounded-full bg-amber-300"></div>
      <div class="mt-6 grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl border border-amber-300/40 bg-white/[0.07] p-5">
          <p class="text-xs font-semibold uppercase tracking-[0.16em] text-white/60">Instalação</p>
          <p class="mt-1 font-[var(--font-heading)] text-3xl font-bold text-amber-300">a partir de R$ <?= number_format(PROMO_INSTALL_AVISTA, 0, ',', '.') ?></p>
          <p class="mt-2 text-sm leading-relaxed text-white/85">
            Inclui a parametrização do seu plano de negócio, carga de catálogo, identidade da marca e
            o domínio no ar com certificado.
          </p>
        </div>
        <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <p class="text-xs font-semibold uppercase tracking-[0.16em] text-white/60">Mensalidade</p>
          <p class="mt-1 font-[var(--font-heading)] text-3xl font-bold">a partir de R$ 500</p>
          <p class="mt-2 text-sm leading-relaxed text-white/85">
            Escala conforme o faturamento da operação — quem fatura mais paga mais, quem está começando
            paga a faixa inicial. A tabela completa está na página da oferta.
          </p>
        </div>
      </div>
      <p class="mt-4 text-sm text-white/80">
        Quer conferir se o seu plano de comissões fecha a conta antes de contratar?
        <a href="../simulador/?utm_source=site&amp;utm_medium=sistema-mmn&amp;utm_campaign=simulador" class="font-semibold text-amber-300 underline decoration-amber-300/40 underline-offset-4 hover:text-white">Use o simulador de plano</a> —
        é gratuito e não pede cadastro.
      </p>
    </section>

    <section class="border-t border-white/15 py-10">
      <h2 class="font-[var(--font-heading)] text-2xl font-bold sm:text-[30px]">Perguntas frequentes sobre sistema MMN</h2>
      <div class="mt-2 h-1 w-[72px] rounded-full bg-amber-300"></div>
      <div class="mt-6 grid gap-3">
        <?php foreach ($faq as [$pergunta, $resposta]): ?>
          <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
            <h3 class="font-semibold"><?= htmlspecialchars($pergunta, ENT_QUOTES, 'UTF-8') ?></h3>
            <p class="mt-2 text-sm leading-relaxed text-white/85"><?= htmlspecialchars($resposta, ENT_QUOTES, 'UTF-8') ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="border-t border-white/15 py-10">
      <div class="rounded-[24px] border border-amber-300/40 bg-amber-400/10 p-6 sm:p-8">
        <h2 class="font-[var(--font-heading)] text-2xl font-bold">Veja o sistema MMN funcionando agora</h2>
        <p class="mt-3 max-w-3xl text-base leading-relaxed text-white/90">
          Loja, escritório do consultor e painel administrativo, com dados de uma operação real de 12
          meses. Sem cadastro, sem agendamento — as credenciais estão publicadas na própria página.
        </p>
        <div class="mt-5 flex flex-wrap gap-3">
          <a href="<?= htmlspecialchars($demoHref, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"
             class="inline-flex items-center justify-center rounded-full bg-amber-400 px-6 py-3.5 text-sm font-bold uppercase tracking-wide text-brand hover:bg-amber-300">
            Entrar na demonstração
          </a>
          <a href="../cases/" class="inline-flex items-center justify-center rounded-full border border-white/50 px-6 py-3.5 text-sm font-bold uppercase tracking-wide hover:bg-white/10">
            Ver os cases
          </a>
        </div>
      </div>
    </section>
  </main>

  <footer class="border-t border-white/15 bg-brand-dark/40">
    <div class="mx-auto flex max-w-[1000px] flex-col gap-3 px-4 py-8 sm:flex-row sm:items-center sm:justify-between sm:px-6">
      <p class="text-sm text-white/70">Sistema Venda Direta · sistema MMN e de venda direta há 10 anos.</p>
      <div class="flex flex-wrap gap-4 text-sm font-semibold">
        <a href="../" class="text-white/85 hover:text-white">Site</a>
        <a href="../cases/" class="text-white/85 hover:text-white">Cases</a>
        <a href="../simulador/" class="text-white/85 hover:text-white">Simulador</a>
        <a href="../blog/" class="text-white/85 hover:text-white">Blog</a>
        <a href="../oferta/" class="text-amber-300 hover:text-amber-200">Promoção 10 Anos</a>
      </div>
    </div>
  </footer>
</body>
</html>
