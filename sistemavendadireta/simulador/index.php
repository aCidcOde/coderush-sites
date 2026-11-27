<?php
declare(strict_types=1);

/*
[Modulo Simulador do Plano SVD]
@Author: André Gomes ( @acidcode )
@since 2026-08-18
@updated 2026-11-26 (Plano SVD como modelo inicial: rede, carreira, 12 meses, 3 cenarios)
@updated 2026-11-27 (aba Seu plano: a pessoa monta o plano dela por blocos)

POR QUE MUDOU: ate 26/11/2026 a pagina so respondia "o seu plano fecha a conta?"
com 9 numeros soltos. O usuario decidiu que o plano de referencia do sistema vira
o plano principal do SVD e pediu um simulador "pra pessoa abrir e sonhar com o
plano dela". O plano veio de um cliente: o nome do cliente, dos consultores e os
dados de producao NAO aparecem em nada publico (texto, JS, PDF, metadado). Os
bonus foram renomeados SVD Inicio, SVD Rede, SVD Carreira, SVD Lideranca, SVD
Conquistas, SVD Link, SVD Lideranca Digital, SVD Prime e SVD Pool; o titulo mais
alto e "Imperial Elite".

SEU PLANO (27/11/2026): a primeira versao abria o Plano SVD pronto e as regras
ficavam escondidas dentro do detalhe de cada bonus — "ficou so uma simulacao,
nao tem como o cara fazer a dele" (usuario). Agora a primeira aba monta o plano:
parte de um modelo (Plano SVD, unilevel simples, binario ou do zero) e cada bloco
(adesao, ativacao, inicio, unilevel, binario, titulos, carreira, lideranca,
conquistas, pool, link, lideranca digital, assinatura) liga, desliga e muda
niveis, geracoes, titulos e kits. A aba Sua rede junta as premissas da operacao
com a previa da arvore. Binario nao faz parte do Plano SVD: e bloco do construtor.

O QUE FICOU DO SIMULADOR ANTIGO: o veredito de viabilidade (fecha / no limite /
nao se sustenta / quebra), agora calculado sobre a margem de contribuicao do
mes 12, com bonus, produto, impostos, frete e operacao. E o PDF pela impressao do
navegador, sem biblioteca.

ONDE ESTA O CALCULO: js/simulador-plano.js (motor puro, versionado por plano;
testavel no node) e js/simulador-plano-tela.js (tela). As verificacoes pedidas
(100 PB -> R$ 50; 50 PB em 5 niveis -> R$ 18; 65 PB em 10 niveis -> R$ 32,50;
adesao promocional e normal; vigencia anual do desconto; pool desligado) rodam
na propria pagina, aba Verificacoes.

O QUE E ATIVO, OPCIONAL OU EM ESTUDO: ativo = Inicio, Rede, Link, Lideranca
Digital; opcional = Carreira, Lideranca, Conquistas (ligados no modelo) e Prime
(desligado); em estudo = Pool, sem distribuicao individual — o simulador mostra
so o fundo, com o aviso "regra de distribuicao individual em configuracao".

Calculo roda no browser: sem servidor, sem lead capturado a forca. Indexavel de
proposito. O link do WhatsApp leva o link da simulacao; a origem da visita (UTM,
gclid) segue pelo svd-attribution do inc/analytics.php, lido pelo zap-gate.
*/

require_once __DIR__ . '/../inc/promo.php';

$seoBase = 'https://sistemavendadireta.com.br';
$seoUrl = $seoBase . '/simulador/';
$seoTitle = 'Simulador de plano de marketing multinível | Plano SVD';
$seoDescription = 'Monte e simule o seu plano de marketing multinível: unilevel, binário, carreira e vendas '
    . 'pelo link, 12 meses em três cenários. Parte do Plano SVD. Sem cadastro.';

