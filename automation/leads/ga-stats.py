#!/usr/bin/env python3
"""
[Modulo Leads SVD — snapshot de estatisticas do GA4]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-03
Roda no HOST (cron, a cada 3h): consulta a Data API do GA4 com a service account
e grava um JSON que o painel-leads renderiza. Sem credencial no container web.
Desde 2026-08-10 tambem puxa investimento e cliques da API do Google Ads.
Saida: sistemavendadireta/storage/ga-stats.json
"""
import json, os, datetime

os.environ["GOOGLE_APPLICATION_CREDENTIALS"] = "/root/.config/svd-analytics/sa-key.json"
from google.analytics.data_v1beta import (BetaAnalyticsDataClient, RunReportRequest,
    DateRange, Dimension, Metric, OrderBy)

OUT = "/data/coderush-sites/sistemavendadireta/storage/ga-stats.json"
PROPS = [("properties/379315278", "sistemavendadireta.com.br")]
FOCUS_EVENTS = ["generate_lead", "whatsapp_click", "simulator_use", "form_start", "purchase"]

c = BetaAnalyticsDataClient()

def report(prop, dims, mets, limit=10, days="28daysAgo", order_metric=None):
    req = RunReportRequest(property=prop,
        date_ranges=[DateRange(start_date=days, end_date="today")],
        dimensions=[Dimension(name=d) for d in dims],
        metrics=[Metric(name=m) for m in mets], limit=limit)
    if order_metric:
        req.order_bys = [OrderBy(metric=OrderBy.MetricOrderBy(metric_name=order_metric), desc=True)]
    return c.run_report(req)

def rows(resp):
    return [{"d": [v.value for v in r.dimension_values],
             "m": [v.value for v in r.metric_values]} for r in resp.rows]

out = {"gerado_em": datetime.datetime.now(datetime.timezone(datetime.timedelta(hours=-3))).isoformat(timespec="seconds"),
       "sites": []}

for prop, host in PROPS:
    site = {"host": host}
    for label, days in (("d7", "7daysAgo"), ("d28", "28daysAgo")):
        r = report(prop, [], ["activeUsers", "sessions", "screenPageViews"], days=days)
        m = r.rows[0].metric_values if r.rows else None
        site[label] = {"usuarios": int(m[0].value) if m else 0,
                       "sessoes": int(m[1].value) if m else 0,
                       "pageviews": int(m[2].value) if m else 0}
    # serie diaria geral (90d, o painel filtra 7/28/90 em cima disso)
    diario = report(prop, ["date"], ["activeUsers", "sessions"], limit=100, days="90daysAgo")
    dias = sorted(rows(diario), key=lambda r: r["d"][0])
    site["diario"] = [{"data": r["d"][0], "usuarios": int(r["m"][0]), "sessoes": int(r["m"][1])} for r in dias]

    # serie diaria POR PAGINA (top 15 paginas, 90d) — alimenta o drill-down do painel
    pg_diario = report(prop, ["pagePath", "date"], ["screenPageViews", "activeUsers"],
                       limit=2000, days="90daysAgo", order_metric="screenPageViews")
    por_pagina = {}
    for r in rows(pg_diario):
        caminho, data = r["d"][0], r["d"][1]
        por_pagina.setdefault(caminho, []).append(
            {"data": data, "views": int(r["m"][0]), "usuarios": int(r["m"][1])})
    top = sorted(por_pagina.items(), key=lambda kv: sum(d["views"] for d in kv[1]), reverse=True)[:15]
    site["paginas_diario"] = {k: sorted(v, key=lambda d: d["data"]) for k, v in top}
    ev = report(prop, ["eventName"], ["eventCount"], limit=30)
    site["eventos"] = {r2["d"][0]: int(r2["m"][0]) for r2 in rows(ev) if r2["d"][0] in FOCUS_EVENTS}
    src = report(prop, ["sessionSourceMedium"], ["sessions"], limit=8, order_metric="sessions")
    site["fontes"] = [{"fonte": r2["d"][0], "sessoes": int(r2["m"][0])} for r2 in rows(src)]
    pg = report(prop, ["pagePath"], ["screenPageViews"], limit=10, order_metric="screenPageViews")
    site["paginas"] = [{"pagina": r2["d"][0], "views": int(r2["m"][0])} for r2 in rows(pg)]
    if prop.endswith("379315278"):
        camp = report(prop, ["sessionCampaignName"], ["sessions", "keyEvents"], limit=10, order_metric="sessions")
        site["campanhas"] = [{"campanha": r2["d"][0], "sessoes": int(r2["m"][0]),
                              "conversoes": float(r2["m"][1])} for r2 in rows(camp)]
        # acessos vindos das campanhas, por periodo — cruza com o custo do Ads no painel
        site["campanhas_periodo"] = {}
        for label, days in (("hoje", "today"), ("d7", "7daysAgo"), ("d28", "28daysAgo")):
            cp = report(prop, ["sessionCampaignName", "sessionSourceMedium"],
                        ["sessions", "keyEvents", "bounceRate", "averageSessionDuration"],
                        limit=25, days=days, order_metric="sessions")
            site["campanhas_periodo"][label] = [
                {"campanha": r2["d"][0], "origem": r2["d"][1], "sessoes": int(r2["m"][0]),
                 "conversoes": float(r2["m"][1]), "rejeicao": round(float(r2["m"][2]) * 100, 1),
                 "duracao": round(float(r2["m"][3]), 1)}
                for r2 in rows(cp) if r2["d"][0] not in ("(direct)", "(not set)", "(organic)")]
        # onde o clique pago cai — sem isso nao da pra ver verba indo pra home errada
        lp = report(prop, ["landingPagePlusQueryString", "sessionCampaignName"],
                    ["sessions", "bounceRate"], limit=15, days="28daysAgo", order_metric="sessions")
        site["landing_pagas"] = [{"pagina": r2["d"][0], "campanha": r2["d"][1],
                                  "sessoes": int(r2["m"][0]), "rejeicao": round(float(r2["m"][1]) * 100, 1)}
                                 for r2 in rows(lp) if r2["d"][1] not in ("(direct)", "(not set)", "(organic)")]
        sim = report(prop, ["customEvent:sim_faturamento"], ["eventCount"], limit=10, order_metric="eventCount")
        site["faixas_simuladas"] = [{"faixa": r2["d"][0], "eventos": int(r2["m"][0])}
                                    for r2 in rows(sim) if r2["d"][0] not in ("", "(not set)")]
    out["sites"].append(site)

