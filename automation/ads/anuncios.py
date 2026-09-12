#!/usr/bin/env python3
"""
[Modulo Ads — leitura e edicao de texto dos anuncios responsivos]
@Author: Andre Gomes ( @acidcode )
@since 2026-09-12

POR QUE ISSO EXISTE: em 12/09/2026 os 9 anuncios da conta anunciavam "ate 31/08"
— prazo vencido havia 12 dias — enquanto o site vendia com prazo 30/09. Cinco
deles estavam NO AR. Prazo vencido no anuncio nao e so credibilidade: quem clica,
compara com a pagina e desiste, e a gente paga o clique do mesmo jeito.

Descobrir isso exigiu varrer os textos um a um. Por isso o --auditar existe: essa
varredura vira rotina, nao arqueologia.

PRESERVAR HISTORICO: RSA se edita com AdService.mutate_ads e FieldMask explicita.
Remover e recriar zera o aprendizado acumulado do anuncio (e o Indice de Qualidade
que veio junto) — nunca faca isso so pra trocar uma data.

ARMADILHA DO FIELD MASK: field_mask(None, pb) descarta valores default. Numa RSA,
descricao que nao mudou precisa ir junto na lista, senao o Google interpreta como
remocao. Por isso mandamos SEMPRE o array inteiro de descriptions/headlines.

LIMITES DO GOOGLE: titulo 30 chars, descricao 90. Estourar derruba a RSA inteira,
nao so o campo. O script confere antes de enviar.

Uso:
  python3 anuncios.py --auditar                      # datas vencidas e textos
  python3 anuncios.py --trocar "31/08" "30/09"       # troca em todos os anuncios
  python3 anuncios.py --trocar "31/08" "30/09" --dry-run
"""
import re
import sys
from datetime import date

sys.path.insert(0, "/data/coderush-sites/automation/ads")
from _conta import cliente, resolver  # noqa: E402

MAX_TITULO = 30
MAX_DESCRICAO = 90
# datas escritas por extenso ou em dd/mm que ja passaram viram alerta no --auditar
PADRAO_DATA = re.compile(r"\b(\d{2})/(\d{2})\b")
MESES = ("janeiro", "fevereiro", "marco", "março", "abril", "maio", "junho",
         "julho", "agosto", "setembro", "outubro", "novembro", "dezembro")


def anuncios(ga, cid, so_svd=True):
    filtro = "AND campaign.name LIKE 'SVD%'" if so_svd else ""
    q = f"""SELECT campaign.name, campaign.status, ad_group.name, ad_group_ad.ad.id,
            ad_group_ad.status, ad_group_ad.ad.responsive_search_ad.headlines,
            ad_group_ad.ad.responsive_search_ad.descriptions,
            ad_group_ad.ad.resource_name
            FROM ad_group_ad WHERE ad_group_ad.status != 'REMOVED' {filtro}"""
    return list(ga.search(customer_id=cid, query=q))


def data_vencida(texto):
    """dd/mm no passado, assumindo o ano corrente — e o formato que usamos em promo."""
    hoje = date.today()
    for d, m in PADRAO_DATA.findall(texto):
        try:
            quando = date(hoje.year, int(m), int(d))
        except ValueError:
            continue
        if quando < hoje:
            return f"{d}/{m}"
    baixo = texto.lower()
    for i, mes in enumerate(MESES, start=1):
        # normaliza marco/março pro mesmo numero
        num = i if i < 4 else i - 1 if mes == "março" else i
        if mes in baixo and num < hoje.month:
            return mes
    return None


