<?php
declare(strict_types=1);

$siteBootstrap = __DIR__;
while (!is_file($siteBootstrap . '/_site_bootstrap.php') && dirname($siteBootstrap) !== $siteBootstrap) {
    $siteBootstrap = dirname($siteBootstrap);
}
if (is_file($siteBootstrap . '/_site_bootstrap.php')) {
    require_once $siteBootstrap . '/_site_bootstrap.php';
}
?>
<!doctype html>
<!--
/*
[Modulo Landing Page WordPress SVD]
@Author: André Gomes ( @acidcode )
@since 2026-02-10
Pagina de servicos WordPress/WooCommerce e Laravel com foco em conversao e qualificacao de leads.
*/
-->
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Serviços WordPress, WooCommerce e Laravel | Sistema Venda Direta</title>
  <meta name="description" content="Desenvolvimento WordPress, WooCommerce e sistemas sob medida com PHP/Laravel. Projetos com foco em performance, conversão, integrações e estabilidade." />
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
  <meta name="theme-color" content="#004AAD" />
  <meta name="author" content="Sistema Venda Direta" />
  <meta name="referrer" content="strict-origin-when-cross-origin" />
  <link rel="canonical" href="https://www.sistemavendadireta.com.br/wordpress/" />
  <link rel="alternate" hreflang="pt-BR" href="https://www.sistemavendadireta.com.br/wordpress/" />
  <link rel="alternate" hreflang="x-default" href="https://www.sistemavendadireta.com.br/wordpress/" />

  <meta property="og:locale" content="pt_BR" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="Serviços WordPress, WooCommerce e Laravel" />
  <meta property="og:description" content="Projetos web com foco em resultado: WordPress, WooCommerce, integrações e infraestrutura para crescimento consistente." />
  <meta property="og:url" content="https://www.sistemavendadireta.com.br/wordpress/" />
  <meta property="og:site_name" content="Sistema Venda Direta" />
  <meta property="og:image" content="https://www.sistemavendadireta.com.br/wp-content/uploads/2023/04/Screenshot-2023-04-26-at-14.38.02.png" />
  <meta property="og:image:alt" content="Serviços WordPress, WooCommerce e Laravel | Sistema Venda Direta" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Serviços WordPress, WooCommerce e Laravel | Sistema Venda Direta" />
  <meta name="twitter:description" content="Desenvolvimento WordPress, WooCommerce e sistemas sob medida com PHP/Laravel. Projetos com foco em performance, conversão, integrações e estabilidade." />
  <meta name="twitter:image" content="https://www.sistemavendadireta.com.br/wp-content/uploads/2023/04/Screenshot-2023-04-26-at-14.38.02.png" />
  <meta name="twitter:site" content="@sistemavendadireta" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Roboto:wght@300;400;500;700&display=swap" />

  <script data-cfasync="false" src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

  <style type="text/tailwindcss">@theme{--color-brand:#004AAD;--color-brand-dark:#003f91;--color-brand-soft:#215BA8;--font-heading:"Montserrat",sans-serif;--font-body:"Roboto",sans-serif;}</style>
  <style>@media (max-width:1024px){html{font-size:15px}}@media (max-width:640px){html{font-size:14px}body{overflow-x:hidden}img{max-width:100%;height:auto}}</style>

  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ProfessionalService",
      "name": "Sistema Venda Direta - Serviços WordPress",
      "url": "https://www.sistemavendadireta.com.br/wordpress/",
      "telephone": "+55 11 99456-6726",
      "email": "contato@sistemavendadireta.com.br",
      "areaServed": "BR",
      "sameAs": [
        "https://www.facebook.com/sistemavendadireta/",
        "https://www.youtube.com/@andregomes8954"
      ],
      "serviceType": [
        "Desenvolvimento WordPress",
        "WooCommerce",
        "Desenvolvimento Laravel",
        "Integrações e APIs"
      ]
    }
  </script>
