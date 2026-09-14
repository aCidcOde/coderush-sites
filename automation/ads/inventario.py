#!/usr/bin/env python3
"""
[Modulo Ads — inventario completo do que esta publicado]
@Author: Andre Gomes ( @acidcode )
@since 2026-09-14

POR QUE ISSO EXISTE: "o que exatamente esta no ar?" e uma pergunta que a
interface do Google Ads responde mal — o dado esta espalhado por seis telas e
cada uma esconde metade. Sem uma visao unica, coisa errada fica no ar por
semanas: foi assim que os 9 anuncios da conta passaram 12 dias anunciando um
prazo vencido, e que 4 campanhas ficaram um mes prontas e paradas.

Mostra, por campanha ativa: orcamento, estrategia de lance, datas, grupos,
palavras com Indice de Qualidade, texto integral dos anuncios, extensoes e
contagem de negativas.

Uso:
  python3 inventario.py                    # so campanhas ativas
  python3 inventario.py --todas            # inclui pausadas
  python3 inventario.py --filtro=SVD       # so as que casam com o nome
"""
import sys
from collections import defaultdict

sys.path.insert(0, "/data/coderush-sites/automation/ads")
from _conta import cliente, resolver  # noqa: E402

SINAL = {"BELOW_AVERAGE": "abaixo", "AVERAGE": "media", "ABOVE_AVERAGE": "acima",
         "UNKNOWN": "-", "UNSPECIFIED": "-"}


def arg(nome, padrao=None):
    for a in sys.argv[1:]:
        if a.startswith(f"--{nome}="):
            return a.split("=", 1)[1]
    return padrao


