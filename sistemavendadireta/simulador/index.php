<?php
declare(strict_types=1);

/*
[Modulo Simulador de Plano MMN — SVD]
@Author: André Gomes ( @acidcode )
@since 2026-08-18

Checador de viabilidade de plano de marketing multinivel.

POR QUE ESTA FERRAMENTA: o erro que quebra plano de MMN quase sempre e o mesmo —
cada bonus e desenhado isolado, ninguem soma o total, e o plano so fecha se a
rede crescer pra sempre. Aqui a pessoa informa margem e percentuais e recebe a
resposta incomoda: fecha ou nao fecha, e onde quebra.

E argumento de venda embutido: quem descobre que o proprio plano nao fecha tem um
problema, e a parametrizacao que resolve e justamente o que vendemos. Diferente
do concorrente, nao exige cadastro — ver antes de falar com alguem converte mais.

Calculo roda todo no browser: sem servidor, sem lead capturado a forca, sem
promessa de resultado. Indexavel de proposito (nao leva noindex): e isca de
conteudo pra busca organica.
*/

require_once __DIR__ . '/../inc/promo.php';

$seoBase = 'https://www.sistemavendadireta.com.br';
$seoUrl = $seoBase . '/simulador/';
$seoTitle = 'Simulador de Plano de Marketing Multinível | Veja se o seu plano fecha';
$seoDescription = 'Calcule o payout real do seu plano de MMN: bônus de indicação, unilevel por nível '
    . 'e carreira. Descubra se a margem sustenta o que o plano promete. Sem cadastro.';

// Faixas de mensalidade — mesma tabela da /oferta/, usada no bloco de custo.
$monthlyTiers = [
    [50000, 500], [100000, 1000], [200000, 1500], [350000, 3000],
    [500000, 4500], [750000, 7000], [1000000, 9000],
];

$whatsappHref = 'https://wa.me/5511994566726?text=' . rawurlencode(
    'Ola! Usei o simulador de plano no site e quero conversar sobre a parametrizacao.');
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
  <?php include __DIR__ . '/../inc/analytics.php'; ?>
