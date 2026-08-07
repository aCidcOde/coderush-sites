#!/bin/bash
# [Modulo Leads SVD — watcher do botao "Atualizar dados" do painel]
# @Author: Andre Gomes ( @acidcode )
# @since 2026-08-05
# O painel roda no container e nao tem credencial do Google: ele so cria o
# arquivo-gatilho storage/ga-refresh.request. Este watcher (cron do host, 1/min)
# ve o gatilho, roda o snapshot da Data API e remove o pedido.
set -u
FLAG=/data/coderush-sites/sistemavendadireta/storage/ga-refresh.request
LOCK=/tmp/ga-refresh.lock

QUEUE=/data/coderush-sites/sistemavendadireta/storage/ga-events.queue

# nada a fazer?
[ -f "$FLAG" ] || [ -s "$QUEUE" ] || exit 0

# evita execucoes concorrentes com o cron de 3h
exec 9>"$LOCK"
flock -n 9 || exit 0

# 1) eventos de etapa do funil enfileirados pelo painel
if [ -s "$QUEUE" ]; then
  /usr/bin/python3 /data/coderush-sites/automation/leads/enviar-eventos.py
fi

# 2) snapshot do GA sob demanda
if [ -f "$FLAG" ]; then
  echo "[$(date -Is)] refresh solicitado pelo painel"
  /usr/bin/python3 /data/coderush-sites/automation/leads/ga-stats.py
  rm -f "$FLAG"
  echo "[$(date -Is)] refresh concluido"
fi
