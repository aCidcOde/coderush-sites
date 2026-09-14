#!/usr/bin/env bash
# [Modulo Git — diagnostico de peso de repositorio]
# @Author: Andre Gomes ( @acidcode )
# @since 2026-09-14
#
# POR QUE ISSO EXISTE: os repositorios dos clientes estavam entre 42 e 190 MB no
# Bitbucket, e a intuicao ("deve ser imagem") estava errada. No avig360 os
# arquivos RASTREADOS somam 40 MB e o .git tem 98 MB — o peso e historico, nao
# conteudo atual. Apagar arquivo hoje nao devolve espaco nenhum: o blob continua
# no historico pra sempre.
#
# Este script separa as tres perguntas que costumam ser confundidas:
#   1. o que ocupa espaco AGORA (working tree)
#   2. o que esta RASTREADO (o que vai pro servidor a cada clone)
#   3. o que esta no HISTORICO (o que faz o .git inchar, mesmo ja apagado)
#
# Uso:
#   ./analisar-peso.sh /data/avig360
#   ./analisar-peso.sh /data/avig360 /data/forone /data/emergency

set -uo pipefail

analisar() {
  local repo="$1"
  [ -d "$repo/.git" ] || { echo "  $repo: nao e repositorio git"; return; }
  cd "$repo" || return

  local tree git_sz commits tracked
  tree=$(du -sh --exclude=.git . 2>/dev/null | cut -f1)
  git_sz=$(du -sm .git 2>/dev/null | cut -f1)
  commits=$(git rev-list --all --count 2>/dev/null || echo "?")
  tracked=$(git ls-files -z 2>/dev/null | xargs -0 -r du -cm 2>/dev/null | tail -1 | cut -f1)

  echo ""
  echo "=============================================================="
  echo " $(basename "$repo")"
  echo "=============================================================="
  printf "  working tree .... %s\n  rastreado ....... %s MB\n  .git ............ %s MB\n  commits ......... %s\n" \
    "$tree" "${tracked:-?}" "$git_sz" "$commits"

  # Se .git e muito maior que o rastreado, o problema e historico — e ai
  # gitignore sozinho nao resolve, precisa reescrever.
  if [ -n "${tracked:-}" ] && [ "$tracked" -gt 0 ]; then
    local ratio=$(( git_sz * 100 / tracked ))
    if [ "$ratio" -gt 150 ]; then
      echo "  >> .git e ${ratio}% do rastreado: peso esta no HISTORICO"
    fi
  fi

  echo ""
  echo "  --- rastreado hoje, por pasta de topo ---"
  git ls-files 2>/dev/null | awk -F/ '{print $1}' | sort -u | while read -r d; do
    [ -e "$d" ] || continue
    local s
    s=$(git ls-files -z "$d" 2>/dev/null | xargs -0 -r du -ck 2>/dev/null | tail -1 | cut -f1)
    [ -n "$s" ] && [ "$s" -gt 512 ] && printf "    %8.1f MB  %s\n" "$(echo "$s/1024" | bc -l)" "$d"
  done | sort -rn | head -8

  echo ""
  echo "  --- suspeitos classicos (nunca deveriam estar versionados) ---"
  local achou=0
  for padrao in "*.phar:ferramenta de dev empacotada" \
                "vendor/*:dependencia do composer" \
                "node_modules/*:dependencia do npm" \
                "*.psd:fonte do Photoshop" \
                "*.sql:dump de banco" \
                "*.zip:pacote" \
                "*.log:log" \
                "*.mp4:video" \
                ".env:credencial"; do
    local glob="${padrao%%:*}" desc="${padrao##*:}"
    local n bytes
    n=$(git ls-files -- "$glob" 2>/dev/null | wc -l)
    [ "$n" -eq 0 ] && continue
    bytes=$(git ls-files -z -- "$glob" 2>/dev/null | xargs -0 -r du -ck 2>/dev/null | tail -1 | cut -f1)
    printf "    %8.1f MB  %-22s %s arquivo(s) — %s\n" "$(echo "${bytes:-0}/1024" | bc -l)" "$glob" "$n" "$desc"
    achou=1
  done
  [ "$achou" -eq 0 ] && echo "    (nenhum)"

  echo ""
  echo "  --- maiores blobs do HISTORICO (o que incha o .git) ---"
  git rev-list --objects --all 2>/dev/null \
    | git cat-file --batch-check='%(objecttype) %(objectname) %(objectsize) %(rest)' 2>/dev/null \
    | awk '$1=="blob" && $4!="" {print $3, $4}' | sort -rn | head -10 \
    | while read -r s f; do
        local estado="apagado"
        git cat-file -e "HEAD:$f" 2>/dev/null && estado="no HEAD"
        printf "    %8.2f MB  [%-7s] %s\n" "$(echo "$s/1048576" | bc -l)" "$estado" "$f"
      done
}

[ $# -eq 0 ] && { echo "uso: $0 <repo> [repo...]"; exit 1; }
for r in "$@"; do analisar "$r"; done
