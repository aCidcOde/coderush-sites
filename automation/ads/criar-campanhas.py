#!/usr/bin/env python3
"""
[Modulo Ads SVD — criacao das campanhas da Promocao 10 Anos via API]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-04
Cria as 5 campanhas (PAUSADAS) na conta SourceNET via Google Ads API.
Requer /root/.config/svd-ads/google-ads.yaml e developer token com acesso Basic.

Uso:
  python3 criar-campanhas.py --dry-run          # so mostra o plano
  python3 criar-campanhas.py                    # cria tudo pausado
  python3 criar-campanhas.py --only="SVD - Promocao 10 Anos"
"""
import sys

CUSTOMER_ID = "3578927161"
YAML = "/root/.config/svd-ads/google-ads.yaml"
BASE = "https://sistemavendadireta.com.br"  # apex: a URL final do anuncio nao pode ter redirect

H_GERAL = ["Sistema para Venda Direta","Software MMN Completo","Até 40% OFF na Instalação","Plano Binário e Unilevel","Escritório Virtual Incluso","Mensalidade desde R$ 500","Implantação Assistida por IA","Clientes em 5 Países","Loja Virtual Integrada","Comissões Automáticas","À Vista por R$ 3.000","Migramos seu Sistema Atual","Promoção 10 Anos SVD","Sua Marca e seu Domínio","Instalação por R$ 3.000","Migração do Sistema Atual","Comissões sem Planilhas"]
D_GERAL = ["Plataforma completa: escritório virtual, rede binária e unilevel, loja e financeiro.","Promoção 10 Anos: R$ 3.500 em 2x ou R$ 3.000 à vista até 31/08. Mensalidade sob medida.","Rodando no Brasil, Paraguai e Bolívia. Multi-idioma, multimoeda e comissão por cargo.","Parametrizamos seu plano de negócio: binário, unilevel ou comissão por cargo."]

def mk_h(extra):
    h = extra + [x for x in H_GERAL if x not in ("Sistema para Venda Direta","Software MMN Completo","Plano Binário e Unilevel")]
    return h[:15]

def url(path, camp, content=None):
    u = f"{BASE}{path}?utm_source=google&utm_medium=cpc&utm_campaign={camp}"
    return u + (f"&utm_content={content}" if content else "")

NEG_ALL = ["gratis","gratuito","emprego","vaga","vagas","curso","como funciona","o que é","piramide","golpe","reclame aqui","download","crack","planilha","excel","hinode","mary kay","natura","herbalife","jeunesse"]
NEG_AFIL = ["hotmart","monetizze","eduzz","kiwify","braip","como ser afiliado","ganhar dinheiro como afiliado"]

