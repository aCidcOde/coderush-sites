#!/usr/bin/env python3
"""
[Modulo Ads SVD — manutencao das palavras-chave positivas]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-14

Troca de correspondencia (frase -> exata e vice-versa).

ATENCAO: a API nao permite editar match_type de um criterio existente — o jeito e
remover e recriar. Isso zera o historico daquela palavra (Indice de Qualidade
volta a se formar do zero). Vale a pena quando a palavra esta captando busca
errada: historico ruim nao e patrimonio.

POR QUE ISSO EXISTE: "sistema marketing multinivel" em FRASE trouxe "marketing
multinivel", depois "empresas de marketing multinivel" — cada dia uma variante
informativa nova, a R$ 6 o clique. Negativa exata vira enxuga-gelo; fechar a
correspondencia resolve na origem.

Uso:
  python3 palavras.py --ver
  python3 palavras.py --trocar="sistema marketing multinivel" --tipo=EXACT --dry-run
  python3 palavras.py --trocar="sistema marketing multinivel" --tipo=EXACT
"""
import sys

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


def main():
    cli = cliente()
    ga = cli.get_service("GoogleAdsService")

    palavras = list(ga.search(customer_id=CUSTOMER_ID, query="""
        SELECT ad_group.name, ad_group.resource_name, ad_group_criterion.resource_name,
               ad_group_criterion.keyword.text, ad_group_criterion.keyword.match_type,
               ad_group_criterion.cpc_bid_micros, metrics.impressions, metrics.clicks,
               metrics.cost_micros
        FROM keyword_view WHERE campaign.status = 'ENABLED'
          AND ad_group_criterion.status = 'ENABLED'
          AND segments.date DURING LAST_14_DAYS"""))

    alvo = arg("trocar")
    if "--ver" in sys.argv or not alvo:
        print(f"{'PALAVRA':<38} {'TIPO':<7} {'IMPR':<5} {'CLIQ':<5} CUSTO")
        for r in sorted(palavras, key=lambda x: -x.metrics.cost_micros):
            k, m = r.ad_group_criterion.keyword, r.metrics
            print(f"{k.text[:37]:<38} {k.match_type.name:<7} {m.impressions:<5} "
                  f"{m.clicks:<5} R$ {m.cost_micros/1e6:.2f}")
        return

    tipo = (arg("tipo") or "EXACT").upper()
    if tipo not in ("EXACT", "PHRASE", "BROAD"):
        sys.exit("--tipo deve ser EXACT, PHRASE ou BROAD")
    dry = "--dry-run" in sys.argv

    achados = [r for r in palavras
               if r.ad_group_criterion.keyword.text.lower() == alvo.lower()]
    if not achados:
        sys.exit(f"palavra '{alvo}' nao encontrada entre as ativas")

    remover, criar = [], []
    for r in achados:
        k = r.ad_group_criterion.keyword
        if k.match_type.name == tipo:
            print(f"  [=] '{k.text}' em {r.ad_group.name} ja e {tipo}")
            continue
        print(f"  [~] '{k.text}' em {r.ad_group.name}: {k.match_type.name} -> {tipo} "
              f"(historico de {r.metrics.impressions} impr / {r.metrics.clicks} cliq sera zerado)")
        if dry:
            continue
        o = cli.get_type("AdGroupCriterionOperation")
        o.remove = r.ad_group_criterion.resource_name
        remover.append(o)

        n = cli.get_type("AdGroupCriterionOperation")
        cr = n.create
        cr.ad_group = r.ad_group.resource_name
        cr.status = cli.enums.AdGroupCriterionStatusEnum.ENABLED
        cr.keyword.text = k.text
        cr.keyword.match_type = getattr(cli.enums.KeywordMatchTypeEnum, tipo)
        if r.ad_group_criterion.cpc_bid_micros:
            cr.cpc_bid_micros = r.ad_group_criterion.cpc_bid_micros
        criar.append(n)

    if dry:
        print("\n(dry-run — nada foi alterado)")
        return
    if not remover:
        print("nada a fazer")
        return

    svc = cli.get_service("AdGroupCriterionService")
    # remove antes de criar: a mesma palavra nao pode existir duas vezes no grupo
    svc.mutate_ad_group_criteria(customer_id=CUSTOMER_ID, operations=remover)
    svc.mutate_ad_group_criteria(customer_id=CUSTOMER_ID, operations=criar)
    print(f"\n  {len(criar)} palavra(s) recriada(s) como {tipo}")


if __name__ == "__main__":
    main()
