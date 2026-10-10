#!/usr/bin/env python3
"""
[Modulo Ads — relatorio diario da campanha]
@Author: Andre Gomes ( @acidcode )
@since 2026-09-16

POR QUE ISSO EXISTE: a leitura diaria vinha sendo feita a mao, e o valor dela e
justamente nao depender de alguem lembrar. Erro que isso teria pego cedo: o
"ate 31/08" ficou 12 dias no ar depois de vencido, e a meta de parcela em 70%
levou 4 dias pra mostrar que dobrava o CPC sem trazer clique.

CANAL SEPARADO DO CONTEUDO. O relatorio e texto; entregar e outro problema.
Hoje sai por e-mail pelo SMTP do .env — o mesmo do formulario do site, porque o
msmtp da maquina esta com a senha de app do Gmail revogada. WhatsApp entra como
mais um adaptador quando houver provedor, sem tocar na parte que gera o numero.

O QUE ELE OLHA, na ordem da secao 2 da guideline — impressao, parcela e motivo da
perda, termos, CTR, custo, conversao. E so alerta quando ha o que dizer: relatorio
que grita todo dia vira relatorio que ninguem le.

Uso:
  python3 relatorio-diario.py                  # imprime na tela
  python3 relatorio-diario.py --email          # manda por e-mail
  python3 relatorio-diario.py --email --para=alguem@dominio.com
  python3 relatorio-diario.py --dia=2026-10-05   # reprocessa um dia passado
"""
import sys
import warnings
from datetime import date, timedelta

warnings.filterwarnings("ignore")
sys.path.insert(0, "/data/coderush-sites/automation/ads")
from _conta import cliente, env, resolver  # noqa: E402

# abaixo disso, variacao e ruido: 35 impressoes/dia nao sustentam conclusao diaria
MIN_CLIQUES_P_CONCLUSAO = 5


def arg(nome, padrao=None):
    for a in sys.argv[1:]:
        if a.startswith(f"--{nome}="):
            return a.split("=", 1)[1]
    return padrao


def brl(v):
    return f"R$ {v:,.2f}".replace(",", "X").replace(".", ",").replace("X", ".")


def coletar(ga, cid, ini, fim):
    q = f"""SELECT campaign.name, metrics.impressions, metrics.clicks,
            metrics.cost_micros, metrics.conversions, metrics.all_conversions, metrics.invalid_clicks,
            metrics.search_impression_share,
            metrics.search_top_impression_share,
            metrics.search_budget_lost_impression_share,
            metrics.search_rank_lost_impression_share
            FROM campaign WHERE segments.date BETWEEN '{ini}' AND '{fim}'
            AND campaign.status = 'ENABLED'"""
    linhas = []
    for r in ga.search(customer_id=cid, query=q):
        m = r.metrics
        if not m.impressions:
            continue
        linhas.append({
            "nome": r.campaign.name, "impr": m.impressions, "cliques": int(m.clicks),
            "custo": m.cost_micros / 1e6, "conv": m.conversions,
            "conv_all": m.all_conversions, "invalidos": int(m.invalid_clicks),
            "is": m.search_impression_share * 100,
            "topo": m.search_top_impression_share * 100,
            "p_orc": m.search_budget_lost_impression_share * 100,
            "p_rank": m.search_rank_lost_impression_share * 100,
        })
    return linhas



def dias_sem_lead(ref=None):
    """
    Dias desde o ultimo lead no sqlite — formulario OU clique de WhatsApp.

    O Ads nao sabe responder isso: a acao de conversao da conta conta clique de
    zap, mas nao conta lead que chegou pelo organico nem pelo formulario. Quem
    tem a verdade e o banco do site. Entre 06 e 09/10 foram quatro dias secos a
    R$ 172 e nada avisou, porque ninguem estava olhando essa tabela de manha.

    `ref` e a data do relatorio. Sem ela, reprocessar 05/10 hoje diria "5 dias sem
    lead" — numero de hoje carimbado num dia em que entraram quatro. Relatorio
    antigo tem que contar o que se sabia naquele dia.
    """
    import sqlite3
    base = ref or date.today()
    caminho = "/data/coderush-sites/sistemavendadireta/storage/leads.sqlite"
    try:
        db = sqlite3.connect(f"file:{caminho}?mode=ro", uri=True)
        ultimo = db.execute(
            "SELECT MAX(created_at) FROM leads WHERE date(created_at) <= ?",
            [base.isoformat()]).fetchone()[0]
        db.close()
        if not ultimo:
            return 0
        return (base - date.fromisoformat(str(ultimo)[:10])).days
    except Exception:
        return 0  # banco fora do ar nao pode derrubar o relatorio