def auditar(ga, cid):
    print("=== auditoria de texto dos anuncios ===")
    problemas = 0
    for r in anuncios(ga, cid):
        a = r.ad_group_ad.ad
        campos = ([("T", h.text, MAX_TITULO) for h in a.responsive_search_ad.headlines] +
                  [("D", d.text, MAX_DESCRICAO) for d in a.responsive_search_ad.descriptions])
        alertas = []
        for tipo, txt, limite in campos:
            venc = data_vencida(txt)
            if venc:
                alertas.append(f"{tipo} DATA VENCIDA ({venc}): {txt}")
            if len(txt) > limite:
                alertas.append(f"{tipo} ESTOURA {len(txt)}/{limite}: {txt}")
            # all-caps derruba a RSA inteira na revisao de politica
            for palavra in txt.split():
                if len(palavra) > 3 and palavra.isupper() and palavra.isalpha():
                    alertas.append(f"{tipo} CAIXA ALTA '{palavra}': {txt}")
        if alertas:
            problemas += 1
            no_ar = (r.campaign.status.name == "ENABLED"
                     and r.ad_group_ad.status.name == "ENABLED")
            print(f"\n  {'[NO AR] ' if no_ar else '[pausado] '}"
                  f"{r.campaign.name} / {r.ad_group.name} (ad {a.id})")
            for x in alertas:
                print(f"      {x}")
    print(f"\n  {problemas} anuncio(s) com problema" if problemas else "\n  tudo certo")


def trocar(cli, ga, cid, de, para, dry):
    print(f"=== trocando {de!r} -> {para!r} ===")
    ops = []
    for r in anuncios(ga, cid):
        a = r.ad_group_ad.ad
        rsa = a.responsive_search_ad
        tit = [h.text for h in rsa.headlines]
        des = [d.text for d in rsa.descriptions]
        novos_t = [t.replace(de, para) for t in tit]
        novos_d = [t.replace(de, para) for t in des]
        if novos_t == tit and novos_d == des:
            continue
        estouro = ([f"titulo {len(t)}/{MAX_TITULO}: {t}" for t in novos_t if len(t) > MAX_TITULO] +
                   [f"descricao {len(t)}/{MAX_DESCRICAO}: {t}" for t in novos_d if len(t) > MAX_DESCRICAO])
        if estouro:
            print(f"  [PULADO] ad {a.id} ({r.campaign.name}) — texto estoura o limite:")
            for e in estouro:
                print(f"      {e}")
            continue
        print(f"  [+] {r.campaign.name} / {r.ad_group.name} (ad {a.id})")
        for antes, depois in list(zip(tit, novos_t)) + list(zip(des, novos_d)):
            if antes != depois:
                print(f"      {antes}\n   -> {depois}")
        if dry:
            continue

        op = cli.get_type("AdOperation")
        novo = op.update
        novo.resource_name = a.resource_name
        # o array inteiro vai junto: mandar so o item alterado apaga os outros
        for texto, destino in ((novos_t, novo.responsive_search_ad.headlines),
                               (novos_d, novo.responsive_search_ad.descriptions)):
            for t in texto:
                asset = cli.get_type("AdTextAsset")
                asset.text = t
                destino.append(asset)
        # FieldMask explicita: o helper automatico descarta valores default
        from google.protobuf.field_mask_pb2 import FieldMask
        op.update_mask.CopyFrom(FieldMask(paths=[
            "responsive_search_ad.headlines", "responsive_search_ad.descriptions"]))
        ops.append(op)

    if dry:
        print("\n  (dry-run — nada foi enviado)")
        return
    if not ops:
        print("  nada a trocar")
        return
    res = cli.get_service("AdService").mutate_ads(customer_id=cid, operations=ops)
    print(f"\n  {len(res.results)} anuncio(s) atualizado(s) — historico preservado")


def main():
    cid = resolver(sys.argv)
    cli = cliente()
    ga = cli.get_service("GoogleAdsService")
    if "--trocar" in sys.argv:
        i = sys.argv.index("--trocar")
        try:
            de, para = sys.argv[i + 1], sys.argv[i + 2]
        except IndexError:
            sys.exit('uso: --trocar "de" "para"')
        trocar(cli, ga, cid, de, para, "--dry-run" in sys.argv)
    else:
        auditar(ga, cid)


if __name__ == "__main__":
    main()
