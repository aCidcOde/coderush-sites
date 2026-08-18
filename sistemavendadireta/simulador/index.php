<?php
declare(strict_types=1);

/*
[Modulo Simulador de Plano MMN — SVD]
@Author: André Gomes ( @acidcode )
@since 2026-08-18
@updated 2026-08-18 (layout em passos, barra fixa de resultado, exportacao em PDF)

Checador de viabilidade de plano de marketing multinivel.

POR QUE ESTA FERRAMENTA: o erro que quebra plano de MMN quase sempre e o mesmo —
cada bonus e desenhado isolado, ninguem soma o total, e o plano so fecha se a
rede crescer pra sempre. Aqui a pessoa informa margem e percentuais e recebe a
resposta incomoda: fecha ou nao fecha, e onde quebra.

DECISOES DE CALCULO (as tres que evitam o resultado bonito e errado):
1. Margem de revenda NAO entra. O ticket informado ja e o preco que o consultor
   paga, com desconto embutido — descontar de novo conta duas vezes.
2. Payout = indicacao + unilevel + carreira. Nada mais.
3. Breakage so incide em unilevel e carreira, que dependem de qualificacao.
   Indicacao entra cheia: sempre existe um patrocinador pra receber.

LAYOUT: passos em vez de formulario unico — sao 9 parametros, e despejar todos de
uma vez faz desistir. Resultado em barra fixa embaixo pro numero acompanhar quem
ainda esta mexendo nos controles, com ancora pro total da tabela.

Calculo roda no browser: sem servidor, sem lead capturado a forca. Indexavel de
proposito — e isca de conteudo, nao LP de campanha.
*/

require_once __DIR__ . '/../inc/promo.php';

$seoBase = 'https://www.sistemavendadireta.com.br';
$seoUrl = $seoBase . '/simulador/';
$seoTitle = 'Simulador de Plano de Marketing Multinível | Veja se o seu plano fecha';
$seoDescription = 'Calcule o payout real do seu plano de MMN: bônus de indicação, unilevel por nível '
    . 'e carreira. Projeção de 6 meses e veredito de viabilidade. Sem cadastro.';

$whatsappHref = 'https://wa.me/5511994566726?text=' . rawurlencode(
    'Ola! Usei o simulador de plano no site e quero conversar sobre a parametrizacao.');
