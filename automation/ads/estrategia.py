#!/usr/bin/env python3
"""
[Modulo Ads — estrategia de lance da campanha (parcela de impressao)]
@Author: Andre Gomes ( @acidcode )
@since 2026-09-12

POR QUE ISSO EXISTE: durante semanas a leitura foi "a gente quase nao aparece, o
concorrente aparece toda hora", e a hipotese natural era lance baixo. Era falso.
A campanha usa TARGET_IMPRESSION_SHARE com meta de 20% no topo e teto de R$ 6 —
e entregava 31% de topo. Ela ja estava ACIMA da meta que a gente deu pra ela.
O Google segurava por instrucao, nao por falta de dinheiro nem por leilao perdido.

A PEGADINHA QUE CUSTOU O DIAGNOSTICO: sob TARGET_IMPRESSION_SHARE os lances por
palavra viram enfeite. O relatorio continua mostrando "lance R$ 12" em
ad_group_criterion.effective_cpc_bid_micros, mas quem decide o leilao e o teto da
estrategia (R$ 6). Foi por isso que "sistema mmn" aparecia com lance R$ 12 e CPC
real R$ 1,87 — nao era folga de qualidade, era outro numero mandando. Sempre leia
campaign.target_impression_share ANTES de concluir qualquer coisa sobre lance.

TETO x META, papeis diferentes:
  - meta (location_fraction) diz o quanto tentar aparecer;
  - teto (cpc_bid_ceiling) diz quanto pode pagar pra conseguir.
Subir a meta sem subir o teto nao faz efeito: o Google quer entregar e nao pode
pagar. Subir o teto sem subir a meta tambem nao: ele pode pagar e nao quer.
Os dois andam juntos.

Uso:
  python3 estrategia.py --ver
  python3 estrategia.py --campanha="SVD - Promocao 10 Anos" --meta=70 --teto=12 --dry-run
  python3 estrategia.py --campanha="SVD - Promocao 10 Anos" --meta=70 --teto=12
"""
import sys

from google.protobuf import field_mask_pb2

sys.path.insert(0, "/data/coderush-sites/automation/ads")
from _conta import cliente, resolver  # noqa: E402

# CPC medio observado ficou entre R$ 1,87 e R$ 5,98; teto acima disso e dedo gordo
TETO_SEGURANCA = 20.0
ALVOS = {"topo": "TOP_OF_PAGE", "primeiro": "ABSOLUTE_TOP_OF_PAGE",
         "qualquer": "ANYWHERE_ON_PAGE"}


def arg(nome, padrao=None):
    for a in sys.argv[1:]:
        if a.startswith(f"--{nome}="):
            return a.split("=", 1)[1]
    return padrao


def ver(ga, cid):
    q = """SELECT campaign.name, campaign.status, campaign.bidding_strategy_type,
           campaign.target_impression_share.location,
           campaign.target_impression_share.location_fraction_micros,
           campaign.target_impression_share.cpc_bid_ceiling_micros
           FROM campaign WHERE campaign.status != 'REMOVED' ORDER BY campaign.name"""
    print(f"  {'campanha':30} {'estrategia':24} {'meta':>6} {'teto':>9}")
    for r in ga.search(customer_id=cid, query=q):
        c = r.campaign
        t = c.target_impression_share
        meta = f"{t.location_fraction_micros/1e4:.0f}%" if t.location_fraction_micros else "-"
        teto = f"R$ {t.cpc_bid_ceiling_micros/1e6:.2f}" if t.cpc_bid_ceiling_micros else "-"
        alvo = f"/{t.location.name[:12]}" if t.location_fraction_micros else ""
        print(f"  [{c.status.name[:7]:7}] {c.name:28} {c.bidding_strategy_type.name+alvo:30} "
              f"{meta:>6} {teto:>9}")


def main():
    cid = resolver(sys.argv)
    cli = cliente()
    ga = cli.get_service("GoogleAdsService")

    nome = arg("campanha")
    if not nome or "--ver" in sys.argv:
        ver(ga, cid)
        return

    meta = arg("meta")
    teto = arg("teto")
    alvo = ALVOS.get(arg("alvo", "topo"), "TOP_OF_PAGE")
    if not meta or not teto:
        sys.exit("informe --meta=<1-100> e --teto=<reais>")
    meta, teto = float(meta), float(teto)
    if not 1 <= meta <= 100:
        sys.exit(f"meta {meta}% fora de 1-100")
    if teto > TETO_SEGURANCA:
        sys.exit(f"teto R$ {teto:.2f} acima do limite de seguranca "
                 f"(R$ {TETO_SEGURANCA:.2f}) — se e proposital, edite o script")

    q = f"""SELECT campaign.resource_name, campaign.name, campaign.bidding_strategy_type,
            campaign.target_impression_share.location_fraction_micros,
            campaign.target_impression_share.cpc_bid_ceiling_micros
            FROM campaign WHERE campaign.name = '{nome}' AND campaign.status != 'REMOVED'"""
    achados = list(ga.search(customer_id=cid, query=q))
    if not achados:
        sys.exit(f"campanha nao encontrada: {nome!r}")
    c = achados[0].campaign
    antes = c.target_impression_share
    print(f"  {c.name}")
    print(f"    de:   meta {antes.location_fraction_micros/1e4:.0f}% "
          f"teto R$ {antes.cpc_bid_ceiling_micros/1e6:.2f} ({c.bidding_strategy_type.name})")
    print(f"    para: meta {meta:.0f}% teto R$ {teto:.2f} (TARGET_IMPRESSION_SHARE/{alvo})")

    if "--dry-run" in sys.argv:
        print("\n  (dry-run — nada foi enviado)")
        return

    op = cli.get_type("CampaignOperation")
    up = op.update
    up.resource_name = c.resource_name
    up.bidding_strategy_type = cli.enums.BiddingStrategyTypeEnum.TARGET_IMPRESSION_SHARE
    up.target_impression_share.location = getattr(
        cli.enums.TargetImpressionShareLocationEnum, alvo)
    up.target_impression_share.location_fraction_micros = int(meta * 1e4)
    up.target_impression_share.cpc_bid_ceiling_micros = int(teto * 1e6)
    # FieldMask explicita: o helper automatico descarta valores default
    op.update_mask.CopyFrom(field_mask_pb2.FieldMask(paths=[
        "bidding_strategy_type",
        "target_impression_share.location",
        "target_impression_share.location_fraction_micros",
        "target_impression_share.cpc_bid_ceiling_micros"]))
    cli.get_service("CampaignService").mutate_campaigns(
        customer_id=cid, operations=[op])
    print("\n  OK — leva algumas horas pra refletir no leilao")


if __name__ == "__main__":
    main()
