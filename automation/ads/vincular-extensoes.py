#!/usr/bin/env python3
"""
[Modulo Ads — replica os assets de uma campanha nas outras]
@Author: Andre Gomes ( @acidcode )
@since 2026-09-12

POR QUE ISSO EXISTE: em 12/09/2026, ao ligar as 4 campanhas segmentadas (Afiliados,
Parceiros, Cosmeticos, Suplementos), elas tinham ZERO extensoes enquanto a
Promocao 10 Anos tinha 12. Extensao entra direto no Ad Rank e nao custa clique a
mais — subir campanha sem elas e entrar no leilao com a mao amarrada, exatamente
o erro que custou 90% de perda por ranking no primeiro dia da campanha original.

POR QUE VINCULAR E NAO RECRIAR: extensoes.py cria assets novos a cada execucao.
Rodar ele 4 vezes geraria 4 copias de cada sitelink — relatorio poluido e
historico de desempenho fatiado entre clones. Asset no Google Ads e reaproveitavel:
o mesmo sitelink pode estar em N campanhas e consolida as metricas. Aqui a gente
so cria o VINCULO (campaign_asset), nunca o asset.

IDEMPOTENTE: le os vinculos existentes antes e pula o que ja esta la, entao rodar
duas vezes nao duplica.

POR QUE NAO NO NIVEL DA CONTA: seria uma linha so, mas a conta e compartilhada —
hospeda Elibell e kernelpanic. Sitelink apontando pra /oferta/ do SVD apareceria
nos anuncios de oficina mecanica.

Uso:
  python3 vincular-extensoes.py --de="SVD - Promocao 10 Anos" --para="SVD - Afiliados,SVD - Parceiros" --dry-run
  python3 vincular-extensoes.py --de="SVD - Promocao 10 Anos" --para="SVD - Afiliados,SVD - Parceiros"
"""
import sys

sys.path.insert(0, "/data/coderush-sites/automation/ads")
from _conta import cliente, resolver  # noqa: E402


def arg(nome, padrao=None):
    for a in sys.argv[1:]:
        if a.startswith(f"--{nome}="):
            return a.split("=", 1)[1]
    return padrao


def campanhas(ga, cid, nomes):
    lista = "','".join(n.replace("'", "") for n in nomes)
    q = f"""SELECT campaign.resource_name, campaign.name FROM campaign
            WHERE campaign.name IN ('{lista}') AND campaign.status != 'REMOVED'"""
    return {r.campaign.name: r.campaign.resource_name
            for r in ga.search(customer_id=cid, query=q)}


def assets_de(ga, cid, nome):
    """(resource_name do asset, tipo, rotulo legivel) ja vinculados a campanha."""
    # campaign.name precisa estar no SELECT pra poder filtrar por ele (GAQL)
    q = f"""SELECT campaign.name, asset.resource_name, asset.type,
            asset.sitelink_asset.link_text, asset.callout_asset.callout_text,
            campaign_asset.field_type
            FROM campaign_asset WHERE campaign.name = '{nome}'
            AND campaign_asset.status != 'REMOVED'"""
    out = []
    for r in ga.search(customer_id=cid, query=q):
        rotulo = (r.asset.sitelink_asset.link_text
                  or r.asset.callout_asset.callout_text
                  or r.asset.type_.name)
        out.append((r.asset.resource_name, r.campaign_asset.field_type, rotulo))
    return out


def ja_vinculados(ga, cid, nome):
    q = f"""SELECT campaign.name, asset.resource_name, campaign_asset.field_type
            FROM campaign_asset WHERE campaign.name = '{nome}'
            AND campaign_asset.status != 'REMOVED'"""
    return {(r.asset.resource_name, r.campaign_asset.field_type)
            for r in ga.search(customer_id=cid, query=q)}


def main():
    cid = resolver(sys.argv)
    origem = arg("de", "SVD - Promocao 10 Anos")
    destinos = [d.strip() for d in (arg("para") or "").split(",") if d.strip()]
    if not destinos:
        sys.exit('informe --para="Campanha A,Campanha B"')
    dry = "--dry-run" in sys.argv

    cli = cliente()
    ga = cli.get_service("GoogleAdsService")

    fonte = assets_de(ga, cid, origem)
    if not fonte:
        sys.exit(f"campanha de origem {origem!r} nao tem assets vinculados")
    print(f"=== {len(fonte)} asset(s) em {origem!r} ===")
    for _, ft, rotulo in fonte:
        print(f"    {ft.name:12} {rotulo}")

    mapa = campanhas(ga, cid, destinos)
    faltando = [d for d in destinos if d not in mapa]
    if faltando:
        sys.exit(f"campanha(s) nao encontrada(s): {faltando}")

    ops = []
    for nome in destinos:
        existentes = ja_vinculados(ga, cid, nome)
        novos = [(rn, ft, rot) for rn, ft, rot in fonte if (rn, ft) not in existentes]
        print(f"\n  {nome}: {len(novos)} a vincular"
              f"{f' ({len(fonte)-len(novos)} ja existiam)' if len(novos) < len(fonte) else ''}")
        for rn, ft, rot in novos:
            print(f"      + {ft.name:12} {rot}")
            op = cli.get_type("CampaignAssetOperation")
            ca = op.create
            ca.campaign = mapa[nome]
            ca.asset = rn
            ca.field_type = ft
            ops.append(op)

    if dry:
        print(f"\n  (dry-run — {len(ops)} vinculo(s) nao enviados)")
        return
    if not ops:
        print("\n  nada a fazer")
        return
    res = cli.get_service("CampaignAssetService").mutate_campaign_assets(
        customer_id=cid, operations=ops)
    print(f"\n  {len(res.results)} vinculo(s) criado(s)")


if __name__ == "__main__":
    main()
