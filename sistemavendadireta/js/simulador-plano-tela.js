/*
 * Simulador do Plano SVD — tela. Le e escreve a configuracao; quem calcula e o
 * window.SIM (simulador-plano.js). Sistema Venda Direta · 2026-11-26
 */
(function () {
  "use strict";
  var SIM = window.SIM;
  if (!SIM) return;

  // ------------------------------------------------------------ utilidades
  function $(s, el) { return (el || document).querySelector(s); }
  function $$(s, el) { return Array.prototype.slice.call((el || document).querySelectorAll(s)); }
  var fBRL = new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL", maximumFractionDigits: 0 });
  var fBRL2 = new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL", minimumFractionDigits: 2, maximumFractionDigits: 2 });
  var fInt = new Intl.NumberFormat("pt-BR", { maximumFractionDigits: 0 });
  var fDec = new Intl.NumberFormat("pt-BR", { maximumFractionDigits: 1 });
  function brl(v) { return fBRL.format(Math.round(v || 0)); }
  function brl2(v) { return fBRL2.format(v || 0); }
  function int(v) { return fInt.format(Math.round(v || 0)); }
  function pct(v) { return fDec.format(v || 0) + "%"; }
  function compacto(v) {
    var a = Math.abs(v), s = v < 0 ? "−" : "";
    if (a >= 1e6) return s + "R$ " + fDec.format(a / 1e6) + " mi";
    if (a >= 1e4) return s + "R$ " + fInt.format(a / 1e3) + " mil";
    return s + fBRL.format(Math.round(a));
  }
  function esc(t) { return String(t == null ? "" : t).replace(/[&<>"']/g, function (c) { return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]; }); }
  function evento(nome, dados) { try { if (window.gtag) window.gtag("event", nome, dados || {}); } catch (e) {} }

  var CHAVE = "svd-simulador";
  var CENARIOS = [["conservador", "Conservador"], ["base", "Base"], ["expansao", "Expansão"]];
  var ORDEM = ["inicio", "rede", "carreira", "lideranca", "conquistas", "link", "liderancaDigital", "pool"];
  var NOMES = { inicio: "SVD Início", rede: "SVD Rede", carreira: "SVD Carreira", lideranca: "SVD Liderança",
    conquistas: "SVD Conquistas", link: "SVD Link", liderancaDigital: "SVD Liderança Digital", pool: "SVD Pool", prime: "SVD Prime" };
  var COR = { inicio: "var(--s1)", rede: "var(--s2)", carreira: "var(--s3)", lideranca: "var(--s4)", conquistas: "var(--s5)",
    link: "var(--s6)", liderancaDigital: "var(--s7)", pool: "var(--s8)" };
  var COR_CENARIO = { conservador: "var(--s2)", base: "var(--s1)", expansao: "var(--s3)" };
  var NOME_CENARIO = { conservador: "Conservador", base: "Base", expansao: "Expansão" };

  var estado = { cfg: null, cenario: "base", mes: 12, aba: "rede", res: {}, usou: false, origem: "padrao" };

  // ------------------------------------------------------ carregar / salvar
  function b64enc(s) { return btoa(unescape(encodeURIComponent(s))).replace(/\+/g, "-").replace(/\//g, "_").replace(/=+$/, ""); }
  function b64dec(s) { s = s.replace(/-/g, "+").replace(/_/g, "/"); while (s.length % 4) s += "="; return decodeURIComponent(escape(atob(s))); }
  function pacote() {
    return { v: estado.cfg.versao, c: estado.cenario, d: SIM.diferenca(SIM.planoPadrao(estado.cfg.versao), estado.cfg) || {} };
  }
  function abrirPacote(p) {
    var cfg = SIM.aplicar(SIM.planoPadrao(p.v), p.d || {});
    cfg.versao = SIM.PLANOS[p.v] ? p.v : SIM.VERSAO_ATUAL;
    estado.cenario = SIM.PLANOS[p.v] && ["conservador", "base", "expansao"].indexOf(p.c) >= 0 ? p.c : "base";
    return cfg;
  }
  function carregar() {
    var cfg = null;
    var m = location.hash.match(/[#&]p=([A-Za-z0-9_-]+)/);
    if (m) { try { cfg = abrirPacote(JSON.parse(b64dec(m[1]))); estado.origem = "link"; } catch (e) { cfg = null; } }
    if (!cfg) {
      try {
        var s = JSON.parse(localStorage.getItem(CHAVE) || "null");
        if (s && s.v) { cfg = abrirPacote(s); estado.origem = "salvo"; }
      } catch (e) { cfg = null; }
    }
    estado.cfg = cfg || SIM.planoPadrao();
  }
  function linkCompartilhavel() { return location.origin + location.pathname + "#p=" + b64enc(JSON.stringify(pacote())); }
  function aviso(txt) {
    var t = document.createElement("div");
    t.className = "sim-toast nao-imprime"; t.setAttribute("role", "status"); t.textContent = txt;
    document.body.appendChild(t);
    setTimeout(function () { t.remove(); }, 2600);
  }

  // ------------------------------------------------------- caminho na cfg
  function obter(path) { return path.split(".").reduce(function (o, k) { return o == null ? o : o[k]; }, estado.cfg); }
  function definir(path, v) {
    var ks = path.split("."), o = estado.cfg;
    for (var i = 0; i < ks.length - 1; i++) o = o[ks[i]];
    o[ks[ks.length - 1]] = v;
  }
  function campo(path, rotulo, opc) {
    opc = opc || {};
    var v = obter(path);
    var ajuda = opc.ajuda ? '<small class="ajuda">' + esc(opc.ajuda) + "</small>" : "";
    var un = opc.un ? "<small>" + esc(opc.un) + "</small>" : "";
    if (opc.tipo === "check") {
      return '<div><label class="sim-check"><input type="checkbox" data-path="' + path + '"' + (v ? " checked" : "") + "> " + esc(rotulo) + "</label>" + ajuda + "</div>";
    }
    if (opc.tipo === "select") {
      return '<label class="sim-campo"><span>' + esc(rotulo) + un + '</span><select class="sim-in" data-path="' + path + '"' +
        (opc.texto ? ' data-texto="1"' : "") + ">" + opc.opcoes.map(function (o) {
          return '<option value="' + esc(o[0]) + '"' + (String(o[0]) === String(v) ? " selected" : "") + ">" + esc(o[1]) + "</option>";
        }).join("") + "</select>" + ajuda + "</label>";
    }
    if (opc.tipo === "texto") {
      return '<label class="sim-campo"><span>' + esc(rotulo) + '</span><input class="sim-in" data-texto="1" data-path="' + path + '" value="' + esc(v) + '" maxlength="80">' + ajuda + "</label>";
    }
    return '<label class="sim-campo"><span>' + esc(rotulo) + un + '</span><input class="sim-in" type="number" inputmode="decimal" data-path="' + path + '" value="' + esc(v) + '"' +
      (opc.min != null ? ' min="' + opc.min + '"' : "") + (opc.max != null ? ' max="' + opc.max + '"' : "") + ' step="' + (opc.step || "any") + '">' + ajuda + "</label>";
  }
  function tabelaNiveis(path, rot, un) {
    var arr = obter(path);
    return '<div class="sim-niveis">' + arr.map(function (v, i) {
      return '<label><span>' + rot + " " + (i + 1) + '</span><input class="sim-in" type="number" step="any" min="0" data-path="' + path + "." + i + '" value="' + v + '" aria-label="' + rot + " " + (i + 1) + " " + (un || "") + '"></label>';
    }).join("") + "</div>";
  }
  function opcoesTitulos() { return estado.cfg.titulos.map(function (t, i) { return [i, t.nome]; }); }

  // --------------------------------------------------------- calculo
  var timer = null;
  function calcular() { CENARIOS.forEach(function (c) { estado.res[c[0]] = SIM.simular(estado.cfg, c[0], 12); }); }
  function atual() { return estado.res[estado.cenario]; }
  function mesSel() { return atual().meses[estado.mes - 1]; }
  function agendar() { clearTimeout(timer); timer = setTimeout(atualizar, 120); }

  document.addEventListener("input", aoMudar);
  document.addEventListener("change", aoMudar);
  function aoMudar(ev) {
    var el = ev.target;
    if (!el || !el.dataset || !el.dataset.path) return;
    if (ev.type === "input" && el.type === "checkbox") return;
    var v;
    if (el.type === "checkbox") v = el.checked;
    else if (el.dataset.texto) v = el.value;
    else {
      v = parseFloat(String(el.value).replace(",", "."));
      if (!isFinite(v)) return;
    }
    definir(el.dataset.path, v);
    if (!estado.usou) { estado.usou = true; evento("simulador_plano_uso"); }
    if (el.dataset.path === "empresa") { renderTitulosEmpresa(); }
    if (el.dataset.estrutural) { montarBonus(); }
    agendar();
  }

  // ------------------------------------------------------------- hero
  function renderTitulosEmpresa() {
    var e = (estado.cfg.empresa || "").trim();
    $$(".sim-nome-empresa").forEach(function (n) { n.textContent = e || "sua empresa"; });
  }
  function renderHero() {
    var r = atual(), u = r.meses[11];
    $("#k-ativos").textContent = int(u.ativos);
    $("#k-ativos-sub").textContent = int(u.consumidores) + " clientes comprando pelo link";
    $("#k-fat").textContent = compacto(u.faturamento);
    $("#k-fat-sub").textContent = compacto(r.total.faturamento) + " no ano";
    $("#k-bonus").textContent = compacto(r.total.bonus);
    $("#k-bonus-sub").textContent = pct(r.total.faturamento ? r.total.bonus / r.total.faturamento * 100 : 0) + " do faturamento";
    $("#k-caixa").textContent = compacto(r.total.caixaFinal);
    $("#k-caixa-sub").textContent = r.total.equilibrio ? "resultado positivo a partir do mês " + r.total.equilibrio : "ainda sem mês positivo em 12 meses";
    $$("[data-cenario]").forEach(function (b) { b.setAttribute("aria-pressed", String(b.dataset.cenario === estado.cenario)); });
  }

  // --------------------------------------------------------- graficos
  function ticks(max) {
    if (max <= 0) return [0];
    var p = Math.pow(10, Math.floor(Math.log10(max))), passo = [1, 2, 2.5, 5, 10].map(function (k) { return k * p; })
      .filter(function (s) { return max / s <= 5; })[0] || p * 10;
    var t = []; for (var v = 0; v <= max + passo * 0.001; v += passo) t.push(v);
    if (t[t.length - 1] < max) t.push(t[t.length - 1] + passo);
    return t;
  }
  function dica(cont, html, x, y) {
    var d = $(".sim-dica", cont);
    if (!d) { d = document.createElement("div"); d.className = "sim-dica"; cont.appendChild(d); }
    if (!html) { d.style.display = "none"; return; }
    d.innerHTML = html; d.style.display = "block";
    var w = d.offsetWidth, W = cont.clientWidth;
    d.style.left = Math.max(4, Math.min(W - w - 4, x + 14)) + "px";
    d.style.top = Math.max(4, y - 10) + "px";
  }
  // desenha na largura real: viewBox fixo esticava o texto no desktop e encolhia no celular
  // no PDF (A4 paisagem) o layout de impressao ainda nao existe quando o beforeprint
  // dispara: a largura de cada grafico vem fixa daqui
  var LARGURA_IMPRESSAO = { "g-bonus": 940, "g-arvore": 430, "g-caixa": 900 };
  var imprimindo = false;
  function largura(cont) {
    if (imprimindo) return LARGURA_IMPRESSAO[cont.id] || 430;
    var w = cont.clientWidth - 24; return w > 200 ? Math.round(w) : 720;
  }
  function legenda(itens, linha) {
    return '<ul class="sim-legenda">' + itens.map(function (i) {
      return '<li><i class="' + (linha ? "linha" : "") + '" style="background:' + i[1] + '"></i>' + esc(i[0]) + "</li>";
    }).join("") + "</ul>";
  }

  // colunas empilhadas: bonus por origem, mes a mes
  function grafBonus(cont, meses) {
    var series = ORDEM.filter(function (k) { return meses.some(function (m) { return m.bonus[k].projetado > 0.5; }); });
    var W = largura(cont), H = W < 480 ? 220 : 260, L = 56, R = 8, T = 18, Bm = 28;
    var max = Math.max.apply(null, meses.map(function (m) { return m.bonusTotal; }).concat([1]));
    var tk = ticks(max), top = tk[tk.length - 1];
    var banda = (W - L - R) / meses.length, larg = Math.min(24, banda * 0.62);
    var y = function (v) { return T + (H - T - Bm) * (1 - v / top); };
    var s = '<svg viewBox="0 0 ' + W + " " + H + '" role="img" aria-label="Bônus projetados por origem, mês a mês">';
    tk.forEach(function (t) {
      s += '<line class="grade" x1="' + L + '" x2="' + (W - R) + '" y1="' + y(t) + '" y2="' + y(t) + '"/>' +
        '<text class="eixo" x="' + (L - 6) + '" y="' + (y(t) + 4) + '" text-anchor="end">' + compacto(t) + "</text>";
    });
    meses.forEach(function (m, i) {
      var x = L + banda * i + (banda - larg) / 2, base = 0;
      var visiveis = series.filter(function (k) { return m.bonus[k].projetado > 0.5; });
      visiveis.forEach(function (k, j) {
        var v = m.bonus[k].projetado, y0 = y(base), y1 = y(base + v), h = Math.max(0, y0 - y1 - (j > 0 ? 2 : 0));
        var yt = y1, ultimo = j === visiveis.length - 1, r = ultimo ? Math.min(4, h) : 0;
        if (h > 0) {
          s += '<path fill="' + COR[k] + '" d="M' + x + "," + (yt + h) + "V" + (yt + r) + "Q" + x + "," + yt + " " + (x + r) + "," + yt +
            "H" + (x + larg - r) + "Q" + (x + larg) + "," + yt + " " + (x + larg) + "," + (yt + r) + "V" + (yt + h) + 'Z"/>';
        }
        base += v;
      });
      s += '<text class="eixo" x="' + (L + banda * i + banda / 2) + '" y="' + (H - 8) + '" text-anchor="middle">' + m.mes + "</text>";
      s += '<rect data-i="' + i + '" x="' + (L + banda * i) + '" y="' + T + '" width="' + banda + '" height="' + (H - T - Bm) + '" fill="transparent"/>';
    });
    var u = meses[meses.length - 1];
    s += '<text class="rotulo" x="' + (L + banda * (meses.length - 0.5)) + '" y="' + (y(u.bonusTotal) - 6) + '" text-anchor="middle">' + compacto(u.bonusTotal) + "</text>";
    s += "</svg>";
    cont.innerHTML = legenda(series.map(function (k) { return [NOMES[k], COR[k]]; })) + s;
    $$("rect[data-i]", cont).forEach(function (r) {
      r.addEventListener("mousemove", function (ev) {
        var m = meses[+r.dataset.i], b = cont.getBoundingClientRect();
        var html = "<b>Mês " + m.mes + " · " + brl(m.bonusTotal) + "</b>" + series.slice().reverse().map(function (k) {
          return m.bonus[k].projetado > 0.5 ? '<div><span><i style="background:' + COR[k] + '"></i>' + NOMES[k] + "</span><span>" + brl(m.bonus[k].projetado) + "</span></div>" : "";
        }).join("");
        dica(cont, html, ev.clientX - b.left, ev.clientY - b.top);
      });
      r.addEventListener("mouseleave", function () { dica(cont, ""); });
    });
  }

  // linhas: caixa acumulado por cenario
  function grafCaixa(cont) {
    var W = largura(cont), estreito = W < 520, H = estreito ? 220 : 260, L = 64, R = estreito ? 14 : 150, T = 14, Bm = 28;
    var todos = [];
    CENARIOS.forEach(function (c) { estado.res[c[0]].meses.forEach(function (m) { todos.push(m.caixa); }); });
    var inv = -estado.res.base.op.investimento;
    todos.push(inv, 0);
    var mx = Math.max.apply(null, todos), mn = Math.min.apply(null, todos);
    var tk = ticks(Math.max(Math.abs(mx), Math.abs(mn))), passo = tk[1] || 1;
    var top = Math.ceil(mx / passo) * passo, bot = Math.floor(mn / passo) * passo;
    if (top === bot) top = bot + passo;
    var y = function (v) { return T + (H - T - Bm) * (top - v) / (top - bot); };
    var x = function (i) { return L + (W - L - R) * i / 12; };
    var s = '<svg viewBox="0 0 ' + W + " " + H + '" role="img" aria-label="Caixa acumulado por cenário">';
    for (var v = bot; v <= top + passo * 0.001; v += passo) {
      s += '<line class="' + (Math.abs(v) < 1e-6 ? "zero" : "grade") + '" x1="' + L + '" x2="' + (W - R) + '" y1="' + y(v) + '" y2="' + y(v) + '"/>' +
        '<text class="eixo" x="' + (L - 6) + '" y="' + (y(v) + 4) + '" text-anchor="end">' + compacto(v) + "</text>";
    }
    for (var i = 0; i <= 12; i += 2) s += '<text class="eixo" x="' + x(i) + '" y="' + (H - 8) + '" text-anchor="middle">' + (i === 0 ? "início" : i) + "</text>";
    var finais = [];
    CENARIOS.forEach(function (c) {
      var pts = [[x(0), y(inv)]].concat(estado.res[c[0]].meses.map(function (m, k) { return [x(k + 1), y(m.caixa)]; }));
      s += '<polyline fill="none" stroke="' + COR_CENARIO[c[0]] + '" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" points="' + pts.map(function (p) { return p.join(","); }).join(" ") + '"/>';
      var f = pts[pts.length - 1];
      s += '<circle cx="' + f[0] + '" cy="' + f[1] + '" r="4" fill="' + COR_CENARIO[c[0]] + '" stroke="var(--sim-surface)" stroke-width="2"/>';
      finais.push({ y: f[1], txt: c[1] + " " + compacto(estado.res[c[0]].total.caixaFinal) });
    });
    finais.sort(function (a, b) { return a.y - b.y; });
    var colidem = finais.some(function (f, k) { return k > 0 && f.y - finais[k - 1].y < 14; });
    if (!colidem && !estreito) finais.forEach(function (f) { s += '<text class="rotulo" x="' + (W - R + 8) + '" y="' + (f.y + 4) + '">' + esc(f.txt) + "</text>"; });
    s += '<line class="cruz" x1="0" x2="0" y1="' + T + '" y2="' + (H - Bm) + '" stroke="rgba(255,255,255,.4)" stroke-width="1" style="display:none"/>';
    s += '<rect class="alvo" x="' + L + '" y="' + T + '" width="' + (W - L - R) + '" height="' + (H - T - Bm) + '" fill="transparent"/>';
    s += "</svg>";
    cont.innerHTML = legenda(CENARIOS.map(function (c) { return [c[1], COR_CENARIO[c[0]]]; }), true) + s;
    var svg = $("svg", cont), cruz = $(".cruz", cont);
    $(".alvo", cont).addEventListener("mousemove", function (ev) {
      var b = svg.getBoundingClientRect(), px = (ev.clientX - b.left) * W / b.width;
      var k = Math.max(0, Math.min(12, Math.round((px - L) / ((W - L - R) / 12))));
      cruz.setAttribute("x1", x(k)); cruz.setAttribute("x2", x(k)); cruz.style.display = "";
      var c2 = cont.getBoundingClientRect();
      dica(cont, "<b>" + (k === 0 ? "Início (investimento)" : "Mês " + k) + "</b>" + CENARIOS.map(function (c) {
        var val = k === 0 ? inv : estado.res[c[0]].meses[k - 1].caixa;
        return '<div><span><i style="background:' + COR_CENARIO[c[0]] + '"></i>' + c[1] + "</span><span>" + brl(val) + "</span></div>";
      }).join(""), ev.clientX - c2.left, ev.clientY - c2.top);
    });
    $(".alvo", cont).addEventListener("mouseleave", function () { cruz.style.display = "none"; dica(cont, ""); });
  }

  // -------------------------------------------------------- rede (arvore)
  function corTitulo(i) { return "var(--t" + (i + 1) + ")"; }
  function renderArvore(cont, m) {
    var e = m.estrutura, tit = e.titulos, cfg = estado.cfg;
    var linhasMostrar = Math.min(e.niveis.length, 5);
    var W = largura(cont), estreito = W < 520, rowH = estreito ? 56 : 64, H = linhasMostrar * rowH + 30;
    var L = estreito ? 62 : 96, R = estreito ? 112 : 200, maxNos = estreito ? 7 : 14;
    var s = '<svg viewBox="0 0 ' + W + " " + H + '" role="img" aria-label="Árvore ilustrativa de uma linha fundadora">';
    var prev = null;
    for (var d = 0; d < linhasMostrar; d++) {
      var qtd = e.niveis[d], mostrar = Math.max(1, Math.min(Math.round(qtd), d === 0 ? 1 : maxNos));
      var yy = 22 + d * rowH, pos = [];
      for (var i = 0; i < mostrar; i++) pos.push(L + (W - L - R) * (mostrar === 1 ? 0.5 : i / (mostrar - 1)));
      if (prev) pos.forEach(function (px, i) {
        var pai = prev[Math.min(prev.length - 1, Math.floor(i * prev.length / pos.length))];
        s += '<line x1="' + pai + '" y1="' + (yy - rowH + 9) + '" x2="' + px + '" y2="' + (yy - 9) + '" stroke="rgba(255,255,255,.22)" stroke-width="1"/>';
      });
      var cor = tit[d] >= 0 ? corTitulo(tit[d]) : "var(--t0)";
      pos.forEach(function (px) { s += '<circle cx="' + px + '" cy="' + yy + '" r="' + (d === 0 ? 9 : 6) + '" fill="' + cor + '" stroke="var(--sim-surface)" stroke-width="2"/>'; });
      s += '<text x="8" y="' + (yy + 4) + '" class="forte">' + (d === 0 ? "Fundador" : "Nível " + d) + "</text>";
      s += '<text x="' + (W - R + 12) + '" y="' + (yy - 2) + '" class="forte">' + int(qtd) + (estreito ? "" : (qtd >= 1.5 ? " consultores" : " consultor")) + "</text>";
      s += '<text x="' + (W - R + 12) + '" y="' + (yy + 14) + '">' + (tit[d] >= 0 ? esc(cfg.titulos[tit[d]].nome) : "sem título") + "</text>";
      prev = pos;
    }
    var resto = 0; for (var k = linhasMostrar; k < e.niveis.length; k++) resto += e.niveis[k];
    if (resto > 0.5) s += '<text x="' + L + '" y="' + (H - 4) + '">+ ' + int(resto) + " consultores em mais " + (e.niveis.length - linhasMostrar) + " nível(is)</text>";
    s += "</svg>";
    var presentes = []; tit.forEach(function (t) { if (presentes.indexOf(t) < 0) presentes.push(t); });
    presentes.sort(function (a, b) { return a - b; });
    cont.innerHTML = legenda(presentes.map(function (t) { return [t >= 0 ? cfg.titulos[t].nome : "Sem título", t >= 0 ? corTitulo(t) : "var(--t0)"]; })) + s;
  }

  function renderRede() {
    var m = mesSel(), r = atual(), cfg = estado.cfg;
    $$(".sim-mes-rot").forEach(function (n) { n.textContent = "mês " + estado.mes; });
    $("#r-ativos").textContent = int(m.ativos);
    $("#r-ativos-sub").textContent = int(m.adesoes) + " adesões · " + int(m.saem) + " saídas no mês";
    $("#r-pedidos").textContent = int(m.pedidos);
    $("#r-pedidos-sub").textContent = int(m.pedDigital) + " de clientes pelo link";
    $("#r-pb").textContent = int(m.pbQual) + " PB";
    $("#r-pb-sub").textContent = int(m.pbBonifTotal) + " PB bonificáveis";
    $("#r-elig").textContent = pct(m.ativosPct * 100);
    $("#r-elig-sub").textContent = pct(m.dezPct * 100) + " com dez níveis liberados";
    $("#r-estrutura").textContent = m.estrutura.linhas + " linhas fundadoras · " + fDec.format(m.estrutura.largura) +
      " indicados ativos por consultor · " + m.estrutura.profundidade + " níveis abaixo do fundador";
    renderArvore($("#g-arvore"), m);
    // titulos
    var tit = m.titulos.map(function (q, i) { return q >= 0.5 ? '<li><i style="background:' + corTitulo(i) + '"></i>' + esc(cfg.titulos[i].nome) + ": " + int(q) + "</li>" : ""; }).join("");
    $("#r-titulos").innerHTML = tit || "<li>Nenhum título neste mês: a rede ainda não fecha a meta de Bronze (" + int(cfg.titulos[0].meta) + " PB).</li>";
    // marcos do fundador
    var marcos = r.meses.filter(function (x) { return x.novoTituloFundador != null && x.novoTituloFundador >= 0; });
    $("#r-marcos").innerHTML = marcos.length ? marcos.map(function (x) {
      return '<div class="sim-marco"><b>Mês ' + x.mes + "</b><span>Fundador chega a <strong>" + esc(cfg.titulos[x.novoTituloFundador].nome) + "</strong></span></div>";
    }).join("") : '<p class="sim-aviso">Em 12 meses o fundador ainda não fecha a meta de Bronze neste cenário. Tente mais cadastros ou mais indicados ativos por consultor.</p>';
    var u = r.meses[11], prox = u.tituloFundador + 1;
    if (prox < cfg.titulos.length) {
      var t = cfg.titulos[prox], e = u.estrutura, q = u.pbMedio;
      var f = e.niveis.length > 1 ? e.niveis[1] / e.niveis[0] : 0;
      var linhaVol = 0; for (var k = 1; k < e.niveis.length; k++) linhaVol += e.niveis[k];
      linhaVol = f > 0 ? linhaVol / f * q : 0;
      var qv = q + f * Math.min(linhaVol, t.vml);
      $("#r-proximo").innerHTML = "Próxima conquista do fundador: <strong>" + esc(t.nome) + "</strong> — " + int(qv) + " de " + int(t.meta) +
        " PB qualificados no mês 12 (faltam " + int(Math.max(0, t.meta - qv)) + " PB; cada linha conta no máximo " + int(t.vml) + " PB).";
    } else $("#r-proximo").textContent = "O fundador está no título mais alto do plano.";
    grafBonus($("#g-bonus"), r.meses);
    tabelaBonusMes();
  }

  function tabelaBonusMes() {
    var r = atual(), linhas = ORDEM.filter(function (k) { return r.meses.some(function (m) { return m.bonus[k].projetado > 0.5; }); });
    var h = '<table class="sim-tabela"><thead><tr><th>Bônus</th>' + r.meses.map(function (m) { return "<th>Mês " + m.mes + "</th>"; }).join("") + "<th>Ano</th></tr></thead><tbody>";
    linhas.forEach(function (k) {
      var tot = 0;
      h += "<tr><td>" + NOMES[k] + "</td>" + r.meses.map(function (m) { tot += m.bonus[k].projetado; return "<td>" + brl(m.bonus[k].projetado) + "</td>"; }).join("") + "<td>" + brl(tot) + "</td></tr>";
    });
    h += '<tr class="total"><td>Total</td>' + r.meses.map(function (m) { return "<td>" + brl(m.bonusTotal) + "</td>"; }).join("") + "<td>" + brl(r.total.bonus) + "</td></tr></tbody></table>";
    $("#t-bonus").innerHTML = h;
  }

  // calculadora de carreira: a pessoa testa a propria rede
  function renderCarreira() {
    var cfg = estado.cfg, pessoal = parseFloat($("#c-pessoal").value) || 0;
    var compra = parseFloat($("#c-compra").value) || 0;
    var linhas = $$("#c-linhas input").map(function (i) { return parseFloat(i.value) || 0; });
    var t = SIM.tituloAlcancado(pessoal, linhas, cfg.titulos);
    var prof = SIM.profundidadePorCompras([compra], cfg.ativacao);
    var out = '<p class="sim-aviso" style="margin-top:0">Título no mês: <strong style="color:var(--sim-text)">' + (t >= 0 ? esc(cfg.titulos[t].nome) : "ainda sem título") + "</strong>" +
      (t >= 0 ? " · SVD Carreira de " + pct(cfg.titulos[t].taxa) + " pelo diferencial" : "") + "</p>";
    out += '<p class="sim-aviso">Sua maior compra única do mês (' + int(compra) + " PB) " + (prof ? "libera <strong style=\"color:var(--sim-text)\">" + prof + " níveis</strong> do SVD Rede." : "não libera níveis do SVD Rede: precisa de uma compra única de " + cfg.ativacao.pbCinco + " PB.") + "</p>";
    var prox = t + 1;
    if (prox < cfg.titulos.length) {
      var alvo = cfg.titulos[prox], qv = SIM.volumeQualificado(pessoal, linhas, alvo), falta = Math.max(0, alvo.meta - qv);
      var cortadas = linhas.filter(function (v) { return v > alvo.vml; }).length;
      out += '<div style="margin-top:12px"><div style="display:flex;justify-content:space-between;font-size:.85rem;margin-bottom:6px"><span>Caminho até <strong>' + esc(alvo.nome) + "</strong></span><span>" + int(qv) + " / " + int(alvo.meta) + " PB</span></div>" +
        '<div class="sim-medidor" role="meter" aria-valuemin="0" aria-valuemax="' + alvo.meta + '" aria-valuenow="' + Math.round(qv) + '"><i style="width:' + Math.min(100, qv / alvo.meta * 100) + '%"></i></div>' +
        '<p class="sim-aviso">Faltam ' + int(falta) + " PB qualificados. " + (cortadas ? cortadas + " linha(s) passam do VML de " + int(alvo.vml) + " PB e contam só até esse limite: o caminho é crescer outra linha." : "Nenhuma linha passa do VML de " + int(alvo.vml) + " PB.") + "</p></div>";
    } else out += '<p class="sim-aviso">Título mais alto do plano.</p>';
    $("#c-saida").innerHTML = out;
  }

  // ------------------------------------------------------------- saude
  var TEXTO_VEREDITO = {
    fecha: ["O plano fecha a conta", "sobram {s} do faturamento depois de bônus, produto, impostos, frete e operação. Há folga para devolução, inadimplência e meses fracos."],
    limite: ["Fecha, mas no limite", "sobram só {s}. Funciona enquanto tudo corre bem; um mês de devolução acima do previsto já come a margem."],
    fragil: ["Não se sustenta", "sobram {s}, na prática zero. O plano só se paga se a rede crescer todo mês, e nenhuma rede cresce para sempre."],
    quebra: ["O plano quebra", "o plano paga {s} a mais do que a margem comporta: cada pedido aumenta o prejuízo."]
  };
  function renderSaude() {
    var r = atual(), v = SIM.veredito(r), t = TEXTO_VEREDITO[v.nivel], tot = r.total;
    var box = $("#s-veredito");
    box.className = "sim-veredito " + v.nivel;
    var icone = { fecha: "✓", limite: "!", fragil: "!", quebra: "✕" }[v.nivel];
    box.innerHTML = '<p class="sim-aviso" style="margin:0">Mês 12 · cenário ' + NOME_CENARIO[estado.cenario] + '</p><div class="tit"><span aria-hidden="true">' + icone + "</span>" + t[0] + "</div>" +
      "<p>No mês 12, os bônus projetados somam " + pct(v.payout) + " do faturamento e " + t[1].replace("{s}", pct(Math.abs(v.sobra))) + "</p>" +
      "<p>" + (tot.equilibrio ? "A operação passa a dar resultado no mês " + tot.equilibrio + (tot.retorno ? " e recupera o investimento no mês " + tot.retorno + "." : "; o investimento ainda não volta em 12 meses.") :
        "A operação não chega a resultado positivo em 12 meses: com esse volume, o custo fixo e a aquisição pesam mais que a margem. Aqui o problema é escala, não o plano.") +
      " A maior necessidade de caixa é de " + brl(Math.abs(Math.min(0, tot.caixaMinimo))) + ".</p>";
    // comparacao de cenarios
    var linhas = [
      ["Faturamento no ano", function (x) { return brl(x.total.faturamento); }],
      ["Bônus projetados no ano", function (x) { return brl(x.total.bonus); }],
      ["Bônus ÷ faturamento", function (x) { return pct(x.total.faturamento ? x.total.bonus / x.total.faturamento * 100 : 0); }],
      ["Consultores ativos no mês 12", function (x) { return int(x.meses[11].ativos); }],
      ["Resultado no ano", function (x) { return brl(x.total.resultado); }],
      ["Caixa no fim do ano", function (x) { return brl(x.total.caixaFinal); }],
      ["Maior necessidade de caixa", function (x) { return brl(Math.abs(Math.min(0, x.total.caixaMinimo))); }],
      ["Primeiro mês com resultado", function (x) { return x.total.equilibrio ? "mês " + x.total.equilibrio : "—"; }]
    ];
    var h = '<table class="sim-tabela" style="min-width:480px"><thead><tr><th></th>' + CENARIOS.map(function (c) { return "<th>" + c[1] + "</th>"; }).join("") + "</tr></thead><tbody>";
    linhas.forEach(function (l) { h += "<tr><td>" + l[0] + "</td>" + CENARIOS.map(function (c) { return "<td>" + l[1](estado.res[c[0]]) + "</td>"; }).join("") + "</tr>"; });
    $("#s-cenarios").innerHTML = h + "</tbody></table>";
    $("#s-premissas-cenario").innerHTML = textoPremissasCenario();
    grafCaixa($("#g-caixa"));
    tabelaCaixa();
    tabelaDre();
  }
  var ROTULO_OP = { novosCadastros: "novos cadastros por mês", crescimentoCadastros: "crescimento dos cadastros", conversao: "cadastros que pagam a adesão",
    perdaMensal: "perda mensal de consultores", ativacaoDez: "consultores com compra única de 65 PB", ativacaoCinco: "consultores com compra de 50 a 64 PB",
    consumidores: "clientes por consultor", renovacao: "renovação anual", pedidosConsumidor: "pedidos por cliente/mês" };
  function textoPremissasCenario() {
    var cfg = estado.cfg;
    return CENARIOS.map(function (c) {
      var op = SIM.operacaoDoCenario(cfg, c[0]);
      return "<li><strong>" + c[1] + ":</strong> " + Object.keys(cfg.cenarios.conservador).map(function (k) {
        var un = ["conversao", "perdaMensal", "ativacaoDez", "ativacaoCinco", "renovacao", "crescimentoCadastros"].indexOf(k) >= 0 ? "%" : "";
        return esc(ROTULO_OP[k] || k) + " " + fDec.format(op[k]) + un;
      }).join(" · ") + "</li>";
    }).join("");
  }
  function tabelaCaixa() {
    var h = '<table class="sim-tabela"><thead><tr><th>Cenário</th><th>Início</th>' + estado.res.base.meses.map(function (m) { return "<th>Mês " + m.mes + "</th>"; }).join("") + "</tr></thead><tbody>";
    CENARIOS.forEach(function (c) {
      var r = estado.res[c[0]];
      h += "<tr><td>" + c[1] + "</td><td>" + brl(-r.op.investimento) + "</td>" + r.meses.map(function (m) { return '<td class="' + (m.caixa < 0 ? "neg" : "") + '">' + brl(m.caixa) + "</td>"; }).join("") + "</tr>";
    });
    $("#t-caixa").innerHTML = h + "</tbody></table>";
  }
  function tabelaDre() {
    var r = atual(), M = r.meses;
    function linha(rot, f, cls) {
      var tot = 0, cel = M.map(function (m) { var v = f(m); tot += v; return '<td class="' + (v < -0.5 && cls !== "sub" ? "neg" : "") + '">' + brl(v) + "</td>"; }).join("");
      return '<tr class="' + (cls || "") + '"><td>' + rot + "</td>" + cel + "<td>" + brl(tot) + "</td></tr>";
    }
    var h = '<table class="sim-tabela"><thead><tr><th>' + esc(NOME_CENARIO[estado.cenario]) + "</th>" + M.map(function (m) { return "<th>Mês " + m.mes + "</th>"; }).join("") + "<th>Ano</th></tr></thead><tbody>";
    h += linha("Faturamento (pedidos pagos)", function (m) { return m.faturamento; }, "total");
    h += linha("· adesões", function (m) { return m.receita.adesao; }, "sub");
    h += linha("· ativação e recompra", function (m) { return m.receita.recompra; }, "sub");
    h += linha("· renovações", function (m) { return m.receita.renovacao; }, "sub");
    h += linha("· vendas digitais", function (m) { return m.receita.digital; }, "sub");
    if (M.some(function (m) { return m.receita.prime > 0; })) h += linha("· SVD Prime", function (m) { return m.receita.prime; }, "sub");
    h += linha("Impostos", function (m) { return -m.impostos; });
    h += linha("Custo dos produtos", function (m) { return -m.cmv; });
    h += linha("Frete subsidiado", function (m) { return -m.frete; });
    ORDEM.forEach(function (k) { if (M.some(function (m) { return m.bonus[k].projetado > 0.5; })) h += linha(NOMES[k], function (m) { return -m.bonus[k].projetado; }); });
    h += linha("Operação variável", function (m) { return -m.operacao; });
    h += linha("Margem de contribuição", function (m) { return m.contribuicao; }, "total");
    h += linha("Aquisição de consultores", function (m) { return -m.cac; });
    h += linha("Custo fixo", function (m) { return -m.custoFixo; });
    h += linha("Resultado do mês", function (m) { return m.resultado; }, "total");
    h += '<tr class="total"><td>Caixa acumulado</td>' + M.map(function (m) { return '<td class="' + (m.caixa < 0 ? "neg" : "pos") + '">' + brl(m.caixa) + "</td>"; }).join("") + "<td>" + brl(r.total.caixaFinal) + "</td></tr>";
    $("#t-dre").innerHTML = h + "</tbody></table>";
  }

  // ------------------------------------------------------------- bonus
  var BONUS = [
    { k: "inicio", status: "ativo", resumo: "Pago na adesão, do 1º ao 10º nível, sobre os PB bonificáveis do kit de entrada.",
      formula: function (c) { return "<code>PB bonificáveis da adesão × % do nível</code>. Tabela: " + c.bonus.inicio.niveis.join("% · ") + "% (soma " + soma(c.bonus.inicio.niveis) + "%). A soma é o teto do que um pedido distribui, não o que uma pessoa recebe."; },
      requisitos: function (c) { return "Elegibilidade aplicada nível a nível: " + (c.bonus.inicio.exigeAtivacao ? "quem recebe precisa estar ativo na competência, com a profundidade liberada pela sua maior compra única (" + c.ativacao.pbCinco + " PB → 5 níveis; " + c.ativacao.pbDez + " PB → 10 níveis)." : "basta existir a upline no nível.") + " Adesão normal de " + c.adesao.opcoes.map(function (o) { return o.pb; }).join(" ou ") + " PB" + (c.adesao.promo.ligada ? "; campanha de " + c.adesao.promo.pb + " PB do mês " + c.adesao.promo.inicio + " ao " + c.adesao.promo.fim + "." : "."); },
      editor: function () {
        var c = estado.cfg;
        return tabelaNiveis("bonus.inicio.niveis", "Nível", "%") + campo("bonus.inicio.exigeAtivacao", "Quem recebe precisa estar ativo, com a profundidade liberada", { tipo: "check" }) +
          '<div class="sim-grid sim-g4">' + c.adesao.opcoes.map(function (o, i) {
            return campo("adesao.opcoes." + i + ".pb", "Adesão opção " + (i + 1), { un: "PB" }) + campo("adesao.opcoes." + i + ".peso", "Peso da opção " + (i + 1), { un: "%" });
          }).join("") + "</div>" +
          '<div class="sim-grid sim-g4">' + campo("adesao.promo.ligada", "Campanha de entrada promocional", { tipo: "check", ajuda: "Vigência explícita; não vira mínimo permanente." }) +
          campo("adesao.promo.pb", "PB da campanha", { un: "PB" }) + campo("adesao.promo.inicio", "Do mês", { min: 1, max: 12, step: 1 }) + campo("adesao.promo.fim", "Até o mês", { min: 1, max: 12, step: 1 }) + "</div>";
      }, sens: { path: "operacao.conversao", delta: 10, rot: "10 p.p. a mais de cadastros que pagam a adesão" } },
    { k: "rede", status: "ativo", resumo: "Pago todo mês, do 1º ao 10º nível, sobre os PB bonificáveis de ativação, recompra e renovação.",
      formula: function (c) { return "<code>PB bonificáveis do pedido × % do nível</code>. Tabela: " + c.bonus.rede.niveis.join("% · ") + "% (soma " + soma(c.bonus.rede.niveis) + "%). Um pedido de 100 PB gera no máximo " + brl2(SIM.valorPedidoNaTabela(100, c.bonus.rede.niveis, 10, c.economia.valorPB)) + "."; },
      requisitos: function (c) { return "A maior compra única paga na competência libera a profundidade: " + c.ativacao.pbCinco + " PB → até 5 níveis; " + c.ativacao.pbDez + " PB → até 10. Pedidos separados não se somam. Corte no dia " + c.ativacao.diaCorte + "; compressão de inativos " + (c.ativacao.compressao ? "ligada (o inativo é pulado e o valor sobe para o próximo ativo)." : "desligada (o valor do inativo fica com a empresa)."); },
      editor: function () {
        return tabelaNiveis("bonus.rede.niveis", "Nível", "%") + '<div class="sim-grid sim-g4">' +
          campo("ativacao.pbCinco", "Compra única que libera 5 níveis", { un: "PB" }) + campo("ativacao.pbDez", "Compra única que libera 10 níveis", { un: "PB" }) +
          campo("ativacao.diaCorte", "Dia de corte da competência", { min: 1, max: 31, step: 1 }) + "</div>" + campo("ativacao.compressao", "Compressão de inativos", { tipo: "check" });
      }, sens: { path: "operacao.ativacaoDez", delta: 10, rot: "10 p.p. a mais de consultores com compra única de 65 PB" } },
    { k: "carreira", status: "opcional", liga: "bonus.carreira.ligado", resumo: "Diferencial entre títulos na mesma linha: quem tem título maior recebe a diferença de taxa sobre o volume de baixo.",
      formula: function (c) { return "<code>PB bonificáveis × (taxa do título − taxa do título abaixo na linha)</code>. Taxas: " + c.titulos.map(function (t) { return t.nome + " " + t.taxa + "%"; }).join(" · ") + ". Nunca paga a taxa cheia para cada graduado da cadeia: no máximo a maior taxa alcançada sobre cada pedido."; },
      requisitos: function () { return "Título pela meta de PB qualificados do mês, contando cada linha até o VML. Faixa: do título mais próximo acima do pedido (piso) ao mais alto da cadeia (teto); o caixa usa o teto."; },
      editor: function () {
        var c = estado.cfg;
        return '<div class="sim-tabela-wrap"><table class="sim-tabela" style="min-width:520px"><thead><tr><th>Título</th><th>Meta (PB)</th><th>VML (PB)</th><th>Linhas</th><th>Taxa (%)</th></tr></thead><tbody>' +
          c.titulos.map(function (t, i) {
            return "<tr><td>" + esc(t.nome) + "</td>" + ["meta", "vml", "linhas", "taxa"].map(function (f) {
              return '<td><input class="sim-in curto" type="number" step="any" min="0" data-path="titulos.' + i + "." + f + '" value="' + t[f] + '" aria-label="' + esc(t.nome) + " " + f + '"></td>';
            }).join("") + "</tr>";
          }).join("") + "</tbody></table></div><p class=\"sim-aviso\">VML é o máximo considerado de uma única linha, não ponto adicional.</p>";
      }, sens: { path: "operacao.largura", delta: 1, rot: "um indicado ativo a mais por consultor" } },
    { k: "lideranca", status: "opcional", liga: "bonus.lideranca.ligado", resumo: "Quatro gerações de líderes qualificados. Geração de líder não é nível da árvore: conta-se de líder em líder.",
      formula: function (c) { return "<code>PB elegíveis do grupo de cada geração × taxa</code>: " + c.bonus.lideranca.geracoes.join("% · ") + "%."; },
      requisitos: function (c) { return "Líder = título " + esc(c.titulos[c.bonus.lideranca.tituloLider].nome) + " ou acima. Faixa: só a 1ª geração (piso) a todas as gerações empilhadas na árvore sintética (teto)."; },
      editor: function () { return tabelaNiveis("bonus.lideranca.geracoes", "Geração", "%") + '<div class="sim-grid sim-g3">' + campo("bonus.lideranca.tituloLider", "Título mínimo de líder", { tipo: "select", opcoes: opcoesTitulos() }) + "</div>"; },
      sens: { path: "operacao.largura", delta: 1, rot: "um indicado ativo a mais por consultor" } },
    { k: "conquistas", status: "opcional", liga: "bonus.conquistas.ligado", resumo: "Fundo de premiação distribuído por faixas de conquista.",
      formula: function (c) { return "<code>Fundo = PB elegíveis × " + c.bonus.conquistas.fundo + "%</code>. Só é projetado como pago se houver quem qualifique; sem qualificado, fica retido."; },
      requisitos: function (c) { return "Faixas = títulos a partir de " + esc(c.titulos[c.bonus.conquistas.tituloMinimo].nome) + ". A divisão entre faixas é regra do plano; o simulador mostra o fundo e quantos qualificam, não renda individual."; },
      editor: function () { return '<div class="sim-grid sim-g3">' + campo("bonus.conquistas.fundo", "Fundo", { un: "% dos PB" }) + campo("bonus.conquistas.tituloMinimo", "Faixa inicial", { tipo: "select", opcoes: opcoesTitulos() }) + "</div>"; },
      extra: function (m) { var c = estado.cfg, q = m.titulos.map(function (v, i) { return i >= c.bonus.conquistas.tituloMinimo && v >= 0.5 ? esc(c.titulos[i].nome) + ": " + int(v) : ""; }).filter(Boolean); return "Qualificados no mês: " + (q.join(" · ") || "ninguém ainda"); },
      sens: { path: "operacao.novosCadastros", fator: 1.2, rot: "20% a mais de cadastros por mês" } },
    { k: "link", status: "ativo", resumo: "Comissão em reais para o titular do link da loja, nas vendas digitais a clientes.",
      formula: function (c) { return "<code>" + (c.digital.base === "tabela" ? "valor de tabela" : "valor pago") + " da venda digital × " + c.bonus.link.taxa + "%</code>. Fora da base de PB: não gera SVD Rede (sem contagem dupla) e não há comissão extra para ponto de entrega."; },
      requisitos: function (c) { return "Cupom do cliente limitado a " + c.digital.cupomTeto + "%. Consumidor inteligente compra com " + c.descontos.consumidorInteligente + "% de desconto."; },
      editor: function () { return '<div class="sim-grid sim-g3">' + campo("bonus.link.taxa", "Comissão do titular", { un: "%" }) + campo("digital.cupomTeto", "Teto do cupom", { un: "%" }) +
        campo("digital.base", "Base da comissão", { tipo: "select", texto: true, opcoes: [["pago", "Valor pago pelo cliente"], ["tabela", "Valor de tabela"]] }) + "</div>"; },
      sens: { path: "operacao.consumidores", delta: 1, rot: "um cliente a mais por consultor" } },
    { k: "liderancaDigital", status: "ativo", resumo: "Comissão em reais para o patrocinador direto de quem vendeu pelo link.",
      formula: function (c) { return "<code>base da venda digital × " + c.bonus.liderancaDigital.taxa + "%</code>, separada dos bônus em PB."; },
      requisitos: function () { return "Paga quando o titular do link tem patrocinador; venda de fundador fica com a empresa."; },
      editor: function () { return '<div class="sim-grid sim-g3">' + campo("bonus.liderancaDigital.taxa", "Comissão do patrocinador", { un: "%" }) + "</div>"; },
      sens: { path: "operacao.pedidosConsumidor", delta: 0.25, rot: "um pedido a mais por cliente a cada 4 meses" } },
    { k: "prime", status: "opcional", liga: "bonus.prime.ligado", modulo: true, resumo: "Módulo de assinatura, desligado no modelo inicial. Ligado, entra com preço, PB e benefícios próprios.",
      formula: function (c) { var p = c.bonus.prime; return "<code>assinantes × R$ " + p.preco + "</code> de receita; cada assinatura soma " + p.pbQual + " PB qualificáveis e " + p.pbBonif + " PB bonificáveis (base do SVD Rede)."; },
      requisitos: function (c) { return "Benefícios: " + esc(c.bonus.prime.beneficios) + ". Quem não habilitou o módulo não tem assinatura na projeção."; },
      editor: function () { return '<div class="sim-grid sim-g3">' + campo("bonus.prime.preco", "Preço mensal", { un: "R$" }) + campo("bonus.prime.adesao", "Consultores que assinam", { un: "%" }) +
        campo("bonus.prime.pbQual", "PB qualificáveis", { un: "PB" }) + campo("bonus.prime.pbBonif", "PB bonificáveis", { un: "PB" }) + campo("bonus.prime.margem", "Margem bruta", { un: "%" }) +
        campo("bonus.prime.beneficios", "Benefícios", { tipo: "texto" }) + "</div>"; },
      sens: { path: "bonus.prime.adesao", delta: 10, rot: "10 p.p. a mais de assinantes" } },
    { k: "pool", status: "estudo", liga: "bonus.pool.cenarioAlternativo", resumo: "Proposta em estudo que substituiria Carreira, Liderança e Conquistas. Desligado: o modelo inicial não paga pool.",
      formula: function (c) { return "<code>Fundo = PB elegíveis × " + c.bonus.pool.fundo + "%</code> (premissa do simulador). Ligado, Carreira, Liderança e Conquistas saem da conta: as saídas não são cumulativas."; },
      requisitos: function () { return '<span class="sim-alerta" style="display:block"><strong>Regra de distribuição individual em configuração.</strong> O simulador mostra só o fundo; não projeta renda individual.</span>'; },
      editor: function () { return '<div class="sim-grid sim-g3">' + campo("bonus.pool.fundo", "Fundo do pool", { un: "% dos PB" }) + "</div>"; },
      sens: { path: "bonus.pool.fundo", delta: 1, rot: "1 p.p. a mais no fundo" } }
  ];
  function soma(a) { return a.reduce(function (s, v) { return s + (+v || 0); }, 0); }

  function montarBonus() {
    var abertos = $$("#b-lista details[open]").map(function (d) { return d.dataset.k; });
    $("#b-lista").innerHTML = BONUS.map(function (b) {
      var c = estado.cfg, ligado = b.liga ? obter(b.liga) : true;
      var rotStatus = { ativo: "Ativo", opcional: b.k === "prime" ? "Opcional · desligado no modelo" : "Opcional", estudo: "Em estudo" }[b.status];
      return '<details class="sim-bonus" data-k="' + b.k + '"' + (abertos.indexOf(b.k) >= 0 ? " open" : "") + "><summary>" +
        '<span class="nome">' + (COR[b.k] ? '<span class="sim-cor" style="background:' + COR[b.k] + '"></span>' : "") + NOMES[b.k] +
        ' <span class="sim-status ' + b.status + '">' + rotStatus + "</span></span>" +
        '<span class="valor" data-v="' + b.k + '">—</span><span class="desc">' + b.resumo + ' <span class="seta"></span></span></summary>' +
        '<div class="corpo">' + (b.liga ? campo(b.liga, b.k === "pool" ? "Simular o cenário alternativo com pool" : "Ligar no modelo", { tipo: "check" }) : "") +
        '<div class="sim-tres" data-tres="' + b.k + '"></div><p class="sim-aviso" data-extra="' + b.k + '" style="margin:0"></p>' +
        '<div class="sim-formula"><strong>Fórmula.</strong> <span data-formula="' + b.k + '">' + b.formula(c) + "</span></div>" +
        '<div class="sim-formula"><strong>Requisitos.</strong> <span data-req="' + b.k + '">' + b.requisitos(c) + "</span></div>" +
        b.editor() + "</div></details>";
    }).join("");
    $$("#b-lista details").forEach(function (d) {
      d.addEventListener("toggle", function () { if (d.open) { evento("simulador_bonus", { bonus: d.dataset.k }); renderValoresBonus(); } });
    });
  }
  function sensibilidade(b) {
    var cfg = JSON.parse(JSON.stringify(estado.cfg)), ks = b.sens.path.split("."), o = cfg;
    for (var i = 0; i < ks.length - 1; i++) o = o[ks[i]];
    var k = ks[ks.length - 1];
    o[k] = b.sens.fator ? o[k] * b.sens.fator : o[k] + b.sens.delta;
    if (b.k === "prime") cfg.bonus.prime.ligado = true;
    if (b.k === "pool") cfg.bonus.pool.cenarioAlternativo = true;
    var novo = SIM.simular(cfg, estado.cenario, 12).meses[estado.mes - 1];
    var antes = mesSel();
    if (b.k === "prime") return [novo.receita.prime - antes.receita.prime, "na receita do SVD Prime"];
    return [novo.bonus[b.k].projetado - antes.bonus[b.k].projetado, "neste bônus"];
  }
  function renderValoresBonus() {
    var m = mesSel(), r = atual(), c = estado.cfg;
    BONUS.forEach(function (b) {
      var alvo = $('[data-v="' + b.k + '"]');
      if (!alvo) return;
      var ano = soma(r.meses.map(function (x) { return b.modulo ? x.receita.prime : x.bonus[b.k].projetado; }));
      var ligado = b.liga ? obter(b.liga) : true;
      alvo.innerHTML = ligado ? brl(b.modulo ? m.receita.prime : m.bonus[b.k].projetado) + '<br><small style="font-weight:400;font-size:.72rem;color:var(--sim-muted)">' + (b.modulo ? "receita no " : "projetado no ") + "mês " + estado.mes + "</small>" :
        '<small style="font-weight:400;font-size:.78rem;color:var(--sim-muted)">' + (b.k === "pool" ? "desligado" : "fora do modelo") + "</small>";
      $('[data-formula="' + b.k + '"]').innerHTML = b.formula(c);
      $('[data-req="' + b.k + '"]').innerHTML = b.requisitos(c);
      var det = alvo.closest("details");
      var tres = $('[data-tres="' + b.k + '"]'), extra = $('[data-extra="' + b.k + '"]');
      if (!det.open) return;
      if (b.modulo) {
        tres.innerHTML = "<div><span>Assinantes no mês " + estado.mes + "</span><strong>" + int(m.prime) + "</strong></div><div><span>Receita no mês</span><strong>" + brl(m.receita.prime) + "</strong></div><div><span>Receita no ano</span><strong>" + brl(ano) + "</strong></div>";
      } else {
        var x = m.bonus[b.k];
        tres.innerHTML = "<div><span>Potencial (tabela cheia)</span><strong>" + brl(x.potencial) + "</strong></div><div><span>Elegível (há quem receba)</span><strong>" + brl(x.elegivel) + "</strong></div><div><span>Projetado para pagamento</span><strong>" + brl(x.projetado) + "</strong></div>";
      }
      var s = sensibilidade(b), linhas = [];
      if (!b.modulo && m.bonus[b.k].faixa && Math.abs(m.bonus[b.k].faixa[1] - m.bonus[b.k].faixa[0]) > 1) linhas.push("Faixa estimada: " + brl(m.bonus[b.k].faixa[0]) + " a " + brl(m.bonus[b.k].faixa[1]) + ".");
      linhas.push("No ano: " + brl(ano) + ".");
      linhas.push("Com " + b.sens.rot + ": " + (s[0] >= 0 ? "+" : "−") + brl(Math.abs(s[0])) + " " + s[1] + " no mês " + estado.mes + ".");
      if (b.extra) linhas.push(b.extra(m));
      extra.innerHTML = linhas.join(" ");
    });
  }

  // ---------------------------------------------------------- premissas
  var GRUPOS = [
    ["Rede e cadastros", [
      ["operacao.consultoresIniciais", "Consultores iniciais", { step: 1, min: 1 }],
      ["operacao.linhasFundadoras", "Linhas fundadoras", { step: 1, min: 1, ajuda: "Quantas redes independentes começam a operação." }],
      ["operacao.largura", "Indicados ativos por consultor", { step: 0.1, min: 1.2, ajuda: "Distribuição da rede por nível: quanto maior, mais rasa e larga." }],
      ["operacao.novosCadastros", "Novos cadastros por mês", { step: 1, min: 0 }],
      ["operacao.crescimentoCadastros", "Crescimento dos cadastros", { un: "% ao mês" }],
      ["operacao.tetoCadastros", "Teto de cadastros por mês", { step: 1, ajuda: "A rede não cresce para sempre." }],
      ["operacao.conversao", "Cadastros que pagam a adesão", { un: "%" }],
      ["operacao.perdaMensal", "Perda mensal de consultores", { un: "%" }],
      ["operacao.renovacao", "Renovação anual dos iniciais", { un: "%" }],
      ["operacao.pbRenovacao", "PB da renovação", { un: "PB" }]]],
    ["Ativação e recompra", [
      ["operacao.ativacaoDez", "Compra única de 65 PB ou mais", { un: "% dos consultores" }],
      ["operacao.ativacaoCinco", "Compra única de 50 a 64 PB", { un: "% dos consultores" }],
      ["operacao.pbMedioDez", "PB médio dessa compra (65+)", { un: "PB" }],
      ["operacao.pbMedioCinco", "PB médio dessa compra (50–64)", { un: "PB" }],
      ["operacao.recompraExtra", "Consultores com pedido extra no mês", { un: "%", ajuda: "Pedido separado: soma PB, não libera nível." }],
      ["operacao.pbRecompraExtra", "PB do pedido extra", { un: "PB" }]]],
    ["Clientes e vendas digitais", [
      ["operacao.consumidores", "Clientes por consultor", { step: 0.1 }],
      ["operacao.pedidosConsumidor", "Pedidos por cliente por mês", { step: 0.05 }],
      ["operacao.ticketDigital", "Ticket da venda digital", { un: "R$, preço ao cliente" }],
      ["operacao.consumidorInteligente", "Clientes como consumidor inteligente", { un: "%" }],
      ["operacao.usoCupom", "Pedidos com cupom", { un: "%" }],
      ["operacao.cupomMedio", "Cupom médio", { un: "%", ajuda: "Limitado ao teto do cupom." }]]],
    ["Economia do plano", [
      ["economia.reaisPorPB", "Faturamento qualificado por PB", { un: "R$", step: 0.01 }],
      ["economia.valorPB", "Valor pago por PB", { un: "R$", step: 0.01 }],
      ["descontos.adesao", "Desconto na adesão e renovação", { un: "%" }],
      ["descontos.recompra", "Desconto na recompra (12 meses)", { un: "%" }],
      ["descontos.consumidorInteligente", "Desconto do consumidor inteligente", { un: "%" }],
      ["cmv", "Custo do produto", { un: "% do preço ao cliente" }],
      ["pbBonifSobreQual.adesao", "PB bonificáveis na adesão", { un: "% dos qualificáveis" }],
      ["pbBonifSobreQual.recompra", "PB bonificáveis na recompra", { un: "% dos qualificáveis" }]]],
    ["Custos da empresa", [
      ["operacao.impostos", "Impostos", { un: "% do faturamento" }],
      ["operacao.freteValor", "Frete subsidiado por pedido", { un: "R$" }],
      ["operacao.freteParcela", "Pedidos com frete subsidiado", { un: "%" }],
      ["operacao.cac", "Aquisição por novo cadastro", { un: "R$" }],
      ["operacao.operacaoVariavel", "Operação variável", { un: "% do faturamento", ajuda: "Gateway, embalagem, atendimento." }],
      ["operacao.custoFixo", "Custo fixo mensal", { un: "R$" }],
      ["operacao.investimento", "Investimento inicial", { un: "R$" }]]]
  ];
  function montarPremissas() {
    $("#p-grupos").innerHTML = GRUPOS.map(function (g) {
      return '<div class="sim-card"><h3>' + g[0] + '</h3><div class="sim-grid sim-g2" style="margin-top:12px">' +
        g[1].map(function (f) { return campo(f[0], f[1], f[2]); }).join("") + "</div>" +
        (g[0] === "Economia do plano" ? '<p class="sim-aviso" id="p-margens"></p>' : "") + "</div>";
    }).join("");
    var cfg = estado.cfg, chaves = Object.keys(cfg.cenarios.conservador);
    var h = '<table class="sim-tabela" style="min-width:560px"><thead><tr><th>Premissa</th><th>Conservador</th><th>Base</th><th>Expansão</th></tr></thead><tbody>';
    chaves.forEach(function (k) {
      var mult = ["novosCadastros", "crescimentoCadastros", "consumidores"].indexOf(k) >= 0;
      h += "<tr><td>" + esc(ROTULO_OP[k] || k) + '<br><small style="color:var(--sim-muted)">' + (mult ? "multiplica a base" : "soma pontos à base") + "</small></td>" +
        '<td><input class="sim-in curto" type="number" step="any" data-path="cenarios.conservador.' + k + '" value="' + cfg.cenarios.conservador[k] + '" aria-label="Conservador ' + esc(ROTULO_OP[k] || k) + '"></td>' +
        "<td>" + (mult ? "× 1" : "+ 0") + "</td>" +
        '<td><input class="sim-in curto" type="number" step="any" data-path="cenarios.expansao.' + k + '" value="' + cfg.cenarios.expansao[k] + '" aria-label="Expansão ' + esc(ROTULO_OP[k] || k) + '"></td></tr>';
    });
    $("#p-cenarios").innerHTML = h + "</tbody></table>";
  }
  function renderMargens() {
    var c = estado.cfg, cmv = c.cmv / 100, el = $("#p-margens");
    if (!el) return;
    var mA = 1 - cmv / (1 - c.descontos.adesao / 100), mR = 1 - cmv / (1 - c.descontos.recompra / 100), mD = 1 - cmv;
    el.innerHTML = "Margem bruta por canal (antes de bônus): adesão " + pct(mA * 100) + " · recompra " + pct(mR * 100) + " · venda ao cliente " + pct(mD * 100) +
      ". Desconto comercial não é bônus: o faturamento já é o valor pago.";
  }

  // -------------------------------------------------------- verificacoes
  function renderVerificacoes() {
    var v = SIM.verificacoes(), ok = v.filter(function (x) { return x.ok; }).length;
    $("#v-resumo").textContent = ok + " de " + v.length + " verificações passaram (Plano SVD, versão " + SIM.VERSAO_ATUAL.replace("svd-", "") + ").";
    $("#v-lista").innerHTML = v.map(function (x) {
      var fmt = function (n) { return Number.isInteger(n) && x.nome.indexOf("PB") >= 0 && x.nome.indexOf("níveis") >= 0 && n <= 10 ? n + " níveis" : (x.nome.indexOf("Desconto") === 0 ? n + "%" : (x.nome.indexOf("volta") >= 0 ? n + " PB" : brl2(n))); };
      return '<li><span class="marca ' + (x.ok ? "ok" : "erro") + '" aria-label="' + (x.ok ? "passou" : "falhou") + '">' + (x.ok ? "✓" : "✕") + "</span><span>" + esc(x.nome) +
        (x.nota ? "<small>" + esc(x.nota) + "</small>" : "") + "</span><span>" + fmt(x.obtido) + "</span></li>";
    }).join("");
  }

  // ------------------------------------------------------- rodape e CTA
  function renderRodape() {
    var r = atual(), v = SIM.veredito(r);
    $("#f-cenario").textContent = NOME_CENARIO[estado.cenario];
    $("#f-payout").textContent = pct(v.payout);
    $("#f-sobra").textContent = pct(v.sobra);
    $("#f-caixa").textContent = compacto(r.total.caixaFinal);
  }
  function atualizarCta() {
    var e = (estado.cfg.empresa || "").trim();
    var txt = "Olá! Montei uma simulação do Plano SVD" + (e ? " para a " + e : "") +
      " e quero conversar sobre adaptar esse plano ao meu negócio. Minha simulação: " + linkCompartilhavel();
    $$(".sim-cta-zap").forEach(function (a) { a.href = "https://wa.me/5511994566726?text=" + encodeURIComponent(txt); });
  }

  // --------------------------------------------------------------- geral
  function atualizar() {
    calcular();
    renderHero(); renderRede(); renderSaude(); renderValoresBonus(); renderMargens(); renderRodape(); atualizarCta(); renderCarreira();
  }
  function montarTudo() {
    $$("[data-path]").forEach(function (el) {
      if (el.closest("#b-lista") || el.closest("#p-grupos") || el.closest("#p-cenarios")) return;
      var v = obter(el.dataset.path);
      if (el.type === "checkbox") el.checked = !!v; else el.value = v == null ? "" : v;
    });
    montarBonus(); montarPremissas(); renderTitulosEmpresa(); atualizar();
  }
  function abrirAba(a) {
    estado.aba = a;
    $$(".sim-abas [role=tab]").forEach(function (b) { b.setAttribute("aria-selected", String(b.dataset.aba === a)); });
    $$(".sim-painel").forEach(function (p) { p.hidden = p.id !== "aba-" + a; });
    redesenhar();
  }
  function redesenhar() {
    if (!estado.res.base) return;
    if (estado.aba === "rede") { renderArvore($("#g-arvore"), mesSel()); grafBonus($("#g-bonus"), atual().meses); }
    if (estado.aba === "saude") grafCaixa($("#g-caixa"));
  }
  var timerTela = null;
  window.addEventListener("resize", function () { if (!imprimindo) { clearTimeout(timerTela); timerTela = setTimeout(redesenhar, 150); } });
  function prepararImpressao() {
    if (!estado.res.base) return;
    imprimindo = true;
    renderArvore($("#g-arvore"), mesSel()); grafBonus($("#g-bonus"), atual().meses); grafCaixa($("#g-caixa"));
  }
  function depoisImpressao() { imprimindo = false; redesenhar(); }
  window.addEventListener("beforeprint", prepararImpressao);
  window.addEventListener("afterprint", depoisImpressao);

  function iniciar() {
    carregar();
    // selects de mes
    $$(".sim-mes").forEach(function (s) {
      s.innerHTML = Array.apply(null, Array(12)).map(function (_, i) { return '<option value="' + (i + 1) + '"' + (i === 11 ? " selected" : "") + ">Mês " + (i + 1) + "</option>"; }).join("");
      s.addEventListener("change", function () { estado.mes = +s.value; $$(".sim-mes").forEach(function (o) { o.value = s.value; }); renderRede(); renderValoresBonus(); });
    });
    $$("[data-cenario]").forEach(function (b) {
      b.addEventListener("click", function () { estado.cenario = b.dataset.cenario; evento("simulador_cenario", { cenario: estado.cenario }); atualizar(); });
    });
    $$(".sim-abas [role=tab]").forEach(function (b) {
      b.addEventListener("click", function () { abrirAba(b.dataset.aba); evento("simulador_aba", { aba: b.dataset.aba }); });
    });
    $$("[data-tabela]").forEach(function (b) {
      b.addEventListener("click", function () {
        var alvo = $("#" + b.dataset.tabela), abrir = alvo.hidden;
        alvo.hidden = !abrir; b.textContent = abrir ? "Esconder tabela" : "Ver em tabela";
      });
    });
    // calculadora de carreira
    function linhaCarreira(v) {
      var l = document.createElement("label"); l.className = "sim-campo";
      l.innerHTML = "<span>Linha " + ($$("#c-linhas label").length + 1) + '<small>PB no mês</small></span><input class="sim-in" type="number" min="0" step="any" value="' + v + '">';
      $("#c-linhas").appendChild(l);
    }
    [900, 700, 400].forEach(linhaCarreira);
    $("#c-mais").addEventListener("click", function () { if ($$("#c-linhas label").length < 10) { linhaCarreira(0); renderCarreira(); } });
    $("#c-menos").addEventListener("click", function () { var ls = $$("#c-linhas label"); if (ls.length > 1) { ls[ls.length - 1].remove(); renderCarreira(); } });
    $("#calc-carreira").addEventListener("input", renderCarreira);

    $("#btn-salvar").addEventListener("click", function () {
      try { localStorage.setItem(CHAVE, JSON.stringify(pacote())); aviso("Simulação salva neste navegador."); evento("simulador_salvar"); }
      catch (e) { aviso("Não deu para salvar neste navegador. Use o link."); }
    });
    $("#btn-link").addEventListener("click", function () {
      var url = linkCompartilhavel();
      history.replaceState(null, "", url);
      (navigator.clipboard ? navigator.clipboard.writeText(url) : Promise.reject()).then(function () { aviso("Link copiado: abre esta simulação com estes parâmetros."); },
        function () { window.prompt("Copie o link da simulação:", url); });
      evento("simulador_link");
    });
    $("#btn-restaurar").addEventListener("click", function () {
      estado.cfg = SIM.planoPadrao(); estado.cenario = "base";
      try { localStorage.removeItem(CHAVE); } catch (e) {}
      history.replaceState(null, "", location.pathname + location.search);
      montarTudo(); aviso("Plano SVD original restaurado.");
    });
    $("#btn-pdf").addEventListener("click", function () {
      var e = (estado.cfg.empresa || "").trim();
      $("#pdf-data").textContent = (e ? e + " · " : "") + "Cenário " + NOME_CENARIO[estado.cenario] + " · Plano SVD " + SIM.VERSAO_ATUAL.replace("svd-", "") +
        " · gerado em " + new Date().toLocaleDateString("pt-BR") + " · projeção ilustrativa, não é renda prometida";
      $("#pdf-link").textContent = linkCompartilhavel();
      var fechados = $$("#b-lista details:not([open])");
      fechados.forEach(function (d) { d.open = true; });
      renderValoresBonus();
      $$("[data-tabela]").forEach(function (b) { $("#" + b.dataset.tabela).dataset.antes = $("#" + b.dataset.tabela).hidden ? "1" : ""; $("#" + b.dataset.tabela).hidden = false; });
      evento("simulador_plano_pdf");
      prepararImpressao();
      window.print();
      setTimeout(function () {
        depoisImpressao();
        fechados.forEach(function (d) { d.open = false; });
        $$("[data-tabela]").forEach(function (b) { var t = $("#" + b.dataset.tabela); if (t.dataset.antes) t.hidden = true; });
      }, 600);
    });
    $$(".sim-cta-zap").forEach(function (a) { a.addEventListener("mousedown", atualizarCta); a.addEventListener("focus", atualizarCta); });

    montarTudo();
    renderVerificacoes();
    abrirAba("rede");
    if (estado.origem === "link") aviso("Simulação aberta a partir do link compartilhado.");
    else if (estado.origem === "salvo") aviso("Sua simulação salva foi carregada.");
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", iniciar); else iniciar();
})();
