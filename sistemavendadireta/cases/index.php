<?php
/*
[Modulo Cases SVD — pagina dedicada]
@Author: André Gomes ( @acidcode )
@since 2026-08-02
Pagina de cases de clientes da plataforma Sistema Venda Direta.
Fonte de dados unica: ../inc/cases.php (mesma usada pela secao #cases da home).
*/

$clientCases = array_values(array_filter(
    require __DIR__ . '/../inc/cases.php',
    static fn (array $case): bool => empty($case['hidden'])
));

$seoBase = 'https://www.sistemavendadireta.com.br';
$seoUrl = $seoBase . '/cases/';
$seoTitle = 'Cases de clientes | Sistema Venda Direta';
$seoDescription = 'Operações de venda direta e marketing multinível no ar sobre a plataforma Sistema Venda Direta: '
    . 'loja virtual, escritório do consultor, plano de comissões e operações internacionais.';
$seoImage = $seoBase . '/imagens/Clientes.jpg';

$itemListElements = [];
foreach ($clientCases as $position => $case) {
    $item = [
        '@type' => 'ListItem',
        'position' => $position + 1,
        'name' => $case['name'],
        'description' => $case['summary'],
    ];
    if (!empty($case['url'])) {
        $item['url'] = $case['url'];
    }
    $itemListElements[] = $item;
}

