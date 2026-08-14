#!/usr/bin/env python3
"""
[Modulo SVD — Search Console: escolher palavra-chave com dado, nao com palpite]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-14

O GA4 nao entrega termo de busca organica desde 2011 ("not provided"). Quem tem
esse dado e o Search Console: para cada consulta real, quantas vezes aparecemos,
quantos cliques e em QUE POSICAO estamos.

A leitura que importa pra midia paga cruza volume com posicao organica:

  COMPRAR   volume alto + posicao ruim (>10). O interesse existe, o organico nao
            entrega, e o Google ja nos considera relevantes o bastante pra exibir
            — isso costuma virar Indice de Qualidade bom e clique mais barato.
  AVALIAR   volume alto + posicao boa (<=5). Ja ganhamos de graca; pagar por isso
            e canibalizar o proprio clique. So vale se a concorrencia anunciar em
            cima e empurrar o organico pra baixo da dobra.
  IGNORAR   CTR pifio apesar de muita impressao — sinal classico de intencao
            errada: a pessoa busca outra coisa e o Google nos exibe por proximidade
            de tema. Candidata a negativa, nao a lance.

Uso:
  python3 search-console.py                 # analise completa, 90 dias
  python3 search-console.py --dias=28
  python3 search-console.py --comprar       # so as oportunidades, pra colar no Ads
"""
import datetime
import sys

SA_KEY = "/root/.config/svd-analytics/sa-key.json"
SITE = "sc-domain:sistemavendadireta.com.br"

# consultas comerciais falam de produto; as demais sao pesquisa sobre o mercado
SINAIS_COMERCIAIS = ("sistema", "software", "plataforma", "aplicativo", "crm",
                     "criar site", "criacao", "criação", "contratar", "preco",
                     "preço", "quanto custa", "aluguel", "alugar")
# marca de cliente nosso: ranqueia no nosso dominio mas nao e demanda por sistema
MARCAS_CLIENTES = ("medplant", "avig", "immunity", "professional", "forone",
                   "mdplant", "antioxidant", "sleep", "memory")


def arg(nome, padrao=None):
    for a in sys.argv[1:]:
        if a.startswith(f"--{nome}="):
            return a.split("=", 1)[1]
    return padrao


def consulta(sc, dims, ini, fim, limit=500):
    body = {"startDate": ini, "endDate": fim, "dimensions": dims,
            "rowLimit": limit, "type": "web"}
    return sc.searchanalytics().query(siteUrl=SITE, body=body).execute().get("rows", [])


def comercial(q):
    if any(m in q for m in MARCAS_CLIENTES):
        return False
    return any(s in q for s in SINAIS_COMERCIAIS)


def main():
    from googleapiclient.discovery import build
    from google.oauth2 import service_account
    cred = service_account.Credentials.from_service_account_file(
        SA_KEY, scopes=["https://www.googleapis.com/auth/webmasters.readonly"])
    sc = build("searchconsole", "v1", credentials=cred, cache_discovery=False)

    dias = int(arg("dias", "90"))
    fim = datetime.date.today()
    ini = fim - datetime.timedelta(days=dias)
    linhas = consulta(sc, ["query"], ini.isoformat(), fim.isoformat())
    if not linhas:
        sys.exit("sem dados no periodo")

    comprar, avaliar, ignorar, marca = [], [], [], []
    for r in linhas:
        q = r["keys"][0]
        item = (q, r["impressions"], r["clicks"], r["ctr"] * 100, r["position"])
        if any(m in q for m in MARCAS_CLIENTES):
            marca.append(item)
        elif not comercial(q):
            ignorar.append(item)
        elif r["position"] > 10:
            comprar.append(item)
        elif r["position"] <= 5:
            avaliar.append(item)
        else:
            comprar.append(item)

    def tabela(titulo, itens, nota=""):
        if not itens:
            return
        print(f"\n=== {titulo} ===")
        if nota:
            print(f"  {nota}")
        print(f"  {'CONSULTA':<42} {'IMPR':>5} {'CLIQ':>5} {'CTR':>7} {'POSICAO':>8}")
        for q, i, c, ctr, pos in sorted(itens, key=lambda x: -x[1]):
            print(f"  {q[:41]:<42} {i:>5.0f} {c:>5.0f} {ctr:>6.1f}% {pos:>8.1f}")

    if "--comprar" in sys.argv:
        print("Palavras pra levar ao Ads (uma por linha):")
        for q, i, c, ctr, pos in sorted(comprar, key=lambda x: -x[1]):
            print(f'"{q}"')
        return

    tot = consulta(sc, [], ini.isoformat(), fim.isoformat())
    if tot:
        t = tot[0]
        print(f"=== {dias} DIAS — TOTAL ORGANICO ===")
        print(f"  {t['impressions']:.0f} impressoes, {t['clicks']:.0f} cliques, "
              f"CTR {t['ctr']*100:.2f}%, posicao media {t['position']:.1f}")

    tabela("COMPRAR — demanda existe e o organico nao entrega", comprar,
           "posicao >5: pagar aqui cobre o que a busca natural nao alcanca")
    tabela("AVALIAR — ja ganhamos no organico", avaliar,
           "posicao <=5: anunciar canibaliza clique que ja vem de graca")
    tabela("IGNORAR / NEGATIVAR — intencao errada", ignorar,
           "sem palavra de produto na busca: pesquisa sobre o mercado, nao compra")
    tabela("MARCA DE CLIENTE — trafego dos subdominios", marca,
           "ranqueia no nosso dominio, mas nao e demanda por sistema")


if __name__ == "__main__":
    main()
