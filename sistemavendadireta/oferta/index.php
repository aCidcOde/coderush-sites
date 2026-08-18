<?php
declare(strict_types=1);

/*
[Modulo Landing de Oferta SVD — trafego pago]
@Author: André Gomes ( @acidcode )
@since 2026-08-02
Landing dedicada a campanha patrocinada: instalacao promocional (2x ou a vista) + implantacao assistida por IA.
Nao e linkada pelo index (trafego pago). noindex por ser pagina de campanha com prazo.

Parametros da campanha ficam no bloco de configuracao abaixo — alterar so aqui.
*/

require_once __DIR__ . '/../inc/promo.php';   // vagas fechadas + links das lojas

$mailStatus = $_GET['mail'] ?? '';
$mailStatus = in_array($mailStatus, ['ok', 'erro'], true) ? $mailStatus : '';

// ----------------------------------------------------------------------------
// Configuracao da campanha
// ----------------------------------------------------------------------------
$promoInstallFrom = 5000;          // valor cheio da instalacao (R$)
$promoInstallTo = 3500;            // valor promocional em 2x (R$)
$promoInstallCash = 3000;          // valor promocional a vista (R$)
$promoDeadline = '2026-08-31';     // ultimo dia da promocao (America/Sao_Paulo)
$promoSlots = 10;                  // vagas de implantacao no periodo (tema: 10 anos)
$promoSlotsFilled = 4;             // ja fechadas: New Professional's, Protech, MedPlant e Zohr
$promoSlotsLeft = max(0, $promoSlots - $promoSlotsFilled);
$whatsappPhone = '5511994566726';
$whatsappMessage = 'Ola! Vim pela Promocao 10 Anos do Sistema Venda Direta. Quero garantir minha vaga.';

$monthlyTiers = [
    ['revenue' => 'até R$ 50 mil', 'price' => 'R$ 500'],
    ['revenue' => 'até R$ 100 mil', 'price' => 'R$ 1.000'],
    ['revenue' => 'até R$ 200 mil', 'price' => 'R$ 1.500'],
    ['revenue' => 'até R$ 350 mil', 'price' => 'R$ 3.000'],
    ['revenue' => 'até R$ 500 mil', 'price' => 'R$ 4.500'],
    ['revenue' => 'até R$ 750 mil', 'price' => 'R$ 7.000'],
    ['revenue' => 'até R$ 1 milhão', 'price' => 'R$ 9.000'],
];

$tz = new DateTimeZone('America/Sao_Paulo');
$deadline = new DateTimeImmutable($promoDeadline . ' 23:59:59', $tz);
$today = new DateTimeImmutable('now', $tz);
$daysLeft = (int) $today->diff($deadline)->format('%r%a');
$promoActive = $daysLeft >= 0;
$deadlineLabel = $deadline->format('d/m/Y');

$moneyFrom = 'R$ ' . number_format($promoInstallFrom, 0, ',', '.');
$moneyTo = 'R$ ' . number_format($promoInstallTo, 0, ',', '.');
$moneyCash = 'R$ ' . number_format($promoInstallCash, 0, ',', '.');
$discountCashPct = (int) round((1 - $promoInstallCash / $promoInstallFrom) * 100);

$whatsappHref = 'https://wa.me/' . $whatsappPhone . '?text=' . rawurlencode($whatsappMessage);

$seoBase = 'https://www.sistemavendadireta.com.br';
$seoUrl = $seoBase . '/oferta/';
// SEO/Ads: o titulo precisa nomear o PRODUTO que a pessoa buscou, nao so a oferta.
// O Indice de Qualidade do Google marcou a experiencia desta pagina como "abaixo da
// media" (nota 5/10) — a palavra "multinivel" nao aparecia nenhuma vez, embora a
// campanha compre exatamente "sistema multinivel" e "sistema marketing multinivel".
$seoTitle = 'Sistema de Marketing Multinível e Venda Direta | Promoção 10 Anos';
$seoDescription = 'Sistema de marketing multinível (MMN) e venda direta completo: escritório '
    . 'do consultor, rede binária e unilevel, loja e financeiro. Instalação a partir de R$ 3.000.';
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="robots" content="noindex, follow" />
  <meta name="theme-color" content="#004AAD" />
  <meta name="author" content="Sistema Venda Direta" />
  <meta name="referrer" content="strict-origin-when-cross-origin" />
  <link rel="icon" type="image/svg+xml" href="../favicon.svg" />
  <link rel="alternate icon" href="../favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png" />

  <meta property="og:locale" content="pt_BR" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="Promoção 10 Anos — Sistema Venda Direta" />
  <meta property="og:description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:url" content="<?= htmlspecialchars($seoUrl, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:site_name" content="Sistema Venda Direta" />
  <meta property="og:image" content="<?= htmlspecialchars($seoBase . '/imagens/og-oferta.jpg?v=3', ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />
  <meta property="og:image:alt" content="Instalação do Sistema Venda Direta com até 40% OFF — R$ 3.500 em 2x ou R$ 3.000 à vista até 31/08" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:image" content="<?= htmlspecialchars($seoBase . '/imagens/og-oferta.jpg?v=3', ENT_QUOTES, 'UTF-8') ?>" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&amp;family=Roboto:wght@300;400;500;700&amp;display=swap" />
  <?php $cssVersion = (string) @filemtime(__DIR__ . '/../css/site-tailwind.css'); ?>
  <link rel="stylesheet" href="../css/site-tailwind.css?v=<?= htmlspecialchars($cssVersion, ENT_QUOTES, 'UTF-8') ?>" />
  <link rel="stylesheet" href="../css/site-optimizations.css?v=<?= htmlspecialchars($cssVersion, ENT_QUOTES, 'UTF-8') ?>" />
  <link rel="stylesheet" href="../css/styles.css?v=<?= htmlspecialchars($cssVersion, ENT_QUOTES, 'UTF-8') ?>" />
  <?php include __DIR__ . '/../inc/analytics.php'; ?>
