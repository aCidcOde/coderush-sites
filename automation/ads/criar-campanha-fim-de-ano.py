#!/usr/bin/env python3
"""
[Modulo Ads SVD — campanha da Promocao de Fim de Ano (01/11 a 31/12/2026)]
@Author: Andre Gomes ( @acidcode )
@since 2026-10-27

POR QUE UMA CAMPANHA NOVA E NAO ESTENDER A 10 ANOS: a 10 Anos termina em 31/10
(decidido em 19/10 nao estender). Campanha por promocao deixa o relatorio de cada
uma limpo, e a troca de estrategia de lance abaixo e grande demais pra fazer em
cima de uma campanha com historico de outra estrategia.

O QUE MUDA EM RELACAO A 10 ANOS (leitura de 22-27/10, ver historico.php):

1. LANCE: Maximizar cliques com teto, no lugar de parcela de impressao no topo.
   A meta de topo otimiza pra SER VISTO; de 06 a 25/10 a campanha foi vista e
   clicada (70 cliques, R$ 301) sem gerar lead. Duas semanas tratamos isso como
   leilao — e o gargalo era conversao. Maximizar cliques compra o maior numero
   de visitas pela verba, e o teto (R$ 8) impede o CPC de R$ 8,58 que o teto de
   R$ 9 da 10 Anos chegou a pagar em "sistema mmn".

2. PARCEIROS DE PESQUISA LIGADOS, de proposito: 3 dos 11 leads pagos com gclid
   vieram de SEARCH_PARTNERS (todos "sistema de venda direta") — 108 cliques e
   R$ 99,60 ali contra R$ 1.557 no Google pra 8 leads. Custo por lead ~6x menor.
   Desligar seria cortar o canal mais barato que a conta tem.

3. SO OS DOIS GRUPOS QUE TEM BUSCA: Concorrentes (22 impressoes em 2,5 meses) e
   Fundo de Funil (zero) nao vieram. De Aluguel, so "sistema mmn pronto", a unica
   que teve clique, entra no grupo MMN. "plataforma (de) venda direta" seguem de
   fora (pausadas em 10/10 por volume sem lead — nao religar).

4. ANUNCIO PROMETE A DEMONSTRACAO SEM CADASTRO, nao so desconto. Ela existe
   desde sempre (painel.sistemavendadireta.com.br/primeiros-passos) e e o pedido
   de menor atrito que temos: concorrente da "teste 14 dias" COM cadastro.
   Titulo 1 fica fixo com o nome do produto (relevancia pro Indice de Qualidade);
   titulo 2 alterna desconto e demonstracao.

Negativas, frases de destaque e telefone vem da 10 Anos (copiadas/vinculadas, nao
recriadas). Sitelinks sao NOVOS porque carregam utm_campaign na URL — reaproveitar
os da 10 Anos atribuiria o lead da Fim de Ano a promocao errada.

Idempotente: cada etapa confere o que ja existe antes de criar, entao rodar de novo
depois de uma falha no meio continua de onde parou.

Uso:
  python3 criar-campanha-fim-de-ano.py --dry-run
  python3 criar-campanha-fim-de-ano.py
"""
import sys

sys.path.insert(0, "/data/coderush-sites/automation/ads")
from _conta import cliente, resolver  # noqa: E402

CAMPANHA = "SVD - Promocao Fim de Ano"
ORIGEM = "SVD - Promocao 10 Anos"   # de onde vem negativas, destaques e telefone
ORCAMENTO = 20.0
TETO_CPC = 8.0
INICIO, FIM = "2026-11-01", "2026-12-31"   # casa com PROMO_CALENDARIO em inc/promo.php
SLUG = "promo-fim-de-ano"                  # idem ('slug')
BASE = "https://sistemavendadireta.com.br"
DEMO = "https://painel.sistemavendadireta.com.br/primeiros-passos"
MAX_TITULO, MAX_DESCRICAO = 30, 90


def utm(content):
    return f"utm_source=google&utm_medium=cpc&utm_campaign={SLUG}&utm_content={content}"


