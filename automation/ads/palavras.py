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

Acento nao precisa de entrada separada aqui: palavra POSITIVA casa variantes
proximas, entao "sistema multinivel" ja cobre "sistema multinível". O oposto vale
pra negativa, que exige cada forma (ver negativas.py).

Uso:
  python3 palavras.py --ver
  python3 palavras.py --trocar="sistema marketing multinivel" --tipo=EXACT
  python3 palavras.py --adicionar="sistema multinivel,plataforma mmn" --grupo="Sistema MMN"
  python3 palavras.py --lance-de="sistema mmn" --lance=9
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

    dry = "--dry-run" in sys.argv
    svc = cli.get_service("AdGroupCriterionService")

    # ---------------------------------------------- adicionar palavras novas
    novas = arg("adicionar")
    if novas:
        grupo_nome = arg("grupo")
        if not grupo_nome:
            sys.exit('informe --grupo="..."')
        tipo = (arg("tipo") or "PHRASE").upper()
        lance = arg("lance")
        grupo_res = None
        for r in ga.search(customer_id=CUSTOMER_ID, query=f"""
                SELECT ad_group.resource_name, ad_group.name FROM ad_group
                WHERE campaign.status='ENABLED' AND ad_group.name = '{grupo_nome}'
                  AND ad_group.status='ENABLED'"""):
            grupo_res = r.ad_group.resource_name
        if not grupo_res:
            sys.exit(f"grupo '{grupo_nome}' nao encontrado")

        existentes = {r.ad_group_criterion.keyword.text.lower() for r in palavras}
        ops = []
        for t in [x.strip() for x in novas.split(",") if x.strip()]:
            if t.lower() in existentes:
                print(f"  [=] '{t}' ja existe na campanha")
                continue
            print(f"  [+] '{t}' em {grupo_nome} ({tipo.lower()})"
                  + (f", lance R$ {float(lance):.2f}" if lance else ""))
            if dry:
                continue
            o = cli.get_type("AdGroupCriterionOperation")
            cr = o.create
            cr.ad_group = grupo_res
            cr.status = cli.enums.AdGroupCriterionStatusEnum.ENABLED
            cr.keyword.text = t
            cr.keyword.match_type = getattr(cli.enums.KeywordMatchTypeEnum, tipo)
            if lance:
                cr.cpc_bid_micros = int(round(float(str(lance).replace(",", ".")) * 1_000_000))
            ops.append(o)
        if dry:
            print("\n(dry-run — nada foi alterado)")
        elif ops:
            svc.mutate_ad_group_criteria(customer_id=CUSTOMER_ID, operations=ops)
            print(f"\n  {len(ops)} palavra(s) adicionada(s)")
        else:
            print("\nnada a fazer")
        return

    # ---------------------------------------------- lance de uma palavra so
    lance_de = arg("lance-de")
    if lance_de:
        valor = float(str(arg("lance", "0")).replace(",", "."))
        if not 0 < valor <= 15:
            sys.exit("--lance deve estar entre 0 e 15")
        achados = [r for r in palavras
                   if r.ad_group_criterion.keyword.text.lower() == lance_de.lower()]
        if not achados:
            sys.exit(f"palavra '{lance_de}' nao encontrada")
        ops = []
        for r in achados:
            atual = (r.ad_group_criterion.cpc_bid_micros or 0) / 1e6
            print(f"  [~] '{r.ad_group_criterion.keyword.text}' em {r.ad_group.name}: "
                  f"lance {'R$ %.2f' % atual if atual else 'do grupo'} -> R$ {valor:.2f}")
            if dry:
                continue
            o = cli.get_type("AdGroupCriterionOperation")
            o.update.resource_name = r.ad_group_criterion.resource_name
            o.update.cpc_bid_micros = int(round(valor * 1_000_000))
            cli.copy_from(o.update_mask, field_mask_pb2.FieldMask(paths=["cpc_bid_micros"]))
            ops.append(o)
        if dry:
            print("\n(dry-run — nada foi alterado)")
        elif ops:
            svc.mutate_ad_group_criteria(customer_id=CUSTOMER_ID, operations=ops)
            print(f"\n  lance atualizado em {len(ops)} palavra(s)")
        return

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

    # remove antes de criar: a mesma palavra nao pode existir duas vezes no grupo
    svc.mutate_ad_group_criteria(customer_id=CUSTOMER_ID, operations=remover)
    svc.mutate_ad_group_criteria(customer_id=CUSTOMER_ID, operations=criar)
    print(f"\n  {len(criar)} palavra(s) recriada(s) como {tipo}")


if __name__ == "__main__":
    main()
