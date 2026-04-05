<?php
declare(strict_types=1);

$mailStatus = $_GET['mail'] ?? '';
$mailStatus = in_array($mailStatus, ['ok', 'erro'], true) ? $mailStatus : '';

$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
$contactRedirect = ($scriptDir === DIRECTORY_SEPARATOR ? '' : rtrim($scriptDir, '/')) . '/';

/** Marcas exibidas no carrossel (nomes alinhados ao material oficial de integrações) */
$integrationsMarqueeBrands = [
    'RD Station Marketing',
    'Melhor Envio',
    'Getnet',
    'Twilio SendGrid',
    'Pepipost',
    'PayPal',
    'Itaú',
    'Mercado Pago',
    'D4Sign',
    'Jadlog',
    'Gerencianet',
    'DocuSign',
    'Cielo',
    'Bling',
    'Rede',
    'Asaas',
    'Iugu',
    'Frenet',
    'Correios',
    'PagSeguro',
    'YouTube',
    'Wirecard',
];

/** Clientes exibidos no carrossel (nomes em texto, mesmo padrão da secção Integrações) */
$clientesMarqueeBrands = [
    'Mauá Bank',
    'Game Station',
    'AZE Games',
    'Boliche Game Station',
    'Bolix boliche',
    'SP Diversões',
    'TELSAN',
    'Moda do Chef',
    'TOP GAME',
    'journeybike',
    'BILLIONS Investimentos',
    'ECOTREND',
    'ELIBELL',
    'organofoods',
    'SCIENCE LIFE WORLD',
    'HAIFLEX',
    'BBOM+',
    'SUSHITOP',
    'econ',
    'Clean Bit',
    'XLR INVEST',
    'UZI NATURAL BRASIL',
    'moove',
];
?>
<!doctype html>
<!--
/*
[Modulo Landing Page Comercial SVD]
@Author: André Gomes ( @acidcode )
@since 2026-02-06
Landing page publica reescrita em Tailwind, sem dependencias de WordPress, com foco em SEO e performance.
*/
-->
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sistema para Vendas Diretas e Marketing Multinível - MMN</title>
  <meta name="description" content="Sistema para Marketing Multinível. Módulos Binário, Unilevel, Gateways de Pagamento e Loja Virtual integrada. Desde 2002!" />
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
  <meta name="theme-color" content="#004AAD" />
  <meta name="author" content="Sistema Venda Direta" />
  <meta name="referrer" content="strict-origin-when-cross-origin" />
  <link rel="canonical" href="https://www.sistemavendadireta.com.br/" />
  <link rel="alternate" hreflang="pt-BR" href="https://www.sistemavendadireta.com.br/" />
  <link rel="alternate" hreflang="x-default" href="https://www.sistemavendadireta.com.br/" />

  <meta property="og:locale" content="pt_BR" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="Sistema para Vendas Diretas" />
  <meta property="og:description" content="Sistema para Marketing Multinível. Módulos Binário, Unilevel, Gateways de Pagamento e Loja Virtual integrada. Desde 2002!" />
  <meta property="og:url" content="https://www.sistemavendadireta.com.br/" />
  <meta property="og:site_name" content="Sistema Venda Direta" />
  <meta property="og:image" content="https://www.sistemavendadireta.com.br/wp-content/uploads/2023/04/Screenshot-2023-04-26-at-14.38.02.png" />
  <meta property="og:image:alt" content="Sistema para Vendas Diretas e Marketing Multinível - MMN" />
  <meta property="og:image:width" content="1888" />
  <meta property="og:image:height" content="1190" />
  <meta property="og:image:type" content="image/png" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Sistema para Vendas Diretas e Marketing Multinível - MMN" />
  <meta name="twitter:description" content="Sistema para Marketing Multinível. Módulos Binário, Unilevel, Gateways de Pagamento e Loja Virtual integrada. Desde 2002!" />
  <meta name="twitter:image" content="https://www.sistemavendadireta.com.br/wp-content/uploads/2023/04/Screenshot-2023-04-26-at-14.38.02.png" />
  <meta name="twitter:site" content="@sistemavendadireta" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&amp;family=Roboto:wght@300;400;500;700&amp;family=Roboto+Slab:wght@400;600&amp;display=swap" />
  <link rel="stylesheet" href="css/site-tailwind.css" />
  <link rel="stylesheet" href="css/site-optimizations.css" />

  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "Organization",
          "name": "Sistema Venda Direta",
          "url": "https://www.sistemavendadireta.com.br/",
          "logo": "https://www.sistemavendadireta.com.br/wp-content/uploads/2023/04/Logo-Azul-004AAD-1.png",
          "sameAs": [
            "https://facebook.com/sistemavendadireta",
            "https://www.youtube.com/@andregomes8954"
          ]
        },
        {
          "@type": "WebSite",
          "name": "Sistema Venda Direta",
          "url": "https://www.sistemavendadireta.com.br/"
        },
        {
          "@type": "FAQPage",
          "mainEntity": [
            {
              "@type": "Question",
              "name": "JÁ POSSUO UM SISTEMA, POSSO MIGRAR OS DADOS?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Sim, podemos fazer a migração dos dados do seu antigo sistema, desde que tenhamos acesso a eles de forma organizada."
              }
            },
            {
              "@type": "Question",
              "name": "JÀ TENHO UM SITE, POSSO USAR O SISTEMA VENDA DIRETA JUNTO COM MEU SITE, NO MESMO DOMÍNIO?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Pode sim, criamos um sub-domínio dentro do seu seu site, ficando por exemplo: painel.seusite.com.br. Basta nos informar depois da compra que deseja usar um domínio já existente."
              }
            },
            {
              "@type": "Question",
              "name": "JÁ TENHO UM PLANO DE NEGÓCIOS, É POSSÍVEL USÁ-LO?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Sim, a instalação contempla a parametrização do seu plano de negócios."
              }
            },
            {
              "@type": "Question",
              "name": "COMO EU VOU ADMNISTRAR O SISTEMA?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "O sistema possui um painel adminstrativo com diversos relatórios financeiros e podemos customizar diversos modelos a necessidade apresentada."
              }
            },
            {
              "@type": "Question",
              "name": "QUAL É A TECNOLOGIA USADA NO SISTEMA?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "O sistema venda direta foi desenvolvido em PHP usando banco de dados MYSQL. O sistema é hospedado nos USA em um servidor dedicado linux usando nginx e php 7.4."
              }
            },
            {
              "@type": "Question",
              "name": "ALÉM DA MENSALIDADE, EXISTE OUTROS CUSTOS?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Não temos taxas extras, poderá existir cobranças novas na medida em que o cliente peça novas funcionalidades, para isso existirá um acordo prévio."
              }
            },
            {
              "@type": "Question",
              "name": "TEM INTEGRAÇÃO COM ERP?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Nosso sistema de venda direta tem uma ótima flexibilidade para integrações, hoje temos um parceiro que é o Bling, mas podemos integrar com o seu ERP."
              }
            },
            {
              "@type": "Question",
              "name": "POSSO USAR MINHA MARCA E MEU DOMINIO?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Sim, um dos diferenciais deste sistema para venda direta é exatamente você utilizar o sistema personalizado com a marca de sua empresa."
              }
            },
            {
              "@type": "Question",
              "name": "COMO É FEITO O SUPORTE",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Oferecemos suporte direto pelo whatsapp e telefone."
              }
            }
          ]
        }
      ]
    }
  </script>