CAMPS = [
 dict(name="SVD - Promocao 10 Anos", budget=50, path="/oferta/", camp="promo-10-anos",
   heads=H_GERAL, descs=D_GERAL, negs=NEG_ALL, groups=[
    ("Sistema MMN","mmn",[('"sistema mmn"'),'"software mmn"','"sistema para mmn"','"sistema marketing multinivel"','"software marketing multinivel"','"plataforma mmn"','"sistema plano binario"','"sistema plano unilevel"','[sistema de marketing multinivel]','[software para mmn]']),
    ("Venda Direta","venda-direta",['"sistema venda direta"','"sistema de venda direta"','"software venda direta"','"plataforma de venda direta"','"sistema para vendas diretas"','"escritorio virtual mmn"','"escritorio virtual venda direta"','[software de venda direta]']),
    ("Fundo de Funil","fundo",['"quanto custa sistema mmn"','"preco software mmn"','"sistema mmn preco"','"contratar sistema venda direta"','"empresa de software para mmn"']),
    ("Aluguel Sistema Pronto","aluguel",['"aluguel sistema mmn"','"alugar sistema mmn"','"sistema mmn pronto"','"sistema mmn mensalidade"','"plataforma mmn pronta"']),
    ("Concorrentes","concorrentes",['"aliadus mmn"','"mmnweb"','"maxnivel sistema"','"m2n sistema"','"sistema mmn eloss"','"embraton mmn"']),
   ]),
 dict(name="SVD - Suplementos", budget=10, path="/oferta/suplementos/", camp="promo-10-anos-suplementos",
   heads=mk_h(["Sistema p/ Suplementos","Rede de Consultores Pronta","Case Real: Protech"]),
   descs=["ERP genérico não entende rede. Consultores, comissões, recompra e loja num só sistema.",D_GERAL[1],D_GERAL[2],D_GERAL[3]], negs=NEG_ALL,
   groups=[("Distribuidora Suplementos",None,['"sistema para distribuidora de suplementos"','"software distribuidora suplementos"','"sistema para venda de suplementos por consultores"','"sistema revenda suplementos"','"plataforma venda direta suplementos"'])]),
 dict(name="SVD - Cosmeticos", budget=10, path="/oferta/cosmeticos/", camp="promo-10-anos-cosmeticos",
   heads=mk_h(["Sistema p/ Cosméticos","Escritório da Consultora","Case Internacional Real"]),
   descs=["Catálogo por linhas, metas e ranking da consultora, comissões e loja integrada.",D_GERAL[1],D_GERAL[2],D_GERAL[3]], negs=NEG_ALL,
   groups=[("Revenda Cosmeticos",None,['"sistema para revenda de cosmeticos"','"sistema para consultoras"','"software revenda por catalogo"','"sistema venda direta cosmeticos"','"plataforma para marca de cosmeticos revenda"'])]),
 dict(name="SVD - Afiliados", budget=10, path="/oferta/afiliados/", camp="promo-10-anos-afiliados",
   heads=mk_h(["Sistema p/ Afiliados","Link e Cupom por Afiliado","Bônus Pagos Sem Planilha"]),
   descs=["Cadastre afiliados com link e cupom próprios. O sistema rastreia, calcula e paga os bônus.",D_GERAL[1],"Parcerias com influenciadores no YouTube, TikTok e Instagram. Extrato transparente.",D_GERAL[3]], negs=NEG_ALL+NEG_AFIL,
   groups=[("Programa de Afiliados",None,['"plataforma de afiliados"','"sistema para programa de afiliados"','"software programa de afiliados"','"sistema de afiliados white label"','"plataforma de afiliados para influenciadores"','"programa de afiliados para minha empresa"'])]),
 dict(name="SVD - Parceiros", budget=10, path="/oferta/parceiros/", camp="promo-10-anos-parceiros",
   heads=mk_h(["Sistema p/ Parceiros","Indicação com Comissão","Cupom por Indicador"]),
   descs=["Parceiros com link e cupom próprios: o sistema rastreia indicações e paga as comissões.",D_GERAL[1],"Regras editáveis, extrato transparente e pagamento sem planilha.",D_GERAL[3]], negs=NEG_ALL+NEG_AFIL,
   groups=[("Programa de Parceiros",None,['"sistema programa de parceiros"','"software programa de indicacao"','"sistema de indicacao de clientes"','"plataforma member get member"','"sistema para gestao de parceiros"','"programa de indicacao para empresa"'])]),
]

for c in CAMPS:
    for h in c["heads"]: assert len(h) <= 30, f"headline >30: {h}"
    for d in c["descs"]: assert len(d) <= 90, f"desc >90: {d}"

GROUP_HEADS = {
    "Sistema MMN": ["Sistema MMN Completo", "Plano Binário e Unilevel", "Rede e Comissões num Só Lugar"],
    "Venda Direta": ["Plataforma de Venda Direta", "Escritório do Consultor", "Loja + Rede Integradas"],
    "Fundo de Funil": ["Instalação por R$ 3.000", "Mensalidade desde R$ 500", "Agende uma Demonstração"],
    "Aluguel Sistema Pronto": ["Sistema Pronto para Usar", "Mensalidade desde R$ 500", "No Ar em Dias, não Meses"],
    "Concorrentes": ["Compare Antes de Fechar", "Clientes em 5 Países", "Migração do Sistema Atual"],
}
PIN_H1 = {"Instalação por R$ 3.000", "Até 40% OFF na Instalação"}

