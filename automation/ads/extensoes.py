#!/usr/bin/env python3
"""
[Modulo Ads SVD — extensoes (assets) da campanha]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-12

POR QUE: no primeiro dia de veiculacao a campanha pegou apenas 10% dos leiloes
elegiveis e perdeu **90% por ranking** (nao por orcamento). Ad Rank = lance x
Indice de Qualidade + impacto das extensoes. Sem nenhuma extensao vinculada,
estavamos entrando no leilao com a mao amarrada — extensao nao custa clique a
mais e e a alavanca mais barata de ranking que existe.

Sitelinks, frases de destaque e telefone ja estavam previstos no plano de midia
(docs/campanha-google-ads-oferta.md); so nunca tinham sido criados via API.

Uso:
  python3 extensoes.py --dry-run
  python3 extensoes.py --campanha="SVD - Promocao 10 Anos"
"""
import sys

CUSTOMER_ID = "3578927161"
ENV_PATH = "/data/coderush-sites/.env"
BASE = "https://sistemavendadireta.com.br"
UTM = "utm_source=google&utm_medium=cpc&utm_campaign=promo-10-anos"

# A demonstracao e a arma que faltava: concorrente oferece "teste gratis 14 dias"
# COM cadastro; a nossa e um ambiente inteiro (loja, escritorio do parceiro e
# admin) com credenciais publicadas, sem cadastro nenhum. Estava linkada em
# lugar nenhum — nem no site, nem no anuncio.
DEMO = "https://zohr.sistemavendadireta.com.br/primeiros-passos"

SITELINKS = [
    ("Ver demonstracao", "Sistema real, sem cadastro", "Loja, escritorio e admin", f"{DEMO}?{UTM}&utm_content=sitelink-demo"),
    ("Cases reais", "Quem ja opera com o SVD", "Brasil, Paraguai e Bolivia", f"{BASE}/cases/?{UTM}&utm_content=sitelink-cases"),
    ("Simular comissoes", "Veja o custo por consultor", "Simulador em 30 segundos", f"{BASE}/oferta/?{UTM}&utm_content=sitelink-simulador#simulador"),
    ("Planos e mensalidade", "A partir de R$ 500/mes", "Sob medida pro seu porte", f"{BASE}/oferta/?{UTM}&utm_content=sitelink-planos#garantir"),
    ("Falar com especialista", "Tire duvidas sem compromisso", "Resposta no mesmo dia", f"{BASE}/oferta/?{UTM}&utm_content=sitelink-contato#garantir"),
]
# maximo 25 caracteres cada
DESTAQUES = ["10 anos de mercado", "Clientes em 5 paises", "Mensalidade desde R$ 500",
             "Implantacao com IA", "Migramos seu sistema", "Suporte em portugues"]
TELEFONE = "11994566726"


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
    dry = "--dry-run" in sys.argv
    nome = arg("campanha", "SVD - Promocao 10 Anos")

    for t, d1, d2, _ in SITELINKS:
        assert len(t) <= 25, f"sitelink >25: {t}"
        assert len(d1) <= 35 and len(d2) <= 35, f"descricao >35: {t}"
    for d in DESTAQUES:
        assert len(d) <= 25, f"destaque >25: {d}"

    if dry:
        print(f"[{nome}] {len(SITELINKS)} sitelinks, {len(DESTAQUES)} frases de destaque, 1 telefone")
        for t, d1, d2, u in SITELINKS:
            print(f"   sitelink '{t}' -> {u}")
        print("   destaques:", " · ".join(DESTAQUES))
        print(f"   telefone: {TELEFONE}")
        return

    cli = cliente()
    ga = cli.get_service("GoogleAdsService")
    asset_svc = cli.get_service("AssetService")
    camp_asset_svc = cli.get_service("CampaignAssetService")

    camp = None
    for r in ga.search(customer_id=CUSTOMER_ID, query=f"""
            SELECT campaign.resource_name FROM campaign
            WHERE campaign.name = '{nome}' AND campaign.status != 'REMOVED'"""):
        camp = r.campaign.resource_name
    if not camp:
        sys.exit(f"campanha '{nome}' nao encontrada")

    # idempotencia: nao duplica extensao ja vinculada
    ja = set()
    for r in ga.search(customer_id=CUSTOMER_ID, query=f"""
            SELECT campaign.resource_name, campaign_asset.field_type,
                   asset.sitelink_asset.link_text, asset.callout_asset.callout_text,
                   asset.call_asset.phone_number
            FROM campaign_asset WHERE campaign.resource_name = '{camp}'
              AND campaign_asset.status != 'REMOVED'"""):
        a = r.asset
        ja.add(a.sitelink_asset.link_text or a.callout_asset.callout_text
               or a.call_asset.phone_number)

    novos = []   # (field_type, operacao de asset)
    for texto, d1, d2, url in SITELINKS:
        if texto in ja:
            print(f"  [=] sitelink '{texto}' ja existe")
            continue
        op = cli.get_type("AssetOperation")
        s = op.create.sitelink_asset
        s.link_text = texto
        s.description1 = d1
        s.description2 = d2
        op.create.final_urls.append(url)
        novos.append(("SITELINK", op, texto))

    for texto in DESTAQUES:
        if texto in ja:
            print(f"  [=] destaque '{texto}' ja existe")
            continue
        op = cli.get_type("AssetOperation")
        op.create.callout_asset.callout_text = texto
        novos.append(("CALLOUT", op, texto))

    if TELEFONE not in ja:
        op = cli.get_type("AssetOperation")
        op.create.call_asset.country_code = "BR"
        op.create.call_asset.phone_number = TELEFONE
        novos.append(("CALL", op, TELEFONE))
    else:
        print("  [=] telefone ja existe")

    if not novos:
        print("nada a criar — extensoes ja estao no ar")
        return

    res = asset_svc.mutate_assets(customer_id=CUSTOMER_ID,
                                  operations=[o for _, o, _ in novos])
    print(f"  {len(res.results)} asset(s) criado(s)")

    vinculos = []
    for (tipo, _, rotulo), r in zip(novos, res.results):
        op = cli.get_type("CampaignAssetOperation")
        op.create.campaign = camp
        op.create.asset = r.resource_name
        op.create.field_type = getattr(cli.enums.AssetFieldTypeEnum, tipo)
        vinculos.append(op)
        print(f"     + {tipo.lower()}: {rotulo}")
    camp_asset_svc.mutate_campaign_assets(customer_id=CUSTOMER_ID, operations=vinculos)
    print(f"  {len(vinculos)} extensao(oes) vinculada(s) a '{nome}'")


if __name__ == "__main__":
    main()
