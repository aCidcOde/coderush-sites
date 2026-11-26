/*
 * Simulador do Plano SVD — motor de calculo + tela.
 * Sistema Venda Direta · 2026-11-26
 *
 * O motor (SIM) e funcao pura: recebe a configuracao e devolve a projecao mes a
 * mes. A tela so le e escreve configuracao. Isso deixa o calculo reproduzivel —
 * o mesmo link abre o mesmo numero — e testavel fora do navegador
 * (node: require("./simulador-plano.js")).
 *
 * VERSOES: cada plano publicado fica congelado em PLANOS[versao]. O link
 * compartilhado leva a versao e so a diferenca em relacao a ela; mudar o plano
 * no futuro cria uma versao nova e nao altera simulacao ja enviada. Campo novo
 * com valor neutro (bloco desligado, numero que reproduz a regra antiga) pode
 * entrar na versao vigente: link antigo continua dando o mesmo numero.
 *
 * MODELOS (27/11/2026): a pessoa monta o plano dela a partir de um modelo —
 * Plano SVD, unilevel simples, binario ou do zero. Modelo troca so as regras do
 * plano (adesao, ativacao, bonus, titulos); rede e custos ficam como estao.
 *
 * O QUE E ESTIMATIVA: a rede e uma estrutura sintetica (linhas fundadoras,
 * largura media, arvore equilibrada). Onde ela nao permite precisao — compressao
 * de inativos, diferencial de carreira, geracoes de lideres — o resultado vem
 * como faixa, e o caixa usa o lado prudente da faixa.
 */