</head>
<body class="bg-brand text-white antialiased font-[var(--font-body)] site-optimized">

  <header class="border-b border-white/10 bg-brand/95">
    <div class="mx-auto flex max-w-[1140px] items-center justify-between gap-4 px-4 py-3 sm:px-6">
      <a href="../"><img decoding="async" src="../imagens/Logo-Branco-1.webp" alt="Sistema Venda Direta" class="h-auto w-[150px] sm:w-[200px]" width="1000" height="300" loading="eager" /></a>
      <div class="flex items-center gap-5">
        <a href="<?= htmlspecialchars(DEMO_URL, ENT_QUOTES, 'UTF-8') ?>?utm_source=site&utm_medium=simulador&utm_campaign=demo" target="_blank" rel="noopener" class="text-sm font-semibold text-amber-300 hover:text-amber-200">Ver demonstração</a>
        <a href="../cases/" class="hidden text-sm font-semibold text-white/85 hover:text-white sm:inline">Cases</a>
        <a href="../oferta/?utm_source=site&amp;utm_medium=simulador&amp;utm_campaign=promo-10-anos" class="rounded-full bg-amber-400 px-4 py-2 text-sm font-bold text-brand hover:bg-amber-300">Ver a promoção</a>
      </div>
    </div>
  </header>

  <main class="mx-auto max-w-[1140px] px-4 sm:px-6">

    <section class="py-10 lg:py-12">
      <p class="inline-flex rounded-full border border-amber-300/50 bg-amber-400/15 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-amber-200">
        Ferramenta gratuita · sem cadastro
      </p>
      <h1 class="mt-4 font-[var(--font-heading)] text-3xl font-bold leading-[1.15] sm:text-4xl lg:text-[40px]">
        Seu plano de marketing multinível<br />
        <span class="text-amber-300">fecha a conta?</span>
      </h1>
      <p class="mt-4 max-w-3xl text-base leading-relaxed text-white/90 sm:text-lg">
        A maioria dos planos de MMN quebra pelo mesmo motivo: cada bônus é desenhado separado, ninguém
        soma o total, e o plano só fecha se a rede crescer para sempre. Informe sua margem e os
        percentuais que pretende pagar — a conta aparece na hora.
      </p>
    </section>

    <section class="grid gap-6 pb-6 lg:grid-cols-[1.15fr_1fr]">

      <div class="flex flex-col gap-4">

        <div class="rounded-[24px] border border-white/20 bg-white/5 p-5 sm:p-6">
          <h2 class="font-[var(--font-heading)] text-lg font-bold">1. A economia do produto</h2>
          <p class="mt-1 text-sm text-white/70">É a margem que paga tudo. Sem ela, nenhum bônus se sustenta.</p>

          <div class="mt-5">
            <div class="flex items-center justify-between gap-3">
              <label for="s-consultores" class="text-sm font-semibold text-white/90">Consultores ativos hoje</label>
              <span id="o-consultores" class="rounded-full bg-white/10 px-3 py-1 text-sm font-bold text-amber-300">100</span>
            </div>
            <input id="s-consultores" type="range" min="10" max="3000" step="10" value="100" class="mt-3 w-full" style="accent-color:#fcd34d" />
          </div>

          <div class="mt-5">
            <div class="flex items-center justify-between gap-3">
              <label for="s-crescimento" class="text-sm font-semibold text-white/90">Crescimento mensal da operação</label>
              <span id="o-crescimento" class="rounded-full bg-white/10 px-3 py-1 text-sm font-bold text-amber-300">15%</span>
            </div>
            <input id="s-crescimento" type="range" min="0" max="40" step="1" value="15" class="mt-3 w-full" style="accent-color:#fcd34d" />
          </div>

          <div class="mt-5">
            <div class="flex items-center justify-between gap-3">
              <label for="s-ticket" class="text-sm font-semibold text-white/90">Ticket médio do pedido</label>
              <span id="o-ticket" class="rounded-full bg-white/10 px-3 py-1 text-sm font-bold text-amber-300">R$ 300</span>
            </div>
            <input id="s-ticket" type="range" min="50" max="2000" step="10" value="300" class="mt-3 w-full" style="accent-color:#fcd34d" />
          </div>

          <div class="mt-5">
            <div class="flex items-center justify-between gap-3">
              <label for="s-margem" class="text-sm font-semibold text-white/90">Margem bruta do produto</label>
              <span id="o-margem" class="rounded-full bg-white/10 px-3 py-1 text-sm font-bold text-amber-300">70%</span>
            </div>
            <input id="s-margem" type="range" min="20" max="90" step="1" value="70" class="mt-3 w-full" style="accent-color:#fcd34d" />
            <p class="mt-2 text-xs text-white/60">Quanto sobra do preço de venda depois do custo do produto. Suplemento e cosmético costumam ficar entre 60% e 75%.</p>
          </div>

          <div class="mt-5">
            <div class="flex items-center justify-between gap-3">
              <label for="s-outros" class="text-sm font-semibold text-white/90">Outros custos (impostos, logística, marketing, sistema)</label>
              <span id="o-outros" class="rounded-full bg-white/10 px-3 py-1 text-sm font-bold text-amber-300">15%</span>
            </div>
            <input id="s-outros" type="range" min="5" max="40" step="1" value="15" class="mt-3 w-full" style="accent-color:#fcd34d" />
          </div>
        </div>

        <div class="rounded-[24px] border border-white/20 bg-white/5 p-5 sm:p-6">
          <h2 class="font-[var(--font-heading)] text-lg font-bold">2. O que o plano paga</h2>
          <p class="mt-1 text-sm text-white/70">Percentuais sobre o volume que gera o bônus.</p>

          <div class="mt-5">
            <label class="block sm:max-w-xs">
              <span class="text-sm font-semibold text-white/90">Bônus de indicação</span>
              <span class="mt-1 flex items-center gap-2">
                <input id="s-indicacao" type="number" min="0" max="60" step="1" value="20" class="w-full rounded-xl border border-white/25 bg-white/10 px-3 py-2 text-white" />
                <span class="text-sm text-white/60">%</span>
              </span>
              <span class="mt-1 block text-xs text-white/55">sobre a primeira compra do indicado</span>
            </label>
            <p class="mt-3 text-xs text-white/60">
              A margem de revenda do consultor não entra aqui: ela é desconto no preço, não bônus.
              O ticket abaixo já é o valor que o consultor paga, com o desconto embutido.
            </p>
          </div>

          <div class="mt-6">
            <div class="flex items-center justify-between gap-3">
              <span class="text-sm font-semibold text-white/90">Unilevel — percentual por nível</span>
              <span id="o-unilevel" class="rounded-full bg-white/10 px-3 py-1 text-sm font-bold text-amber-300">21%</span>
            </div>
            <div id="niveis" class="mt-3 grid gap-2 sm:grid-cols-5"></div>
            <div class="mt-3 flex gap-2">
              <button type="button" id="add-nivel" class="rounded-full border border-white/30 px-4 py-1.5 text-xs font-bold uppercase tracking-wide hover:bg-white/10">+ nível</button>
              <button type="button" id="del-nivel" class="rounded-full border border-white/30 px-4 py-1.5 text-xs font-bold uppercase tracking-wide hover:bg-white/10">− nível</button>
            </div>
          </div>

          <div class="mt-6">
            <label class="block sm:max-w-xs">
              <span class="text-sm font-semibold text-white/90">Bônus de carreira / título</span>
              <span class="mt-1 flex items-center gap-2">
                <input id="s-titulo" type="number" min="0" max="30" step="1" value="5" class="w-full rounded-xl border border-white/25 bg-white/10 px-3 py-2 text-white" />
                <span class="text-sm text-white/60">%</span>
              </span>
              <span class="mt-1 block text-xs text-white/55">pago aos qualificados por graduação</span>
            </label>
          </div>

          <div class="mt-6">
            <div class="flex items-center justify-between gap-3">
              <label for="s-novos" class="text-sm font-semibold text-white/90">Parte do faturamento que vem de primeira compra</label>
              <span id="o-novos" class="rounded-full bg-white/10 px-3 py-1 text-sm font-bold text-amber-300">20%</span>
            </div>
            <input id="s-novos" type="range" min="0" max="60" step="1" value="20" class="mt-3 w-full" style="accent-color:#fcd34d" />
            <p class="mt-2 text-xs text-white/60">
              É o que define o custo real do bônus de indicação: ele só incide sobre quem está comprando
              pela primeira vez. Operação madura fica entre 10% e 20%; em lançamento passa de 50%.
            </p>
          </div>

          <div class="mt-6">
            <div class="flex items-center justify-between gap-3">
              <label for="s-breakage" class="text-sm font-semibold text-white/90">Quanto do plano é efetivamente pago</label>
              <span id="o-breakage" class="rounded-full bg-white/10 px-3 py-1 text-sm font-bold text-amber-300">70%</span>
            </div>
            <input id="s-breakage" type="range" min="40" max="100" step="5" value="70" class="mt-3 w-full" style="accent-color:#fcd34d" />
            <p class="mt-2 text-xs text-white/60">
              Nem todo mundo qualifica para todos os bônus. O que não é pago chama-se <em>breakage</em>, e no
              mercado fica entre 60% e 75%. Coloque 100% para ver o pior cenário.
            </p>
          </div>
        </div>
      </div>

      <div class="flex flex-col gap-3">
        <div id="veredito" class="rounded-2xl border p-5">
          <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Veredito</p>
          <p id="v-titulo" class="mt-1 font-[var(--font-heading)] text-2xl font-bold">—</p>
          <p id="v-texto" class="mt-2 text-sm leading-relaxed text-white/85"></p>
        </div>

        <div class="rounded-2xl border border-white/20 bg-white/5 px-5 py-4">
          <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Payout nominal recorrente</p>
          <p id="r-nominal" class="mt-1 font-[var(--font-heading)] text-3xl font-bold text-white">26%</p>
          <p id="r-nominal-nota" class="mt-1 text-xs text-white/60">unilevel + carreira + indicação</p>
        </div>

        <div class="rounded-2xl border border-white/20 bg-white/5 px-5 py-4">
          <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Payout real, com breakage</p>
          <p id="r-real" class="mt-1 font-[var(--font-heading)] text-3xl font-bold text-white">18,2%</p>
          <p id="r-real-rs" class="mt-1 text-xs text-white/60">R$ 55 por pedido de R$ 300</p>
        </div>

        <div class="rounded-2xl border border-amber-300/40 bg-white/[0.07] px-5 py-4">
          <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Sobra para a empresa</p>
          <p id="r-sobra" class="mt-1 font-[var(--font-heading)] text-3xl font-bold text-amber-300">11,8%</p>
          <p id="r-sobra-rs" class="mt-1 text-xs text-white/60">R$ 35 por pedido</p>
        </div>

        <div class="rounded-2xl border border-white/20 bg-white/5 px-5 py-4">
          <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Ponto de ruptura</p>
          <p id="r-ruptura" class="mt-1 text-sm leading-relaxed text-white/85"></p>
        </div>

        <a href="<?= htmlspecialchars(DEMO_URL, ENT_QUOTES, 'UTF-8') ?>?utm_source=site&utm_medium=simulador&utm_campaign=demo" target="_blank" rel="noopener"
           class="inline-flex items-center justify-center rounded-full bg-amber-400 px-6 py-3.5 text-center text-sm font-bold uppercase tracking-wide text-brand transition hover:-translate-y-0.5 hover:bg-amber-300">
          Ver esse plano rodando no sistema
        </a>
        <a href="<?= htmlspecialchars($whatsappHref, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center justify-center rounded-full border border-white/50 px-6 py-3 text-center text-sm font-bold uppercase tracking-wide hover:bg-white/10">
          Revisar meu plano com um especialista
        </a>
      </div>
    </section>

    <section class="border-t border-white/15 py-10">
      <h2 class="font-[var(--font-heading)] text-2xl font-bold sm:text-[28px]">Seis meses com esse plano</h2>
      <div class="mt-2 h-1 w-[72px] rounded-full bg-amber-300"></div>
      <p class="mt-4 max-w-3xl text-base leading-relaxed text-white/90">
        Percentual não paga conta — reais pagam. Abaixo, a mesma configuração projetada mês a mês com o
        crescimento que você definiu. É aqui que dá pra ver se a operação acumula caixa ou dívida.
      </p>

      <div class="mt-6 overflow-x-auto rounded-2xl border border-white/20 bg-white/5">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-white/15 text-left text-xs uppercase tracking-[0.12em] text-white/60">
              <th class="px-4 py-3">Mês</th>
              <th class="px-4 py-3 text-right">Consultores</th>
              <th class="px-4 py-3 text-right">Faturamento</th>
              <th class="px-4 py-3 text-right">Pago em bônus</th>
              <th class="px-4 py-3 text-right">Outros custos</th>
              <th class="px-4 py-3 text-right">Sobra no mês</th>
              <th class="px-4 py-3 text-right">Caixa acumulado</th>
            </tr>
          </thead>
          <tbody id="proj-corpo"></tbody>
          <tfoot id="proj-total" class="border-t border-white/25 font-bold text-amber-300"></tfoot>
        </table>
      </div>
      <p id="proj-nota" class="mt-3 text-sm leading-relaxed text-white/80"></p>
    </section>

    <section class="border-t border-white/15 py-10">
      <h2 class="font-[var(--font-heading)] text-2xl font-bold sm:text-[28px]">Como ler esses números</h2>
      <div class="mt-2 h-1 w-[72px] rounded-full bg-amber-300"></div>
      <div class="mt-6 grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-semibold text-amber-300">Payout nominal x real</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/85">
            O nominal é o que o plano promete no papel. O real é o que sai do caixa, depois que se
            desconta quem não qualificou. Planejar pelo nominal é seguro; planejar pelo real é otimista.
          </p>
        </div>
        <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-semibold text-amber-300">Indicação custa mais em crescimento</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/85">
            O bônus de indicação incide sobre primeira compra, então quanto mais a operação cresce, maior
            a fatia do faturamento que ele consome. Plano que fecha parado pode sangrar justo no lançamento.
          </p>
        </div>
        <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
          <h3 class="font-semibold text-amber-300">Profundidade custa caro</h3>
          <p class="mt-2 text-sm leading-relaxed text-white/85">
            Cada nível novo de unilevel parece barato isolado, mas soma direto no payout e é pago para
            sempre. Cinco níveis bem calibrados costumam render mais que dez mal distribuídos.
          </p>
        </div>
      </div>
      <p class="mt-6 text-xs text-white/55">
        Projeção ilustrativa a partir dos valores que você informou — não é promessa de resultado nem
        recomendação contábil. O cálculo roda no seu navegador; nada é enviado nem armazenado.
      </p>
    </section>

  </main>

  <footer class="border-t border-white/15 bg-brand-dark/40">
    <div class="mx-auto flex max-w-[1140px] flex-col gap-3 px-4 py-8 sm:flex-row sm:items-center sm:justify-between sm:px-6">
      <p class="text-sm text-white/70">Sistema Venda Direta · 10 anos parametrizando plano de MMN que fecha a conta.</p>
      <div class="flex gap-4 text-sm font-semibold">
        <a href="../" class="text-white/85 hover:text-white">Site</a>
        <a href="../cases/" class="text-white/85 hover:text-white">Cases</a>
        <a href="../oferta/" class="text-amber-300 hover:text-amber-200">Promoção 10 Anos</a>
      </div>
    </div>
  </footer>

