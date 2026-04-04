<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>CodeRush — Hub de Tecnologia</title>
  <meta name="description" content="CodeRush é um hub central de tecnologia que reúne múltiplas empresas e serviços especializados em vendas diretas, desenvolvimento de software, WordPress, automação com IA e design digital." />
  <meta name="robots" content="index, follow" />
  <link rel="stylesheet" href="css/site-tailwind.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet" />
  <style>
    body { background: #020b1a; color: #f0f4ff; }
    .card-hover { transition: transform 0.25s ease, box-shadow 0.25s ease; }
    .card-hover:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(0,74,173,0.25); }
    .gradient-text { background: linear-gradient(135deg, #60a5fa, #a78bfa); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
  </style>
</head>
<body class="min-h-screen antialiased">

  <!-- Header -->
  <header class="sticky top-0 z-50 border-b border-white/10 bg-[#020b1a]/90 backdrop-blur-md">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
      <div class="flex items-center gap-2">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand font-heading text-sm font-bold text-white">CR</div>
        <span class="font-heading text-xl font-bold tracking-tight">CodeRush</span>
      </div>
      <nav class="hidden items-center gap-6 text-sm text-white/75 md:flex">
        <a href="#empresas" class="transition hover:text-white">Empresas</a>
        <a href="#sobre" class="transition hover:text-white">Sobre</a>
        <a href="#contato" class="rounded-full bg-brand px-4 py-1.5 text-white transition hover:bg-blue-600">Contato</a>
      </nav>
    </div>
  </header>

  <!-- Hero -->
  <section class="relative overflow-hidden px-6 py-24 text-center">
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
      <div class="absolute left-1/2 top-0 h-[500px] w-[800px] -translate-x-1/2 rounded-full bg-brand/20 blur-[120px]"></div>
    </div>
    <div class="relative mx-auto max-w-3xl">
      <span class="inline-block rounded-full border border-blue-500/30 bg-blue-500/10 px-4 py-1 text-xs font-semibold uppercase tracking-widest text-blue-300">Hub de Tecnologia</span>
      <h1 class="mt-6 font-heading text-5xl font-extrabold leading-tight tracking-tight sm:text-6xl">
        Soluções digitais<br/><span class="gradient-text">que geram resultado</span>
      </h1>
      <p class="mt-6 text-lg leading-relaxed text-white/75">
        CodeRush é um hub central de tecnologia que reúne empresas especializadas em vendas diretas, desenvolvimento de software, WordPress, automação com IA e design digital.
      </p>
      <div class="mt-8 flex flex-wrap justify-center gap-4">
        <a href="#empresas" class="rounded-full bg-brand px-7 py-3 text-sm font-semibold text-white transition hover:bg-blue-600">Conheça nossas empresas</a>
        <a href="#contato" class="rounded-full border border-white/25 px-7 py-3 text-sm font-semibold text-white/90 transition hover:border-white/60 hover:bg-white/5">Fale conosco</a>
      </div>
    </div>
  </section>

  <!-- Empresas -->
  <section id="empresas" class="px-6 py-20">
    <div class="mx-auto max-w-6xl">
      <div class="mb-12 text-center">
        <p class="text-xs font-semibold uppercase tracking-widest text-blue-400">Portfólio</p>
        <h2 class="mt-2 font-heading text-4xl font-bold">Nossas empresas</h2>
        <p class="mt-3 text-white/60">Cada empresa com especialidade própria, integradas em um ecossistema tecnológico coeso.</p>
      </div>

      <!-- Linha 1: 3 cards -->
      <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

        <!-- Sistema Venda Direta -->
        <a href="/sistemavendadireta/" class="card-hover group block rounded-2xl border border-white/10 bg-white/[0.04] p-6">
          <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-blue-600/20 text-2xl">🛒</div>
          <h3 class="font-heading text-lg font-bold text-white group-hover:text-blue-300 transition">Sistema Venda Direta</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/65">Plataforma completa para empresas de vendas diretas e marketing multinível. Gestão de força de vendas, lojas virtuais, backoffice e relatórios gerenciais.</p>
          <span class="mt-4 inline-block text-xs font-semibold text-blue-400">sistemavendadireta.com.br →</span>
        </a>

        <!-- Codafacil -->
        <a href="/codafacil/" class="card-hover group block rounded-2xl border border-white/10 bg-white/[0.04] p-6">
          <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-violet-600/20 text-2xl">🤖</div>
          <h3 class="font-heading text-lg font-bold text-white group-hover:text-violet-300 transition">Codafacil.dev</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/65">Desenvolvimento de software sob medida com IA. Soluções ágeis e escaláveis, entregas quinzenais, stack moderno com Laravel, Livewire e Tailwind.</p>
          <span class="mt-4 inline-block text-xs font-semibold text-violet-400">codafacil.dev →</span>
        </a>

        <!-- WordPress Consultoria -->
        <a href="/wordpressconsultoria/" class="card-hover group block rounded-2xl border border-white/10 bg-white/[0.04] p-6">
          <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-sky-600/20 text-2xl">🌐</div>
          <h3 class="font-heading text-lg font-bold text-white group-hover:text-sky-300 transition">WordPress Consultoria</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/65">Desenvolvimento sob medida para WordPress. Plugins personalizados, gateways de pagamento, integrações com ERP, multi-lojas e customização de templates.</p>
          <span class="mt-4 inline-block text-xs font-semibold text-sky-400">wordpressconsultoria.com.br →</span>
        </a>

      </div>

      <!-- Linha 2: 2 cards centralizados -->
      <div class="mt-6 grid grid-cols-2 gap-6 lg:mx-auto lg:w-2/3">

        <!-- FluxoInteligente IA -->
        <a href="/fluxointeligenteia/" class="card-hover group block rounded-2xl border border-white/10 bg-white/[0.04] p-6">
          <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-600/20 text-2xl">⚡</div>
          <h3 class="font-heading text-lg font-bold text-white group-hover:text-emerald-300 transition">FluxoInteligente IA</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/65">Consultoria de automação que transforma processos complexos em fluxos inteligentes. Agentes integrados ao n8n, automação comercial e IA aplicada.</p>
          <span class="mt-4 inline-block text-xs font-semibold text-emerald-400">fluxointeligenteia.com.br →</span>
        </a>

        <!-- Traço Creative Lab -->
        <div class="card-hover block rounded-2xl border border-white/10 bg-white/[0.04] p-6 opacity-75">
          <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-rose-600/20 text-2xl">🎨</div>
          <h3 class="font-heading text-lg font-bold text-white">Traço Creative Lab</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/65">Experiências digitais completas: design e UX, prototipagem de interfaces e desenvolvimento front-end para web e mobile. Do conceito à implementação.</p>
          <span class="mt-4 inline-block rounded-full border border-white/20 px-3 py-0.5 text-xs text-white/45">Em breve</span>
        </div>

      </div>
    </div>
  </section>

  <!-- Sobre -->
  <section id="sobre" class="px-6 py-20">
    <div class="mx-auto max-w-4xl rounded-3xl border border-white/10 bg-white/[0.03] p-10 text-center">
      <p class="text-xs font-semibold uppercase tracking-widest text-blue-400">O que é o CodeRush</p>
      <h2 class="mt-3 font-heading text-3xl font-bold">Um hub central de tecnologia</h2>
      <p class="mt-4 text-base leading-relaxed text-white/70">
        Reunimos múltiplas empresas e serviços especializados sob um mesmo ecossistema. Cada empresa mantém sua identidade e foco, enquanto compartilhamos metodologias, processos e a visão de entregar tecnologia que realmente funciona para o negócio do cliente.
      </p>
      <div class="mt-8 grid gap-6 sm:grid-cols-3">
        <div>
          <p class="font-heading text-3xl font-extrabold text-blue-400">5</p>
          <p class="mt-1 text-sm text-white/60">Empresas no grupo</p>
        </div>
        <div>
          <p class="font-heading text-3xl font-extrabold text-blue-400">+20</p>
          <p class="mt-1 text-sm text-white/60">Anos de experiência</p>
        </div>
        <div>
          <p class="font-heading text-3xl font-extrabold text-blue-400">IA</p>
          <p class="mt-1 text-sm text-white/60">No centro de tudo</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Contato -->
  <section id="contato" class="px-6 py-16 text-center">
    <div class="mx-auto max-w-xl">
      <h2 class="font-heading text-3xl font-bold">Quer conversar?</h2>
      <p class="mt-3 text-white/65">Entre em contato com a empresa do grupo que melhor atende sua necessidade, ou fale diretamente conosco.</p>
      <a href="mailto:contato@coderush.com.br" class="mt-6 inline-block rounded-full bg-brand px-8 py-3 text-sm font-semibold text-white transition hover:bg-blue-600">contato@coderush.com.br</a>
    </div>
  </section>

  <!-- Footer -->
  <footer class="border-t border-white/10 px-6 py-8 text-center text-xs text-white/40">
    <p class="mb-3 text-white/30 text-[10px] uppercase tracking-widest">Ecossistema CodeRush</p>
    <div class="mb-4 flex flex-wrap justify-center gap-2">
      <a href="https://sistemavendadireta.com.br" target="_blank" rel="noopener noreferrer" class="rounded-full border border-white/15 px-3 py-1 transition hover:border-white/35 hover:text-white/80">sistemavendadireta.com.br</a>
      <a href="https://codafacil.dev" target="_blank" rel="noopener noreferrer" class="rounded-full border border-white/15 px-3 py-1 transition hover:border-white/35 hover:text-white/80">codafacil.dev</a>
      <a href="https://wordpressconsultoria.com.br" target="_blank" rel="noopener noreferrer" class="rounded-full border border-white/15 px-3 py-1 transition hover:border-white/35 hover:text-white/80">wordpressconsultoria.com.br</a>
      <a href="https://fluxointeligenteia.com.br" target="_blank" rel="noopener noreferrer" class="rounded-full border border-white/15 px-3 py-1 transition hover:border-white/35 hover:text-white/80">fluxointeligenteia.com.br</a>
    </div>
    <p>© <?= date('Y') ?> CodeRush — Todos os direitos reservados.</p>
  </footer>

</body>
</html>
