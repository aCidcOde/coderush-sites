<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Arquitetura Laravel: Aumentando a DX | Codafacil.dev</title>
  <meta name="description" content="A arquitetura Laravel facilita a entrega de software sob medida por meio de uma estrutura organizada, suporte a testes automatizados e integração com ferramentas de IA, como GitHub Copilot. Isso resulta em um ciclo de desenvolvimento mais ágil e com maior qualidade." />
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
  <link rel="canonical" href="https://codafacil.dev/2026/06/09/arquitetura-laravel-aumentando-dx-software-sob-medida/" />
  <link rel="icon" type="image/svg+xml" href="../../../../favicon.svg" />
  <link rel="alternate icon" href="../../../../favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="../../../../apple-touch-icon.png" />
  <meta property="og:type" content="article" />
  <meta property="og:title" content="Arquitetura Laravel: Aumentando a DX | Codafacil.dev" />
  <meta property="og:description" content="A arquitetura Laravel facilita a entrega de software sob medida por meio de uma estrutura organizada, suporte a testes automatizados e integração com ferramentas de IA, como GitHub Copilot. Isso resulta em um ciclo de desenvolvimento mais ágil e com maior qualidade." />
  <meta property="og:url" content="https://codafacil.dev/2026/06/09/arquitetura-laravel-aumentando-dx-software-sob-medida/" />
  <meta property="og:image" content="https://codafacil.dev/imagens/posts/arquitetura-laravel-aumentando-dx-software-sob-medida.jpg" />
  <meta property="og:site_name" content="Codafacil.dev" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Arquitetura Laravel: Aumentando a DX | Codafacil.dev" />
  <meta name="twitter:description" content="A arquitetura Laravel facilita a entrega de software sob medida por meio de uma estrutura organizada, suporte a testes automatizados e integração com ferramentas de IA, como GitHub Copilot. Isso resulta em um ciclo de desenvolvimento mais ágil e com maior qualidade." />
  <meta name="twitter:image" content="https://codafacil.dev/imagens/posts/arquitetura-laravel-aumentando-dx-software-sob-medida.jpg" />
  <link rel="stylesheet" href="../../../../css/site-tailwind.css" />
  <link rel="stylesheet" href="../../../../css/styles.css" />
  <link rel="stylesheet" href="../../../../css/site-optimizations.css" />
  <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": "Arquitetura Laravel: Aumentando a DX em Software Sob Medida | Codafacil.dev",
  "description": "A arquitetura Laravel facilita a entrega de software sob medida por meio de uma estrutura organizada, suporte a testes automatizados e integração com ferramentas de IA, como GitHub Copilot. Isso resulta em um ciclo de desenvolvimento mais ágil e com maior qualidade.",
  "datePublished": "2026-06-09T09:00:00-03:00",
  "dateModified": "2026-06-09T09:00:00-03:00",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "https://codafacil.dev/2026/06/09/arquitetura-laravel-aumentando-dx-software-sob-medida/"
  },
  "image": [
    "https://codafacil.dev/imagens/posts/arquitetura-laravel-aumentando-dx-software-sob-medida.jpg"
  ],
  "author": {
    "@type": "Organization",
    "name": "Codafacil.dev"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Codafacil.dev"
  }
}
  </script>
  <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Quais são as vantagens do Laravel para sistemas sob medida?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "O Laravel oferece uma arquitetura robusta, suporte a testes automatizados e integração fácil com APIs, permitindo um desenvolvimento ágil e de alta qualidade."
      }
    },
    {
      "@type": "Question",
      "name": "Como a IA pode ser integrada ao desenvolvimento Laravel?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Ferramentas como GitHub Copilot podem auxiliar na geração de código, reduzindo o tempo de desenvolvimento e aumentando a produtividade dos desenvolvedores."
      }
    },
    {
      "@type": "Question",
      "name": "Quais práticas ajudam a garantir qualidade em software sob medida?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Implementar testes automatizados desde o início e ter um escopo claro são práticas fundamentais para garantir a qualidade do software durante o desenvolvimento."
      }
    }
  ]
}
  </script>