# '"x"' = frase, '[x]' = exata — mesmas correspondencias que a 10 Anos usava
GRUPOS = {
    "Sistema MMN": dict(
        destino="/sistema-mmn/", content="mmn", path=("Sistema-MMN", "Fim-de-Ano"),
        h1=["Sistema MMN Completo", "Software MMN Pronto p/ Operar", "Plataforma MMN Completa"],
        extra=["Plano Binário e Unilevel"],
        palavras=['"sistema mmn"', '"sistema multinivel"', '"site de marketing multinivel"',
                  '"plataforma marketing multinivel"', '"software para marketing multinivel"',
                  '[software para mmn]', '"plataforma de mmn"', '"software mmn"', '"sistema para mmn"',
                  '"site mmn"', '"software de marketing multinivel"', '"script mmn"',
                  '[sistema de marketing de rede]', '"plataforma mmn"', '"software marketing multinivel"',
                  '[sistema de marketing multinivel]', '[sistema marketing multinivel]',
                  '"script marketing multinivel"', '"sistema unilevel"', '"sistema binario multinivel"',
                  '"aplicativo para mmn"', '"sistema plano binario"', '"sistema plano unilevel"',
                  '"sistema mmn pronto"']),
    "Venda Direta": dict(
        destino="/sistema-venda-direta/", content="venda-direta", path=("Venda-Direta", "Fim-de-Ano"),
        h1=["Sistema de Venda Direta", "Plataforma de Venda Direta", "Software de Venda Direta"],
        extra=["Escritório do Consultor"],
        palavras=['"sistema venda direta"', '"sistema de venda direta"', '"software venda direta"',
                  '[software de venda direta]', '"aplicativo de venda direta"', '"escritorio virtual mmn"',
                  '"sistema para vendas diretas"', '"escritorio virtual venda direta"']),
}
# disputam a posicao 2: um anuncio puxa pelo preco, outro pelo "entre e veja"
H2 = ["Até 40% de Desconto", "Demonstração sem Cadastro"]
TITULOS = ["Promoção de Fim de Ano", "Instalação por R$ 3.000", "Entre e Teste o Sistema Agora",
           "Escritório Virtual Incluso", "Mensalidade desde R$ 500", "Clientes em 5 Países",
           "Migramos seu Sistema Atual", "10 Anos de Mercado", "Comece 2027 com a Rede no Ar"]
# 3 e 4 sao as da 10 Anos, aprovadas. Nada de "Comissões Automáticas": reprovado
# por MISLEADING_CONTENT no grupo Venda Direta em 19/10.
DESCRICOES = [
    "Promoção de Fim de Ano: instalação R$ 3.000 à vista ou R$ 3.500 em 2x até 31/12.",
    "Entre na demonstração sem cadastro: loja, escritório do consultor e painel do admin.",
    "Plataforma completa: escritório virtual, rede binária e unilevel, loja e financeiro.",
    "Rodando no Brasil, Paraguai e Bolívia. Multi-idioma, multimoeda e comissão por cargo.",
]
# (texto, linha 1, linha 2, url) — texto <=25, linhas <=35
SITELINKS = [
    ("Ver demonstração", "Sistema real, sem cadastro", "Loja, escritório e painel", f"{DEMO}?{utm('sitelink-demo')}"),
    ("Cases reais", "Quem já opera com o SVD", "Brasil, Paraguai e Bolívia", f"{BASE}/cases/?{utm('sitelink-cases')}"),
    ("Simular comissões", "Custo da rede por consultor", "Simulador em 30 segundos", f"{BASE}/simulador/?{utm('sitelink-simulador')}"),
    ("Planos e mensalidade", "A partir de R$ 500 por mês", "Proporcional ao faturamento", f"{BASE}/sistema-venda-direta/?{utm('sitelink-planos')}#simulador"),
    ("Falar com especialista", "Tire dúvidas sem compromisso", "Resposta no mesmo dia", f"{BASE}/sistema-venda-direta/?{utm('sitelink-contato')}#garantir"),
]
DESTAQUES_NOVOS = ["Demonstração sem cadastro", "Promoção até 31/12"]   # <=25
PROMOCAO = dict(alvo="Instalação do SVD", pct=40, url=f"{BASE}/sistema-venda-direta/?{utm('promocao')}")


def kw(k):
    return (k.strip('"[]'), "EXACT" if k.startswith("[") else "PHRASE")