(function (raiz) {
  "use strict";

  function clone(o) { return JSON.parse(JSON.stringify(o)); }
  function soma(a) { return a.reduce(function (s, v) { return s + (+v || 0); }, 0); }
  function lim(v, a, b) { return Math.max(a, Math.min(b, v)); }

  // ---------------------------------------------------------------- planos
  var PLANOS = {
    "svd-2026.11": {
      versao: "svd-2026.11",
      modelo: "svd",
      nomePlano: "Plano SVD",
      nomes: {}, // nome que a pessoa da a cada bonus no plano dela
      empresa: "",
      economia: { reaisPorPB: 3, valorPB: 1 },
      // % sobre o preco ao consumidor
      descontos: { adesao: 40, recompra: 50, consumidorInteligente: 40 },
      cmv: 20, // custo do produto, % do preco ao consumidor
      pbBonifSobreQual: { adesao: 100, recompra: 100 },
      adesao: {
        opcoes: [{ pb: 80, peso: 50 }, { pb: 100, peso: 50 }],
        promo: { ligada: false, pb: 50, inicio: 1, fim: 3 }
      },
      // pbCinco libera os primeiros `niveisCinco` niveis; pbDez libera a tabela toda
      // (os nomes vem do Plano SVD, onde sao 5 e 10 niveis)
      ativacao: { pbCinco: 50, pbDez: 65, niveisCinco: 5, diaCorte: 25, compressao: true },
      bonus: {
        inicio: { ligado: true, niveis: [50, 5, 5, 5, 5, 3, 3, 3, 3, 3], exigeAtivacao: true },
        rede: { ligado: true, niveis: [10, 8, 7, 6, 5, 4, 3, 3, 2, 2], exigeAtivacao: true },
        // fora do Plano SVD: bloco do construtor, desligado aqui
        binario: { ligado: false, taxa: 10, teto: 3000, equilibrio: 50 },
        carreira: { ligado: true },
        lideranca: { ligado: true, geracoes: [3, 1, 0.5, 0.5], tituloLider: 2 },
        conquistas: { ligado: true, fundo: 5, tituloMinimo: 2 },
        link: { ligado: true, taxa: 25 },
        liderancaDigital: { ligado: true, taxa: 5 },
        prime: { ligado: false, preco: 99, pbQual: 30, pbBonif: 20, adesao: 10, margem: 70,
                 beneficios: "Frete reduzido e mimo mensal" },
        pool: { cenarioAlternativo: false, fundo: 10 }
      },
      titulos: [
        { nome: "Bronze", meta: 2500, vml: 1250, linhas: 0, taxa: 4 },
        { nome: "Prata", meta: 5000, vml: 2500, linhas: 0, taxa: 6 },
        { nome: "Ouro", meta: 10000, vml: 4000, linhas: 0, taxa: 7 },
        { nome: "Esmeralda", meta: 20000, vml: 7500, linhas: 0, taxa: 8 },
        { nome: "Diamante", meta: 40000, vml: 12000, linhas: 0, taxa: 12 },
        { nome: "Duplo Diamante", meta: 100000, vml: 30000, linhas: 0, taxa: 13 },
        { nome: "Imperial", meta: 300000, vml: 65000, linhas: 0, taxa: 14 },
        { nome: "Imperial Elite", meta: 900000, vml: 150000, linhas: 0, taxa: 15 }
      ],
      digital: { cupomTeto: 15, base: "pago" },
      // premissas da operacao (cenario base). Ilustrativas: a pessoa troca pelas dela.
      operacao: {
        consultoresIniciais: 40,
        linhasFundadoras: 4,
        largura: 3,
        novosCadastros: 45,
        crescimentoCadastros: 6,
        tetoCadastros: 250,
        conversao: 60,
        perdaMensal: 4,
        renovacao: 70,
        pbRenovacao: 80,
        ativacaoDez: 45,
        ativacaoCinco: 20,
        pbMedioDez: 70,
        pbMedioCinco: 55,
        recompraExtra: 25,
        pbRecompraExtra: 40,
        consumidores: 2,
        pedidosConsumidor: 0.5,
        ticketDigital: 180,
        consumidorInteligente: 30,
        usoCupom: 40,
        cupomMedio: 10,
        impostos: 12,
        freteValor: 12,
        freteParcela: 30,
        cac: 60,
        operacaoVariavel: 6,
        custoFixo: 22000,
        investimento: 40000
      },
      // o que muda entre os cenarios (sobre o cenario base)
      cenarios: {
        conservador: { novosCadastros: 0.6, crescimentoCadastros: 0.5, conversao: -10, perdaMensal: 2,
                       ativacaoDez: -10, consumidores: 0.7 },
        expansao: { novosCadastros: 1.5, crescimentoCadastros: 1.5, conversao: 10, perdaMensal: -1,
                    ativacaoDez: 10, consumidores: 1.3 }
      }
    }
  };
  var VERSAO_ATUAL = "svd-2026.11";

  // variaveis com efeito multiplicativo no cenario (as outras somam pontos)
  var MULTIPLICA = { novosCadastros: 1, crescimentoCadastros: 1, consumidores: 1 };

  function planoPadrao(versao) { return clone(PLANOS[versao] || PLANOS[VERSAO_ATUAL]); }

  // diferenca entre dois objetos (so o que mudou), pra caber num link
  function diferenca(a, b) {
    if (Array.isArray(b)) return JSON.stringify(a) === JSON.stringify(b) ? undefined : clone(b);
    if (b && typeof b === "object") {
      var out = {}, tem = false;
      Object.keys(b).forEach(function (k) {
        var d = diferenca(a ? a[k] : undefined, b[k]);
        if (d !== undefined) { out[k] = d; tem = true; }
      });
      return tem ? out : undefined;
    }
    return a === b ? undefined : b;
  }
  function aplicar(base, dif) {
    if (!dif || typeof dif !== "object" || Array.isArray(dif)) return dif === undefined ? base : clone(dif);
    var out = Array.isArray(base) ? clone(base) : Object.assign({}, base);
    Object.keys(dif).forEach(function (k) {
      out[k] = (base && typeof base[k] === "object" && !Array.isArray(base[k]) && typeof dif[k] === "object" && !Array.isArray(dif[k]))
        ? aplicar(base[k], dif[k]) : clone(dif[k]);
    });
    return out;
  }

  function operacaoDoCenario(cfg, cenario) {
    var op = clone(cfg.operacao);
    var aj = cenario && cenario !== "base" ? cfg.cenarios[cenario] || {} : {};
    Object.keys(aj).forEach(function (k) {
      if (!(k in op)) return;
      op[k] = MULTIPLICA[k] ? op[k] * aj[k] : op[k] + aj[k];
    });
    ["conversao", "perdaMensal", "ativacaoDez", "ativacaoCinco", "renovacao"].forEach(function (k) {
      op[k] = lim(op[k], 0, 100);
    });
    if (op.ativacaoDez + op.ativacaoCinco > 100) op.ativacaoCinco = 100 - op.ativacaoDez;
    return op;
  }

  // ------------------------------------------------------ regras unitarias
  // Profundidade liberada pela MAIOR compra unica paga na competencia.
  // Pedidos separados nao se somam.
  function profundidadePorCompras(pedidosPB, ativ, totalNiveis) {
    var maior = pedidosPB.length ? Math.max.apply(null, pedidosPB) : 0;
    var todos = totalNiveis || 10, primeiros = Math.min(ativ.niveisCinco || 5, todos);
    if (maior >= ativ.pbDez) return todos;
    if (maior >= ativ.pbCinco) return primeiros;
    return 0;
  }
  // Binario: paga a taxa sobre a perna menor, limitado ao teto do consultor.
  function valorBinario(pernaA, pernaB, taxa, teto, valorPB) {
    var v = Math.min(pernaA, pernaB) * taxa / 100 * (valorPB || 1);
    return teto > 0 ? Math.min(teto, v) : v;
  }
  // Valor que um pedido gera na tabela, olhando so os niveis liberados.
  function valorPedidoNaTabela(pb, tabela, niveisLiberados, valorPB) {
    var s = 0;
    for (var i = 0; i < Math.min(niveisLiberados, tabela.length); i++) s += pb * tabela[i] / 100;
    return s * (valorPB || 1);
  }
  // Desconto de recompra vale pelos 12 meses do plano anual, ativado ou nao.
  function descontoRecompra(mesEntrada, mesCompra, cfg) {
    return (mesCompra - mesEntrada) < 12 ? cfg.descontos.recompra : 0;
  }
  function pbDaAdesao(cfg, mes) {
    var p = cfg.adesao.promo;
    if (p.ligada && mes >= p.inicio && mes <= p.fim) return [{ pb: p.pb, peso: 100, promo: true }];
    var tot = soma(cfg.adesao.opcoes.map(function (o) { return o.peso; })) || 1;
    return cfg.adesao.opcoes.map(function (o) { return { pb: o.pb, peso: o.peso * 100 / tot }; });
  }

  // ------------------------------------------------- estrutura sintetica
  // Floresta de `linhas` arvores equilibradas com `largura` indicados ativos
  // por consultor. Da a fracao de pedidos com upline em cada nivel e o volume
  // de cada linha — o suficiente pra estimar elegibilidade e titulos.
  function estrutura(total, largura, linhas) {
    linhas = Math.max(1, Math.round(linhas));
    var n = Math.max(1, total / linhas);
    var b = Math.max(1.2, largura);
    var niveis = [], resto = n, d = 0;
    while (resto > 1e-6 && d < 80) {
      var q = Math.min(Math.pow(b, d), resto);
      niveis.push(q); resto -= q; d++;
    }
    var acum = []; // acum[d] = nos com profundidade >= d
    var s = 0;
    for (var i = niveis.length - 1; i >= 0; i--) { s += niveis[i]; acum[i] = s; }
    function X(k) { return k <= 0 ? 1 : (k < niveis.length ? acum[k] / n : 0); }
    function Xf(k) { var a = Math.floor(k), f = k - a; return X(a) * (1 - f) + X(a + 1) * f; }
    function sub(dd) { return dd < niveis.length ? acum[dd] / niveis[dd] : 0; }
    function filhos(dd) { return dd + 1 < niveis.length ? niveis[dd + 1] / niveis[dd] : 0; }
    return { n: n, linhas: linhas, largura: b, niveis: niveis, profundidade: niveis.length - 1,
             X: X, Xf: Xf, sub: sub, filhos: filhos };
  }

  // titulo de um no na profundidade d (indice em cfg.titulos, -1 = sem titulo)
  function volumeQualificado(pbPessoal, linhasPB, titulo) {
    return pbPessoal + soma(linhasPB.map(function (v) { return Math.min(v, titulo.vml || Infinity); }));
  }
  function tituloAlcancado(pbPessoal, linhasPB, titulos) {
    var melhor = -1;
    titulos.forEach(function (t, i) {
      var ativas = linhasPB.filter(function (v) { return v > 0; }).length;
      if (volumeQualificado(pbPessoal, linhasPB, t) >= t.meta && ativas >= (t.linhas || 0)) melhor = i;
    });
    return melhor;
  }
  function titulosPorProfundidade(est, q, titulos) {
    var out = [];
    for (var d = 0; d < est.niveis.length; d++) {
      var f = est.filhos(d), vol = est.sub(d + 1) * q;
      var melhor = -1;
      titulos.forEach(function (t, i) {
        var qv = q + f * Math.min(vol, t.vml || Infinity);
        if (qv >= t.meta && f + 1e-9 >= (t.linhas || 0)) melhor = i;
      });
      out.push(melhor);
    }
    return out;
  }

  // elegibilidade por nivel: fracao do PB de um pedido que chega a alguem apto
  function elegibilidadeNiveis(est, ativos, dez, comCompressao, exigeAtivacao, total, primeiros) {
    var out = [];
    for (var k = 1; k <= total; k++) {
      if (!exigeAtivacao) { out.push(est.X(k)); continue; }
      var apto = k <= primeiros ? ativos : dez; // apto = ativo com profundidade >= k
      if (comCompressao) {
        // inativo e pulado: o k-esimo ATIVO acima recebe, se tiver profundidade
        var existe = ativos > 0 ? est.Xf(k / ativos) : 0;
        out.push(existe * (ativos > 0 ? apto / ativos : 0));
      } else {
        out.push(est.X(k) * apto);
      }
    }
    return out;
  }

  // ------------------------------------------------------------ projecao
  function simular(cfg, cenario, meses) {
    meses = meses || 12;
    var op = operacaoDoCenario(cfg, cenario);
    var B = cfg.bonus, E = cfg.economia, rp = E.reaisPorPB, vpb = E.valorPB;
    var poolAtivo = !!B.pool.cenarioAlternativo;
    var resultado = { cenario: cenario || "base", op: op, meses: [] };
    var ativosBase = op.consultoresIniciais; // consultores com plano vigente
    var iniciais = op.consultoresIniciais;
    var caixa = -op.investimento;
    var tituloFundadorAnterior = -1;

    for (var m = 1; m <= meses; m++) {
      var r = { mes: m };
      // cadastros (com teto: rede nao cresce pra sempre)
      var novos = Math.min(op.tetoCadastros, op.novosCadastros * Math.pow(1 + op.crescimentoCadastros / 100, m - 1));
      var adesoes = novos * op.conversao / 100;
      // perda mensal + quem dos iniciais nao renova no aniversario (espalhado no ano)
      var saem = ativosBase * op.perdaMensal / 100;
      iniciais = iniciais * (1 - op.perdaMensal / 100);
      var aniversario = iniciais / Math.max(1, 13 - m); // renova 1/12 por mes do que sobrou
      var renovam = aniversario * op.renovacao / 100;
      var naoRenovam = aniversario - renovam;
      iniciais -= aniversario;
      var veteranos = Math.max(0, ativosBase - saem - naoRenovam);
      var ativos = veteranos + adesoes;
      ativosBase = ativos;

      // pedidos e PB
      var mixAdesao = pbDaAdesao(cfg, m);
      var pbAdesao = adesoes * soma(mixAdesao.map(function (o) { return o.pb * o.peso / 100; }));
      var adesaoDez = adesoes * soma(mixAdesao.map(function (o) { return o.pb >= cfg.ativacao.pbDez ? o.peso / 100 : 0; }));
      var adesaoCinco = adesoes * soma(mixAdesao.map(function (o) { return o.pb >= cfg.ativacao.pbCinco && o.pb < cfg.ativacao.pbDez ? o.peso / 100 : 0; }));
      var baseAtiv = Math.max(0, veteranos - renovam); // quem renova faz a compra da renovacao
      var nDez = baseAtiv * op.ativacaoDez / 100, nCinco = baseAtiv * op.ativacaoCinco / 100;
      var pbAtivacao = nDez * op.pbMedioDez + nCinco * op.pbMedioCinco;
      var nExtra = veteranos * op.recompraExtra / 100;
      var pbExtra = nExtra * op.pbRecompraExtra;
      var pbRenov = renovam * op.pbRenovacao;
      var renovDez = op.pbRenovacao >= cfg.ativacao.pbDez ? renovam : 0;
      var renovCinco = op.pbRenovacao >= cfg.ativacao.pbCinco && op.pbRenovacao < cfg.ativacao.pbDez ? renovam : 0;
      var prime = B.prime.ligado ? ativos * B.prime.adesao / 100 : 0;

      var fBA = cfg.pbBonifSobreQual.adesao / 100, fBR = cfg.pbBonifSobreQual.recompra / 100;
      var pbQual = pbAdesao + pbAtivacao + pbExtra + pbRenov + prime * B.prime.pbQual;
      var pbBonifAdesao = pbAdesao * fBA;
      var pbBonifRede = (pbAtivacao + pbExtra + pbRenov) * fBR + prime * B.prime.pbBonif;

      // receita (so pedido pago, pelo valor pago)
      var recAdesao = pbAdesao * rp, recAtiv = pbAtivacao * rp, recExtra = pbExtra * rp, recRenov = pbRenov * rp;
      var consumidores = ativos * op.consumidores;
      var pedDigital = consumidores * op.pedidosConsumidor;
      var cupom = Math.min(op.cupomMedio, cfg.digital.cupomTeto) / 100 * op.usoCupom / 100;
      var parcelaInteligente = op.consumidorInteligente / 100;
      var precoMedioDigital = op.ticketDigital * (parcelaInteligente * (1 - cfg.descontos.consumidorInteligente / 100)
        + (1 - parcelaInteligente) * (1 - cupom));
      var recDigital = pedDigital * precoMedioDigital;
      var recPrime = prime * B.prime.preco;
      var faturamento = recAdesao + recAtiv + recExtra + recRenov + recDigital + recPrime;

      // custo do produto: o CMV e sobre o preco ao consumidor
      var cmv = cfg.cmv / 100;
      var cmvR = recAdesao * cmv / (1 - cfg.descontos.adesao / 100)
        + recRenov * cmv / (1 - cfg.descontos.adesao / 100)
        + (recAtiv + recExtra) * cmv / (1 - cfg.descontos.recompra / 100)
        + pedDigital * op.ticketDigital * cmv
        + recPrime * (1 - B.prime.margem / 100);

      // estrutura e elegibilidade
      var est = estrutura(ativos, op.largura, op.linhasFundadoras);
      var ativosPct = ativos > 0 ? lim((nDez + nCinco + adesaoDez + adesaoCinco + renovDez + renovCinco) / ativos, 0, 1) : 0;
      var dezPct = ativos > 0 ? lim((nDez + adesaoDez + renovDez) / ativos, 0, 1) : 0;
      var comp = !!cfg.ativacao.compressao;

      function tabelaPaga(pb, tabela, exige) {
        var n = tabela.length, prim = cfg.ativacao.niveisCinco || 5;
        var semC = elegibilidadeNiveis(est, ativosPct, dezPct, false, exige, n, prim);
        var comC = elegibilidadeNiveis(est, ativosPct, dezPct, true, exige, n, prim);
        var exist = elegibilidadeNiveis(est, 1, 1, false, false, n, prim);
        var pot = 0, eleg = 0, sem = 0, com = 0;
        for (var k = 0; k < n; k++) {
          var t = (tabela[k] || 0) / 100;
          pot += pb * t; eleg += pb * t * exist[k]; sem += pb * t * semC[k]; com += pb * t * comC[k];
        }
        var proj = comp ? com : sem;
        return { potencial: pot * vpb, elegivel: eleg * vpb, projetado: proj * vpb,
                 faixa: [Math.min(sem, com) * vpb, Math.max(sem, com) * vpb] };
      }

      var b = {};
      b.inicio = B.inicio.ligado ? tabelaPaga(pbBonifAdesao, B.inicio.niveis, B.inicio.exigeAtivacao) : zero();
      b.rede = B.rede.ligado ? tabelaPaga(pbBonifRede, B.rede.niveis, B.rede.exigeAtivacao !== false) : zero();

      // titulos na estrutura sintetica
      var q = ativos > 0 ? pbQual / ativos : 0;
      var tit = titulosPorProfundidade(est, q, cfg.titulos);
      var contTit = cfg.titulos.map(function () { return 0; });
      tit.forEach(function (t, d) { if (t >= 0) contTit[t] += est.niveis[d] * est.linhas; });
      var taxa = function (i) { return i >= 0 ? cfg.titulos[i].taxa / 100 : 0; };
      var pbBonifTotal = pbBonifAdesao + pbBonifRede;

      // carreira: diferencial. Teto = maior taxa acima; piso = taxa do titulado mais proximo
      var carMax = 0, carMin = 0, lidMax = 0, lidMin = 0;
      var G = B.lideranca.geracoes, ultimoTit = cfg.titulos.length - 1;
      var tituloLider = Math.min(B.lideranca.tituloLider, ultimoTit), tituloConq = Math.min(B.conquistas.tituloMinimo, ultimoTit);
      for (var d = 1; d < est.niveis.length; d++) {
        var frac = est.niveis[d] / est.n, maior = 0, prox = 0;
        for (var j = d - 1; j >= 0; j--) {
          if (tit[j] >= 0 && !prox) prox = taxa(tit[j]);
          maior = Math.max(maior, taxa(tit[j]));
        }
        carMax += frac * maior; carMin += frac * prox;
      }
      for (var d2 = 0; d2 < est.niveis.length; d2++) {
        var lideres = 0;
        for (var j2 = 0; j2 <= d2; j2++) if (tit[j2] >= tituloLider) lideres++;
        var acima = Math.max(0, lideres - 1);
        var fr = est.niveis[d2] / est.n;
        lidMax += fr * soma(G.slice(0, Math.min(G.length, acima))) / 100;
        lidMin += fr * (acima >= 1 ? G[0] / 100 : 0);
      }
      var usaCarreira = B.carreira.ligado && !poolAtivo;
      b.carreira = usaCarreira ? {
        potencial: pbBonifTotal * taxa(cfg.titulos.length - 1) * vpb,
        elegivel: pbBonifTotal * carMax * vpb,
        projetado: pbBonifTotal * carMax * vpb,
        faixa: [pbBonifTotal * carMin * vpb, pbBonifTotal * carMax * vpb]
      } : zero();
      var usaLid = B.lideranca.ligado && !poolAtivo;
      b.lideranca = usaLid ? {
        potencial: pbBonifTotal * soma(G) / 100 * vpb,
        elegivel: pbBonifTotal * lidMax * vpb,
        projetado: pbBonifTotal * lidMax * vpb,
        faixa: [pbBonifTotal * lidMin * vpb, pbBonifTotal * lidMax * vpb]
      } : zero();
      var qualificadosConq = soma(contTit.slice(tituloConq));
      var fundoConq = pbBonifTotal * B.conquistas.fundo / 100 * vpb;
      var usaConq = B.conquistas.ligado && !poolAtivo;
      b.conquistas = usaConq ? {
        potencial: fundoConq, elegivel: fundoConq,
        projetado: qualificadosConq > 0 ? fundoConq : 0,
        faixa: [qualificadosConq > 0 ? fundoConq : 0, fundoConq], qualificados: qualificadosConq
      } : zero();
      b.pool = poolAtivo ? (function () {
        var f = pbBonifTotal * B.pool.fundo / 100 * vpb;
        return { potencial: f, elegivel: f, projetado: f, faixa: [f, f] };
      })() : zero();

      // binario: arvore de posicionamento propria (duas pernas por consultor), uma
      // por linha fundadora. Cada pedido conta na perna de todos os de cima; paga a
      // perna menor, ate o teto, so a quem esta ativo. Sem acumulo de pontos.
      var BB = B.binario || {};
      b.binario = BB.ligado ? (function () {
        var eb = estrutura(ativos, 2, op.linhasFundadoras);
        var qb = ativos > 0 ? pbBonifTotal / ativos : 0;
        var e = lim(+BB.equilibrio || 0, 1, 100) / 100, menor = e / (1 + e);
        var pot = 0, eleg = 0;
        for (var d = 0; d < eb.niveis.length; d++) {
          var nos = eb.niveis[d] * eb.linhas;
          var duasPernas = lim(eb.filhos(d) - 1, 0, 1); // ultima camada nao tem as duas
          var abaixo = Math.max(0, eb.sub(d) - 1) * qb;
          var v = abaixo * menor * BB.taxa / 100 * vpb;
          pot += nos * duasPernas * v;
          eleg += nos * duasPernas * (BB.teto > 0 ? Math.min(BB.teto, v) : v);
        }
        var proj = eleg * ativosPct;
        return { potencial: pot, elegivel: eleg, projetado: proj, faixa: [proj, proj], profundidade: eb.profundidade };
      })() : zero();

      // digital: em reais, fora da base de PB (sem contagem dupla)
      var baseDigital = cfg.digital.base === "tabela" ? pedDigital * op.ticketDigital : recDigital;
      var temPatrocinador = est.X(1);
      b.link = B.link.ligado ? fixo(baseDigital * B.link.taxa / 100) : zero();
      b.liderancaDigital = B.liderancaDigital.ligado ? {
        potencial: baseDigital * B.liderancaDigital.taxa / 100,
        elegivel: baseDigital * B.liderancaDigital.taxa / 100 * temPatrocinador,
        projetado: baseDigital * B.liderancaDigital.taxa / 100 * temPatrocinador,
        faixa: [baseDigital * B.liderancaDigital.taxa / 100 * temPatrocinador, baseDigital * B.liderancaDigital.taxa / 100 * temPatrocinador]
      } : zero();

      var bonusTotal = soma(Object.keys(b).map(function (k) { return b[k].projetado; }));
      var impostos = faturamento * op.impostos / 100;
      var pedidos = adesoes + nDez + nCinco + nExtra + renovam + pedDigital + prime;
      var frete = pedidos * op.freteParcela / 100 * op.freteValor;
      var cac = novos * op.cac;
      var operacao = faturamento * op.operacaoVariavel / 100;
      var contribuicao = faturamento - impostos - cmvR - frete - bonusTotal - operacao;
      var resultadoMes = contribuicao - cac - op.custoFixo;
      caixa += resultadoMes;

      var tFund = tit[0];
      Object.assign(r, {
        novos: novos, adesoes: adesoes, ativos: ativos, renovam: renovam, saem: saem + naoRenovam,
        ativacaoDez: nDez, ativacaoCinco: nCinco, consumidores: consumidores, pedDigital: pedDigital, prime: prime,
        pedidos: pedidos, pbQual: pbQual, pbBonifAdesao: pbBonifAdesao, pbBonifRede: pbBonifRede, pbBonifTotal: pbBonifTotal,
        receita: { adesao: recAdesao, recompra: recAtiv + recExtra, renovacao: recRenov, digital: recDigital, prime: recPrime },
        faturamento: faturamento, impostos: impostos, cmv: cmvR, frete: frete, bonus: b, bonusTotal: bonusTotal,
        cac: cac, operacao: operacao, custoFixo: op.custoFixo, contribuicao: contribuicao, resultado: resultadoMes, caixa: caixa,
        estrutura: { profundidade: est.profundidade, linhas: est.linhas, largura: est.largura, porLinha: est.n,
                     niveis: est.niveis.slice(0, 12), titulos: tit.slice(0, 12) },
        titulos: contTit, tituloFundador: tFund, novoTituloFundador: tFund > tituloFundadorAnterior ? tFund : null,
        pbMedio: q, ativosPct: ativosPct, dezPct: dezPct, promo: mixAdesao[0] && mixAdesao[0].promo
      });
      if (tFund > tituloFundadorAnterior) tituloFundadorAnterior = tFund;
      resultado.meses.push(r);
    }

    var M = resultado.meses;
    resultado.total = {
      faturamento: soma(M.map(function (x) { return x.faturamento; })),
      bonus: soma(M.map(function (x) { return x.bonusTotal; })),
      resultado: soma(M.map(function (x) { return x.resultado; })),
      caixaFinal: M[M.length - 1].caixa,
      caixaMinimo: Math.min.apply(null, [-op.investimento].concat(M.map(function (x) { return x.caixa; }))),
      equilibrio: (M.filter(function (x) { return x.resultado >= 0; })[0] || {}).mes || null,
      retorno: (M.filter(function (x) { return x.caixa >= 0; })[0] || {}).mes || null
    };
    return resultado;

    function zero() { return { potencial: 0, elegivel: 0, projetado: 0, faixa: [0, 0] }; }
    function fixo(v) { return { potencial: v, elegivel: v, projetado: v, faixa: [v, v] }; }
  }

  // veredito herdado do simulador anterior: o que sobra do faturamento depois
  // de bonus e custos variaveis, no ultimo mes projetado
  function veredito(r) {
    var u = r.meses[r.meses.length - 1];
    var sobra = u.faturamento > 0 ? u.contribuicao / u.faturamento * 100 : 0;
    var payout = u.faturamento > 0 ? u.bonusTotal / u.faturamento * 100 : 0;
    var nivel = sobra >= 15 ? "fecha" : sobra >= 5 ? "limite" : sobra >= 0 ? "fragil" : "quebra";
    return { sobra: sobra, payout: payout, nivel: nivel };
  }

  // -------------------------------------------------------- verificacoes
  function verificacoes() {
    var cfg = planoPadrao(VERSAO_ATUAL), rede = cfg.bonus.rede.niveis, ini = cfg.bonus.inicio.niveis;
    var out = [];
    function ok(nome, obtido, esperado, nota) {
      out.push({ nome: nome, obtido: obtido, esperado: esperado, ok: Math.abs(obtido - esperado) < 0.005, nota: nota || "" });
    }
    ok("Pedido de 100 PB na tabela completa do SVD Rede", valorPedidoNaTabela(100, rede, 10, 1), 50);
    ok("Pedido de 50 PB, só os cinco primeiros níveis", valorPedidoNaTabela(50, rede, profundidadePorCompras([50], cfg.ativacao), 1), 18);
    ok("Pedido de 65 PB, dez níveis liberados", valorPedidoNaTabela(65, rede, profundidadePorCompras([65], cfg.ativacao), 1), 32.5);
    ok("Pedidos de 30 + 35 PB não somam para liberar níveis", profundidadePorCompras([30, 35], cfg.ativacao), 0,
       "a maior compra única (35 PB) fica abaixo de 50 PB");
    var promo = clone(cfg); promo.adesao.promo.ligada = true; promo.adesao.promo.inicio = 1; promo.adesao.promo.fim = 2;
    ok("Adesão promocional de 50 PB: SVD Início potencial", valorPedidoNaTabela(pbDaAdesao(promo, 1)[0].pb, ini, 10, 1), 42.5,
       "50 PB × 85%; vale só na vigência da campanha");
    ok("Campanha fora da vigência volta à adesão normal", pbDaAdesao(promo, 3)[0].pb, 80);
    ok("Adesão normal de 80 PB: SVD Início potencial", valorPedidoNaTabela(80, ini, 10, 1), 68);
    ok("Adesão normal de 100 PB: SVD Início potencial", valorPedidoNaTabela(100, ini, 10, 1), 85);
    ok("Adesão de 80 PB libera dez níveis", profundidadePorCompras([80], cfg.ativacao), 10);
    ok("Desconto de recompra no 12º mês do plano, sem ativação", descontoRecompra(1, 12, cfg), 50,
       "a falta de ativação tira bônus, não o desconto");
    ok("Desconto de recompra no 13º mês, sem renovar", descontoRecompra(1, 13, cfg), 0);
    var semPool = simular(cfg, "base", 12).meses[11];
    var comPoolCfg = clone(cfg); comPoolCfg.bonus.pool.cenarioAlternativo = true;
    var comPool = simular(comPoolCfg, "base", 12).meses[11];
    ok("Pool desligado no modelo inicial", semPool.bonus.pool.projetado, 0);
    ok("Com o pool, Carreira + Liderança + Conquistas saem (não somam)",
       comPool.bonus.carreira.projetado + comPool.bonus.lideranca.projetado + comPool.bonus.conquistas.projetado, 0);
    ok("Venda digital não entra na base de PB do SVD Rede",
       semPool.pbBonifRede - (simular(semDigital(cfg), "base", 12).meses[11].pbBonifRede), 0);
    // blocos do construtor, fora do Plano SVD
    var tres = clone(cfg.ativacao); tres.niveisCinco = 3;
    ok("Plano próprio: compra de 50 PB configurada para liberar 3 níveis", profundidadePorCompras([50], tres, 6), 3,
       "bloco de ativação do construtor; no Plano SVD são 5");
    ok("Binário: pernas de 1.000 e 600 PB a 10% pagam a perna menor", valorBinario(1000, 600, 10, 0, 1), 60,
       "bloco opcional, fora do Plano SVD");
    ok("Binário com teto de R$ 50 por consultor", valorBinario(1000, 600, 10, 50, 1), 50);
    return out;
    function semDigital(c) { var x = clone(c); x.operacao.consumidores = 0; return x; }
  }


  // ------------------------------------------------------------- modelos
  // Ponto de partida para a pessoa montar o plano dela. So as regras do plano
  // mudam; rede, custos e cenarios ficam como a pessoa deixou.
  var NOMES_SVD = { inicio: "SVD Início", rede: "SVD Rede", binario: "SVD Binário", carreira: "SVD Carreira",
    lideranca: "SVD Liderança", conquistas: "SVD Conquistas", link: "SVD Link", liderancaDigital: "SVD Liderança Digital",
    prime: "SVD Prime", pool: "SVD Pool" };
  var NOMES_GENERICOS = { inicio: "Bônus de início", rede: "Unilevel", binario: "Binário", carreira: "Bônus de carreira",
    lideranca: "Bônus de liderança", conquistas: "Fundo de conquistas", link: "Comissão do link", liderancaDigital: "Liderança digital",
    prime: "Assinatura", pool: "Pool" };
  var REGRAS = ["modelo", "nomePlano", "nomes", "adesao", "ativacao", "bonus", "titulos", "digital"];
  function regrasDoPlano(cfg) {
    var r = {};
    REGRAS.forEach(function (k) { r[k] = clone(cfg[k] === undefined ? null : cfg[k]); });
    return r;
  }
  function desligarTudo(B) {
    ["inicio", "rede", "binario", "carreira", "lideranca", "conquistas", "link", "liderancaDigital", "prime"].forEach(function (k) { B[k].ligado = false; });
    B.pool.cenarioAlternativo = false;
  }
  var MODELOS = {
    svd: { nome: "Plano SVD", resumo: "O plano completo: início e unilevel em 10 níveis, carreira, liderança, conquistas e vendas pelo link.",
      regras: function () { return regrasDoPlano(planoPadrao(VERSAO_ATUAL)); } },
    unilevel: { nome: "Unilevel simples", resumo: "Indicação direta, unilevel de 6 níveis, carreira com 4 títulos e comissão no link.",
      regras: function () {
        var r = regrasDoPlano(planoPadrao(VERSAO_ATUAL)), B = r.bonus;
        r.modelo = "unilevel"; r.nomePlano = "Plano unilevel"; r.nomes = clone(NOMES_GENERICOS);
        desligarTudo(B);
        B.inicio = { ligado: true, niveis: [25], exigeAtivacao: false };
        B.rede = { ligado: true, niveis: [8, 6, 5, 4, 3, 2], exigeAtivacao: true };
        B.carreira.ligado = true; B.link.ligado = true; B.link.taxa = 20;
        r.ativacao.niveisCinco = 3;
        r.titulos = r.titulos.slice(0, 4);
        B.lideranca.tituloLider = 1; B.conquistas.tituloMinimo = 2;
        return r;
      } },
    binario: { nome: "Binário", resumo: "Indicação direta, binário de 10% na perna menor com teto, residual de 4 níveis e fundo de conquistas.",
      regras: function () {
        var r = regrasDoPlano(planoPadrao(VERSAO_ATUAL)), B = r.bonus;
        r.modelo = "binario"; r.nomePlano = "Plano binário"; r.nomes = clone(NOMES_GENERICOS);
        r.nomes.rede = "Residual unilevel";
        desligarTudo(B);
        B.inicio = { ligado: true, niveis: [20], exigeAtivacao: false };
        B.binario = { ligado: true, taxa: 10, teto: 3000, equilibrio: 50 };
        B.rede = { ligado: true, niveis: [4, 3, 2, 1], exigeAtivacao: true };
        B.conquistas = { ligado: true, fundo: 3, tituloMinimo: 2 };
        B.link.ligado = true; B.link.taxa = 15;
        r.ativacao.niveisCinco = 2;
        r.titulos = r.titulos.slice(0, 5);
        B.lideranca.tituloLider = 1;
        return r;
      } },
    zero: { nome: "Do zero", resumo: "Todas as regras desligadas. Ligue uma a uma e veja o efeito de cada bônus.",
      regras: function () {
        var r = regrasDoPlano(planoPadrao(VERSAO_ATUAL));
        r.modelo = "zero"; r.nomePlano = "Meu plano"; r.nomes = clone(NOMES_GENERICOS);
        desligarTudo(r.bonus);
        return r;
      } }
  };
  function aplicarModelo(cfg, nome) {
    var out = clone(cfg), r = (MODELOS[nome] || MODELOS.svd).regras();
    Object.keys(r).forEach(function (k) { out[k] = r[k]; });
    return normalizar(out);
  }
  function planoMudou(cfg) {
    var m = MODELOS[cfg.modelo] || MODELOS.svd;
    return diferenca(m.regras(), regrasDoPlano(cfg)) !== undefined;
  }
  function nomeBonus(cfg, k) {
    var n = cfg.nomes && cfg.nomes[k] && String(cfg.nomes[k]).trim();
    return n || (cfg.modelo && cfg.modelo !== "svd" ? NOMES_GENERICOS[k] : NOMES_SVD[k]) || k;
  }
  // indices e tamanhos dentro do que o motor e a tela suportam
  var LIMITES = { niveis: 15, geracoes: 6, titulos: 8, kits: 4 };
  function normalizar(cfg) {
    var B = cfg.bonus, t = Math.max(1, cfg.titulos.length);
    if (!cfg.nomes || typeof cfg.nomes !== "object") cfg.nomes = {};
    if (!B.binario) B.binario = clone(PLANOS[VERSAO_ATUAL].bonus.binario);
    B.lideranca.tituloLider = lim(Math.round(+B.lideranca.tituloLider || 0), 0, t - 1);
    B.conquistas.tituloMinimo = lim(Math.round(+B.conquistas.tituloMinimo || 0), 0, t - 1);
    cfg.ativacao.niveisCinco = lim(Math.round(+cfg.ativacao.niveisCinco || 5), 1, LIMITES.niveis);
    return cfg;
  }
  // o que um pedido de 100 PB distribui, no maximo, pela tabela de cada bonus
  function tetoPorPedido(cfg, tipo) {
    var B = cfg.bonus, pool = !!B.pool.cenarioAlternativo, itens = [];
    var bonif = (tipo === "adesao" ? cfg.pbBonifSobreQual.adesao : cfg.pbBonifSobreQual.recompra) / 100;
    function add(k, taxa) { if (taxa > 0) itens.push({ k: k, valor: 100 * bonif * taxa / 100 * cfg.economia.valorPB }); }
    if (tipo === "adesao" && B.inicio.ligado) add("inicio", soma(B.inicio.niveis));
    if (tipo !== "adesao" && B.rede.ligado) add("rede", soma(B.rede.niveis));
    if (B.carreira.ligado && !pool) add("carreira", Math.max.apply(null, cfg.titulos.map(function (x) { return +x.taxa || 0; }).concat([0])));
    if (B.lideranca.ligado && !pool) add("lideranca", soma(B.lideranca.geracoes));
    if (B.conquistas.ligado && !pool) add("conquistas", B.conquistas.fundo);
    if (pool) add("pool", B.pool.fundo);
    var total = soma(itens.map(function (i) { return i.valor; })), pago = 100 * cfg.economia.reaisPorPB;
    return { itens: itens, total: total, pago: pago, pct: pago > 0 ? total / pago * 100 : 0, binario: !!B.binario.ligado };
  }

  var SIM = {
    PLANOS: PLANOS, VERSAO_ATUAL: VERSAO_ATUAL, planoPadrao: planoPadrao, diferenca: diferenca, aplicar: aplicar,
    simular: simular, veredito: veredito, verificacoes: verificacoes, estrutura: estrutura,
    tituloAlcancado: tituloAlcancado, volumeQualificado: volumeQualificado, valorPedidoNaTabela: valorPedidoNaTabela,
    profundidadePorCompras: profundidadePorCompras, operacaoDoCenario: operacaoDoCenario, valorBinario: valorBinario,
    MODELOS: MODELOS, aplicarModelo: aplicarModelo, planoMudou: planoMudou, regrasDoPlano: regrasDoPlano,
    nomeBonus: nomeBonus, normalizar: normalizar, LIMITES: LIMITES, tetoPorPedido: tetoPorPedido
  };
  if (typeof module !== "undefined" && module.exports) module.exports = SIM;
  raiz.SIM = SIM;
})(typeof window !== "undefined" ? window : globalThis);
