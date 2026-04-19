<?php
$seoBase = 'https://codafacil.dev';
$seoUrl = $seoBase . '/';
$seoTitle = 'Codafacil.dev — Desenvolvimento orientado por IA';
$seoDescription = 'Software sob medida orientado por IA: desenvolvimento ágil, integrações e automação para empresas que escalam com qualidade. Codafacil.dev, ecossistema CodeRush no Brasil.';
$seoOgImage = $seoBase . '/imagens/logo.webp';
$seoLogo = $seoBase . '/imagens/logo.webp';
$seoLdGraph = [
  '@context' => 'https://schema.org',
  '@graph' => [
    [
      '@type' => 'Organization',
      '@id' => $seoUrl . '#organization',
      'name' => 'Codafacil.dev',
      'url' => $seoUrl,
      'logo' => [
        '@type' => 'ImageObject',
        'url' => $seoLogo,
      ],
      'areaServed' => 'Brasil',
      'parentOrganization' => [
        '@type' => 'Organization',
        'name' => 'CodeRush',
        'url' => 'https://coderush.com.br/',
      ],
      'sameAs' => [
        $seoUrl,
        'https://coderush.com.br/',
        'https://www.sistemavendadireta.com.br/',
        'https://www.sistemavendadireta.com.br/inteligencia-artificial/',
      ],
    ],
    [
      '@type' => 'WebSite',
      '@id' => $seoUrl . '#website',
      'url' => $seoUrl,
      'name' => 'Codafacil.dev',
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
    [
      '@type' => 'Service',
      '@id' => $seoUrl . '#service',
      'name' => 'Desenvolvimento orientado por IA',
      'provider' => ['@id' => $seoUrl . '#organization'],
      'serviceType' => 'Desenvolvimento de software com IA, integração e automação',
      'areaServed' => 'Brasil',
    ],
  ],
];
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
  <meta name="theme-color" content="#0b4db6" />
  <meta name="author" content="Codafacil.dev" />
  <meta name="referrer" content="strict-origin-when-cross-origin" />
  <link rel="canonical" href="<?= htmlspecialchars($seoUrl, ENT_QUOTES, 'UTF-8') ?>" />
  <link rel="icon" type="image/webp" href="imagens/logo.webp" />
  <link rel="alternate" hreflang="pt-BR" href="<?= htmlspecialchars($seoUrl, ENT_QUOTES, 'UTF-8') ?>" />
  <link rel="alternate" hreflang="x-default" href="<?= htmlspecialchars($seoUrl, ENT_QUOTES, 'UTF-8') ?>" />

  <meta property="og:locale" content="pt_BR" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="<?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:url" content="<?= htmlspecialchars($seoUrl, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:site_name" content="Codafacil.dev" />
  <meta property="og:image" content="<?= htmlspecialchars($seoOgImage, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />
  <meta property="og:image:alt" content="Codafacil.dev — desenvolvimento de software orientado por IA" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="twitter:description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="twitter:image" content="<?= htmlspecialchars($seoOgImage, ENT_QUOTES, 'UTF-8') ?>" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/site-tailwind.css?v=<?= filemtime(__DIR__.'/css/site-tailwind.css') ?>" />
  <link rel="stylesheet" href="css/styles.css" />

  <link rel="stylesheet" href="css/site-optimizations.css" />

  <script type="application/ld+json"><?= json_encode($seoLdGraph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
</head>

<body id="top" class="min-h-full bg-[#04110d] text-white site-optimized">
  <!-- Header / Hero -->
  <header class="hero-bg">
    <div class="mx-auto max-w-7xl px-6">
      <nav class="flex items-center justify-between py-6">
        <!-- Logo -->
        <a href="#top" class="flex items-center gap-3">
          <img decoding="async" src="imagens/logo.webp" class="logo-img" alt="Codafacil.dev" width="200" height="48" />
        </a>

        <!-- Menu (desktop) -->
        <div class="hidden items-center gap-8 md:flex">
          <a href="#servicos" class="text-sm opacity-90 hover:opacity-100">Serviços</a>
          <a href="#processo" class="text-sm opacity-90 hover:opacity-100">Processo</a>
          <a href="#vantagens" class="text-sm opacity-90 hover:opacity-100">Vantagens</a>
          <a href="#stack" class="text-sm opacity-90 hover:opacity-100">Tecnologia</a>
          <a href="#clientes" class="text-sm opacity-90 hover:opacity-100">Clientes</a>
          <a href="https://www.sistemavendadireta.com.br/" target="_blank" rel="noopener noreferrer" class="text-sm opacity-90 hover:opacity-100">Sistema Venda Direta</a>
        </div>

        <!-- CTA -->
        <div class="flex items-center gap-3">
          <a href="https://wa.me/5511994566726?text=Ol%C3%A1%2C%20quero%20um%20or%C3%A7amento%20para%20meu%20projeto%20com%20a%20Codafacil."
             target="_blank"
             rel="noopener noreferrer"
             class="hidden rounded-full bg-white/10 px-5 py-3 text-sm font-semibold btn-outline hover:bg-white/15 md:inline-flex">
            Solicite no WhatsApp
          </a>

          <button class="inline-flex items-center justify-center rounded-xl bg-white/10 p-3 ring-1 ring-white/15 md:hidden"
                  aria-label="Abrir menu">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" class="opacity-90">
              <path d="M4 7h16M4 12h16M4 17h16" stroke="white" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </button>
        </div>
      </nav>

      <!-- Hero -->
      <section class="relative pb-6 pt-6 md:pb-8 md:pt-10">
        <div class="grid items-center gap-12 md:grid-cols-2">
          <!-- Left -->
          <div>
            <h1 class="text-4xl font-extrabold tracking-tight md:text-6xl">
              Software sob medida
              <br class="hidden md:block" />
              orientado por IA
            </h1>

            <p class="mt-6 max-w-xl text-base leading-relaxed text-white/80 md:text-lg">
              A <strong class="text-white">Codafacil.dev</strong> desenvolve software <strong class="text-white">ágil, escalável e robusto</strong>,
              adaptado às demandas específicas de cada projeto, usando inteligência artificial para
              <strong class="text-white">acelerar</strong> e <strong class="text-white">refinar</strong> todo o processo.
            </p>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
              <a href="#contato"
                 class="inline-flex items-center justify-center rounded-full bg-white px-6 py-4 text-sm font-bold text-[#0b4db6] hover:bg-white/95">
                Contato
                <span class="ml-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-[#0b4db6]/10">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                    <path d="M8 5l8 7-8 7" stroke="#0b4db6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </span>
              </a>

              <a href="#processo"
                 class="inline-flex items-center justify-center rounded-full bg-white/10 px-6 py-4 text-sm font-semibold btn-outline hover:bg-white/15">
                Como entregamos
              </a>

              <a href="<?= htmlspecialchars($seoBase . '/apresentacao_codafacil_desenvolvimento_com_ia.pdf', ENT_QUOTES, 'UTF-8') ?>"
                 target="_blank"
                 rel="noopener noreferrer"
                 class="inline-flex items-center justify-center rounded-full bg-white/10 px-6 py-4 text-sm font-semibold btn-outline hover:bg-white/15">
                Apresentação PDF
              </a>
            </div>

            <!-- Micro badges -->
            <div class="mt-8 flex flex-wrap gap-2">
              <span class="rounded-full bg-white/10 px-4 py-2 text-xs ring-1 ring-white/10">Entrega quinzenal</span>
              <span class="rounded-full bg-white/10 px-4 py-2 text-xs ring-1 ring-white/10">Transparência</span>
              <span class="rounded-full bg-white/10 px-4 py-2 text-xs ring-1 ring-white/10">IA + MCPs com governança</span>
            </div>
          </div>

          <!-- Right -->
          <div class="relative">
            <div id="processo" class="soft-shadow scroll-mt-28 rounded-3xl bg-white/10 p-6 ring-1 ring-white/15">

              <!-- Processo ilustrado -->
              <div class=" grid gap-4 md:grid-cols-2">
                <!-- 1 -->
                <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10">
                  <div class="flex items-start gap-3">
                    <span class="icon-wrap">
                      <!-- Clipboard -->
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M9 5h6" stroke="white" stroke-width="2" stroke-linecap="round"/>
                        <path d="M9 3h6a2 2 0 0 1 2 2v1H7V5a2 2 0 0 1 2-2z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7 6h10v15a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2V6z" stroke="white" stroke-width="2" stroke-linejoin="round"/>
                      </svg>
                    </span>
                    <div>
                      <div class="text-xs text-white/70">1) Levantamento de requisitos</div>
                      <div class="mt-1 text-base font-bold">Alinhamento e objetivo</div>
                      <div class="mt-2 text-xs text-white/75">
                        Escopo, prioridades, métricas e definição do que é “pronto”.
                      </div>
                    </div>
                  </div>
                </div>

                <!-- 2 -->
                <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10">
                  <div class="flex items-start gap-3">
                    <span class="icon-wrap">
                      <!-- Flow -->
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M5 6h6a3 3 0 0 1 3 3v6a3 3 0 0 0 3 3h2" stroke="white" stroke-width="2" stroke-linecap="round"/>
                        <path d="M5 6l2-2M5 6l2 2" stroke="white" stroke-width="2" stroke-linecap="round"/>
                        <path d="M19 18l-2-2M19 18l-2 2" stroke="white" stroke-width="2" stroke-linecap="round"/>
                      </svg>
                    </span>
                    <div>
                      <div class="text-xs text-white/70">2) Engenharia de processos</div>
                      <div class="mt-1 text-base font-bold">Mapeia o fluxo real</div>
                      <div class="mt-2 text-xs text-white/75">
                        Regras, exceções e integrações, sem suposições.
                      </div>
                    </div>
                  </div>
                </div>

                <!-- 3 -->
                <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10">
                  <div class="flex items-start gap-3">
                    <span class="icon-wrap">
                      <!-- Code -->
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M9 18l-6-6 6-6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15 6l6 6-6 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </span>
                    <div>
                      <div class="text-xs text-white/70">3) Desenvolvimento</div>
                      <div class="mt-1 text-base font-bold">Entrega incremental</div>
                      <div class="mt-2 text-xs text-white/75">
                        Implementação com cadência, padrões e IA aplicada com critério.
                      </div>
                    </div>
                  </div>
                </div>

                <!-- 4 -->
                <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10">
                  <div class="flex items-start gap-3">
                    <span class="icon-wrap">
                      <!-- Check -->
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M20 6L9 17l-5-5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </span>
                    <div>
                      <div class="text-xs text-white/70">4) Revisão</div>
                      <div class="mt-1 text-base font-bold">Qualidade e consistência</div>
                      <div class="mt-2 text-xs text-white/75">
                        Code review + guardrails para evitar retrabalho.
                      </div>
                    </div>
                  </div>
                </div>

                <!-- 5 -->
                <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10">
                  <div class="flex items-start gap-3">
                    <span class="icon-wrap">
                      <!-- Beaker -->
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M10 2v6l-5 9a3 3 0 0 0 2.6 4.5h8.8A3 3 0 0 0 19 17l-5-9V2" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 14h8" stroke="white" stroke-width="2" stroke-linecap="round"/>
                      </svg>
                    </span>
                    <div>
                      <div class="text-xs text-white/70">5) Testes</div>
                      <div class="mt-1 text-base font-bold">Confiabilidade</div>
                      <div class="mt-2 text-xs text-white/75">
                        Testes por módulo, regressão e validação do que importa.
                      </div>
                    </div>
                  </div>
                </div>

                <!-- 6 -->
                <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10">
                  <div class="flex items-start gap-3">
                    <span class="icon-wrap">
                      <!-- Rocket -->
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M5 14c4-1 6-3 7-7 2-1 5-1 7 1 2 2 2 5 1 7-4 1-6 3-7 7-2 1-5 1-7-1-2-2-2-5-1-7z" stroke="white" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M9 15l-4 4" stroke="white" stroke-width="2" stroke-linecap="round"/>
                      </svg>
                    </span>
                    <div>
                      <div class="text-xs text-white/70">6) Deploy e evolução</div>
                      <div class="mt-1 text-base font-bold">Operação sustentável</div>
                      <div class="mt-2 text-xs text-white/75">
                        Deploy previsível, observabilidade e evolução contínua.
                      </div>
                    </div>
                  </div>
                </div>

                <!-- MCPs (mantém o conceito, mas no seu formato novo e compacto) -->
              </div><!-- /grid -->
            </div>

            <div class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
            <div class="pointer-events-none absolute -left-10 -bottom-10 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
          </div>
        </div>
      </section>
    </div>
  </header>

  <main class="bg-[#04110d]">
    <section id="stack" class="scroll-mt-28 border-t border-white/10" aria-labelledby="stack-heading">
      <div class="mx-auto max-w-7xl px-6 py-12 md:py-14">
        <h2 id="stack-heading" class="text-2xl font-extrabold md:text-3xl">Tecnologia</h2>
        <p class="mt-4 max-w-3xl text-sm text-white/80 md:text-base">
          Stack moderna conforme o projeto: PHP/Laravel, APIs, integrações, pipelines de CI/CD e IA aplicada com governança — sem atalhos que viram dívida técnica.
        </p>
      </div>
    </section>

    <!-- Video -->
    <section id="video-demo" class="mx-auto max-w-7xl px-6 pb-16 pt-10 md:pb-20 md:pt-12">
      <div class="soft-shadow rounded-3xl bg-white/10 p-6 ring-1 ring-white/15 md:p-8">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
          <div>
            <h2 class="text-3xl font-extrabold md:text-4xl">Vídeo de demonstração</h2>
            <p class="mt-3 max-w-3xl text-sm text-white/80 md:text-base">
              Veja um resumo visual da proposta da Codafacil e da abordagem de desenvolvimento orientado por IA.
            </p>
          </div>
          <a href="https://youtu.be/_FbHxB9So14"
             target="_blank"
             rel="noopener noreferrer"
             class="inline-flex items-center justify-center rounded-full bg-white/10 px-5 py-3 text-sm font-semibold btn-outline hover:bg-white/15">
            Assistir no YouTube
          </a>
        </div>

        <div class="mt-6 overflow-hidden rounded-2xl ring-1 ring-white/20">
          <div class="relative w-full" style="padding-bottom:56.25%">
            <iframe class="absolute left-0 top-0 h-full w-full"
                    src="https://www.youtube.com/embed/_FbHxB9So14"
                    title="Codafacil - Demonstracao"
                    loading="lazy"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin"
                    allowfullscreen>
            </iframe>
          </div>
        </div>
      </div>
    </section>

    <!-- Serviços (sem espaço “vão” com o topo) -->
    <section id="servicos" class="services-tight mx-auto max-w-7xl px-6 pb-16 md:pb-20">
      <div class="flex items-end justify-between gap-6">
        <div>
          <h2 class="text-4xl font-extrabold md:text-5xl">Serviços</h2>
          <div class="mt-4 h-1 w-20 rounded-full bg-white/70"></div>
          <p class="mt-6 max-w-2xl text-white/80">
            Atuamos do discovery ao deploy, com cadência e transparência. A IA entra para acelerar,
            reduzir custos e elevar a qualidade final — sem “atalhos” que viram dívida técnica.
          </p>
        </div>
        <a href="#contato"
           class="hidden rounded-full bg-white/10 px-6 py-4 text-sm font-semibold btn-outline hover:bg-white/15 md:inline-flex">
          Solicitar proposta
        </a>
      </div>

      <div class="mt-12 grid gap-6 md:grid-cols-4">
        <div class="rounded-3xl bg-white/10 p-7 ring-1 ring-white/15">
          <div class="flex items-center gap-3">
            <span class="icon-wrap">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M9 18l-6-6 6-6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M15 6l6 6-6 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            <div class="font-bold">Desenvolvimento</div>
          </div>
          <p class="mt-4 text-sm text-white/80">
            Produto sob medida com arquitetura sólida, UI moderna e entrega incremental.
          </p>
          <ul class="mt-5 space-y-2 text-sm text-white/80">
            <li>• Backlog e roadmap</li>
            <li>• Implementação + testes</li>
            <li>• Deploy e evolução</li>
          </ul>
        </div>

        <div class="rounded-3xl bg-white/10 p-7 ring-1 ring-white/15">
          <div class="flex items-center gap-3">
            <span class="icon-wrap">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M21 21l-4.3-4.3" stroke="white" stroke-width="2" stroke-linecap="round"/>
                <circle cx="11" cy="11" r="6" stroke="white" stroke-width="2"/>
              </svg>
            </span>
            <div class="font-bold">Consultoria</div>
          </div>
          <p class="mt-4 text-sm text-white/80">
            Diagnóstico e plano de ação para destravar gargalos, reduzir risco e acelerar entregas.
          </p>
          <ul class="mt-5 space-y-2 text-sm text-white/80">
            <li>• Arquitetura e performance</li>
            <li>• Dívida técnica e segurança</li>
            <li>• Processos e CI/CD</li>
          </ul>
        </div>

        <div class="rounded-3xl bg-white/10 p-7 ring-1 ring-white/15">
          <div class="flex items-center gap-3">
            <span class="icon-wrap">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M12 2v4" stroke="white" stroke-width="2" stroke-linecap="round"/>
                <path d="M12 18v4" stroke="white" stroke-width="2" stroke-linecap="round"/>
                <path d="M4.9 4.9l2.8 2.8" stroke="white" stroke-width="2" stroke-linecap="round"/>
                <path d="M16.3 16.3l2.8 2.8" stroke="white" stroke-width="2" stroke-linecap="round"/>
                <path d="M2 12h4" stroke="white" stroke-width="2" stroke-linecap="round"/>
                <path d="M18 12h4" stroke="white" stroke-width="2" stroke-linecap="round"/>
                <path d="M4.9 19.1l2.8-2.8" stroke="white" stroke-width="2" stroke-linecap="round"/>
                <path d="M16.3 7.7l2.8-2.8" stroke="white" stroke-width="2" stroke-linecap="round"/>
                <circle cx="12" cy="12" r="4" stroke="white" stroke-width="2"/>
              </svg>
            </span>
            <div class="font-bold">Sustentação</div>
          </div>
          <p class="mt-4 text-sm text-white/80">
            Manutenção e melhorias contínuas com foco em estabilidade e previsibilidade.
          </p>
          <ul class="mt-5 space-y-2 text-sm text-white/80">
            <li>• Correções e evolução</li>
            <li>• Observabilidade</li>
            <li>• Menos incidentes</li>
          </ul>
        </div>

        <div class="rounded-3xl bg-white/10 p-7 ring-1 ring-white/15">
          <div class="flex items-center gap-3">
            <span class="icon-wrap">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M16 11c1.7 0 3-1.3 3-3s-1.3-3-3-3-3 1.3-3 3 1.3 3 3 3z" stroke="white" stroke-width="2"/>
                <path d="M8 11c1.7 0 3-1.3 3-3S9.7 5 8 5 5 6.3 5 8s1.3 3 3 3z" stroke="white" stroke-width="2"/>
                <path d="M2 20c0-3 3-5 6-5" stroke="white" stroke-width="2" stroke-linecap="round"/>
                <path d="M22 20c0-3-3-5-6-5" stroke="white" stroke-width="2" stroke-linecap="round"/>
                <path d="M12 20c0-3 2-5 4-5" stroke="white" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </span>
            <div class="font-bold">Complementação</div>
          </div>
          <p class="mt-4 text-sm text-white/80">
            Extensão do seu time, integrando rituais, ferramentas e metas com transparência.
          </p>
          <ul class="mt-5 space-y-2 text-sm text-white/80">
            <li>• Onboarding acelerado</li>
            <li>• Padrões e governança</li>
            <li>• Entrega consistente</li>
          </ul>
        </div>
      </div>
    </section>



    <!-- Vantagens -->
    <section id="vantagens" class="mx-auto max-w-7xl px-6 pb-16 md:pb-20">
      <div class="grid gap-8 md:grid-cols-3">
        <div class="rounded-3xl bg-white/10 p-8 ring-1 ring-white/15">
          <div class="text-lg font-bold">Redução de custos</div>
          <p class="mt-3 text-sm text-white/80">
            Automação de tarefas repetitivas e ciclos mais curtos de entrega reduzem esforço e desperdício.
          </p>
        </div>
        <div class="rounded-3xl bg-white/10 p-8 ring-1 ring-white/15">
          <div class="text-lg font-bold">Aumento de qualidade</div>
          <p class="mt-3 text-sm text-white/80">
            Padrões, revisão e testes assistidos por IA para consistência, menos bugs e menos retrabalho.
          </p>
        </div>
        <div class="rounded-3xl bg-white/10 p-8 ring-1 ring-white/15">
          <div class="text-lg font-bold">Customização real</div>
          <p class="mt-3 text-sm text-white/80">
            Soluções alinhadas ao seu contexto, processos e necessidades — sem engessar a operação.
          </p>
        </div>
      </div>
    </section>



    <!-- Clientes -->
    <section id="clientes" class="mx-auto max-w-7xl px-6 pb-16 md:pb-20">
      <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
          <h3 class="text-3xl font-extrabold">Clientes e resultados</h3>
          <p class="mt-4 max-w-2xl text-white/80">
            Projetos com foco em operação real, automação e previsibilidade para equipes de negócio.
          </p>
        </div>
        <a href="#contato" class="rounded-full bg-white px-6 py-4 text-sm font-bold text-[#0b4db6] hover:bg-white/95">
          Pedir orçamento
        </a>
      </div>

      <div class="mt-10 grid gap-6 md:grid-cols-3">
        <div class="rounded-3xl bg-white/10 p-7 ring-1 ring-white/15">
          <div class="flex min-h-14 items-center">
            <img decoding="async" src="https://emergency.com.br/logo-dark.png" alt="Emergency SAAS" class="max-h-12 w-auto object-contain" loading="lazy" />
          </div>
          <div class="mt-5 text-sm font-semibold">Emergency SAAS</div>
          <p class="mt-2 text-sm text-white/80">Soluções completas para documentação imobiliária e due diligence.</p>
          <p class="mt-3 text-sm text-white/80">
            Infraestrutura com n8n, plataforma SaaS em Laravel.
          </p>
          <a href="https://emergency.com.br" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex text-sm font-semibold text-white underline underline-offset-4">
            emergency.com.br
          </a>
        </div>

        <div class="rounded-3xl bg-white/10 p-7 ring-1 ring-white/15">
          <div class="flex min-h-14 items-center">
            <img decoding="async" src="https://hub-payments.isysistemas.com.br/logo-hub-payments.png" alt="hub Payments" class="max-h-12 w-auto object-contain" loading="lazy" />
          </div>
          <div class="mt-5 text-sm font-semibold">Hub Payments</div>
          <p class="mt-3 text-sm text-white/80">
            Sistema de gestão de pagamentos e conciliação financeira para empresas de médio e grande porte.
          </p>
          <p class="mt-3 text-sm text-white/80">Múltiplas tecnologias financeiras.</p>
          <a href="https://isysistemas.com.br/" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex text-sm font-semibold text-white underline underline-offset-4">
            isysistemas.com.br
          </a>
        </div>

        <div class="rounded-3xl bg-white/10 p-7 ring-1 ring-white/15">
          <div class="text-sm font-semibold">Em breve pode ser você</div>
          <p class="mt-4 text-sm text-white/80">
            Se sua operação precisa de automação, integração e um SaaS confiável para escalar, vamos desenhar
            juntos a próxima entrega.
          </p>
          <p class="mt-3 text-sm text-white/80">
            Planejamento técnico, execução em ciclos curtos e foco no resultado do negócio.
          </p>
          <a href="#contato" class="mt-6 inline-flex rounded-full border border-white/60 px-5 py-2.5 text-sm font-semibold hover:bg-white/10">
            Quero ser o próximo case
          </a>
        </div>
      </div>
    </section>

    <section id="blog" class="mx-auto max-w-7xl px-6 pb-16 md:pb-20" aria-labelledby="blog-heading">
      <div class="flex items-end justify-between gap-4">
        <div>
          <div class="text-sm font-semibold text-white/70">Conteudo</div>
          <h2 id="blog-heading" class="mt-3 text-2xl font-extrabold md:text-3xl">Blog Codafacil.dev</h2>
          <div class="mt-2 h-1 w-[72px] rounded-full bg-sky-300"></div>
        </div>
        <a href="blog/" class="text-sm font-semibold text-sky-300 transition hover:text-white">Ver todos os posts</a>
      </div>

      <p class="mt-5 max-w-3xl text-sm leading-relaxed text-white/75">
        Conteudo sobre software sob medida, integracoes, automacao e entrega orientada por IA para operacoes criticas.
      </p>

      <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