</head>
<body class="bg-brand font-[var(--font-body)] text-white antialiased selection:bg-white selection:text-brand">
  <header class="border-b border-white/15 bg-brand-dark/30">
    <div class="mx-auto flex w-full max-w-[1140px] items-center justify-between px-4 py-4 sm:px-6">
      <a href="../" class="block" aria-label="Sistema Venda Direta">
        <img src="../index_svd_files/Logo-Branco-1.png" alt="Sistema Venda Direta" class="h-auto w-[165px] sm:w-[210px]" width="1000" height="300" />
      </a>
      <a href="https://wa.me/+5511994566726" target="_blank" rel="noopener noreferrer" class="hidden rounded-full border border-white/70 px-5 py-2 text-sm font-semibold uppercase tracking-wide hover:bg-white/10 sm:inline-flex">
        Falar no WhatsApp
      </a>
    </div>

    <nav class="mx-auto flex w-full max-w-[1140px] flex-wrap items-center gap-3 px-4 pb-4 text-sm text-white/90 sm:px-6" aria-label="Navegação da página de serviços">
      <a href="#servicos" class="rounded-full border border-white/30 px-3 py-1.5 hover:bg-white/10">Serviços</a>
      <a href="#processo" class="rounded-full border border-white/30 px-3 py-1.5 hover:bg-white/10">Processo</a>
      <a href="#cases" class="rounded-full border border-white/30 px-3 py-1.5 hover:bg-white/10">Cases</a>
      <a href="#planos" class="rounded-full border border-white/30 px-3 py-1.5 hover:bg-white/10">Planos</a>
      <a href="#faq" class="rounded-full border border-white/30 px-3 py-1.5 hover:bg-white/10">FAQ</a>
      <a href="#contato" class="rounded-full border border-white/30 px-3 py-1.5 hover:bg-white/10">Contato</a>
    </nav>
  </header>

  <main class="mx-auto w-full max-w-[1140px] px-4 py-8 sm:px-6 sm:py-10">
    <section class="rounded-3xl border border-white/20 bg-white/5 p-6 sm:p-10">
      <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/80">Serviços</p>
      <h1 class="mt-4 font-[var(--font-heading)] text-3xl font-bold leading-tight sm:text-5xl">Desenvolvimento WordPress, WooCommerce e Sistemas sob medida (PHP/Laravel)</h1>
      <p class="mt-4 max-w-3xl text-base leading-relaxed text-white/90 sm:text-lg">Criamos e evoluímos sites institucionais, e-commerces e plataformas com foco em performance, conversão e estabilidade. Integrações, automações com n8n e infraestrutura profissional quando necessário.</p>
      <div class="mt-6 flex flex-wrap gap-3">
        <a href="https://wa.me/+5511994566726" target="_blank" rel="noopener noreferrer" class="inline-flex rounded-full bg-white px-6 py-3 text-sm font-bold uppercase tracking-wide text-brand hover:bg-white/90">Falar no WhatsApp</a>
        <a href="#contato" class="inline-flex rounded-full border border-white/70 px-6 py-3 text-sm font-bold uppercase tracking-wide hover:bg-white/10">Solicitar orçamento</a>
      </div>
      <ul class="mt-6 grid gap-3 text-sm text-white/90 sm:grid-cols-2">
        <li>WordPress com SEO e velocidade</li>
        <li>WooCommerce com checkout e integrações</li>
        <li>APIs e automação para operação comercial</li>
        <li>Deploy com AWS, Docker e CI/CD</li>
      </ul>
    </section>

    <section class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4" aria-label="Confiança">
      <article class="rounded-2xl border border-white/20 bg-white/5 p-5 text-sm">+22 anos de experiência em desenvolvimento</article>
      <article class="rounded-2xl border border-white/20 bg-white/5 p-5 text-sm">Especialista em PHP/Laravel e WordPress/WooCommerce</article>
      <article class="rounded-2xl border border-white/20 bg-white/5 p-5 text-sm">Segurança, performance e manutenção orientadas por processo</article>
      <article class="rounded-2xl border border-white/20 bg-white/5 p-5 text-sm">Entrega com checklist técnico e validação antes do go-live</article>
    </section>

    <section id="servicos" class="mt-12">
      <h2 class="font-[var(--font-heading)] text-3xl font-bold">Serviços principais</h2>
      <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-[var(--font-heading)] text-xl font-semibold">Site institucional</h3>
          <p class="mt-2 text-sm text-white/85">Layout moderno, responsivo e rápido com secoes de sobre, serviços, cases, blog e contato.</p>
        </article>
        <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-[var(--font-heading)] text-xl font-semibold">WooCommerce</h3>
          <p class="mt-2 text-sm text-white/85">Catálogo, variações, frete, pagamento e melhorias de conversão no checkout e carrinho.</p>
        </article>
        <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-[var(--font-heading)] text-xl font-semibold">Integrações e APIs</h3>
          <p class="mt-2 text-sm text-white/85">Integração com AnyMarket, gateways, ERP, webhooks e rotinas de sincronização confiáveis.</p>
        </article>
        <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-[var(--font-heading)] text-xl font-semibold">Automação com n8n</h3>
          <p class="mt-2 text-sm text-white/85">Fluxos de cadastro, atualização, notificações e monitoramento para reduzir operação manual.</p>
        </article>
        <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-[var(--font-heading)] text-xl font-semibold">Infraestrutura e deploy</h3>
          <p class="mt-2 text-sm text-white/85">Ambientes dev/stage/prod, backup, logs e rotina de deploy com governança técnica.</p>
        </article>
        <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-[var(--font-heading)] text-xl font-semibold">Consultoria técnica</h3>
          <p class="mt-2 text-sm text-white/85">Arquitetura, performance, revisão de escopo e plano evolutivo para operações em crescimento.</p>
        </article>
      </div>
    </section>

    <section class="mt-12">
      <h2 class="font-[var(--font-heading)] text-3xl font-bold">Para quem e</h2>
      <div class="mt-6 grid gap-4 md:grid-cols-3">
        <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-[var(--font-heading)] text-lg font-semibold">Empresas com foco em presença digital</h3>
          <p class="mt-2 text-sm text-white/85">Sites institucionais com credibilidade, velocidade e estratégia de captação.</p>
        </article>
        <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-[var(--font-heading)] text-lg font-semibold">Negócios que vendem online</h3>
          <p class="mt-2 text-sm text-white/85">WooCommerce estável, integrações de pagamento e operação pronta para escala.</p>
        </article>
        <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-[var(--font-heading)] text-lg font-semibold">Operações com múltiplos sistemas</h3>
          <p class="mt-2 text-sm text-white/85">Conexão com ERP/marketplace e automações monitoradas de ponta a ponta.</p>
        </article>
      </div>
    </section>

    <section id="processo" class="mt-12">
      <h2 class="font-[var(--font-heading)] text-3xl font-bold">Como trabalhamos</h2>
      <ol class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <li class="rounded-2xl border border-white/20 bg-white/5 p-5"><strong>1. Diagnóstico</strong><p class="mt-2 text-sm text-white/85">Objetivo, público, requisitos e prioridades do projeto.</p></li>
        <li class="rounded-2xl border border-white/20 bg-white/5 p-5"><strong>2. Proposta</strong><p class="mt-2 text-sm text-white/85">Escopo, prazo, custo, premissas e riscos de implementação.</p></li>
        <li class="rounded-2xl border border-white/20 bg-white/5 p-5"><strong>3. Execução</strong><p class="mt-2 text-sm text-white/85">Desenvolvimento com checkpoints semanais e acompanhamento.</p></li>
        <li class="rounded-2xl border border-white/20 bg-white/5 p-5"><strong>4. Validação</strong><p class="mt-2 text-sm text-white/85">Teste funcional, performance, SEO básico e segurança.</p></li>
        <li class="rounded-2xl border border-white/20 bg-white/5 p-5"><strong>5. Go-live</strong><p class="mt-2 text-sm text-white/85">Publicação com checklist técnico e monitoramento inicial.</p></li>
        <li class="rounded-2xl border border-white/20 bg-white/5 p-5"><strong>6. Evolução</strong><p class="mt-2 text-sm text-white/85">Melhorias contínuas, manutenção e novos módulos.</p></li>
      </ol>
    </section>

    <section id="cases" class="mt-12">
      <h2 class="font-[var(--font-heading)] text-3xl font-bold">Cases e entregas recentes</h2>
      <div class="mt-6 grid gap-4 md:grid-cols-3">
        <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-[var(--font-heading)] text-lg font-semibold">Frete integrado em loja WooCommerce</h3>
          <p class="mt-2 text-sm text-white/85"><strong>Problema:</strong> calculo manual com erros.<br /><strong>Solução:</strong> integração com endpoint de frete.<br /><strong>Resultado:</strong> operação mais rápida e previsivel.</p>
        </article>
        <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-[var(--font-heading)] text-lg font-semibold">E-mail transacional com entregabilidade</h3>
          <p class="mt-2 text-sm text-white/85"><strong>Problema:</strong> e-mails indo para spam.<br /><strong>Solução:</strong> ajuste SMTP e remetente.<br /><strong>Resultado:</strong> melhora na comunicação com clientes.</p>
        </article>
        <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-[var(--font-heading)] text-lg font-semibold">Deploy em AWS com Docker</h3>
          <p class="mt-2 text-sm text-white/85"><strong>Problema:</strong> publicação instável.<br /><strong>Solução:</strong> pipeline CI/CD com ambientes separados.<br /><strong>Resultado:</strong> go-live controlado e seguro.</p>
        </article>
      </div>
    </section>

    <section id="planos" class="mt-12">
      <h2 class="font-[var(--font-heading)] text-3xl font-bold">Formas de contratação</h2>
      <div class="mt-6 grid gap-4 md:grid-cols-3">
        <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-[var(--font-heading)] text-lg font-semibold">Projeto fechado</h3>
          <p class="mt-2 text-sm text-white/85">Escopo definido para site institucional ou entrega pontual.</p>
        </article>
        <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-[var(--font-heading)] text-lg font-semibold">Mensalidade evolutiva</h3>
          <p class="mt-2 text-sm text-white/85">Pacote mensal para melhorias, correções e manutenção contínua.</p>
        </article>
        <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-[var(--font-heading)] text-lg font-semibold">Consultoria / Tech Lead</h3>
          <p class="mt-2 text-sm text-white/85">Revisão arquitetural, performance, integrações e governança técnica.</p>
        </article>
      </div>
    </section>

    <section id="faq" class="mt-12">
      <h2 class="font-[var(--font-heading)] text-3xl font-bold">FAQ</h2>
      <div class="mt-6 grid gap-4">
        <details class="rounded-2xl border border-white/20 bg-white/5 p-5"><summary class="cursor-pointer font-semibold">Você trabalha com Elementor e Gutenberg?</summary><p class="mt-2 text-sm text-white/85">Sim. Adaptamos o projeto ao stack mais adequado para o objetivo de negócio.</p></details>
        <details class="rounded-2xl border border-white/20 bg-white/5 p-5"><summary class="cursor-pointer font-semibold">Faz manutenção contínua?</summary><p class="mt-2 text-sm text-white/85">Sim. Oferecemos formato mensal para sustentação e evolução com previsibilidade.</p></details>
        <details class="rounded-2xl border border-white/20 bg-white/5 p-5"><summary class="cursor-pointer font-semibold">Da para integrar WooCommerce com ERP e marketplace?</summary><p class="mt-2 text-sm text-white/85">Sim. Mapeamos fluxo, dados e regras para integração robusta e monitorada.</p></details>
      </div>
    </section>

    <section id="contato" class="mt-12 rounded-3xl border border-white/20 bg-white/5 p-6 sm:p-8">
      <h2 class="font-[var(--font-heading)] text-3xl font-bold">Vamos tirar seu projeto do papel?</h2>
      <p class="mt-3 max-w-2xl text-sm leading-relaxed text-white/85">Me conte rapidamente o que você precisa e eu te retorno com caminho técnico, prazo e próximo passo recomendado.</p>
      <div class="mt-6 flex flex-wrap gap-3">
        <a href="https://wa.me/+5511994566726" target="_blank" rel="noopener noreferrer" class="inline-flex rounded-full bg-white px-6 py-3 text-sm font-bold uppercase tracking-wide text-brand hover:bg-white/90">WhatsApp</a>
        <!--email_off--><a href="mailto:contato@sistemavendadireta.com.br" class="inline-flex rounded-full border border-white/70 px-6 py-3 text-sm font-bold uppercase tracking-wide hover:bg-white/10">Solicitar orçamento</a><!--/email_off-->
      </div>
    </section>
  </main>

  <footer class="mt-10 border-t border-white/15 bg-brand-dark/40">
    <div class="mx-auto max-w-[1140px] px-4 py-10 sm:px-6">
      <div class="grid gap-8 md:grid-cols-3">
        <div class="space-y-3">
          <img src="../index_svd_files/Logo-Branco-1.png" alt="Sistema Venda Direta" class="h-auto w-[180px]" width="1000" height="300" loading="lazy" />
          <p class="max-w-sm text-sm leading-relaxed text-white/85">A Sistema Venda Direta desenvolve soluções para operação comercial, vendas diretas e evolução tecnológica com IA aplicada ao negócio.</p>
          <p class="text-sm text-white/90">Telefone: <a href="tel:+5511994566726" class="font-semibold hover:underline">11 99456-6726</a></p>
          <p class="text-sm text-white/90">Email: <!--email_off--><a href="mailto:contato@sistemavendadireta.com.br" class="font-semibold hover:underline">contato@sistemavendadireta.com.br</a><!--/email_off--></p>
        </div>

        <div class="space-y-3">
          <h4 class="font-[var(--font-heading)] text-lg font-semibold">Institucional</h4>
          <nav class="grid gap-2 text-sm text-white/90" aria-label="Menu institucional">
            <a href="../" class="hover:underline">Sistema Venda Direta</a>
            <a href="../wordpress/" class="hover:underline">WordPress</a>
            <a href="../codafacil/" class="hover:underline">Desenvolvimento com IA</a>
            <a href="../inteligencia-artificial/" class="hover:underline">Multinível com IA</a>
            <a href="../blog/" class="hover:underline">Blog</a>
          </nav>
        </div>

        <div class="space-y-4">
          <h4 class="font-[var(--font-heading)] text-lg font-semibold">25 anos de experiência desenvolvendo sistemas</h4>
          <a href="../#contato" class="inline-flex rounded-full border border-white/70 px-5 py-2.5 text-sm font-semibold uppercase tracking-wide hover:bg-white/10">Solicite um Orçamento</a>
          <div class="flex items-center gap-3">
            <a href="https://www.facebook.com/sistemavendadireta/" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[#3b5998] text-sm font-bold">f</a>
            <a href="https://www.youtube.com/@andregomes8954" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[#cd201f] text-sm font-bold">▶</a>
          </div>
        </div>
      </div>

      <div class="mt-8 border-t border-white/15 pt-4 text-xs text-white/70">© Sistema Venda Direta - Todos os direitos reservados.</div>
    </div>
  </footer>

  <a href="https://wa.me/+5511994566726" target="_blank" rel="noopener noreferrer" aria-label="Falar no WhatsApp" class="fixed bottom-3 right-3 z-[70] inline-flex items-center gap-2 rounded-full bg-[#25D366] px-4 py-3 text-sm font-bold text-white shadow-[0_10px_24px_rgba(0,0,0,0.35)] ring-2 ring-white/30 sm:bottom-4 sm:right-4 sm:h-14 sm:w-14 sm:justify-center sm:px-0">
    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/20 text-base leading-none">W</span>
    <span class="sm:hidden">WhatsApp</span>
  </a>
</body>
</html>
