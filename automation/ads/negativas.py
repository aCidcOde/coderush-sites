#!/usr/bin/env python3
"""
[Modulo Ads SVD — palavras-chave negativas]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-12

Limpeza semanal dos termos de busca: o relatorio mostra o que a pessoa realmente
digitou, e boa parte nunca vira cliente (pesquisa academica, curioso, concorrente
procurando emprego). Cada termo desses consome impressao e, se clicar, dinheiro.

FRASE x EXATA: negativa de frase bloqueia tudo que contenha aquele trecho — util
pra lixo obvio ("curso", "vaga"). Negativa exata bloqueia so aquela busca literal,
que e o que se usa quando o termo generico gasta dinheiro mas a versao qualificada
dele precisa continuar rodando: negativar [marketing multinivel] barra o curioso
sem barrar "sistema de marketing multinivel".

ARMADILHA: negativa NAO casa acento nem variacao. "gratis" nao bloqueia "grátis" —
tem que cadastrar as duas.

Uso:
  python3 negativas.py --termos                       # lista os termos captados
  python3 negativas.py --add="curso,vaga" --dry-run   # frase
  python3 negativas.py --exatas="mmn,marketing multinivel"
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
    exatas = arg("exatas")
    if not bruto and not exatas:
        sys.exit('use --termos, --add="..." (frase) ou --exatas="..." (exata)')
    tipo = "EXACT" if exatas else "PHRASE"
    termos = [t.strip().lower() for t in (exatas or bruto).split(",") if t.strip()]
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
            SELECT campaign.resource_name, campaign_criterion.keyword.text,
                   campaign_criterion.keyword.match_type
            FROM campaign_criterion WHERE campaign.resource_name = '{camp}'
              AND campaign_criterion.negative = TRUE
              AND campaign_criterion.type = 'KEYWORD'
              AND campaign_criterion.status != 'REMOVED'"""):
        k = r.campaign_criterion.keyword
        ja.add((k.text.lower(), k.match_type.name))

    ops = []
    rotulo = "exata" if tipo == "EXACT" else "frase"
    for t in termos:
        if (t, tipo) in ja:
            print(f"  [=] '{t}' ja e negativa {rotulo}")
            continue
        print(f"  [+] '{t}' ({rotulo})")
        if dry:
            continue
        o = cli.get_type("CampaignCriterionOperation")
        o.create.campaign = camp
        o.create.negative = True
        o.create.keyword.text = t
        o.create.keyword.match_type = getattr(cli.enums.KeywordMatchTypeEnum, tipo)
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