def valida():
    """Titulo >30 ou descricao >90 derruba a RSA inteira, nao so o campo; palavra
    toda em caixa alta (fora sigla) reprova por politica."""
    erros = []
    for g, c in GRUPOS.items():
        tit = c["h1"] + H2 + c["extra"] + TITULOS
        if len(tit) > 15 or len(set(tit)) != len(tit):
            erros.append(f"[{g}] {len(tit)} titulos (max 15, sem repetir)")
        erros += [f"titulo {len(t)}/{MAX_TITULO}: {t}" for t in tit if len(t) > MAX_TITULO]
        erros += [f"path >15: {p}" for p in c["path"] if len(p) > 15]
    erros += [f"descricao {len(d)}/{MAX_DESCRICAO}: {d}" for d in DESCRICOES if len(d) > MAX_DESCRICAO]
    for t, l1, l2, _ in SITELINKS:
        erros += [f"sitelink {x!r} {len(x)}/{n}" for x, n in ((t, 25), (l1, 35), (l2, 35)) if len(x) > n]
    erros += [f"destaque {len(d)}/25: {d}" for d in DESTAQUES_NOVOS if len(d) > 25]
    if len(PROMOCAO["alvo"]) > 20:
        erros.append(f"promocao alvo {len(PROMOCAO['alvo'])}/20")
    textos = sum((c["h1"] + c["extra"] for c in GRUPOS.values()), []) + H2 + TITULOS + DESCRICOES
    for txt in textos:
        for p in txt.split():
            limpo = "".join(ch for ch in p if ch.isalpha())
            if len(limpo) > 3 and limpo.isupper() and limpo not in ("MMN", "SVD"):
                erros.append(f"caixa alta '{p}': {txt}")
    return erros


