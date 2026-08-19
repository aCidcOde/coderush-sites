#!/usr/bin/env python3
"""
[Modulo Ads SVD — orcamento diario da campanha]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-19

POR QUE EXISTE: orcamento nao e so teto de gasto, e teto de ERRO. Com CPC de
~R$ 6, R$ 50/dia significa que uma palavra mal calibrada queima 8 cliques antes
de alguem olhar o relatorio — foi o que aconteceu com "sistema de marketing de
rede", que levou 25% da verba trazendo busca navegacional de concorrente.
Cortar pela metade nao reduz so o gasto: reduz quanto um erro consegue escalar
antes da leitura do dia seguinte.

COMO O GOOGLE TRATA O NUMERO: nao e limite rigido por dia. Ele pode gastar ate
2x num dia especifico e compensa nos fracos; o que garante e o teto do ciclo —
diario x 30,4. R$ 25/dia = no maximo R$ 760 no mes.

Uso:
  python3 orcamento.py --ver
  python3 orcamento.py --campanha="SVD - Promocao 10 Anos" --valor=25 --dry-run
  python3 orcamento.py --campanha="SVD - Promocao 10 Anos" --valor=25
"""
import sys

from google.protobuf import field_mask_pb2

CUSTOMER_ID = "3578927161"
ENV_PATH = "/data/coderush-sites/.env"
TETO_SEGURANCA = 200.0  # acima disso e quase certo dedo gordo


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

    linhas = list(ga.search(customer_id=CUSTOMER_ID, query="""
        SELECT campaign.name, campaign.status, campaign_budget.resource_name,
               campaign_budget.amount_micros, campaign_budget.name
        FROM campaign WHERE campaign.status != 'REMOVED' ORDER BY campaign.name"""))

    valor = arg("valor")
    if "--ver" in sys.argv or not valor:
        print(f"{'CAMPANHA':<28} {'STATUS':<9} {'DIARIO':>10}   TETO DO CICLO")
        for r in linhas:
            d = r.campaign_budget.amount_micros / 1e6
            print(f"{r.campaign.name[:27]:<28} {r.campaign.status.name.lower():<9} "
                  f"R$ {d:>7.2f}   R$ {d * 30.4:,.2f}".replace(",", "."))
        return

    valor = float(str(valor).replace(",", "."))
    if not 0 < valor <= TETO_SEGURANCA:
        sys.exit(f"--valor deve ficar entre 0 e {TETO_SEGURANCA:.0f}")
    nome = arg("campanha")
    if not nome:
        sys.exit('informe --campanha="..."')
    dry = "--dry-run" in sys.argv

    alvo = [r for r in linhas if r.campaign.name == nome]
    if not alvo:
        sys.exit(f"campanha '{nome}' nao encontrada")

    ops = []
    for r in alvo:
        atual = r.campaign_budget.amount_micros / 1e6
        if abs(atual - valor) < 0.005:
            print(f"  [=] '{nome}' ja esta em R$ {valor:.2f}/dia")
            return
        print(f"  [~] '{nome}': R$ {atual:.2f}/dia -> R$ {valor:.2f}/dia")
        print(f"      teto do ciclo: R$ {atual*30.4:.2f} -> R$ {valor*30.4:.2f}")
        if dry:
            continue
        o = cli.get_type("CampaignBudgetOperation")
        o.update.resource_name = r.campaign_budget.resource_name
        o.update.amount_micros = int(round(valor * 1_000_000))
        cli.copy_from(o.update_mask, field_mask_pb2.FieldMask(paths=["amount_micros"]))
        ops.append(o)

    if dry:
        print("\n(dry-run — nada foi alterado)")
        return
    cli.get_service("CampaignBudgetService").mutate_campaign_budgets(
        customer_id=CUSTOMER_ID, operations=ops)
    print("\n  orcamento atualizado")


if __name__ == "__main__":
    main()
