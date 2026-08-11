#!/usr/bin/env python3
"""
[Modulo Ads SVD — saneamento da conta antes de ligar a campanha]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-11

Duas correcoes que precisam existir ANTES de qualquer campanha veicular:

1. Acoes de conversao. As tres importadas do GA4 (generate_lead, whatsapp_click,
   purchase) chegaram com status HIDDEN e primary_for_goal=False — nesse estado o
   Google nao conta conversao nenhuma, e a campanha roda cega. Alem disso, as acoes
   marcadas como principais eram herdadas de outra conta (oficina mecanica), o que
   sujava a coluna "Conversoes" com evento que nunca vai disparar no SVD.

   Politica adotada:
     - generate_lead  -> ENABLED + principal   (e o que otimizamos)
     - whatsapp_click -> ENABLED + secundaria  (sinal, nao meta)
     - purchase       -> ENABLED + secundaria  (carrega o valor; vira principal
                         quando houver volume pra lance inteligente)
     - acoes da oficina -> rebaixadas a secundaria (nao removidas: o historico fica)

2. Campanha antiga "Sistema Venda Direta". Estava com Rede de Display ligada e
   queimou 84% da verba de 10/08 em clique acidental, mandando todo mundo pra home.
   Os anuncios dela ja estavam removidos; a campanha vira REMOVED (no Google isso
   preserva o historico de metricas, nao apaga dado).

Uso:
  python3 preparar-lancamento.py --dry-run   # mostra o que mudaria
  python3 preparar-lancamento.py             # aplica
"""
import sys

from google.protobuf import field_mask_pb2

CUSTOMER_ID = "3578927161"
ENV_PATH = "/data/coderush-sites/.env"

# nome -> (ativar?, principal?)
POLITICA_GA4 = {
    "generate_lead": (True, True),
    "whatsapp_click": (True, False),
    "purchase": (True, False),
}
# herdadas da conta da oficina: nunca disparam no SVD, so poluem a coluna
REBAIXAR = {"Obter rota", "Numero de Telefone Mecânica", "Numero de Telefone Mecanica"}

CAMPANHA_ANTIGA = 12683979737  # "Sistema Venda Direta"


def carregar_env(path=ENV_PATH):
    env = {}
    for line in open(path, encoding="utf-8"):
        line = line.strip()
        if line and not line.startswith("#") and "=" in line:
            k, v = line.split("=", 1)
            env[k.strip()] = v.strip().strip("\"'")
    return env


def cliente():
    env = carregar_env()
    from google.ads.googleads.client import GoogleAdsClient
    return GoogleAdsClient.load_from_dict({
        "developer_token": env["GOOGLE_ADS_DEVELOPER_TOKEN"],
        "client_id": env["GOOGLE_ADS_CLIENT_ID"],
        "client_secret": env["GOOGLE_ADS_CLIENT_SECRET"],
        "refresh_token": env["GOOGLE_ADS_REFRESH_TOKEN"],
        "use_proto_plus": True})


