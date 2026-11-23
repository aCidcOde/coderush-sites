#!/usr/bin/env bash
# [Modulo Ads — agenda da Promocao de Fim de Ano (dezembro/2026)]
# @Author: Andre Gomes ( @acidcode )
# @since 2026-11-23
#
# POR QUE ISSO EXISTE: a campanha ficou pausada de 04/11 a 30/11 pelo usuario
# ("volto em dezembro"). O combinado em 23/11 tem tres datas, e cada uma e uma
# coisa que alguem precisaria lembrar de fazer de madrugada:
#
#   01/12 00:05  ligar   — liga a campanha (so o nivel da campanha; grupos e
#                          anuncios ja estao ativos).
#   01/12 05:00  relatorio — religa o relatorio diario (comentado no crontab com
#                          "#PAUSA-ADS "). Separado do ligar de proposito: o
#                          relatorio roda 04:00 SP sobre o dia anterior, e no
#                          proprio 01/12 ele mandaria "nenhuma impressao" sobre
#                          30/11 — alarme falso no dia do lancamento.
#   24/12 00:05  reduzir — verba de R$ 20 pra R$ 10/dia: depois do Natal a busca
#                          de empresa cai, e a promocao ainda vale ate 31/12.
#   02/01 06:00  encerrar — a campanha ja parou sozinha (data de termino 31/12);
#                          comenta o relatorio de novo, senao ele manda
#                          "nenhuma impressao" todo dia ate a proxima campanha.
#
# Cada etapa manda um e-mail curto com o estado final lido da API — agenda que
# falha calada e pior que nao ter agenda (foi o "ate 31/08" que ficou 12 dias no ar).
#
# Uso:
#   agenda-fim-de-ano.sh ligar|relatorio|reduzir|encerrar
#   agenda-fim-de-ano.sh conferir   # so le o estado e imprime o e-mail, sem mudar nada
set -uo pipefail

ADS=/data/coderush-sites/automation/ads
PY=/usr/bin/python3
CAMP="SVD - Promocao Fim de Ano"
ETAPA="${1:-}"

estado() {
    "$PY" "$ADS/ligar-campanha.py" --status 2>&1 | grep -v -i warning | grep -E "CAMPANHA|$CAMP"
    "$PY" "$ADS/orcamento.py" --ver 2>&1 | grep -v -i warning | grep -E "CAMPANHA|$CAMP"
    echo
    echo "Relatorio diario no crontab:"
    crontab -l | grep "relatorio-diario.py" | grep -v "^# " | sed 's/^/  /'
}

avisar() {  # $1 assunto, $2 corpo
    local para
    para=$(sed -n 's/^MAIL_TO_ADDRESS=//p' /data/coderush-sites/.env | tr -d "\"'\r")
    if [ "$ETAPA" = "conferir" ] || [ -z "$para" ]; then
        printf -- '--- e-mail (nao enviado) ---\nAssunto: %s\n\n%s\n' "$1" "$2"
        return 0
    fi
    printf 'From: contato@coderush.com.br\nTo: %s\nSubject: %s\nContent-Type: text/plain; charset=UTF-8\n\n%s\n' \
        "$para" "$1" "$2" | /usr/bin/msmtp -a hostinger "$para"
}

case "$ETAPA" in
    ligar)
        saida=$("$PY" "$ADS/ligar-campanha.py" --campanha="$CAMP" --fim=2026-12-31 2>&1 | grep -v -i warning)
        assunto="[Ads SVD] Promocao de Fim de Ano ligada — primeiro relatorio diario chega em 02/12"
        ;;
    relatorio)
        crontab -l | sed 's/^#PAUSA-ADS //' | crontab -
        echo "$(date '+%d/%m/%Y %H:%M UTC') relatorio diario religado:"
        crontab -l | grep "relatorio-diario.py" | grep -v "^# "
        exit 0
        ;;
    reduzir)
        saida=$("$PY" "$ADS/orcamento.py" --campanha="$CAMP" --valor=10 2>&1 | grep -v -i warning)
        assunto="[Ads SVD] Fim de Ano: verba reduzida para R\$ 10/dia ate 31/12"
        ;;
    encerrar)
        crontab -l | sed 's|^\([^#].*relatorio-diario\.py.*\)$|#PAUSA-ADS \1|' | crontab -
        saida="Campanha encerrada pela data de termino (31/12). Relatorio diario comentado de novo com #PAUSA-ADS."
        assunto="[Ads SVD] Fim de Ano encerrada — relatorio diario pausado"
        ;;
    conferir)
        saida="(conferencia — nada foi alterado)"
        assunto="[Ads SVD] conferencia da agenda"
        ;;
    *)
        echo "uso: $0 ligar|relatorio|reduzir|encerrar|conferir" >&2
        exit 2
        ;;
esac

corpo="$(date '+%d/%m/%Y %H:%M UTC') — etapa: $ETAPA

$saida

ESTADO LIDO DA API DEPOIS DA ETAPA
$(estado)"
echo "$corpo"
avisar "$assunto" "$corpo"