$seoLdGraph = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'WebPage',
            'name' => $seoTitle,
            'description' => $seoDescription,
            'url' => $seoUrl,
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => 'Sistema Venda Direta',
                'url' => $seoBase . '/',
            ],
        ],
        [
            '@type' => 'ItemList',
            'name' => 'Cases de clientes',
            'itemListElement' => $itemListElements,
        ],
    ],
];
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
  <meta name="theme-color" content="#004AAD" />
  <meta name="author" content="Sistema Venda Direta" />
  <meta name="referrer" content="strict-origin-when-cross-origin" />
  <link rel="canonical" href="<?= htmlspecialchars($seoUrl, ENT_QUOTES, 'UTF-8') ?>" />
  <link rel="icon" type="image/svg+xml" href="../favicon.svg" />
  <link rel="alternate icon" href="../favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png" />
  <link rel="alternate" hreflang="pt-BR" href="<?= htmlspecialchars($seoUrl, ENT_QUOTES, 'UTF-8') ?>" />
  <link rel="alternate" hreflang="x-default" href="<?= htmlspecialchars($seoUrl, ENT_QUOTES, 'UTF-8') ?>" />

  <meta property="og:locale" content="pt_BR" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="<?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:url" content="<?= htmlspecialchars($seoUrl, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:site_name" content="Sistema Venda Direta" />
  <meta property="og:image" content="<?= htmlspecialchars($seoImage, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:image:alt" content="Cases de clientes do Sistema Venda Direta" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="twitter:description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="twitter:image" content="<?= htmlspecialchars($seoImage, ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="twitter:site" content="@sistemavendadireta" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&amp;family=Roboto:wght@300;400;500;700&amp;display=swap" />
  <?php $cssVersion = (string) @filemtime(__DIR__ . '/../css/site-tailwind.css'); ?>
  <link rel="stylesheet" href="../css/site-tailwind.css?v=<?= htmlspecialchars($cssVersion, ENT_QUOTES, 'UTF-8') ?>" />
  <link rel="stylesheet" href="../css/site-optimizations.css?v=<?= htmlspecialchars($cssVersion, ENT_QUOTES, 'UTF-8') ?>" />
  <link rel="stylesheet" href="../css/styles.css?v=<?= htmlspecialchars($cssVersion, ENT_QUOTES, 'UTF-8') ?>" />

  <script type="application/ld+json">
<?= json_encode($seoLdGraph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>

  </script>
  <?php include __DIR__ . '/../inc/analytics.php'; ?>
</head>
<body class="bg-brand text-white antialiased font-[var(--font-body)] site-optimized">
  <a href="#conteudo" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-full focus:bg-white focus:px-4 focus:py-2 focus:font-semibold focus:text-brand">
    Pular para o conteúdo
  </a>

  <header class="sticky top-0 z-40 border-b border-white/10 bg-brand/95 backdrop-blur">
    <div class="mx-auto max-w-[1140px] px-4 sm:px-6">
      <nav class="flex items-center justify-between gap-4 py-3" aria-label="Menu principal">
        <a href="../" class="block" aria-label="Sistema Venda Direta">
          <img decoding="async"
            src="../imagens/Logo-Branco-1.webp"
            alt="Sistema Venda Direta"
            class="h-auto w-[165px] sm:w-[210px] lg:w-[260px]"
            width="1000"
            height="300"
          />
        </a>

        <ul class="hidden items-center gap-6 lg:flex">
          <li><a href="../" class="text-sm font-medium text-white/90 hover:text-white">Site principal</a></li>
          <li><a href="../inteligencia-artificial/" class="text-sm font-medium text-white/90 hover:text-white">IA para MMN</a></li>
          <li><a href="./" class="rounded-md bg-white/15 px-3 py-2 text-sm font-medium">Cases</a></li>
          <li><a href="../blog/" class="text-sm font-medium text-white/90 hover:text-white">Blog</a></li>
        </ul>

        <a href="../#contato" class="hidden rounded-full border border-white/70 px-4 py-2 text-sm font-medium transition hover:bg-white/10 md:inline-flex">
          Solicite um Orçamento
        </a>
      </nav>
    </div>
  </header>

  <main id="conteudo" class="mx-auto max-w-[1140px] px-4 sm:px-6">
    <section class="py-10">
      <p class="text-xs font-semibold uppercase tracking-[0.22em] text-white/70">Cases de clientes</p>
      <h1 class="mt-3 font-[var(--font-heading)] text-3xl font-semibold leading-tight sm:text-4xl">
        Sistemas em produção desenvolvidos pela Sistema Venda Direta
      </h1>
      <div class="mt-3 h-1 w-[72px] rounded-full bg-white"></div>
      <p class="mt-4 max-w-3xl text-base leading-relaxed text-white/90">
        Venda direta e MMN, e-commerce, ERP, SaaS e IA aplicada. Cada operação tem sua própria regra de negócio —
        moeda, idioma, documento fiscal, plano de comissões, integração — e abaixo está o que foi entregue em cada uma,
        sem generalização, com o escopo real de cada projeto.
      </p>
    </section>

    <?php foreach ($clientCases as $case): ?>
      <section id="<?= htmlspecialchars($case['slug'], ENT_QUOTES, 'UTF-8') ?>" class="scroll-mt-28 py-6">
        <article class="rounded-[30px] border border-white/25 bg-white/[0.06] p-6 sm:p-8">
          <div class="grid gap-6 lg:grid-cols-[auto_1fr] lg:items-center">
            <div class="flex max-w-[280px] items-center rounded-2xl bg-white px-5 py-4">
              <picture>
                <source srcset="../<?= htmlspecialchars($case['logo'], ENT_QUOTES, 'UTF-8') ?>" type="image/webp" />
                <img
                  src="../<?= htmlspecialchars($case['logoFallback'], ENT_QUOTES, 'UTF-8') ?>"
                  alt="<?= htmlspecialchars($case['name'], ENT_QUOTES, 'UTF-8') ?>"
                  class="h-10 w-auto object-contain sm:h-12"
                  width="<?= (int) $case['logoWidth'] ?>"
                  height="<?= (int) $case['logoHeight'] ?>"
                  loading="lazy"
                />
              </picture>
            </div>

            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/70">
                <?= htmlspecialchars($case['segment'], ENT_QUOTES, 'UTF-8') ?> • <?= htmlspecialchars($case['period'], ENT_QUOTES, 'UTF-8') ?>
              </p>
              <h2 class="mt-2 font-[var(--font-heading)] text-2xl font-semibold sm:text-[30px]"><?= htmlspecialchars($case['name'], ENT_QUOTES, 'UTF-8') ?></h2>
              <p class="mt-3 max-w-3xl text-base leading-relaxed text-white/90"><?= htmlspecialchars($case['summary'], ENT_QUOTES, 'UTF-8') ?></p>
              <?php if (!empty($case['url'])): ?>
                <a
                  href="<?= htmlspecialchars($case['url'], ENT_QUOTES, 'UTF-8') ?>"
                  target="_blank"
                  rel="noopener"
                  class="mt-5 inline-flex rounded-full border border-white/70 px-5 py-2.5 text-sm font-semibold uppercase tracking-wide hover:bg-white/10"
                >
                  Visitar operação
                </a>
              <?php else: ?>
                <p class="mt-5 inline-flex rounded-full border border-white/30 px-5 py-2.5 text-sm font-semibold uppercase tracking-wide text-white/70">
                  Sistema interno do cliente
                </p>
              <?php endif; ?>
            </div>
          </div>

          <?php if (!empty($case['metrics'])): ?>
            <div class="mt-7 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
              <?php foreach ($case['metrics'] as $metric): ?>
                <div class="rounded-2xl border border-white/20 bg-white/5 px-4 py-3">
                  <p class="font-[var(--font-heading)] text-2xl font-semibold"><?= htmlspecialchars($metric['value'], ENT_QUOTES, 'UTF-8') ?></p>
                  <p class="mt-1 text-sm text-white/80"><?= htmlspecialchars($metric['label'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <h3 class="mt-8 font-[var(--font-heading)] text-xl font-semibold">O que foi entregue</h3>
          <div class="mt-4 grid gap-4 md:grid-cols-2">
            <?php foreach ($case['details'] as $detail): ?>
              <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
                <h4 class="font-[var(--font-heading)] text-base font-semibold"><?= htmlspecialchars($detail['title'], ENT_QUOTES, 'UTF-8') ?></h4>
                <p class="mt-2 text-sm leading-relaxed text-white/90"><?= htmlspecialchars($detail['body'], ENT_QUOTES, 'UTF-8') ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        </article>
      </section>
    <?php endforeach; ?>

    <section class="py-10">
      <div class="rounded-[30px] border border-white/30 bg-white/[0.08] p-6 text-center sm:p-8">
        <h2 class="font-[var(--font-heading)] text-2xl font-semibold sm:text-3xl">Sua operação tem uma regra que nenhum sistema pronto atende?</h2>
        <p class="mx-auto mt-3 max-w-2xl text-base leading-relaxed text-white/90">
          Moeda local, documento fiscal por país, plano de comissões próprio, forma de entrega combinada.
          É exatamente aí que a plataforma é ajustada ao seu negócio.
        </p>
        <a href="../#contato" class="mt-6 inline-flex rounded-full border border-white/70 px-6 py-3 text-sm font-semibold uppercase tracking-wide hover:bg-white/10">
          Solicite um Orçamento
        </a>
      </div>
    </section>
  </main>

  <footer class="mt-10 border-t border-white/15 bg-brand-dark/40">
    <div class="mx-auto max-w-[1140px] px-4 py-10 sm:px-6">
      <div class="grid gap-8 md:grid-cols-3">
        <div class="space-y-3">
          <img decoding="async" src="../imagens/Logo-Branco-1.webp" alt="Sistema Venda Direta" class="h-auto w-[180px]" width="1000" height="300" loading="lazy" />
          <p class="max-w-sm text-sm leading-relaxed text-white/85">
            A Sistema Venda Direta desenvolve soluções para operação comercial, vendas diretas e evolução tecnológica com IA aplicada ao negócio.
          </p>
          <p class="text-sm text-white/90">Telefone: <a href="tel:+5511994566726" class="font-semibold hover:underline">11 99456-6726</a></p>
          <p class="text-sm text-white/90">Email: <a href="mailto:contato@sistemavendadireta.com.br" class="font-semibold hover:underline">contato@sistemavendadireta.com.br</a></p>
        </div>

        <div class="space-y-3">
          <h4 class="font-[var(--font-heading)] text-lg font-semibold">Institucional</h4>
          <nav class="grid gap-2 text-sm text-white/90" aria-label="Menu institucional">
            <a href="../" class="hover:underline">Sistema Venda Direta</a>
            <a href="../wordpress/" class="hover:underline">WordPress</a>
            <a href="../codafacil/" class="hover:underline">Desenvolvimento com IA</a>
            <a href="../inteligencia-artificial/" class="hover:underline">Multinível com IA</a>
            <a href="./" class="hover:underline">Cases</a>
            <a href="../blog/" class="hover:underline">Blog</a>
          </nav>
        </div>

        <div class="space-y-4">
          <h4 class="font-[var(--font-heading)] text-lg font-semibold">25 anos de experiência desenvolvendo sistemas</h4>
          <a href="../#contato" class="inline-flex rounded-full border border-white/70 px-5 py-2.5 text-sm font-semibold uppercase tracking-wide hover:bg-white/10">
            Solicite um Orçamento
          </a>
          <div class="flex items-center gap-3">
            <a href="https://facebook.com/sistemavendadireta" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[#3b5998] text-sm font-bold">f</a>
            <a href="https://www.youtube.com/@andregomes8954" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[#cd201f] text-sm font-bold">▶</a>
          </div>
        </div>
      </div>

      <div class="mt-8 border-t border-white/15 pt-4 text-xs text-white/70">
        © Sistema Venda Direta - Todos os direitos reservados.
      </div>
    </div>
  </footer>

  <a href="https://wa.me/+5511994566726" target="_blank" rel="noopener noreferrer" aria-label="Falar no WhatsApp" class="fixed bottom-3 right-3 z-[70] inline-flex items-center gap-2 rounded-full bg-[#25D366] px-4 py-3 text-sm font-bold text-white shadow-[0_10px_24px_rgba(0,0,0,0.35)] ring-2 ring-white/30 sm:bottom-4 sm:right-4 sm:h-14 sm:w-14 sm:justify-center sm:px-0">
    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/20 text-base leading-none">W</span>
    <span class="sm:hidden">WhatsApp</span>
  </a>
</body>
</html>