def main():
    dry = "--dry-run" in sys.argv
    cli = cliente()
    ga = cli.get_service("GoogleAdsService")
    conv_svc = cli.get_service("ConversionActionService")
    camp_svc = cli.get_service("CampaignService")

    # ---------------------------------------------------- 1. conversoes
    acoes = list(ga.search(customer_id=CUSTOMER_ID, query="""
        SELECT conversion_action.resource_name, conversion_action.name,
               conversion_action.status, conversion_action.primary_for_goal,
               conversion_action.type, conversion_action.category
        FROM conversion_action WHERE conversion_action.status != 'REMOVED'"""))

    ops = []
    print("=== ACOES DE CONVERSAO ===")
    for r in acoes:
        a = r.conversion_action
        alvo = None
        for sufixo, (ativar, principal) in POLITICA_GA4.items():
            if a.name.endswith(sufixo):
                alvo = (ativar, principal)
                break
        if alvo is None and a.name in REBAIXAR and a.primary_for_goal:
            alvo = (a.status.name == "ENABLED", False)
        if alvo is None:
            print(f"  [ ] {a.name} — mantido ({a.status.name.lower()}, "
                  f"{'principal' if a.primary_for_goal else 'secundaria'})")
            continue

        ativar, principal = alvo
        novo_status = "ENABLED" if ativar else a.status.name
        if a.status.name == novo_status and a.primary_for_goal == principal:
            print(f"  [=] {a.name} — ja esta correto")
            continue

        print(f"  [~] {a.name}: {a.status.name.lower()}/"
              f"{'principal' if a.primary_for_goal else 'secundaria'}"
              f"  ->  {novo_status.lower()}/{'principal' if principal else 'secundaria'}")
        if dry:
            continue
        op = cli.get_type("ConversionActionOperation")
        up = op.update
        up.resource_name = a.resource_name
        up.status = cli.enums.ConversionActionStatusEnum.ENABLED
        up.primary_for_goal = principal
        cli.copy_from(op.update_mask, field_mask_pb2.FieldMask(paths=["status", "primary_for_goal"]))
        ops.append(op)

    if ops:
        try:
            res = conv_svc.mutate_conversion_actions(customer_id=CUSTOMER_ID, operations=ops)
            print(f"  -> {len(res.results)} acoes atualizadas")
        except Exception as e:
            print("  [!] falhou:", str(e).split("message:")[-1][:200])

    # ------------------------------------------- 1b. metas por categoria
    # primary_for_goal nao e mais editavel por acao: quem decide o que entra na
    # coluna "Conversoes" e a meta da CATEGORIA, no nivel da conta. Herdamos
    # GET_DIRECTIONS ligada da conta da oficina — o SVD nao tem loja fisica, entao
    # "obter rota" nunca deveria contar. PHONE_CALL_LEAD fica: o plano de midia
    # preve extensao de chamada e ligacao e lead de verdade pra gente.
    DESLIGAR_CATEGORIAS = {"GET_DIRECTIONS"}
    goal_svc = cli.get_service("CustomerConversionGoalService")
    metas = list(ga.search(customer_id=CUSTOMER_ID, query="""
        SELECT customer_conversion_goal.resource_name, customer_conversion_goal.category,
               customer_conversion_goal.origin, customer_conversion_goal.biddable
        FROM customer_conversion_goal"""))
    print("\n=== METAS POR CATEGORIA ===")
    gops = []
    for r in metas:
        g = r.customer_conversion_goal
        if not g.biddable:
            continue
        if g.category.name in DESLIGAR_CATEGORIAS:
            print(f"  [~] {g.category.name} ({g.origin.name}) -> nao conta mais como conversao")
            if not dry:
                op = cli.get_type("CustomerConversionGoalOperation")
                op.update.resource_name = g.resource_name
                op.update.biddable = False
                cli.copy_from(op.update_mask, field_mask_pb2.FieldMask(paths=["biddable"]))
                gops.append(op)
        else:
            print(f"  [ ] {g.category.name} ({g.origin.name}) — mantida")
    if gops:
        try:
            goal_svc.mutate_customer_conversion_goals(customer_id=CUSTOMER_ID, operations=gops)
            print(f"  -> {len(gops)} meta(s) ajustada(s)")
        except Exception as e:
            print("  [!] falhou:", str(e).split("message:")[-1][:200])

    # ---------------------------------------------------- 2. campanha antiga
    print("\n=== CAMPANHA ANTIGA ===")
    antiga = list(ga.search(customer_id=CUSTOMER_ID, query=f"""
        SELECT campaign.resource_name, campaign.name, campaign.status
        FROM campaign WHERE campaign.id = {CAMPANHA_ANTIGA}"""))
    if not antiga:
        print("  ja nao existe")
    else:
        c = antiga[0].campaign
        if c.status.name == "REMOVED":
            print(f"  [=] '{c.name}' ja esta removida")
        else:
            print(f"  [~] '{c.name}' [{c.status.name.lower()}] -> removida"
                  f" (historico de metricas e preservado)")
            if not dry:
                # REMOVED nao pode ser gravado via update: a API exige a operacao
                # 'remove' explicita (status e read-only pra esse valor).
                op = cli.get_type("CampaignOperation")
                op.remove = c.resource_name
                try:
                    camp_svc.mutate_campaigns(customer_id=CUSTOMER_ID, operations=[op])
                    print("  -> removida")
                except Exception as e:
                    print("  [!] falhou:", str(e).split("message:")[-1][:200])

    if dry:
        print("\n(dry-run — nada foi alterado)")


if __name__ == "__main__":
    main()
