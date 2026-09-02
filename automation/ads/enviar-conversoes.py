#!/usr/bin/env python3
"""
[Modulo Ads SVD — upload de conversao offline direto pro Google Ads]
@Author: Andre Gomes ( @acidcode )
@since 2026-09-02

POR QUE ISSO EXISTE: a venda da VELARO (R$ 3.500, 26/08) foi enviada pelo
Measurement Protocol do GA4 e NUNCA chegou ao Ads, mesmo com a acao de conversao
ativa e o evento visivel no relatorio do GA4.

A causa e arquitetural, nao de configuracao. O Measurement Protocol amarra o
evento a um client_id. Aquele lead veio de WebView e nao tinha ga_client_id,
entao o envio usou o sintetico "offline.6" — que nao corresponde a nenhuma sessao
real. O GA4 aceita e registra, mas o evento fica orfao: sem sessao, sem gclid, e
o Ads nao tem como ligar aquilo a um clique de anuncio.

O caminho correto pra venda offline e enviar o GCLID direto pro Ads — sem
depender de sessao, cookie ou JavaScript.

ATENCAO (02/09/2026): o Google FECHOU o ConversionUploadService pra contas novas.
Tentar usar devolve "New integrations for uploading click conversions should use
the Data Manager API". Entao o envio automatico por aqui nao funciona nesta conta;
o script serve pra criar a acao de conversao e listar o que esta pendente.

O upload em si sai pelo CSV do painel (aba Funil -> "Exportar conversoes"), em
Ads -> Metas -> Uploads. O nome no CSV precisa casar EXATAMENTE com o nome da
acao criada aqui, senao o Google recusa a linha.

Migrar pra Data Manager API resolve o automatico — vale quando o volume de vendas
justificar; com 1 venda por semana, o CSV manual custa menos.

Uso:
  python3 enviar-conversoes.py --listar          # o que ha pra enviar
  python3 enviar-conversoes.py --criar-acao      # cria a acao de upload
  python3 enviar-conversoes.py --dry-run
  python3 enviar-conversoes.py
"""
import sqlite3
import sys
from datetime import datetime, timezone, timedelta

CUSTOMER_ID = "3578927161"
ENV_PATH = "/data/coderush-sites/.env"
DB = "/data/coderush-sites/sistemavendadireta/storage/leads.sqlite"
ACAO_NOME = "Venda fechada (offline)"
# marca no proprio banco o que ja subiu, pra reexecucao nao duplicar
TABELA_LOG = "conversoes_enviadas"


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


def banco():
    con = sqlite3.connect(DB)
    con.row_factory = sqlite3.Row
    con.execute(f"""CREATE TABLE IF NOT EXISTS {TABELA_LOG} (
        lead_id INTEGER PRIMARY KEY, enviado_em TEXT, valor REAL, resposta TEXT)""")
    return con


def achar_acao(cli, ga):
    q = f"""SELECT conversion_action.resource_name, conversion_action.name,
            conversion_action.status FROM conversion_action
            WHERE conversion_action.name = '{ACAO_NOME}'
              AND conversion_action.status != 'REMOVED'"""
    for r in ga.search(customer_id=CUSTOMER_ID, query=q):
        return r.conversion_action.resource_name
    return None


def criar_acao(cli):
    """Acao do tipo UPLOAD_CLICKS: a unica que aceita gclid vindo de fora."""
    op = cli.get_type("ConversionActionOperation")
    a = op.create
    a.name = ACAO_NOME
    a.type_ = cli.enums.ConversionActionTypeEnum.UPLOAD_CLICKS
    a.category = cli.enums.ConversionActionCategoryEnum.PURCHASE
    a.status = cli.enums.ConversionActionStatusEnum.ENABLED
    # venda de instalacao acontece uma vez por clique; contar varias inflaria
    a.counting_type = cli.enums.ConversionActionCountingTypeEnum.ONE_PER_CLICK
    # ciclo daqui e curto (a VELARO fechou em 4h30), mas 90 dias cobre o lead
    # que demora a decidir sem perder a atribuicao
    a.click_through_lookback_window_days = 90
    a.value_settings.always_use_default_value = False
    res = cli.get_service("ConversionActionService").mutate_conversion_actions(
        customer_id=CUSTOMER_ID, operations=[op])
    return res.results[0].resource_name


