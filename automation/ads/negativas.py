#!/usr/bin/env python3
"""
[Modulo Ads SVD — palavras-chave negativas]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-12

Limpeza semanal dos termos de busca: o relatorio mostra o que a pessoa realmente
digitou, e boa parte nunca vira cliente (pesquisa academica, curioso, concorrente
procurando emprego). Cada termo desses consome impressao e, se clicar, dinheiro.

Uso:
  python3 negativas.py --termos                       # lista os termos captados
  python3 negativas.py --add="empresas multinivel,modelo de negocio" --dry-run
  python3 negativas.py --add="empresas multinivel,modelo de negocio"
"""
import sys

CUSTOMER_ID = "3578927161"
ENV_PATH = "/data/coderush-sites/.env"
CAMPANHA_PADRAO = "SVD - Promocao 10 Anos"


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
    nome = arg("campanha", CAMPANHA_PADRAO)

    if "--termos" in sys.argv:
        dias = arg("dias", "7")
        print(f"Termos de busca dos ultimos {dias} dias:\n")
        n = 0
        for r in ga.search(customer_id=CUSTOMER_ID, query=f"""
                SELECT search_term_view.search_term, metrics.impressions, metrics.clicks,
                       metrics.cost_micros, metrics.conversions
                FROM search_term_view WHERE segments.date DURING LAST_{dias}_DAYS
                ORDER BY metrics.impressions DESC LIMIT 100"""):
            m = r.metrics
            n += 1
            print(f'  "{r.search_term_view.search_term}" — {m.impressions} impr, '
                  f"{m.clicks} cliques, R$ {m.cost_micros/1e6:.2f}, {m.conversions:.0f} conv")
        if not n:
            print("  (nenhum termo com volume suficiente ainda)")
        return

    bruto = arg("add")
    if not bruto:
        sys.exit('use --termos ou --add="termo1,termo2"')
    termos = [t.strip().lower() for t in bruto.split(",") if t.strip()]
    dry = "--dry-run" in sys.argv

    camp = None
    for r in ga.search(customer_id=CUSTOMER_ID, query=f"""
            SELECT campaign.resource_name FROM campaign
            WHERE campaign.name = '{nome}' AND campaign.status != 'REMOVED'"""):
        camp = r.campaign.resource_name
    if not camp:
        sys.exit(f"campanha '{nome}' nao encontrada")

    ja = set()
    for r in ga.search(customer_id=CUSTOMER_ID, query=f"""
            SELECT campaign.resource_name, campaign_criterion.keyword.text
            FROM campaign_criterion WHERE campaign.resource_name = '{camp}'
              AND campaign_criterion.negative = TRUE
              AND campaign_criterion.type = 'KEYWORD'
              AND campaign_criterion.status != 'REMOVED'"""):
        ja.add(r.campaign_criterion.keyword.text.lower())

    ops = []
    for t in termos:
        if t in ja:
            print(f"  [=] '{t}' ja e negativa")
            continue
        print(f"  [+] '{t}' (correspondencia de frase)")
        if dry:
            continue
        o = cli.get_type("CampaignCriterionOperation")
        o.create.campaign = camp
        o.create.negative = True
        o.create.keyword.text = t
        o.create.keyword.match_type = cli.enums.KeywordMatchTypeEnum.PHRASE
        ops.append(o)

    if dry:
        print("\n(dry-run — nada foi alterado)")
        return
    if ops:
        cli.get_service("CampaignCriterionService").mutate_campaign_criteria(
            customer_id=CUSTOMER_ID, operations=ops)
        print(f"\n  {len(ops)} negativa(s) adicionada(s) a '{nome}' "
              f"(total agora: {len(ja) + len(ops)})")
    else:
        print("\nnada a fazer")


if __name__ == "__main__":
    main()