def main():
    cid = resolver(sys.argv)
    dry = "--dry-run" in sys.argv
    erros = valida()
    if erros:
        print("VALIDACAO FALHOU:")
        for e in erros:
            print(f"  {e}")
        sys.exit(1)

    cli = cliente()
    ga = cli.get_service("GoogleAdsService")
    E = cli.enums

    def q(s):
        return list(ga.search(customer_id=cid, query=s))

    origem = q(f"SELECT campaign.resource_name FROM campaign WHERE campaign.name = '{ORIGEM}' "
               "AND campaign.status != 'REMOVED'")
    if not origem:
        sys.exit(f"campanha de origem {ORIGEM!r} nao encontrada")
    negativas = [(r.campaign_criterion.keyword.text, r.campaign_criterion.keyword.match_type.name)
                 for r in q(f"""SELECT campaign.name, campaign_criterion.keyword.text,
                    campaign_criterion.keyword.match_type FROM campaign_criterion
                    WHERE campaign.name = '{ORIGEM}' AND campaign_criterion.negative = TRUE
                    AND campaign_criterion.type = 'KEYWORD'""")]
    herdados = [(r.asset.resource_name, r.campaign_asset.field_type.name,
                 r.asset.callout_asset.callout_text or r.asset.call_asset.phone_number)
                for r in q(f"""SELECT campaign.name, asset.resource_name, campaign_asset.field_type,
                    asset.callout_asset.callout_text, asset.call_asset.phone_number
                    FROM campaign_asset WHERE campaign.name = '{ORIGEM}'
                    AND campaign_asset.status != 'REMOVED'
                    AND campaign_asset.field_type IN ('CALLOUT', 'CALL')""")]

    print(f"=== {CAMPANHA} ===")
    print(f"  {INICIO} a {FIM} · R$ {ORCAMENTO:.2f}/dia · Maximizar cliques, teto R$ {TETO_CPC:.2f}")
    print("  Google + parceiros de pesquisa, sem Display · Brasil · portugues")
    for g, c in GRUPOS.items():
        print(f"  [{g}] {len(c['palavras'])} palavras -> {c['destino']}")
    print(f"  {len(negativas)} negativas e {len(herdados)} destaques/telefone herdados de {ORIGEM}")
    print(f"  {len(SITELINKS)} sitelinks novos, {len(DESTAQUES_NOVOS)} destaques novos, 1 promocao (ate {PROMOCAO['pct']}%)")
    if dry:
        print("\n  (dry-run — nada foi criado)")
        return

    # 1) orcamento + campanha
    camp = q(f"SELECT campaign.resource_name FROM campaign WHERE campaign.name = '{CAMPANHA}' "
             "AND campaign.status != 'REMOVED'")
    if camp:
        camp_rn = camp[0].campaign.resource_name
        print(f"\n  [=] campanha ja existe: {camp_rn}")
    else:
        nome_orc = f"Orcamento {CAMPANHA}"
        orc = q(f"SELECT campaign_budget.resource_name FROM campaign_budget "
                f"WHERE campaign_budget.name = '{nome_orc}' AND campaign_budget.status != 'REMOVED'")
        if orc:
            orc_rn = orc[0].campaign_budget.resource_name
        else:
            op = cli.get_type("CampaignBudgetOperation")
            op.create.name = nome_orc
            op.create.amount_micros = int(ORCAMENTO * 1e6)
            op.create.delivery_method = E.BudgetDeliveryMethodEnum.STANDARD
            op.create.explicitly_shared = False
            orc_rn = cli.get_service("CampaignBudgetService").mutate_campaign_budgets(
                customer_id=cid, operations=[op]).results[0].resource_name
        op = cli.get_type("CampaignOperation")
        c = op.create
        c.name = CAMPANHA
        c.advertising_channel_type = E.AdvertisingChannelTypeEnum.SEARCH
        # ENABLED com inicio no futuro: os anuncios passam pela revisao de politica
        # agora e a campanha so veicula a partir de INICIO
        c.status = E.CampaignStatusEnum.ENABLED
        c.campaign_budget = orc_rn
        c.target_spend.cpc_bid_ceiling_micros = int(TETO_CPC * 1e6)
        c.network_settings.target_google_search = True
        c.network_settings.target_search_network = True
        c.network_settings.target_content_network = False
        c.start_date_time = f"{INICIO} 00:00:00"
        c.end_date_time = f"{FIM} 23:59:59"
        c.contains_eu_political_advertising = (
            E.EuPoliticalAdvertisingStatusEnum.DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING)
        camp_rn = cli.get_service("CampaignService").mutate_campaigns(
            customer_id=cid, operations=[op]).results[0].resource_name
        print(f"\n  [+] campanha {camp_rn}")

        # geo + idioma + negativas: so na criacao (criterio repetido da erro)
        ops = []
        for campo, valor in (("location.geo_target_constant", "geoTargetConstants/2076"),
                             ("language.language_constant", "languageConstants/1014")):
            o = cli.get_type("CampaignCriterionOperation")
            o.create.campaign = camp_rn
            alvo, attr = campo.split(".")
            setattr(getattr(o.create, alvo), attr, valor)
            ops.append(o)
        for texto, tipo in negativas:
            o = cli.get_type("CampaignCriterionOperation")
            o.create.campaign = camp_rn
            o.create.negative = True
            o.create.keyword.text = texto
            o.create.keyword.match_type = E.KeywordMatchTypeEnum[tipo]
            ops.append(o)
        cli.get_service("CampaignCriterionService").mutate_campaign_criteria(customer_id=cid, operations=ops)
        print(f"  [+] Brasil, portugues e {len(negativas)} negativas")
    camp_id = camp_rn.rsplit("/", 1)[1]

    # 2) grupos, palavras e anuncios
    for nome, c in GRUPOS.items():
        g = q(f"SELECT ad_group.resource_name FROM ad_group WHERE campaign.id = {camp_id} "
              f"AND ad_group.name = '{nome}' AND ad_group.status != 'REMOVED'")
        if g:
            g_rn = g[0].ad_group.resource_name
        else:
            op = cli.get_type("AdGroupOperation")
            op.create.name = nome
            op.create.campaign = camp_rn
            op.create.status = E.AdGroupStatusEnum.ENABLED
            op.create.type_ = E.AdGroupTypeEnum.SEARCH_STANDARD
            g_rn = cli.get_service("AdGroupService").mutate_ad_groups(
                customer_id=cid, operations=[op]).results[0].resource_name
        gid = g_rn.rsplit("/", 1)[1]

        ja = {r.ad_group_criterion.keyword.text.lower() for r in q(
            f"""SELECT ad_group_criterion.keyword.text FROM ad_group_criterion WHERE ad_group.id = {gid}
                AND ad_group_criterion.type = 'KEYWORD' AND ad_group_criterion.status != 'REMOVED'""")}
        ops = []
        for k in c["palavras"]:
            texto, tipo = kw(k)
            if texto in ja:
                continue
            o = cli.get_type("AdGroupCriterionOperation")
            o.create.ad_group = g_rn
            o.create.status = E.AdGroupCriterionStatusEnum.ENABLED
            o.create.keyword.text = texto
            o.create.keyword.match_type = E.KeywordMatchTypeEnum[tipo]
            ops.append(o)
        if ops:
            cli.get_service("AdGroupCriterionService").mutate_ad_group_criteria(customer_id=cid, operations=ops)

        tem_ad = q(f"SELECT ad_group_ad.ad.id FROM ad_group_ad WHERE ad_group.id = {gid} "
                   "AND ad_group_ad.status != 'REMOVED'")
        if tem_ad:
            print(f"  [=] [{nome}] +{len(ops)} palavras, anuncio ja existe")
            continue
        op = cli.get_type("AdGroupAdOperation")
        ad = op.create
        ad.ad_group = g_rn
        ad.status = E.AdGroupAdStatusEnum.ENABLED
        ad.ad.final_urls.append(f"{BASE}{c['destino']}?{utm(c['content'])}")
        rsa = ad.ad.responsive_search_ad
        rsa.path1, rsa.path2 = c["path"]
        for lista, pin in ((c["h1"], E.ServedAssetFieldTypeEnum.HEADLINE_1),
                           (H2, E.ServedAssetFieldTypeEnum.HEADLINE_2),
                           (c["extra"] + TITULOS, None)):
            for t in lista:
                a = cli.get_type("AdTextAsset")
                a.text = t
                if pin:
                    a.pinned_field = pin
                rsa.headlines.append(a)
        for d in DESCRICOES:
            a = cli.get_type("AdTextAsset")
            a.text = d
            rsa.descriptions.append(a)
        try:
            cli.get_service("AdGroupAdService").mutate_ad_group_ads(customer_id=cid, operations=[op])
            print(f"  [+] [{nome}] {len(ops)} palavras + anuncio")
        except Exception as err:  # reprovacao de politica nao pode derrubar o resto
            topicos = [f"{pt.type_.name}:{pt.topic}"
                       for e2 in getattr(getattr(err, "failure", None), "errors", []) or []
                       for pt in e2.details.policy_finding_details.policy_topic_entries]
            print(f"  [!] [{nome}] palavras ok, ANUNCIO RECUSADO: {', '.join(topicos) or str(err)[:200]}")

    # 3) extensoes: herdadas (vinculo) + novas (asset)
    vinc = {(r.asset.resource_name, r.campaign_asset.field_type.name) for r in q(
        f"""SELECT campaign.name, asset.resource_name, campaign_asset.field_type FROM campaign_asset
            WHERE campaign.name = '{CAMPANHA}' AND campaign_asset.status != 'REMOVED'""")}
    textos_vinc = {r.asset.sitelink_asset.link_text or r.asset.callout_asset.callout_text
                   or r.asset.promotion_asset.promotion_target for r in q(
        f"""SELECT campaign.name, asset.sitelink_asset.link_text, asset.callout_asset.callout_text,
            asset.promotion_asset.promotion_target FROM campaign_asset
            WHERE campaign.name = '{CAMPANHA}' AND campaign_asset.status != 'REMOVED'""")}

    def vincular(asset_rn, campo):
        if (asset_rn, campo) in vinc:
            return False
        o = cli.get_type("CampaignAssetOperation")
        o.create.campaign = camp_rn
        o.create.asset = asset_rn
        o.create.field_type = E.AssetFieldTypeEnum[campo]
        cli.get_service("CampaignAssetService").mutate_campaign_assets(customer_id=cid, operations=[o])
        return True

    def criar(preencher):
        o = cli.get_type("AssetOperation")
        preencher(o.create)
        return cli.get_service("AssetService").mutate_assets(
            customer_id=cid, operations=[o]).results[0].resource_name

    n = sum(vincular(rn, campo) for rn, campo, _ in herdados)
    print(f"  [+] {n} destaques/telefone vinculados da {ORIGEM}")

    for texto, l1, l2, url in SITELINKS:
        if texto in textos_vinc:
            continue

        def sl(a, texto=texto, l1=l1, l2=l2, url=url):
            a.sitelink_asset.link_text = texto
            a.sitelink_asset.description1 = l1
            a.sitelink_asset.description2 = l2
            a.final_urls.append(url)
        vincular(criar(sl), "SITELINK")
        print(f"  [+] sitelink {texto}")

    for texto in DESTAQUES_NOVOS:
        if texto in textos_vinc:
            continue
        vincular(criar(lambda a, texto=texto: setattr(a.callout_asset, "callout_text", texto)), "CALLOUT")
        print(f"  [+] destaque {texto}")

    if PROMOCAO["alvo"] not in textos_vinc:
        def promo(a):
            p = a.promotion_asset
            p.promotion_target = PROMOCAO["alvo"]
            p.discount_modifier = E.PromotionExtensionDiscountModifierEnum.UP_TO
            p.percent_off = PROMOCAO["pct"] * 10_000   # 1.000.000 = 100%
            p.language_code = "pt-BR"
            p.start_date, p.end_date = INICIO, FIM
            a.final_urls.append(PROMOCAO["url"])
        vincular(criar(promo), "PROMOTION")
        print(f"  [+] promocao ate {PROMOCAO['pct']}% em {PROMOCAO['alvo']}")

    print(f"\n  PRONTO — {CAMPANHA} ativa, veicula de {INICIO} a {FIM}.")


if __name__ == "__main__":
    main()