def main():
    dry = "--dry-run" in sys.argv
    only = next((a.split("=",1)[1] for a in sys.argv if a.startswith("--only=")), None)
    camps = [c for c in CAMPS if not only or c["name"] == only]

    if dry:
        for c in camps:
            print(f"[{c['name']}] R${c['budget']}/dia -> {c['path']} | grupos: {len(c['groups'])} | negs: {len(c['negs'])}")
            for g, content, kws in c["groups"]:
                print(f"   - {g}: {len(kws)} keywords | RSA {len(c['heads'])}H/{len(c['descs'])}D")
        return

    from google.ads.googleads.client import GoogleAdsClient
    cfg = open(YAML).read().replace('login_customer_id: "3139585203"', f'login_customer_id: "{CUSTOMER_ID}"')
    import tempfile
    with tempfile.NamedTemporaryFile("w", suffix=".yaml", delete=False) as f:
        f.write(cfg); tmp = f.name
    client = GoogleAdsClient.load_from_storage(tmp)

    def enums(name): return getattr(client.enums, name)

    budget_svc = client.get_service("CampaignBudgetService")
    camp_svc = client.get_service("CampaignService")
    group_svc = client.get_service("AdGroupService")
    crit_svc = client.get_service("AdGroupCriterionService")
    camp_crit_svc = client.get_service("CampaignCriterionService")
    ad_svc = client.get_service("AdGroupAdService")

    for c in camps:
        # budget
        op = client.get_type("CampaignBudgetOperation")
        b = op.create
        b.name = f"Budget {c['name']}"
        b.amount_micros = c["budget"] * 1_000_000
        b.delivery_method = enums("BudgetDeliveryMethodEnum").STANDARD
        b.explicitly_shared = False
        budget_res = budget_svc.mutate_campaign_budgets(customer_id=CUSTOMER_ID, operations=[op]).results[0].resource_name

        # campaign (PAUSED, Search, Manual CPC)
        op = client.get_type("CampaignOperation")
        cp = op.create
        cp.name = c["name"]
        cp.status = enums("CampaignStatusEnum").PAUSED
        cp.advertising_channel_type = enums("AdvertisingChannelTypeEnum").SEARCH
        cp.campaign_budget = budget_res
        cp.manual_cpc.enhanced_cpc_enabled = False
        cp.network_settings.target_google_search = True
        cp.network_settings.target_search_network = False
        cp.network_settings.target_content_network = False
        cp.contains_eu_political_advertising = enums("EuPoliticalAdvertisingStatusEnum").DOES_NOT_CONTAIN_EU_POLITICAL_ADVERTISING
        camp_res = camp_svc.mutate_campaigns(customer_id=CUSTOMER_ID, operations=[op]).results[0].resource_name
        print(f"[ok] campanha {c['name']} -> {camp_res}")

        # geo Brasil (2076) + idioma pt (1014)
        ops = []
        o = client.get_type("CampaignCriterionOperation")
        o.create.campaign = camp_res
        o.create.location.geo_target_constant = "geoTargetConstants/2076"
        ops.append(o)
        o = client.get_type("CampaignCriterionOperation")
        o.create.campaign = camp_res
        o.create.language.language_constant = "languageConstants/1014"
        ops.append(o)
        # negativas
        for neg in c["negs"]:
            o = client.get_type("CampaignCriterionOperation")
            o.create.campaign = camp_res
            o.create.negative = True
            o.create.keyword.text = neg
            o.create.keyword.match_type = enums("KeywordMatchTypeEnum").PHRASE
            ops.append(o)
        camp_crit_svc.mutate_campaign_criteria(customer_id=CUSTOMER_ID, operations=ops)

        for gname, content, kws in c["groups"]:
            op = client.get_type("AdGroupOperation")
            g = op.create
            g.name = gname
            g.campaign = camp_res
            g.status = enums("AdGroupStatusEnum").PAUSED
            g.type_ = enums("AdGroupTypeEnum").SEARCH_STANDARD
            g.cpc_bid_micros = 6_000_000
            group_res = group_svc.mutate_ad_groups(customer_id=CUSTOMER_ID, operations=[op]).results[0].resource_name

            kw_ops = []
            for k in kws:
                o = client.get_type("AdGroupCriterionOperation")
                cr = o.create
                cr.ad_group = group_res
                cr.status = enums("AdGroupCriterionStatusEnum").ENABLED
                cr.keyword.text = k.strip('"[]')
                cr.keyword.match_type = enums("KeywordMatchTypeEnum").EXACT if k.startswith("[") else enums("KeywordMatchTypeEnum").PHRASE
                kw_ops.append(o)
            crit_svc.mutate_ad_group_criteria(customer_id=CUSTOMER_ID, operations=kw_ops)

            op = client.get_type("AdGroupAdOperation")
            ada = op.create
            ada.ad_group = group_res
            ada.status = enums("AdGroupAdStatusEnum").PAUSED
            rsa = ada.ad.responsive_search_ad
            # headline especifico do grupo entra na frente (relevancia por intencao)
            heads = GROUP_HEADS.get(gname, []) + [h for h in c["heads"] if h not in GROUP_HEADS.get(gname, [])]
            for h in heads[:15]:
                a = client.get_type("AdTextAsset"); a.text = h
                # 2 titulos disputam a posicao 1 (fixar so um limita as combinacoes)
                if h in PIN_H1:
                    a.pinned_field = enums("ServedAssetFieldTypeEnum").HEADLINE_1
                rsa.headlines.append(a)
            for d in c["descs"]:
                a = client.get_type("AdTextAsset"); a.text = d
                rsa.descriptions.append(a)
            ada.ad.final_urls.append(url(c["path"], c["camp"], content))
            ad_svc.mutate_ad_group_ads(customer_id=CUSTOMER_ID, operations=[op])
            print(f"     grupo {gname}: {len(kws)} kw + RSA ok")

    print("\nTUDO CRIADO PAUSADO — revisar em ads.google.com e ativar quando quiser.")

if __name__ == "__main__":
    main()
