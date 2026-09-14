#!/usr/bin/env bash
# [Modulo Git — remove lixo do historico e encolhe o repositorio]
# @Author: Andre Gomes ( @acidcode )
# @since 2026-09-14
#
# POR QUE ISSO EXISTE: os repositorios de cliente estavam entre 42 e 190 MB no
# Bitbucket. A intuicao era "deve ser imagem"; o dado disse outra coisa. No
# emergency, os arquivos rastreados somam 39 MB e o .git tem 331 MB — 848%. O
# peso e HISTORICO: blob que entrou uma vez fica pra sempre, mesmo depois de o
# arquivo ser apagado. Por isso .gitignore sozinho nao resolve nada retroativo.
#
# O QUE O DADO MOSTROU (medido em copia, nao estimado):
#   emergency  328 MB -> 79 MB  (-76%)   dumps .sql de 105 MB no historico
#   avig360     94 MB -> 71 MB  (-24%)   22 MB de .phar
#   forone      94 MB -> 80 MB  (-15%)   .phar + 25 .psd + nohup.out de 6,5 MB
#
# NAO E SO ESPACO — O EMERGENCY TEM DADO DE CLIENTE NO HISTORICO:
# documentacao/emergency_2018-05-08-18.sql.zip (68 MB) e limpeza_dados_emergency.sql
# (37 MB) foram apagados do HEAD, mas seguem recuperaveis por qualquer pessoa com
# acesso de clone. O dump referencia colunas de nascimento e RG. Isso e exposicao
# de dado pessoal, nao peso de arquivo.
#
# O QUE NAO DA PRA TIRAR: vendor/. Nestes repos nao existe composer.json na raiz
# — as bibliotecas foram copiadas a mao pra not_public/class/, cada uma com seu
# vendor, e o deploy nao roda composer. Remover quebraria a aplicacao, porque nao
# ha nada que restaure. Sao 9 MB em 19.653 arquivos; resolver isso e reorganizar
# dependencia, nao limpar git.
#
# ============================ LEIA ANTES DE RODAR ============================
# Reescrever historico TROCA O HASH DE TODOS OS COMMITS. Consequencias reais:
#
#   1. exige push forcado; quem tiver clone antigo e der push devolve todo o
#      lixo pro servidor. TODO MUNDO precisa reclonar depois — nao adianta pull.
#   2. link pra commit especifico (Jira, PR, changelog) para de funcionar.
#   3. o servidor (Bitbucket) so libera o espaco depois de rodar o GC dele, o
#      que pode levar horas ou exigir abrir chamado.
#
# Por isso o script SEMPRE opera numa copia espelho, nunca no seu diretorio de
# trabalho, e nunca empurra sozinho. O push e um passo manual, seu.
# =============================================================================
#
# Uso:
#   ./limpar-repo.sh /data/emergency              # analisa e limpa a copia
#   ./limpar-repo.sh /data/emergency --com-fontes # tambem tira fontes CJK do mpdf

set -euo pipefail

REPO="${1:-}"
[ -z "$REPO" ] && { echo "uso: $0 <caminho-do-repo> [--com-fontes]"; exit 1; }
[ -d "$REPO/.git" ] || { echo "erro: $REPO nao e repositorio git"; exit 1; }

NOME=$(basename "$REPO")
TRAB="${TMPDIR:-/tmp}/limpeza-git/$NOME"
ESPELHO="$TRAB/$NOME.git"
COM_FONTES=0
[ "${2:-}" = "--com-fontes" ] && COM_FONTES=1

command -v git-filter-repo >/dev/null || {
  echo "erro: git-filter-repo ausente. Instale com: pip3 install git-filter-repo"; exit 1; }

rm -rf "$TRAB"; mkdir -p "$TRAB"

echo "=== 1. espelho (o repositorio original nao e tocado) ==="
git clone --mirror "$REPO" "$ESPELHO" -q
ANTES=$(du -sm "$ESPELHO" | cut -f1)
echo "    $ESPELHO — $ANTES MB"

REGRAS="$TRAB/remover.txt"
# Regra por TAMANHO foi descartada: --strip-blobs-bigger-than 1M apagaria
# cat_2.png, NoovCalm_Art_01.jpg e a webfont do tema — arquivos de 1 a 2 MB que o
# site usa de verdade. Aqui so entra o que foi conferido caso a caso.
cat > "$REGRAS" <<'EOF'
glob:*.phar
glob:*.psd
glob:*.exe
glob:*.log
glob:*nohup.out
glob:*.mp4
glob:*.dmp
glob:*.bak
glob:*.BAK
glob:*dist/summernote-*.zip
EOF