$demoHref = DEMO_URL . '?utm_source=site&utm_medium=simulador&utm_campaign=demo';
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>" />
  <link rel="canonical" href="<?= htmlspecialchars($seoUrl, ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="theme-color" content="#004AAD" />
  <link rel="icon" type="image/svg+xml" href="../favicon.svg" />
  <link rel="alternate icon" href="../favicon.ico" />

  <meta property="og:locale" content="pt_BR" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="Simulador de Plano de Marketing Multinível" />
  <meta property="og:description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:url" content="<?= htmlspecialchars($seoUrl, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:site_name" content="Sistema Venda Direta" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&amp;family=Roboto:wght@300;400;500;700&amp;display=swap" />
  <?php $cssVersion = (string) @filemtime(__DIR__ . '/../css/site-tailwind.css'); ?>
  <link rel="stylesheet" href="../css/site-tailwind.css?v=<?= htmlspecialchars($cssVersion, ENT_QUOTES, 'UTF-8') ?>" />
  <link rel="stylesheet" href="../css/site-optimizations.css?v=<?= htmlspecialchars($cssVersion, ENT_QUOTES, 'UTF-8') ?>" />
  <link rel="stylesheet" href="../css/styles.css?v=<?= htmlspecialchars($cssVersion, ENT_QUOTES, 'UTF-8') ?>" />
  <style>
    /* PDF sai pela impressao do proprio navegador: sem biblioteca, sem CDN, e ja
       vem paginado com fonte real. O clique abre os tres passos antes, senao
       imprimiria so o que estava visivel. */
    .so-impressao { display: none; }
    @media print {
      body { background: #fff !important; color: #111 !important; }
      .nao-imprime { display: none !important; }
      .so-impressao { display: block !important; }
      .passo { display: block !important; break-inside: avoid; margin-bottom: 16px; }
      .cartao-impressao { border: 1px solid #cbd5e1 !important; background: #fff !important; }
      .cartao-impressao *, table, th, td, h2, h3, p, span, td a { color: #111 !important; }
      th, td { border-bottom: 1px solid #e2e8f0 !important; }
      main { max-width: none !important; padding: 0 !important; }
      @page { margin: 14mm; }
    }
  </style>
  <?php include __DIR__ . '/../inc/analytics.php'; ?>
</head>
<body class="bg-brand text-white antialiased font-[var(--font-body)] site-optimized">

  <header class="nao-imprime border-b border-white/10 bg-brand/95">
    <div class="mx-auto flex w-full items-center justify-between gap-4 px-4 py-3 sm:px-8">
      <a href="../"><img decoding="async" src="../imagens/Logo-Branco-1.webp" alt="Sistema Venda Direta" class="h-auto w-[150px] sm:w-[190px]" width="1000" height="300" loading="eager" /></a>
      <div class="flex items-center gap-4">
        <a href="<?= htmlspecialchars($demoHref, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="hidden text-sm font-semibold text-amber-300 hover:text-amber-200 sm:inline">Ver demonstração</a>
        <a href="../oferta/?utm_source=site&amp;utm_medium=simulador&amp;utm_campaign=promo-10-anos" class="rounded-full bg-amber-400 px-4 py-2 text-sm font-bold text-brand hover:bg-amber-300">Ver a promoção</a>
      </div>
    </div>
  </header>

  <main class="mx-auto max-w-[1000px] px-4 pb-36 sm:px-6">

    <div class="so-impressao mb-6 border-b border-gray-300 pb-4">
      <p style="font-size:11px;letter-spacing:.14em;text-transform:uppercase;">Sistema Venda Direta</p>
      <h1 style="font-size:21px;font-weight:800;margin-top:4px;">Simulação de plano de marketing multinível</h1>
      <p id="pdf-data" style="font-size:11px;margin-top:4px;"></p>
    </div>

    <section class="nao-imprime grid items-center gap-8 pt-8 pb-6 lg:grid-cols-[1.15fr_1fr]">
      <div>
        <p class="inline-flex rounded-full border border-amber-300/50 bg-amber-400/15 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-amber-200">
          Ferramenta gratuita · sem cadastro
        </p>
        <h1 class="mt-4 font-[var(--font-heading)] text-3xl font-bold leading-[1.15] sm:text-4xl">
          Seu plano de marketing multinível<br />
          <span class="text-amber-300">fecha a conta?</span>
        </h1>
        <p class="mt-4 text-base leading-relaxed text-white/90">
          A maioria dos planos de MMN quebra pelo mesmo motivo: cada bônus é desenhado separado, ninguém
          soma o total, e o plano só fecha se a rede crescer para sempre. Preencha os três passos — a conta
          aparece na barra abaixo enquanto você mexe.
        </p>
      </div>
      <div class="overflow-hidden rounded-3xl border border-white/15 bg-white/5">
        <picture>
          <source srcset="../imagens/lp/hero-oferta.webp" type="image/webp" />
          <img src="../imagens/lp/hero-oferta.jpg"
               alt="Painel com a rede de consultores e os indicadores do plano de comissões"
               class="h-full w-full object-cover" width="900" height="720"
               loading="eager" decoding="async" fetchpriority="high" />
        </picture>
      </div>
    </section>

    <nav class="nao-imprime sticky top-0 z-40 -mx-4 mb-5 border-b border-white/15 bg-brand/95 px-4 py-3 backdrop-blur sm:-mx-6 sm:px-6">
      <div class="flex gap-2 overflow-x-auto">
        <button type="button" data-aba="1" class="aba flex-shrink-0 rounded-full px-4 py-2 text-sm font-bold transition">1 · Operação</button>
        <button type="button" data-aba="2" class="aba flex-shrink-0 rounded-full px-4 py-2 text-sm font-bold transition">2 · Bônus do plano</button>
        <button type="button" data-aba="3" class="aba flex-shrink-0 rounded-full px-4 py-2 text-sm font-bold transition">3 · Resultado</button>
      </div>
    </nav>

    <section id="passo-1" class="passo">
      <div class="cartao-impressao rounded-[24px] border border-white/20 bg-white/5 p-5 sm:p-7">
        <h2 class="font-[var(--font-heading)] text-xl font-bold">A operação</h2>
        <p class="mt-1 text-sm text-white/70">Tamanho, ritmo e a margem que vai pagar tudo.</p>

        <div class="mt-6 grid gap-6 sm:grid-cols-2">
          <div>
            <div class="flex items-center justify-between gap-3">
              <label for="s-consultores" class="text-sm font-semibold text-white/90">Consultores ativos hoje</label>
              <span id="o-consultores" class="rounded-full bg-white/10 px-3 py-1 text-sm font-bold text-amber-300">100</span>
            </div>
            <input id="s-consultores" type="range" min="10" max="3000" step="10" value="100" class="mt-3 w-full" style="accent-color:#fcd34d" />
          </div>
          <div>
            <div class="flex items-center justify-between gap-3">
              <label for="s-ticket" class="text-sm font-semibold text-white/90">Ticket médio do pedido</label>
              <span id="o-ticket" class="rounded-full bg-white/10 px-3 py-1 text-sm font-bold text-amber-300">R$ 300</span>
            </div>
            <input id="s-ticket" type="range" min="50" max="2000" step="10" value="300" class="mt-3 w-full" style="accent-color:#fcd34d" />
          </div>
          <div>
            <div class="flex items-center justify-between gap-3">
              <label for="s-crescimento" class="text-sm font-semibold text-white/90">Crescimento mensal</label>
              <span id="o-crescimento" class="rounded-full bg-white/10 px-3 py-1 text-sm font-bold text-amber-300">15%</span>
            </div>
            <input id="s-crescimento" type="range" min="0" max="40" step="1" value="15" class="mt-3 w-full" style="accent-color:#fcd34d" />
          </div>
          <div>
            <div class="flex items-center justify-between gap-3">
              <label for="s-margem" class="text-sm font-semibold text-white/90">Margem bruta do produto</label>
              <span id="o-margem" class="rounded-full bg-white/10 px-3 py-1 text-sm font-bold text-amber-300">70%</span>
            </div>
            <input id="s-margem" type="range" min="20" max="90" step="1" value="70" class="mt-3 w-full" style="accent-color:#fcd34d" />
          </div>
          <div class="sm:col-span-2">
            <div class="flex items-center justify-between gap-3">
              <label for="s-outros" class="text-sm font-semibold text-white/90">Outros custos (impostos, logística, marketing, sistema)</label>
              <span id="o-outros" class="rounded-full bg-white/10 px-3 py-1 text-sm font-bold text-amber-300">15%</span>
            </div>
            <input id="s-outros" type="range" min="5" max="40" step="1" value="15" class="mt-3 w-full" style="accent-color:#fcd34d" />
          </div>
        </div>

        <p class="nao-imprime mt-6 rounded-xl border-l-4 border-amber-300/60 bg-amber-400/10 px-4 py-3 text-xs leading-relaxed text-white/80">
          O ticket é o valor que o consultor <strong>paga</strong>, já com o desconto de revenda embutido.
          A margem de revenda não entra na conta: é desconto no preço, não bônus pago pela empresa.
        </p>

        <div class="nao-imprime mt-6 flex justify-end">
          <button type="button" data-vai="2" class="proximo rounded-full bg-amber-400 px-6 py-3 text-sm font-bold uppercase tracking-wide text-brand hover:bg-amber-300">Próximo: bônus do plano</button>
        </div>
      </div>
    </section>

    <section id="passo-2" class="passo">
      <div class="cartao-impressao rounded-[24px] border border-white/20 bg-white/5 p-5 sm:p-7">
        <h2 class="font-[var(--font-heading)] text-xl font-bold">O que o plano paga</h2>
        <p class="mt-1 text-sm text-white/70">Indicação, unilevel e carreira — os três bônus que somam no payout.</p>

        <div class="mt-6 grid gap-6 sm:grid-cols-2">
          <div>
            <label for="s-indicacao" class="text-sm font-semibold text-white/90">Bônus de indicação</label>
            <div class="mt-2 flex items-center gap-2">
              <input id="s-indicacao" type="number" min="0" max="60" step="1" value="20" class="w-full rounded-xl border border-white/25 bg-white/10 px-3 py-2 text-white" />
              <span class="text-sm text-white/60">%</span>
            </div>
            <p class="mt-1 text-xs text-white/55">sobre a primeira compra do indicado</p>
          </div>
          <div>
            <div class="flex items-center justify-between gap-3">
              <label for="s-novos" class="text-sm font-semibold text-white/90">Faturamento vindo de primeira compra</label>
              <span id="o-novos" class="rounded-full bg-white/10 px-3 py-1 text-sm font-bold text-amber-300">20%</span>
            </div>
            <input id="s-novos" type="range" min="0" max="60" step="1" value="20" class="mt-3 w-full" style="accent-color:#fcd34d" />
            <p class="mt-1 text-xs text-white/55">é o que define o custo real da indicação</p>
          </div>
        </div>

        <div class="mt-7">
          <div class="flex items-center justify-between gap-3">
            <span class="text-sm font-semibold text-white/90">Unilevel — percentual por nível</span>
            <span id="o-unilevel" class="rounded-full bg-white/10 px-3 py-1 text-sm font-bold text-amber-300">21%</span>
          </div>
          <div id="niveis" class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-5"></div>
          <div class="nao-imprime mt-3 flex gap-2">
            <button type="button" id="add-nivel" class="rounded-full border border-white/30 px-4 py-1.5 text-xs font-bold uppercase tracking-wide hover:bg-white/10">+ nível</button>
            <button type="button" id="del-nivel" class="rounded-full border border-white/30 px-4 py-1.5 text-xs font-bold uppercase tracking-wide hover:bg-white/10">− nível</button>
          </div>
        </div>

        <div class="mt-7 grid gap-6 sm:grid-cols-2">
          <div>
            <label for="s-titulo" class="text-sm font-semibold text-white/90">Bônus de carreira / título</label>
            <div class="mt-2 flex items-center gap-2">
              <input id="s-titulo" type="number" min="0" max="30" step="1" value="5" class="w-full rounded-xl border border-white/25 bg-white/10 px-3 py-2 text-white" />
              <span class="text-sm text-white/60">%</span>
            </div>
            <p class="mt-1 text-xs text-white/55">pago aos qualificados por graduação</p>
          </div>
          <div>
            <div class="flex items-center justify-between gap-3">
              <label for="s-breakage" class="text-sm font-semibold text-white/90">Quanto do plano é efetivamente pago</label>
              <span id="o-breakage" class="rounded-full bg-white/10 px-3 py-1 text-sm font-bold text-amber-300">70%</span>
            </div>
            <input id="s-breakage" type="range" min="40" max="100" step="5" value="70" class="mt-3 w-full" style="accent-color:#fcd34d" />
            <p class="mt-1 text-xs text-white/55">quem não qualifica não recebe — o mercado fica entre 60% e 75%</p>
          </div>
        </div>

        <p class="nao-imprime mt-6 rounded-xl border-l-4 border-amber-300/60 bg-amber-400/10 px-4 py-3 text-xs leading-relaxed text-white/80">
          O desconto por não-qualificação vale para unilevel e carreira. A indicação entra cheia:
          sempre existe um patrocinador para receber.
        </p>

        <div class="nao-imprime mt-6 flex justify-between gap-3">
          <button type="button" data-vai="1" class="proximo rounded-full border border-white/40 px-6 py-3 text-sm font-bold uppercase tracking-wide hover:bg-white/10">Voltar</button>
          <button type="button" data-vai="3" class="proximo rounded-full bg-amber-400 px-6 py-3 text-sm font-bold uppercase tracking-wide text-brand hover:bg-amber-300">Ver o resultado</button>
        </div>
      </div>
    </section>

    <section id="passo-3" class="passo">
      <div id="veredito" class="cartao-impressao rounded-[24px] border p-5 sm:p-7">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Veredito</p>
        <p id="v-titulo" class="mt-1 font-[var(--font-heading)] text-3xl font-bold">—</p>
        <p id="v-texto" class="mt-3 text-base leading-relaxed text-white/85"></p>
      </div>

      <div class="mt-4 grid gap-4 sm:grid-cols-3">
        <div class="cartao-impressao rounded-2xl border border-white/20 bg-white/5 px-5 py-4">
          <p class="text-xs font-semibold uppercase tracking-[0.16em] text-white/60">Payout nominal</p>
          <p id="r-nominal" class="mt-1 font-[var(--font-heading)] text-2xl font-bold">30%</p>
          <p class="mt-1 text-xs text-white/60">indicação + unilevel + carreira</p>
        </div>
        <div class="cartao-impressao rounded-2xl border border-white/20 bg-white/5 px-5 py-4">
          <p class="text-xs font-semibold uppercase tracking-[0.16em] text-white/60">Payout real</p>
          <p id="r-real" class="mt-1 font-[var(--font-heading)] text-2xl font-bold">22,2%</p>
          <p id="r-real-rs" class="mt-1 text-xs text-white/60">por pedido</p>
        </div>
        <div class="cartao-impressao rounded-2xl border border-amber-300/40 bg-white/[0.07] px-5 py-4">
          <p class="text-xs font-semibold uppercase tracking-[0.16em] text-white/60">Sobra para a empresa</p>
          <p id="r-sobra" class="mt-1 font-[var(--font-heading)] text-2xl font-bold text-amber-300">32,8%</p>
          <p id="r-sobra-rs" class="mt-1 text-xs text-white/60">por pedido</p>
        </div>
      </div>

      <div class="cartao-impressao mt-4 rounded-2xl border border-white/20 bg-white/5 p-5">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-white/60">Ponto de ruptura</p>
        <p id="r-ruptura" class="mt-2 text-sm leading-relaxed text-white/85"></p>
      </div>

      <div id="projecao" class="cartao-impressao mt-4 scroll-mt-24 rounded-2xl border border-white/20 bg-white/5 p-5 sm:p-6">
        <h2 class="font-[var(--font-heading)] text-xl font-bold">Seis meses com esse plano</h2>
        <p class="mt-1 text-sm text-white/70">Percentual não paga conta — reais pagam.</p>
        <div class="mt-5 overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-white/15 text-left text-xs uppercase tracking-[0.1em] text-white/60">
                <th class="py-3 pr-3">Mês</th>
                <th class="py-3 px-3 text-right">Consultores</th>
                <th class="py-3 px-3 text-right">Faturamento</th>
                <th class="py-3 px-3 text-right">Bônus pagos</th>
                <th class="py-3 px-3 text-right">Sobra</th>
                <th class="py-3 pl-3 text-right">Caixa acumulado</th>
              </tr>
            </thead>
            <tbody id="proj-corpo"></tbody>
            <tfoot id="proj-total" class="border-t border-white/25 font-bold text-amber-300"></tfoot>
          </table>
        </div>
        <p id="proj-nota" class="mt-4 text-sm leading-relaxed text-white/80"></p>
      </div>

      <div class="nao-imprime mt-5 grid gap-3 sm:grid-cols-2">
        <a href="<?= htmlspecialchars($demoHref, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"
           class="inline-flex items-center justify-center rounded-full bg-amber-400 px-6 py-3.5 text-center text-sm font-bold uppercase tracking-wide text-brand transition hover:-translate-y-0.5 hover:bg-amber-300">
          Ver esse plano rodando no sistema
        </a>
        <a href="<?= htmlspecialchars($whatsappHref, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center justify-center rounded-full border border-white/50 px-6 py-3.5 text-center text-sm font-bold uppercase tracking-wide hover:bg-white/10">
          Revisar meu plano com um especialista
        </a>
      </div>
    </section>

    <section class="nao-imprime border-t border-white/15 py-10">
      <h2 class="font-[var(--font-heading)] text-2xl font-bold">Como ler esses números</h2>
      <div class="mt-2 h-1 w-[72px] rounded-full bg-amber-300"></div>
      <div class="mt-6 grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-semibold text-amber-300">Payout nominal x real</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/85">
            O nominal é o que o plano promete no papel. O real é o que sai do caixa depois de descontar
            quem não qualificou. Planejar pelo nominal é seguro; pelo real é otimista.
          </p>
        </div>
        <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-semibold text-amber-300">Indicação custa mais em crescimento</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/85">
            Ela incide sobre primeira compra. Quanto mais a operação cresce, maior a fatia do faturamento
            que ela consome — plano que fecha parado pode sangrar justo no lançamento.
          </p>
        </div>
        <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-semibold text-amber-300">Profundidade custa caro</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/85">
            Cada nível novo de unilevel parece barato isolado, mas soma direto no payout e é pago para
            sempre. Cinco níveis bem calibrados rendem mais que dez mal distribuídos.
          </p>
        </div>
      </div>
      <p class="mt-6 text-xs text-white/55">
        Projeção ilustrativa a partir dos valores informados — não é promessa de resultado nem
        recomendação contábil. O cálculo roda no seu navegador; nada é enviado nem armazenado.
      </p>
    </section>
  </main>

  <div class="nao-imprime fixed inset-x-0 bottom-0 z-50 border-t border-white/20 bg-brand-dark/95 backdrop-blur">
    <div class="mx-auto flex max-w-[1000px] flex-wrap items-center gap-x-6 gap-y-2 px-4 py-3 sm:px-6">
      <div class="flex items-baseline gap-2">
        <span class="text-[11px] uppercase tracking-[0.14em] text-white/55">Payout real</span>
        <span id="b-payout" class="font-[var(--font-heading)] text-lg font-bold">22,2%</span>
      </div>
      <div class="flex items-baseline gap-2">
        <span class="text-[11px] uppercase tracking-[0.14em] text-white/55">Sobra</span>
        <span id="b-sobra" class="font-[var(--font-heading)] text-lg font-bold text-amber-300">32,8%</span>
      </div>
      <div class="flex items-baseline gap-2">
        <span class="text-[11px] uppercase tracking-[0.14em] text-white/55">6 meses</span>
        <a href="#projecao" id="b-acumulado" class="font-[var(--font-heading)] text-lg font-bold text-amber-300 underline decoration-amber-300/40 underline-offset-4 hover:text-white">R$ 86.100</a>
      </div>
      <span id="b-chip" class="rounded-full px-3 py-1 text-xs font-bold">—</span>
      <div class="ml-auto">
        <button type="button" id="btn-pdf" class="rounded-full border border-white/45 px-4 py-2 text-xs font-bold uppercase tracking-wide hover:bg-white/10">Exportar PDF</button>
      </div>
    </div>
  </div>

<script>
(function () {
  var brl = new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL", maximumFractionDigits: 0 });
  var n1 = new Intl.NumberFormat("pt-BR", { minimumFractionDigits: 1, maximumFractionDigits: 1 });
  function $(id) { return document.getElementById(id); }

  var niveis = [10, 5, 3, 2, 1];
  var usou = false;
  var abaAtual = 1;

  function abrir(n) {
    abaAtual = n;
    [1, 2, 3].forEach(function (i) { $("passo-" + i).style.display = i === n ? "block" : "none"; });
    document.querySelectorAll(".aba").forEach(function (b) {
      var ativo = +b.dataset.aba === n;
      b.className = "aba flex-shrink-0 rounded-full px-4 py-2 text-sm font-bold transition " +
        (ativo ? "bg-amber-400 text-brand" : "border border-white/25 text-white/80 hover:bg-white/10");
    });
  }
  document.querySelectorAll(".aba").forEach(function (b) {
    b.addEventListener("click", function () { abrir(+b.dataset.aba); });
  });
  document.querySelectorAll(".proximo").forEach(function (b) {
    b.addEventListener("click", function () {
      abrir(+b.dataset.vai);
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  });

  function desenhaNiveis() {
    var wrap = $("niveis");
    wrap.innerHTML = "";
    niveis.forEach(function (v, i) {
      var l = document.createElement("label");
      l.className = "block";
      l.innerHTML = '<span class="text-xs text-white/60">Nível ' + (i + 1) + '</span>' +
        '<input type="number" min="0" max="30" step="0.5" value="' + v + '"' +
        ' class="mt-1 w-full rounded-xl border border-white/25 bg-white/10 px-3 py-2 text-white" />';
      l.querySelector("input").addEventListener("input", function () {
        niveis[i] = parseFloat(this.value) || 0;
        calc();
      });
      wrap.appendChild(l);
    });
  }

  function projetar(consultores, ticket, cresc, payoutReal, outros, margem) {
    var corpo = $("proj-corpo"), rodape = $("proj-total");
    corpo.innerHTML = ""; rodape.innerHTML = "";
    var acum = 0, totFat = 0, totBonus = 0, totSobra = 0, ultimoFat = 0;

    for (var i = 0; i < 6; i++) {
      var c = Math.round(consultores * Math.pow(1 + cresc / 100, i));
      var fat = c * ticket;
      var bonus = fat * payoutReal / 100;
      var sobra = fat * (margem - payoutReal - outros) / 100;
      acum += sobra; totFat += fat; totBonus += bonus; totSobra += sobra; ultimoFat = fat;

      var tr = document.createElement("tr");
      tr.className = "border-b border-white/10";
      tr.innerHTML =
        '<td class="py-3 pr-3 font-semibold">Mês ' + (i + 1) + '</td>' +
        '<td class="py-3 px-3 text-right text-white/80">' + c.toLocaleString("pt-BR") + '</td>' +
        '<td class="py-3 px-3 text-right">' + brl.format(fat) + '</td>' +
        '<td class="py-3 px-3 text-right text-white/80">' + brl.format(bonus) + '</td>' +
        '<td class="py-3 px-3 text-right font-semibold ' + (sobra < 0 ? "text-red-300" : "text-white") + '">' + brl.format(sobra) + '</td>' +
        '<td class="py-3 pl-3 text-right font-bold ' + (acum < 0 ? "text-red-300" : "text-amber-300") + '">' + brl.format(acum) + '</td>';
      corpo.appendChild(tr);
    }

    rodape.innerHTML =
      '<tr><td class="py-3 pr-3">Total</td><td></td>' +
      '<td class="py-3 px-3 text-right">' + brl.format(totFat) + '</td>' +
      '<td class="py-3 px-3 text-right">' + brl.format(totBonus) + '</td>' +
      '<td class="py-3 px-3 text-right">' + brl.format(totSobra) + '</td>' +
      '<td class="py-3 pl-3 text-right">' + brl.format(acum) + '</td></tr>';

    var nota = $("proj-nota");
    if (acum < 0) {
      nota.className = "mt-4 text-sm leading-relaxed text-red-300";
      nota.textContent = "Em seis meses a operação acumula " + brl.format(Math.abs(acum)) +
        " de prejuízo, faturando " + brl.format(totFat) + ". Crescer piora: cada consultor novo aumenta " +
        "o rombo, porque o plano paga mais do que a margem comporta.";
    } else {
      nota.className = "mt-4 text-sm leading-relaxed text-white/80";
      nota.textContent = "Em seis meses sobram " + brl.format(acum) + " sobre " + brl.format(totFat) +
        " de faturamento, com " + brl.format(totBonus) + " distribuídos em bônus. O sexto mês sozinho " +
        "fatura " + brl.format(ultimoFat) + " — é o tamanho que o plano precisa aguentar.";
    }
    return acum;
  }

  function calc() {
    var consultores = +$("s-consultores").value, ticket = +$("s-ticket").value;
    var cresc = +$("s-crescimento").value, margem = +$("s-margem").value;
    var outros = +$("s-outros").value, novos = +$("s-novos").value;
    var pago = +$("s-breakage").value;
    var indicacao = parseFloat($("s-indicacao").value) || 0;
    var titulo = parseFloat($("s-titulo").value) || 0;
    var uni = niveis.reduce(function (a, b) { return a + b; }, 0);

    $("o-consultores").textContent = consultores.toLocaleString("pt-BR");
    $("o-ticket").textContent = brl.format(ticket);
    $("o-crescimento").textContent = cresc + "%";
    $("o-margem").textContent = margem + "%";
    $("o-outros").textContent = outros + "%";
    $("o-novos").textContent = novos + "%";
    $("o-breakage").textContent = pago + "%";
    $("o-unilevel").textContent = n1.format(uni) + "%";

    // Breakage so em unilevel e carreira: dependem de qualificacao. Indicacao
    // entra cheia — sempre ha um patrocinador. O que limita o custo dela e a
    // fatia do faturamento que e primeira compra.
    var nominalRede = uni + titulo;
    var custoIndicacao = indicacao * novos / 100;
    var nominal = nominalRede + custoIndicacao;
    var real = (nominalRede * pago / 100) + custoIndicacao;

    // O ticket ja e o preco que o consultor paga: a margem informada e sobre ele.
    var sobra = margem - real - outros;

    $("r-nominal").textContent = n1.format(nominal) + "%";
    $("r-real").textContent = n1.format(real) + "%";
    $("r-real-rs").textContent = brl.format(ticket * real / 100) + " por pedido";
    $("r-sobra").textContent = n1.format(sobra) + "%";
    $("r-sobra-rs").textContent = brl.format(ticket * sobra / 100) + " por pedido";
    $("b-payout").textContent = n1.format(real) + "%";
    $("b-sobra").textContent = n1.format(sobra) + "%";

    var cor, tit, msg, chip;
    if (sobra >= 15) {
      cor = "border-emerald-300/50 bg-emerald-400/10"; tit = "O plano fecha"; chip = "bg-emerald-400 text-brand";
      msg = "Sobra " + n1.format(sobra) + "% depois de pagar os bônus e os custos. É folga suficiente para " +
            "absorver inadimplência, devolução e crescimento sem apertar o caixa.";
    } else if (sobra >= 5) {
      cor = "border-amber-300/60 bg-amber-400/10"; tit = "Fecha, mas no limite"; chip = "bg-amber-400 text-brand";
      msg = "Sobram apenas " + n1.format(sobra) + "%. Funciona enquanto tudo corre bem — uma devolução acima " +
            "do previsto ou um mês de queda já come a margem. Vale reduzir profundidade ou o bônus de indicação.";
    } else if (sobra >= 0) {
      cor = "border-orange-400/60 bg-orange-500/10"; tit = "Não se sustenta"; chip = "bg-orange-400 text-brand";
      msg = "Sobram " + n1.format(sobra) + "%, o que na prática é zero. O plano só se paga se a rede crescer " +
            "todo mês, e nenhuma cresce para sempre.";
    } else {
      cor = "border-red-400/60 bg-red-500/10"; tit = "O plano quebra"; chip = "bg-red-400 text-white";
      msg = "O plano promete " + n1.format(Math.abs(sobra)) + "% a mais do que a margem comporta. Cada pedido " +
            "vendido aumenta o prejuízo — é o cenário em que a empresa fecha vendendo bem.";
    }
    $("veredito").className = "cartao-impressao rounded-[24px] border p-5 sm:p-7 " + cor;
    $("v-titulo").textContent = tit;
    $("v-texto").textContent = msg;
    $("b-chip").className = "rounded-full px-3 py-1 text-xs font-bold " + chip;
    $("b-chip").textContent = tit;

    var teto = margem - outros;
    $("r-ruptura").textContent = "Com essa margem, o payout real não pode passar de " +
      n1.format(Math.max(0, teto)) + "%. O bônus de indicação sozinho já consome " +
      n1.format(custoIndicacao) + "% (" + indicacao + "% sobre os " + novos +
      "% do faturamento que são primeira compra), sobrando " +
      n1.format(Math.max(0, teto - custoIndicacao)) + "% para unilevel e carreira.";

    var acum = projetar(consultores, ticket, cresc, real, outros, margem);
    $("b-acumulado").textContent = brl.format(acum);
    $("b-acumulado").className = "font-[var(--font-heading)] text-lg font-bold underline " +
      "decoration-amber-300/40 underline-offset-4 hover:text-white " + (acum < 0 ? "text-red-300" : "text-amber-300");

    if (!usou && window.gtag) { usou = true; gtag("event", "simulador_plano_uso"); }
  }

  ["s-consultores", "s-ticket", "s-crescimento", "s-margem", "s-outros",
   "s-novos", "s-breakage", "s-indicacao", "s-titulo"]
    .forEach(function (id) { $(id).addEventListener("input", calc); });

  $("add-nivel").addEventListener("click", function () {
    if (niveis.length < 10) { niveis.push(1); desenhaNiveis(); calc(); }
  });
  $("del-nivel").addEventListener("click", function () {
    if (niveis.length > 1) { niveis.pop(); desenhaNiveis(); calc(); }
  });

  // O PDF precisa dos tres passos abertos, senao imprime so o que esta visivel.
  $("btn-pdf").addEventListener("click", function () {
    $("pdf-data").textContent = "Gerado em " + new Date().toLocaleDateString("pt-BR") +
      " · sistemavendadireta.com.br/simulador";
    [1, 2, 3].forEach(function (i) { $("passo-" + i).style.display = "block"; });
    if (window.gtag) gtag("event", "simulador_plano_pdf");
    window.print();
    setTimeout(function () { abrir(abaAtual); }, 500);
  });

  // A ancora salta pra tabela sem depender do passo em que a pessoa esta.
  $("b-acumulado").addEventListener("click", function (e) {
    e.preventDefault();
    abrir(3);
    setTimeout(function () { $("projecao").scrollIntoView({ behavior: "smooth", block: "start" }); }, 100);
  });

  desenhaNiveis();
  abrir(1);
  calc();
})();
</script>
</body>
</html>