<script>
(function () {
  var brl = new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL", maximumFractionDigits: 0 });
  var n1 = new Intl.NumberFormat("pt-BR", { minimumFractionDigits: 1, maximumFractionDigits: 1 });
  function $(id) { return document.getElementById(id); }

  // Unilevel: percentual por nivel. Comeca com a distribuicao que mais aparece
  // em plano saudavel — peso no primeiro nivel, caindo rapido.
  var niveis = [10, 5, 3, 2, 1];
  var usou = false;

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


  /**
   * Seis meses mes a mes. Percentual nao paga conta — reais pagam, e o
   * acumulado mostra se a operacao junta caixa ou divida.
   *
   * O crescimento entra nos consultores; o faturamento acompanha. Payout e
   * custos sao percentuais, entao escalam junto — o que muda com o tamanho e o
   * VALOR absoluto em risco. Plano com 2% de sobra em R$ 30 mil da R$ 600; nos
   * R$ 60 mil do sexto mes, da R$ 1.200 e a mesma fragilidade custa o dobro.
   */
  function projetar(consultores, ticket, cresc, payoutReal, outros, margem) {
    var corpo = $("proj-corpo"), rodape = $("proj-total");
    corpo.innerHTML = ""; rodape.innerHTML = "";
    var acum = 0, totFat = 0, totBonus = 0, totSobra = 0;
    var meses = ["Mês 1", "Mês 2", "Mês 3", "Mês 4", "Mês 5", "Mês 6"];

    meses.forEach(function (nome, i) {
      var c = Math.round(consultores * Math.pow(1 + cresc / 100, i));
      var fat = c * ticket;
      var bonus = fat * payoutReal / 100;
      var custo = fat * outros / 100;
      var sobra = fat * (margem - payoutReal - outros) / 100;
      acum += sobra; totFat += fat; totBonus += bonus; totSobra += sobra;

      var tr = document.createElement("tr");
      tr.className = "border-b border-white/10";
      tr.innerHTML =
        '<td class="px-4 py-3 font-semibold">' + nome + '</td>' +
        '<td class="px-4 py-3 text-right text-white/80">' + c.toLocaleString("pt-BR") + '</td>' +
        '<td class="px-4 py-3 text-right">' + brl.format(fat) + '</td>' +
        '<td class="px-4 py-3 text-right text-white/80">' + brl.format(bonus) + '</td>' +
        '<td class="px-4 py-3 text-right text-white/60">' + brl.format(custo) + '</td>' +
        '<td class="px-4 py-3 text-right font-semibold ' + (sobra < 0 ? "text-red-300" : "text-white") + '">' + brl.format(sobra) + '</td>' +
        '<td class="px-4 py-3 text-right font-bold ' + (acum < 0 ? "text-red-300" : "text-amber-300") + '">' + brl.format(acum) + '</td>';
      corpo.appendChild(tr);
    });

    rodape.innerHTML =
      '<tr><td class="px-4 py-3">Total 6 meses</td><td></td>' +
      '<td class="px-4 py-3 text-right">' + brl.format(totFat) + '</td>' +
      '<td class="px-4 py-3 text-right">' + brl.format(totBonus) + '</td><td></td>' +
      '<td class="px-4 py-3 text-right">' + brl.format(totSobra) + '</td>' +
      '<td class="px-4 py-3 text-right">' + brl.format(acum) + '</td></tr>';

    var nota = $("proj-nota");
    if (acum < 0) {
      nota.className = "mt-3 text-sm leading-relaxed text-red-300";
      nota.textContent = "Em seis meses a operação acumula " + brl.format(Math.abs(acum)) +
        " de prejuízo, faturando " + brl.format(totFat) + ". Crescer piora: cada consultor novo " +
        "aumenta o rombo, porque o plano paga mais do que a margem comporta.";
    } else {
      nota.className = "mt-3 text-sm leading-relaxed text-white/80";
      nota.textContent = "Em seis meses sobram " + brl.format(acum) + " sobre " + brl.format(totFat) +
        " de faturamento, com " + brl.format(totBonus) + " distribuídos em bônus. " +
        "O sexto mês sozinho vale " + brl.format(totFat > 0 ? (consultores * Math.pow(1 + cresc / 100, 5) * ticket) : 0) +
        " — é o tamanho que o plano precisa aguentar.";
    }
  }

  function calc() {
    var ticket = +$("s-ticket").value;
    var margem = +$("s-margem").value;
    var outros = +$("s-outros").value;
    var indicacao = parseFloat($("s-indicacao").value) || 0;
    var titulo = parseFloat($("s-titulo").value) || 0;
    var pago = +$("s-breakage").value;
    var novos = +$("s-novos").value;
    var consultores = +$("s-consultores").value;
    var cresc = +$("s-crescimento").value;

    var uni = niveis.reduce(function (a, b) { return a + b; }, 0);

    $("o-ticket").textContent = brl.format(ticket);
    $("o-margem").textContent = margem + "%";
    $("o-outros").textContent = outros + "%";
    $("o-unilevel").textContent = n1.format(uni) + "%";
    $("o-breakage").textContent = pago + "%";
    $("o-novos").textContent = novos + "%";
    $("o-consultores").textContent = consultores.toLocaleString("pt-BR");
    $("o-crescimento").textContent = cresc + "%";

    // Rede (unilevel + carreira) sofre breakage: depende de qualificacao.
    // Indicacao nao sofre — sempre existe um patrocinador pra receber. O que
    // limita o custo dela e a fatia do faturamento que e primeira compra.
    var nominalRede = uni + titulo;
    var custoIndicacao = indicacao * novos / 100;
    var nominal = nominalRede + custoIndicacao;
    var real = (nominalRede * pago / 100) + custoIndicacao;

    // O ticket ja e o preco que o consultor paga (com o desconto de revenda
    // embutido), entao a margem informada e sobre esse valor. Descontar a revenda
    // de novo seria contar duas vezes.
    var sobra = margem - real - outros;

    $("r-nominal").textContent = n1.format(nominal) + "%";
    $("r-real").textContent = n1.format(real) + "%";
    $("r-real-rs").textContent = brl.format(ticket * real / 100) + " por pedido de " + brl.format(ticket);
    $("r-sobra").textContent = n1.format(sobra) + "%";
    $("r-sobra-rs").textContent = brl.format(ticket * sobra / 100) + " por pedido";

    var box = $("veredito"), t = $("v-titulo"), txt = $("v-texto");
    var cor, titulo_, msg;
    if (sobra >= 15) {
      cor = "border-emerald-300/50 bg-emerald-400/10"; titulo_ = "O plano fecha";
      msg = "Sobra " + n1.format(sobra) + "% depois de pagar os bônus e os custos. É folga suficiente para " +
            "absorver inadimplência, devolução e crescimento sem apertar o caixa.";
    } else if (sobra >= 5) {
      cor = "border-amber-300/60 bg-amber-400/10"; titulo_ = "Fecha, mas no limite";
      msg = "Sobram apenas " + n1.format(sobra) + "%. Funciona enquanto tudo corre bem — uma devolução acima do " +
            "previsto ou um mês de queda já come a margem. Vale reduzir profundidade ou o bônus de indicação.";
    } else if (sobra >= 0) {
      cor = "border-orange-400/60 bg-orange-500/10"; titulo_ = "Não se sustenta";
      msg = "Sobram " + n1.format(sobra) + "%, o que na prática é zero. O plano só se paga se a rede crescer todo " +
            "mês, e nenhuma cresce para sempre.";
    } else {
      cor = "border-red-400/60 bg-red-500/10"; titulo_ = "O plano quebra";
      msg = "O plano promete " + n1.format(Math.abs(sobra)) + "% a mais do que a margem comporta. Cada pedido " +
            "vendido aumenta o prejuízo — é o cenário em que a empresa fecha vendendo bem.";
    }
    box.className = "rounded-2xl border p-5 " + cor;
    t.textContent = titulo_;
    txt.textContent = msg;

    // Ponto de ruptura: quanto de payout real a margem ainda aguenta
    var teto = margem - outros;
    $("r-ruptura").textContent = "Com essa margem, o payout real não pode passar de " +
      n1.format(Math.max(0, teto)) + "%. O bônus de indicação sozinho já consome " +
      n1.format(custoIndicacao) + "% (" + indicacao + "% sobre os " + novos +
      "% do faturamento que são primeira compra), sobrando " +
      n1.format(Math.max(0, teto - custoIndicacao)) + "% para unilevel e carreira.";

    projetar(consultores, ticket, cresc, real, outros, margem);

    if (!usou && window.gtag) { usou = true; gtag("event", "simulador_plano_uso"); }
  }

  ["s-ticket", "s-margem", "s-outros", "s-breakage", "s-novos", "s-indicacao", "s-titulo", "s-consultores", "s-crescimento"]
    .forEach(function (id) { $(id).addEventListener("input", calc); });

  $("add-nivel").addEventListener("click", function () {
    if (niveis.length < 10) { niveis.push(1); desenhaNiveis(); calc(); }
  });
  $("del-nivel").addEventListener("click", function () {
    if (niveis.length > 1) { niveis.pop(); desenhaNiveis(); calc(); }
  });

  desenhaNiveis();
  calc();
})();
</script>
</body>
</html>