def montar(ga, cid):
    hoje = date.today()
    # --dia=AAAA-MM-DD reprocessa um dia passado. Serve pra conferir um alerta que
    # a gente so entendeu depois (foi assim com os 29% de invalidos de 05/10) sem
    # ter que esperar o proximo caso acontecer.
    escolhido = arg("dia")
    ontem = date.fromisoformat(escolhido) if escolhido else hoje - timedelta(days=1)
    sete = ontem - timedelta(days=6)

    ont = coletar(ga, cid, ontem.isoformat(), ontem.isoformat())
    if not ont:
        return (f"Campanha SVD — {ontem.strftime('%d/%m')}",
                "Nenhuma impressao ontem. Verificar se a campanha esta ativa e com "
                "faturamento em dia — campanha sem veiculacao nao avisa.")

    L = [f"CAMPANHA SVD — {ontem.strftime('%d/%m/%Y')}", ""]
    tc = tcu = 0.0
    tk = 0
    for c in ont:
        tc += c["impr"]; tk += int(c["cliques"]); tcu += c["custo"]
        cpc = c["custo"] / c["cliques"] if c["cliques"] else 0
        ctr = c["cliques"] / c["impr"] * 100
        L += [f"{c['nome']}",
              f"  {c['impr']} impressoes · {c['cliques']} cliques · {brl(c['custo'])}",
              f"  CPC {brl(cpc)} · CTR {ctr:.1f}% · cliques de WhatsApp {c['conv_all']:.0f}"
              + (f" · invalidos {c['invalidos']}" if c["invalidos"] else ""),
              f"  parcela {c['is']:.0f}% (topo {c['topo']:.0f}%) · "
              f"perda: orcamento {c['p_orc']:.0f}%, ranking {c['p_rank']:.0f}%", ""]
    if len(ont) > 1:
        L += [f"TOTAL  {tc} impr · {tk} cliques · {brl(tcu)} · "
              f"CPC {brl(tcu / tk if tk else 0)}", ""]

    # ── comparacao com a semana, pra dizer se ontem foi fora da curva ──
    sem = coletar(ga, cid, sete.isoformat(), ontem.isoformat())
    if sem:
        s_cu = sum(c["custo"] for c in sem)
        s_k = sum(c["cliques"] for c in sem)
        media = s_cu / 7
        cpc_sem = s_cu / s_k if s_k else 0
        cpc_ont = tcu / tk if tk else 0
        L += ["ULTIMOS 7 DIAS",
              f"  {brl(s_cu)} · {s_k} cliques · CPC medio {brl(cpc_sem)} · {brl(media)}/dia"]
        if media:
            d = (tcu - media) / media * 100
            if abs(d) > 40:
                L.append(f"  >> ontem {'gastou' if d > 0 else 'gastou'} {abs(d):.0f}% "
                         f"{'acima' if d > 0 else 'abaixo'} da media")
        if cpc_sem and cpc_ont and abs(cpc_ont - cpc_sem) / cpc_sem > 0.35:
            L.append(f"  >> CPC de ontem {brl(cpc_ont)} vs {brl(cpc_sem)} na semana")
        L.append("")

    # ── termos que gastaram, so os com clique ──
    q = f"""SELECT search_term_view.search_term, metrics.clicks, metrics.cost_micros
            FROM search_term_view WHERE segments.date = '{ontem.isoformat()}'
            AND metrics.clicks > 0 ORDER BY metrics.cost_micros DESC LIMIT 8"""
    termos = [(r.search_term_view.search_term, r.metrics.clicks, r.metrics.cost_micros / 1e6)
              for r in ga.search(customer_id=cid, query=q)]
    if termos:
        L.append("TERMOS QUE GERARAM CLIQUE")
        for t, k, c in termos:
            L.append(f"  {brl(c):>10}  {k}c  {t}")
        L.append("")

    # ── alertas: so o que exige acao ──
    #
    # Revisto em 10/10/2026. O relatorio de 09/10 estava CERTO — mostrou "gastou 82%
    # acima da media" e o "vdi venda direta" no topo dos termos. Mesmo assim o dia de
    # R$ 74,81 so foi notado quando o Andre perguntou.
    #
    # A culpa e da secao de alerta, que disparava "N cliques e nenhuma conversao"
    # TODO santo dia: a acao whatsapp_click esta como nao-primaria na conta, entao
    # metrics.conversions e sempre 0, aconteca o que acontecer. Alerta que toca todo
    # dia nao e alerta, e barulho — e ensina a pular a secao onde o resto aparece.
    al = []
    for c in ont:
        if c["p_orc"] > 30:
            al.append(f"{c['nome']}: {c['p_orc']:.0f}% de perda por ORCAMENTO — "
                      f"o teto diario esta cortando entrega")
        # all_conversions inclui o clique de WhatsApp; conversions nao, enquanto a
        # acao nao for marcada como primaria. Sem isso o alerta nunca cala.
        if c["cliques"] >= MIN_CLIQUES_P_CONCLUSAO and c["conv_all"] == 0:
            al.append(f"{c['nome']}: {c['cliques']} cliques e nenhum clique de WhatsApp")
        cpc = c["custo"] / c["cliques"] if c["cliques"] else 0
        if cpc > 8:
            al.append(f"{c['nome']}: CPC de {brl(cpc)} — conferir se o lance nao "
                      f"esta comprando exposicao marginal cara")
        # 09/10: o Google filtrou 0 invalidos, mas em 05/10 filtrou 29% e ninguem viu.
        # Taxa alta e sinal de trafego ruim no leilao, mesmo com o reembolso feito.
        if c["invalidos"] and c["cliques"]:
            taxa = c["invalidos"] / (c["cliques"] + c["invalidos"]) * 100
            if taxa > 15:
                al.append(f"{c['nome']}: {c['invalidos']} cliques invalidos "
                          f"({taxa:.0f}%) filtrados pelo Google — trafego de baixa "
                          f"qualidade no leilao")

    # concentracao de termo: um unico termo levando a maior parte do dia e o padrao
    # do "vdi venda direta" (36% de 09/10) e do "shopee" antes dele. Quase sempre e
    # busca de marca entrando por correspondencia ampla demais.
    if termos and tcu > 0:
        t_top, k_top, c_top = termos[0]
        fatia = c_top / tcu * 100
        if fatia > 25 and c_top > 10:
            al.append(f'"{t_top}" levou {brl(c_top)} = {fatia:.0f}% do dia — '
                      f"conferir se e intencao de produto ou nome de empresa")

    dias_sem = dias_sem_lead(ontem)
    if dias_sem >= 3:
        al.append(f"{dias_sem} dias sem nenhum lead no banco (nem formulario, "
                  f"nem clique de WhatsApp)")

    if al:
        L.append("PRECISA DE OLHO")
        L += [f"  - {a}" for a in al]
        L.append("")

    L.append("painel: https://sistemavendadireta.com.br/painel-leads/")
    return f"SVD {ontem.strftime('%d/%m')} — {brl(tcu)}, {tk} cliques", "\n".join(L)


