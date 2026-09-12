#!/usr/bin/env python3
"""
[Modulo Ads — credencial compartilhada e resolucao de conta]
@Author: Andre Gomes ( @acidcode )
@since 2026-09-12

POR QUE ISSO EXISTE: cada script de ads repetia o mesmo bloco de leitura do .env
e o CUSTOMER_ID cravado no topo do arquivo. Com mais de uma marca anunciando
(SVD e BFR), o ID cravado vira armadilha: rodar o script errado mexe na conta
errada sem avisar. Aqui a conta e escolhida por nome (--conta=bfr), nunca por
numero decorado.

O login_customer_id e o MCC: sem ele, chamada em conta filha devolve
USER_PERMISSION_DENIED mesmo com o token certo.
"""
ENV_PATH = "/data/coderush-sites/.env"
MCC = "3139585203"          # SVD (manager)
CONTAS = {
    "svd": "3578927161",    # SourceNET Tecnologia — onde roda o SVD hoje
}


def env():
    d = {}
    for line in open(ENV_PATH, encoding="utf-8"):
        line = line.strip()
        if line and not line.startswith("#") and "=" in line:
            k, v = line.split("=", 1)
            d[k.strip()] = v.strip().strip("\"'")
    return d


def cliente(login_mcc=True):
    e = env()
    from google.ads.googleads.client import GoogleAdsClient
    cfg = {
        "developer_token": e["GOOGLE_ADS_DEVELOPER_TOKEN"],
        "client_id": e["GOOGLE_ADS_CLIENT_ID"],
        "client_secret": e["GOOGLE_ADS_CLIENT_SECRET"],
        "refresh_token": e["GOOGLE_ADS_REFRESH_TOKEN"],
        "use_proto_plus": True,
    }
    if login_mcc:
        cfg["login_customer_id"] = MCC
    return GoogleAdsClient.load_from_dict(cfg)


def resolver(argv, padrao="svd"):
    """--conta=<apelido|id>. Devolve customer_id sem hifens."""
    alvo = padrao
    for a in argv:
        if a.startswith("--conta="):
            alvo = a.split("=", 1)[1]
    cid = CONTAS.get(alvo, alvo).replace("-", "")
    if not cid.isdigit():
        raise SystemExit(f"conta desconhecida: {alvo} (use {'/'.join(CONTAS)} ou o ID)")
    return cid
