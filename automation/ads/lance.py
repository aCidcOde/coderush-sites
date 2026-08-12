#!/usr/bin/env python3
"""
[Modulo Ads SVD — teto de CPC por grupo]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-12

Quando a campanha perde impressao por RANKING (nao por orcamento), so existem tres
alavancas: extensoes (feito), Indice de Qualidade (precisa de historico, nao da pra
forcar) e lance. Este script mexe na terceira.

Cuidado: subir o teto NAO significa que vai pagar o teto — no CPC manual voce paga
o minimo pra superar o anunciante de baixo. Subir o teto so amplia em quantos
leiloes voce consegue competir. O risco real e o orcamento diario acabar mais cedo;
como hoje sobra verba (0% de perda por orcamento), o risco e baixo.

Uso:
  python3 lance.py --ver
  python3 lance.py --grupo="Sistema MMN" --valor=10 --dry-run
  python3 lance.py --grupo="Sistema MMN" --valor=10
  python3 lance.py --todos --valor=10
"""
import sys

from google.protobuf import field_mask_pb2

CUSTOMER_ID = "3578927161"
ENV_PATH = "/data/coderush-sites/.env"
TETO_SEGURANCA = 15.0  # acima disso e quase certo desperdicio nesse nicho


def cliente():
    env = {}
    for line in open(ENV_PATH, encoding="utf-8"):
        line = line.strip()
        if line and not line.startswith("#") and "=" in line:
            k, v = line.split("=", 1)
            env[k.strip()] = v.strip().strip("\"'")
    from google.ads.googleads.client import GoogleAdsClient
    return GoogleAdsClient.load_from_dict({
        "developer_token": env["GOOGLE_ADS_DEVELOPER_TOKEN"],
        "client_id": env["GOOGLE_ADS_CLIENT_ID"],
        "client_secret": env["GOOGLE_ADS_CLIENT_SECRET"],
        "refresh_token": env["GOOGLE_ADS_REFRESH_TOKEN"],
        "use_proto_plus": True})


def arg(nome, padrao=None):
    for a in sys.argv[1:]:
        if a.startswith(f"--{nome}="):
            return a.split("=", 1)[1]
    return padrao


def main():
    cli = cliente()
    ga = cli.get_service("GoogleAdsService")

    grupos = list(ga.search(customer_id=CUSTOMER_ID, query="""
        SELECT campaign.name, ad_group.resource_name, ad_group.name,
               ad_group.cpc_bid_micros, metrics.impressions, metrics.clicks,
               metrics.search_impression_share, metrics.search_rank_lost_impression_share
        FROM ad_group WHERE campaign.status = 'ENABLED' AND ad_group.status = 'ENABLED'
          AND segments.date DURING LAST_7_DAYS"""))

    if "--ver" in sys.argv or not (arg("valor")):
        print(f"{'GRUPO':<26} {'LANCE':<10} {'IMPR':<6} {'CLIQ':<5} {'PARC.IMPR':<10} PERDA/RANKING")
        for r in grupos:
            g, m = r.ad_group, r.metrics
            pi = f"{m.search_impression_share*100:.0f}%" if m.search_impression_share else "-"
            pr = f"{m.search_rank_lost_impression_share*100:.0f}%" if m.search_rank_lost_impression_share else "-"
            print(f"{g.name:<26} R$ {g.cpc_bid_micros/1e6:<7.2f} {m.impressions:<6} "
                  f"{m.clicks:<5} {pi:<10} {pr}")
        if not grupos:
            print("  (sem dados nos ultimos 7 dias)")
        return

    valor = float(str(arg("valor")).replace(",", "."))
    if valor > TETO_SEGURANCA:
        sys.exit(f"R$ {valor:.2f} passa do teto de seguranca (R$ {TETO_SEGURANCA:.2f}). "
                 f"Se e proposital, ajuste TETO_SEGURANCA no script.")
    alvo = arg("grupo")
    todos = "--todos" in sys.argv
    dry = "--dry-run" in sys.argv
    if not alvo and not todos:
        sys.exit('informe --grupo="..." ou --todos')

    vistos, ops = set(), []
    for r in grupos:
        g = r.ad_group
        if g.resource_name in vistos:
            continue
        if not todos and g.name != alvo:
            continue
        vistos.add(g.resource_name)
        atual = g.cpc_bid_micros / 1e6
        if abs(atual - valor) < 0.005:
            print(f"  [=] {g.name}: ja esta em R$ {valor:.2f}")
            continue
        print(f"  [~] {g.name}: R$ {atual:.2f} -> R$ {valor:.2f}")
        if dry:
            continue
        o = cli.get_type("AdGroupOperation")
        o.update.resource_name = g.resource_name
        o.update.cpc_bid_micros = int(round(valor * 1_000_000))
        cli.copy_from(o.update_mask, field_mask_pb2.FieldMask(paths=["cpc_bid_micros"]))
        ops.append(o)

    if not vistos:
        sys.exit(f"grupo '{alvo}' nao encontrado")
    if dry:
        print("\n(dry-run — nada foi alterado)")
        return
    if ops:
        cli.get_service("AdGroupService").mutate_ad_groups(
            customer_id=CUSTOMER_ID, operations=ops)
        print(f"\n  {len(ops)} grupo(s) atualizado(s)")
    else:
        print("\nnada a fazer")


if __name__ == "__main__":
    main()