</head>
<body class="min-h-screen bg-[#04110d] text-white antialiased">
  <header class="border-b border-white/10 bg-[#04110d]/95 backdrop-blur">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
      <a href="../../../../" class="text-lg font-semibold tracking-tight text-white">Codafacil.dev</a>
      <nav class="hidden items-center gap-5 md:flex">
          <a href="../../../../" class="text-sm text-white/85 hover:text-white">Site principal</a>
          <a href="../../../../#servicos" class="text-sm text-white/85 hover:text-white">Serviços</a>
          <a href="../../../../blog/" class="text-sm text-white/85 hover:text-white">Blog</a>
          <a href="../../../../#contato" class="text-sm text-white/85 hover:text-white">Contato</a>
      </nav>
    </div>
  </header>

  <main class="mx-auto max-w-4xl px-4 py-8 sm:px-6 sm:py-10">
    <a href="../../../../" class="inline-flex rounded-full border border-white/40 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white/85 hover:bg-white/10">
      Voltar para o site principal
    </a>

    <article class="mt-5 overflow-hidden rounded-3xl border border-white/15 bg-white/5">
      <figure class="relative">
        <img src="../../../../imagens/posts/arquitetura-laravel-aumentando-dx-software-sob-medida.jpg" alt="Blueprint aéreo de um sistema modular com hexágonos interconectados em verde-petróleo, azul e violeta, destacando um núcleo otimizado" class="block w-full object-cover" style="aspect-ratio:1200/630" width="1200" height="630" loading="eager" decoding="async" />
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/85 via-black/45 to-transparent" aria-hidden="true"></div>
        <figcaption class="absolute inset-x-0 bottom-0 p-5 sm:p-8 lg:p-10">
          <span class="block h-1 w-12 rounded-full bg-sky-300" aria-hidden="true"></span>
          <p class="mt-3 text-[11px] font-semibold uppercase tracking-[0.22em] text-white/85 sm:text-xs">Guia prático • 09/06/2026</p>
          <h1 class="mt-2 text-2xl font-semibold leading-tight text-white sm:text-3xl lg:text-4xl">Arquitetura Laravel: Aumentando a DX em Software Sob Medida</h1>
          <p class="mt-3 max-w-3xl text-sm leading-6 text-white/90 sm:text-base sm:leading-7">Entenda como a arquitetura Laravel pode otimizar o desenvolvimento de software sob medida com foco em experiência do desenvolvedor.</p>
        </figcaption>
      </figure>
      <div class="p-5 sm:p-8">
      <aside class="mt-6 rounded-2xl border-l-4 border-sky-300 bg-white/[0.04] p-5 sm:p-6">
        <h3 class="text-sm font-semibold uppercase tracking-[0.18em] text-sky-300">Como a arquitetura Laravel pode melhorar a entrega de software sob medida?</h3>
        <p class="mt-2 text-base leading-7 text-white/90 sm:text-lg">A arquitetura Laravel facilita a entrega de software sob medida por meio de uma estrutura organizada, suporte a testes automatizados e integração com ferramentas de IA, como GitHub Copilot. Isso resulta em um ciclo de desenvolvimento mais ágil e com maior qualidade.</p>
      </aside>
      <section class="mt-6">
        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-white/55">Em resumo</p>
        <ul class="mt-3 grid gap-3 sm:grid-cols-3">
        <li class="rounded-xl border border-white/10 bg-white/5 p-4 text-sm leading-6 text-white/90">Laravel otimiza a entrega de software sob medida</li>
        <li class="rounded-xl border border-white/10 bg-white/5 p-4 text-sm leading-6 text-white/90">Integração de IA melhora a experiência do desenvolvedor</li>
        <li class="rounded-xl border border-white/10 bg-white/5 p-4 text-sm leading-6 text-white/90">Testes automatizados garantem qualidade desde o início</li>
        </ul>
      </section>
      <section class="mt-8">
        <h2 class="text-xl font-semibold text-white sm:text-2xl">Por que escolher Laravel?</h2>
        <p class="mt-3 text-sm leading-7 text-white/85 sm:text-base">O Laravel se destaca pela sua simplicidade e robustez, oferecendo uma arquitetura que favorece o desenvolvimento ágil. Com suas funcionalidades nativas, como rotas, middleware e Eloquent ORM, é possível construir sistemas web sob medida de forma eficiente. Além disso, a comunidade ativa garante atualizações constantes e suporte, essencial para manter o software alinhado às novas demandas do mercado.</p>
      </section>
      <section class="mt-8">
        <h2 class="text-xl font-semibold text-white sm:text-2xl">Benefícios do Laravel para a DX</h2>
        <ul class="mt-4 space-y-2 text-sm leading-7 text-white/85 sm:text-base">
        <li class="flex gap-3">
          <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-sky-300" aria-hidden="true"></span>
          <span>Estrutura clara que facilita manutenção e escalabilidade</span>
        </li>
        <li class="flex gap-3">
          <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-sky-300" aria-hidden="true"></span>
          <span>Rápida aplicação de mudanças com rotas dinâmicas</span>
        </li>
        <li class="flex gap-3">
          <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-sky-300" aria-hidden="true"></span>
          <span>Integração nativa com testes automatizados</span>
        </li>
        <li class="flex gap-3">
          <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-sky-300" aria-hidden="true"></span>
          <span>Suporte a APIs para extensibilidade</span>
        </li>
        <li class="flex gap-3">
          <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-sky-300" aria-hidden="true"></span>
          <span>Comunidade ativa e rica em recursos</span>
        </li>
        </ul>
      </section>
      <aside class="my-8 rounded-2xl border-l-4 border-sky-300 bg-sky-300/10 p-5 sm:p-6">
        <p class="text-sm leading-7 text-white/90 sm:text-base">Já estruturamos isso em diversos projetos com Laravel. Vale uma conversa para ver como podemos ajudar no seu cenário específico. <a href="../../../../#contato" class="font-semibold text-sky-300 underline decoration-sky-300/40 underline-offset-4 hover:text-white">Fale com a Codafacil.dev →</a></p>
      </aside>
      <section class="mt-8">
        <h2 class="text-xl font-semibold text-white sm:text-2xl">Integração com IA no fluxo de desenvolvimento</h2>
        <p class="mt-3 text-sm leading-7 text-white/85 sm:text-base">Integrar IA, como o GitHub Copilot, pode acelerar a codificação e aumentar a qualidade do código gerado. Essa parceria entre humanos e máquinas permite que os desenvolvedores se concentrem em aspectos mais críticos do projeto, enquanto a IA cuida das tarefas repetitivas. A aplicação de IA no desenvolvimento Laravel não é apenas uma tendência, mas uma necessidade para garantir eficiência e inovação, conforme discutido em [Commit House](https://www.commithouse.com.br/servicos/desenvolvimento) e [Duílio Fanton](https://duiliofanton.com.br/).</p>
      </section>
      <p class="my-6 text-sm leading-7 text-white/85 sm:text-base">
        Vamos conversar sobre como podemos otimizar seu processo de desenvolvimento. <a href="../../../../#contato" class="font-semibold text-sky-300 underline decoration-sky-300/40 underline-offset-4 hover:text-white">Fale com a Codafacil.dev →</a>
      </p>
      <section class="mt-8">
        <h2 class="text-xl font-semibold text-white sm:text-2xl">Evite armadilhas comuns</h2>
        <p class="mt-3 text-sm leading-7 text-white/85 sm:text-base">É importante ter clareza no escopo do projeto para evitar mudanças excessivas que podem atrasar a entrega. Além disso, não subestime a importância de testes automatizados; começar a aplicá-los desde o primeiro sprint pode evitar retrabalho e garantir a qualidade do software. Fique atento também à documentação, que deve ser mantida em dia para facilitar a manutenção futura do sistema.</p>
      </section>
      <section class="mt-10">
        <h2 class="text-xl font-semibold text-white sm:text-2xl">Perguntas frequentes</h2>
        <details class="mt-3 rounded-xl border border-white/10 bg-white/5 p-4">
          <summary class="cursor-pointer text-base font-semibold text-white">Quais são as vantagens do Laravel para sistemas sob medida?</summary>
          <p class="mt-2 text-sm leading-6 text-white/85">O Laravel oferece uma arquitetura robusta, suporte a testes automatizados e integração fácil com APIs, permitindo um desenvolvimento ágil e de alta qualidade.</p>
        </details>
        <details class="mt-3 rounded-xl border border-white/10 bg-white/5 p-4">
          <summary class="cursor-pointer text-base font-semibold text-white">Como a IA pode ser integrada ao desenvolvimento Laravel?</summary>
          <p class="mt-2 text-sm leading-6 text-white/85">Ferramentas como GitHub Copilot podem auxiliar na geração de código, reduzindo o tempo de desenvolvimento e aumentando a produtividade dos desenvolvedores.</p>
        </details>
        <details class="mt-3 rounded-xl border border-white/10 bg-white/5 p-4">
          <summary class="cursor-pointer text-base font-semibold text-white">Quais práticas ajudam a garantir qualidade em software sob medida?</summary>
          <p class="mt-2 text-sm leading-6 text-white/85">Implementar testes automatizados desde o início e ter um escopo claro são práticas fundamentais para garantir a qualidade do software durante o desenvolvimento.</p>
        </details>
      </section>
      </div>
    </article>

    <section class="mt-8 rounded-2xl border border-white/15 bg-white/5 p-5">
      <h2 class="text-xl font-semibold text-white">Precisa tirar um produto ou integração do papel?</h2>
      <p class="mt-3 text-sm leading-7 text-white/85 sm:text-base">A Codafacil.dev combina engenharia e IA aplicada para acelerar a entrega sem perder governança técnica.</p>
      <a href="../../../../#contato" class="mt-4 inline-flex rounded-full border border-white/40 px-5 py-2.5 text-sm font-semibold uppercase tracking-[0.2em] text-white hover:bg-white/10">
        Fale com a Codafacil.dev
      </a>
    </section>

    <section class="mt-6 flex items-center justify-center">
      <a href="../../../../blog/" class="inline-flex rounded-full border border-white/40 px-5 py-2.5 text-sm font-semibold uppercase tracking-[0.2em] text-white hover:bg-white/10">
        Veja mais no blog
      </a>
    </section>

    <!-- BLOG-LEIA-TAMBEM START -->
<section class="mt-8 rounded-2xl border border-white/15 bg-white/5 p-5">
  <div class="flex items-end justify-between gap-4">
    <h2 class="text-2xl font-semibold text-white">Leia também sobre PHP e Laravel</h2>
    <a href="../../../../blog/" class="text-sm font-semibold text-white/85 hover:text-white">Ver todos os artigos da Codafacil.dev</a>
  </div>
  <div class="mt-5 grid gap-4 md:grid-cols-3">
<article class="overflow-hidden rounded-2xl border border-white/15 bg-white/5">
  <a href="../../../../2026/07/30/integrar-testes-automatizados-desenvolvimento-software-sob-medida/"><img src="../../../../imagens/posts/integrar-testes-automatizados-desenvolvimento-software-sob-medida.jpg" alt="Como integrar testes automatizados no desenvolvimento de software sob medida" class="h-44 w-full object-cover" width="1200" height="630" loading="lazy" /></a>
  <div class="p-4">
    <h3 class="text-base font-semibold leading-snug"><a href="../../../../2026/07/30/integrar-testes-automatizados-desenvolvimento-software-sob-medida/" class="hover:underline">Como integrar testes automatizados no desenvolvimento de software sob medida</a></h3>
    <p class="mt-2 text-sm leading-relaxed text-white/80">Entenda a importância dos testes automatizados na entrega de software com qualidade e agilidade.</p>
  </div>
</article>
<article class="overflow-hidden rounded-2xl border border-white/15 bg-white/5">
  <a href="../../../../2026/07/03/ia-pode-otimizar-ciclo-desenvolvimento-software/"><img src="../../../../imagens/posts/ia-pode-otimizar-ciclo-desenvolvimento-software.jpg" alt="Como a IA pode otimizar o ciclo de desenvolvimento de software" class="h-44 w-full object-cover" width="1200" height="630" loading="lazy" /></a>
  <div class="p-4">
    <h3 class="text-base font-semibold leading-snug"><a href="../../../../2026/07/03/ia-pode-otimizar-ciclo-desenvolvimento-software/" class="hover:underline">Como a IA pode otimizar o ciclo de desenvolvimento de software</a></h3>
    <p class="mt-2 text-sm leading-relaxed text-white/80">Descubra como utilizar ferramentas de IA para acelerar a entrega de software sob medida sem comprometer a qualidade.</p>
  </div>
</article>
<article class="overflow-hidden rounded-2xl border border-white/15 bg-white/5">
  <a href="../../../../2026/06/21/implementar-testes-automatizados-desenvolvimento-software-sob-medida/"><img src="../../../../imagens/posts/implementar-testes-automatizados-desenvolvimento-software-sob-medida.jpg" alt="Como implementar testes automatizados no desenvolvimento de software sob medida" class="h-44 w-full object-cover" width="1200" height="630" loading="lazy" /></a>
  <div class="p-4">
    <h3 class="text-base font-semibold leading-snug"><a href="../../../../2026/06/21/implementar-testes-automatizados-desenvolvimento-software-sob-medida/" class="hover:underline">Como implementar testes automatizados no desenvolvimento de software sob medida</a></h3>
    <p class="mt-2 text-sm leading-relaxed text-white/80">Entenda como integrar testes automatizados no ciclo de entrega de software sob medida com IA.</p>
  </div>
</article>
  </div>
</section>
<!-- BLOG-LEIA-TAMBEM END -->
    <!-- BLOG-CROSS-SITE START -->
<section class="mt-8 rounded-2xl border border-white/15 bg-white/5 p-5">
  <div class="flex items-end justify-between gap-4">
    <h2 class="text-2xl font-semibold text-white">Mais do hub CodeRush</h2>
    <a href="https://coderush.com.br/" rel="noopener" target="_blank" class="text-sm font-semibold text-white/85 hover:text-white">Conhecer a CodeRush</a>
  </div>
  <p class="mt-2 text-sm text-white/70">Conteúdo recente dos outros sites do ecossistema.</p>
  <div class="mt-5 grid gap-4 md:grid-cols-3">
<article class="overflow-hidden rounded-2xl border border-white/15 bg-white/5">
  <a href="https://coderush.com.br/2026/06/18/comprar-construir-software-considerar-arquitetura/" rel="noopener" target="_blank"><img src="https://coderush.com.br/imagens/posts/comprar-construir-software-considerar-arquitetura.jpg" alt="Comprar ou construir software: o que considerar em arquitetura" class="h-44 w-full object-cover" width="1200" height="630" loading="lazy" /></a>
  <div class="p-4">
    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/55">CodeRush</p>
    <h3 class="mt-1 text-base font-semibold leading-snug"><a href="https://coderush.com.br/2026/06/18/comprar-construir-software-considerar-arquitetura/" rel="noopener" target="_blank" class="hover:underline">Comprar ou construir software: o que considerar em arquitetura</a></h3>
    <p class="mt-2 text-sm leading-relaxed text-white/80">Entenda os principais fatores na decisão de comprar ou construir software sob medida.</p>
  </div>
</article>
<article class="overflow-hidden rounded-2xl border border-white/15 bg-white/5">
  <a href="https://sistemavendadireta.com.br/2026/08/15/integrar-sistema-venda-direta-erp/" rel="noopener" target="_blank"><img src="https://sistemavendadireta.com.br/imagens/posts/integrar-sistema-venda-direta-erp.jpg" alt="Como integrar seu sistema de venda direta com ERP" class="h-44 w-full object-cover" width="1200" height="630" loading="lazy" /></a>
  <div class="p-4">
    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/55">Sistema Venda Direta</p>
    <h3 class="mt-1 text-base font-semibold leading-snug"><a href="https://sistemavendadireta.com.br/2026/08/15/integrar-sistema-venda-direta-erp/" rel="noopener" target="_blank" class="hover:underline">Como integrar seu sistema de venda direta com ERP</a></h3>
    <p class="mt-2 text-sm leading-relaxed text-white/80">Entenda como a integração de sistemas pode otimizar seu MMN.</p>
  </div>
</article>
<article class="overflow-hidden rounded-2xl border border-white/15 bg-white/5">
  <a href="https://bfrintelligence.com.br/2026/08/05/bfr-intelligence-microsoft-startups-aws-activate/" rel="noopener" target="_blank"><img src="https://bfrintelligence.com.br/imagens/posts/bfr-intelligence-microsoft-startups-aws-activate.jpg" alt="BFR Intelligence nos programas de startup da Microsoft e da AWS" class="h-44 w-full object-cover" width="1200" height="630" loading="lazy" /></a>
  <div class="p-4">
    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/55">BFR Intelligence</p>
    <h3 class="mt-1 text-base font-semibold leading-snug"><a href="https://bfrintelligence.com.br/2026/08/05/bfr-intelligence-microsoft-startups-aws-activate/" rel="noopener" target="_blank" class="hover:underline">BFR Intelligence nos programas de startup da Microsoft e da AWS</a></h3>
    <p class="mt-2 text-sm leading-relaxed text-white/80">Agora com Azure OpenAI e AWS Bedrock disponíveis: mais opções de modelo por caso de uso, sem lock-in e com o dado sob governança de nuvem...</p>
  </div>
</article>
  </div>
</section>
<!-- BLOG-CROSS-SITE END -->
  </main>

  <footer class="border-t border-white/10 bg-[#04110d]/80">
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
      <div class="grid gap-8 md:grid-cols-3">
        <div>
          <h2 class="text-xl font-semibold text-white">Codafacil.dev</h2>
          <p class="mt-3 max-w-sm text-sm leading-7 text-white/80">A Codafacil.dev acelera software sob medida com IA sem abrir mão de engenharia, testes e clareza de escopo.</p>
          <p class="mt-3 text-sm text-white/80">Telefone: <a href="tel:+5511994566726" class="font-semibold hover:underline">11 99456-6726</a></p>

        </div>
        <div>
          <h3 class="text-lg font-semibold text-white">Navegacao</h3>
          <nav class="mt-4 grid gap-2 text-sm text-white/85" aria-label="Mapa do site">
            <a href="../../../../" class="hover:underline">Site principal</a>
            <a href="../../../../#servicos" class="hover:underline">Serviços</a>
            <a href="../../../../blog/" class="hover:underline">Blog</a>
            <a href="../../../../#contato" class="hover:underline">Contato</a>
          </nav>
        </div>
        <div>
          <h3 class="text-lg font-semibold text-white">Próximo passo</h3>
          <p class="mt-3 text-sm leading-7 text-white/80">Resposta humana, sem fila generica. Fale com o time comercial do site.</p>
          <a href="../../../../#contato" class="mt-4 inline-flex rounded-full border border-white/40 px-5 py-2.5 text-sm font-semibold uppercase tracking-[0.2em] text-white hover:bg-white/10">
            Fale com a Codafacil.dev
          </a>
        </div>
      </div>
      <div class="mt-8 border-t border-white/10 pt-4 text-xs text-white/45">© Codafacil.dev - Todos os direitos reservados.</div>
    </div>
  </footer>
</body>
</html>
