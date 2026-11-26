#!/usr/bin/env python3
"""
[Modulo Blog Bot — topo do post sem a capa]
@Author: Andre Gomes ( @acidcode )
@since 2026-11-26

POR QUE ISSO EXISTE: o topo do post era a capa com o titulo em HTML por cima.
Desde 05/10 a capa e tipografica (capa-local.py desenha o titulo NA imagem), entao
o leitor via a mesma frase duas vezes, uma atras da outra — e nas capas antigas
de IA as vezes com texto alucinado ("MUNTINIVEL"). O usuario pediu em 26/11:
"tira a imagem, deixa so o texto e poe um fundo para todas as paginas".

O topo passa a ser so texto sobre um fundo gerado por codigo (capa-local.py
--sem-texto, mesma semente do titulo). A capa continua existindo: e ela que vai
no og:image e nos cards do blog, onde o titulo dentro da imagem ajuda.

Idempotente: post que ja tem o topo novo e pulado. O publisher.js gera o mesmo
markup pros posts novos — mexeu aqui, mexe la.

Uso: python3 topo-sem-capa.py [--site-root=/data/coderush-sites/sistemavendadireta] [--paleta=...]
"""
import glob, html, os, re, subprocess, sys

ROOT = "/data/coderush-sites/sistemavendadireta"
PALETA = "#040c1e,#0b3a8a,#7cb8ff"
for a in sys.argv[1:]:
    if a.startswith("--site-root="): ROOT = a.split("=", 1)[1]
    if a.startswith("--paleta="): PALETA = a.split("=", 1)[1]

CAPA_LOCAL = os.path.join(os.path.dirname(os.path.abspath(__file__)), "capa-local.py")
FIG = re.compile(r'(?s)      <figure class="relative">\n.*?      </figure>\n')
H1 = re.compile(r'(?s)<h1[^>]*>(.*?)</h1>')
FIGCAP = re.compile(r'(?s)<figcaption[^>]*>\n(.*?)\n\s*</figcaption>')


def topo(rel, slug, miolo):
    return (
        f'      <header class="relative overflow-hidden" style="background:#040c1e url(\'{rel}imagens/posts/fundos/{slug}.webp\') center/cover no-repeat">\n'
        '        <div class="pointer-events-none absolute inset-0" style="background:linear-gradient(90deg,rgba(4,12,30,.55) 0%,rgba(4,12,30,.25) 65%,rgba(4,12,30,0) 100%)" aria-hidden="true"></div>\n'
        '        <div class="relative p-5 sm:p-8 lg:p-10" style="min-height:260px;display:flex;flex-direction:column;justify-content:flex-end">\n'
        f'{miolo}\n'
        '        </div>\n'
        '      </header>\n'
    )


def main():
    feitos = pulados = 0
    for f in sorted(glob.glob(os.path.join(ROOT, "20[0-9][0-9]/[01][0-9]/[0-3][0-9]/*/index.php"))):
        slug = os.path.basename(os.path.dirname(f))
        s = open(f, encoding="utf-8").read()
        m = FIG.search(s)
        if not m:
            pulados += 1
            continue
        cap = FIGCAP.search(m.group(0))
        titulo = html.unescape(re.sub(r"<[^>]+>", "", H1.search(m.group(0)).group(1))).strip()
        fundo = os.path.join(ROOT, "imagens/posts/fundos", f"{slug}.webp")
        os.makedirs(os.path.dirname(fundo), exist_ok=True)
        if not os.path.exists(fundo):
            r = subprocess.run([sys.executable, CAPA_LOCAL, f"--saida={fundo}", f"--titulo={titulo}",
                                f"--paleta={PALETA}", "--sem-texto"], capture_output=True, text=True)
            if r.returncode != 0:
                print(f"FALHOU fundo {slug}: {r.stderr.strip()}", file=sys.stderr)
                continue
        rel = "../../../../"
        s = s[:m.start()] + topo(rel, slug, cap.group(1)) + s[m.end():]
        open(f, "w", encoding="utf-8").write(s)
        feitos += 1
        print("ok", slug)
    print(f"{feitos} posts com topo novo, {pulados} ja estavam (ou sem capa no topo)")


if __name__ == "__main__":
    main()