</head>
<body class="bg-brand text-white antialiased font-[var(--font-body)] site-optimized svd-header-page">
  <a href="#conteudo" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:rounded-full focus:bg-white focus:px-4 focus:py-2 focus:text-brand focus:font-semibold">
    Pular para o conteúdo
  </a>

  <header id="site-header" class="svd-header-root">
    <div class="svd-header-pill">
      <a href="/" class="svd-header-brand" aria-label="Sistema Venda Direta — início">
        <img
          decoding="async"
          src="index_svd_files/Logo-Branco-1.webp"
          alt=""
          class="svd-header-logo"
          width="1000"
          height="300"
        />
      </a>

      <nav class="svd-header-nav" aria-label="Menu principal">
        <a href="#funcionalidades" class="svd-header-link svd-header-link--active">Funcionalidades</a>
        <a href="#porque" class="svd-header-link">Porque</a>
        <a href="#vantagens" class="svd-header-link">Vantagens</a>
        <a href="#desenvolvimento-ia" class="svd-header-link">IA</a>
        <a href="#clientes" class="svd-header-link">Clientes</a>
      </nav>

      <details class="svd-header-menu">
        <summary class="svd-header-menu-btn">
          <span class="sr-only">Menu de navegação</span>
          <svg class="svd-header-icon svd-header-icon--open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
            <line x1="4" y1="7" x2="20" y2="7" />
            <line x1="4" y1="12" x2="20" y2="12" />
            <line x1="4" y1="17" x2="20" y2="17" />
          </svg>
          <svg class="svd-header-icon svd-header-icon--close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
            <line x1="6" y1="6" x2="18" y2="18" />
            <line x1="18" y1="6" x2="6" y2="18" />
          </svg>
        </summary>
        <div class="svd-header-menu-panel">
          <a href="#funcionalidades">Funcionalidades</a>
          <a href="#porque">Porque</a>
          <a href="#vantagens">Vantagens</a>
          <a href="#desenvolvimento-ia">Desenvolvimento com IA</a>
          <a href="#clientes">Clientes</a>
          <a href="#contato" class="svd-header-menu-panel-cta">Solicite um orçamento</a>
        </div>
      </details>

      <a href="#contato" class="svd-header-cta">Orçamento</a>
    </div>
  </header>

  <main id="conteudo" class="w-full max-w-none">
    <section id="secao1" class="hero-svd relative mb-2 flex min-h-[min(88svh,920px)] scroll-mt-32 flex-col overflow-hidden md:mb-6" aria-labelledby="hero-heading">
      <div class="hero-svd__backdrop" aria-hidden="true">
        <div class="hero-svd__bg-base"></div>
        <div class="hero-svd__aurora"></div>
        <div class="hero-svd__orb hero-svd__orb--1"></div>
        <div class="hero-svd__orb hero-svd__orb--2"></div>
        <div class="hero-svd__orb hero-svd__orb--3"></div>
        <div class="hero-svd__grain"></div>
        <div class="hero-svd__mesh hero-svd__mesh--fine"></div>
        <div class="hero-svd__vignette"></div>
        <div class="hero-svd__fade-bottom"></div>
        <div class="hero-svd__sheen" aria-hidden="true"></div>
      </div>

      <div class="hero-svd__content-shell relative z-10 mx-auto flex w-full max-w-[1920px] flex-1 flex-col px-4 sm:px-8 lg:px-12 xl:px-16 2xl:px-24">
      <div class="hero-svd__main flex min-h-0 flex-1 flex-col items-center justify-center py-10 md:py-14 lg:py-16">
      <div class="hero-svd__grid mx-auto flex w-full max-w-4xl flex-col items-center gap-8 text-center md:gap-10">
        <div class="hero-svd__copy flex min-w-0 flex-col items-center gap-5">
          <p class="hero-anim hero-anim--1 m-0">
            <span class="hero-svd__badge inline-flex rounded-md border border-white/15 bg-white/5 px-3 py-1.5 font-mono text-[10px] font-medium uppercase tracking-[0.32em] text-white/55 sm:text-[11px]">
              MMN · 25+ anos
            </span>
          </p>

          <div class="hero-anim hero-anim--2 w-full space-y-4">
            <h1 id="hero-heading" class="font-[var(--font-heading)] text-balance text-[1.85rem] font-extrabold leading-[1.12] tracking-tight sm:text-4xl md:text-5xl lg:text-[2.65rem] lg:leading-[1.08] xl:text-[2.85rem]">
              <span class="hero-svd__kicker mb-2 block font-mono text-[11px] font-medium uppercase tracking-[0.22em] text-white/45 sm:text-xs">Plataforma white-label para rede e venda direta</span>
              <span class="hero-svd__title-shine block">Sistema Venda Direta</span>
            </h1>
            <p class="hero-svd__subtitle mx-auto max-w-2xl text-pretty text-base leading-[1.65] text-white/70 sm:text-lg">
              Backoffice, loja e comissões na sua marca. Multi-idioma, integrações de pagamento e logística e onboarding com o time comercial.
            </p>
          </div>
        </div>

        <div class="hero-anim hero-anim--3 hero-svd__showcase w-full max-w-xl" aria-hidden="true">
          <div class="hero-svd__showcase-card">
            <div class="hero-svd__pulse-stack">
              <span class="hero-svd__pulse-ring"></span>
              <span class="hero-svd__pulse-ring"></span>
              <span class="hero-svd__pulse-ring"></span>
            </div>
            <svg class="hero-svd__mesh-svg" viewBox="0 0 400 120" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path class="hero-svd__mesh-path hero-svd__mesh-path--a" d="M20 90 Q100 20 200 60 T380 40" stroke="url(#hero-mesh-grad)" stroke-width="1.5" stroke-linecap="round" />
              <path class="hero-svd__mesh-path hero-svd__mesh-path--b" d="M0 55 L120 55 L160 25 L260 95 L400 30" stroke="rgba(255,255,255,0.12)" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" />
              <circle class="hero-svd__mesh-node" cx="120" cy="55" r="4" fill="rgba(125,211,252,0.9)" />
              <circle class="hero-svd__mesh-node hero-svd__mesh-node--b" cx="260" cy="95" r="3.5" fill="rgba(255,255,255,0.65)" />
              <circle class="hero-svd__mesh-node hero-svd__mesh-node--c" cx="320" cy="48" r="3" fill="rgba(56,189,248,0.75)" />
              <defs>
                <linearGradient id="hero-mesh-grad" x1="0%" y1="0%" x2="100%" y2="0%">
                  <stop offset="0%" stop-color="rgba(56,189,248,0.15)" />
                  <stop offset="50%" stop-color="rgba(255,255,255,0.45)" />
                  <stop offset="100%" stop-color="rgba(56,189,248,0.2)" />
                </linearGradient>
              </defs>
            </svg>
            <div class="hero-svd__eq" aria-hidden="true">
              <span class="hero-svd__eq-bar"></span>
              <span class="hero-svd__eq-bar"></span>
              <span class="hero-svd__eq-bar"></span>
              <span class="hero-svd__eq-bar"></span>
              <span class="hero-svd__eq-bar"></span>
            </div>
            <p class="hero-svd__showcase-kicker font-mono text-[9px] uppercase tracking-[0.42em] text-sky-200/50">Operação em tempo real</p>
            <div class="hero-svd__chip-row">
              <span class="hero-svd__chip">White-label</span>
              <span class="hero-svd__chip">Multi-idioma</span>
              <span class="hero-svd__chip">APIs &amp; webhooks</span>
              <span class="hero-svd__chip">Comissões</span>
            </div>
          </div>
        </div>

        <div class="hero-anim hero-anim--4 hero-svd__cta-block mx-auto w-full max-w-md">
          <div class="flex w-full flex-col gap-3 sm:max-w-xl sm:flex-row sm:items-center sm:justify-center">
            <a
              href="#contato"
              class="hero-cta-primary hero-cta-shine relative inline-flex min-h-[50px] flex-1 items-center justify-center overflow-hidden rounded-full bg-white px-7 py-3 text-xs font-bold uppercase tracking-[0.12em] text-brand shadow-[0_16px_40px_rgba(0,0,0,0.35)] ring-2 ring-white/40 transition duration-300 hover:-translate-y-0.5 hover:bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white sm:min-h-[52px] sm:min-w-[220px] sm:flex-none"
            >
              <span class="relative z-10">Agendar demonstração</span>
            </a>
            <a
              href="https://wa.me/5511994566726?text=Ol%C3%A1%2C%20vim%20pela%20home%20do%20Sistema%20Venda%20Direta%20e%20quero%20falar%20com%20o%20comercial%20sobre%20demonstra%C3%A7%C3%A3o."
              target="_blank"
              rel="noopener noreferrer"
              class="hero-cta-secondary inline-flex min-h-[50px] flex-1 items-center justify-center gap-2.5 rounded-full border border-white/55 bg-white/10 px-6 py-3 text-xs font-semibold text-white shadow-[0_8px_32px_rgba(0,0,0,0.15)] backdrop-blur-md transition duration-300 hover:border-white/85 hover:bg-white/18 sm:min-h-[52px] sm:flex-none sm:px-7"
            >
              <svg class="h-5 w-5 shrink-0 text-[#25D366]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
              Falar no WhatsApp
            </a>
          </div>
          <p class="hero-svd__cta-note mt-3 text-center text-[11px] text-white/40 sm:text-xs">Horário comercial · demonstração sem compromisso</p>
        </div>
      </div>
      </div>

      <div class="hero-svd__scroll-wrap hero-anim hero-anim--5 flex shrink-0 justify-center pb-6 pt-2 md:pb-8 md:pt-4">
        <a
          href="#funcionalidades"
          class="hero-svd__scroll-link group inline-flex items-center gap-3 rounded-lg px-3 py-2 text-white/55 transition-colors duration-200 hover:text-white/95 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-sky-400/50"
          aria-label="Descer para a secção Funcionalidades"
        >
          <span class="hero-svd__scroll-icon" aria-hidden="true">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M6 9l6 6 6-6" />
            </svg>
          </span>
          <span class="font-mono text-[10px] uppercase tracking-[0.38em] sm:text-[11px]">Funcionalidades</span>
        </a>
      </div>
      </div>
    </section>

    <div class="site-page-container mx-auto w-full max-w-[1140px] px-4 sm:px-6">
    <!-- Ícones SVG: Lucide (https://lucide.dev) — ISC License -->
    <section id="funcionalidades" class="svd-features scroll-mt-28 py-14 md:py-20" aria-labelledby="heading-funcionalidades">
      <header class="svd-section-head max-w-3xl">
        <p class="svd-section-head__eyebrow">Ecossistema</p>
        <h2 id="heading-funcionalidades" class="svd-section-head__title">Funcionalidades</h2>
        <p class="svd-section-head__lead">
          Tudo o que precisa para operar MMN e venda direta numa única plataforma white-label: pagamentos, integrações, logística, relatórios e experiência mobile.
        </p>
      </header>

      <div class="svd-features__grid mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <article data-feature-card class="svd-feature-card group relative overflow-hidden rounded-2xl border border-white/12 bg-gradient-to-br from-white/10 to-transparent p-6 text-left shadow-[0_20px_50px_rgba(0,0,0,0.2)] backdrop-blur-sm transition-[transform,box-shadow,border-color] duration-300 hover:-translate-y-1 hover:border-white/25 hover:shadow-[0_28px_60px_rgba(0,40,100,0.35)]">
          <div class="svd-feature-card__icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
          </div>
          <h3 class="mt-4 font-[var(--font-heading)] text-lg font-semibold leading-snug">Meios de pagamento</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/80">
            Pix, cartão, boleto e principais gateways com rastreio de transações e conciliação para reduzir retrabalho financeiro.
          </p>
        </article>

        <article data-feature-card class="svd-feature-card group relative overflow-hidden rounded-2xl border border-white/12 bg-gradient-to-br from-white/10 to-transparent p-6 text-left shadow-[0_20px_50px_rgba(0,0,0,0.2)] backdrop-blur-sm transition-[transform,box-shadow,border-color] duration-300 hover:-translate-y-1 hover:border-white/25 hover:shadow-[0_28px_60px_rgba(0,40,100,0.35)]">
          <div class="svd-feature-card__icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5V19A9 3 0 0 0 21 19V5"/><path d="M3 12A9 3 0 0 0 21 12"/></svg>
          </div>
          <h3 class="mt-4 font-[var(--font-heading)] text-lg font-semibold leading-snug">Integração com ERP</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/80">
            Conecte produtos, pedidos e estoque com Bling e outros ERPs para manter a operação alinhada entre canais.
          </p>
        </article>

        <article data-feature-card class="svd-feature-card group relative overflow-hidden rounded-2xl border border-white/12 bg-gradient-to-br from-white/10 to-transparent p-6 text-left shadow-[0_20px_50px_rgba(0,0,0,0.2)] backdrop-blur-sm transition-[transform,box-shadow,border-color] duration-300 hover:-translate-y-1 hover:border-white/25 hover:shadow-[0_28px_60px_rgba(0,40,100,0.35)]">
          <div class="svd-feature-card__icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
          </div>
          <h3 class="mt-4 font-[var(--font-heading)] text-lg font-semibold leading-snug">Logística</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/80">
            Transportadoras, cálculo de frete e rastreio integrados para entregas em todo o país com visibilidade para o cliente final.
          </p>
        </article>

        <article data-feature-card class="svd-feature-card group relative overflow-hidden rounded-2xl border border-white/12 bg-gradient-to-br from-white/10 to-transparent p-6 text-left shadow-[0_20px_50px_rgba(0,0,0,0.2)] backdrop-blur-sm transition-[transform,box-shadow,border-color] duration-300 hover:-translate-y-1 hover:border-white/25 hover:shadow-[0_28px_60px_rgba(0,40,100,0.35)]">
          <div class="svd-feature-card__icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
          </div>
          <h3 class="mt-4 font-[var(--font-heading)] text-lg font-semibold leading-snug">Relatórios completos</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/80">
            Rede, financeiro e operação com indicadores claros para decisões rápidas e auditoria da sua operação MMN.
          </p>
        </article>

        <article data-feature-card class="svd-feature-card group relative overflow-hidden rounded-2xl border border-white/12 bg-gradient-to-br from-white/10 to-transparent p-6 text-left shadow-[0_20px_50px_rgba(0,0,0,0.2)] backdrop-blur-sm transition-[transform,box-shadow,border-color] duration-300 hover:-translate-y-1 hover:border-white/25 hover:shadow-[0_28px_60px_rgba(0,40,100,0.35)]">
          <div class="svd-feature-card__icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="16" y="16" width="6" height="6" rx="1"/><rect x="2" y="16" width="6" height="6" rx="1"/><rect x="9" y="2" width="6" height="6" rx="1"/><path d="M5 16v-3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3"/><path d="M12 12V8"/></svg>
          </div>
          <h3 class="mt-4 font-[var(--font-heading)] text-lg font-semibold leading-snug">Planos de compensação</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/80">
            Binário, unilevel e estruturas personalizadas para o modelo da sua empresa, com regras sob medida.
          </p>
        </article>

        <article data-feature-card class="svd-feature-card group relative overflow-hidden rounded-2xl border border-white/12 bg-gradient-to-br from-white/10 to-transparent p-6 text-left shadow-[0_20px_50px_rgba(0,0,0,0.2)] backdrop-blur-sm transition-[transform,box-shadow,border-color] duration-300 hover:-translate-y-1 hover:border-white/25 hover:shadow-[0_28px_60px_rgba(0,40,100,0.35)]">
          <div class="svd-feature-card__icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/></svg>
          </div>
          <h3 class="mt-4 font-[var(--font-heading)] text-lg font-semibold leading-snug">Mobile e acesso remoto</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/80">
            Painel responsivo e seguro para distribuidores e gestores acompanharem a rede em qualquer lugar.
          </p>
        </article>
      </div>
    </section>

    <section
      id="demonstracao-video"
      class="svd-video scroll-mt-28 py-12 md:py-16"
      aria-labelledby="heading-video-demo"
      data-svd-video-section
    >
      <header class="svd-section-head max-w-3xl">
        <p class="svd-section-head__eyebrow">Demonstração</p>
        <h2 id="heading-video-demo" class="svd-section-head__title">Vídeo: funcionamento básico do sistema</h2>
        <p class="svd-section-head__lead">
          Tour rápido em português — visão geral dos módulos, painel e fluxo de operação para MMN e venda direta.
        </p>
      </header>

      <div class="svd-video__shell mt-8 md:mt-10">
        <div class="svd-video__glow" aria-hidden="true"></div>
        <div class="svd-video__frame">
          <div class="svd-video__chrome relative aspect-video overflow-hidden rounded-xl bg-black/50 ring-1 ring-white/10">
            <iframe
              class="absolute inset-0 h-full w-full"
              title="Vídeo: funcionamento básico do Sistema Venda Direta (YouTube)"
              src="https://www.youtube.com/embed/b1-JqxgRhas?rel=0"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            ></iframe>
          </div>
        </div>
      </div>
    </section>
    <noscript>
      <style>
        .svd-video .svd-video__shell {
          opacity: 1 !important;
          transform: none !important;
        }
        .svd-porque .svd-porque__visual,
        .svd-porque .svd-porque__content {
          opacity: 1 !important;
          transform: none !important;
        }
        .svd-caracteristicas .svd-caracteristicas__visual,
        .svd-caracteristicas .svd-caracteristicas__content {
          opacity: 1 !important;
          transform: none !important;
        }
        .svd-vantagens .svd-vantagens__intro,
        .svd-vantagens .svd-vantagens__card {
          opacity: 1 !important;
          transform: none !important;
        }
        .svd-integracoes .svd-integracoes__intro,
        .svd-integracoes .svd-integracoes__tags,
        .svd-integracoes .svd-integracoes__marquees {
          opacity: 1 !important;
          transform: none !important;
        }
        .svd-empresas .svd-empresas__intro,
        .svd-empresas .svd-empresas__tags,
        .svd-empresas .svd-empresas__marquees,
        .svd-empresas .svd-empresas__cta {
          opacity: 1 !important;
          transform: none !important;
        }
      </style>
    </noscript>

    <!-- Ícones: Lucide (lucide.dev) — ISC -->
    <section id="porque" class="svd-porque scroll-mt-28 py-12 md:py-20" aria-labelledby="heading-porque" data-svd-porque-section>
      <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-14">
        <div class="svd-porque__visual order-2 mx-auto w-full max-w-[440px] lg:order-1">
          <div class="svd-porque__frame">
            <div class="svd-porque__glow" aria-hidden="true"></div>
            <div class="svd-porque__lottie-inner rounded-2xl bg-gradient-to-b from-white/10 to-transparent p-[2px] ring-1 ring-white/10">
              <div class="overflow-hidden rounded-[0.9rem] bg-brand-soft/20">
                <div
                  class="lottie-box h-[220px] w-full sm:h-[300px] lg:h-[320px]"
                  data-lottie-src="index_svd_files/lottie-porque.json"
                  data-lottie-mobile="false"
                  aria-label="Animação ilustrativa da operação com o sistema"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <div class="svd-porque__content order-1 lg:order-2">
          <div class="svd-porque__intro svd-section-head">
            <p class="svd-section-head__eyebrow">Diferenciais</p>
            <h2 id="heading-porque" class="svd-section-head__title">Por que escolher o Sistema Venda Direta?</h2>
            <p class="svd-section-head__lead svd-section-head__lead--muted">
              Plataforma madura para MMN e venda direta: estável, integrada e preparada para crescer com a sua operação — do cadastro ao fechamento financeiro.
            </p>
          </div>

          <ul class="svd-porque__details mt-8 space-y-0" role="list">
            <li class="svd-porque__detail">
              <span class="svd-porque__detail-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
              </span>
              <span class="svd-porque__detail-text">
                <strong class="font-semibold text-white/95">Trajetória</strong>
                <span class="mt-0.5 block text-sm leading-relaxed text-white/75">Mais de duas décadas acompanhando redes, com processos validados em produção.</span>
              </span>
            </li>
            <li class="svd-porque__detail">
              <span class="svd-porque__detail-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 17H7A5 5 0 0 1 7 7h2"/><path d="M15 7h2a5 5 0 1 1 0 10h-2"/><line x1="8" x2="16" y1="12" y2="12"/></svg>
              </span>
              <span class="svd-porque__detail-text">
                <strong class="font-semibold text-white/95">Ecossistema conectado</strong>
                <span class="mt-0.5 block text-sm leading-relaxed text-white/75">ERP, pagamentos, logística e relatórios articulados para menos retrabalho.</span>
              </span>
            </li>
            <li class="svd-porque__detail">
              <span class="svd-porque__detail-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="21" x2="14" y1="4" y2="4"/><line x1="10" x2="3" y1="4" y2="4"/><line x1="21" x2="12" y1="12" y2="12"/><line x1="8" x2="3" y1="12" y2="12"/><line x1="21" x2="16" y1="20" y2="20"/><line x1="12" x2="3" y1="20" y2="20"/><line x1="14" x2="14" y1="2" y2="6"/><line x1="8" x2="8" y1="10" y2="14"/><line x1="16" x2="16" y1="18" y2="22"/></svg>
              </span>
              <span class="svd-porque__detail-text">
                <strong class="font-semibold text-white/95">Sob medida para o seu plano</strong>
                <span class="mt-0.5 block text-sm leading-relaxed text-white/75">Compensação e regras alinhadas ao modelo da sua empresa, com acompanhamento comercial.</span>
              </span>
            </li>
          </ul>

          <a
            href="#form"
            class="svd-porque__cta mt-8 inline-flex min-h-[48px] items-center justify-center rounded-full border border-white/65 bg-white/10 px-6 py-3 text-sm font-bold uppercase tracking-[0.12em] text-white shadow-[0_12px_36px_rgba(0,0,0,0.2)] backdrop-blur-sm transition duration-300 hover:-translate-y-0.5 hover:border-white/85 hover:bg-white/18 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
          >
            Solicitar orçamento
          </a>
        </div>
      </div>
    </section>

    <!-- Ícones módulos: Lucide (lucide.dev) — ISC -->
    <section id="secao4" class="svd-caracteristicas scroll-mt-28 py-12 md:py-20" aria-labelledby="heading-caracteristicas" data-svd-caracteristicas-section>
      <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-14">
        <div class="svd-caracteristicas__content order-1">
          <div class="svd-caracteristicas__intro svd-section-head">
            <p class="svd-section-head__eyebrow">Arquitetura</p>
            <h2 id="heading-caracteristicas" class="svd-section-head__title">Características do sistema</h2>
            <p class="svd-section-head__lead">
              <strong class="font-semibold text-white/95">Gestão para marketing multinível e venda direta.</strong>
              Pensado para redes de consumo, MMN e venda direta: um núcleo único com módulos que conversam entre si — da gestão administrativa à loja e ao escritório do distribuidor.
            </p>
          </div>

          <ul class="svd-caracteristicas__modules" role="list" aria-label="Módulos principais">
            <li class="svd-caracteristicas__mod">
              <span class="svd-caracteristicas__mod-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
              </span>
              <span class="svd-caracteristicas__mod-text">Administração</span>
            </li>
            <li class="svd-caracteristicas__mod">
              <span class="svd-caracteristicas__mod-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" x2="12" y1="22.08" y2="12"/></svg>
              </span>
              <span class="svd-caracteristicas__mod-text">Centro de distribuição</span>
            </li>
            <li class="svd-caracteristicas__mod">
              <span class="svd-caracteristicas__mod-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>
              </span>
              <span class="svd-caracteristicas__mod-text">Escritório virtual</span>
            </li>
            <li class="svd-caracteristicas__mod">
              <span class="svd-caracteristicas__mod-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
              </span>
              <span class="svd-caracteristicas__mod-text">Loja virtual</span>
            </li>
          </ul>
        </div>

        <div class="svd-caracteristicas__visual order-2 mx-auto w-full max-w-[440px]">
          <div class="svd-caracteristicas__frame">
            <div class="svd-caracteristicas__glow" aria-hidden="true"></div>
            <div class="svd-caracteristicas__lottie-inner rounded-2xl bg-gradient-to-b from-white/10 to-transparent p-[2px] ring-1 ring-white/10">
              <div class="overflow-hidden rounded-[0.9rem] bg-brand-soft/20">
                <div
                  class="lottie-box h-[220px] w-full sm:h-[300px] lg:h-[320px]"
                  data-lottie-src="index_svd_files/lottie-caracteristicas.json"
                  aria-label="Animação das características do sistema"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Ícones: Lucide (lucide.dev) — ISC -->
    <section id="vantagens" class="svd-vantagens scroll-mt-28 py-12 md:py-20" aria-labelledby="heading-vantagens" data-svd-vantagens-section>
      <header class="svd-vantagens__intro svd-section-head max-w-3xl">
        <p class="svd-section-head__eyebrow">Resultados</p>
        <h2 id="heading-vantagens" class="svd-section-head__title">Aumente pedidos e automatize vendas</h2>
        <p class="svd-section-head__lead">
          Várias vantagens em uma única plataforma: menos fricção na operação, personalização quando precisar e experiência clara para rede e gestão.
        </p>
      </header>

      <div class="svd-vantagens__grid mt-10 grid gap-5 md:grid-cols-3">
        <article class="svd-vantagens__card group relative overflow-hidden rounded-2xl border border-white/12 bg-gradient-to-br from-white/10 to-transparent p-6 text-left shadow-[0_18px_44px_rgba(0,0,0,0.22)] backdrop-blur-sm transition-[transform,box-shadow,border-color] duration-300 hover:-translate-y-1 hover:border-white/22 hover:shadow-[0_26px_56px_rgba(0,40,100,0.32)]">
          <div class="svd-vantagens__icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/></svg>
          </div>
          <h3 class="mt-4 font-[var(--font-heading)] text-lg font-semibold">Economia</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/80">
            Um ecossistema online integrado: menos ferramentas soltas, mais controle financeiro e operacional num só lugar.
          </p>
        </article>

        <article class="svd-vantagens__card group relative overflow-hidden rounded-2xl border border-white/12 bg-gradient-to-br from-white/10 to-transparent p-6 text-left shadow-[0_18px_44px_rgba(0,0,0,0.22)] backdrop-blur-sm transition-[transform,box-shadow,border-color] duration-300 hover:-translate-y-1 hover:border-white/22 hover:shadow-[0_26px_56px_rgba(0,40,100,0.32)]">
          <div class="svd-vantagens__icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
          </div>
          <h3 class="mt-4 font-[var(--font-heading)] text-lg font-semibold">Sob medida</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/80">
            Tem uma visão diferente? Evoluímos regras, integrações e fluxos com o time comercial e de projeto — sem perder a base estável do produto.
          </p>
        </article>

        <article class="svd-vantagens__card group relative overflow-hidden rounded-2xl border border-white/12 bg-gradient-to-br from-white/10 to-transparent p-6 text-left shadow-[0_18px_44px_rgba(0,0,0,0.22)] backdrop-blur-sm transition-[transform,box-shadow,border-color] duration-300 hover:-translate-y-1 hover:border-white/22 hover:shadow-[0_26px_56px_rgba(0,40,100,0.32)]">
          <div class="svd-vantagens__icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" x2="9.01" y1="9" y2="9"/><line x1="15" x2="15.01" y1="9" y2="9"/></svg>
          </div>
          <h3 class="mt-4 font-[var(--font-heading)] text-lg font-semibold">Facilidade</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/80">
            Interfaces pensadas para o consultor e para o administrador: menos cliques, menos treinamento e rotina mais previsível.
          </p>
        </article>
      </div>
    </section>

    <section id="desenvolvimento-ia" class="scroll-mt-28 py-10">
      <div class="rounded-[30px] border border-white/30 bg-white/[0.06] p-6 sm:p-8">
        <div class="grid items-center gap-6 lg:grid-cols-[1fr_auto]">
          <div>
            <header class="svd-section-head max-w-3xl">
              <p class="svd-section-head__eyebrow">CodaFácil</p>
              <h2 class="svd-section-head__title">Desenvolvimento com IA para acelerar sua operação</h2>
              <p class="svd-section-head__lead">
                O <strong>codafacil.dev</strong> é o subproduto da SVD focado em software sob medida orientado por IA.
                Atuamos com desenvolvimento, consultoria, sustentação e complementação de equipe, com processo claro:
                levantamento de requisitos, engenharia de processos, desenvolvimento incremental, revisão, testes e deploy.
              </p>
              <p class="svd-section-head__lead svd-section-head__lead--secondary">
                Isso cria sinergia direta com o Sistema Venda Direta: enquanto o SVD entrega o produto consolidado de MMN,
                o CodaFácil acelera projetos customizados, integrações e novas frentes tecnológicas da sua empresa.
              </p>
            </header>
          </div>

          <div class="flex flex-col items-stretch gap-3 sm:min-w-[280px]">
            <a href="codafacil/" class="inline-flex items-center justify-center rounded-full border border-white/75 px-5 py-2.5 text-sm font-semibold uppercase tracking-wide hover:bg-white/10">
              Conhecer CodaFácil
            </a>
          </div>
        </div>

        <div class="mt-6 grid gap-3 text-sm text-white/90 sm:grid-cols-2 lg:grid-cols-4">
          <div class="rounded-2xl border border-white/20 bg-white/5 px-4 py-3">Desenvolvimento de software sob medida</div>
          <div class="rounded-2xl border border-white/20 bg-white/5 px-4 py-3">Consultoria técnica e arquitetura</div>
          <div class="rounded-2xl border border-white/20 bg-white/5 px-4 py-3">Sustentação e evolução contínua</div>
          <div class="rounded-2xl border border-white/20 bg-white/5 px-4 py-3">IA aplicada com governança e qualidade</div>
        </div>
      </div>
    </section>

    <section id="ia-mmn" class="scroll-mt-28 py-10">
      <div class="rounded-[30px] border border-white/30 bg-white/[0.08] p-6 sm:p-8">
        <div class="grid items-center gap-6 lg:grid-cols-[1fr_auto]">
          <div>
            <header class="svd-section-head max-w-3xl">
              <p class="svd-section-head__eyebrow">Inteligência aplicada</p>
              <h2 class="svd-section-head__title">Inteligência Artificial para Multinível e Vendas Direta</h2>
              <p class="svd-section-head__lead">
                Criamos uma frente dedicada de IA para empresas de MMN e vendas direta com foco em resultado operacional:
                reduzir custo, diminuir retrabalho e acelerar a tomada de decisão.
              </p>
              <p class="svd-section-head__lead svd-section-head__lead--secondary">
                Essa abordagem conecta tecnologia, processo e operação comercial para escalar com mais previsibilidade.
              </p>
            </header>
          </div>

          <div class="flex flex-col items-stretch gap-3 sm:min-w-[300px]">
            <a href="inteligencia-artificial/" class="inline-flex items-center justify-center rounded-full border border-white/75 px-5 py-2.5 text-sm font-semibold uppercase tracking-wide hover:bg-white/10">
              Ver IA para MMN
            </a>
            <a href="inteligencia-artificial/#contato" class="inline-flex items-center justify-center rounded-full border border-white/35 px-5 py-2.5 text-sm font-semibold uppercase tracking-wide hover:bg-white/10">
              Solicitar diagnóstico de IA
            </a>
          </div>
        </div>

        <div class="mt-6 grid gap-3 text-sm text-white/90 sm:grid-cols-2 lg:grid-cols-4">
          <div class="rounded-2xl border border-white/20 bg-white/5 px-4 py-3">Automação de processos de rede</div>
          <div class="rounded-2xl border border-white/20 bg-white/5 px-4 py-3">Mais produtividade comercial</div>
          <div class="rounded-2xl border border-white/20 bg-white/5 px-4 py-3">Menor custo operacional</div>
          <div class="rounded-2xl border border-white/20 bg-white/5 px-4 py-3">Escalabilidade com governança</div>
        </div>
      </div>
    </section>

    <section id="integracoes" class="svd-integracoes scroll-mt-28 py-12 md:py-20" aria-labelledby="heading-integracoes" data-svd-integracoes-section>
      <header class="svd-integracoes__intro svd-section-head max-w-3xl">
        <p class="svd-section-head__eyebrow">Ecossistema</p>
        <h2 id="heading-integracoes" class="svd-section-head__title">Integrações com parceiros de mercado</h2>
        <p class="svd-section-head__lead">
          Conectores prontos para reduzir tempo de projeto: pagamentos, logística, ERP, assinatura eletrônica e marketing — sem reinventar integração a cada go-live.
        </p>
      </header>

      <div class="svd-integracoes__tags mt-6 flex flex-wrap gap-2" aria-hidden="true">
        <span class="rounded-full border border-white/12 bg-white/5 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-white/65">Pagamentos</span>
        <span class="rounded-full border border-white/12 bg-white/5 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-white/65">Logística</span>
        <span class="rounded-full border border-white/12 bg-white/5 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-white/65">ERP</span>
        <span class="rounded-full border border-white/12 bg-white/5 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-white/65">Assinatura</span>
        <span class="rounded-full border border-white/12 bg-white/5 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-white/65">Marketing</span>
      </div>

      <div class="svd-integracoes__marquees mt-10 space-y-4">
        <div class="integrations-marquee-wrap" role="region" aria-label="Integrações em rotação contínua">
          <div class="integrations-marquee-inner">
            <div class="integrations-marquee-segment">
              <?php foreach ($integrationsMarqueeBrands as $brandLabel) : ?>
                <div class="integrations-marquee-item">
                  <p class="integrations-marquee-item--text"><?= htmlspecialchars($brandLabel, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
              <?php endforeach; ?>
            </div>
            <div class="integrations-marquee-segment" aria-hidden="true">
              <?php foreach ($integrationsMarqueeBrands as $brandLabel) : ?>
                <div class="integrations-marquee-item">
                  <p class="integrations-marquee-item--text"><?= htmlspecialchars($brandLabel, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div class="integrations-marquee-wrap" aria-hidden="true">
          <div class="integrations-marquee-inner integrations-marquee-inner--alt">
            <div class="integrations-marquee-segment">
              <?php foreach ($integrationsMarqueeBrands as $brandLabel) : ?>
                <div class="integrations-marquee-item">
                  <p class="integrations-marquee-item--text"><?= htmlspecialchars($brandLabel, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
              <?php endforeach; ?>
            </div>
            <div class="integrations-marquee-segment" aria-hidden="true">
              <?php foreach ($integrationsMarqueeBrands as $brandLabel) : ?>
                <div class="integrations-marquee-item">
                  <p class="integrations-marquee-item--text"><?= htmlspecialchars($brandLabel, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <a
        href="#form"
        class="svd-integracoes__cta mt-10 inline-flex min-h-[48px] items-center justify-center rounded-full border border-white/65 bg-white/10 px-6 py-3 text-sm font-bold uppercase tracking-[0.12em] text-white shadow-[0_12px_36px_rgba(0,0,0,0.2)] backdrop-blur-sm transition duration-300 hover:-translate-y-0.5 hover:border-white/85 hover:bg-white/18 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
      >
        Solicitar orçamento
      </a>
    </section>

    <section id="clientes" class="scroll-mt-28 py-10" aria-labelledby="heading-clientes">
      <header class="svd-section-head max-w-3xl">
        <p class="svd-section-head__eyebrow">Depoimentos</p>
        <h2 id="heading-clientes" class="svd-section-head__title">Quem usa e recomenda</h2>
        <p class="svd-section-head__lead">Confira o que alguns clientes têm a dizer sobre o Sistema Venda Direta.</p>
      </header>

      <div class="mt-8 grid gap-4 md:grid-cols-2">
        <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <p class="text-sm leading-relaxed text-white/90">Há mais de 8 anos usamos o sistema venda direta, que nos ajuda a gerenciar nossa rede de distribuidores e a aumentar nossas vendas com perfeição, eu recomendo! Já estamos a 8 anos aprimorando e crescendo, com um escritorio virtual simples para qualquer pessoa.</p>
          <div class="mt-4 flex items-center gap-3">
            <img decoding="async" src="index_svd_files/logo@2x-q5j2vw22ajhto7ptb6lous0grda1y29olapc310134.webp" alt="Ecotrend South América" class="h-10 w-10 rounded-full bg-white/15 p-1" loading="lazy" />
            <div>
              <p class="font-semibold">Leandro Sato</p>
              <p class="text-sm text-white/80">Ecotrend South America</p>
            </div>
          </div>
          <p class="mt-3 text-base">★★★★★</p>
        </article>

        <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <p class="text-sm leading-relaxed text-white/90">Há 15 anos trabalhamos com a empresa SVD, manteve nosso sistema ERP legado de 2006 e reestruturou para novas versões, além de implementar novas funcionalidades. O sistema gerencia toda nossa operação, desde comercial ao faturamento.</p>
          <div class="mt-4 flex items-center gap-3">
            <img decoding="async" src="index_svd_files/logo_sistema-q5j2yjxlnn52kfujrc1mx3thco5hr6uayh8v191vgg.webp" alt="Emergency Doc Imob" class="h-10 w-10 rounded-full bg-white/15 p-1" loading="lazy" />
            <div>
              <p class="font-semibold">Carlos Lemos</p>
              <p class="text-sm text-white/80">Emergency Doc Imob</p>
            </div>
          </div>
          <p class="mt-3 text-base">★★★★★</p>
        </article>

        <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <p class="text-sm leading-relaxed text-white/90">O sistema de MMN da empresa nos ajuda a gerenciar nossos distribuidores em todo o mundo. Trabalhamos com vários idiomas e moedas, com centros de distribuição em diferentes países. O módulo de centro de distribuição nos auxilia muito.</p>
          <div class="mt-4 flex items-center gap-3">
            <img decoding="async" src="index_svd_files/logo-1-q5j39t21h8jnkdi31p5u9rl5dqoowqim66f4vid6yo.webp" alt="Science Life" class="h-10 w-10 rounded-full bg-white/15 p-1" loading="lazy" />
            <div>
              <p class="font-semibold">Alessandro</p>
              <p class="text-sm text-white/80">Science Life</p>
            </div>
          </div>
          <p class="mt-3 text-base">★★★★★</p>
        </article>

        <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <p class="text-sm leading-relaxed text-white/90">O sistema de Ecommerce da Sistema Venda Direta nos ajuda a gerenciar nossos clientes e vendas de vouchers. Contamos com módulos customizados de anti-fraude e controle de clientes. O sistema suporta o volume das promoções sem problemas.</p>
          <div class="mt-4 flex items-center gap-3">
            <img decoding="async" src="index_svd_files/341249332_244034884767561_2785740054709534699_n-150x150.webp" alt="SP Diversoões" class="h-10 w-10 rounded-full" loading="lazy" />
            <div>
              <p class="font-semibold">Yoshiaki Shinagawa</p>
              <p class="text-sm text-white/80">SP Diversões / Game Station</p>
            </div>
          </div>
          <p class="mt-3 text-base">★★★★★</p>
        </article>
      </div>
    </section>

    <?php require __DIR__ . '/components/sections/empresas-atendidas-grid.php'; ?>

    <section id="form" class="svd-lp-form-wrap scroll-mt-28 py-12 md:py-16" aria-labelledby="contato">
      <div class="svd-lp-form__ambient" aria-hidden="true">
        <span class="svd-lp-form__orb svd-lp-form__orb--a"></span>
        <span class="svd-lp-form__orb svd-lp-form__orb--b"></span>
        <span class="svd-lp-form__orb svd-lp-form__orb--c"></span>
      </div>

      <header class="svd-section-head max-w-3xl mb-8 md:mb-10 relative z-[1]">
        <p class="svd-section-head__eyebrow">Contato</p>
        <h2 id="contato" class="svd-section-head__title">Fale conosco</h2>
        <p class="svd-section-head__lead">Preencha seu nome e WhatsApp para nosso time comercial retornar o mais breve possível.</p>
      </header>

      <?php if ($mailStatus === 'ok'): ?>
        <div class="svd-lp-form__alert svd-lp-form__alert--ok relative z-[1] mb-6" role="status">
          Recebemos seus dados. Vamos abrir o WhatsApp com uma mensagem pronta para agilizar seu atendimento.
        </div>
      <?php elseif ($mailStatus === 'erro'): ?>
        <div class="svd-lp-form__alert svd-lp-form__alert--err relative z-[1] mb-6" role="alert">
          Não conseguimos enviar seus dados agora. Revise o WhatsApp informado ou use o atalho direto para falar com o comercial.
        </div>
      <?php endif; ?>

      <div class="svd-lp-form__layout relative z-[1]">
        <aside class="svd-lp-form__aside">
          <div class="svd-lp-form__wa-card">
            <div class="svd-lp-form__wa-icon" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
            </div>
            <h3 class="svd-lp-form__wa-title">Prefere falar agora?</h3>
            <p class="svd-lp-form__wa-text">Fale direto com o comercial no WhatsApp — resposta humana, sem fila robótica.</p>
            <a
              href="https://wa.me/5511994566726?text=Ol%C3%A1%2C%20quero%20um%20or%C3%A7amento%20e%20acesso%20%C3%A0%20demonstra%C3%A7%C3%A3o%20do%20Sistema%20Venda%20Direta."
              target="_blank"
              rel="noopener noreferrer"
              class="svd-lp-form__wa-btn"
            >
              <span class="svd-lp-form__wa-btn-glow" aria-hidden="true"></span>
              <span class="relative z-[1]">Abrir WhatsApp</span>
            </a>
            <p class="svd-lp-form__wa-phone">
              <span class="svd-lp-form__wa-phone-label">Comercial</span>
              <a href="tel:+5511994566726">11 99456-6726</a>
            </p>
          </div>
        </aside>

        <div class="svd-lp-form__panel">
          <div class="svd-lp-form__panel-inner">
            <h3 class="svd-lp-form__panel-title">Receber contato comercial</h3>
            <p class="svd-lp-form__panel-lead">Enviamos seu lead à equipe e uma pessoa real retorna pelo WhatsApp.</p>

            <form
              id="contact-lead-form"
              action="enviar-contato.php"
              method="post"
              class="svd-lp-form__fields"
              data-whatsapp-phone="5511994566726"
              data-whatsapp-message-template="Ola, vim pelo site da Sistema Venda Direta. Meu nome e {nome} e meu WhatsApp e {whatsapp}. Quero um orcamento e uma demonstracao."
            >
              <input type="hidden" name="redirect" value="<?= htmlspecialchars($contactRedirect, ENT_QUOTES, 'UTF-8') ?>" />
              <input type="hidden" name="origem" value="home-svd" />
              <input type="hidden" name="servico" value="Sistema Venda Direta" />
              <input type="hidden" name="mensagem" value="Lead enviado pela home" />

              <div class="svd-lp-form__field">
                <label for="contact-nome" class="svd-lp-form__label">Nome</label>
                <input id="contact-nome" name="nome" type="text" required autocomplete="name" placeholder="Como podemos te chamar?" class="svd-lp-form__input" />
              </div>

              <div class="svd-lp-form__field">
                <label for="contact-whatsapp" class="svd-lp-form__label">WhatsApp</label>
                <input id="contact-whatsapp" name="whatsapp" type="tel" required autocomplete="tel" inputmode="tel" placeholder="(11) 99999-9999" class="svd-lp-form__input" />
              </div>

              <button type="submit" class="svd-lp-form__submit">
                <span class="svd-lp-form__submit-shine" aria-hidden="true"></span>
                <span class="svd-lp-form__submit-text">Quero falar com o comercial</span>
              </button>
            </form>

            <?php if ($mailStatus === 'ok'): ?>
              <div class="svd-lp-form__inline-msg svd-lp-form__inline-msg--ok">
                <p class="font-semibold text-white">Lead enviado com sucesso.</p>
                <a id="contact-success-whatsapp-link" href="https://wa.me/5511994566726" target="_blank" rel="noopener noreferrer" class="svd-lp-form__inline-link">
                  Abrir WhatsApp agora
                </a>
              </div>
            <?php elseif ($mailStatus === 'erro'): ?>
              <div class="svd-lp-form__inline-msg svd-lp-form__inline-msg--err">
                <p class="font-semibold text-white">Não foi possível concluir o envio.</p>
                <p class="mt-1 text-sm text-white/80">Tente novamente em instantes ou use o atalho do WhatsApp ao lado.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>

    <section id="faq" class="svd-faq scroll-mt-28 py-12 md:py-16" aria-labelledby="heading-faq" data-svd-faq-section>
      <header class="svd-section-head max-w-3xl mb-8 md:mb-10">
        <p class="svd-section-head__eyebrow">Central de ajuda</p>
        <h2 id="heading-faq" class="svd-section-head__title">Dúvidas frequentes</h2>
        <p class="svd-section-head__lead">
          Abaixo estão as dúvidas mais frequentes. Se ainda restar alguma dúvida, entre em contato pelo ícone do WhatsApp.
        </p>
      </header>

      <div class="svd-faq__ambient" aria-hidden="true">
        <span class="svd-faq__glow svd-faq__glow--a"></span>
        <span class="svd-faq__glow svd-faq__glow--b"></span>
      </div>

      <div class="svd-faq__shell">
        <div class="svd-faq__list">
          <details class="svd-faq__item">
            <summary class="svd-faq__summary">Já possuo um sistema. Posso migrar os dados?</summary>
            <div class="svd-faq__answer">
              <p>Sim, podemos fazer a migração dos dados do seu antigo sistema, desde que tenhamos acesso a eles de forma organizada.</p>
            </div>
          </details>

          <details class="svd-faq__item">
            <summary class="svd-faq__summary">Já tenho um site. Posso usar o Sistema Venda Direta no mesmo domínio?</summary>
            <div class="svd-faq__answer">
              <p>Pode sim: criamos um subdomínio no seu site, por exemplo <span class="svd-faq__highlight">painel.seusite.com.br</span>. Basta informar após a compra que deseja usar um domínio já existente.</p>
            </div>
          </details>

          <details class="svd-faq__item">
            <summary class="svd-faq__summary">Já tenho um plano de negócios. É possível usá-lo?</summary>
            <div class="svd-faq__answer">
              <p>Sim, a instalação contempla a parametrização do seu plano de negócios.</p>
            </div>
          </details>

          <details class="svd-faq__item">
            <summary class="svd-faq__summary">Como vou administrar o sistema?</summary>
            <div class="svd-faq__answer">
              <p>O sistema possui um painel administrativo com diversos relatórios financeiros e podemos customizar modelos conforme a necessidade apresentada.</p>
            </div>
          </details>

          <details class="svd-faq__item">
            <summary class="svd-faq__summary">Qual é a tecnologia usada no sistema?</summary>
            <div class="svd-faq__answer">
              <p>O Sistema Venda Direta foi desenvolvido em PHP com banco de dados MySQL.</p>
              <p>A hospedagem é em servidor dedicado (Linux, nginx e PHP), com infraestrutura preparada para escala.</p>
            </div>
          </details>

          <details class="svd-faq__item">
            <summary class="svd-faq__summary">Além da mensalidade, existem outros custos?</summary>
            <div class="svd-faq__answer">
              <p>Não temos taxas escondidas. Novas cobranças só aparecem se você solicitar funcionalidades extras — sempre com acordo prévio.</p>
            </div>
          </details>

          <details class="svd-faq__item">
            <summary class="svd-faq__summary">Tem integração com ERP?</summary>
            <div class="svd-faq__answer">
              <p>Sim, o sistema tem flexibilidade para integrações. Hoje temos parceria com o Bling; outras integrações podem ser avaliadas com o comercial.</p>
            </div>
          </details>

          <details class="svd-faq__item">
            <summary class="svd-faq__summary">Posso usar minha marca e meu domínio?</summary>
            <div class="svd-faq__answer">
              <p>Sim. Um dos diferenciais é utilizar o sistema personalizado com a marca da sua empresa e o seu domínio.</p>
            </div>
          </details>

          <details class="svd-faq__item">
            <summary class="svd-faq__summary">Como é feito o suporte?</summary>
            <div class="svd-faq__answer">
              <p>Oferecemos suporte direto por WhatsApp e telefone.</p>
            </div>
          </details>
        </div>
      </div>
    </section>

    <section class="py-10" aria-labelledby="heading-blog-home">
      <div class="flex flex-wrap items-end justify-between gap-4">
        <header class="svd-section-head max-w-3xl">
          <p class="svd-section-head__eyebrow">Conteúdo</p>
          <h2 id="heading-blog-home" class="svd-section-head__title">Blog SVD</h2>
          <p class="svd-section-head__lead">Artigos sobre produto, tecnologia e operação — MMN, venda direta e IA aplicada.</p>
        </header>
        <a href="blog/" class="shrink-0 text-sm font-semibold text-white/85 hover:text-white">Ver todos os posts</a>
      </div>

      <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <article class="overflow-hidden rounded-2xl border border-white/20 bg-white/5">
          <a href="2026/03/18/agentes-de-ia-em-2026-mcp-stateful-e-governanca-para-operar-em-escala/">
            <img src="index_svd_files/posts/agentes-de-ia-em-2026-mcp-stateful-e-governanca-para-operar-em-escala.jpg" alt="Agentes de IA em 2026: MCP stateful e governanca para operar em escala" class="h-44 w-full object-cover" width="1200" height="630" loading="lazy" />
          </a>
          <div class="p-4">
            <h2 class="font-[var(--font-heading)] text-base font-semibold leading-snug"><a href="2026/03/18/agentes-de-ia-em-2026-mcp-stateful-e-governanca-para-operar-em-escala/" class="hover:underline">Agentes de IA em 2026: MCP stateful e governanca para operar em escala</a></h2>
            <p class="mt-2 text-sm text-white/85">Guia pratico para estruturar agentes com contexto persistente, governanca e previsibilidade de custo.</p>
          </div>
        </article>
        <article class="overflow-hidden rounded-2xl border border-white/20 bg-white/5">
          <a href="2026/03/11/soc-agentico-e-seguranca-multicloud-com-governanca-unificada/">
            <img src="index_svd_files/posts/soc-agentico-e-seguranca-multicloud-com-governanca-unificada.jpg" alt="SOC agentico e seguranca multicloud: guia pratico de governanca em 2026" class="h-44 w-full object-cover" width="1200" height="630" loading="lazy" />
          </a>
          <div class="p-4">
            <h2 class="font-[var(--font-heading)] text-base font-semibold leading-snug"><a href="2026/03/11/soc-agentico-e-seguranca-multicloud-com-governanca-unificada/" class="hover:underline">SOC agentico e seguranca multicloud: guia pratico de governanca em 2026</a></h2>
            <p class="mt-2 text-sm text-white/85">Guia pratico para unificar seguranca multicloud, acelerar resposta e reduzir risco operacional.</p>
          </div>
        </article>
        <article class="overflow-hidden rounded-2xl border border-white/20 bg-white/5">
          <a href="2026/03/04/php-8-5-3-em-producao-checklist-para-atualizar-com-seguranca/">
            <img src="index_svd_files/posts/php-8-5-3-em-producao-checklist-para-atualizar-com-seguranca.jpg" alt="PHP 8.5.3 em producao: checklist para atualizar com seguranca" class="h-44 w-full object-cover" width="1200" height="630" loading="lazy" />
          </a>
          <div class="p-4">
            <h2 class="font-[var(--font-heading)] text-base font-semibold leading-snug"><a href="2026/03/04/php-8-5-3-em-producao-checklist-para-atualizar-com-seguranca/" class="hover:underline">PHP 8.5.3 em producao: checklist para atualizar com seguranca</a></h2>
            <p class="mt-2 text-sm text-white/85">Checklist pratico para atualizar seu backend com base no ciclo oficial do PHP e reduzir risco operacional.</p>
          </div>
        </article>
      </div>
    </section>
    </div>
  </main>
<!-- BLOG-FOOTER START -->
  <footer class="svd-lp-footer svd-lp-footer--clean" role="contentinfo">
    <div class="svd-lp-footer__ambient" aria-hidden="true">
      <span class="svd-lp-footer__aurora"></span>
      <span class="svd-lp-footer__orb svd-lp-footer__orb--a"></span>
      <span class="svd-lp-footer__orb svd-lp-footer__orb--b"></span>
      <span class="svd-lp-footer__grain"></span>
    </div>
    <div class="svd-lp-footer__topline" aria-hidden="true">
      <span class="svd-lp-footer__topline-beam"></span>
    </div>

    <div class="svd-lp-footer__inner mx-auto w-full max-w-[1140px] px-4 sm:px-6">
      <div class="svd-lp-footer__main">
        <div class="svd-lp-footer__brand">
          <a href="/" class="svd-lp-footer__logo-link">
            <img decoding="async" src="index_svd_files/Logo-Branco-1.webp" alt="Sistema Venda Direta" class="svd-lp-footer__logo" width="1000" height="300" loading="lazy" />
          </a>
          <p class="svd-lp-footer__tagline">
            Soluções para operação comercial, vendas diretas e evolução com IA aplicada ao negócio — com time comercial e projeto no Brasil.
          </p>
          <ul class="svd-lp-footer__contacts mt-5 space-y-2 text-sm">
            <li>
              <a href="tel:+5511994566726" class="svd-lp-footer__contact-link">
                <span class="svd-lp-footer__contact-icon" aria-hidden="true">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </span>
                11 99456-6726
              </a>
            </li>
            <li>
              <a href="mailto:contato@sistemavendadireta.com.br" class="svd-lp-footer__contact-link">
                <span class="svd-lp-footer__contact-icon" aria-hidden="true">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </span>
                contato@sistemavendadireta.com.br
              </a>
            </li>
          </ul>
        </div>

        <nav class="svd-lp-footer__nav" aria-label="Menu institucional">
          <p class="svd-lp-footer__nav-title">Navegar</p>
          <ul class="svd-lp-footer__nav-list">
            <li><a href="/" class="svd-lp-footer__link">Sistema Venda Direta</a></li>
            <li><a href="wordpress/" class="svd-lp-footer__link">WordPress</a></li>
            <li><a href="codafacil/" class="svd-lp-footer__link">CodaFácil · IA</a></li>
            <li><a href="inteligencia-artificial/" class="svd-lp-footer__link">Multinível com IA</a></li>
            <li><a href="blog/" class="svd-lp-footer__link">Blog</a></li>
          </ul>
        </nav>

        <div class="svd-lp-footer__cta-col">
          <p class="svd-lp-footer__cta-kicker">+25 anos</p>
          <p class="svd-lp-footer__cta-text">Experiência em sistemas para MMN e venda direta.</p>
          <a href="/#contato" class="svd-lp-footer__cta-btn">
            <span class="svd-lp-footer__cta-btn-ripple" aria-hidden="true"></span>
            <span class="svd-lp-footer__cta-btn-label">Solicitar orçamento</span>
          </a>
          <div class="svd-lp-footer__social">
            <a href="https://facebook.com/sistemavendadireta" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="svd-lp-footer__social-btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            </a>
            <a href="https://www.youtube.com/@andregomes8954" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="svd-lp-footer__social-btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
            </a>
          </div>
        </div>
      </div>

      <div class="svd-lp-footer__partners">
        <span class="svd-lp-footer__partners-label">Ecossistema</span>
        <div class="svd-lp-footer__pills">
          <a href="https://codafacil.dev" target="_blank" rel="noopener noreferrer" class="svd-lp-footer__pill">codafacil.dev</a>
          <a href="https://wordpressconsultoria.com.br" target="_blank" rel="noopener noreferrer" class="svd-lp-footer__pill">wordpressconsultoria.com.br</a>
          <a href="https://fluxointeligenteia.com.br" target="_blank" rel="noopener noreferrer" class="svd-lp-footer__pill">fluxointeligenteia.com.br</a>
        </div>
      </div>

      <div class="svd-lp-footer__bottom">
        <p class="svd-lp-footer__copy">© <?php echo date('Y'); ?> Sistema Venda Direta. Todos os direitos reservados.</p>
      </div>
    </div>
  </footer>
  <!-- BLOG-FOOTER END -->

  <button type="button" id="svd-backtotop" class="svd-backtotop" aria-label="Voltar ao topo" title="Voltar ao topo">
    <span class="svd-backtotop__glow" aria-hidden="true"></span>
    <span class="svd-backtotop__inner">
      <svg class="svd-backtotop__icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" d="M18 15l-6-6-6 6" />
      </svg>
    </span>
  </button>

  <a href="https://wa.me/+5511994566726" target="_blank" rel="noopener noreferrer" aria-label="Falar no WhatsApp" class="fixed bottom-3 right-3 z-[70] inline-flex items-center gap-2 rounded-full bg-[#25D366] px-4 py-3 text-sm font-bold text-white shadow-[0_10px_24px_rgba(0,0,0,0.35)] ring-2 ring-white/30 sm:bottom-4 sm:right-4 sm:h-14 sm:w-14 sm:justify-center sm:px-0">
    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/20 text-base leading-none">W</span>
    <span class="sm:hidden">WhatsApp</span>
  </a>

  <script src="index_svd_files/lottie.min.js" defer></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      var containers = document.querySelectorAll(".lottie-box[data-lottie-src]");
      var leadForm = document.getElementById("contact-lead-form");
      var leadNameInput = document.getElementById("contact-nome");
      var leadWhatsappInput = document.getElementById("contact-whatsapp");
      var successWhatsappLink = document.getElementById("contact-success-whatsapp-link");
      var leadStorageKey = "svd-contact-lead";
      var urlParams = new URLSearchParams(window.location.search);
      var mailStatus = urlParams.get("mail");

      var featureCards = document.querySelectorAll("[data-feature-card]");
      var prefersReducedMotionFeatures =
        window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

      var headerMenu = document.querySelector(".svd-header-menu");
      if (headerMenu) {
        headerMenu.querySelectorAll(".svd-header-menu-panel a").forEach(function (link) {
          link.addEventListener("click", function () {
            headerMenu.removeAttribute("open");
          });
        });
      }

      var backToTopBtn = document.getElementById("svd-backtotop");
      if (backToTopBtn) {
        var backToTopThreshold = 420;
        var setBackToTopVisibility = function () {
          if (window.scrollY > backToTopThreshold) {
            backToTopBtn.classList.add("svd-backtotop--visible");
          } else {
            backToTopBtn.classList.remove("svd-backtotop--visible");
          }
        };
        window.addEventListener("scroll", setBackToTopVisibility, { passive: true });
        setBackToTopVisibility();
        backToTopBtn.addEventListener("click", function () {
          window.scrollTo({
            top: 0,
            behavior: prefersReducedMotionFeatures ? "auto" : "smooth"
          });
        });
      }

      if (featureCards.length) {
        if (prefersReducedMotionFeatures || !("IntersectionObserver" in window)) {
          featureCards.forEach(function (card) {
            card.style.opacity = "1";
            card.style.transform = "none";
          });
        } else {
          var featureObserver = new IntersectionObserver(
            function (entries, observer) {
              entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                  return;
                }
                entry.target.style.opacity = "1";
                entry.target.style.transform = "translateY(0)";
                observer.unobserve(entry.target);
              });
            },
            { threshold: 0.12, rootMargin: "0px 0px -40px 0px" }
          );
          featureCards.forEach(function (card, index) {
            card.style.opacity = "0";
            card.style.transform = "translateY(18px)";
            var delay = index * 75;
            card.style.transition =
              "opacity 520ms cubic-bezier(0.22, 1, 0.36, 1) " +
              delay +
              "ms, transform 520ms cubic-bezier(0.22, 1, 0.36, 1) " +
              delay +
              "ms";
            featureObserver.observe(card);
          });
        }
      }

      var videoDemoSection = document.querySelector("[data-svd-video-section]");
      if (videoDemoSection) {
        if (prefersReducedMotionFeatures || !("IntersectionObserver" in window)) {
          videoDemoSection.classList.add("svd-video--inview");
        } else {
          var videoDemoObserver = new IntersectionObserver(
            function (entries, observer) {
              entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                  return;
                }
                entry.target.classList.add("svd-video--inview");
                observer.unobserve(entry.target);
              });
            },
            { threshold: 0.14, rootMargin: "0px 0px -48px 0px" }
          );
          videoDemoObserver.observe(videoDemoSection);
        }
      }

      var porqueSection = document.querySelector("[data-svd-porque-section]");
      if (porqueSection) {
        if (prefersReducedMotionFeatures || !("IntersectionObserver" in window)) {
          porqueSection.classList.add("svd-porque--inview");
        } else {
          var porqueObserver = new IntersectionObserver(
            function (entries, observer) {
              entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                  return;
                }
                entry.target.classList.add("svd-porque--inview");
                observer.unobserve(entry.target);
              });
            },
            { threshold: 0.12, rootMargin: "0px 0px -56px 0px" }
          );
          porqueObserver.observe(porqueSection);
        }
      }

      var caracteristicasSection = document.querySelector("[data-svd-caracteristicas-section]");
      if (caracteristicasSection) {
        if (prefersReducedMotionFeatures || !("IntersectionObserver" in window)) {
          caracteristicasSection.classList.add("svd-caracteristicas--inview");
        } else {
          var caracteristicasObserver = new IntersectionObserver(
            function (entries, observer) {
              entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                  return;
                }
                entry.target.classList.add("svd-caracteristicas--inview");
                observer.unobserve(entry.target);
              });
            },
            { threshold: 0.12, rootMargin: "0px 0px -56px 0px" }
          );
          caracteristicasObserver.observe(caracteristicasSection);
        }
      }

      var vantagensSection = document.querySelector("[data-svd-vantagens-section]");
      if (vantagensSection) {
        if (prefersReducedMotionFeatures || !("IntersectionObserver" in window)) {
          vantagensSection.classList.add("svd-vantagens--inview");
        } else {
          var vantagensObserver = new IntersectionObserver(
            function (entries, observer) {
              entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                  return;
                }
                entry.target.classList.add("svd-vantagens--inview");
                observer.unobserve(entry.target);
              });
            },
            { threshold: 0.1, rootMargin: "0px 0px -48px 0px" }
          );
          vantagensObserver.observe(vantagensSection);
        }
      }

      var integracoesSection = document.querySelector("[data-svd-integracoes-section]");
      if (integracoesSection) {
        if (prefersReducedMotionFeatures || !("IntersectionObserver" in window)) {
          integracoesSection.classList.add("svd-integracoes--inview");
        } else {
          var integracoesObserver = new IntersectionObserver(
            function (entries, observer) {
              entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                  return;
                }
                entry.target.classList.add("svd-integracoes--inview");
                observer.unobserve(entry.target);
              });
            },
            { threshold: 0.08, rootMargin: "0px 0px -40px 0px" }
          );
          integracoesObserver.observe(integracoesSection);
        }
      }

      var empresasSection = document.querySelector("[data-svd-empresas-section]");
      if (empresasSection) {
        if (prefersReducedMotionFeatures || !("IntersectionObserver" in window)) {
          empresasSection.classList.add("svd-empresas--inview");
        } else {
          var empresasObserver = new IntersectionObserver(
            function (entries, observer) {
              entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                  return;
                }
                entry.target.classList.add("svd-empresas--inview");
                observer.unobserve(entry.target);
              });
            },
            { threshold: 0.1, rootMargin: "0px 0px -48px 0px" }
          );
          empresasObserver.observe(empresasSection);
        }
      }

      function normalizePhone(value) {
        return (value || "").replace(/\D+/g, "");
      }

      function getStoredLead() {
        try {
          var rawLead = window.sessionStorage.getItem(leadStorageKey);
          return rawLead ? JSON.parse(rawLead) : null;
        } catch (error) {
          return null;
        }
      }

      function buildWhatsappUrl(lead) {
        var fallbackMessage = "Ola, quero um orcamento e acesso a demonstracao do Sistema Venda Direta.";
        var phone = leadForm ? (leadForm.getAttribute("data-whatsapp-phone") || "5511994566726") : "5511994566726";
        var template = leadForm ? (leadForm.getAttribute("data-whatsapp-message-template") || fallbackMessage) : fallbackMessage;
        var safeLead = lead || {};
        if (!safeLead.nome && !safeLead.whatsapp) {
          return "https://wa.me/" + phone + "?text=" + encodeURIComponent(fallbackMessage);
        }
        var message = template
          .replace("{nome}", (safeLead.nome || "Nao informado").trim())
          .replace("{whatsapp}", (safeLead.whatsapp || "Nao informado").trim());

        return "https://wa.me/" + phone + "?text=" + encodeURIComponent(message);
      }

      if (leadForm) {
        if (mailStatus !== "ok") {
          var previousLead = getStoredLead();
          if (previousLead) {
            if (leadNameInput && !leadNameInput.value) {
              leadNameInput.value = previousLead.nome || "";
            }
            if (leadWhatsappInput && !leadWhatsappInput.value) {
              leadWhatsappInput.value = previousLead.whatsapp || "";
            }
          }
        }

        leadForm.addEventListener("submit", function () {
          var payload = {
            nome: leadNameInput ? leadNameInput.value.trim() : "",
            whatsapp: leadWhatsappInput ? leadWhatsappInput.value.trim() : "",
            whatsappDigits: normalizePhone(leadWhatsappInput ? leadWhatsappInput.value : "")
          };

          try {
            window.sessionStorage.setItem(leadStorageKey, JSON.stringify(payload));
          } catch (error) {
          }
        });
      }

      if (successWhatsappLink) {
        var storedLead = getStoredLead();
        successWhatsappLink.href = buildWhatsappUrl(storedLead);

        if (mailStatus === "ok") {
          window.setTimeout(function () {
            var popup = window.open(successWhatsappLink.href, "_blank", "noopener,noreferrer");
            if (popup) {
              popup.opener = null;
            }
          }, 250);

          try {
            window.sessionStorage.removeItem(leadStorageKey);
          } catch (error) {
          }
        }
      } else if (mailStatus === "erro") {
        try {
          window.sessionStorage.removeItem(leadStorageKey);
        } catch (error) {
        }
      }

      if (!containers.length || !window.lottie) {
        containers = [];
      }

      var reduceMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
      var isSmallScreen = window.matchMedia && window.matchMedia("(max-width: 767px)").matches;
      var startAnimation = function (container) {
        if (container.dataset.lottieLoaded === "1") {
          return;
        }

        var src = container.getAttribute("data-lottie-src");
        if (!src) {
          return;
        }

        if (container.getAttribute("data-lottie-mobile") === "false" && isSmallScreen) {
          return;
        }

        window.lottie.loadAnimation({
          container: container,
          renderer: "svg",
          loop: !reduceMotion,
          autoplay: !reduceMotion,
          path: src
        });

        container.dataset.lottieLoaded = "1";
      };

      if (!containers.length) {
      } else if (!("IntersectionObserver" in window)) {
        containers.forEach(startAnimation);
      } else {
        var observer = new IntersectionObserver(function (entries, currentObserver) {
          entries.forEach(function (entry) {
            if (!entry.isIntersecting) {
              return;
            }
            startAnimation(entry.target);
            currentObserver.unobserve(entry.target);
          });
        }, { rootMargin: "120px 0px" });

        containers.forEach(function (container) {
          observer.observe(container);
        });
      }
    });
  </script>
</body>
</html>
