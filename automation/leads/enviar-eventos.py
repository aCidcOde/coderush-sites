#!/usr/bin/env python3
"""
[Modulo Leads SVD — eventos de etapa do funil pro GA4]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-07
Consome storage/ga-events.queue (escrita pelo painel, que nao tem credencial) e
dispara os eventos de estagio (qualify_lead, schedule_demo) via Measurement
Protocol, amarrados ao client_id do lead — assim o Google otimiza por lead BOM,
nao por volume de formulario.
"""
import json, os, urllib.request

BASE = "/data/coderush-sites"
QUEUE = f"{BASE}/sistemavendadireta/storage/ga-events.queue"
DONE = f"{BASE}/sistemavendadireta/storage/ga-events.enviados"
MEASUREMENT_ID = "G-4107EVTE0Q"

def secret():
    for line in open(f"{BASE}/.env"):
        if line.strip().startswith("GA4_MP_SECRET_SVD="):
            return line.split("=", 1)[1].strip()
    raise SystemExit("GA4_MP_SECRET_SVD ausente")

def main():
    if not os.path.exists(QUEUE) or os.path.getsize(QUEUE) == 0:
        return
    linhas = [l for l in open(QUEUE, encoding="utf-8").read().splitlines() if l.strip()]
    open(QUEUE, "w").close()  # esvazia cedo: reprocessar duplicaria evento

    api = secret()
    url = f"https://www.google-analytics.com/mp/collect?measurement_id={MEASUREMENT_ID}&api_secret={api}"
    enviados = 0
    with open(DONE, "a", encoding="utf-8") as log:
        for linha in linhas:
            try:
                ev = json.loads(linha)
            except Exception:
                continue
            params = {"lead_id": ev.get("lead_id"), "origem_lead": ev.get("origem", "")}
            if ev["event"] == "purchase":
                params.update({
                    "transaction_id": f"svd-{ev.get('lead_id')}-{ev.get('ts','')[:10].replace('-','')}",
                    "value": float(ev.get("valor") or 0),
                    "currency": "BRL",
                })
            payload = {
                "client_id": ev.get("client_id") or f"offline.{ev.get('lead_id')}",
                "events": [{"name": ev["event"], "params": params}],
            }
            req = urllib.request.Request(url, data=json.dumps(payload).encode(),
                                         headers={"Content-Type": "application/json"})
            try:
                with urllib.request.urlopen(req, timeout=20) as r:
                    ok = 200 <= r.status < 300
            except Exception as e:
                ok = False
                print("falha:", str(e)[:80])
            log.write(json.dumps({**ev, "ok": ok}, ensure_ascii=False) + "\n")
            enviados += int(ok)
    print(f"eventos de funil enviados: {enviados}/{len(linhas)}")

if __name__ == "__main__":
    main()