$demoHref = demoLink('simulador');
$v = static fn (string $arq): string => (string) @filemtime(__DIR__ . '/../' . $arq);
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
  <meta property="og:title" content="Simulador do Plano SVD: monte o seu plano" />
  <meta property="og:description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:url" content="<?= htmlspecialchars($seoUrl, ENT_QUOTES, 'UTF-8') ?>" />
  <meta property="og:site_name" content="Sistema Venda Direta" />
  <meta property="og:image" content="<?= htmlspecialchars($seoBase . promoOgImagem(), ENT_QUOTES, 'UTF-8') ?>" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:image" content="<?= htmlspecialchars($seoBase . promoOgImagem(), ENT_QUOTES, 'UTF-8') ?>" />
  <script type="application/ld+json">
  <?= json_encode([
      '@context' => 'https://schema.org',
      '@graph' => [
          [
              '@type' => 'WebApplication',
              'name' => 'Simulador do Plano SVD',
              'alternateName' => 'Simulador de plano de marketing multinível',
              'url' => $seoUrl,
              'description' => $seoDescription,
              'applicationCategory' => 'BusinessApplication',
              'operatingSystem' => 'Web',
              'inLanguage' => 'pt-BR',
              'isAccessibleForFree' => true,
              'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'BRL'],
              'provider' => ['@type' => 'Organization', 'name' => 'Sistema Venda Direta', 'url' => $seoBase . '/'],
          ],
          [
              '@type' => 'BreadcrumbList',
              'itemListElement' => [
                  ['@type' => 'ListItem', 'position' => 1, 'name' => 'Sistema Venda Direta', 'item' => $seoBase . '/'],
                  ['@type' => 'ListItem', 'position' => 2, 'name' => 'Simulador do Plano SVD', 'item' => $seoUrl],
              ],
          ],
      ],
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
  </script>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&amp;family=Roboto:wght@300;400;500;700&amp;display=swap" />
  <link rel="stylesheet" href="../css/site-tailwind.css?v=<?= $v('css/site-tailwind.css') ?>" />
  <link rel="stylesheet" href="../css/site-optimizations.css?v=<?= $v('css/site-optimizations.css') ?>" />
  <link rel="stylesheet" href="../css/styles.css?v=<?= $v('css/styles.css') ?>" />
  <link rel="stylesheet" href="../css/simulador.css?v=<?= $v('css/simulador.css') ?>" />
  <?php include __DIR__ . '/../inc/analytics.php'; ?>
</head>
<body class="bg-brand text-white antialiased font-[var(--font-body)] site-optimized">

  <header class="nao-imprime border-b border-white/10 bg-brand/95">
    <div class="mx-auto flex w-full items-center justify-between gap-4 px-4 py-3 sm:px-8">
      <a href="../"><img decoding="async" src="../imagens/Logo-Branco-1.webp" alt="Sistema Venda Direta" class="h-auto w-[150px] sm:w-[190px]" width="1000" height="300" loading="eager" /></a>
      <div class="flex items-center gap-4">
        <a href="<?= htmlspecialchars($demoHref, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="hidden text-sm font-semibold text-amber-300 hover:text-amber-200 sm:inline">Ver demonstração</a>
        <a href="../sistema-venda-direta/" class="rounded-full bg-amber-400 px-4 py-2 text-sm font-bold text-brand hover:bg-amber-300">Ver a promoção</a>
      </div>
    </div>
  </header>

  <main class="sim mx-auto max-w-site px-4 pb-40 sm:px-6">

    <div class="so-impressao" style="border-bottom:1px solid #cbd5e1;padding-bottom:10px;margin-bottom:12px">
      <p style="font-size:11px;letter-spacing:.14em;text-transform:uppercase">Sistema Venda Direta</p>
      <p style="font-size:21px;font-weight:800;margin-top:4px">Simulação: <span id="pdf-plano">Plano SVD</span> — <span class="sim-nome-empresa">sua empresa</span></p>
      <p id="pdf-data" style="font-size:11px;margin-top:4px"></p>
      <p style="font-size:9px;margin-top:2px;word-break:break-all">Abrir esta simulação: <span id="pdf-link"></span></p>
    </div>

    <section class="sim-hero">
      <div>
        <span class="sim-selo nao-imprime">Plano SVD · simulador sem cadastro</span>
        <h1>Monte o <em>seu plano</em> e veja a rede crescer</h1>
        <p class="lead">
          Comece pelo Plano SVD, já preenchido e funcionando, ou por um modelo unilevel, binário ou do zero.
          Ligue e desligue cada bônus, mude níveis, títulos e porcentagens, diga como a sua rede cresce
          e veja 12 meses em três cenários.
        </p>
        <div class="sim-empresa nao-imprime">
          <label for="sim-empresa">Nome da sua empresa</label>
          <input id="sim-empresa" class="sim-in" style="max-width:320px" data-path="empresa" data-texto="1" maxlength="80" placeholder="Ex.: Minha Marca Cosméticos" autocomplete="organization" />
        </div>
        <div class="nao-imprime" style="margin-top:14px">
          <div class="sim-seg" role="group" aria-label="Cenário da projeção">
            <button type="button" data-cenario="conservador" aria-pressed="false">Conservador</button>
            <button type="button" data-cenario="base" aria-pressed="true">Base</button>
            <button type="button" data-cenario="expansao" aria-pressed="false">Expansão</button>
          </div>
        </div>
      </div>
      <div class="sim-grid" style="grid-template-columns:repeat(2,minmax(0,1fr))">
        <div class="sim-kpi"><div class="rot">Consultores ativos no mês 12</div><div class="val" id="k-ativos">—</div><div class="sub" id="k-ativos-sub"></div></div>
        <div class="sim-kpi"><div class="rot">Faturamento no mês 12</div><div class="val" id="k-fat">—</div><div class="sub" id="k-fat-sub"></div></div>
        <div class="sim-kpi"><div class="rot">Bônus projetados no ano</div><div class="val" id="k-bonus">—</div><div class="sub" id="k-bonus-sub"></div></div>
        <div class="sim-kpi"><div class="rot">Caixa no fim do ano</div><div class="val" id="k-caixa">—</div><div class="sub" id="k-caixa-sub"></div></div>
      </div>
    </section>
    <p class="sim-aviso" style="margin:0 0 18px">
      Projeções ilustrativas: dependem das premissas que você informa e não são promessa de renda nem de resultado.
      O cálculo roda no seu navegador e nada é enviado.
    </p>

    <nav class="sim-abas" aria-label="Partes do simulador">
      <div role="tablist">
        <span class="sim-abas-rot" aria-hidden="true">Monte</span>
        <button type="button" role="tab" data-aba="plano" aria-selected="true">Seu plano</button>
        <button type="button" role="tab" data-aba="operacao" aria-selected="false">Sua rede</button>
        <span class="sim-abas-rot" aria-hidden="true">Veja</span>
        <button type="button" role="tab" data-aba="rede" aria-selected="false">Potencial da rede</button>
        <button type="button" role="tab" data-aba="saude" aria-selected="false">Saúde da operação</button>
        <button type="button" role="tab" data-aba="bonus" aria-selected="false">Bônus em detalhe</button>
        <button type="button" role="tab" data-aba="verificacoes" aria-selected="false">Verificações</button>
      </div>
    </nav>

    <!-- SEU PLANO -->
    <section id="aba-plano" class="sim-painel sim-sec" role="tabpanel" aria-label="Seu plano">
      <header>
        <div>
          <h2>Monte o seu plano</h2>
          <div class="sim-barra"></div>
          <p>Escolha um modelo para começar e ajuste bloco a bloco: ligue o que o seu plano paga, desligue o que não paga e mude níveis, títulos e porcentagens. A projeção recalcula a cada mudança.</p>
        </div>
      </header>
      <p class="sim-grupo-tit nao-imprime">Comece de um modelo</p>
      <div class="sim-modelos nao-imprime" id="pl-modelos" role="group" aria-label="Modelos de plano"></div>
      <div class="sim-montar">
        <div id="pl-blocos" class="nao-imprime"></div>
        <aside class="sim-resumo" aria-label="Resumo do seu plano">
          <div class="sim-card">
            <p class="rot">Seu plano agora</p>
            <h3 id="pl-nome" style="margin-top:6px">Plano SVD</h3>
            <p class="sim-aviso" id="pl-base" style="margin-top:4px"></p>
            <p class="sim-grupo-tit" style="margin:16px 0 0">O que um pedido distribui, no máximo</p>
            <div class="sim-teto" id="pl-teto"></div>
            <p class="sim-grupo-tit" style="margin:16px 0 0">O que o plano tem</p>
            <ul class="sim-ficha" id="pl-ficha"></ul>
            <p class="sim-aviso" id="pl-fora"></p>
            <label class="sim-campo nao-imprime" style="margin-top:14px"><span>Nome do plano</span><input class="sim-in" data-path="nomePlano" data-texto="1" maxlength="60" /></label>
          </div>
          <div id="pl-result" class="sim-veredito mini"></div>
          <button type="button" class="sim-btn forte nao-imprime" data-ir="rede">Ver a rede crescer</button>
        </aside>
      </div>
    </section>

    <!-- SUA REDE -->
    <section id="aba-operacao" class="sim-painel sim-sec" role="tabpanel" aria-label="Sua rede" hidden>
      <header>
        <div>
          <h2>Sua rede e a operação</h2>
          <div class="sim-barra"></div>
          <p>Como a rede começa e cresce, o que consultores e clientes compram, o preço do produto e quanto custa operar. São os números do cenário base; o conservador e o de expansão saem deles.</p>
        </div>
      </header>
      <div class="sim-montar">
        <div>
          <div id="p-grupos" class="sim-grid sim-g2"></div>
          <div class="sim-card" style="margin-top:14px">
            <h3>O que muda nos cenários conservador e expansão</h3>
            <p>Multiplicadores e pontos aplicados sobre o cenário base.</p>
            <div class="sim-tabela-wrap" style="margin-top:10px" id="p-cenarios"></div>
          </div>
        </div>
        <aside class="sim-resumo nao-imprime" aria-label="Prévia da rede">
          <div class="sim-card">
            <p class="rot">Sua rede no mês 12 · <span id="o-cenario">cenário base</span></p>
            <div class="sim-grid sim-kpis3">
              <div class="sim-kpi mini"><div class="rot">Ativos</div><div class="val" id="o-ativos">—</div></div>
              <div class="sim-kpi mini"><div class="rot">Níveis</div><div class="val" id="o-niveis">—</div></div>
              <div class="sim-kpi mini"><div class="rot">Com título</div><div class="val" id="o-titulos">—</div></div>
            </div>
            <p class="sim-aviso" id="o-texto"></p>
            <div class="sim-graf sim-arvore" id="g-arvore-op" style="margin-top:10px"></div>
            <p class="sim-aviso">Uma linha fundadora, ilustrada.</p>
          </div>
          <button type="button" class="sim-btn forte" data-ir="rede">Ver o potencial da rede</button>
        </aside>
      </div>
    </section>

    <!-- POTENCIAL DA REDE -->
    <section id="aba-rede" class="sim-painel sim-sec" role="tabpanel" aria-label="Potencial da rede" hidden>
      <header>
        <div>
          <h2>Potencial da rede da <span class="sim-nome-empresa">sua empresa</span></h2>
          <div class="sim-barra"></div>
          <p>Veja a rede crescer, quem já chega a um título e de onde vem cada bônus. Escolha o mês para ver a fotografia daquele momento.</p>
        </div>
        <label class="sim-campo nao-imprime" style="min-width:150px"><span>Fotografia do</span><select class="sim-in sim-mes" aria-label="Mês da fotografia"></select></label>
      </header>

      <div class="sim-grid sim-g4">
        <div class="sim-kpi"><div class="rot">Consultores ativos · <span class="sim-mes-rot">mês 12</span></div><div class="val" id="r-ativos">—</div><div class="sub" id="r-ativos-sub"></div></div>
        <div class="sim-kpi"><div class="rot">Pedidos pagos no mês</div><div class="val" id="r-pedidos">—</div><div class="sub" id="r-pedidos-sub"></div></div>
        <div class="sim-kpi"><div class="rot">Volume do mês</div><div class="val" id="r-pb">—</div><div class="sub" id="r-pb-sub"></div></div>
        <div class="sim-kpi"><div class="rot">Consultores aptos a receber</div><div class="val" id="r-elig">—</div><div class="sub" id="r-elig-sub"></div></div>
      </div>

      <div class="sim-grid sim-g2" style="margin-top:14px">
        <div class="sim-card">
          <h3>Uma linha fundadora, ilustrada</h3>
          <p id="r-estrutura"></p>
          <div class="sim-graf sim-arvore" id="g-arvore" style="margin-top:12px"></div>
          <p class="sim-aviso">Árvore sintética e equilibrada, só para visualizar. Numa rede real as linhas crescem de forma desigual; por isso compressão, diferencial e gerações aparecem como faixa estimada.</p>
        </div>
        <div class="sim-card">
          <h3>Títulos e conquistas</h3>
          <p>Títulos na rede no <span class="sim-mes-rot">mês 12</span>:</p>
          <ul class="sim-legenda" id="r-titulos" style="margin-top:8px"></ul>
          <h3 style="margin-top:16px">Marcos do fundador</h3>
          <div id="r-marcos" style="margin-top:6px"></div>
          <p class="sim-aviso" id="r-proximo"></p>
        </div>
      </div>

      <div class="sim-card" style="margin-top:14px">
        <div style="display:flex;flex-wrap:wrap;justify-content:space-between;gap:8px;align-items:baseline">
          <h3>Bônus projetados por origem, mês a mês</h3>
          <button type="button" class="sim-link nao-imprime" data-tabela="t-bonus-wrap">Ver em tabela</button>
        </div>
        <div class="sim-graf" id="g-bonus" style="margin-top:10px"></div>
        <div class="sim-tabela-wrap" id="t-bonus-wrap" hidden style="margin-top:10px"><div id="t-bonus"></div></div>
      </div>

      <div class="sim-card" id="calc-carreira" style="margin-top:14px">
        <h3>Veja o caminho até a sua próxima conquista</h3>
        <p>Informe o seu volume e o de cada linha no mês. O título usa a meta de PB qualificados, e cada linha conta no máximo o VML do título.</p>
        <div class="sim-grid sim-g2" style="margin-top:12px">
          <div>
            <div class="sim-grid sim-g2">
              <label class="sim-campo"><span>Seu volume pessoal<small>PB no mês</small></span><input id="c-pessoal" class="sim-in" type="number" min="0" step="any" value="100" /></label>
              <label class="sim-campo"><span>Sua maior compra única<small>PB</small></span><input id="c-compra" class="sim-in" type="number" min="0" step="any" value="65" /></label>
            </div>
            <div id="c-linhas" class="sim-grid sim-g2" style="margin-top:10px"></div>
            <div class="nao-imprime" style="display:flex;gap:8px;margin-top:10px">
              <button type="button" class="sim-btn" id="c-mais">+ linha</button>
              <button type="button" class="sim-btn" id="c-menos">− linha</button>
            </div>
          </div>
          <div id="c-saida"></div>
        </div>
      </div>
    </section>

    <!-- SAUDE DA OPERACAO -->
    <section id="aba-saude" class="sim-painel sim-sec" role="tabpanel" aria-label="Saúde da operação" hidden>
      <header>
        <div>
          <h2>Saúde da operação</h2>
          <div class="sim-barra"></div>
          <p>O lado da empresa: faturamento pelo valor pago, custo do produto, bônus projetados, custos e caixa. É aqui que se vê se o plano fecha a conta.</p>
        </div>
      </header>
      <div id="s-veredito" class="sim-veredito"></div>
      <div class="sim-grid sim-g2" style="margin-top:14px">
        <div class="sim-card">
          <h3>Os três cenários lado a lado</h3>
          <div class="sim-tabela-wrap" style="margin-top:10px" id="s-cenarios"></div>
          <p class="sim-aviso">O que muda de um cenário para outro:</p>
          <ul id="s-premissas-cenario" class="sim-aviso" style="padding-left:18px;margin-top:6px"></ul>
        </div>
        <div class="sim-card">
          <div style="display:flex;flex-wrap:wrap;justify-content:space-between;gap:8px;align-items:baseline">
            <h3>Caixa acumulado</h3>
            <button type="button" class="sim-link nao-imprime" data-tabela="t-caixa-wrap">Ver em tabela</button>
          </div>
          <p>Parte do investimento inicial, negativo, e soma o resultado de cada mês.</p>
          <div class="sim-graf" id="g-caixa" style="margin-top:10px"></div>
          <div class="sim-tabela-wrap" id="t-caixa-wrap" hidden style="margin-top:10px"><div id="t-caixa"></div></div>
        </div>
      </div>
      <div class="sim-card" style="margin-top:14px">
        <h3>Mês a mês, no cenário escolhido</h3>
        <p>Cada bônus aparece na base certa (PB ou reais) e só uma vez: fundos e comissões não se somam em dobro.</p>
        <div class="sim-tabela-wrap" style="margin-top:10px" id="t-dre"></div>
      </div>
    </section>

    <!-- BONUS -->
    <section id="aba-bonus" class="sim-painel sim-sec" role="tabpanel" aria-label="Bônus do plano" hidden>
      <header>
        <div>
          <h2>Os bônus em detalhe</h2>
          <div class="sim-barra"></div>
          <p>Abra cada bônus para ver a fórmula, os requisitos e o efeito de mudar uma variável. As regras se mudam na aba Seu plano. <strong>Potencial</strong> é a tabela cheia; <strong>elegível</strong>, o que tem quem receba na rede; <strong>projetado</strong>, o que sai do caixa.</p>
        </div>
        <label class="sim-campo nao-imprime" style="min-width:150px"><span>Valores do</span><select class="sim-in sim-mes" aria-label="Mês dos valores"></select></label>
      </header>
      <div id="b-lista" class="sim-grid"></div>
    </section>

    <!-- VERIFICACOES -->
    <section id="aba-verificacoes" class="sim-painel sim-sec sim-verif" role="tabpanel" aria-label="Verificações" hidden>
      <header>
        <div>
          <h2>Verificações do cálculo</h2>
          <div class="sim-barra"></div>
          <p>Casos simples, conferidos no seu navegador contra as regras do Plano SVD original e dos blocos do construtor, para você confiar nos números.</p>
        </div>
      </header>
      <p id="v-resumo" style="font-weight:700"></p>
      <ul id="v-lista" style="margin-top:8px"></ul>
    </section>

    <section class="sim-sec nao-imprime">
      <div class="sim-card" style="display:grid;gap:14px">
        <div>
          <h2>Quer esse plano rodando na <span class="sim-nome-empresa">sua empresa</span>?</h2>
          <div class="sim-barra"></div>
          <p style="margin-top:10px;color:var(--sim-text-2);max-width:760px;line-height:1.55">
            O Plano SVD já roda no Sistema Venda Direta, e o plano que você montou aqui também pode rodar.
            Na conversa a gente parte desta simulação: o link vai junto com a mensagem.
          </p>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:10px">
          <a class="sim-btn zap sim-cta-zap" href="https://wa.me/5511994566726" target="_blank" rel="noopener noreferrer">Conversar sobre adaptar este plano</a>
          <a class="sim-btn forte" href="<?= htmlspecialchars($demoHref, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Ver o sistema funcionando</a>
          <button type="button" class="sim-btn" id="btn-restaurar">Voltar ao Plano SVD original</button>
        </div>
      </div>
    </section>
  </main>

  <div class="sim sim-rodape nao-imprime">
    <div>
      <div class="item"><span>Cenário</span><strong id="f-cenario">Base</strong></div>
      <div class="item"><span>Bônus no mês 12</span><strong id="f-payout">—</strong></div>
      <div class="item opc"><span>Sobra no mês 12</span><strong id="f-sobra">—</strong></div>
      <div class="item"><span>Caixa em 12 meses</span><strong id="f-caixa">—</strong></div>
      <div class="acoes">
        <button type="button" class="sim-btn" id="btn-salvar">Salvar</button>
        <button type="button" class="sim-btn" id="btn-link">Copiar link</button>
        <button type="button" class="sim-btn forte" id="btn-pdf">PDF</button>
      </div>
    </div>
  </div>

  <script src="../js/simulador-plano.js?v=<?= $v('js/simulador-plano.js') ?>"></script>
  <script src="../js/simulador-plano-tela.js?v=<?= $v('js/simulador-plano-tela.js') ?>"></script>
  <?php $zapOrigem = 'simulador'; include __DIR__ . '/../inc/zap-tracking.php'; ?>
</body>
</html>