def enviar_email(assunto, corpo, para):
    """Usa o SMTP do .env, o mesmo que o formulario do site.

    NAO usa o msmtp da maquina de proposito: em 16/09/2026 a senha de app do
    Gmail dele estava revogada ("Username and Password not accepted"). A
    credencial do .env e a que o site usa em producao todo dia, entao e a que
    tem mais chance de continuar valendo — e quando quebrar, quebra junto com o
    formulario de contato e alguem percebe.

    Porta 465 e SSL implicito (SMTP_SSL), nao STARTTLS. Errar isso devolve
    timeout, nao erro de autenticacao — o que manda a investigacao pro lado
    errado.
    """
    import smtplib
    import ssl
    from email.message import EmailMessage

    e = env()
    msg = EmailMessage()
    msg["From"] = f"{e.get('MAIL_FROM_NAME', 'CodeRush')} <{e['MAIL_FROM_ADDRESS']}>"
    msg["To"] = para
    msg["Subject"] = assunto
    msg.set_content(corpo)

    porta = int(e.get("MAIL_PORT", 465))
    ctx = ssl.create_default_context()
    if porta == 465:
        with smtplib.SMTP_SSL(e["MAIL_HOST"], porta, timeout=30, context=ctx) as s:
            s.login(e["MAIL_USERNAME"], e["MAIL_PASSWORD"])
            s.send_message(msg)
    else:
        with smtplib.SMTP(e["MAIL_HOST"], porta, timeout=30) as s:
            s.starttls(context=ctx)
            s.login(e["MAIL_USERNAME"], e["MAIL_PASSWORD"])
            s.send_message(msg)


def enviar_whatsapp(assunto, corpo, destino):
    """Adaptador vazio de proposito.

    Enviar WhatsApp exige provedor (Meta Cloud API, Z-API, Evolution). Quando
    houver, o que entra aqui e so a chamada HTTP — o relatorio acima nao muda uma
    linha. Deixar explicito e melhor que fingir suporte que nao existe.
    """
    raise NotImplementedError(
        "canal WhatsApp ainda sem provedor. Ao contratar, implementar aqui: "
        "POST com o texto de `corpo` para o numero `destino`.")


def main():
    cid = resolver(sys.argv)
    ga = cliente().get_service("GoogleAdsService")
    assunto, corpo = montar(ga, cid)

    if "--email" in sys.argv:
        para = arg("para", env().get("MAIL_TO_ADDRESS", ""))
        if not para:
            sys.exit("sem destinatario: use --para= ou defina MAIL_TO_ADDRESS no .env")
        enviar_email(assunto, corpo, para)
        print(f"  enviado para {para}: {assunto}")
    elif "--whatsapp" in sys.argv:
        enviar_whatsapp(assunto, corpo, arg("para", ""))
    else:
        print(corpo)


if __name__ == "__main__":
    main()
