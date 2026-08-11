#!/usr/bin/env python3
"""
[Modulo Ads SVD — liga/desliga campanha inteira]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-11

Ativar pela interface exige clicar em tres niveis (campanha, grupo, anuncio) e e
facil deixar um grupo pausado sem perceber — a campanha fica "ativa" gastando zero.
Este script liga os tres de uma vez e confirma o estado final.

A data de termino e um freio de mao: a Promocao 10 Anos acaba em 31/08 e a campanha
nao pode continuar veiculando uma oferta que expirou.

Uso:
  python3 ligar-campanha.py --campanha="SVD - Promocao 10 Anos" --fim=2026-08-31
  python3 ligar-campanha.py --campanha="SVD - Promocao 10 Anos" --pausar
  python3 ligar-campanha.py --status                # so mostra o estado de tudo
"""
import sys

from google.protobuf import field_mask_pb2

CUSTOMER_ID = "3578927161"
ENV_PATH = "/data/coderush-sites/.env"


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


def status(cli, ga):
    # campaign_budget nao pode ser selecionado a partir de ad_group_ad: busca a parte
    orc = {}
    for r in ga.search(customer_id=CUSTOMER_ID, query="""
            SELECT campaign.name, campaign_budget.amount_micros, campaign.end_date_time
            FROM campaign WHERE campaign.status != 'REMOVED'"""):
        orc[r.campaign.name] = (r.campaign_budget.amount_micros / 1e6,
                                (r.campaign.end_date_time or '-')[:10])

    print(f"{'CAMPANHA':<26} {'GRUPO':<26} {'CAMP':<8} {'GRUPO':<8} {'ANUNCIO':<8} ORCAMENTO")
    for r in ga.search(customer_id=CUSTOMER_ID, query="""
            SELECT campaign.name, campaign.status, ad_group.name, ad_group.status,
                   ad_group_ad.status
            FROM ad_group_ad WHERE campaign.status != 'REMOVED'
              AND ad_group_ad.status != 'REMOVED'
            ORDER BY campaign.name, ad_group.name"""):
        verba, fim = orc.get(r.campaign.name, (0, '-'))
        print(f"{r.campaign.name[:25]:<26} {r.ad_group.name[:25]:<26} "
              f"{r.campaign.status.name.lower():<8} {r.ad_group.status.name.lower():<8} "
              f"{r.ad_group_ad.status.name.lower():<8} R$ {verba:.0f}/dia ate {fim}")


def main():
    cli = cliente()
    ga = cli.get_service("GoogleAdsService")

    if "--status" in sys.argv:
        status(cli, ga)
        return

    nome = arg("campanha")
    if not nome:
        sys.exit("informe --campanha=\"...\"")
    pausar = "--pausar" in sys.argv
    fim = arg("fim")

    alvo = cli.enums.CampaignStatusEnum.PAUSED if pausar else cli.enums.CampaignStatusEnum.ENABLED
    alvo_g = cli.enums.AdGroupStatusEnum.PAUSED if pausar else cli.enums.AdGroupStatusEnum.ENABLED
    alvo_a = cli.enums.AdGroupAdStatusEnum.PAUSED if pausar else cli.enums.AdGroupAdStatusEnum.ENABLED
    verbo = "PAUSANDO" if pausar else "LIGANDO"

    linhas = list(ga.search(customer_id=CUSTOMER_ID, query=f"""
        SELECT campaign.resource_name, campaign.name, ad_group.resource_name,
               ad_group.name, ad_group_ad.resource_name
        FROM ad_group_ad WHERE campaign.name = '{nome}'
          AND campaign.status != 'REMOVED' AND ad_group_ad.status != 'REMOVED'"""))
    if not linhas:
        sys.exit(f"campanha '{nome}' nao encontrada (ou sem anuncios)")

    print(f"{verbo} '{nome}' — {len(linhas)} anuncio(s)")

    # campanha (+ data de termino)
    op = cli.get_type("CampaignOperation")
    op.update.resource_name = linhas[0].campaign.resource_name
    op.update.status = alvo
    paths = ["status"]
    if fim:
        # v25 renomeou end_date -> end_date_time e exige o horario junto
        op.update.end_date_time = f"{fim} 23:59:59"
        paths.append("end_date_time")
        print(f"  data de termino: {fim} 23:59:59")
    cli.copy_from(op.update_mask, field_mask_pb2.FieldMask(paths=paths))
    cli.get_service("CampaignService").mutate_campaigns(
        customer_id=CUSTOMER_ID, operations=[op])

    # grupos (dedup: varios anuncios podem cair no mesmo grupo)
    gops = []
    for res in {l.ad_group.resource_name for l in linhas}:
        o = cli.get_type("AdGroupOperation")
        o.update.resource_name = res
        o.update.status = alvo_g
        cli.copy_from(o.update_mask, field_mask_pb2.FieldMask(paths=["status"]))
        gops.append(o)
    cli.get_service("AdGroupService").mutate_ad_groups(customer_id=CUSTOMER_ID, operations=gops)

    # anuncios
    aops = []
    for l in linhas:
        o = cli.get_type("AdGroupAdOperation")
        o.update.resource_name = l.ad_group_ad.resource_name
        o.update.status = alvo_a
        cli.copy_from(o.update_mask, field_mask_pb2.FieldMask(paths=["status"]))
        aops.append(o)
    cli.get_service("AdGroupAdService").mutate_ad_group_ads(customer_id=CUSTOMER_ID, operations=aops)

    print(f"  campanha + {len(gops)} grupo(s) + {len(aops)} anuncio(s) -> "
          f"{'pausados' if pausar else 'ativos'}\n")
    status(cli, ga)


if __name__ == "__main__":
    main()