</head>
<body class="bg-brand text-white antialiased font-[var(--font-body)] site-optimized">

  <?php if ($promoActive): ?>
    <div class="sticky top-0 z-50 border-b border-amber-300/40 bg-amber-400 text-brand">
      <div class="mx-auto flex max-w-[1140px] flex-nowrap items-center justify-between gap-3 px-4 py-2 sm:px-6">
        <p class="min-w-0 text-xs font-bold uppercase leading-tight tracking-wide sm:text-sm">
          Promoção 10 Anos: até <?= (int) $discountCashPct ?>% OFF<span class="hidden md:inline"> na instalação · encerra <?= htmlspecialchars($deadlineLabel, ENT_QUOTES, 'UTF-8') ?></span>
          <span class="ml-1 whitespace-nowrap rounded-full bg-brand px-2 py-0.5 text-[10px] font-bold text-white sm:text-xs"><?= (int) $daysLeft ?> dia<?= $daysLeft === 1 ? '' : 's' ?></span>
        </p>
        <a href="#garantir" class="shrink-0 whitespace-nowrap rounded-full bg-brand px-3 py-1.5 text-[11px] font-bold uppercase tracking-wide text-white hover:bg-brand-dark sm:px-4 sm:text-xs">
          Garantir<span class="hidden sm:inline"> desconto</span>
        </a>
      </div>
    </div>
  <?php endif; ?>

  <header class="border-b border-white/10 bg-brand/95">
    <div class="mx-auto flex max-w-[1140px] items-center justify-between gap-4 px-4 py-3 sm:px-6">
      <a href="../" target="_blank" rel="noopener" aria-label="Abrir o site do Sistema Venda Direta em nova aba"><img decoding="async" src="../imagens/Logo-Branco-1.webp" alt="Sistema Venda Direta" class="h-auto w-[150px] sm:w-[200px]" width="1000" height="300" loading="eager" /></a>
      <div class="flex items-center gap-5">
        <a href="https://painel.sistemavendadireta.com.br/primeiros-passos?utm_source=site&utm_medium=oferta&utm_campaign=demo" target="_blank" rel="noopener" class="text-sm font-semibold text-amber-300 hover:text-amber-200">Ver demonstração</a>
        <a href="../cases/" class="text-sm font-semibold text-white/85 hover:text-white">Cases</a>
        <a href="<?= htmlspecialchars($whatsappHref, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="hidden rounded-full bg-[#25D366] px-4 py-2 text-sm font-bold sm:inline-flex">
          Falar agora
        </a>
      </div>
    </div>
  </header>

  <main class="mx-auto max-w-[1140px] px-4 sm:px-6">

    <section class="py-10 lg:py-14">
      <div>
        <p class="inline-flex rounded-full border border-amber-300/50 bg-amber-400/15 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-amber-200">
          Celebrando 10 anos
        </p>
        <h1 class="mt-4 font-[var(--font-heading)] text-3xl font-bold leading-[1.1] sm:text-4xl lg:text-[46px]">
          Sistema de marketing multinível e venda direta no ar com <span class="text-amber-300">até <?= (int) $discountCashPct ?>% de desconto</span> na instalação
        </h1>
        <p class="mt-4 max-w-3xl text-base leading-relaxed text-white/90 sm:text-lg">
          Plataforma completa de MMN (marketing multinível): escritório virtual do consultor, rede binária e unilevel, loja, pedidos,
          financeiro e relatórios. Sem construir nada do zero — o sistema já existe, roda hoje e é parametrizado para a sua regra de negócio.
        </p>

      </div>

      <div class="mt-8 grid items-stretch gap-6 lg:grid-cols-2">
        <div class="flex flex-col justify-center rounded-3xl border border-amber-300/40 bg-white/[0.07] p-5 sm:p-6">
          <p class="text-sm font-semibold uppercase tracking-[0.18em] text-white/70">Instalação</p>
          <div class="mt-2 flex flex-wrap items-end gap-3">
            <span class="text-2xl font-semibold text-white/50 line-through"><?= htmlspecialchars($moneyFrom, ENT_QUOTES, 'UTF-8') ?></span>
            <span class="font-[var(--font-heading)] text-5xl font-bold text-amber-300"><?= htmlspecialchars($moneyTo, ENT_QUOTES, 'UTF-8') ?></span>
            <span class="pb-1 text-sm font-semibold text-white/85">em 2x &nbsp;·&nbsp; ou <span class="text-amber-300 font-bold"><?= htmlspecialchars($moneyCash, ENT_QUOTES, 'UTF-8') ?></span> à vista</span>
          </div>
          <p class="mt-2 text-sm text-white/85">
            Parcelado: metade no fechamento, metade na entrega. À vista: <strong><?= htmlspecialchars($moneyCash, ENT_QUOTES, 'UTF-8') ?></strong> no fechamento. Mensalidade a partir de <strong>R$ 500</strong>, proporcional ao seu faturamento.
          </p>
          <div class="mt-5 grid gap-3 sm:grid-cols-2">
            <a href="#garantir" class="inline-flex items-center justify-center rounded-full bg-amber-400 px-6 py-3.5 text-sm font-bold uppercase tracking-wide text-brand transition hover:-translate-y-0.5 hover:bg-amber-300">
              Quero garantir o desconto
            </a>
            <a href="<?= htmlspecialchars($whatsappHref, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-full bg-[#25D366] px-6 py-3.5 text-sm font-bold uppercase tracking-wide text-white transition hover:-translate-y-0.5">
              Chamar no WhatsApp
            </a>
          </div>
          <?php if ($promoActive): ?>
            <p class="mt-3 text-center text-xs text-white/70">
              <?= (int) $promoSlotsFilled ?> das <?= (int) $promoSlots ?> vagas já preenchidas (<?= promoClientesHtml() ?>) — restam <strong class="text-amber-300"><?= (int) $promoSlotsLeft ?></strong> até <?= htmlspecialchars($deadlineLabel, ENT_QUOTES, 'UTF-8') ?>.
            </p>
            <div class="mx-auto mt-2 h-1.5 w-full max-w-[260px] overflow-hidden rounded-full bg-white/15">
              <div class="h-full rounded-full bg-amber-400" style="width: <?= (int) round($promoSlotsFilled / max(1, $promoSlots) * 100) ?>%"></div>
            </div>
          <?php endif; ?>
        </div>

        <div class="relative overflow-hidden rounded-3xl border border-amber-300/40 bg-white/[0.07]">
          <picture>
            <source srcset="../imagens/lp/hero-oferta.webp" type="image/webp" />
            <img
              src="../imagens/lp/hero-oferta.jpg"
              alt="Painel do escritório virtual com a rede de consultores conectada"
              class="h-full min-h-[280px] w-full object-cover"
              width="900"
              height="720"
              loading="eager"
              decoding="async"
              fetchpriority="high"
            />
          </picture>
        </div>
      </div>
    </section>

    <section class="pb-4">
      <div class="grid gap-3 md:grid-cols-3">
        <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <p class="font-[var(--font-heading)] text-3xl font-bold text-amber-300">10 dias</p>
          <p class="mt-1 text-sm text-white/85">foi o prazo da última implantação internacional: loja, escritório e admin no ar em julho de 2026.</p>
        </div>
        <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <p class="font-[var(--font-heading)] text-3xl font-bold text-amber-300">10 anos</p>
          <p class="mt-1 text-sm text-white/85">de Sistema Venda Direta no ar — com um time que desenvolve sistemas de vendas, financeiro e fiscal desde 2002.</p>
        </div>
        <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <p class="font-[var(--font-heading)] text-3xl font-bold text-amber-300">5 países</p>
          <p class="mt-1 text-sm text-white/85">atendidos pela plataforma — Brasil, Paraguai, Bolívia, Estados Unidos e Portugal — em três idiomas e moeda local.</p>
        </div>
      </div>
    </section>

    <section class="py-10">
      <h2 class="font-[var(--font-heading)] text-2xl font-bold sm:text-[32px]">Implantação assistida por IA</h2>
      <div class="mt-2 h-1 w-[72px] rounded-full bg-amber-300"></div>
      <p class="mt-4 max-w-3xl text-base leading-relaxed text-white/90">
        A parte lenta de colocar um sistema de venda direta no ar nunca foi o sistema — é a carga de dados, a tradução,
        a parametrização do plano e os testes. É exatamente aí que aplicamos IA no nosso processo de implantação,
        com revisão humana em cima de tudo que entra em produção.
      </p>

      <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-semibold">Carga de catálogo</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/85">Descrição, categoria e preço tratados em lote. Na última implantação foram 170 SKUs carregados e reagrupados em 6 famílias.</p>
        </div>
        <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-semibold">Tradução da plataforma</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/85">Cerca de 900 chaves em português, espanhol e inglês cobrindo loja, cadastro, escritório e login.</p>
        </div>
        <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-semibold">Textos da marca</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/85">Conteúdo institucional escrito a partir do seu material técnico, no lugar do texto genérico de sistema.</p>
        </div>
        <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-semibold">Testes e revisão</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/85">Varredura de moeda, formulários e segurança antes do primeiro consultor entrar no sistema.</p>
        </div>
      </div>
    </section>

    <section class="py-10">
      <h2 class="font-[var(--font-heading)] text-2xl font-bold sm:text-[32px]">O que o sistema de marketing multinível já traz pronto</h2>
      <div class="mt-2 h-1 w-[72px] rounded-full bg-amber-300"></div>

      <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        <?php
        $included = [
            'Escritório virtual do consultor, com dashboard de ganhos, saldo e ativação',
            'Rede multinível binária com troca de perna preferencial e árvore unilevel',
            'Ranking de líderes, contas inativas e contas pendentes',
            'Convites por e-mail com acompanhamento de cadastro',
            'Recompra e pacotes com desconto por plano',
            'Pedidos com PIX, cartão, boleto, depósito e pagamento com bônus',
            'Carrinho com valor de compra, venda e lucro, e cálculo de frete',
            'Financeiro: extrato, saque e aprovação de pedido com saldo',
            'Loja virtual integrada ao cadastro do consultor',
            '11 relatórios prontos, mais os customizados na parametrização',
            'Múltiplos idiomas (PT/EN/ES) e múltiplas moedas',
            'Servidor Linux dedicado, filtro de acesso e backup replicado',
        ];
        foreach ($included as $item): ?>
          <div class="flex gap-3 rounded-2xl border border-white/20 bg-white/5 px-4 py-3">
            <span class="mt-0.5 font-bold text-amber-300" aria-hidden="true">✓</span>
            <span class="text-sm leading-relaxed text-white/90"><?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?></span>
          </div>
        <?php endforeach; ?>
      </div>

      <p class="mt-4 text-sm text-white/70">
        Integrações de ERP e gateway de pagamento são orçadas à parte, conforme o provedor escolhido.
      </p>
    </section>

    <section class="py-6">
      <h2 class="font-[var(--font-heading)] text-xl font-bold sm:text-2xl">Tecnologia por trás da sua operação</h2>
      <div class="mt-2 h-1 w-[56px] rounded-full bg-amber-300"></div>
      <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <div class="flex items-start gap-3 rounded-2xl border border-white/20 bg-white/5 px-4 py-3"><svg class="h-5 w-5 shrink-0 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg><span class="text-sm leading-relaxed text-white/90"><strong>Proteção Cloudflare</strong> contra ataques e sobrecarga</span></div>
        <div class="flex items-start gap-3 rounded-2xl border border-white/20 bg-white/5 px-4 py-3"><svg class="h-5 w-5 shrink-0 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg><span class="text-sm leading-relaxed text-white/90"><strong>Infraestrutura dedicada</strong> — nada compartilhado com terceiros</span></div>
        <div class="flex items-start gap-3 rounded-2xl border border-white/20 bg-white/5 px-4 py-3"><svg class="h-5 w-5 shrink-0 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg><span class="text-sm leading-relaxed text-white/90"><strong>SSL automático</strong> e cookies de sessão endurecidos</span></div>
        <div class="flex items-start gap-3 rounded-2xl border border-white/20 bg-white/5 px-4 py-3"><svg class="h-5 w-5 shrink-0 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg><span class="text-sm leading-relaxed text-white/90"><strong>Backups replicados</strong> em ambiente externo</span></div>
      </div>
    </section>


    <section class="py-10">
      <h2 class="font-[var(--font-heading)] text-2xl font-bold sm:text-[32px]">Mensalidade proporcional ao seu faturamento</h2>
      <div class="mt-2 h-1 w-[72px] rounded-full bg-amber-300"></div>
      <p class="mt-4 max-w-3xl text-base leading-relaxed text-white/90">
        Você não paga por porte que ainda não tem. A mensalidade acompanha o faturamento da operação — começa em R$ 500 e sobe por faixa.
      </p>

      <div class="mt-6 overflow-x-auto rounded-2xl border border-white/20 bg-white/5">
        <table class="w-full min-w-[420px] text-left text-sm">
          <thead class="border-b border-white/15 text-white/70">
            <tr>
              <th class="px-5 py-3 font-semibold uppercase tracking-wide">Faturamento mensal</th>
              <th class="px-5 py-3 font-semibold uppercase tracking-wide">Mensalidade</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($monthlyTiers as $tier): ?>
              <tr class="border-b border-white/10 last:border-0">
                <td class="px-5 py-3 text-white/90"><?= htmlspecialchars($tier['revenue'], ENT_QUOTES, 'UTF-8') ?></td>
                <td class="px-5 py-3 font-semibold text-amber-300"><?= htmlspecialchars($tier['price'], ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <p class="mt-3 text-sm text-white/70">
        Acima de R$ 1 milhão por mês, avaliamos a infraestrutura e apresentamos a proposta com pelo menos 30 dias de antecedência.
      </p>
    </section>


    <section id="simulador" class="scroll-mt-24 py-10">
      <h2 class="font-[var(--font-heading)] text-2xl font-bold sm:text-[32px]">Simule os números da sua operação</h2>
      <div class="mt-2 h-1 w-[72px] rounded-full bg-amber-300"></div>
      <p class="mt-4 max-w-3xl text-base leading-relaxed text-white/90">
        Ajuste os três controles e veja o faturamento projetado, quanto o plano distribui em comissões
        e quanto o sistema custa nessa faixa — sem cadastro, sem e-mail.
      </p>

      <div class="mt-6 grid gap-6 lg:grid-cols-[1.1fr_1fr]">
        <div class="rounded-[24px] border border-white/20 bg-white/5 p-5 sm:p-6">
          <div>
            <div class="flex items-center justify-between gap-3">
              <label for="sim-consultores" class="text-sm font-semibold text-white/90">Consultores ativos</label>
              <span id="sim-consultores-out" class="rounded-full bg-white/10 px-3 py-1 text-sm font-bold text-amber-300">100</span>
            </div>
            <input id="sim-consultores" type="range" min="10" max="3000" step="10" value="100" class="mt-3 w-full" style="accent-color:#fcd34d" />
          </div>

          <div class="mt-6">
            <div class="flex items-center justify-between gap-3">
              <label for="sim-ticket" class="text-sm font-semibold text-white/90">Compra média mensal por consultor</label>
              <span id="sim-ticket-out" class="rounded-full bg-white/10 px-3 py-1 text-sm font-bold text-amber-300">R$ 300</span>
            </div>
            <input id="sim-ticket" type="range" min="50" max="2000" step="50" value="300" class="mt-3 w-full" style="accent-color:#fcd34d" />
          </div>

          <div class="mt-6">
            <div class="flex items-center justify-between gap-3">
              <label for="sim-payout" class="text-sm font-semibold text-white/90">Payout do plano (comissões + bônus)</label>
              <span id="sim-payout-out" class="rounded-full bg-white/10 px-3 py-1 text-sm font-bold text-amber-300">40%</span>
            </div>
            <input id="sim-payout" type="range" min="10" max="60" step="5" value="40" class="mt-3 w-full" style="accent-color:#fcd34d" />
            <p class="mt-2 text-xs text-white/60">Payout é o total que o plano devolve pra rede. Acima de ~50% costuma comprometer a margem — parametrizamos isso com você na implantação.</p>
          </div>
        </div>

        <div class="flex flex-col gap-3">
          <div class="rounded-2xl border border-white/20 bg-white/5 px-5 py-4">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Faturamento projetado / mês</p>
            <p id="sim-faturamento" class="mt-1 font-[var(--font-heading)] text-3xl font-bold text-white">R$ 30.000</p>
          </div>
          <div class="rounded-2xl border border-white/20 bg-white/5 px-5 py-4">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Comissões distribuídas pela rede</p>
            <p id="sim-comissoes" class="mt-1 font-[var(--font-heading)] text-3xl font-bold text-white">R$ 12.000</p>
            <p class="mt-1 text-xs text-white/60">calculadas e pagas automaticamente pelo sistema</p>
          </div>
          <div class="rounded-2xl border border-amber-300/40 bg-white/[0.07] px-5 py-4">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Mensalidade do sistema nessa faixa</p>
            <p id="sim-mensalidade" class="mt-1 font-[var(--font-heading)] text-3xl font-bold text-amber-300">R$ 500</p>
            <p id="sim-percentual" class="mt-1 text-xs text-white/60">1,7% do faturamento projetado</p>
          </div>
          <a href="#garantir" class="inline-flex items-center justify-center rounded-full bg-amber-400 px-6 py-3.5 text-sm font-bold uppercase tracking-wide text-brand transition hover:-translate-y-0.5 hover:bg-amber-300">
            Quero esse plano rodando
          </a>
        </div>
      </div>
      <p class="mt-3 text-xs text-white/55">Projeção ilustrativa a partir dos valores informados — não é promessa de resultado. A tabela oficial de mensalidades está acima; acima de R$ 1 milhão/mês, proposta sob avaliação de infraestrutura.</p>

      <script>
        (function () {
          var tiers = [[50000,500],[100000,1000],[200000,1500],[350000,3000],[500000,4500],[750000,7000],[1000000,9000]];
          var brl = new Intl.NumberFormat("pt-BR",{style:"currency",currency:"BRL",maximumFractionDigits:0});
          var pct = new Intl.NumberFormat("pt-BR",{maximumFractionDigits:1});
          var used = false;
          function $(id){return document.getElementById(id);}
          function calc(){
            var c = +$("sim-consultores").value, t = +$("sim-ticket").value, p = +$("sim-payout").value;
            $("sim-consultores-out").textContent = c.toLocaleString("pt-BR");
            $("sim-ticket-out").textContent = brl.format(t);
            $("sim-payout-out").textContent = p + "%";
            var fat = c * t;
            $("sim-faturamento").textContent = brl.format(fat);
            $("sim-comissoes").textContent = brl.format(fat * p / 100);
            var fee = null;
            for (var i = 0; i < tiers.length; i++) { if (fat <= tiers[i][0]) { fee = tiers[i][1]; break; } }
            window.__svdSimBucket = fat <= 50000 ? "ate-50k" : fat <= 100000 ? "50k-100k" : fat <= 200000 ? "100k-200k" : fat <= 350000 ? "200k-350k" : fat <= 500000 ? "350k-500k" : fat <= 1000000 ? "500k-1M" : "acima-1M";
            if (fee === null) {
              $("sim-mensalidade").textContent = "Sob proposta";
              $("sim-percentual").textContent = "acima de R$ 1 milhão/mês — avaliação de infraestrutura";
            } else {
              $("sim-mensalidade").textContent = brl.format(fee);
              $("sim-percentual").textContent = pct.format(fee / fat * 100) + "% do faturamento projetado";
            }
            if (!used) { used = true; if (typeof window.gtag === "function") { window.gtag("event", "simulator_use", { page: "lp-oferta-instalacao" }); } }
          }
          ["sim-consultores","sim-ticket","sim-payout"].forEach(function(id){ $(id).addEventListener("input", calc); });
          calc(); used = false;
        })();
      </script>
    </section>

    <section class="py-10">
      <h2 class="font-[var(--font-heading)] text-2xl font-bold sm:text-[32px]">Não é promessa — é operação rodando</h2>
      <div class="mt-2 h-1 w-[72px] rounded-full bg-amber-300"></div>

      <?= promoVitrine("../") ?>
    </section>



    <section class="py-6">
      <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-amber-300/40 bg-white/[0.07] p-5">
          <p class="font-[var(--font-heading)] text-lg font-bold text-amber-300">No ar em dias, não meses</p>
          <p class="mt-2 text-sm leading-relaxed text-white/90">A última implantação internacional entrou em produção em 10 dias — loja, escritório e administrativo. O sistema já existe; o trabalho é parametrizar a sua regra.</p>
        </div>
        <div class="rounded-2xl border border-amber-300/40 bg-white/[0.07] p-5">
          <p class="font-[var(--font-heading)] text-lg font-bold text-amber-300">Migração com valor fechado</p>
          <p class="mt-2 text-sm leading-relaxed text-white/90">Vindo de outro sistema? Migramos seus dados com escopo e preço definidos antes do contrato — sem surpresa no meio do caminho.</p>
        </div>
        <div class="rounded-2xl border border-amber-300/40 bg-white/[0.07] p-5">
          <p class="font-[var(--font-heading)] text-lg font-bold text-amber-300">Integração com Bling</p>
          <p class="mt-2 text-sm leading-relaxed text-white/90">Pedidos, estoque e faturamento sincronizados com o Bling, nosso ERP parceiro — e se você já usa outro ERP, integramos com ele.</p>
        </div>
      </div>

      <figure class="mt-6 rounded-2xl border border-white/20 bg-white/5 p-5 sm:p-6">
        <blockquote class="text-base leading-relaxed text-white/90">
          "Há mais de 10 anos usamos o sistema venda direta, que nos ajuda a gerenciar nossa rede de distribuidores
          e a aumentar nossas vendas com perfeição, eu recomendo!"
        </blockquote>
        <figcaption class="mt-3 text-sm font-semibold text-white/80">Leandro Sato — Ecotrend South America <span class="ml-2 text-amber-300">★★★★★</span></figcaption>
      </figure>
    </section>

    <section class="py-6">
      <div class="rounded-[30px] border border-white/30 bg-white/[0.08] p-6 sm:p-8">
        <div class="grid items-center gap-6 lg:grid-cols-[1fr_auto]">
          <div>
            <h2 class="font-[var(--font-heading)] text-2xl font-bold sm:text-[28px]">Quer conhecer a plataforma por completo?</h2>
            <p class="mt-3 max-w-2xl text-base leading-relaxed text-white/90">
              Entre no ambiente de demonstração e navegue pelo sistema inteiro: loja, escritório do
              consultor e painel administrativo, com dados de uma operação real. Sem cadastro.
            </p>
          </div>
          <div class="flex flex-col items-stretch gap-3 sm:min-w-[280px]">
            <a href="https://painel.sistemavendadireta.com.br/primeiros-passos?utm_source=site&utm_medium=oferta&utm_campaign=demo" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-full border border-white/75 px-6 py-3 text-sm font-bold uppercase tracking-wide hover:bg-white/10">
              Conhecer o Sistema Venda Direta
            </a>
            <a href="../cases/" class="inline-flex items-center justify-center rounded-full border border-white/35 px-6 py-3 text-sm font-semibold uppercase tracking-wide hover:bg-white/10">
              Ver os cases
            </a>
          </div>
        </div>
      </div>
    </section>

    <section id="garantir" class="scroll-mt-24 py-10">
      <div class="rounded-[30px] border border-amber-300/40 bg-white/[0.07] p-6 sm:p-8">
        <div class="grid gap-8 lg:grid-cols-[1fr_1fr]">
          <div>
            <h2 class="font-[var(--font-heading)] text-2xl font-bold sm:text-[32px]">
              Garanta a instalação por <span class="text-amber-300"><?= htmlspecialchars($moneyTo, ENT_QUOTES, 'UTF-8') ?></span> — ou <span class="text-amber-300"><?= htmlspecialchars($moneyCash, ENT_QUOTES, 'UTF-8') ?></span> à vista
            </h2>
            <p class="mt-3 text-base leading-relaxed text-white/90">
              Deixe seu WhatsApp. Uma pessoa da equipe entra em contato, entende seu plano de negócio
              e te mostra o sistema rodando — sem apresentação genérica.
            </p>
            <?php if ($promoActive): ?>
              <p class="mt-4 inline-flex rounded-full border border-amber-300/50 bg-amber-400/15 px-4 py-2 text-sm font-semibold text-amber-200">
                Condição válida até <?= htmlspecialchars($deadlineLabel, ENT_QUOTES, 'UTF-8') ?>
              </p>
            <?php endif; ?>

            <a href="<?= htmlspecialchars($whatsappHref, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-[#25D366] px-5 py-3.5 text-sm font-bold uppercase tracking-wide text-white sm:w-auto sm:px-8">
              Prefiro falar no WhatsApp
            </a>
            <p class="mt-3 text-sm text-white/70">Telefone: 11 99456-6726</p>
          </div>

          <div class="rounded-[24px] border border-white/20 bg-brand-dark/40 p-5 sm:p-6">
            <form
              id="contact-lead-form"
              action="../enviar-contato.php"
              method="post"
              class="space-y-4"
              data-whatsapp-phone="<?= htmlspecialchars($whatsappPhone, ENT_QUOTES, 'UTF-8') ?>"
              data-whatsapp-message-template="Ola, vim pela Promocao 10 Anos. Meu nome e {nome} e meu WhatsApp e {whatsapp}."
            >
              <input type="hidden" name="redirect" value="/oferta/" />
              <input type="hidden" name="origem" value="lp-oferta-instalacao" />
              <input type="hidden" name="servico" value="Sistema Venda Direta — instalacao promocional" />
              <input type="hidden" name="mensagem" value="Lead da LP de oferta (instalacao promocional)" />

              <div>
                <label for="contact-nome" class="mb-2 block text-sm font-medium text-white/90">Nome</label>
                <input id="contact-nome" name="nome" type="text" required autocomplete="name" placeholder="Seu nome" class="w-full rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-white/45 focus:border-white/60 focus:outline-none focus:ring-2 focus:ring-white/20" />
              </div>

              <div>
                <label for="contact-whatsapp" class="mb-2 block text-sm font-medium text-white/90">WhatsApp</label>
                <input id="contact-whatsapp" name="whatsapp" type="tel" required autocomplete="tel" inputmode="tel" placeholder="(11) 99999-9999" class="w-full rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-white/45 focus:border-white/60 focus:outline-none focus:ring-2 focus:ring-white/20" />
              </div>

              <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-amber-400 px-5 py-3.5 text-sm font-bold uppercase tracking-wide text-brand transition hover:-translate-y-0.5 hover:bg-amber-300">
                Quero garantir o desconto
              </button>
              <p class="text-center text-xs text-white/60">Sem compromisso. Respondemos em horário comercial.</p>
            </form>

            <?php if ($mailStatus === 'ok'): ?>
              <div class="mt-4 rounded-2xl border border-emerald-300/30 bg-emerald-500/10 p-4">
                <p class="text-sm font-semibold text-white">Recebemos seu contato.</p>
                <a id="contact-success-whatsapp-link" href="<?= htmlspecialchars($whatsappHref, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex w-full items-center justify-center rounded-full border border-emerald-200/45 px-4 py-2.5 text-sm font-semibold text-white hover:bg-white/10">
                  Abrir WhatsApp agora
                </a>
              </div>
            <?php elseif ($mailStatus === 'erro'): ?>
              <div class="mt-4 rounded-2xl border border-rose-300/30 bg-rose-500/10 p-4">
                <p class="text-sm font-semibold text-white">Não foi possível concluir o envio.</p>
                <p class="mt-1 text-sm text-white/80">Tente novamente ou fale direto no WhatsApp.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>

    <section class="py-10">
      <h2 class="font-[var(--font-heading)] text-2xl font-bold sm:text-[32px]">Perguntas que todo mundo faz antes de fechar</h2>
      <div class="mt-2 h-1 w-[72px] rounded-full bg-amber-300"></div>

      <div class="mt-6 grid gap-3">
        <?php
        $faq = [
            ['Já tenho um sistema. Dá para migrar os dados?', 'Sim, desde que tenhamos acesso a eles de forma organizada. A migração entra no escopo da parametrização.'],
            ['Posso usar minha marca e meu domínio?', 'Sim. O sistema roda com a identidade da sua empresa e no seu domínio — é um dos diferenciais da plataforma.'],
            ['Meu plano de negócio é diferente. Serve?', 'A instalação contempla a parametrização do seu plano. Binário, unilevel, comissão por cargo ou modelo próprio: a regra é configurada, não improvisada.'],
            ['O que não está incluso?', 'Integrações de ERP e gateway de pagamento são orçadas separadamente, porque dependem do provedor que você já usa.'],
            ['Existe outro custo além da mensalidade?', 'Não. Novas funcionalidades pedidas depois são acordadas antes de qualquer cobrança.'],
            ['Onde o sistema fica hospedado?', 'Em infraestrutura dedicada Linux, atrás de proteção Cloudflare, com certificado SSL automático e backups replicados. Sua operação não divide servidor com terceiros.'],
            ['Como funciona o suporte?', 'Direto por WhatsApp e telefone, com a mesma equipe que fez a implantação.'],
        ];
        foreach ($faq as $pair): ?>
          <details class="rounded-2xl border border-white/20 bg-white/5 p-4">
            <summary class="cursor-pointer font-semibold"><?= htmlspecialchars($pair[0], ENT_QUOTES, 'UTF-8') ?></summary>
            <p class="mt-2 text-sm leading-relaxed text-white/90"><?= htmlspecialchars($pair[1], ENT_QUOTES, 'UTF-8') ?></p>
          </details>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="py-12 text-center">
      <h2 class="font-[var(--font-heading)] text-2xl font-bold sm:text-3xl">
        Promoção 10 Anos: <?= htmlspecialchars($moneyFrom, ENT_QUOTES, 'UTF-8') ?> viram <?= htmlspecialchars($moneyCash, ENT_QUOTES, 'UTF-8') ?> à vista<?= $promoActive ? ' até ' . htmlspecialchars($deadlineLabel, ENT_QUOTES, 'UTF-8') : '' ?>
      </h2>
      <p class="mx-auto mt-3 max-w-2xl text-base leading-relaxed text-white/90">
        Depois disso, a instalação volta ao valor cheio. Se a sua operação vai começar este ano, começar agora sai bem mais barato.
      </p>
      <a href="#garantir" class="mt-6 inline-flex rounded-full bg-amber-400 px-8 py-3.5 text-sm font-bold uppercase tracking-wide text-brand transition hover:-translate-y-0.5 hover:bg-amber-300">
        Garantir minha vaga
      </a>
    </section>
  </main>

  <footer class="border-t border-white/15 bg-brand-dark/40">
    <div class="mx-auto max-w-[1140px] px-4 py-8 sm:px-6">
      <div class="flex flex-wrap items-center justify-between gap-4">
        <a href="../" target="_blank" rel="noopener" aria-label="Abrir o site do Sistema Venda Direta em nova aba"><img decoding="async" src="../imagens/Logo-Branco-1.webp" alt="Sistema Venda Direta" class="h-auto w-[150px]" width="1000" height="300" loading="lazy" /></a>
        <p class="text-sm text-white/85">
          contato@sistemavendadireta.com.br · 11 99456-6726
        </p>
      </div>
      <p class="mt-6 text-xs text-white/60">
        © Sistema Venda Direta — Todos os direitos reservados.
        Promoção 10 Anos — condição promocional de instalação (<?= htmlspecialchars($moneyTo, ENT_QUOTES, 'UTF-8') ?> em duas parcelas ou <?= htmlspecialchars($moneyCash, ENT_QUOTES, 'UTF-8') ?> à vista) válida para contratos fechados até <?= htmlspecialchars($deadlineLabel, ENT_QUOTES, 'UTF-8') ?>,
        limitada a <?= (int) $promoSlots ?> implantações no período. Não cumulativa com outras condições comerciais.
      </p>
    </div>
  </footer>

  <a href="<?= htmlspecialchars($whatsappHref, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" aria-label="Falar no WhatsApp" class="fixed bottom-3 right-3 z-[70] inline-flex items-center gap-2 rounded-full bg-[#25D366] px-4 py-3 text-sm font-bold text-white shadow-[0_10px_24px_rgba(0,0,0,0.35)] ring-2 ring-white/30 sm:bottom-4 sm:right-4">
    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/20 text-base leading-none">W</span>
    <span>WhatsApp</span>
  </a>

  <script src="../js/scripts.js" defer></script>
  <script>
    // Eventos de conversao GA4 — no-op enquanto o gtag nao estiver carregado (SVD_GA4_ID vazio).
    (function () {
      function track(name, params) {
        if (typeof window.gtag === "function") {
          var enriched = params || {};
          if (window.__svdSimBucket) {
            enriched.sim_faturamento = window.__svdSimBucket;
          }
          window.gtag("event", name, enriched);
        }
      }
      function zapRef() {
        try {
          var r = window.sessionStorage.getItem("svd-zap-ref");
          if (!r) {
            r = Math.random().toString(36).slice(2, 7).toUpperCase().replace(/[^A-Z0-9]/g, "X");
            while (r.length < 5) r += "X";
            window.sessionStorage.setItem("svd-zap-ref", r);
          }
          return r;
        } catch (e) { return "AAAAA"; }
      }
      document.addEventListener("click", function (event) {
        var link = event.target.closest && event.target.closest('a[href*="wa.me"]');
        if (!link) return;
        track("whatsapp_click", { page: "lp-oferta-instalacao" });
        var ref = zapRef();
        // embute o codigo de referencia na mensagem pre-preenchida do WhatsApp
        if (link.href.indexOf("text=") !== -1 && link.href.indexOf("%5Bref") === -1) {
          link.href += encodeURIComponent(" [ref " + ref + "]");
        }
        // registra o lead de intencao no servidor com a atribuicao da sessao
        try {
          var stored = {};
          try { stored = JSON.parse(window.sessionStorage.getItem("svd-attribution") || "{}"); } catch (e) {}
          var data = new FormData();
          data.append("ref", ref);
          data.append("origem", "lp-oferta-instalacao");
          data.append("ga_client_id", (document.cookie.match(/(?:^|;\s*)_ga=GA\d+\.\d+\.(\d+\.\d+)/) || [])[1] || "");
          if (stored.gclid) data.append("gclid", stored.gclid);
          if (stored.utm_source) data.append("utm_source", stored.utm_source);
          if (stored.utm_medium) data.append("utm_medium", stored.utm_medium);
          if (stored.utm_campaign) data.append("utm_campaign", stored.utm_campaign);
          if (stored.utm_content) data.append("utm_content", stored.utm_content);
          if (window.__svdSimBucket) data.append("sim_faturamento", window.__svdSimBucket);
          data.append("page_url", window.location.href.split("#")[0]);
          navigator.sendBeacon("/zap-lead.php", data);
        } catch (e) {}
      });
      var form = document.getElementById("contact-lead-form");
      if (form) {
        form.addEventListener("submit", function () {
          track("generate_lead", { page: "lp-oferta-instalacao" });
        });
      }
    })();
  </script>
</body>
</html>