<!-- BLOG-HOME-CARDS:START -->
<article class="overflow-hidden rounded-2xl border border-white/15 bg-white/5" data-blog-path="2026/04/19/codafacil-php-2026-04-19/" data-blog-image="imagens/posts/codafacil-php-2026-04-19.jpg" data-blog-slug="codafacil-php-2026-04-19" data-blog-date="2026-04-19">
  <a href="2026/04/19/codafacil-php-2026-04-19/">
    <img src="imagens/posts/codafacil-php-2026-04-19.jpg" alt="A Revolução do Software Sob Medida com IA: Como PHP Pode Transformar Seu Negócio" class="h-44 w-full object-cover" width="1200" height="630" loading="lazy" />
  </a>
  <div class="p-4">
    <h2 class="text-base font-semibold leading-snug"><a href="2026/04/19/codafacil-php-2026-04-19/" class="hover:underline">A Revolução do Software Sob Medida com IA: Como PHP Pode Transformar Seu Negócio</a></h2>
    <p class="mt-2 text-sm leading-relaxed text-white/80">Descubra como a combinação de software sob medida e inteligência artificial pode otimizar operações, melhorar a tomada de decisões e trazer resultados significativos para sua em...</p>
  </div>
</article>
<article class="overflow-hidden rounded-2xl border border-white/15 bg-white/5" data-blog-path="2026/04/14/codafacil-ia-2026-04-14/" data-blog-image="imagens/posts/codafacil-ia-2026-04-14.jpg" data-blog-slug="codafacil-ia-2026-04-14" data-blog-date="2026-04-14">
  <a href="2026/04/14/codafacil-ia-2026-04-14/">
    <img src="imagens/posts/codafacil-ia-2026-04-14.jpg" alt="Codafacil.dev: como usar IA de forma pratica" class="h-44 w-full object-cover" width="1200" height="630" loading="lazy" />
  </a>
  <div class="p-4">
    <h2 class="text-base font-semibold leading-snug"><a href="2026/04/14/codafacil-ia-2026-04-14/" class="hover:underline">Codafacil.dev: como usar IA de forma pratica</a></h2>
    <p class="mt-2 text-sm leading-relaxed text-white/80">Guia objetivo sobre software sob medida com IA aplicada com foco em resultado operacional.</p>
  </div>
