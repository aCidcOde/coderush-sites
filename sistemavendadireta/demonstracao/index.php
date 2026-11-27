<?php
/*
[Modulo Demonstracao SVD — porta de entrada do ambiente de demonstracao]
@Author: André Gomes ( @acidcode )
@since 2026-11-27

POR QUE EXISTE: "soh ve apresentacao quem deixar um contato pelo menos" (usuario,
27/11/2026). Ate aqui os botoes do site apontavam direto pro painel; o cadastro
interceptava o clique comum, mas clique do meio, "abrir em nova aba" e link
copiado iam pro painel sem passar por ele. Agora todo link de demonstracao do
site (DEMO_URL / demoLink() em inc/promo.php, posts do blog, blog-bot) aponta
pra ca, e o endereco real so sai do cadastro (inc/zap-gate.php):

  - sem contato: a caixa de cadastro abre na chegada; enviado, o painel abre
    nesta mesma aba;
  - ja identificado (cadastro ou formulario antes): a caixa aparece do mesmo
    jeito, preenchida, e abre com um clique — sem atalho (usuario, 27/11);
  - fechou a caixa: fica a pagina com o que tem na demonstracao e o botao.

noindex: e uma porta, nao um conteudo — fora do sitemap e do Google.
?de= diz de qual botao a pessoa veio e entra na origem do lead.
*/

$de = preg_replace('/[^a-z0-9-]/', '', strtolower((string) ($_GET['de'] ?? '')));
$de = substr($de, 0, 60);
$zapOrigem = 'demonstracao' . ($de !== '' ? '-' . $de : '');

$seoUrl = 'https://sistemavendadireta.com.br/demonstracao/';
$seoTitle = 'Demonstração do Sistema Venda Direta';
$seoDescription = 'Loja, escritório do consultor e painel administrativo de uma operação real. Deixe nome e WhatsApp e a demonstração abre na hora.';
$cssVersion = (string) @filemtime(__DIR__ . '/../css/site-tailwind.css');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="robots" content="noindex, follow" />
  <meta name="referrer" content="strict-origin-when-cross-origin" />
  <link rel="canonical" href="<?= htmlspecialchars($seoUrl, ENT_QUOTES, 'UTF-8') ?>" />
  <link rel="icon" type="image/svg+xml" href="../favicon.svg" />
  <link rel="alternate icon" href="../favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&amp;family=Roboto:wght@300;400;500;700&amp;display=swap" />
  <link rel="stylesheet" href="../css/site-tailwind.css?v=<?= htmlspecialchars($cssVersion, ENT_QUOTES, 'UTF-8') ?>" />
  <link rel="stylesheet" href="../css/site-optimizations.css?v=<?= htmlspecialchars($cssVersion, ENT_QUOTES, 'UTF-8') ?>" />
  <link rel="stylesheet" href="../css/styles.css?v=<?= htmlspecialchars($cssVersion, ENT_QUOTES, 'UTF-8') ?>" />
  <?php include __DIR__ . '/../inc/analytics.php'; ?>
</head>
<body class="bg-brand text-white antialiased font-[var(--font-body)] site-optimized">
  <header class="border-b border-white/10 bg-brand/95">
    <div class="mx-auto max-w-site px-4 sm:px-6">
      <nav class="flex items-center justify-between gap-4 py-3" aria-label="Menu principal">
        <a href="../" class="block" aria-label="Sistema Venda Direta">
          <img decoding="async" src="../imagens/Logo-Branco-1.webp" alt="Sistema Venda Direta"
            class="h-auto w-[165px] sm:w-[210px] lg:w-[260px]" width="1000" height="300" />
        </a>
        <a href="../" class="hidden rounded-full border border-white/70 px-4 py-2 text-sm font-medium transition hover:bg-white/10 md:inline-flex">
          Voltar para o site
        </a>
      </nav>
    </div>
  </header>

  <main id="conteudo" class="mx-auto max-w-site px-4 py-10 sm:px-6">
    <div class="rounded-[24px] border border-amber-300/40 bg-amber-400/10 p-6 sm:p-8">
      <p class="text-sm font-semibold uppercase tracking-wide text-amber-200">Ambiente de demonstração</p>
      <h1 class="mt-2 font-[var(--font-heading)] text-3xl font-bold sm:text-4xl">Veja o Sistema Venda Direta funcionando</h1>
      <p class="mt-3 max-w-3xl text-base leading-relaxed text-white/90">
        Loja, escritório do consultor e painel administrativo, com dados de uma operação real de 12 meses.
        Deixe nome e WhatsApp e a demonstração abre na hora.
      </p>
      <ul class="mt-5 grid gap-3 text-sm leading-relaxed text-white/85 sm:grid-cols-3">
        <li class="rounded-2xl border border-white/20 bg-white/5 p-4"><strong class="text-white">Loja virtual</strong><br />O cliente final compra e a venda cai na conta de quem indicou.</li>
        <li class="rounded-2xl border border-white/20 bg-white/5 p-4"><strong class="text-white">Escritório do consultor</strong><br />Rede, pedidos, comissões e o cadastro de indicados.</li>
        <li class="rounded-2xl border border-white/20 bg-white/5 p-4"><strong class="text-white">Painel administrativo</strong><br />Financeiro, relatórios e a configuração do plano.</li>
      </ul>
      <div class="mt-6 flex flex-wrap gap-3">
        <a href="/demonstracao/" class="inline-flex items-center justify-center rounded-full bg-amber-400 px-6 py-3.5 text-sm font-bold uppercase tracking-wide text-brand hover:bg-amber-300">
          Abrir a demonstração
        </a>
        <a href="https://wa.me/5511994566726?text=<?= rawurlencode('Olá! Quero ver a demonstração do Sistema Venda Direta com o meu plano.') ?>" target="_blank" rel="noopener"
          class="inline-flex items-center justify-center rounded-full border border-white/50 px-6 py-3.5 text-sm font-bold uppercase tracking-wide hover:bg-white/10">
          Ver com o meu plano
        </a>
      </div>
      <noscript><p class="mt-4 text-sm text-white/80">Para abrir a demonstração, ative o JavaScript ou fale com a gente pelo WhatsApp (11) 99456-6726.</p></noscript>
    </div>
  </main>

  <?php include __DIR__ . '/../inc/zap-tracking.php'; ?>
</body>
</html>
