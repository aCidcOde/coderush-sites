<?php
declare(strict_types=1);

/*
[Modulo Landing de Oferta SVD — trafego pago]
@Author: André Gomes ( @acidcode )
@since 2026-08-02
Landing dedicada a campanha patrocinada: 50% de desconto na instalacao + implantacao assistida por IA.
Nao e linkada pelo index (trafego pago). noindex por ser pagina de campanha com prazo.

Parametros da campanha ficam no bloco de configuracao abaixo — alterar so aqui.
*/

$mailStatus = $_GET['mail'] ?? '';
$mailStatus = in_array($mailStatus, ['ok', 'erro'], true) ? $mailStatus : '';

// ----------------------------------------------------------------------------
// Configuracao da campanha
// ----------------------------------------------------------------------------
$promoInstallFrom = 5000;          // valor cheio da instalacao (R$)
$promoInstallTo = 2500;            // valor promocional (R$)
$promoDeadline = '2026-08-31';     // ultimo dia da promocao (America/Sao_Paulo)
$promoSlots = 8;                   // vagas de implantacao no periodo
$whatsappPhone = '5511994566726';
$whatsappMessage = 'Ola! Vim pela campanha de 50% na instalacao do Sistema Venda Direta. Quero garantir minha vaga.';

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

$whatsappHref = 'https://wa.me/' . $whatsappPhone . '?text=' . rawurlencode($whatsappMessage);