</article>
<!-- BLOG-HOME-CARDS:END -->
      </div>
    </section>

    <!-- Contato -->
    <section id="contato" class="scroll-mt-28 mx-auto max-w-7xl px-6 pb-24">
      <h4 class="text-[30px] font-semibold" style="font-family:'Montserrat',sans-serif">Fale conosco</h4>
      <div class="mt-2 h-1 w-[72px] rounded-full bg-white"></div>

      <div id="contact-feedback" class="contact-feedback-alert hidden" role="alert"></div>

      <div class="contact-layout">
        <div class="contact-copy">
          <h2 class="text-base leading-relaxed text-white/90 sm:text-lg">
            Preencha seu nome e WhatsApp para nosso time comercial retornar o mais breve possível.

          </h2>
          <div class="contact-aside-card">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Atalho direto</p>
            <h3 class="mt-2 text-lg font-semibold">Prefere falar agora?</h3>
            <p class="mt-2 text-sm text-white/85">Se quiser pular o formulário, fale direto com nossa equipe comercial no WhatsApp.</p>
            <a href="https://wa.me/5511994566726?text=Ol%C3%A1%2C%20quero%20um%20or%C3%A7amento%20para%20meu%20projeto%20com%20a%20Codafacil." target="_blank" rel="noopener noreferrer" class="contact-direct-link mt-4">Falar no WhatsApp agora</a>
            <p class="mt-3 text-sm text-white/70"><b>Telefone para Contato:</b> 11 99456-6726</p>
          </div>
        </div>

        <div class="contact-form-card">
          <h3 class="text-lg font-semibold">Receber contato comercial</h3>
          <p class="mt-2 text-sm text-white/85">Entraremos em contato o mais breve possível.</p>

          <form id="contact-lead-form" action="/enviar-contato.php" method="post" class="contact-form" data-whatsapp-phone="5511994566726" data-whatsapp-message-template="Olá, vim pelo site da Codafacil. Meu nome é {nome} e meu WhatsApp é {whatsapp}. Quero um orçamento para meu projeto.">
            <input type="hidden" name="redirect" value="/" />
            <input type="hidden" name="origem" value="codafacil" />
            <input type="hidden" name="servico" value="Codafacil" />
            <input type="hidden" name="mensagem" value="Lead enviado pela home" />

            <div>
              <label for="contact-nome" class="contact-label mb-2">Nome</label>
              <input id="contact-nome" name="nome" type="text" required autocomplete="name" placeholder="Seu nome" class="contact-input" />
            </div>

            <div>
              <label for="contact-whatsapp" class="contact-label mb-2">WhatsApp</label>
              <input id="contact-whatsapp" name="whatsapp" type="tel" required autocomplete="tel" inputmode="tel" placeholder="(11) 99999-9999" class="contact-input" />
            </div>

            <button type="submit" class="contact-submit contact-submit-shine"><span>Enviar contato</span></button>
          </form>

          <a href="https://wa.me/5511994566726?text=Ol%C3%A1%2C%20quero%20um%20or%C3%A7amento%20para%20meu%20projeto%20com%20a%20Codafacil." target="_blank" rel="noopener noreferrer" class="contact-direct-link mt-3">Ou falar direto no WhatsApp</a>

          <p class="contact-privacy">Usamos seus dados apenas para retorno comercial sobre a Codafacil.</p>

          <div id="contact-success-box" class="contact-status-card is-success hidden">
            <p class="text-sm font-semibold text-white">Lead enviado com sucesso.</p>
            <p class="mt-1 text-sm text-white/80">Se o WhatsApp não abrir automaticamente, use o botão abaixo.</p>
            <a id="contact-success-whatsapp-link" href="https://wa.me/5511994566726" target="_blank" rel="noopener noreferrer" class="contact-status-action mt-3">Abrir WhatsApp agora</a>
          </div>

          <div id="contact-error-box" class="contact-status-card is-error hidden">
            <p class="text-sm font-semibold text-white">Não foi possível concluir o envio.</p>
            <p class="mt-1 text-sm text-white/80">Tente novamente em instantes ou use o atalho direto para falar com nossa equipe.</p>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- WhatsApp floating button -->
  <a href="https://wa.me/5511994566726"
     class="fixed bottom-7 right-7 grid h-16 w-16 place-items-center rounded-full bg-[#25D366] shadow-lg ring-1 ring-black/10 hover:scale-[1.02] transition"
     aria-label="Chamar no WhatsApp"
     target="_blank" rel="noopener noreferrer">
    <svg width="30" height="30" viewBox="0 0 32 32" fill="none">
      <path fill="white" d="M19.11 17.6c-.2-.1-1.2-.6-1.38-.67-.19-.07-.33-.1-.47.1-.14.2-.54.67-.66.8-.12.13-.24.15-.44.05-.2-.1-.86-.32-1.64-1.03-.6-.54-1-1.2-1.12-1.4-.12-.2-.01-.31.09-.41.09-.09.2-.24.3-.36.1-.12.14-.2.21-.34.07-.14.03-.26-.01-.36-.04-.1-.47-1.13-.64-1.55-.17-.41-.34-.36-.47-.37h-.4c-.14 0-.36.05-.55.26-.19.2-.72.7-.72 1.7s.74 1.97.84 2.1c.1.13 1.46 2.24 3.54 3.14.5.22.89.35 1.19.45.5.16.95.14 1.31.09.4-.06 1.2-.49 1.37-.96.17-.47.17-.87.12-.96-.05-.09-.18-.14-.38-.24z"/>
      <path fill="white" fill-rule="evenodd" d="M16 3C8.82 3 3 8.82 3 16c0 2.27.58 4.4 1.6 6.25L3 29l6.92-1.56A12.93 12.93 0 0 0 16 29c7.18 0 13-5.82 13-13S23.18 3 16 3zm0 23.6c-2.02 0-3.9-.58-5.5-1.58l-.4-.25-4.1.92.88-4-.26-.41A10.6 10.6 0 1 1 16 26.6z" clip-rule="evenodd"/>
    </svg>
  </a>

  <footer class="border-t border-white/10 bg-[#04110d]">
    <div class="mx-auto max-w-7xl px-6 py-10">
      <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div class="text-sm text-white/70">© <span id="year"></span> Codafacil.dev — Desenvolvimento orientado por IA</div>
        <div class="flex flex-wrap gap-4 text-sm text-white/70">
          <a href="#servicos" class="hover:text-white">Serviços</a>
          <a href="#processo" class="hover:text-white">Processo</a>
          <a href="#vantagens" class="hover:text-white">Vantagens</a>
          <a href="blog/" class="hover:text-white">Blog</a>
          <a href="#contato" class="hover:text-white">Contato</a>
        </div>
      </div>
      <div class="mt-6 border-t border-white/10 pt-5">
        <p class="mb-3 text-center text-[10px] uppercase tracking-widest text-white/30">Ecossistema CodeRush</p>
        <div class="flex flex-wrap justify-center gap-2 text-xs text-white/45">
          <a href="https://coderush.com.br" target="_blank" rel="noopener noreferrer" class="rounded-full border border-white/15 px-3 py-1 transition hover:border-white/35 hover:text-white/80">coderush.com.br</a>
          <a href="https://sistemavendadireta.com.br" target="_blank" rel="noopener noreferrer" class="rounded-full border border-white/15 px-3 py-1 transition hover:border-white/35 hover:text-white/80">sistemavendadireta.com.br</a>
          <a href="https://fluxointeligenteia.com.br" target="_blank" rel="noopener noreferrer" class="rounded-full border border-white/15 px-3 py-1 transition hover:border-white/35 hover:text-white/80">fluxointeligenteia.com.br</a>
        </div>
      </div>
    </div>
  </footer>


  <script src="js/scripts.js" defer></script>
</body>
</html>
