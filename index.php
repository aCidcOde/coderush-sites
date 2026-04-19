<?php
$seoBase = 'https://coderush.com.br';
$seoUrl = $seoBase . '/';
$seoTitle = 'CodeRush | Hub de tecnologia — software sob medida, IA e automação';
$seoDescription = 'CodeRush é um hub de tecnologia com software sob medida, automação com IA, vendas diretas e MMN. Ecossistema de empresas no Brasil.';
$seoOgImage = $seoBase . '/og-coderush.jpg';
$seoLdGraph = [
  '@context' => 'https://schema.org',
  '@graph' => [
    [
      '@type' => 'Organization',
      '@id' => $seoUrl . '#organization',
      'name' => 'CodeRush',
      'url' => $seoUrl,
      'logo' => [
        '@type' => 'ImageObject',
        'url' => $seoOgImage,
      ],
      'areaServed' => 'Brasil',
    ],
    [
      '@type' => 'WebSite',
      '@id' => $seoUrl . '#website',
      'url' => $seoUrl,
      'name' => 'CodeRush',
      'publisher' => ['@id' => $seoUrl . '#organization'],
    ],
    [
      '@type' => 'WebPage',
      '@id' => $seoUrl . '#webpage',
      'url' => $seoUrl,
      'name' => $seoTitle,
      'description' => $seoDescription,
      'inLanguage' => 'pt-BR',
      'isPartOf' => ['@id' => $seoUrl . '#website'],
    ],
  ],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>" />
  <link rel="canonical" href="<?= htmlspecialchars($seoUrl, ENT_QUOTES, 'UTF-8') ?>" />
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='12' fill='%23020b1a'/%3E%3Ctext x='50%25' y='56%25' dominant-baseline='middle' text-anchor='middle' font-family='Arial,sans-serif' font-size='26' font-weight='700' fill='%23ffffff'%3ECR%3C/text%3E%3C/svg%3E" />
  <meta property="og:title" content="<?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="<?= htmlspecialchars($seoUrl, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:site_name" content="CodeRush" />
  <meta property="og:locale" content="pt_BR" />
  <meta property="og:image" content="<?= htmlspecialchars($seoOgImage, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />
  <meta property="og:image:alt" content="CodeRush — hub de tecnologia" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="twitter:description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="twitter:image" content="<?= htmlspecialchars($seoOgImage, ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="theme-color" content="#020b1a" />
  <meta name="keywords" content="CodeRush, cases de sucesso, software sob medida, vendas diretas, automação com IA, Laravel, n8n, consultoria tecnológica, Brasil" />
  <meta name="robots" content="index, follow" />
  <script type="application/ld+json"><?= json_encode($seoLdGraph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
  <link rel="stylesheet" href="css/site-tailwind.css?v=<?= filemtime(__DIR__.'/css/site-tailwind.css') ?>" />
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet" />
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "ItemList",
    "name": "Cases de sucesso — ecossistema CodeRush",
    "description": "Destaques de projetos em vendas diretas, desenvolvimento de software, WordPress e automação com IA no Brasil.",
    "numberOfItems": 4,
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "item": {
          "@type": "CreativeWork",
          "name": "Plataforma nacional de vendas diretas e MMN",
          "abstract": "Escala de rede, comissionamento e lojas integradas com alta disponibilidade.",
          "keywords": "vendas diretas, MMN, plataforma, integração"
        }
      },
      {
        "@type": "ListItem",
        "position": 2,
        "item": {
          "@type": "CreativeWork",
          "name": "Software sob medida com IA e integrações",
          "abstract": "Backoffice, APIs e entregas iterativas com stack Laravel e automações.",
          "keywords": "Laravel, IA, software sob medida, API"
        }
      },
      {
        "@type": "ListItem",
        "position": 3,
        "item": {
          "@type": "CreativeWork",
          "name": "WordPress corporativo com ERP e pagamentos",
          "abstract": "E-commerce e catálogos B2B com sincronização de estoque e pedidos.",
          "keywords": "WordPress, WooCommerce, ERP, B2B"
        }
      },
      {
        "@type": "ListItem",
        "position": 4,
        "item": {
          "@type": "CreativeWork",
          "name": "Automação comercial e agentes com n8n",
          "abstract": "Fluxos inteligentes ligando CRM, mensageria e IA aplicada.",
          "keywords": "n8n, automação, IA, CRM"
        }
      }
    ]
  }
  </script>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
      {
        "@type": "Question",
        "name": "O que é o ecossistema CodeRush?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "É um hub que reúne empresas de tecnologia com focos diferentes — vendas diretas, software, WordPress e automação com IA — compartilhando processos e visão de entrega."
        }
      },
      {
        "@type": "Question",
        "name": "Como escolho qual empresa do grupo fala com o meu projeto?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Pelo tipo de necessidade: MMN e plataforma de vendas; desenvolvimento sob medida; WordPress e lojas; ou automação e IA. O formulário de contato também ajuda a direcionar seu briefing."
        }
      },
      {
        "@type": "Question",
        "name": "Os sites e sistemas são seguros?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "As soluções seguem boas práticas de segurança, HTTPS e controles de acesso conforme o escopo de cada projeto. Detalhes são alinhados no discovery com a equipe."
        }
      },
      {
        "@type": "Question",
        "name": "Como funciona o contato comercial?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Você pode usar o formulário nesta página, o e-mail contato@coderush.com.br ou o site da empresa do grupo mais alinhada ao seu caso. Resposta humana, sem fila automática genérica."
        }
      },
      {
        "@type": "Question",
        "name": "Vocês trabalham com IA e automação?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Sim. Há times dedicados a software com IA, integrações, WordPress e fluxos com ferramentas como n8n, além de consultoria em automação comercial."
        }
      },
      {
        "@type": "Question",
        "name": "Atendem empresas fora do Brasil?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "O foco principal é o mercado brasileiro; projetos internacionais podem ser avaliados caso a caso, conforme fuso, idioma e requisitos legais."
        }
      }
    ]
  }
  </script>
