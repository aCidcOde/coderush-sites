#!/usr/bin/env python3
"""
[Modulo Ads BFR — criacao da primeira campanha de busca]
@Author: Andre Gomes ( @acidcode )
@since 2026-09-12

POR QUE ISSO EXISTE: primeira entrada da BFR Intelligence em midia paga.

AS PALAVRAS AQUI SAO HIPOTESE, NAO DADO — e isso muda como a campanha e operada.
No SVD a escolha de pauta veio do Search Console; a BFR nao tem Search Console
configurado (pendencia registrada no playbook desde 17/08) e o Keyword Planner da
API esta bloqueado — o developer token segue em "explorer access", aguardando
acesso Basic. Sem as duas fontes, a unica saida honesta e chutar com metodo e
corrigir rapido: por isso a lista de negativas ja nasce grande e o relatorio de
termos de busca precisa ser lido nas primeiras 48h, nao no fim da quinzena.
Ver docs/playbook-conteudo-seo.md secao 7.

O RISCO ESPECIFICO DESTE NICHO: "agente de ia" e "inteligencia artificial" atraem
volume enorme de busca informativa e de estudante — curso, tutorial, o que e,
como criar, ChatGPT, API, Python. No SVD tudo isso era desperdicio direto de
verba; aqui e pior, porque o CPC do nicho de IA e varias vezes o de MMN. As
negativas abaixo nao sao paranoia, sao o que impede a verba evaporar em 3 dias.

TARGET_IMPRESSION_SHARE com teto: mesma escolha do SVD. Sem historico de
conversao, lance por conversao nao tem o que aprender; o objetivo aqui e comprar
presenca medida e descobrir CPC real. O teto e o freio.

Uso:
  python3 criar-campanha-bfr.py --dry-run
  python3 criar-campanha-bfr.py
"""
import sys

sys.path.insert(0, "/data/coderush-sites/automation/ads")
from _conta import cliente, resolver  # noqa: E402

CAMPANHA = "BFR - Agentes de IA"
ORCAMENTO = 25.0
META_TOPO = 65          # % de parcela de impressao no topo
TETO_CPC = 14.0         # nicho de IA e caro; abaixo disso nao aparece
FIM = "2026-09-26"      # 15 dias de teste, conforme combinado
BASE = "https://bfrintelligence.com.br/"
UTM = "utm_source=google&utm_medium=cpc&utm_campaign=bfr-agentes"

MAX_TITULO, MAX_DESCRICAO = 30, 90

GRUPOS = {
    "Agentes de IA": [
        "agente de ia para empresas",
        "plataforma de agentes de ia",
        "agente de ia corporativo",
        "criar agente de ia para empresa",
        "agentes de ia para negocios",
    ],
    "Chatbot IA Empresa": [
        "chatbot com inteligencia artificial",
        "chatbot para empresas",
        "atendimento automatizado com ia",
        "ia para atendimento ao cliente",
        "agente de ia para whatsapp",
    ],
    "IA Corporativa": [
        "inteligencia artificial para empresas",
        "solucoes de ia para empresas",
        "implantar ia na empresa",
        "automatizar processos com ia",
    ],
}

TITULOS = [
    "Agentes de IA Corporativos",
    "IA que Entra em Operação",
    "Diagnóstico Gratuito",
    "Sua Marca, Seu Domínio",
    "No Ar em Semanas",
    "Multiempresa e Whitelabel",
    "Integra com Seus Sistemas",
    "Custo de IA sob Controle",
    "Agente de IA no WhatsApp",
    "Painel Unificado 360",
    "Governança e Auditoria",
    "Do Piloto à Operação",
    "LGPD by Design",
    "Fale com um Especialista",
    "BFR Intelligence",
]
DESCRICOES = [
    "Não vendemos modelo de IA. Entregamos agente em operação, com sua marca e governança.",
    "Diagnóstico gratuito: mapeamos seus processos e estimamos o retorno antes de investir.",
    "Multiempresa, whitelabel e dados isolados. LGPD by design em todos os níveis.",
    "Integra com seus sistemas e canais. Custo por empresa visível e sob controle.",
]