def main():
    cid = resolver(sys.argv)
    ga = cliente().get_service("GoogleAdsService")
    filtro = arg("filtro", "")
    status = "" if "--todas" in sys.argv else "AND campaign.status = 'ENABLED'"
    nome_like = f"AND campaign.name LIKE '{filtro}%'" if filtro else ""

    # ── campanhas ──
    q = f"""SELECT campaign.id, campaign.name, campaign.status,
            campaign.advertising_channel_type, campaign.start_date_time,
            campaign.end_date_time, campaign_budget.amount_micros,
            campaign.bidding_strategy_type,
            campaign.target_impression_share.location,
            campaign.target_impression_share.location_fraction_micros,
            campaign.target_impression_share.cpc_bid_ceiling_micros
            FROM campaign WHERE campaign.status != 'REMOVED' {status} {nome_like}
            ORDER BY campaign.name"""
    campanhas = list(ga.search(customer_id=cid, query=q))
    if not campanhas:
        sys.exit("nenhuma campanha encontrada")

    # ── coleta em lote (uma consulta por tipo, nao uma por campanha) ──
    kw = defaultdict(list)
    q2 = f"""SELECT campaign.name, campaign.status, ad_group.name, ad_group_criterion.keyword.text,
             ad_group_criterion.keyword.match_type, ad_group_criterion.status,
             ad_group_criterion.quality_info.quality_score,
             ad_group_criterion.quality_info.post_click_quality_score,
             ad_group_criterion.quality_info.creative_quality_score
             FROM ad_group_criterion WHERE ad_group_criterion.type = 'KEYWORD'
             AND ad_group_criterion.negative = FALSE
             AND ad_group_criterion.status != 'REMOVED'
             AND campaign.status != 'REMOVED' {status} {nome_like}"""
    for r in ga.search(customer_id=cid, query=q2):
        kw[(r.campaign.name, r.ad_group.name)].append(r.ad_group_criterion)

    neg = defaultdict(int)
    q3 = f"""SELECT campaign.name, campaign.status, campaign_criterion.keyword.text FROM campaign_criterion
             WHERE campaign_criterion.type = 'KEYWORD' AND campaign_criterion.negative = TRUE
             AND campaign.status != 'REMOVED' {status} {nome_like}"""
    for r in ga.search(customer_id=cid, query=q3):
        neg[r.campaign.name] += 1

    ads = defaultdict(list)
    q4 = f"""SELECT campaign.name, campaign.status, ad_group.name, ad_group_ad.status,
             ad_group_ad.policy_summary.approval_status, ad_group_ad.ad_strength,
             ad_group_ad.ad.final_urls,
             ad_group_ad.ad.responsive_search_ad.headlines,
             ad_group_ad.ad.responsive_search_ad.descriptions
             FROM ad_group_ad WHERE ad_group_ad.status != 'REMOVED'
             AND campaign.status != 'REMOVED' {status} {nome_like}"""
    for r in ga.search(customer_id=cid, query=q4):
        ads[(r.campaign.name, r.ad_group.name)].append(r.ad_group_ad)

    ext = defaultdict(list)
    q5 = f"""SELECT campaign.name, campaign.status, campaign_asset.field_type,
             asset.sitelink_asset.link_text, asset.callout_asset.callout_text,
             asset.call_asset.phone_number
             FROM campaign_asset WHERE campaign_asset.status != 'REMOVED'
             AND campaign.status != 'REMOVED' {status} {nome_like}"""
    for r in ga.search(customer_id=cid, query=q5):
        rotulo = (r.asset.sitelink_asset.link_text or r.asset.callout_asset.callout_text
                  or r.asset.call_asset.phone_number or r.campaign_asset.field_type.name)
        ext[r.campaign.name].append((r.campaign_asset.field_type.name, rotulo))

    # ── impressao ──
    for c in campanhas:
        cp = c.campaign
        t = cp.target_impression_share
        estrategia = cp.bidding_strategy_type.name
        if t.location_fraction_micros:
            estrategia += (f" · meta {t.location_fraction_micros/1e4:.0f}% no "
                           f"{t.location.name.replace('_',' ').lower()} · teto "
                           f"R$ {t.cpc_bid_ceiling_micros/1e6:.2f}")
        print(f"\n{'=' * 78}")
        print(f" {cp.name}   [{cp.status.name}]")
        print(f"{'=' * 78}")
        print(f"  orcamento   R$ {c.campaign_budget.amount_micros/1e6:.2f}/dia")
        print(f"  estrategia  {estrategia}")
        print(f"  periodo     {cp.start_date_time[:10]} -> {(cp.end_date_time or '(sem fim)')[:10]}")
        print(f"  negativas   {neg.get(cp.name, 0)}")
        if ext.get(cp.name):
            por_tipo = defaultdict(list)
            for tipo, rot in ext[cp.name]:
                por_tipo[tipo].append(rot)
            for tipo, itens in por_tipo.items():
                print(f"  {tipo.lower():11} {', '.join(itens)}")
        else:
            print("  extensoes   NENHUMA")

        grupos = sorted({g for (cn, g) in list(kw) + list(ads) if cn == cp.name})
        for g in grupos:
            print(f"\n  ── grupo: {g} ──")
            for a in ads.get((cp.name, g), []):
                forca = a.ad_strength.name
                print(f"     anuncio [{a.status.name}] aprovacao={a.policy_summary.approval_status.name} forca={forca}")
                print(f"     destino: {a.ad.final_urls[0] if a.ad.final_urls else '-'}")
                for h in a.ad.responsive_search_ad.headlines:
                    print(f"       T ({len(h.text):2}) {h.text}")
                for d in a.ad.responsive_search_ad.descriptions:
                    print(f"       D ({len(d.text):2}) {d.text}")
            palavras = kw.get((cp.name, g), [])
            if palavras:
                print(f"     {len(palavras)} palavra(s):")
                for k in sorted(palavras, key=lambda x: x.keyword.text):
                    qi = k.quality_info
                    marca = "" if k.status.name == "ENABLED" else f" [{k.status.name}]"
                    print(f"       {k.keyword.text:42} {k.keyword.match_type.name[:6]:6} "
                          f"IQ={qi.quality_score or '-'} pagina={SINAL.get(qi.post_click_quality_score.name,'-'):6} "
                          f"anuncio={SINAL.get(qi.creative_quality_score.name,'-'):6}{marca}")


if __name__ == "__main__":
    main()
