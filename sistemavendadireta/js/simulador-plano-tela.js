/*
 * Simulador do Plano SVD — tela. Le e escreve a configuracao; quem calcula e o
 * window.SIM (simulador-plano.js). Sistema Venda Direta · 2026-11-26
 *
 * 27/11/2026 — aba "Seu plano". Ate aqui a pessoa so mexia no Plano SVD por
 * dentro do detalhe de cada bonus: a tela parecia uma simulacao pronta, sem
 * jeito de montar o plano dela. Agora o plano se monta por blocos (adesao,
 * ativacao, inicio, unilevel, binario, titulos, carreira, lideranca,
 * conquistas, pool, link, lideranca digital, assinatura): cada bloco liga e
 * desliga, e niveis, geracoes, titulos e kits entram e saem. A partida e um
 * modelo (Plano SVD, unilevel, binario ou do zero). A aba "Sua rede" junta os
 * numeros da operacao com a previa da arvore.
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
  function clone(o) { return JSON.parse(JSON.stringify(o)); }
  function soma(a) { return a.reduce(function (s, v) { return s + (+v || 0); }, 0); }
  function texto(v, c) { return typeof v === "function" ? v(c) : v; }

  var CHAVE = "svd-simulador";
  var LIM = SIM.LIMITES;
  var CENARIOS = [["conservador", "Conservador"], ["base", "Base"], ["expansao", "Expansão"]];
  var ORDEM = ["inicio", "rede", "binario", "carreira", "lideranca", "conquistas", "pool", "link", "liderancaDigital"];
  // O grafico empilha por familia, nao por bonus: com bonus ligando e desligando,
  // oito cores vizinhas nao se distinguem (validador: laranja x ambar, rosa x
  // vermelho). Estas quatro passam em todos os pares, no escuro e na impressao;
  // o valor de cada bonus fica na dica, na tabela e na aba de detalhe.
  var FAMILIAS = [
    { k: "entrada", nome: "Entrada", cor: "var(--f1)", bonus: ["inicio"] },
    { k: "rede", nome: "Rede", cor: "var(--f2)", bonus: ["rede", "binario"] },
    { k: "carreira", nome: "Carreira e liderança", cor: "var(--f3)", bonus: ["carreira", "lideranca", "conquistas", "pool"] },
    { k: "link", nome: "Vendas pelo link", cor: "var(--f4)", bonus: ["link", "liderancaDigital"] }
  ];
  var COR = {};
  FAMILIAS.forEach(function (f) { f.bonus.forEach(function (k) { COR[k] = f.cor; }); });
  var COR_CENARIO = { conservador: "var(--s2)", base: "var(--s1)", expansao: "var(--s3)" };
  var NOME_CENARIO = { conservador: "Conservador", base: "Base", expansao: "Expansão" };

  var estado = { cfg: null, cenario: "base", mes: 12, aba: "plano", res: {}, usou: false, origem: "padrao", abertos: {} };
  function nome(k) { return SIM.nomeBonus(estado.cfg, k); }
  function marcarUso() { if (!estado.usou) { estado.usou = true; evento("simulador_plano_uso"); } }

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
    estado.cfg = SIM.normalizar(cfg || SIM.planoPadrao());
  }
  function linkCompartilhavel() { return location.origin + location.pathname + "#p=" + b64enc(JSON.stringify(pacote())); }
  function aviso(txt) {
    var t = document.createElement("div");
    t.className = "sim-toast nao-imprime"; t.setAttribute("role", "status"); t.textContent = txt;
    document.body.appendChild(t);
    setTimeout(function () { t.remove(); }, 2600);
  }

  // ------------------------------------------------------- caminho na cfg
  function obterEm(o, path) { return path.split(".").reduce(function (x, k) { return x == null ? x : x[k]; }, o); }
  function definirEm(o, path, v) {
    var ks = path.split(".");
    for (var i = 0; i < ks.length - 1; i++) o = o[ks[i]];
    o[ks[ks.length - 1]] = v;
  }
  function obter(path) { return obterEm(estado.cfg, path); }
  function definir(path, v) { definirEm(estado.cfg, path, v); }
  function campo(path, rotulo, opc) {
    opc = opc || {};
    var v = obter(path);
    var ajuda = opc.ajuda ? '<small class="ajuda">' + esc(opc.ajuda) + "</small>" : "";
    var un = opc.un ? "<small>" + esc(opc.un) + "</small>" : "";
    var est = opc.estrutural ? ' data-estrutural="' + opc.estrutural + '"' : "";
    if (opc.tipo === "check") {
      return '<div><label class="sim-check"><input type="checkbox" data-path="' + path + '"' + est + (v ? " checked" : "") + "> " + esc(rotulo) + "</label>" + ajuda + "</div>";
    }
    if (opc.tipo === "select") {
      return '<label class="sim-campo"><span>' + esc(rotulo) + un + '</span><select class="sim-in" data-path="' + path + '"' + est +
        (opc.texto ? ' data-texto="1"' : "") + ">" + opc.opcoes.map(function (o) {
          return '<option value="' + esc(o[0]) + '"' + (String(o[0]) === String(v) ? " selected" : "") + ">" + esc(o[1]) + "</option>";
        }).join("") + "</select>" + ajuda + "</label>";
    }
    if (opc.tipo === "texto") {
      return '<label class="sim-campo"><span>' + esc(rotulo) + '</span><input class="sim-in" data-texto="1" data-path="' + path + '"' + est + ' value="' + esc(v) + '" maxlength="' + (opc.max || 80) + '"' +
        (opc.placeholder ? ' placeholder="' + esc(opc.placeholder) + '"' : "") + ">" + ajuda + "</label>";
    }
    return '<label class="sim-campo"><span>' + esc(rotulo) + un + '</span><input class="sim-in" type="number" inputmode="decimal" data-path="' + path + '" value="' + esc(v) + '"' +
      (opc.min != null ? ' min="' + opc.min + '"' : "") + (opc.max != null ? ' max="' + opc.max + '"' : "") + ' step="' + (opc.step || "any") + '">' + ajuda + "</label>";
  }
  // tabela de niveis ou geracoes, com botoes de por e tirar
  function tabelaNiveis(path, rot, plural, max) {
    var arr = obter(path);
    return '<div class="sim-niveis">' + arr.map(function (v, i) {
      return '<label><span>' + rot + " " + (i + 1) + '</span><input class="sim-in" type="number" step="any" min="0" data-path="' + path + "." + i + '" value="' + v + '" aria-label="' + rot + " " + (i + 1) + ', em %"></label>';
    }).join("") + "</div>" +
      '<div class="sim-mini-acoes">' +
      '<button type="button" class="sim-mini" data-acao="mais" data-lista="' + path + '"' + (arr.length >= max ? " disabled" : "") + ">+ " + rot.toLowerCase() + "</button>" +
      '<button type="button" class="sim-mini" data-acao="menos" data-lista="' + path + '"' + (arr.length <= 1 ? " disabled" : "") + ">− " + rot.toLowerCase() + "</button>" +
      '<span data-soma="' + path + '" data-rot="' + rot.toLowerCase() + "|" + plural + '"></span></div>';
  }
  function renderSomas() {
    $$("[data-soma]").forEach(function (el) {
      var arr = obter(el.dataset.soma) || [], r = el.dataset.rot.split("|");
      el.textContent = arr.length + " " + (arr.length === 1 ? r[0] : r[1]) + " · soma " + pct(soma(arr)) + " (até " + LIM[el.dataset.soma.indexOf("geracoes") >= 0 ? "geracoes" : "niveis"] + ")";
    });
  }
  function opcoesTitulos() { return estado.cfg.titulos.map(function (t, i) { return [i, t.nome]; }); }
  function listaPct(a) { return a.map(function (v) { return fDec.format(v); }).join(" · ") + "%"; }
  function nomeTit(i) { var t = estado.cfg.titulos[i]; return t ? esc(t.nome) : "—"; }
  function regraAtivacao(c) {
    var a = c.ativacao;
    return int(a.pbCinco) + " PB libera " + (a.niveisCinco === 1 ? "o 1º nível" : "os primeiros " + a.niveisCinco + " níveis") + "; " + int(a.pbDez) + " PB libera todos";
  }
  function trocadoPeloPool(k, c) { return ["carreira", "lideranca", "conquistas"].indexOf(k) >= 0 && !!c.bonus.pool.cenarioAlternativo; }

  // --------------------------------------------------------- calculo
  var timer = null;
  function calcular() {
    SIM.normalizar(estado.cfg);
    CENARIOS.forEach(function (c) { estado.res[c[0]] = SIM.simular(estado.cfg, c[0], 12); });
  }
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
    marcarUso();
    if (el.getAttribute("role") === "switch") evento("simulador_bloco_ligar", { bloco: el.closest("[data-bloco]") ? el.closest("[data-bloco]").dataset.bloco : el.dataset.path, ligado: v });
    if (el.dataset.path === "empresa") renderTitulosEmpresa();
    var est = el.dataset.estrutural;
    if (est && (est !== "change" || ev.type === "change")) remontar();
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

  // colunas empilhadas: bonus por familia, mes a mes
  function grafBonus(cont, meses) {
    var fam = FAMILIAS.map(function (f) {
      return { f: f, v: meses.map(function (m) { return soma(f.bonus.map(function (k) { return m.bonus[k] ? m.bonus[k].projetado : 0; })); }) };
    }).filter(function (x) { return x.v.some(function (v) { return v > 0.5; }); });
    if (!fam.length) {
      cont.innerHTML = '<p class="sim-aviso" style="margin:0;padding:24px 8px;text-align:center">Nenhum bônus ligado no seu plano. Ligue os blocos na aba Seu plano.</p>';
      return;
    }
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
      var visiveis = fam.filter(function (z) { return z.v[i] > 0.5; });
      visiveis.forEach(function (z, j) {
        var v = z.v[i], y0 = y(base), y1 = y(base + v), h = Math.max(0, y0 - y1 - (j > 0 ? 2 : 0));
        var yt = y1, ultimo = j === visiveis.length - 1, r = ultimo ? Math.min(4, h) : 0;
        if (h > 0) {
          s += '<path fill="' + z.f.cor + '" d="M' + x + "," + (yt + h) + "V" + (yt + r) + "Q" + x + "," + yt + " " + (x + r) + "," + yt +
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
    cont.innerHTML = legenda(fam.map(function (z) { return [z.f.nome, z.f.cor]; })) + s;
    $$("rect[data-i]", cont).forEach(function (r) {
      r.addEventListener("mousemove", function (ev) {
        var i = +r.dataset.i, m = meses[i], b = cont.getBoundingClientRect();
        var html = "<b>Mês " + m.mes + " · " + brl(m.bonusTotal) + "</b>" + fam.slice().reverse().map(function (z) {
          if (z.v[i] <= 0.5) return "";
          var det = z.f.bonus.filter(function (k) { return m.bonus[k] && m.bonus[k].projetado > 0.5; });
          return '<div><span><i style="background:' + z.f.cor + '"></i>' + esc(z.f.nome) + "</span><span>" + brl(z.v[i]) + "</span></div>" +
            (det.length > 1 || (det.length === 1 && nome(det[0]) !== z.f.nome) ? det.map(function (k) {
              return '<div class="det"><span>' + esc(nome(k)) + "</span><span>" + brl(m.bonus[k].projetado) + "</span></div>";
            }).join("") : "");
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
    $("#r-elig-sub").textContent = pct(m.dezPct * 100) + " com todos os níveis liberados";
    $("#r-estrutura").textContent = m.estrutura.linhas + " linhas fundadoras · " + fDec.format(m.estrutura.largura) +
      " indicados ativos por consultor · " + m.estrutura.profundidade + " níveis abaixo do fundador";
    if (estado.aba === "rede" && !imprimindo) renderArvore($("#g-arvore"), m);
    var tit = m.titulos.map(function (q, i) { return q >= 0.5 ? '<li><i style="background:' + corTitulo(i) + '"></i>' + esc(cfg.titulos[i].nome) + ": " + int(q) + "</li>" : ""; }).join("");
    $("#r-titulos").innerHTML = tit || "<li>Nenhum título neste mês: a rede ainda não fecha a meta de " + esc(cfg.titulos[0].nome) + " (" + int(cfg.titulos[0].meta) + " PB).</li>";
    var marcos = r.meses.filter(function (x) { return x.novoTituloFundador != null && x.novoTituloFundador >= 0; });
    $("#r-marcos").innerHTML = marcos.length ? marcos.map(function (x) {
      return '<div class="sim-marco"><b>Mês ' + x.mes + "</b><span>Fundador chega a <strong>" + esc(cfg.titulos[x.novoTituloFundador].nome) + "</strong></span></div>";
    }).join("") : '<p class="sim-aviso">Em 12 meses o fundador ainda não fecha a meta de ' + esc(cfg.titulos[0].nome) + " neste cenário. Tente mais cadastros ou mais indicados ativos por consultor.</p>";
    var u = r.meses[11], prox = u.tituloFundador + 1;
    if (prox < cfg.titulos.length) {
      var t = cfg.titulos[prox], e = u.estrutura, q = u.pbMedio;
      var f = e.niveis.length > 1 ? e.niveis[1] / e.niveis[0] : 0;
      var linhaVol = 0; for (var k = 1; k < e.niveis.length; k++) linhaVol += e.niveis[k];
      linhaVol = f > 0 ? linhaVol / f * q : 0;
      var qv = q + f * Math.min(linhaVol, t.vml || Infinity);
      $("#r-proximo").innerHTML = "Próxima conquista do fundador: <strong>" + esc(t.nome) + "</strong> — " + int(qv) + " de " + int(t.meta) +
        " PB qualificados no mês 12 (faltam " + int(Math.max(0, t.meta - qv)) + " PB; cada linha conta no máximo " + int(t.vml) + " PB).";
    } else $("#r-proximo").textContent = "O fundador está no título mais alto do plano.";
    if (estado.aba === "rede" && !imprimindo) grafBonus($("#g-bonus"), r.meses);
    tabelaBonusMes();
  }

  function tabelaBonusMes() {
    var r = atual(), linhas = ORDEM.filter(function (k) { return r.meses.some(function (m) { return m.bonus[k].projetado > 0.5; }); });
    var h = '<table class="sim-tabela"><thead><tr><th>Bônus</th>' + r.meses.map(function (m) { return "<th>Mês " + m.mes + "</th>"; }).join("") + "<th>Ano</th></tr></thead><tbody>";
    linhas.forEach(function (k) {
      var tot = 0;
      h += "<tr><td>" + esc(nome(k)) + "</td>" + r.meses.map(function (m) { tot += m.bonus[k].projetado; return "<td>" + brl(m.bonus[k].projetado) + "</td>"; }).join("") + "<td>" + brl(tot) + "</td></tr>";
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
    var prof = SIM.profundidadePorCompras([compra], cfg.ativacao, cfg.bonus.rede.niveis.length);
    var carreira = cfg.bonus.carreira.ligado && !cfg.bonus.pool.cenarioAlternativo;
    var out = '<p class="sim-aviso" style="margin-top:0">Título no mês: <strong style="color:var(--sim-text)">' + (t >= 0 ? esc(cfg.titulos[t].nome) : "ainda sem título") + "</strong>" +
      (t >= 0 && carreira ? " · " + esc(nome("carreira")) + " de " + pct(cfg.titulos[t].taxa) + " pelo diferencial" : "") + "</p>";
    if (cfg.bonus.rede.ligado) {
      out += '<p class="sim-aviso">Sua maior compra única do mês (' + int(compra) + " PB) " + (prof ? "libera <strong style=\"color:var(--sim-text)\">" + prof + (prof === 1 ? " nível" : " níveis") + "</strong> do " + esc(nome("rede")) + "." : "não libera níveis do " + esc(nome("rede")) + ": precisa de uma compra única de " + int(cfg.ativacao.pbCinco) + " PB.") + "</p>";
    }
    var prox = t + 1;
    if (prox < cfg.titulos.length) {
      var alvo = cfg.titulos[prox], qv = SIM.volumeQualificado(pessoal, linhas, alvo), falta = Math.max(0, alvo.meta - qv);
      var cortadas = linhas.filter(function (v) { return v > alvo.vml; }).length;
      out += '<div style="margin-top:12px"><div style="display:flex;justify-content:space-between;font-size:.85rem;margin-bottom:6px"><span>Caminho até <strong>' + esc(alvo.nome) + "</strong></span><span>" + int(qv) + " / " + int(alvo.meta) + " PB</span></div>" +
        '<div class="sim-medidor" role="meter" aria-valuemin="0" aria-valuemax="' + alvo.meta + '" aria-valuenow="' + Math.round(qv) + '"><i style="width:' + Math.min(100, alvo.meta > 0 ? qv / alvo.meta * 100 : 100) + '%"></i></div>' +
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
  var ICONE = { fecha: "✓", limite: "!", fragil: "!", quebra: "✕" };
  function renderSaude() {
    var r = atual(), v = SIM.veredito(r), t = TEXTO_VEREDITO[v.nivel], tot = r.total;
    var box = $("#s-veredito");
    box.className = "sim-veredito " + v.nivel;
    box.innerHTML = '<p class="sim-aviso" style="margin:0">Mês 12 · cenário ' + NOME_CENARIO[estado.cenario] + '</p><div class="tit"><span aria-hidden="true">' + ICONE[v.nivel] + "</span>" + t[0] + "</div>" +
      "<p>No mês 12, os bônus projetados somam " + pct(v.payout) + " do faturamento e " + t[1].replace("{s}", pct(Math.abs(v.sobra))) + "</p>" +
      "<p>" + (tot.equilibrio ? "A operação passa a dar resultado no mês " + tot.equilibrio + (tot.retorno ? " e recupera o investimento no mês " + tot.retorno + "." : "; o investimento ainda não volta em 12 meses.") :
        "A operação não chega a resultado positivo em 12 meses: com esse volume, o custo fixo e a aquisição pesam mais que a margem. Aqui o problema é escala, não o plano.") +
      " A maior necessidade de caixa é de " + brl(Math.abs(Math.min(0, tot.caixaMinimo))) + ".</p>";
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
    if (estado.aba === "saude" && !imprimindo) grafCaixa($("#g-caixa"));
    tabelaCaixa();
    tabelaDre();
  }
  var ROTULO_OP = { novosCadastros: "novos cadastros por mês", crescimentoCadastros: "crescimento dos cadastros", conversao: "cadastros que pagam a adesão",
    perdaMensal: "perda mensal de consultores", consumidores: "clientes por consultor", renovacao: "renovação anual", pedidosConsumidor: "pedidos por cliente/mês" };
  function rotuloOp(k) {
    var a = estado.cfg.ativacao;
    if (k === "ativacaoDez") return "consultores com compra única de " + int(a.pbDez) + " PB ou mais";
    if (k === "ativacaoCinco") return "consultores com compra de " + int(a.pbCinco) + " a " + int(Math.max(a.pbCinco, a.pbDez - 1)) + " PB";
    return ROTULO_OP[k] || k;
  }
  function textoPremissasCenario() {
    var cfg = estado.cfg;
    return CENARIOS.map(function (c) {
      var op = SIM.operacaoDoCenario(cfg, c[0]);
      return "<li><strong>" + c[1] + ":</strong> " + Object.keys(cfg.cenarios.conservador).map(function (k) {
        var un = ["conversao", "perdaMensal", "ativacaoDez", "ativacaoCinco", "renovacao", "crescimentoCadastros"].indexOf(k) >= 0 ? "%" : "";
        return esc(rotuloOp(k)) + " " + fDec.format(op[k]) + un;
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
    if (M.some(function (m) { return m.receita.prime > 0; })) h += linha("· " + esc(nome("prime")), function (m) { return m.receita.prime; }, "sub");
    h += linha("Impostos", function (m) { return -m.impostos; });
    h += linha("Custo dos produtos", function (m) { return -m.cmv; });
    h += linha("Frete subsidiado", function (m) { return -m.frete; });
    ORDEM.forEach(function (k) { if (M.some(function (m) { return m.bonus[k].projetado > 0.5; })) h += linha(esc(nome(k)), function (m) { return -m.bonus[k].projetado; }); });
    h += linha("Operação variável", function (m) { return -m.operacao; });
    h += linha("Margem de contribuição", function (m) { return m.contribuicao; }, "total");
    h += linha("Aquisição de consultores", function (m) { return -m.cac; });
    h += linha("Custo fixo", function (m) { return -m.custoFixo; });
    h += linha("Resultado do mês", function (m) { return m.resultado; }, "total");
    h += '<tr class="total"><td>Caixa acumulado</td>' + M.map(function (m) { return '<td class="' + (m.caixa < 0 ? "neg" : "pos") + '">' + brl(m.caixa) + "</td>"; }).join("") + "<td>" + brl(r.total.caixaFinal) + "</td></tr>";
    $("#t-dre").innerHTML = h + "</tbody></table>";
  }

  // ------------------------------------------------------ bonus em detalhe
  // Leitura de cada bonus: formula, requisitos e efeito de mudar uma variavel.
  // As regras se editam na aba Seu plano (botao em cada bonus leva ate o bloco).
  var BONUS = [
    { k: "inicio", status: "ativo", liga: "bonus.inicio.ligado",
      resumo: function (c) { var n = c.bonus.inicio.niveis.length; return "Pago na adesão, " + (n === 1 ? "no 1º nível" : "do 1º ao " + n + "º nível") + ", sobre os PB bonificáveis do kit de entrada."; },
      formula: function (c) { var n = c.bonus.inicio.niveis; return "<code>PB bonificáveis da adesão × % do nível</code>. Tabela: " + listaPct(n) + " (soma " + pct(soma(n)) + "). A soma é o teto do que um pedido distribui, não o que uma pessoa recebe."; },
      requisitos: function (c) { return "Elegibilidade nível a nível: " + (c.bonus.inicio.exigeAtivacao ? "quem recebe precisa estar ativo na competência, com a profundidade liberada pela sua maior compra única (" + regraAtivacao(c) + ")." : "basta existir a upline no nível.") + " Adesão de " + c.adesao.opcoes.map(function (o) { return int(o.pb); }).join(" ou ") + " PB" + (c.adesao.promo.ligada ? "; campanha de " + int(c.adesao.promo.pb) + " PB do mês " + c.adesao.promo.inicio + " ao " + c.adesao.promo.fim + "." : "."); },
      sens: { path: "operacao.conversao", delta: 10, rot: "10 p.p. a mais de cadastros que pagam a adesão" } },
    { k: "rede", status: "ativo", liga: "bonus.rede.ligado",
      resumo: function (c) { var n = c.bonus.rede.niveis.length; return "Pago todo mês, " + (n === 1 ? "no 1º nível" : "do 1º ao " + n + "º nível") + ", sobre os PB bonificáveis de ativação, recompra e renovação."; },
      formula: function (c) { var n = c.bonus.rede.niveis; return "<code>PB bonificáveis do pedido × % do nível</code>. Tabela: " + listaPct(n) + " (soma " + pct(soma(n)) + "). Um pedido de 100 PB gera no máximo " + brl2(SIM.valorPedidoNaTabela(100, n, n.length, c.economia.valorPB)) + "."; },
      requisitos: function (c) { return (c.bonus.rede.exigeAtivacao !== false ? "A maior compra única paga na competência libera a profundidade: " + regraAtivacao(c) + ". Pedidos separados não se somam." : "Sem exigência de ativação: basta existir a upline no nível.") + " Corte no dia " + c.ativacao.diaCorte + "; compressão de inativos " + (c.ativacao.compressao ? "ligada (o inativo é pulado e o valor sobe para o próximo ativo)." : "desligada (o valor do inativo fica com a empresa)."); },
      sens: { path: "operacao.ativacaoDez", delta: 10, rot: function (c) { return "10 p.p. a mais de consultores com compra única de " + int(c.ativacao.pbDez) + " PB"; } } },
    { k: "binario", status: "fora", liga: "bonus.binario.ligado",
      resumo: "Duas pernas por consultor: paga uma porcentagem do volume da perna menor, com teto por consultor no mês.",
      formula: function (c) { var b = c.bonus.binario; return "<code>PB da perna menor × " + pct(b.taxa) + "</code>" + (b.teto > 0 ? ", até " + brl(b.teto) + " por consultor no mês" : ", sem teto") + ". Cada pedido conta na perna de todos os que estão acima dele na árvore binária: por isso o limite do binário é por consultor, não por pedido."; },
      requisitos: function (c) { return "Recebe quem está ativo no mês e tem volume nas duas pernas. Estimativa: árvore binária equilibrada em cada linha fundadora, perna menor com " + pct(c.bonus.binario.equilibrio) + " do volume da maior e sem acúmulo de pontos de um mês para o outro (o acúmulo aumenta o pagamento)."; },
      sens: { path: "bonus.binario.equilibrio", delta: 10, rot: "pernas 10 p.p. mais equilibradas" } },
    { k: "carreira", status: "opcional", liga: "bonus.carreira.ligado",
      resumo: "Diferencial entre títulos na mesma linha: quem tem título maior recebe a diferença de taxa sobre o volume de baixo.",
      formula: function (c) { return "<code>PB bonificáveis × (taxa do título − taxa do título abaixo na linha)</code>. Taxas: " + c.titulos.map(function (t) { return esc(t.nome) + " " + pct(t.taxa); }).join(" · ") + ". Nunca paga a taxa cheia para cada graduado da cadeia: no máximo a maior taxa alcançada sobre cada pedido."; },
      requisitos: function () { return "Título pela meta de PB qualificados do mês, contando cada linha até o VML. Faixa: do título mais próximo acima do pedido (piso) ao mais alto da cadeia (teto); o caixa usa o teto."; },
      sens: { path: "operacao.largura", delta: 1, rot: "um indicado ativo a mais por consultor" } },
    { k: "lideranca", status: "opcional", liga: "bonus.lideranca.ligado",
      resumo: function (c) { var n = c.bonus.lideranca.geracoes.length; return n + (n === 1 ? " geração" : " gerações") + " de líderes qualificados. Geração de líder não é nível da árvore: conta-se de líder em líder."; },
      formula: function (c) { return "<code>PB elegíveis do grupo de cada geração × taxa</code>: " + listaPct(c.bonus.lideranca.geracoes) + "."; },
      requisitos: function (c) { return "Líder = título " + nomeTit(c.bonus.lideranca.tituloLider) + " ou acima. Faixa: só a 1ª geração (piso) a todas as gerações empilhadas na árvore sintética (teto)."; },
      sens: { path: "operacao.largura", delta: 1, rot: "um indicado ativo a mais por consultor" } },
    { k: "conquistas", status: "opcional", liga: "bonus.conquistas.ligado",
      resumo: "Fundo de premiação distribuído por faixas de conquista.",
      formula: function (c) { return "<code>Fundo = PB elegíveis × " + pct(c.bonus.conquistas.fundo) + "</code>. Só é projetado como pago se houver quem qualifique; sem qualificado, fica retido."; },
      requisitos: function (c) { return "Faixas = títulos a partir de " + nomeTit(c.bonus.conquistas.tituloMinimo) + ". A divisão entre faixas é regra do plano; o simulador mostra o fundo e quantos qualificam, não renda individual."; },
      extra: function (m) { var c = estado.cfg, q = m.titulos.map(function (v, i) { return i >= c.bonus.conquistas.tituloMinimo && v >= 0.5 ? esc(c.titulos[i].nome) + ": " + int(v) : ""; }).filter(Boolean); return "Qualificados no mês: " + (q.join(" · ") || "ninguém ainda"); },
      sens: { path: "operacao.novosCadastros", fator: 1.2, rot: "20% a mais de cadastros por mês" } },
    { k: "link", status: "ativo", liga: "bonus.link.ligado",
      resumo: "Comissão em reais para o titular do link da loja, nas vendas digitais a clientes.",
      formula: function (c) { return "<code>" + (c.digital.base === "tabela" ? "valor de tabela" : "valor pago") + " da venda digital × " + pct(c.bonus.link.taxa) + "</code>. Fora da base de PB: não gera bônus de rede (sem contagem dupla) e não há comissão extra para ponto de entrega."; },
      requisitos: function (c) { return "Cupom do cliente limitado a " + pct(c.digital.cupomTeto) + ". Consumidor inteligente compra com " + pct(c.descontos.consumidorInteligente) + " de desconto."; },
      sens: { path: "operacao.consumidores", delta: 1, rot: "um cliente a mais por consultor" } },
    { k: "liderancaDigital", status: "ativo", liga: "bonus.liderancaDigital.ligado",
      resumo: "Comissão em reais para o patrocinador direto de quem vendeu pelo link.",
      formula: function (c) { return "<code>base da venda digital × " + pct(c.bonus.liderancaDigital.taxa) + "</code>, separada dos bônus em PB."; },
      requisitos: function () { return "Paga quando o titular do link tem patrocinador; venda de fundador fica com a empresa."; },
      sens: { path: "operacao.pedidosConsumidor", delta: 0.25, rot: "um pedido a mais por cliente a cada 4 meses" } },
    { k: "prime", status: "opcional", liga: "bonus.prime.ligado", modulo: true,
      resumo: "Módulo de assinatura, desligado no Plano SVD. Ligado, entra com preço, PB e benefícios próprios.",
      formula: function (c) { var p = c.bonus.prime; return "<code>assinantes × " + brl2(p.preco) + "</code> de receita; cada assinatura soma " + int(p.pbQual) + " PB qualificáveis e " + int(p.pbBonif) + " PB bonificáveis (base do bônus de rede)."; },
      requisitos: function (c) { return "Benefícios: " + esc(c.bonus.prime.beneficios) + ". Quem não habilitou o módulo não tem assinatura na projeção."; },
      sens: { path: "bonus.prime.adesao", delta: 10, rot: "10 p.p. a mais de assinantes" } },
    { k: "pool", status: "estudo", liga: "bonus.pool.cenarioAlternativo",
      resumo: "Fundo único que substitui carreira, liderança e conquistas. No Plano SVD está em estudo e desligado.",
      formula: function (c) { return "<code>Fundo = PB elegíveis × " + pct(c.bonus.pool.fundo) + "</code> (premissa do simulador). Ligado, carreira, liderança e conquistas saem da conta: as saídas não são cumulativas."; },
      requisitos: function () { return '<span class="sim-alerta" style="display:block"><strong>Regra de distribuição individual em configuração.</strong> O simulador mostra só o fundo; não projeta renda individual.</span>'; },
      sens: { path: "bonus.pool.fundo", delta: 1, rot: "1 p.p. a mais no fundo" } }
  ];
  var ROT_STATUS = { ativo: "Plano SVD: ativo", opcional: "Plano SVD: opcional", estudo: "Plano SVD: em estudo", fora: "Fora do Plano SVD" };

  function montarBonus() {
    var abertos = $$("#b-lista details[open]").map(function (d) { return d.dataset.k; });
    $("#b-lista").innerHTML = BONUS.map(function (b) {
      var c = estado.cfg;
      return '<details class="sim-bonus" data-k="' + b.k + '"' + (abertos.indexOf(b.k) >= 0 ? " open" : "") + "><summary>" +
        '<span class="nome">' + (COR[b.k] ? '<span class="sim-cor" style="background:' + COR[b.k] + '"></span>' : "") + esc(nome(b.k)) +
        ' <span class="sim-status ' + b.status + '">' + ROT_STATUS[b.status] + "</span></span>" +
        '<span class="valor" data-v="' + b.k + '">—</span><span class="desc" data-resumo="' + b.k + '">' + texto(b.resumo, c) + ' <span class="seta"></span></span></summary>' +
        '<div class="corpo">' +
        '<div class="sim-tres" data-tres="' + b.k + '"></div><p class="sim-aviso" data-extra="' + b.k + '" style="margin:0"></p>' +
        '<div class="sim-formula"><strong>Fórmula.</strong> <span data-formula="' + b.k + '">' + b.formula(c) + "</span></div>" +
        '<div class="sim-formula"><strong>Requisitos.</strong> <span data-req="' + b.k + '">' + b.requisitos(c) + "</span></div>" +
        '<p class="nao-imprime"><button type="button" class="sim-btn" data-editar="' + b.k + '">Mudar esta regra no seu plano</button></p></div></details>';
    }).join("");
    $$("#b-lista details").forEach(function (d) {
      d.addEventListener("toggle", function () { if (d.open) { evento("simulador_bonus", { bonus: d.dataset.k }); renderValoresBonus(); } });
    });
  }
  function simularCom(muda) {
    var cfg = clone(estado.cfg);
    muda(cfg);
    return SIM.simular(cfg, estado.cenario, 12).meses[estado.mes - 1];
  }
  function valorDo(b, m) { return b.modulo ? m.receita.prime : m.bonus[b.k].projetado; }
  function sensibilidade(b) {
    var novo = simularCom(function (cfg) {
      var atualV = obterEm(cfg, b.sens.path);
      definirEm(cfg, b.sens.path, b.sens.fator ? atualV * b.sens.fator : atualV + b.sens.delta);
    });
    return [valorDo(b, novo) - valorDo(b, mesSel()), b.modulo ? "na receita da assinatura" : "neste bônus"];
  }
  function renderValoresBonus() {
    var m = mesSel(), r = atual(), c = estado.cfg;
    BONUS.forEach(function (b) {
      var alvo = $('[data-v="' + b.k + '"]');
      if (!alvo) return;
      var ligado = !!obter(b.liga), trocado = trocadoPeloPool(b.k, c);
      var ano = soma(r.meses.map(function (x) { return valorDo(b, x); }));
      alvo.innerHTML = ligado && !trocado ? brl(valorDo(b, m)) + '<br><small style="font-weight:400;font-size:.72rem;color:var(--sim-muted)">' + (b.modulo ? "receita no " : "projetado no ") + "mês " + estado.mes + "</small>" :
        '<small style="font-weight:400;font-size:.78rem;color:var(--sim-muted)">' + (trocado ? "substituído pelo pool" : "fora do seu plano") + "</small>";
      $('[data-resumo="' + b.k + '"]').innerHTML = texto(b.resumo, c) + ' <span class="seta"></span>';
      $('[data-formula="' + b.k + '"]').innerHTML = b.formula(c);
      $('[data-req="' + b.k + '"]').innerHTML = b.requisitos(c);
      var det = alvo.closest("details");
      var tres = $('[data-tres="' + b.k + '"]'), extra = $('[data-extra="' + b.k + '"]');
      if (!det.open) return;
      if (!ligado || trocado) {
        tres.innerHTML = "";
        if (trocado) { extra.textContent = "Com o pool ligado, este bônus sai da conta: o pool fica no lugar de carreira, liderança e conquistas."; return; }
        var v = valorDo(b, simularCom(function (cfg) { definirEm(cfg, b.liga, true); }));
        extra.textContent = "Desligado no seu plano. Ligado com estas regras, " + (b.modulo ? "a assinatura renderia " : "pagaria ") + brl(v) + " no mês " + estado.mes +
          (b.k === "pool" ? ", e carreira, liderança e conquistas sairiam da conta." : ".");
        return;
      }
      if (b.modulo) {
        tres.innerHTML = "<div><span>Assinantes no mês " + estado.mes + "</span><strong>" + int(m.prime) + "</strong></div><div><span>Receita no mês</span><strong>" + brl(m.receita.prime) + "</strong></div><div><span>Receita no ano</span><strong>" + brl(ano) + "</strong></div>";
      } else {
        var x = m.bonus[b.k];
        tres.innerHTML = "<div><span>Potencial (tabela cheia)</span><strong>" + brl(x.potencial) + "</strong></div><div><span>Elegível (há quem receba)</span><strong>" + brl(x.elegivel) + "</strong></div><div><span>Projetado para pagamento</span><strong>" + brl(x.projetado) + "</strong></div>";
      }
      var s = sensibilidade(b), linhas = [];
      if (!b.modulo && m.bonus[b.k].faixa && Math.abs(m.bonus[b.k].faixa[1] - m.bonus[b.k].faixa[0]) > 1) linhas.push("Faixa estimada: " + brl(m.bonus[b.k].faixa[0]) + " a " + brl(m.bonus[b.k].faixa[1]) + ".");
      linhas.push("No ano: " + brl(ano) + ".");
      linhas.push("Com " + texto(b.sens.rot, c) + ": " + (s[0] >= 0 ? "+" : "−") + brl(Math.abs(s[0])) + " " + s[1] + " no mês " + estado.mes + ".");
      if (b.extra) linhas.push(b.extra(m));
      extra.innerHTML = linhas.join(" ");
    });
  }

  // ---------------------------------------------------------- seu plano
  // Cada bloco: o nome que o mercado usa, o que ele faz, a ficha de uma linha
  // (que aparece fechada e no resumo) e os campos. `liga` = chave liga/desliga.
  function corpoAdesao(c) {
    var o = c.adesao.opcoes;
    return '<div class="sim-grid sim-g3">' + o.map(function (k, i) {
      return '<div class="sim-kit"><div class="sim-kit-cab"><strong>Kit ' + (i + 1) + "</strong>" +
        (o.length > 1 ? '<button type="button" class="sim-mini" data-acao="tirar" data-lista="adesao.opcoes" data-i="' + i + '" aria-label="Tirar o kit ' + (i + 1) + '">✕</button>' : "") + "</div>" +
        campo("adesao.opcoes." + i + ".pb", "Volume do kit", { un: "PB", min: 0 }) +
        campo("adesao.opcoes." + i + ".peso", "Quem escolhe este kit", { un: "% das adesões", min: 0 }) + "</div>";
    }).join("") + "</div>" +
      '<div class="sim-mini-acoes"><button type="button" class="sim-mini" data-acao="mais" data-lista="adesao.opcoes"' + (o.length >= LIM.kits ? " disabled" : "") + ">+ kit</button>" +
      '<span class="sim-aviso" style="margin:0">Até ' + LIM.kits + " kits. As proporções se ajustam para somar 100%. O preço do kit é o volume × " + brl2(c.economia.reaisPorPB) + " por PB (aba Sua rede).</span></div>" +
      '<div class="sim-grid sim-g4">' + campo("adesao.promo.ligada", "Campanha de entrada promocional", { tipo: "check", ajuda: "Com início e fim: não vira mínimo permanente." }) +
      campo("adesao.promo.pb", "PB da campanha", { un: "PB" }) + campo("adesao.promo.inicio", "Do mês", { min: 1, max: 12, step: 1 }) + campo("adesao.promo.fim", "Até o mês", { min: 1, max: 12, step: 1 }) + "</div>";
  }
  var ROT_TIT = { meta: "meta em PB", vml: "VML em PB", linhas: "linhas mínimas", taxa: "taxa de carreira em %" };
  function corpoTitulos(c) {
    var n = c.titulos.length;
    return '<div class="sim-tabela-wrap"><table class="sim-tabela sim-tabela-edit" style="min-width:600px"><thead><tr><th>Título</th><th>Meta (PB)</th><th>VML (PB)</th><th>Linhas</th><th>Carreira (%)</th><th><span class="sim-sr">Tirar</span></th></tr></thead><tbody>' +
      c.titulos.map(function (t, i) {
        return '<tr><td><span class="sim-cor" style="background:' + corTitulo(i) + '"></span> <input class="sim-in" data-texto="1" data-estrutural="change" data-path="titulos.' + i + '.nome" value="' + esc(t.nome) + '" maxlength="30" aria-label="Nome do título ' + (i + 1) + '"></td>' +
          ["meta", "vml", "linhas", "taxa"].map(function (f) {
            return '<td><input class="sim-in curto" type="number" step="any" min="0" data-path="titulos.' + i + "." + f + '" value="' + esc(t[f]) + '" aria-label="' + esc(t.nome) + ", " + ROT_TIT[f] + '"></td>';
          }).join("") +
          "<td>" + (n > 1 ? '<button type="button" class="sim-mini" data-acao="tirar" data-lista="titulos" data-i="' + i + '" aria-label="Tirar o título ' + esc(t.nome) + '">✕</button>' : "") + "</td></tr>";
      }).join("") + "</tbody></table></div>" +
      '<div class="sim-mini-acoes"><button type="button" class="sim-mini" data-acao="mais" data-lista="titulos"' + (n >= LIM.titulos ? " disabled" : "") + ">+ título</button>" +
      '<span class="sim-aviso" style="margin:0">Até ' + LIM.titulos + " títulos, do menor para o maior. VML é o máximo que uma única linha conta para a meta; linhas é o mínimo de linhas com volume.</span></div>";
  }
  var BLOCOS = [
    { grupo: "Entrada e ativação", itens: [
      { k: "adesao", tit: "Adesão (kit de entrada)", svd: "No Plano SVD: kits de 80 ou 100 PB, meio a meio.",
        desc: "O que o consultor compra para entrar. O volume do kit é a base do bônus de início.",
        ficha: function (c) { var o = c.adesao.opcoes.map(function (x) { return int(x.pb); }); return (o.length === 1 ? "Kit de " : "Kits de ") + (o.length > 1 ? o.slice(0, -1).join(", ") + " ou " + o[o.length - 1] : o[0]) + " PB" + (c.adesao.promo.ligada ? " · campanha de " + int(c.adesao.promo.pb) + " PB" : ""); },
        corpo: corpoAdesao },
      { k: "ativacao", tit: "Ativação mensal", svd: "No Plano SVD: 50 PB libera 5 níveis e 65 PB libera os 10.",
        desc: "A compra mínima do mês para receber. A maior compra única define até que nível a pessoa recebe; pedidos separados não se somam.",
        ficha: function (c) { return regraAtivacao(c) + (c.ativacao.compressao ? " · com compressão" : " · sem compressão"); },
        corpo: function () {
          return '<div class="sim-grid sim-g4">' + campo("ativacao.pbCinco", "Compra menor", { un: "PB", ajuda: "Libera só os primeiros níveis." }) +
            campo("ativacao.niveisCinco", "Níveis que ela libera", { min: 1, max: LIM.niveis, step: 1 }) +
            campo("ativacao.pbDez", "Compra que libera todos os níveis", { un: "PB" }) +
            campo("ativacao.diaCorte", "Dia de corte do mês", { min: 1, max: 31, step: 1 }) + "</div>" +
            campo("ativacao.compressao", "Compressão de inativos: o inativo é pulado e o valor sobe para o próximo ativo", { tipo: "check" });
        } }
    ] },
    { grupo: "Bônus sobre a rede", itens: [
      { k: "inicio", liga: "bonus.inicio.ligado", bonus: true, tit: "Bônus de início (indicação)", svd: "No Plano SVD: SVD Início, 10 níveis, soma 85%.",
        desc: "Pago quando alguém da rede entra, sobre o volume do kit. Com um nível só, é o bônus de indicação direta.",
        ficha: function (c) { var n = c.bonus.inicio.niveis; return n.length + (n.length === 1 ? " nível: " : " níveis: ") + listaPct(n) + (n.length > 1 ? " (soma " + pct(soma(n)) + ")" : ""); },
        corpo: function () { return tabelaNiveis("bonus.inicio.niveis", "Nível", "níveis", LIM.niveis) + campo("bonus.inicio.exigeAtivacao", "Quem recebe precisa estar ativo, com a profundidade liberada pela ativação", { tipo: "check" }); } },
      { k: "rede", liga: "bonus.rede.ligado", bonus: true, tit: "Unilevel", svd: "No Plano SVD: SVD Rede, 10 níveis, soma 50%.",
        desc: "Pago todo mês sobre ativação, recompra e renovação da rede, nível a nível, sem limite de largura.",
        ficha: function (c) { var n = c.bonus.rede.niveis; return n.length + (n.length === 1 ? " nível: " : " níveis: ") + listaPct(n) + (n.length > 1 ? " (soma " + pct(soma(n)) + ")" : ""); },
        corpo: function () { return tabelaNiveis("bonus.rede.niveis", "Nível", "níveis", LIM.niveis) + campo("bonus.rede.exigeAtivacao", "Quem recebe precisa estar ativo, com a profundidade liberada pela ativação", { tipo: "check" }); } },
      { k: "binario", liga: "bonus.binario.ligado", bonus: true, tit: "Binário", svd: "Fora do Plano SVD: bloco para quem trabalha com binário.",
        desc: "Cada consultor tem duas pernas. Paga uma porcentagem do volume da perna menor, com teto por consultor no mês.",
        ficha: function (c) { var b = c.bonus.binario; return pct(b.taxa) + " da perna menor · " + (b.teto > 0 ? "teto de " + brl(b.teto) + " por consultor" : "sem teto"); },
        corpo: function () {
          return '<div class="sim-grid sim-g3">' + campo("bonus.binario.taxa", "Porcentagem da perna menor", { un: "%" }) +
            campo("bonus.binario.teto", "Teto por consultor no mês", { un: "R$", ajuda: "Zero = sem teto." }) +
            campo("bonus.binario.equilibrio", "Perna menor em relação à maior", { un: "%", min: 1, max: 100, ajuda: "Premissa da rede, não regra do plano. Numa rede real, 30% a 60%." }) + "</div>";
        } }
    ] },
    { grupo: "Carreira e liderança", itens: [
      { k: "titulos", tit: "Títulos (graduações)", svd: "No Plano SVD: 8 títulos, de Bronze a Imperial Elite.",
        desc: "A escada de carreira. A meta soma o volume da pessoa e o da rede no mês, com cada linha contando até o VML do título.",
        ficha: function (c) { var t = c.titulos, u = t[t.length - 1]; return t.length + (t.length === 1 ? " título: " : " títulos, de ") + esc(t[0].nome) + " (" + int(t[0].meta) + " PB)" + (t.length > 1 ? " a " + esc(u.nome) + " (" + int(u.meta) + " PB)" : ""); },
        corpo: corpoTitulos },
      { k: "carreira", liga: "bonus.carreira.ligado", bonus: true, tit: "Bônus de carreira (diferencial)", svd: "No Plano SVD: SVD Carreira, de 4% a 15%.",
        desc: "Quem tem título maior recebe a diferença de taxa sobre o volume de baixo.",
        ficha: function (c) { var tx = c.titulos.map(function (t) { return +t.taxa || 0; }); return "diferencial de " + pct(Math.min.apply(null, tx)) + " a " + pct(Math.max.apply(null, tx)) + " por título"; },
        corpo: function () { return '<p class="sim-aviso" style="margin:0">A taxa de cada título fica na tabela do bloco <button type="button" class="sim-link" data-abrir="titulos">Títulos</button>.</p>'; } },
      { k: "lideranca", liga: "bonus.lideranca.ligado", bonus: true, tit: "Bônus de liderança (gerações)", svd: "No Plano SVD: SVD Liderança, 4 gerações.",
        desc: "Paga sobre o volume de outros líderes da linha, de líder em líder. Geração não é nível da árvore.",
        ficha: function (c) { var g = c.bonus.lideranca.geracoes; return g.length + (g.length === 1 ? " geração: " : " gerações: ") + listaPct(g) + " · líder a partir de " + nomeTit(c.bonus.lideranca.tituloLider); },
        corpo: function () { return tabelaNiveis("bonus.lideranca.geracoes", "Geração", "gerações", LIM.geracoes) + '<div class="sim-grid sim-g3">' + campo("bonus.lideranca.tituloLider", "Líder a partir do título", { tipo: "select", opcoes: opcoesTitulos(), estrutural: "change" }) + "</div>"; } },
      { k: "conquistas", liga: "bonus.conquistas.ligado", bonus: true, tit: "Fundo de conquistas", svd: "No Plano SVD: SVD Conquistas, fundo de 5%.",
        desc: "Um fundo dividido entre quem chega aos títulos de cima. Sem ninguém qualificado, o fundo fica retido.",
        ficha: function (c) { return "fundo de " + pct(c.bonus.conquistas.fundo) + " para " + nomeTit(c.bonus.conquistas.tituloMinimo) + " ou acima"; },
        corpo: function () { return '<div class="sim-grid sim-g3">' + campo("bonus.conquistas.fundo", "Fundo", { un: "% dos PB" }) + campo("bonus.conquistas.tituloMinimo", "Faixa inicial", { tipo: "select", opcoes: opcoesTitulos(), estrutural: "change" }) + "</div>"; } },
      { k: "pool", liga: "bonus.pool.cenarioAlternativo", bonus: true, tit: "Pool", svd: "No Plano SVD: SVD Pool, em estudo.",
        desc: "Um fundo único no lugar de carreira, liderança e conquistas: ligado, os três saem da conta.",
        ficha: function (c) { return "fundo de " + pct(c.bonus.pool.fundo) + " · substitui carreira, liderança e conquistas"; },
        corpo: function () { return '<div class="sim-grid sim-g3">' + campo("bonus.pool.fundo", "Fundo do pool", { un: "% dos PB" }) + '</div><p class="sim-alerta">O simulador mostra só o tamanho do fundo: a regra de divisão entre as pessoas não entra na projeção, então não há renda individual.</p>'; } }
    ] },
    { grupo: "Vendas ao cliente", itens: [
      { k: "link", liga: "bonus.link.ligado", bonus: true, tit: "Comissão do link (loja do consultor)", svd: "No Plano SVD: SVD Link, 25%.",
        desc: "O cliente compra pela loja do consultor, e o consultor recebe em reais, fora da base de PB.",
        ficha: function (c) { return pct(c.bonus.link.taxa) + " do valor " + (c.digital.base === "tabela" ? "de tabela" : "pago") + " · cupom até " + pct(c.digital.cupomTeto); },
        corpo: function () {
          return '<div class="sim-grid sim-g3">' + campo("bonus.link.taxa", "Comissão do titular", { un: "%" }) + campo("digital.cupomTeto", "Teto do cupom do cliente", { un: "%" }) +
            campo("digital.base", "Base da comissão", { tipo: "select", texto: true, opcoes: [["pago", "Valor pago pelo cliente"], ["tabela", "Valor de tabela"]] }) + "</div>";
        } },
      { k: "liderancaDigital", liga: "bonus.liderancaDigital.ligado", bonus: true, tit: "Liderança digital", svd: "No Plano SVD: SVD Liderança Digital, 5%.",
        desc: "O patrocinador de quem vendeu pelo link também recebe, em reais.",
        ficha: function (c) { return pct(c.bonus.liderancaDigital.taxa) + " para o patrocinador de quem vendeu"; },
        corpo: function () { return '<div class="sim-grid sim-g3">' + campo("bonus.liderancaDigital.taxa", "Comissão do patrocinador", { un: "%" }) + "</div>"; } },
      { k: "prime", liga: "bonus.prime.ligado", bonus: true, tit: "Assinatura (clube)", svd: "No Plano SVD: SVD Prime, opcional e desligado.",
        desc: "Mensalidade com benefícios para o consultor. Gera receita e volume para a rede.",
        ficha: function (c) { var p = c.bonus.prime; return brl2(p.preco) + " por mês · " + pct(p.adesao) + " dos consultores assinam"; },
        corpo: function () {
          return '<div class="sim-grid sim-g3">' + campo("bonus.prime.preco", "Preço mensal", { un: "R$" }) + campo("bonus.prime.adesao", "Consultores que assinam", { un: "%" }) +
            campo("bonus.prime.pbQual", "PB qualificáveis", { un: "PB" }) + campo("bonus.prime.pbBonif", "PB bonificáveis", { un: "PB" }) + campo("bonus.prime.margem", "Margem bruta", { un: "%" }) +
            campo("bonus.prime.beneficios", "Benefícios", { tipo: "texto" }) + "</div>";
        } }
    ] }
  ];
  function cadaBloco(fn) { BLOCOS.forEach(function (g) { g.itens.forEach(fn); }); }
  function blocoLigado(b) { return b.liga ? !!obter(b.liga) : true; }
  function fichaTexto(b) {
    var c = estado.cfg;
    if (!blocoLigado(b)) return "Fora do seu plano";
    if (trocadoPeloPool(b.k, c)) return "Substituído pelo pool";
    return b.ficha(c);
  }
  function nomePadrao(k) { return SIM.nomeBonus(Object.assign({}, estado.cfg, { nomes: {} }), k); }
  function htmlBloco(b) {
    var c = estado.cfg, ligado = blocoLigado(b), aberto = !!estado.abertos[b.k];
    var apagado = !ligado || trocadoPeloPool(b.k, c);
    var chave = b.liga
      ? '<label class="sim-switch"><input type="checkbox" role="switch" data-path="' + b.liga + '" data-estrutural="1"' + (ligado ? " checked" : "") + ' aria-label="' + esc(b.tit) + ' no meu plano"><span aria-hidden="true"></span></label>'
      : '<span class="sim-regra" title="Regra do plano: sempre presente">regra</span>';
    var apelido = b.bonus && c.nomes[b.k] && String(c.nomes[b.k]).trim() ? ' <em>· ' + esc(c.nomes[b.k]) + "</em>" : "";
    return '<div class="sim-bloco' + (apagado ? " desligado" : "") + '" id="bloco-' + b.k + '" data-bloco="' + b.k + '">' +
      '<div class="cab">' + chave +
      '<button type="button" class="abre" data-bloco-abre="' + b.k + '" aria-expanded="' + aberto + '" aria-controls="bc-' + b.k + '">' +
      '<span class="tit">' + (COR[b.k] ? '<span class="sim-cor" style="background:' + COR[b.k] + '"></span>' : "") + esc(b.tit) + apelido + "</span>" +
      '<span class="sub" data-ficha="' + b.k + '">' + fichaTexto(b) + "</span></button></div>" +
      '<div class="corpo" id="bc-' + b.k + '"' + (aberto ? "" : " hidden") + ">" +
      '<p class="desc">' + b.desc + ' <span class="svd">' + b.svd + "</span></p>" +
      (b.bonus ? '<div class="sim-grid sim-g3">' + campo("nomes." + b.k, "Nome no seu plano", { tipo: "texto", max: 40, placeholder: nomePadrao(b.k), estrutural: "change" }) + "</div>" : "") +
      b.corpo(c) + "</div></div>";
  }
  function montarPlano() {
    $("#pl-blocos").innerHTML = BLOCOS.map(function (g) {
      return '<div class="sim-grupo"><p class="sim-grupo-tit">' + g.grupo + "</p>" + g.itens.map(htmlBloco).join("") + "</div>";
    }).join("");
    renderSomas();
  }
  function montarModelos() {
    var c = estado.cfg;
    $("#pl-modelos").innerHTML = Object.keys(SIM.MODELOS).map(function (k) {
      var m = SIM.MODELOS[k];
      return '<button type="button" class="sim-modelo" data-modelo="' + k + '" aria-pressed="' + (c.modelo === k) + '"><strong>' + esc(m.nome) + "</strong><span>" + esc(m.resumo) + "</span></button>";
    }).join("");
  }
  function trocarModelo(k) {
    var mudou = SIM.planoMudou(estado.cfg);
    if (estado.cfg.modelo === k && !mudou) return;
    if (mudou && !window.confirm("Trocar de modelo desfaz as mudanças que você fez nas regras do plano. A rede e os custos continuam como estão. Trocar?")) return;
    estado.cfg = SIM.aplicarModelo(estado.cfg, k);
    estado.abertos = {};
    evento("simulador_modelo", { modelo: k });
    marcarUso();
    montarModelos(); remontar(); agendar();
    aviso("Modelo " + SIM.MODELOS[k].nome + " aplicado. Agora ajuste bloco a bloco.");
  }
  function limiteDe(path) {
    return path === "titulos" ? LIM.titulos : path === "adesao.opcoes" ? LIM.kits : path === "bonus.lideranca.geracoes" ? LIM.geracoes : LIM.niveis;
  }
  function novoItem(path, arr) {
    var u = arr[arr.length - 1];
    if (path === "titulos") return { nome: "Título " + (arr.length + 1), meta: Math.round((+u.meta || 1000) * 2), vml: Math.round((+u.vml || 500) * 2), linhas: +u.linhas || 0, taxa: (+u.taxa || 0) + 1 };
    if (path === "adesao.opcoes") return { pb: (+u.pb || 0) + 20, peso: 25 };
    return +u || 0;
  }
  function acaoLista(btn) {
    var path = btn.dataset.lista, arr = obter(path), acao = btn.dataset.acao, i = +btn.dataset.i;
    if (acao === "mais" && arr.length < limiteDe(path)) arr.push(novoItem(path, arr));
    else if (acao === "menos" && arr.length > 1) { i = arr.length - 1; arr.pop(); }
    else if (acao === "tirar" && arr.length > 1) arr.splice(i, 1);
    else return;
    // tirar um titulo do meio nao pode mudar o titulo de lider e a faixa de conquistas
    if (path === "titulos" && acao !== "mais") {
      var B = estado.cfg.bonus;
      if (B.lideranca.tituloLider > i) B.lideranca.tituloLider--;
      if (B.conquistas.tituloMinimo > i) B.conquistas.tituloMinimo--;
    }
    marcarUso(); remontar(); agendar();
  }
  function abrirBloco(k) {
    estado.abertos[k] = true;
    montarPlano();
    var el = $("#bloco-" + k);
    if (el) { el.scrollIntoView({ block: "start", behavior: "smooth" }); var b = $(".abre", el); if (b) b.focus({ preventScroll: true }); }
  }
  // remonta os blocos depois de mudanca de estrutura, devolvendo o foco
  function remontar() {
    var f = document.activeElement, sel = null, dentro = f && f.closest ? f.closest("#pl-blocos, #b-lista") : null;
    if (dentro && f.dataset) {
      if (f.dataset.path) sel = '[data-path="' + f.dataset.path + '"]';
      else if (f.dataset.acao) sel = '[data-acao="' + f.dataset.acao + '"][data-lista="' + f.dataset.lista + '"]' + (f.dataset.i ? '[data-i="' + Math.min(+f.dataset.i, obter(f.dataset.lista).length - 1) + '"]' : "");
      else if (f.dataset.blocoAbre) sel = '[data-bloco-abre="' + f.dataset.blocoAbre + '"]';
    }
    SIM.normalizar(estado.cfg);
    montarPlano(); montarBonus();
    if (sel) {
      var n = $(sel, dentro.id === "b-lista" ? $("#b-lista") : $("#pl-blocos"));
      if (n && !n.disabled) n.focus({ preventScroll: true });
      else if (f.dataset.lista) { var alt = $('[data-lista="' + f.dataset.lista + '"]:not([disabled])', $("#pl-blocos")); if (alt) alt.focus({ preventScroll: true }); }
    }
  }
  function renderFichas() {
    cadaBloco(function (b) { var el = $('[data-ficha="' + b.k + '"]'); if (el) el.innerHTML = fichaTexto(b); });
    renderSomas();
  }
  function renderResumoPlano() {
    var c = estado.cfg, m = SIM.MODELOS[c.modelo] || SIM.MODELOS.svd, mudou = SIM.planoMudou(c);
    $("#pl-nome").textContent = (c.nomePlano || "").trim() || "Meu plano";
    $("#pl-base").textContent = "A partir do modelo " + m.nome + (mudou ? ", com as suas mudanças." : ", sem mudanças.");
    $$("[data-modelo]").forEach(function (b) { b.setAttribute("aria-pressed", String(b.dataset.modelo === c.modelo)); });
    var tr = SIM.tetoPorPedido(c, "recompra"), ta = SIM.tetoPorPedido(c, "adesao");
    function linhaTeto(rot, t) {
      return '<div class="sim-teto-linha"><span>' + rot + " de 100 PB (" + brl(t.pago) + ")</span><strong>até " + brl2(t.total) + "</strong><small>" + pct(t.pct) + " do valor pago" +
        (t.itens.length ? ": " + t.itens.map(function (i) { return esc(nome(i.k)) + " " + brl2(i.valor); }).join(" · ") : "") + "</small></div>";
    }
    $("#pl-teto").innerHTML = linhaTeto("Adesão", ta) + linhaTeto("Recompra", tr) +
      (tr.binario ? '<p class="sim-aviso" style="margin-top:4px">O binário fica fora desta conta: cada pedido conta na perna de todos os de cima, e o limite é o teto ' + (c.bonus.binario.teto > 0 ? "de " + brl(c.bonus.binario.teto) + " " : "") + "por consultor.</p>" : "");
    var tem = [], fora = [];
    cadaBloco(function (b) {
      var rot = b.bonus ? nome(b.k) : b.tit;
      if (blocoLigado(b) && !trocadoPeloPool(b.k, c)) tem.push("<li><strong>" + esc(rot) + "</strong><span>" + b.ficha(c) + "</span></li>");
      else if (b.bonus) fora.push(esc(rot));
    });
    $("#pl-ficha").innerHTML = tem.join("");
    $("#pl-fora").innerHTML = fora.length ? "Fora do plano: " + fora.join(", ") + "." : "";
    var r = atual(), v = SIM.veredito(r), box = $("#pl-result");
    box.className = "sim-veredito mini " + v.nivel;
    box.innerHTML = '<p class="sim-aviso" style="margin:0">Mês 12 · cenário ' + NOME_CENARIO[estado.cenario] + '</p><div class="tit"><span aria-hidden="true">' + ICONE[v.nivel] + "</span>" + TEXTO_VEREDITO[v.nivel][0] + "</div>" +
      "<p>Os bônus somam " + pct(v.payout) + " do faturamento e, depois de produto, impostos, frete e operação, " + (v.sobra >= 0 ? "sobram " + pct(v.sobra) + "." : "faltam " + pct(-v.sobra) + ".") + "</p>";
  }

  // ------------------------------------------------------------ sua rede
  function gruposRede() {
    var a = estado.cfg.ativacao, cinco = a.niveisCinco === 1 ? "o 1º nível" : "os primeiros " + a.niveisCinco + " níveis";
    return [
      ["Como a rede começa", [
        ["operacao.consultoresIniciais", "Consultores iniciais", { step: 1, min: 1 }],
        ["operacao.linhasFundadoras", "Linhas fundadoras", { step: 1, min: 1, ajuda: "Quantas redes independentes começam a operação." }],
        ["operacao.largura", "Indicados ativos por consultor", { step: 0.1, min: 1.2, ajuda: "Quanto maior, mais larga e rasa a rede." }]]],
      ["Como ela cresce", [
        ["operacao.novosCadastros", "Novos cadastros por mês", { step: 1, min: 0 }],
        ["operacao.crescimentoCadastros", "Crescimento dos cadastros", { un: "% ao mês" }],
        ["operacao.tetoCadastros", "Teto de cadastros por mês", { step: 1, ajuda: "A rede não cresce para sempre." }],
        ["operacao.conversao", "Cadastros que pagam a adesão", { un: "%" }],
        ["operacao.perdaMensal", "Perda mensal de consultores", { un: "%" }],
        ["operacao.renovacao", "Renovação anual dos iniciais", { un: "%" }],
        ["operacao.pbRenovacao", "PB da renovação", { un: "PB" }]]],
      ["O que os consultores compram", [
        ["operacao.ativacaoDez", "Compra única de " + int(a.pbDez) + " PB ou mais", { un: "% dos consultores", ajuda: "Libera todos os níveis." }],
        ["operacao.ativacaoCinco", "Compra única de " + int(a.pbCinco) + " a " + int(Math.max(a.pbCinco, a.pbDez - 1)) + " PB", { un: "% dos consultores", ajuda: "Libera " + cinco + "." }],
        ["operacao.pbMedioDez", "PB médio da compra maior", { un: "PB" }],
        ["operacao.pbMedioCinco", "PB médio da compra menor", { un: "PB" }],
        ["operacao.recompraExtra", "Consultores com pedido extra no mês", { un: "%", ajuda: "Pedido separado: soma PB, não libera nível." }],
        ["operacao.pbRecompraExtra", "PB do pedido extra", { un: "PB" }]]],
      ["Clientes e vendas pelo link", [
        ["operacao.consumidores", "Clientes por consultor", { step: 0.1 }],
        ["operacao.pedidosConsumidor", "Pedidos por cliente por mês", { step: 0.05 }],
        ["operacao.ticketDigital", "Ticket da venda digital", { un: "R$, preço ao cliente" }],
        ["operacao.consumidorInteligente", "Clientes como consumidor inteligente", { un: "%" }],
        ["operacao.usoCupom", "Pedidos com cupom", { un: "%" }],
        ["operacao.cupomMedio", "Cupom médio", { un: "%", ajuda: "Limitado ao teto do cupom." }]]],
      ["Produto e preço", [
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
  }
  function montarPremissas() {
    $("#p-grupos").innerHTML = gruposRede().map(function (g) {
      return '<div class="sim-card"><h3>' + g[0] + '</h3><div class="sim-grid sim-g2" style="margin-top:12px">' +
        g[1].map(function (f) { return campo(f[0], f[1], f[2]); }).join("") + "</div>" +
        (g[0] === "Produto e preço" ? '<p class="sim-aviso" id="p-margens"></p>' : "") + "</div>";
    }).join("");
    var cfg = estado.cfg, chaves = Object.keys(cfg.cenarios.conservador);
    var h = '<table class="sim-tabela" style="min-width:560px"><thead><tr><th>Premissa</th><th>Conservador</th><th>Base</th><th>Expansão</th></tr></thead><tbody>';
    chaves.forEach(function (k) {
      var mult = ["novosCadastros", "crescimentoCadastros", "consumidores"].indexOf(k) >= 0;
      h += "<tr><td>" + esc(rotuloOp(k)) + '<br><small style="color:var(--sim-muted)">' + (mult ? "multiplica a base" : "soma pontos à base") + "</small></td>" +
        '<td><input class="sim-in curto" type="number" step="any" data-path="cenarios.conservador.' + k + '" value="' + cfg.cenarios.conservador[k] + '" aria-label="Conservador: ' + esc(rotuloOp(k)) + '"></td>' +
        "<td>" + (mult ? "× 1" : "+ 0") + "</td>" +
        '<td><input class="sim-in curto" type="number" step="any" data-path="cenarios.expansao.' + k + '" value="' + cfg.cenarios.expansao[k] + '" aria-label="Expansão: ' + esc(rotuloOp(k)) + '"></td></tr>';
    });
    $("#p-cenarios").innerHTML = h + "</tbody></table>";
    renderMargens();
  }
  function renderMargens() {
    var c = estado.cfg, cmv = c.cmv / 100, el = $("#p-margens");
    if (!el) return;
    var mA = 1 - cmv / (1 - c.descontos.adesao / 100), mR = 1 - cmv / (1 - c.descontos.recompra / 100), mD = 1 - cmv;
    el.innerHTML = "Margem bruta por canal (antes de bônus): adesão " + pct(mA * 100) + " · recompra " + pct(mR * 100) + " · venda ao cliente " + pct(mD * 100) +
      ". Desconto comercial não é bônus: o faturamento já é o valor pago.";
  }
  function renderOperacao() {
    var u = atual().meses[11], e = u.estrutura;
    $("#o-cenario").textContent = "cenário " + NOME_CENARIO[estado.cenario].toLowerCase();
    $("#o-ativos").textContent = int(u.ativos);
    $("#o-niveis").textContent = e.profundidade;
    $("#o-titulos").textContent = int(soma(u.titulos));
    $("#o-texto").textContent = e.linhas + " linhas fundadoras com " + int(e.porLinha) + " consultores cada, " + fDec.format(e.largura) + " indicados ativos por consultor. " +
      int(u.adesoes) + " adesões e " + int(u.saem) + " saídas no mês 12.";
    if (estado.aba === "operacao" && !imprimindo) renderArvore($("#g-arvore-op"), u);
  }

  // -------------------------------------------------------- verificacoes
  function renderVerificacoes() {
    var v = SIM.verificacoes(), ok = v.filter(function (x) { return x.ok; }).length;
    $("#v-resumo").textContent = ok + " de " + v.length + " verificações passaram (Plano SVD, versão " + SIM.VERSAO_ATUAL.replace("svd-", "") + ", e blocos do construtor).";
    $("#v-lista").innerHTML = v.map(function (x) {
      var fmt = function (n) { return Number.isInteger(n) && x.nome.indexOf("PB") >= 0 && x.nome.indexOf("níveis") >= 0 && n <= 15 ? n + " níveis" : (x.nome.indexOf("Desconto") === 0 ? n + "%" : (x.nome.indexOf("volta") >= 0 ? n + " PB" : brl2(n))); };
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
    var c = estado.cfg, e = (c.empresa || "").trim(), m = SIM.MODELOS[c.modelo] || SIM.MODELOS.svd;
    var plano = (c.nomePlano || "").trim() || "meu plano";
    var qual = plano + (plano !== m.nome ? ", a partir do modelo " + m.nome : SIM.planoMudou(c) ? ", com mudanças" : "");
    var txt = "Olá! Montei o meu plano no simulador do Sistema Venda Direta (" + qual + ")" +
      (e ? " para a " + e : "") + " e quero conversar sobre colocar esse plano para rodar. Minha simulação: " + linkCompartilhavel();
    $$(".sim-cta-zap").forEach(function (a) { a.href = "https://wa.me/5511994566726?text=" + encodeURIComponent(txt); });
  }

  // --------------------------------------------------------------- geral
  function atualizar() {
    calcular();
    renderHero(); renderRede(); renderSaude(); renderValoresBonus(); renderMargens(); renderRodape(); atualizarCta(); renderCarreira();
    renderFichas(); renderResumoPlano(); renderOperacao();
  }
  function montarTudo() {
    $$("[data-path]").forEach(function (el) {
      if (el.closest("#b-lista") || el.closest("#p-grupos") || el.closest("#p-cenarios") || el.closest("#pl-blocos")) return;
      var v = obter(el.dataset.path);
      if (el.type === "checkbox") el.checked = !!v; else el.value = v == null ? "" : v;
    });
    montarModelos(); montarPlano(); montarBonus(); montarPremissas(); renderTitulosEmpresa(); atualizar();
  }
  function abrirAba(a) {
    estado.aba = a;
    $$(".sim-abas [role=tab]").forEach(function (b) { b.setAttribute("aria-selected", String(b.dataset.aba === a)); });
    $$(".sim-painel").forEach(function (p) { p.hidden = p.id !== "aba-" + a; });
    if (a === "operacao") montarPremissas();
    redesenhar();
  }
  function redesenhar() {
    if (!estado.res.base) return;
    if (estado.aba === "rede") { renderArvore($("#g-arvore"), mesSel()); grafBonus($("#g-bonus"), atual().meses); }
    if (estado.aba === "saude") grafCaixa($("#g-caixa"));
    if (estado.aba === "operacao") renderArvore($("#g-arvore-op"), atual().meses[11]);
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
  function irPara(aba) {
    abrirAba(aba);
    var nav = $(".sim-abas");
    if (nav) window.scrollTo({ top: nav.getBoundingClientRect().top + window.pageYOffset - 4, behavior: "smooth" });
  }

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
    $$("[data-ir]").forEach(function (b) { b.addEventListener("click", function () { irPara(b.dataset.ir); evento("simulador_aba", { aba: b.dataset.ir }); }); });
    $$("[data-tabela]").forEach(function (b) {
      b.addEventListener("click", function () {
        var alvo = $("#" + b.dataset.tabela), abrir = alvo.hidden;
        alvo.hidden = !abrir; b.textContent = abrir ? "Esconder tabela" : "Ver em tabela";
      });
    });
    // seu plano
    $("#pl-modelos").addEventListener("click", function (ev) { var t = ev.target.closest("[data-modelo]"); if (t) trocarModelo(t.dataset.modelo); });
    $("#pl-blocos").addEventListener("click", function (ev) {
      var t = ev.target.closest("button");
      if (!t) return;
      if (t.dataset.blocoAbre) {
        var k = t.dataset.blocoAbre, ab = !estado.abertos[k];
        estado.abertos[k] = ab; t.setAttribute("aria-expanded", String(ab)); $("#bc-" + k).hidden = !ab;
        if (ab) evento("simulador_bloco", { bloco: k });
      } else if (t.dataset.acao) acaoLista(t);
      else if (t.dataset.abrir) abrirBloco(t.dataset.abrir);
    });
    $("#b-lista").addEventListener("click", function (ev) {
      var t = ev.target.closest("[data-editar]");
      if (!t) return;
      ev.preventDefault();
      abrirAba("plano"); abrirBloco(t.dataset.editar);
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
      estado.cfg = SIM.normalizar(SIM.planoPadrao()); estado.cenario = "base"; estado.abertos = {};
      try { localStorage.removeItem(CHAVE); } catch (e) {}
      history.replaceState(null, "", location.pathname + location.search);
      montarTudo(); aviso("Plano SVD original restaurado.");
    });
    $("#btn-pdf").addEventListener("click", function () {
      var c = estado.cfg, e = (c.empresa || "").trim(), m = SIM.MODELOS[c.modelo] || SIM.MODELOS.svd;
      $("#pdf-plano").textContent = (c.nomePlano || "").trim() || "Meu plano";
      $("#pdf-data").textContent = (e ? e + " · " : "") + "Cenário " + NOME_CENARIO[estado.cenario] + " · modelo " + m.nome + (SIM.planoMudou(c) ? " com mudanças" : "") +
        " · Plano SVD " + SIM.VERSAO_ATUAL.replace("svd-", "") + " · gerado em " + new Date().toLocaleDateString("pt-BR") + " · projeção ilustrativa, não é renda prometida";
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
    abrirAba(estado.origem === "link" ? "rede" : "plano");
    if (estado.origem === "link") aviso("Simulação aberta a partir do link compartilhado.");
    else if (estado.origem === "salvo") aviso("Sua simulação salva foi carregada.");
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", iniciar); else iniciar();
})();