# Busca informativa e de estudante domina este nicho. Cada termo aqui e verba que
# nao vira clique de quem nunca ia contratar.
NEGATIVAS = [
    "curso", "cursos", "gratis", "gratuito", "free", "download", "baixar",
    "tutorial", "como criar", "como fazer", "o que e", "o que sao", "significado",
    "exemplos", "python", "api", "codigo", "github", "open source", "chatgpt",
    "gpt", "copilot", "gemini", "deepseek", "llama", "prompt", "prompts",
    "emprego", "vaga", "vagas", "salario", "carreira", "faculdade", "pos graduacao",
    "certificacao", "estudar", "aprender", "livro", "pdf", "artigo cientifico",
    "trabalho academico", "tcc", "mestrado", "gerador de imagem", "gerar imagem",
    "trader", "apostas", "namorada virtual",
]


def valida():
    """Titulo >30 ou descricao >90 derruba a RSA inteira, nao so o campo.
    Palavra toda em caixa alta com mais de 3 letras e reprovada por politica —
    foi o que reprovou a RSA inteira do SVD com 'Ate 40% OFF'."""
    erros = []
    for t in TITULOS:
        if len(t) > MAX_TITULO:
            erros.append(f"titulo {len(t)}/{MAX_TITULO}: {t}")
    for d in DESCRICOES:
        if len(d) > MAX_DESCRICAO:
            erros.append(f"descricao {len(d)}/{MAX_DESCRICAO}: {d}")
    for txt in TITULOS + DESCRICOES:
        for p in txt.split():
            limpo = "".join(c for c in p if c.isalpha())
            # LGPD e nome proprio de lei; siglas reconhecidas o Google aceita
            if len(limpo) > 3 and limpo.isupper() and limpo not in ("LGPD",):
                erros.append(f"caixa alta '{p}': {txt}")
    if len(TITULOS) < 3 or len(DESCRICOES) < 2:
        erros.append("RSA exige no minimo 3 titulos e 2 descricoes")
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
    print(f"  validacao ok: {len(TITULOS)} titulos, {len(DESCRICOES)} descricoes")

    cli = cliente()
    ga = cli.get_service("GoogleAdsService")

    ja = list(ga.search(customer_id=cid, query=f"""
        SELECT campaign.name FROM campaign WHERE campaign.name = '{CAMPANHA}'
        AND campaign.status != 'REMOVED'"""))
    if ja:
        sys.exit(f"campanha {CAMPANHA!r} ja existe — use os scripts de ajuste")

    total_kw = sum(len(v) for v in GRUPOS.values())
    print(f"\n=== {CAMPANHA} ===")
    print(f"  orcamento R$ {ORCAMENTO:.2f}/dia ate {FIM}")
    print(f"  meta {META_TOPO}% no topo, teto R$ {TETO_CPC:.2f}")
    print(f"  {len(GRUPOS)} grupo(s), {total_kw} palavra(s), {len(NEGATIVAS)} negativa(s)")
    for g, kws in GRUPOS.items():
        print(f"    [{g}] {', '.join(kws)}")
    if dry:
        print("\n  (dry-run — nada foi criado)")
        return

    # 1) orcamento — reaproveita se ja existe. A criacao da campanha pode falhar
    # depois desta etapa (v25 passou a exigir campos novos sem aviso), e rodar de
    # novo sem checar deixaria orcamento orfao acumulando na conta.
    nome_orc = f"Orcamento {CAMPANHA}"
    achado = list(ga.search(customer_id=cid, query=f"""
        SELECT campaign_budget.resource_name, campaign_budget.name
        FROM campaign_budget WHERE campaign_budget.name = '{nome_orc}'
        AND campaign_budget.status != 'REMOVED'"""))
    if achado:
        orcamento_rn = achado[0].campaign_budget.resource_name
        print(f"\n  orcamento reaproveitado: {orcamento_rn}")
    else:
        bop = cli.get_type("CampaignBudgetOperation")
        b = bop.create
        b.name = nome_orc
        b.amount_micros = int(ORCAMENTO * 1e6)
        b.delivery_method = cli.enums.BudgetDeliveryMethodEnum.STANDARD
        b.explicitly_shared = False
        orcamento_rn = cli.get_service("CampaignBudgetService").mutate_campaign_budgets(
            customer_id=cid, operations=[bop]).results[0].resource_name

    # 2) campanha
    cop = cli.get_type("CampaignOperation")
    c = cop.create
    c.name = CAMPANHA
    c.advertising_channel_type = cli.enums.AdvertisingChannelTypeEnum.SEARCH
    c.status = cli.enums.CampaignStatusEnum.PAUSED  # so liga depois de conferir
    c.campaign_budget = orcamento_rn
    c.target_impression_share.location = (
        cli.enums.TargetImpressionShareLocationEnum.TOP_OF_PAGE)
    c.target_impression_share.location_fraction_micros = int(META_TOPO * 1e4)
    c.target_impression_share.cpc_bid_ceiling_micros = int(TETO_CPC * 1e6)
    # display e parceiros diluem verba de teste em inventario que nao da pra ler
    c.network_settings.target_google_search = True
    c.network_settings.target_search_network = False
    c.network_settings.target_content_network = False
    c.network_settings.target_partner_search_network = False
    c.end_date_time = f"{FIM} 23:59:59"
    # obrigatorio na v25 (regra de publicidade politica da UE): omitir devolve
    # "The required field was not present" apontando pra este campo
    c.contains_eu_political_advertising = (
        cli.enums.EuPoliticalAdvertisingStatusEnum
        .DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING)
    campanha_rn = cli.get_service("CampaignService").mutate_campaigns(
        customer_id=cid, operations=[cop]).results[0].resource_name
    print(f"\n  campanha criada (pausada): {campanha_rn}")

    # 3) geo Brasil + idioma portugues
    crits = []
    geo = cli.get_type("CampaignCriterionOperation")
    geo.create.campaign = campanha_rn
    geo.create.location.geo_target_constant = "geoTargetConstants/2076"
    crits.append(geo)
    idioma = cli.get_type("CampaignCriterionOperation")
    idioma.create.campaign = campanha_rn
    idioma.create.language.language_constant = "languageConstants/1014"
    crits.append(idioma)
    for termo in NEGATIVAS:
        n = cli.get_type("CampaignCriterionOperation")
        n.create.campaign = campanha_rn
        n.create.negative = True
        n.create.keyword.text = termo
        n.create.keyword.match_type = cli.enums.KeywordMatchTypeEnum.PHRASE
        crits.append(n)
    cli.get_service("CampaignCriterionService").mutate_campaign_criteria(
        customer_id=cid, operations=crits)
    print(f"  geo + idioma + {len(NEGATIVAS)} negativas aplicadas")

    # 4) grupos, palavras e anuncios
    ag_svc = cli.get_service("AdGroupService")
    for nome, palavras in GRUPOS.items():
        aop = cli.get_type("AdGroupOperation")
        a = aop.create
        a.name = nome
        a.campaign = campanha_rn
        a.status = cli.enums.AdGroupStatusEnum.ENABLED
        a.type_ = cli.enums.AdGroupTypeEnum.SEARCH_STANDARD
        grupo_rn = ag_svc.mutate_ad_groups(
            customer_id=cid, operations=[aop]).results[0].resource_name

        kops = []
        for termo in palavras:
            k = cli.get_type("AdGroupCriterionOperation")
            kc = k.create
            kc.ad_group = grupo_rn
            kc.status = cli.enums.AdGroupCriterionStatusEnum.ENABLED
            kc.keyword.text = termo
            # frase, nao ampla: ampla neste nicho puxa o mundo inteiro
            kc.keyword.match_type = cli.enums.KeywordMatchTypeEnum.PHRASE
            kops.append(k)
        cli.get_service("AdGroupCriterionService").mutate_ad_group_criteria(
            customer_id=cid, operations=kops)

        slug = nome.lower().replace(" ", "-")
        dop = cli.get_type("AdGroupAdOperation")
        ad = dop.create
        ad.ad_group = grupo_rn
        ad.status = cli.enums.AdGroupAdStatusEnum.ENABLED
        ad.ad.final_urls.append(f"{BASE}?{UTM}&utm_content={slug}")
        for t in TITULOS:
            asset = cli.get_type("AdTextAsset")
            asset.text = t
            ad.ad.responsive_search_ad.headlines.append(asset)
        for d in DESCRICOES:
            asset = cli.get_type("AdTextAsset")
            asset.text = d
            ad.ad.responsive_search_ad.descriptions.append(asset)
        cli.get_service("AdGroupAdService").mutate_ad_group_ads(
            customer_id=cid, operations=[dop])
        print(f"  [{nome}] {len(palavras)} palavras + 1 anuncio")

    print(f"\n  PRONTO — {CAMPANHA} criada PAUSADA.")
    print(f"  ligar: python3 ligar-campanha.py --campanha=\"{CAMPANHA}\" --fim={FIM}")


if __name__ == "__main__":
    main()
