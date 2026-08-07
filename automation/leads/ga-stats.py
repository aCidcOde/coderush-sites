#!/usr/bin/env python3
"""
[Modulo Leads SVD — snapshot de estatisticas do GA4]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-03
Roda no HOST (cron, a cada 3h): consulta a Data API do GA4 com a service account
e grava um JSON que o painel-leads renderiza. Sem credencial no container web.
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
        sim = report(prop, ["customEvent:sim_faturamento"], ["eventCount"], limit=10, order_metric="eventCount")
        site["faixas_simuladas"] = [{"faixa": r2["d"][0], "eventos": int(r2["m"][0])}
                                    for r2 in rows(sim) if r2["d"][0] not in ("", "(not set)")]
    out["sites"].append(site)

tmp = OUT + ".tmp"
with open(tmp, "w", encoding="utf-8") as f:
    json.dump(out, f, ensure_ascii=False, indent=1)
os.replace(tmp, OUT)
os.chmod(OUT, 0o644)
print("gravado", OUT)