</head>
<body id="top" class="min-h-screen antialiased">

  <!-- Header -->
  <header class="site-header" data-site-header>
    <div class="site-header-pill">
    <div class="site-header-inner mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-2.5 sm:gap-4 sm:px-6 sm:py-3 md:py-3.5">
      <a href="#top" class="header-brand group flex shrink-0 items-center gap-2.5 rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/55 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
        <span class="header-brand-mark" aria-hidden="true">CR</span>
        <span class="header-brand-text">Code<span>Rush</span></span>
      </a>

      <div class="hidden items-center gap-5 lg:gap-7 md:flex">
        <nav class="flex flex-wrap items-center justify-end gap-x-1 gap-y-1 sm:gap-x-2 lg:gap-x-3 xl:gap-x-4" aria-label="Seções da página">
          <a href="#sobre" class="header-nav-link focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Sobre</a>
          <a href="#empresas" class="header-nav-link focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Empresas</a>
          <a href="#cases" class="header-nav-link focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Cases</a>
          <a href="#faq" class="header-nav-link focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">FAQ</a>
        </nav>
        <div class="flex items-center gap-2.5 pl-2 lg:border-l lg:border-white/[0.08] lg:pl-5">
          <a href="#contato" class="header-btn-primary inline-flex items-center justify-center focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/55 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
            <span class="header-btn-primary-shine" aria-hidden="true"></span>
            <span class="relative z-[1]">Fale conosco</span>
          </a>
        </div>
      </div>

      <button type="button" id="mobile-menu-btn" class="header-menu-btn relative z-[60] flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/14 bg-white/[0.03] text-white/90 md:hidden focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]" aria-expanded="false" aria-controls="mobile-menu" aria-label="Abrir menu de navegação">
        <span class="sr-only">Alternar menu</span>
        <svg id="mobile-menu-icon-open" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
        <svg id="mobile-menu-icon-close" class="hidden h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
      </button>
    </div>
    </div>

    <div id="mobile-menu" class="header-mobile-panel hidden border border-white/[0.1] bg-[#020b1a]/94 backdrop-blur-xl md:hidden">
      <nav class="mx-auto flex max-w-6xl flex-col gap-0.5 px-6 py-4" aria-label="Menu mobile">
        <a href="#sobre" class="header-mobile-link px-3 py-3 text-sm font-medium text-white/85 hover:bg-white/[0.05] hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-inset">Sobre</a>
        <a href="#empresas" class="header-mobile-link px-3 py-3 text-sm font-medium text-white/85 hover:bg-white/[0.05] hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-inset">Empresas</a>
        <a href="#cases" class="header-mobile-link px-3 py-3 text-sm font-medium text-white/85 hover:bg-white/[0.05] hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-inset">Cases</a>
        <a href="#faq" class="header-mobile-link px-3 py-3 text-sm font-medium text-white/85 hover:bg-white/[0.05] hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-inset">FAQ</a>
        <div class="mt-3 flex flex-col gap-2.5 border-t border-white/10 pt-4">
          <a href="#contato" class="header-btn-primary flex w-full items-center justify-center px-4 py-3 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/55 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
            <span class="header-btn-primary-shine" aria-hidden="true"></span>
            <span class="relative z-[1]">Fale conosco</span>
          </a>
        </div>
      </nav>
    </div>
  </header>

  <!-- Hero -->
  <section class="hero-section relative overflow-hidden px-6 text-center" aria-labelledby="hero-heading">
    <div class="hero-ambient" aria-hidden="true">
      <div class="hero-grid"></div>
      <div class="hero-blob hero-blob-1"></div>
      <div class="hero-blob hero-blob-2"></div>
      <div class="hero-blob hero-blob-3"></div>
      <div class="hero-vignette"></div>
      <div class="hero-noise"></div>
    </div>

    <div class="relative z-10 mx-auto w-full max-w-3xl">
      <div class="hero-reveal-item hero-delay-1">
        <span class="hero-badge rounded-full px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-blue-200">Hub de Tecnologia</span>
      </div>

      <h1 id="hero-heading" class="hero-reveal-item hero-delay-2 mt-7 font-heading text-4xl font-extrabold leading-[1.12] tracking-tight sm:text-5xl md:text-6xl md:leading-[1.1]">
        <span class="block text-white">Soluções digitais</span>
        <span class="gradient-text mt-2 block sm:mt-3">que geram resultados</span>
      </h1>

      <p class="hero-reveal-item hero-delay-3 mx-auto mt-7 max-w-2xl text-base leading-relaxed text-white/75 sm:text-lg">
        CodeRush é um hub central de tecnologia que reúne empresas especializadas em vendas diretas, parcerias, desenvolvimento de software, WordPress, automação com IA e design digital.
      </p>

      <div class="hero-reveal-item hero-delay-4 mt-10 flex flex-wrap items-center justify-center gap-4">
        <a href="#empresas" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-brand px-8 py-3.5 text-sm font-semibold text-white shadow-[0_0_0_1px_rgba(255,255,255,0.08),0_16px_40px_-12px_rgba(0,74,173,0.65)] transition duration-300 hover:-translate-y-0.5 hover:bg-blue-600 hover:shadow-[0_0_0_1px_rgba(255,255,255,0.12),0_22px_48px_-8px_rgba(0,74,173,0.55)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/60 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
          <span class="relative z-10">Areas de Atuação</span>
          <span class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/15 to-transparent transition duration-700 group-hover:translate-x-full" aria-hidden="true"></span>
        </a>
        <a href="#contato" class="inline-flex items-center justify-center rounded-full border border-white/25 bg-white/[0.03] px-8 py-3.5 text-sm font-semibold text-white/95 backdrop-blur-sm transition duration-300 hover:-translate-y-0.5 hover:border-white/45 hover:bg-white/[0.08] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Fale conosco</a>
      </div>

      <div class="hero-reveal-item hero-delay-5 mt-14 flex flex-col items-center gap-2 md:mt-20">
        <a href="#sobre" class="hero-scroll-hint group inline-flex flex-col items-center gap-2 rounded-lg px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-white/45 transition hover:text-white/75 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
          <span>Saiba mais</span>
          <svg class="h-5 w-5 text-blue-400/80 group-hover:text-blue-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
        </a>
      </div>
    </div>
  </section>

  <!-- Carrossel de clientes (mock) -->
  <section id="clientes" class="clients-strip border-t border-white/[0.06] bg-[#020b1a] py-10 md:py-14" aria-label="Marcas parceiras do ecossistema">
    <div class="mx-auto max-w-6xl px-6">
      <p class="text-center text-[11px] font-semibold uppercase tracking-[0.2em] text-white/40">Confiam em soluções do grupo</p>
      <p class="mx-auto mt-2 max-w-xl text-center text-xs text-white/45">Cada desafio, uma solução inovadora.</p>
    </div>
    <div class="clients-marquee mx-auto mt-8 max-w-[100vw]">
      <div class="clients-track">
        <div class="clients-group">
          <span class="clients-logo">RAASA Advogados</span>
          <span class="clients-logo">Emergency Documentação</span>
          <span class="clients-logo">Top Pericias</span>
          <span class="clients-logo">Lojas Leader</span>
          <span class="clients-logo">Ecotrend Parceiros</span>
          <span class="clients-logo">Avig360 Saúde Mental</span>
          <span class="clients-logo">Game Station</span>
          <span class="clients-logo">Haiflex</span>
        </div>
        <div class="clients-group clients-group-dup" role="presentation" aria-hidden="true">
          <span class="clients-logo">BBom</span>
          <span class="clients-logo">Science Life World</span>
          <span class="clients-logo">Forone Bolivia</span>
          <span class="clients-logo">Mauabank </span>
          <span class="clients-logo">Retech Engenharia</span>
          <span class="clients-logo">Accenti</span>
          <span class="clients-logo">Sublimity</span>
        </div>
      </div>
    </div>

  </section>

  <!-- Quem somos -->
  <section id="sobre" class="sobre-section border-t border-white/[0.06] px-6 py-20 md:py-28" aria-labelledby="sobre-heading">
    <div class="sobre-ambient" aria-hidden="true"></div>
    <div class="sobre-ring hidden lg:block" aria-hidden="true"></div>
    <div class="pointer-events-none absolute inset-x-0 top-0 mx-auto h-px max-w-3xl bg-gradient-to-r from-transparent via-blue-500/35 to-transparent" aria-hidden="true"></div>
    <div class="relative mx-auto max-w-6xl">
      <div class="grid gap-14 lg:grid-cols-12 lg:items-start lg:gap-12">
        <div class="lg:col-span-5">
          <p class="sobre-reveal sobre-d-1 font-heading text-[11px] font-semibold uppercase tracking-[0.22em] text-blue-400/95">Quem somos</p>
          <h2 id="sobre-heading" class="sobre-reveal sobre-d-2 mt-3 font-heading text-3xl font-bold tracking-tight text-white sm:text-4xl">
            Um hub de tecnologia que une especialistas e propósito
          </h2>
          <p class="sobre-reveal sobre-d-3 mt-5 text-base leading-relaxed text-white/68">
            O CodeRush reúne <strong class="font-semibold text-white/90">várias empresas e serviços</strong> sob um ecossistema coordenado. Cada marca mantém sua identidade e foco de mercado, com metodologias e qualidade de entrega alinhadas.
          </p>
          <p class="sobre-reveal sobre-d-4 mt-4 text-base leading-relaxed text-white/60">
            Do MMN e vendas diretas ao desenvolvimento sob medida, WordPress, automação e IA aplicada — você encontra o time certo sem perder tempo em burocracias.
          </p>
          <div class="sobre-reveal sobre-d-5 mt-8 flex flex-wrap gap-3">
            <a href="#empresas" class="inline-flex items-center justify-center gap-2 rounded-full bg-brand px-6 py-3 text-sm font-semibold text-white shadow-[0_12px_36px_-12px_rgba(0,74,173,0.65)] transition hover:-translate-y-0.5 hover:bg-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/60 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
              <i data-lucide="building-2" class="h-4 w-4" aria-hidden="true"></i>
              Conhecer as empresas
            </a>
            <a href="#contato" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/22 bg-white/[0.04] px-6 py-3 text-sm font-semibold text-white/92 backdrop-blur-sm transition hover:-translate-y-0.5 hover:border-white/40 hover:bg-white/[0.08] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
              <i data-lucide="message-square" class="h-4 w-4" aria-hidden="true"></i>
              Falar com o time
            </a>
          </div>
        </div>
        <div class="lg:col-span-7">
          <div class="grid grid-cols-3 gap-3 sm:gap-4">
            <div class="sobre-reveal sobre-d-3 sobre-stat px-3 py-5 text-center sm:px-4 sm:py-6">
              <p class="font-heading text-2xl font-extrabold tabular-nums text-blue-400 sm:text-3xl">+40</p>
              <p class="mt-1.5 text-[11px] font-medium leading-snug text-white/55 sm:text-xs">Projetos nos ultimos anos</p>
            </div>
            <div class="sobre-reveal sobre-d-4 sobre-stat px-3 py-5 text-center sm:px-4 sm:py-6">
              <p class="font-heading text-2xl font-extrabold tabular-nums text-blue-400 sm:text-3xl">+24</p>
              <p class="mt-1.5 text-[11px] font-medium leading-snug text-white/55 sm:text-xs">Anos de experiência</p>
            </div>
            <div class="sobre-reveal sobre-d-5 sobre-stat px-3 py-5 text-center sm:px-4 sm:py-6">
              <p class="font-heading text-2xl font-extrabold text-blue-400 sm:text-3xl">IA FIRST</p>
              <p class="mt-1.5 text-[11px] font-medium leading-snug text-white/55 sm:text-xs">No centro da operação</p>
            </div>
          </div>
          <div class="mt-4 grid gap-3 sm:grid-cols-2">
            <article class="sobre-reveal sobre-d-4 sobre-pillar flex gap-4 p-4 sm:p-5">
              <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-500/15 text-blue-200 ring-1 ring-blue-400/20">
                <i data-lucide="network" class="h-5 w-5" aria-hidden="true"></i>
              </span>
              <div>
                <h3 class="font-heading text-sm font-bold text-white">Ecossistema integrado</h3>
                <p class="mt-1.5 text-xs leading-relaxed text-white/55">Processos e visão comuns entre as marcas — menos atrito, mais velocidade no seu projeto.</p>
              </div>
            </article>
            <article class="sobre-reveal sobre-d-5 sobre-pillar flex gap-4 p-4 sm:p-5">
              <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-500/15 text-violet-200 ring-1 ring-violet-400/20">
                <i data-lucide="crosshair" class="h-5 w-5" aria-hidden="true"></i>
              </span>
              <div>
                <h3 class="font-heading text-sm font-bold text-white">Foco no negócio</h3>
                <p class="mt-1.5 text-xs leading-relaxed text-white/55">Tecnologia escolhida pelo resultado: conversão, escala, segurança e time-to-market.</p>
              </div>
            </article>
            <article class="sobre-reveal sobre-d-6 sobre-pillar flex gap-4 p-4 sm:p-5">
              <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-500/15 text-sky-200 ring-1 ring-sky-400/20">
                <i data-lucide="shield-check" class="h-5 w-5" aria-hidden="true"></i>
              </span>
              <div>
                <h3 class="font-heading text-sm font-bold text-white">Entrega responsável</h3>
                <p class="mt-1.5 text-xs leading-relaxed text-white/55">Boas práticas de segurança, LGPD e acompanhamento humano nas fases críticas.</p>
              </div>
            </article>
            <article class="sobre-reveal sobre-d-7 sobre-pillar flex gap-4 p-4 sm:p-5">
              <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-200 ring-1 ring-emerald-400/20">
                <i data-lucide="sparkles" class="h-5 w-5" aria-hidden="true"></i>
              </span>
              <div>
                <h3 class="font-heading text-sm font-bold text-white">IA onde faz sentido</h3>
                <p class="mt-1.5 text-xs leading-relaxed text-white/55">Automação e inteligência aplicadas ao funil e à operação — sem hype vazio.</p>
              </div>
            </article>
          </div>

        </div>
      </div>
    </div>
  </section>

  <!-- Empresas -->
  <section id="empresas" class="portfolio-section border-t border-white/[0.06] px-6 py-20 md:py-24" aria-labelledby="portfolio-heading">
    <div class="pointer-events-none absolute inset-x-0 top-0 mx-auto h-px max-w-3xl bg-gradient-to-r from-transparent via-blue-500/40 to-transparent" aria-hidden="true"></div>
    <div class="relative mx-auto max-w-6xl">
      <div class="mx-auto mb-14 max-w-2xl text-center md:mb-16">
        <p class="text-xs font-semibold uppercase tracking-widest text-blue-400">Portfólio</p>
        <h2 id="portfolio-heading" class="mt-3 font-heading text-3xl font-bold tracking-tight text-white sm:text-4xl">Nossas empresas</h2>
        <p class="mt-4 text-base leading-relaxed text-white/60">Cada marca com foco próprio, integrada ao ecossistema CodeRush. Use <span class="text-white/80">Visitar</span> para ir ao site da empresa.</p>
      </div>

      <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
        <!-- Sistema Venda Direta -->
        <a href="https://sistemavendadireta.com.br" target="_blank" class="portfolio-card group relative flex h-full flex-col rounded-2xl border border-white/10 bg-gradient-to-b from-white/[0.07] to-white/[0.02] p-6 shadow-lg shadow-black/25 backdrop-blur-sm hover:border-blue-500/35">
          <div class="mb-5 flex items-start justify-between gap-3">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-500/15 ring-1 ring-blue-400/25 text-blue-300">
              <i data-lucide="store" class="h-6 w-6" aria-hidden="true"></i>
            </span>
            <span class="shrink-0 rounded-full bg-blue-500/10 px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-blue-300/90">Vendas &amp; MMN</span>
          </div>
          <h3 class="font-heading text-lg font-bold text-white transition group-hover:text-blue-200">Sistema Venda Direta</h3>
          <p class="mt-3 flex-grow text-sm leading-relaxed text-white/65">Plataforma completa para vendas diretas e marketing multinível: força de vendas, lojas virtuais, backoffice e relatórios gerenciais.</p>
          <div class="mt-6 flex items-center justify-between gap-3 border-t border-white/10 pt-5">
            <span class="truncate text-xs text-white/45" title="sistemavendadireta.com.br">sistemavendadireta.com.br</span>
            <span class="inline-flex shrink-0 items-center gap-1.5 text-sm font-semibold text-blue-400 transition group-hover:text-blue-300">
              Visitar
              <i data-lucide="arrow-right" class="h-4 w-4 transition group-hover:translate-x-0.5" aria-hidden="true"></i>
            </span>
          </div>
        </a>

        <!-- Codafacil -->
        <a href="https://codafacil.dev" target="_blank" class="portfolio-card group relative flex h-full flex-col rounded-2xl border border-white/10 bg-gradient-to-b from-white/[0.07] to-white/[0.02] p-6 shadow-lg shadow-black/25 backdrop-blur-sm hover:border-violet-500/35">
          <div class="mb-5 flex items-start justify-between gap-3">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-500/15 ring-1 ring-violet-400/25 text-violet-200">
              <i data-lucide="code-xml" class="h-6 w-6" aria-hidden="true"></i>
            </span>
            <span class="shrink-0 rounded-full bg-violet-500/10 px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-violet-200/90">Dev &amp; IA</span>
          </div>
          <h3 class="font-heading text-lg font-bold text-white transition group-hover:text-violet-200">Codafacil.dev</h3>
          <p class="mt-3 flex-grow text-sm leading-relaxed text-white/65">Desenvolvimento de Software sob medida com IA, entregas ágeis e stack moderna. Mobile, desktop e SAAS.</p>
          <div class="mt-6 flex items-center justify-between gap-3 border-t border-white/10 pt-5">
            <span class="truncate text-xs text-white/45" title="codafacil.dev">codafacil.dev</span>
            <span class="inline-flex shrink-0 items-center gap-1.5 text-sm font-semibold text-violet-400 transition group-hover:text-violet-300">
              Visitar
              <i data-lucide="arrow-right" class="h-4 w-4 transition group-hover:translate-x-0.5" aria-hidden="true"></i>
            </span>
          </div>
        </a>

        <!-- FluxoInteligente IA -->
        <a href="https://fluxointeligenteia.com.br" target="_blank " class="portfolio-card group relative flex h-full flex-col rounded-2xl border border-white/10 bg-gradient-to-b from-white/[0.07] to-white/[0.02] p-6 shadow-lg shadow-black/25 backdrop-blur-sm hover:border-emerald-500/35">
          <div class="mb-5 flex items-start justify-between gap-3">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-500/15 ring-1 ring-emerald-400/25 text-emerald-200">
              <i data-lucide="workflow" class="h-6 w-6" aria-hidden="true"></i>
            </span>
            <span class="shrink-0 rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-emerald-200/90">Automação</span>
          </div>
          <h3 class="font-heading text-lg font-bold text-white transition group-hover:text-emerald-200">FluxoInteligente IA</h3>
          <p class="mt-3 flex-grow text-sm leading-relaxed text-white/65">Processos complexos viram fluxos inteligentes: n8n, langchain, RAG, agentes e IA aplicada ao negócio.</p>
          <div class="mt-6 flex items-center justify-between gap-3 border-t border-white/10 pt-5">
            <span class="truncate text-xs text-white/45" title="fluxointeligenteia.com.br">fluxointeligenteia.com.br</span>
            <span class="inline-flex shrink-0 items-center gap-1.5 text-sm font-semibold text-emerald-400 transition group-hover:text-emerald-300">
              Visitar
              <i data-lucide="arrow-right" class="h-4 w-4 transition group-hover:translate-x-0.5" aria-hidden="true"></i>
            </span>
          </div>
        </a>
      </div>
    </div>
  </section>

  <!-- Cases -->
  <section id="cases" class="cases-section relative border-t border-white/[0.06] px-6 py-20 md:py-28" aria-labelledby="cases-heading">
    <div class="cases-ambient" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-6xl">
      <header class="cases-reveal cases-d-1 mx-auto mb-12 max-w-3xl text-center md:mb-16">
        <p class="text-xs font-semibold uppercase tracking-widest text-emerald-400/90">Resultados &amp; Casos de Sucesso</p>
        <h2 id="cases-heading" class="mt-3 font-heading text-3xl font-bold tracking-tight text-white sm:text-4xl">Cases de sucesso no ecossistema CodeRush</h2>
        <p class="mt-4 text-base leading-relaxed text-white/65">
          Selecionamos <strong class="font-semibold text-white/85">destaques reais</strong> de entregas feitas pelas empresas do grupo: <strong class="font-semibold text-white/85">performance</strong>, <strong class="font-semibold text-white/85">SEO técnico</strong>, integrações e foco em <strong class="font-semibold text-white/85">conversão</strong>. Use estes cenários para comparar com o seu próximo projeto.
        </p>
      </header>

      <article class="cases-reveal cases-d-3 cases-card group relative overflow-hidden rounded-3xl border border-blue-500/25 bg-gradient-to-br from-blue-950/50 via-[#020b1a] to-[#020b1a] p-6 shadow-xl shadow-blue-950/40 sm:p-8 md:p-10">
        <div class="cases-featured-bar absolute left-0 right-0 top-0 h-px opacity-80" aria-hidden="true"></div>
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
          <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 rounded-full border border-blue-400/30 bg-blue-500/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-blue-200">
              <i data-lucide="trending-up" class="h-3.5 w-3.5" aria-hidden="true"></i>
              Vendas e Comissões em alta escala
            </div>
            <h3 class="mt-4 font-heading text-2xl font-bold text-white md:text-3xl">Plataforma vendas diretas,MMN e parcerias.</h3>
            <p class="mt-3 text-sm leading-relaxed text-white/70 md:text-base">
              Plataforma SaaS de gestão comercial para operações de venda direta e marketing multinível, cobrindo escritório virtual para a rede de distribuidores, loja com checkout integrado (PIX, cartão e boleto), planos de compensação configuráveis (binário, unilevel e híbrido), cálculo automático de comissões, sincronização com ERP e logística, relatórios financeiros e de performance em tempo real, e módulo de IA aplicada para automação de processos operacionais e tomada de decisão.
            </p>
            <ul class="mt-5 flex flex-wrap gap-2" aria-label="Destaques do case">
              <li class="rounded-full border border-white/15 bg-white/[0.05] px-3 py-1 text-xs font-medium text-white/75">Alta escala</li>
              <li class="rounded-full border border-white/15 bg-white/[0.05] px-3 py-1 text-xs font-medium text-white/75">Comissionamento</li>
              <li class="rounded-full border border-white/15 bg-white/[0.05] px-3 py-1 text-xs font-medium text-white/75">Multi-loja</li>
              <li class="rounded-full border border-white/15 bg-white/[0.05] px-3 py-1 text-xs font-medium text-white/75">Relatórios</li>
            </ul>
          </div>
          <div class="flex w-full shrink-0 flex-col gap-3 sm:flex-row sm:items-center lg:w-auto lg:flex-col">
            <a href="/sistemavendadireta/" class="inline-flex items-center justify-center gap-2 rounded-full bg-brand px-6 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/60 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
              Ver solução Sistema Venda Direta
              <i data-lucide="arrow-right" class="h-4 w-4 transition group-hover:translate-x-0.5" aria-hidden="true"></i>
            </a>
            <a href="#contato" class="inline-flex items-center justify-center rounded-full border border-white/25 px-6 py-3 text-sm font-semibold text-white/90 transition hover:border-white/50 hover:bg-white/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Quero algo parecido</a>
          </div>
        </div>
      </article>
      <article class="mt-3 cases-reveal cases-d-3 cases-card group relative overflow-hidden rounded-3xl border border-blue-500/25 bg-gradient-to-br from-blue-950/50 via-[#020b1a] to-[#020b1a] p-6 shadow-xl shadow-blue-950/40 sm:p-8 md:p-10">
        <div class="cases-featured-bar absolute left-0 right-0 top-0 h-px opacity-80" aria-hidden="true"></div>
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
          <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 rounded-full border border-blue-400/30 bg-blue-500/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-blue-200">
              <i data-lucide="trending-up" class="h-3.5 w-3.5" aria-hidden="true"></i>
              SAAS de Due Deligence e Análise de Risco
            </div>
            <h3 class="mt-4 font-heading text-2xl font-bold text-white md:text-3xl">Plataforma SAAS para vendas de serviço de Due Deligence.</h3>
            <p class="mt-3 text-sm leading-relaxed text-white/70 md:text-base">
              Plataforma SaaS desenvolvida para digitalizar e automatizar o ciclo completo de pedidos de certidões, desde o cadastro do cliente e escolha dos serviços até pagamento, emissão, acompanhamento e operação administrativa, com integrações externas, filas de processamento, auditoria e notificações automatizadas.
            </p>
            <ul class="mt-5 flex flex-wrap gap-2" aria-label="Destaques do case">
              <li class="rounded-full border border-white/15 bg-white/[0.05] px-3 py-1 text-xs font-medium text-white/75">Alta Disponibilidade</li>
              <li class="rounded-full border border-white/15 bg-white/[0.05] px-3 py-1 text-xs font-medium text-white/75">Integrações</li>
              <li class="rounded-full border border-white/15 bg-white/[0.05] px-3 py-1 text-xs font-medium text-white/75">Inteligência Artificial</li>
              <li class="rounded-full border border-white/15 bg-white/[0.05] px-3 py-1 text-xs font-medium text-white/75">UX & UI Friendly</li>
            </ul>
          </div>
          <div class="flex w-full shrink-0 flex-col gap-3 sm:flex-row sm:items-center lg:w-auto lg:flex-col">
            <a href="https://emergency.com.br" class="inline-flex items-center justify-center gap-2 rounded-full bg-brand px-6 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/60 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
              Ver solução Emergency
              <i data-lucide="arrow-right" class="h-4 w-4 transition group-hover:translate-x-0.5" aria-hidden="true"></i>
            </a>
            <a href="#contato" class="inline-flex items-center justify-center rounded-full border border-white/25 px-6 py-3 text-sm font-semibold text-white/90 transition hover:border-white/50 hover:bg-white/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Quero algo parecido</a>
          </div>
        </div>
      </article>
      <article class="mt-3 cases-reveal cases-d-3 cases-card group relative overflow-hidden rounded-3xl border border-blue-500/25 bg-gradient-to-br from-blue-950/50 via-[#020b1a] to-[#020b1a] p-6 shadow-xl shadow-blue-950/40 sm:p-8 md:p-10">
        <div class="cases-featured-bar absolute left-0 right-0 top-0 h-px opacity-80" aria-hidden="true"></div>
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
          <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 rounded-full border border-blue-400/30 bg-blue-500/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-blue-200">
              <i data-lucide="trending-up" class="h-3.5 w-3.5" aria-hidden="true"></i>
              Lojas onlines Distribuidas e estoque Centralizado
            </div>
            <h3 class="mt-4 font-heading text-2xl font-bold text-white md:text-3xl">Plataforma centralizada com Multiplas integrações e rotinas</h3>
            <p class="mt-3 text-sm leading-relaxed text-white/70 md:text-base">
              O Middleware tem como responsabilidade integrar os sistemas de vendas, estoque e financeiro, garantindo que as informações estejam sempre atualizadas e sincronizadas entre as lojas online e o estoque centralizado. Ele é capaz de processar grandes volumes de dados em tempo real, permitindo que as lojas online tenham acesso a informações precisas sobre disponibilidade de produtos, preços e status dos pedidos.
            </p>
            <ul class="mt-5 flex flex-wrap gap-2" aria-label="Destaques do case">
              <li class="rounded-full border border-white/15 bg-white/[0.05] px-3 py-1 text-xs font-medium text-white/75">Alta escala</li>
              <li class="rounded-full border border-white/15 bg-white/[0.05] px-3 py-1 text-xs font-medium text-white/75">Comissionamento</li>
              <li class="rounded-full border border-white/15 bg-white/[0.05] px-3 py-1 text-xs font-medium text-white/75">Multi-loja</li>
              <li class="rounded-full border border-white/15 bg-white/[0.05] px-3 py-1 text-xs font-medium text-white/75">Relatórios</li>
            </ul>
          </div>
          <div class="flex w-full shrink-0 flex-col gap-3 sm:flex-row sm:items-center lg:w-auto lg:flex-col">
            <a href="https://middleware.isysistemas.com.br/" class="inline-flex items-center justify-center gap-2 rounded-full bg-brand px-6 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/60 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
              Ver solução Isy Middleware
              <i data-lucide="arrow-right" class="h-4 w-4 transition group-hover:translate-x-0.5" aria-hidden="true"></i>
            </a>
            <a href="#contato" class="inline-flex items-center justify-center rounded-full border border-white/25 px-6 py-3 text-sm font-semibold text-white/90 transition hover:border-white/50 hover:bg-white/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Quero algo parecido</a>
          </div>
        </div>
      </article>


      <div class="cases-reveal cases-d-8 mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
        <a href="#contato" class="inline-flex w-full items-center justify-center rounded-full bg-brand px-8 py-3.5 text-sm font-semibold text-white shadow-lg transition hover:bg-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/60 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a] sm:w-auto">
          Falar com um especialista
        </a>
        <a href="#empresas" class="inline-flex w-full items-center justify-center rounded-full border border-white/25 px-8 py-3.5 text-sm font-semibold text-white/90 transition hover:border-white/45 hover:bg-white/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a] sm:w-auto">
          Ver todas as empresas
        </a>
      </div>
    </div>
  </section>

  <!-- Depoimentos -->

  <!-- FAQ -->
  <section id="faq" class="faq-section relative border-t border-white/[0.06] px-6 py-20 md:py-24" aria-labelledby="faq-heading">
    <div class="pointer-events-none absolute inset-x-0 top-0 mx-auto h-px max-w-lg bg-gradient-to-r from-transparent via-violet-500/30 to-transparent" aria-hidden="true"></div>
    <div class="relative mx-auto max-w-3xl">
      <div class="text-center">
        <p class="text-xs font-semibold uppercase tracking-widest text-violet-400/90">Dúvidas frequentes</p>
        <h2 id="faq-heading" class="mt-3 font-heading text-3xl font-bold tracking-tight text-white sm:text-4xl">Perguntas e respostas</h2>
        <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-white/55">Tudo em um só lugar. Abra um tópico por vez — o painel usa tema escuro para leitura confortável.</p>
      </div>

      <div class="mt-12 space-y-3">
        <details name="coderush-faq" class="faq-item faq-reveal faq-d-1">
          <summary class="faq-summary">
            <span>O que é o ecossistema CodeRush?</span>
            <span class="faq-chevron"><i data-lucide="chevron-down" class="h-4 w-4" aria-hidden="true"></i></span>
          </summary>
          <div class="faq-panel">
            <div class="faq-panel-inner">
              <div class="faq-answer">
                <p>É um <strong class="font-medium text-white/90">hub de tecnologia</strong> que reúne empresas com especialidades distintas — vendas diretas e MMN, desenvolvimento de software, WordPress e automação com IA — com processos e visão de entrega alinhados.</p>
              </div>
            </div>
          </div>
        </details>

        <details name="coderush-faq" class="faq-item faq-reveal faq-d-2">
          <summary class="faq-summary">
            <span>Como escolho qual empresa do grupo fala com o meu projeto?</span>
            <span class="faq-chevron"><i data-lucide="chevron-down" class="h-4 w-4" aria-hidden="true"></i></span>
          </summary>
          <div class="faq-panel">
            <div class="faq-panel-inner">
              <div class="faq-answer">
                <p>Pelo <strong class="font-medium text-white/90">tipo de necessidade</strong>: plataforma de MMN e vendas diretas; software sob medida; WordPress e e-commerce; ou automação com n8n e IA. Você também pode usar o <a href="#contato" class="font-medium text-blue-400 underline decoration-blue-400/40 underline-offset-2 hover:text-blue-300">formulário de contato</a> para descrever o briefing e receber direcionamento.</p>
              </div>
            </div>
          </div>
        </details>

        <details name="coderush-faq" class="faq-item faq-reveal faq-d-3">
          <summary class="faq-summary">
            <span>Os sites e sistemas são seguros?</span>
            <span class="faq-chevron"><i data-lucide="chevron-down" class="h-4 w-4" aria-hidden="true"></i></span>
          </summary>
          <div class="faq-panel">
            <div class="faq-panel-inner">
              <div class="faq-answer">
                <p>As entregas seguem <strong class="font-medium text-white/90">boas práticas</strong> de segurança (HTTPS, controles de acesso e revisão de dependências conforme o escopo). Requisitos específicos — LGPD, auditoria, SSO — são tratados na fase de discovery com a equipe.</p>
              </div>
            </div>
          </div>
        </details>

        <details name="coderush-faq" class="faq-item faq-reveal faq-d-4">
          <summary class="faq-summary">
            <span>Como funciona o contato comercial?</span>
            <span class="faq-chevron"><i data-lucide="chevron-down" class="h-4 w-4" aria-hidden="true"></i></span>
          </summary>
          <div class="faq-panel">
            <div class="faq-panel-inner">
              <div class="faq-answer">
                <p>Você pode usar o <strong class="font-medium text-white/90">formulário</strong> nesta página, o e-mail <a href="mailto:contato@coderush.com.br" class="font-medium text-blue-400 hover:text-blue-300">contato@coderush.com.br</a> ou ir direto ao site da empresa do grupo que melhor encaixa no seu caso. Priorizamos retorno humano e objetivo.</p>
              </div>
            </div>
          </div>
        </details>

        <details name="coderush-faq" class="faq-item faq-reveal faq-d-5">
          <summary class="faq-summary">
            <span>Vocês trabalham com IA e automação?</span>
            <span class="faq-chevron"><i data-lucide="chevron-down" class="h-4 w-4" aria-hidden="true"></i></span>
          </summary>
          <div class="faq-panel">
            <div class="faq-panel-inner">
              <div class="faq-answer">
                <p>Sim. Há ofertas de <strong class="font-medium text-white/90">software com IA</strong>, integrações, WordPress avançado e fluxos com <strong class="font-medium text-white/90">n8n</strong> e agentes, além de consultoria em automação comercial — sempre com foco em resultado mensurável.</p>
              </div>
            </div>
          </div>
        </details>

        <details name="coderush-faq" class="faq-item faq-reveal faq-d-6">
          <summary class="faq-summary">
            <span>Atendem empresas fora do Brasil?</span>
            <span class="faq-chevron"><i data-lucide="chevron-down" class="h-4 w-4" aria-hidden="true"></i></span>
          </summary>
          <div class="faq-panel">
            <div class="faq-panel-inner">
              <div class="faq-answer">
                <p>O <strong class="font-medium text-white/90">foco principal</strong> é o mercado brasileiro. Projetos com atuação internacional podem ser avaliados caso a caso (idioma, fuso, compliance e contratos).</p>
              </div>
            </div>
          </div>
        </details>
      </div>

      <p class="mt-10 text-center text-sm text-white/45">Não encontrou o que precisa? <a href="#contato" class="font-semibold text-blue-400 underline decoration-blue-400/35 underline-offset-2 transition hover:text-blue-300">Fale conosco</a>.</p>
    </div>
  </section>

  <!-- Contato -->
  <section id="contato" class="contact-section scroll-mt-28 border-t border-white/[0.06] px-6 py-20 md:py-28" aria-labelledby="contato-heading">
    <div class="contact-ambient" aria-hidden="true"></div>
    <div class="relative mx-auto max-w-6xl">
      <h4 id="contato-heading" class="font-heading text-[30px] font-semibold">Fale conosco</h4>
      <div class="mt-2 h-1 w-[72px] rounded-full bg-white"></div>

      <div id="contact-feedback" class="contact-feedback-alert hidden" role="alert"></div>

      <div class="contact-layout">
        <div class="contact-copy">
          <h2 class="text-base leading-relaxed text-white/90 sm:text-lg">
            Preencha seu nome e WhatsApp para nosso time comercial retornar o mais breve possivel.
          </h2>
          <div class="contact-aside-card">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Atalho direto</p>
            <h3 class="mt-2 text-lg font-semibold">Prefere falar agora?</h3>
            <p class="mt-2 text-sm text-white/85">Se quiser pular o formulario, fale direto com nossa equipe comercial no WhatsApp.</p>
            <a href="https://wa.me/5511994566726?text=Ol%C3%A1%2C%20vim%20pelo%20site%20CodeRush%20e%20quero%20saber%20mais%20sobre%20as%20solu%C3%A7%C3%B5es%20do%20ecossistema." target="_blank" rel="noopener noreferrer" class="contact-direct-link mt-4">Falar no WhatsApp agora</a>
            <p class="mt-3 text-sm text-white/70"><b>Telefone para Contato:</b> 11 99456-6726</p>
          </div>
        </div>

        <div class="contact-form-card">
          <h3 class="text-lg font-semibold">Receber contato comercial</h3>
          <p class="mt-2 text-sm text-white/85">Sua mensagem vai ser respondida o mais breve possivel.</p>

          <form id="contact-lead-form" action="/enviar-contato.php" method="post" class="contact-form" data-whatsapp-phone="5511994566726" data-whatsapp-message-template="Ola, vim pelo site CodeRush. Meu nome e {nome} e meu WhatsApp e {whatsapp}. Quero saber mais sobre as solucoes do ecossistema.">
            <input type="hidden" name="redirect" value="/" />
            <input type="hidden" name="origem" value="coderush-hub" />
            <input type="hidden" name="servico" value="CodeRush Hub" />
            <input type="hidden" name="mensagem" value="Lead enviado pela home" />

            <div>
              <label for="contact-nome" class="contact-label mb-2">Nome</label>
              <input id="contact-nome" name="nome" type="text" required autocomplete="name" placeholder="Seu nome" class="contact-input" />
            </div>

            <div>
              <label for="contact-whatsapp" class="contact-label mb-2">WhatsApp</label>
              <input id="contact-whatsapp" name="whatsapp" type="tel" required autocomplete="tel" inputmode="tel" placeholder="(11) 99999-9999" class="contact-input" />
            </div>

            <button type="submit" class="contact-submit contact-submit-shine"><span class="relative z-[1]">Enviar contato</span></button>
          </form>

          <a href="https://wa.me/5511994566726?text=Ol%C3%A1%2C%20vim%20pelo%20site%20CodeRush%20e%20quero%20saber%20mais%20sobre%20as%20solu%C3%A7%C3%B5es%20do%20ecossistema." target="_blank" rel="noopener noreferrer" class="contact-direct-link mt-3">Ou falar direto no WhatsApp</a>

          <p class="contact-privacy">Usamos seus dados apenas para retorno comercial sobre o ecossistema CodeRush.</p>

          <div id="contact-success-box" class="contact-status-card is-success hidden">
            <p class="text-sm font-semibold text-white">Lead enviado com sucesso.</p>
            <p class="mt-1 text-sm text-white/80">Se o WhatsApp nao abrir automaticamente, use o botao abaixo.</p>
            <a id="contact-success-whatsapp-link" href="https://wa.me/5511994566726" target="_blank" rel="noopener noreferrer" class="contact-status-action mt-3">Abrir WhatsApp agora</a>
          </div>

          <div id="contact-error-box" class="contact-status-card is-error hidden">
            <p class="text-sm font-semibold text-white">Nao foi possivel concluir o envio.</p>
            <p class="mt-1 text-sm text-white/80">Tente novamente em instantes ou use o atalho direto para falar com nossa equipe.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer id="site-footer" class="footer-site relative px-6 pb-10 pt-14 md:pb-14 md:pt-16" aria-label="Rodapé">
    <div class="footer-top-glow" aria-hidden="true"></div>
    <div class="footer-ambient" aria-hidden="true"></div>
    <div class="relative mx-auto max-w-6xl">
      <div class="grid gap-12 md:grid-cols-12 md:gap-10 lg:gap-14">
        <div class="footer-reveal md:col-span-5">
          <p class="font-heading text-[10px] font-semibold uppercase tracking-[0.2em] text-white/35">Próximo passo</p>
          <h2 class="mt-2 font-heading text-xl font-bold tracking-tight text-white md:text-2xl">Vamos conversar sobre o seu projeto</h2>
          <p class="mt-3 max-w-md text-sm leading-relaxed text-white/55">
            Resposta humana, sem fila genérica. Escolha o canal que preferir — ou explore as empresas do ecossistema.
          </p>
          <div class="mt-6 flex flex-wrap gap-3">
            <a href="#contato" class="footer-cta-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/60 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
              <i data-lucide="arrow-right" class="h-4 w-4" aria-hidden="true"></i>
              Fale conosco
            </a>
          </div>
          <a href="mailto:contato@coderush.com.br" class="mt-6 inline-flex items-center gap-2 text-sm font-medium text-blue-400/90 transition hover:text-blue-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
            <i data-lucide="mail" class="h-4 w-4 shrink-0" aria-hidden="true"></i>
            contato@coderush.com.br
          </a>
        </div>
        <nav class="footer-reveal footer-stagger-1 md:col-span-3" aria-label="Mapa do site">
          <p class="font-heading text-[10px] font-semibold uppercase tracking-[0.2em] text-white/35">Navegação</p>
          <ul class="mt-4 space-y-2.5 text-sm">
            <li><a href="#sobre" class="footer-nav-link rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Sobre</a></li>
            <li><a href="#empresas" class="footer-nav-link rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Empresas</a></li>
            <li><a href="#cases" class="footer-nav-link rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Cases</a></li>
            <li><a href="#faq" class="footer-nav-link rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">FAQ</a></li>
          </ul>
        </nav>
        <div class="footer-reveal footer-stagger-2 md:col-span-4">
          <p class="font-heading text-[10px] font-semibold uppercase tracking-[0.2em] text-white/35">Ecossistema CodeRush</p>
          <p class="mt-2 text-xs leading-relaxed text-white/45">Marcas do grupo — qual é a sua necessidade?</p>
          <div class="mt-4 flex flex-wrap gap-2">
            <a href="https://sistemavendadireta.com.br" target="_blank" rel="noopener noreferrer" class="footer-eco-pill focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
              <span class="max-w-[11rem] truncate sm:max-w-none">sistemavendadireta.com.br</span>
              <i data-lucide="external-link" class="h-3 w-3 shrink-0 opacity-70" aria-hidden="true"></i>
            </a>
            <a href="https://codafacil.dev" target="_blank" rel="noopener noreferrer" class="footer-eco-pill focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
              codafacil.dev
              <i data-lucide="external-link" class="h-3 w-3 shrink-0 opacity-70" aria-hidden="true"></i>
            </a>
            <a href="https://fluxointeligenteia.com.br" target="_blank" rel="noopener noreferrer" class="footer-eco-pill focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
              <span class="max-w-[12rem] truncate sm:max-w-none">fluxointeligenteia.com.br</span>
              <i data-lucide="external-link" class="h-3 w-3 shrink-0 opacity-70" aria-hidden="true"></i>
            </a>
          </div>
        </div>
      </div>
      <div class="footer-reveal footer-stagger-3 mt-12 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-8 text-xs text-white/40 md:flex-row md:items-center">
        <p>© <?= date('Y') ?> CodeRush — Todos os direitos reservados.</p>
        <p class="text-center text-white/35 md:text-right">
          <span class="hidden sm:inline">Amor por   </span>
          <a href="#contato" class="font-medium text-blue-400/80 transition hover:text-blue-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">desenvolvimento de software</a>
        </p>
      </div>
    </div>
  </footer>

  <div class="fab-dock" role="group" aria-label="Ações rápidas">
    <button
      type="button"
      id="back-to-top"
      class="back-to-top-fab is-at-top"
      aria-label="Voltar ao topo da página"
    >
      <i data-lucide="chevron-up" class="h-6 w-6" aria-hidden="true"></i>
    </button>
    <a
      href="https://wa.me/5511994566726?text=Ol%C3%A1%2C%20vim%20pelo%20site%20CodeRush%20e%20quero%20falar%20com%20o%20time."
      target="_blank"
      rel="noopener noreferrer"
      class="chat-launcher"
      aria-label="Falar no WhatsApp"
    >
      <i data-lucide="message-circle" class="h-7 w-7" aria-hidden="true"></i>
    </a>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/lucide@0.469.0/dist/umd/lucide.min.js" crossorigin="anonymous"></script>

  <script src="js/scripts.js" defer></script>
</body>
</html>
