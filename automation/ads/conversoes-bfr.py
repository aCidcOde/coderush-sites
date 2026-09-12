#!/usr/bin/env python3
"""
[Modulo Ads BFR — acoes de conversao e snippet da tag]
@Author: Andre Gomes ( @acidcode )
@since 2026-09-12

POR QUE ISSO EXISTE: a BFR entrou em midia paga sem nenhuma acao de conversao na
conta. Campanha sem conversao medida nao e campanha ruim — e campanha invisivel:
da pra saber quanto gastou e nada sobre o que voltou.

POR QUE NAO IMPORTAR DO GA4, como fizemos no SVD: importar exige vincular a
propriedade GA4 a conta de Ads pelo painel, na mao. E o SVD ja mostrou o limite
desse caminho — a conversao de compra enviada por Measurement Protocol nunca
chegou ao Ads, porque o evento ficava orfao, amarrado a um client_id sintetico
sem sessao nem gclid. Tag propria do Ads dispara no navegador com o gclid vivo,
que e o que o Ads precisa pra atribuir.

CONTA COMPARTILHADA — LEIA ANTES DE MEXER: a 3578927161 hospeda SVD, Elibell e
kernelpanic. Acao de conversao e recurso de CONTA, nao de campanha: criar aqui
aparece no relatorio de todo mundo. Hoje isso e tolerado porque nenhuma campanha
usa lance por conversao (todas estao em TARGET_IMPRESSION_SHARE), entao misturar
nao envenena nenhum algoritmo — so exige segmentar por nome na leitura. No dia em
que alguma campanha migrar pra Maximize Conversions, isto vira problema real e a
BFR precisa de conta propria.

Uso:
  python3 conversoes-bfr.py --criar
  python3 conversoes-bfr.py --snippet    # imprime o AW-xxx e os labels pro site
"""
import sys

sys.path.insert(0, "/data/coderush-sites/automation/ads")
from _conta import cliente, resolver  # noqa: E402

# prefixo no nome e o que permite filtrar BFR do resto numa conta compartilhada
ACOES = [
    ("BFR - Clique WhatsApp", "SUBMIT_LEAD_FORM", 30,
     "caminho principal: no SVD as duas vendas fechadas vieram do zap"),
    ("BFR - Formulario de contato", "SUBMIT_LEAD_FORM", 30,
     "o form ja existia; agora com atribuicao"),
]


def existentes(ga, cid):
    q = """SELECT conversion_action.resource_name, conversion_action.name,
           conversion_action.status, conversion_action.id
           FROM conversion_action WHERE conversion_action.status != 'REMOVED'"""
    return {r.conversion_action.name: r.conversion_action
            for r in ga.search(customer_id=cid, query=q)}


def criar(cli, ga, cid):
    ja = existentes(ga, cid)
    ops = []
    for nome, categoria, janela, motivo in ACOES:
        if nome in ja:
            print(f"  [=] ja existe: {nome}")
            continue
        print(f"  [+] {nome} — {motivo}")
        op = cli.get_type("ConversionActionOperation")
        a = op.create
        a.name = nome
        a.type_ = cli.enums.ConversionActionTypeEnum.WEBPAGE
        a.category = getattr(cli.enums.ConversionActionCategoryEnum, categoria)
        a.status = cli.enums.ConversionActionStatusEnum.ENABLED
        # lead e por clique: a mesma pessoa clicando 3x no zap e 1 lead, nao 3
        a.counting_type = cli.enums.ConversionActionCountingTypeEnum.ONE_PER_CLICK
        a.click_through_lookback_window_days = janela
        a.value_settings.always_use_default_value = False
        ops.append(op)
    if not ops:
        return
    res = cli.get_service("ConversionActionService").mutate_conversion_actions(
        customer_id=cid, operations=ops)
    print(f"\n  {len(res.results)} acao(oes) criada(s)")


def snippet(ga, cid):
    nomes = "','".join(n for n, _, _, _ in ACOES)
    q = f"""SELECT conversion_action.name, conversion_action.id,
            conversion_action.status, conversion_action.tag_snippets
            FROM conversion_action WHERE conversion_action.name IN ('{nomes}')
            AND conversion_action.status != 'REMOVED'"""
    achou = False
    for r in ga.search(customer_id=cid, query=q):
        c = r.conversion_action
        achou = True
        print(f"\n=== {c.name} (id {c.id}, {c.status.name}) ===")
        for s in c.tag_snippets:
            # o event_snippet traz o send_to com AW-<id>/<label>, que e o que o
            # site precisa; o global_site_tag e o mesmo pra todas as acoes
            if s.event_snippet:
                import re
                m = re.search(r"'send_to':\s*'([^']+)'", s.event_snippet)
                print(f"  formato {s.type_.name}: send_to = {m.group(1) if m else '?'}")
    if not achou:
        print("  nenhuma acao encontrada — rode --criar primeiro")
        return
    q2 = f"""SELECT conversion_action.tag_snippets FROM conversion_action
             WHERE conversion_action.name IN ('{nomes}')
             AND conversion_action.status != 'REMOVED' LIMIT 1"""
    for r in ga.search(customer_id=cid, query=q2):
        for s in r.conversion_action.tag_snippets:
            if s.global_site_tag:
                import re
                m = re.search(r"AW-\d+", s.global_site_tag)
                if m:
                    print(f"\n  ID de conversao da conta: {m.group(0)}")
                    return


def main():
    cid = resolver(sys.argv)
    cli = cliente()
    ga = cli.get_service("GoogleAdsService")
    if "--criar" in sys.argv:
        criar(cli, ga, cid)
        snippet(ga, cid)
    else:
        snippet(ga, cid)


if __name__ == "__main__":
    main()