# ATENCAO AO .sql: o primeiro rascunho usava glob:*.sql e teria apagado
# database/migrations/*.sql do emergency — que sao MIGRACOES, parte do codigo, nao
# dump. Aqui removemos so os dumps grandes, escolhidos pelo tamanho do blob no
# historico, e o caminho e listado antes de sumir.
#
# Tamanho tambem nao basta como criterio: migrar_rede_binario.sql tem 584 KB e e
# o script da migracao de rede binaria do avig360 — documentacao de uma operacao
# real, nao backup. Caminho que cheire a migracao fica, independente do tamanho.
LIMITE_SQL=$((512 * 1024))
PROTEGIDOS='migration|migrations|migrar|migracao|snippets|seed|schema|estrutura'
cd "$ESPELHO"
git rev-list --objects --all 2>/dev/null \
  | git cat-file --batch-check='%(objecttype) %(objectname) %(objectsize) %(rest)' 2>/dev/null \
  | awk -v lim="$LIMITE_SQL" '$1=="blob" && $3>lim && $4 ~ /\.(sql|sql\.zip|sql\.gz)$/ {print $4}' \
  | { grep -viE "$PROTEGIDOS" || true; } \
  | sort -u > "$TRAB/dumps.txt"
# o || true acima nao e enfeite: com set -e + pipefail, um grep que nao acha nada
# devolve 1 e mata o script. Foi o que aconteceu no avig360 — o unico .sql grande
# era justamente o protegido, o filtro esvaziou, e a limpeza inteira nao rodou
# sem dizer uma palavra. Falha silenciosa e pior que erro.
if [ -s "$TRAB/dumps.txt" ]; then
  echo ""
  echo "    dumps de banco a remover (>512 KB; migracoes ficam):"
  sed 's/^/      /' "$TRAB/dumps.txt"
  sed 's/^/literal:/' "$TRAB/dumps.txt" >> "$REGRAS"
fi

if [ "$COM_FONTES" -eq 1 ]; then
  # Fontes CJK que o mpdf empacota. Num sistema brasileiro nao sao usadas —
  # conferido: zero referencia fora do proprio mpdf. Se algum dia precisar gerar
  # PDF em chines/japones/coreano, elas voltam pelo pacote do mpdf.
  cat >> "$REGRAS" <<'EOF'
glob:*/mpdf/ttfonts/Sun-Ext*
glob:*/mpdf/ttfonts/UnBatang*
glob:*/mpdf/ttfonts/Aegyptus*
glob:*/mpdf/ttfonts/Quivira*
glob:*/mpdf/ttfonts/ZawgyiOne*
glob:*/mpdf/ttfonts/Ayar*
EOF
fi

echo ""
echo "=== 2. o que sai (maiores, do historico inteiro) ==="
# O preview usa AS MESMAS regras do filtro. Uma versao anterior listava por
# extensao e mostrava migrar_rede_binario.sql como se fosse sair, quando ele e
# protegido — preview que mente sobre o que uma ferramenta destrutiva vai fazer e
# pior que nao ter preview.
cd "$ESPELHO"
PREV_RE=$(grep '^glob:' "$REGRAS" | sed 's|^glob:||; s|\.|\\.|g; s|\*|.*|g' | paste -sd'|')
git rev-list --objects --all 2>/dev/null \
  | git cat-file --batch-check='%(objecttype) %(objectname) %(objectsize) %(rest)' 2>/dev/null \
  | awk '$1=="blob" && $4!=""{print $3, $4}' \
  | { grep -iE "(${PREV_RE})$" || true; } \
  | sort -rn | head -12 \
  | awk '{printf "    %7.2f MB  %s\n", $1/1048576, $2}'
if [ -s "$TRAB/dumps.txt" ]; then
  while read -r d; do
    sz=$(git rev-list --objects --all | grep -F " $d" | head -1 | cut -d' ' -f1 \
         | xargs -r git cat-file -s 2>/dev/null || echo 0)
    printf "    %7.2f MB  %s\n" "$(echo "${sz:-0}/1048576" | bc -l)" "$d"
  done < "$TRAB/dumps.txt"
fi

echo ""
echo "=== 3. reescrevendo ==="
git filter-repo --force --invert-paths --paths-from-file "$REGRAS" 2>&1 | tail -2
git reflog expire --expire=now --all 2>/dev/null || true
git gc --prune=now --aggressive -q 2>/dev/null || true

DEPOIS=$(du -sm "$ESPELHO" | cut -f1)
echo ""
echo "=============================================================="
printf "  %s: %s MB -> %s MB  (-%.0f%%)\n" "$NOME" "$ANTES" "$DEPOIS" \
  "$(echo "(1-$DEPOIS/$ANTES)*100" | bc -l)"
echo "=============================================================="
cat <<EOF

  A copia limpa esta em: $ESPELHO
  O repositorio original em $REPO NAO foi alterado.

  Para publicar (passo manual, e irreversivel no servidor):

    cd $ESPELHO
    git remote add origin <URL-do-bitbucket>
    git push --force --mirror origin

  ANTES de empurrar:
    - avise quem trabalha no repo: depois do push, TODOS precisam RECLONAR.
      Clone antigo que der push devolve o lixo inteiro pro servidor.
    - guarde um backup do estado atual (o proprio Bitbucket serve, ou um
      git clone --mirror guardado fora).

  DEPOIS de empurrar:
    - o Bitbucket so libera o espaco apos o GC dele; pode demorar.
    - commite o .gitignore (automation/git/gitignore-sistemas) no repo, senao o
      lixo volta no proximo commit e a limpeza foi so um ciclo.
EOF