$seoBase = 'https://www.sistemavendadireta.com.br';
$seoUrl = $seoBase . '/oferta/';
$seoTitle = 'Sistema de venda direta com 50% off na instalação | Sistema Venda Direta';
$seoDescription = 'Plataforma completa de MMN e venda direta: escritório virtual, loja, rede e financeiro. '
    . 'Instalação com 50% de desconto e implantação assistida por IA.';
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
  <meta property="og:title" content="Instalação com 50% de desconto — Sistema Venda Direta" />
  <meta property="og:description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:url" content="<?= htmlspecialchars($seoUrl, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:site_name" content="Sistema Venda Direta" />
  <meta property="og:image" content="<?= htmlspecialchars($seoBase . '/imagens/Clientes.jpg', ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="twitter:card" content="summary_large_image" />

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
      <div class="mx-auto flex max-w-[1140px] flex-wrap items-center justify-between gap-2 px-4 py-2 sm:px-6">
        <p class="text-sm font-bold uppercase tracking-wide">
          50% OFF na instalação · encerra <?= htmlspecialchars($deadlineLabel, ENT_QUOTES, 'UTF-8') ?>
          <span class="ml-1 rounded-full bg-brand px-2 py-0.5 text-xs font-bold text-white"><?= (int) $daysLeft ?> dia<?= $daysLeft === 1 ? '' : 's' ?></span>
        </p>
        <a href="#garantir" class="rounded-full bg-brand px-4 py-1.5 text-xs font-bold uppercase tracking-wide text-white hover:bg-brand-dark">
          Garantir desconto
        </a>
      </div>
    </div>
  <?php endif; ?>

  <header class="border-b border-white/10 bg-brand/95">
    <div class="mx-auto flex max-w-[1140px] items-center justify-between gap-4 px-4 py-3 sm:px-6">
      <img decoding="async" src="../imagens/Logo-Branco-1.webp" alt="Sistema Venda Direta" class="h-auto w-[150px] sm:w-[200px]" width="1000" height="300" />
      <a href="<?= htmlspecialchars($whatsappHref, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="hidden rounded-full bg-[#25D366] px-4 py-2 text-sm font-bold sm:inline-flex">
        Falar agora
      </a>
    </div>
  </header>

  <main class="mx-auto max-w-[1140px] px-4 sm:px-6">

    <section class="grid items-center gap-8 py-10 lg:grid-cols-[1.15fr_1fr] lg:py-14">
      <div>
        <p class="inline-flex rounded-full border border-amber-300/50 bg-amber-400/15 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-amber-200">
          Campanha por tempo limitado
        </p>
        <h1 class="mt-4 font-[var(--font-heading)] text-3xl font-bold leading-[1.1] sm:text-4xl lg:text-[46px]">
          Sua operação de venda direta no ar por <span class="text-amber-300">metade do preço</span> de instalação
        </h1>
        <p class="mt-4 max-w-xl text-base leading-relaxed text-white/90 sm:text-lg">
          Plataforma completa de MMN: escritório virtual do consultor, rede binária e unilevel, loja, pedidos,
          financeiro e relatórios. Sem construir nada do zero — o sistema já existe, roda hoje e é parametrizado para a sua regra de negócio.
        </p>

        <div class="mt-7 rounded-3xl border border-amber-300/40 bg-white/[0.07] p-5 sm:p-6">
          <p class="text-sm font-semibold uppercase tracking-[0.18em] text-white/70">Instalação</p>
          <div class="mt-2 flex flex-wrap items-end gap-3">
            <span class="text-2xl font-semibold text-white/50 line-through"><?= htmlspecialchars($moneyFrom, ENT_QUOTES, 'UTF-8') ?></span>
            <span class="font-[var(--font-heading)] text-5xl font-bold text-amber-300"><?= htmlspecialchars($moneyTo, ENT_QUOTES, 'UTF-8') ?></span>
          </div>
          <p class="mt-2 text-sm text-white/85">
            Em duas parcelas: metade no fechamento, metade na entrega. Mensalidade a partir de <strong>R$ 500</strong>, proporcional ao seu faturamento.
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
              <?= (int) $promoSlots ?> vagas de implantação até <?= htmlspecialchars($deadlineLabel, ENT_QUOTES, 'UTF-8') ?> — a fila respeita a ordem de fechamento.
            </p>
          <?php endif; ?>
        </div>
      </div>

      <div class="grid gap-3">
        <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <p class="font-[var(--font-heading)] text-3xl font-bold text-amber-300">5 dias</p>
          <p class="mt-1 text-sm text-white/85">foi o prazo da última implantação internacional: loja, escritório e admin no ar em julho de 2026.</p>
        </div>
        <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <p class="font-[var(--font-heading)] text-3xl font-bold text-amber-300">25 anos</p>
          <p class="mt-1 text-sm text-white/85">de experiência do time em sistemas de vendas, financeiro e fiscal.</p>
        </div>
        <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <p class="font-[var(--font-heading)] text-3xl font-bold text-amber-300">2 países</p>
          <p class="mt-1 text-sm text-white/85">com operação ativa hoje — Brasil e Paraguai, em três idiomas e moeda local.</p>
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
      <h2 class="font-[var(--font-heading)] text-2xl font-bold sm:text-[32px]">O que está incluso na instalação</h2>
      <div class="mt-2 h-1 w-[72px] rounded-full bg-amber-300"></div>

      <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        <?php
        $included = [
            'Escritório virtual do consultor, com dashboard de ganhos, saldo e ativação',
            'Rede binária com troca de perna preferencial e árvore unilevel',
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

    <section class="py-10">
      <h2 class="font-[var(--font-heading)] text-2xl font-bold sm:text-[32px]">Não é promessa — é operação rodando</h2>
      <div class="mt-2 h-1 w-[72px] rounded-full bg-amber-300"></div>

      <div class="mt-6 grid gap-4 md:grid-cols-2">
        <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <div class="flex items-center rounded-xl bg-white px-4 py-3">
            <picture>
              <source srcset="../imagens/clientes/new-professionals.webp" type="image/webp" />
              <img src="../imagens/clientes/new-professionals.png" alt="New Professional's" class="h-9 w-auto object-contain sm:h-11" width="480" height="131" loading="lazy" />
            </picture>
          </div>
          <p class="mt-4 text-sm leading-relaxed text-white/90">
            Operação no Paraguai em três idiomas, preço em guarani, comissão por cargo editável no administrativo
            e endereço resolvido pela base oficial de código postal do país. No ar em 5 dias.
          </p>
        </article>

        <article class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <div class="flex items-center rounded-xl bg-white px-4 py-3">
            <picture>
              <source srcset="../imagens/clientes/protech-nutritional.webp" type="image/webp" />
              <img src="../imagens/clientes/protech-nutritional.png" alt="Protech Nutritional" class="h-9 w-auto object-contain sm:h-11" width="480" height="102" loading="lazy" />
            </picture>
          </div>
          <p class="mt-4 text-sm leading-relaxed text-white/90">
            Suplementos com distribuição exclusiva por consultor: entrada na loja pelo fluxo de indicação,
            catálogo em 9 linhas, escritório virtual e plano com três formas de ganho.
          </p>
        </article>
      </div>

      <a href="../cases/" class="mt-6 inline-flex rounded-full border border-white/60 px-5 py-2.5 text-sm font-semibold uppercase tracking-wide hover:bg-white/10">
        Ver os cases completos
      </a>
    </section>

    <section id="garantir" class="scroll-mt-24 py-10">
      <div class="rounded-[30px] border border-amber-300/40 bg-white/[0.07] p-6 sm:p-8">
        <div class="grid gap-8 lg:grid-cols-[1fr_1fr]">
          <div>
            <h2 class="font-[var(--font-heading)] text-2xl font-bold sm:text-[32px]">
              Garanta a instalação por <span class="text-amber-300"><?= htmlspecialchars($moneyTo, ENT_QUOTES, 'UTF-8') ?></span>
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
              data-whatsapp-message-template="Ola, vim pela campanha de 50% na instalacao. Meu nome e {nome} e meu WhatsApp e {whatsapp}."
            >
              <input type="hidden" name="redirect" value="/oferta/" />
              <input type="hidden" name="origem" value="lp-oferta-instalacao" />
              <input type="hidden" name="servico" value="Sistema Venda Direta — 50% instalacao" />
              <input type="hidden" name="mensagem" value="Lead da LP de oferta (50% instalacao)" />

              <div>
                <label for="contact-nome" class="mb-2 block text-sm font-medium text-white/90">Nome</label>
                <input id="contact-nome" name="nome" type="text" required autocomplete="name" placeholder="Seu nome" class="w-full rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-white/45 focus:border-white/60 focus:outline-none focus:ring-2 focus:ring-white/20" />
              </div>

              <div>
                <label for="contact-whatsapp" class="mb-2 block text-sm font-medium text-white/90">WhatsApp</label>
                <input id="contact-whatsapp" name="whatsapp" type="tel" required autocomplete="tel" inputmode="tel" placeholder="(11) 99999-9999" class="w-full rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-white/45 focus:border-white/60 focus:outline-none focus:ring-2 focus:ring-white/20" />
              </div>

              <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-amber-400 px-5 py-3.5 text-sm font-bold uppercase tracking-wide text-brand transition hover:-translate-y-0.5 hover:bg-amber-300">
                Quero o desconto de 50%
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
        <?= htmlspecialchars($moneyFrom, ENT_QUOTES, 'UTF-8') ?> viram <?= htmlspecialchars($moneyTo, ENT_QUOTES, 'UTF-8') ?><?= $promoActive ? ' até ' . htmlspecialchars($deadlineLabel, ENT_QUOTES, 'UTF-8') : '' ?>
      </h2>
      <p class="mx-auto mt-3 max-w-2xl text-base leading-relaxed text-white/90">
        Depois disso, a instalação volta ao valor cheio. Se a sua operação vai começar este ano, começar agora custa metade.
      </p>
      <a href="#garantir" class="mt-6 inline-flex rounded-full bg-amber-400 px-8 py-3.5 text-sm font-bold uppercase tracking-wide text-brand transition hover:-translate-y-0.5 hover:bg-amber-300">
        Garantir minha vaga
      </a>
    </section>
  </main>

  <footer class="border-t border-white/15 bg-brand-dark/40">
    <div class="mx-auto max-w-[1140px] px-4 py-8 sm:px-6">
      <div class="flex flex-wrap items-center justify-between gap-4">
        <img decoding="async" src="../imagens/Logo-Branco-1.webp" alt="Sistema Venda Direta" class="h-auto w-[150px]" width="1000" height="300" loading="lazy" />
        <p class="text-sm text-white/85">
          contato@sistemavendadireta.com.br · 11 99456-6726
        </p>
      </div>
      <p class="mt-6 text-xs text-white/60">
        © Sistema Venda Direta — Todos os direitos reservados.
        Promoção de 50% sobre o valor de instalação válida para contratos fechados até <?= htmlspecialchars($deadlineLabel, ENT_QUOTES, 'UTF-8') ?>,
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
          window.gtag("event", name, params || {});
        }
      }
      document.addEventListener("click", function (event) {
        var link = event.target.closest && event.target.closest('a[href*="wa.me"]');
        if (link) {
          track("whatsapp_click", { page: "lp-oferta-instalacao" });
        }
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