def pendentes(con):
    return con.execute(f"""
        SELECT l.id, l.nome, l.gclid, l.close_value, l.closed_at
        FROM leads l LEFT JOIN {TABELA_LOG} e ON e.lead_id = l.id
        WHERE l.status = 'fechado' AND l.gclid IS NOT NULL AND l.gclid != ''
          AND l.close_value > 0 AND e.lead_id IS NULL
        ORDER BY l.id""").fetchall()


def main():
    cli = cliente()
    ga = cli.get_service("GoogleAdsService")
    con = banco()

    if "--criar-acao" in sys.argv:
        atual = achar_acao(cli, ga)
        if atual:
            print(f"  acao ja existe: {atual}")
        else:
            print(f"  criada: {criar_acao(cli)}")
        return

    linhas = pendentes(con)
    if "--listar" in sys.argv or not linhas:
        print(f"=== {len(linhas)} venda(s) pendente(s) de envio ===")
        for r in linhas:
            print(f"  #{r['id']} {r['nome']} — R$ {r['close_value']:.2f} "
                  f"em {r['closed_at'][:16]} | gclid {r['gclid'][:24]}...")
        if not linhas:
            print("  (nada a enviar: precisa de status=fechado, gclid e valor)")
        return

    acao = achar_acao(cli, ga)
    if not acao:
        sys.exit(f"acao '{ACAO_NOME}' nao existe — rode --criar-acao primeiro")

    dry = "--dry-run" in sys.argv
    ops = []
    for r in linhas:
        # o Ads exige data com fuso explicito; closed_at ja vem em ISO com offset
        quando = datetime.fromisoformat(r["closed_at"]).astimezone(
            timezone(timedelta(hours=-3))).strftime("%Y-%m-%d %H:%M:%S-03:00")
        print(f"  [+] #{r['id']} {r['nome']}: R$ {r['close_value']:.2f} em {quando}")
        if dry:
            continue
        cc = cli.get_type("ClickConversion")
        cc.gclid = r["gclid"]
        cc.conversion_action = acao
        cc.conversion_date_time = quando
        cc.conversion_value = float(r["close_value"])
        cc.currency_code = "BRL"
        cc.order_id = f"svd-{r['id']}"
        ops.append((r["id"], float(r["close_value"]), cc))

    if dry:
        print("\n(dry-run — nada foi enviado)")
        return

    svc = cli.get_service("ConversionUploadService")
    resp = svc.upload_click_conversions(
        customer_id=CUSTOMER_ID,
        conversions=[cc for _, _, cc in ops],
        partial_failure=True)  # um erro nao derruba os outros

    erro = resp.partial_failure_error
    if erro and erro.message:
        print(f"\n  [!] {erro.message}")
        for d in erro.details:
            print(f"      {str(d)[:200]}")

    agora = datetime.now(timezone(timedelta(hours=-3))).isoformat(timespec="seconds")
    ok = 0
    for (lead_id, valor, _), res in zip(ops, resp.results):
        sucesso = bool(res.gclid or res.conversion_date_time)
        if sucesso:
            ok += 1
            con.execute(f"INSERT OR REPLACE INTO {TABELA_LOG} VALUES (?,?,?,?)",
                        (lead_id, agora, valor, "ok"))
    con.commit()
    print(f"\n  {ok}/{len(ops)} conversao(oes) aceita(s) pelo Google")
    if ok:
        print("  aparece no Ads em ate 3 horas, na coluna 'Todas as conversoes'")


if __name__ == "__main__":
    main()