# ---------------------------------------------------------------- Google Ads
# Investimento vem da API do Ads (numero oficial cobrado), nao do GA: o GA so
# enxerga sessao, e clique pago nem sempre vira sessao (bounce antes do tag).
ADS_CID = "3578927161"


def _env(path="/data/coderush-sites/.env"):
    env = {}
    if not os.path.isfile(path):
        return env
    for line in open(path, encoding="utf-8"):
        line = line.strip()
        if line and not line.startswith("#") and "=" in line:
            k, v = line.split("=", 1)
            env[k.strip()] = v.strip().strip("\"'")
    return env


def _ads_snapshot():
    env = _env()
    need = ["GOOGLE_ADS_DEVELOPER_TOKEN", "GOOGLE_ADS_CLIENT_ID",
            "GOOGLE_ADS_CLIENT_SECRET", "GOOGLE_ADS_REFRESH_TOKEN"]
    if not all(env.get(k) for k in need):
        return {"erro": "credenciais do Google Ads ausentes no .env"}
    from google.ads.googleads.client import GoogleAdsClient
    cli = GoogleAdsClient.load_from_dict({
        "developer_token": env["GOOGLE_ADS_DEVELOPER_TOKEN"],
        "client_id": env["GOOGLE_ADS_CLIENT_ID"],
        "client_secret": env["GOOGLE_ADS_CLIENT_SECRET"],
        "refresh_token": env["GOOGLE_ADS_REFRESH_TOKEN"],
        "use_proto_plus": True})
    svc = cli.get_service("GoogleAdsService")

    hoje = datetime.datetime.now(datetime.timezone(datetime.timedelta(hours=-3))).date()
    faixas = {"hoje": (hoje, hoje),
              "d7": (hoje - datetime.timedelta(days=7), hoje),
              "d28": (hoje - datetime.timedelta(days=28), hoje)}

    # metricas cruas -> as derivadas (CTR, CPC, custo/conv, taxa) sao recalculadas
    # a partir dos totais; media de medias mentiria ao somar campanhas.
    MET = ("metrics.clicks, metrics.impressions, metrics.cost_micros, metrics.conversions, "
           "metrics.conversions_value, metrics.search_impression_share, "
           "metrics.search_budget_lost_impression_share, metrics.search_rank_lost_impression_share")

    def derivar(cli_, imp, custo, conv, valor, share=None, perd_orc=None, perd_rank=None):
        return {
            "cliques": cli_, "impressoes": imp, "custo": round(custo, 2),
            "conversoes": round(conv, 2), "valor_conversoes": round(valor, 2),
            "ctr": round(cli_ / imp * 100, 2) if imp else 0.0,
            "cpc_medio": round(custo / cli_, 2) if cli_ else 0.0,
            "custo_por_conversao": round(custo / conv, 2) if conv else 0.0,
            "taxa_conversao": round(conv / cli_ * 100, 2) if cli_ else 0.0,
            "roas": round(valor / custo, 2) if custo else 0.0,
            "parcela_impressoes": round(share * 100, 1) if share else None,
            "perdida_orcamento": round(perd_orc * 100, 1) if perd_orc else None,
            "perdida_ranking": round(perd_rank * 100, 1) if perd_rank else None,
        }

    ads = {"conta": ADS_CID, "periodos": {}, "campanhas": {}, "diario": []}

    for label, (ini, fim) in faixas.items():
        entre = f"BETWEEN '{ini.isoformat()}' AND '{fim.isoformat()}'"
        tot = dict(c=0, i=0, cu=0.0, cv=0.0, vl=0.0)
        camps = []
        for r in svc.search(customer_id=ADS_CID, query=f"""
                SELECT campaign.id, campaign.name, campaign.status, campaign_budget.amount_micros,
                       {MET} FROM campaign WHERE segments.date {entre}"""):
            m, cp = r.metrics, r.campaign
            custo = m.cost_micros / 1e6
            tot["c"] += m.clicks; tot["i"] += m.impressions; tot["cu"] += custo
            tot["cv"] += m.conversions; tot["vl"] += m.conversions_value
            if m.impressions == 0 and m.clicks == 0 and custo == 0:
                continue  # campanha sem atividade no periodo nao polui a tabela
            camps.append(dict(
                id=str(cp.id), nome=cp.name, status=cp.status.name,
                orcamento=round(r.campaign_budget.amount_micros / 1e6, 2),
                **derivar(m.clicks, m.impressions, custo, m.conversions, m.conversions_value,
                          m.search_impression_share, m.search_budget_lost_impression_share,
                          m.search_rank_lost_impression_share)))
        ads["periodos"][label] = derivar(tot["c"], tot["i"], tot["cu"], tot["cv"], tot["vl"])
        ads["campanhas"][label] = sorted(camps, key=lambda x: x["custo"], reverse=True)

    # serie diaria de custo (90d) para o grafico do painel
    ini90 = (hoje - datetime.timedelta(days=90)).isoformat()
    por_dia = {}
    for r in svc.search(customer_id=ADS_CID, query=f"""
            SELECT segments.date, metrics.cost_micros, metrics.clicks, metrics.impressions,
                   metrics.conversions FROM campaign
            WHERE segments.date BETWEEN '{ini90}' AND '{hoje.isoformat()}'"""):
        d = r.segments.date.replace("-", "")
        acc = por_dia.setdefault(d, dict(custo=0.0, cliques=0, impressoes=0, conversoes=0.0))
        acc["custo"] += r.metrics.cost_micros / 1e6
        acc["cliques"] += r.metrics.clicks
        acc["impressoes"] += r.metrics.impressions
        acc["conversoes"] += r.metrics.conversions
    ads["diario"] = [dict(data=k, custo=round(v["custo"], 2), cliques=v["cliques"],
                          impressoes=v["impressoes"], conversoes=round(v["conversoes"], 2))
                     for k, v in sorted(por_dia.items())]

    # acoes de conversao ativas — o painel avisa se generate_lead nao estiver contando
    ads["conversoes_config"] = []
    for r in svc.search(customer_id=ADS_CID, query="""
            SELECT conversion_action.name, conversion_action.status, conversion_action.category,
                   conversion_action.primary_for_goal FROM conversion_action
            WHERE conversion_action.status != 'REMOVED'"""):
        a = r.conversion_action
        ads["conversoes_config"].append({"nome": a.name, "status": a.status.name,
                                         "categoria": a.category.name, "principal": a.primary_for_goal})
    return ads


# Falha de auth e transitoria (refresh token expira quando o app OAuth esta em
# modo Teste: 7 dias). Sobrescrever o bloco bom com {"erro": ...} apagava semanas
# de historico de investimento no painel — preserva o ultimo snapshot valido e so
# anexa o aviso.
try:
    out["ads"] = _ads_snapshot()
except Exception as e:
    anterior = {}
    if os.path.isfile(OUT):
        try:
            anterior = (json.load(open(OUT, encoding="utf-8")) or {}).get("ads") or {}
        except Exception:
            anterior = {}
    anterior.pop("erro", None)
    if anterior.get("periodos"):
        anterior["aviso"] = "dados de " + str(anterior.get("atualizado_em", "snapshot anterior")) \
                            + " — nao foi possivel atualizar: " + str(e)[:160]
        out["ads"] = anterior
    else:
        out["ads"] = {"erro": str(e)[:200]}
if out["ads"].get("periodos"):
    out["ads"].setdefault("atualizado_em", out["gerado_em"])

tmp = OUT + ".tmp"
with open(tmp, "w", encoding="utf-8") as f:
    json.dump(out, f, ensure_ascii=False, indent=1)
os.replace(tmp, OUT)
os.chmod(OUT, 0o644)
print("gravado", OUT)
