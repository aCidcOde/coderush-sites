#!/usr/bin/env python3
"""
[Modulo Blog Bot — capa de emergencia, gerada localmente]
@Author: Andre Gomes ( @acidcode )
@since 2026-10-05

POR QUE ISSO EXISTE: entre 22/09 e 05/10/2026 o bot nao publicou uma linha. O
saldo do OpenRouter zerou e a geracao de imagem passou a responder 402 ("requires
at least $1.00 in balance"). Os sites file-based (SVD, BFR) sobreviveram porque o
publisher.js cai numa capa fallback do proprio site; o destino de API (emergency)
nao tinha fallback nenhum, entao a excecao da capa derrubava o post inteiro.

A pergunta certa nao e "como gerar arte sem IA" — e "o que fazer quando a arte
nao vem". Um post sem capa nao entra (a API exige multipart com cover). Um post
com a MESMA capa de sempre entra, mas vira ruido visual no indice do blog.

Esta e a terceira via: capa tipografica, na paleta da marca, com o titulo do
proprio post. Determinista, sem custo, sem risco de texto vazado (o texto e
escolhido por nos, nao alucinado), e visualmente distinta post a post porque o
titulo muda. Nao substitui a capa de IA — e o que entra no lugar dela quando ela
falha, pra que a falha custe qualidade de imagem e nao um ciclo de publicacao.

Uso:
  python3 capa-local.py --saida=/caminho/capa.jpg --titulo="..." \
      --eyebrow="..." --paleta="#0d1118,#0f172a,#d89b1a,#f3c65a" --marca="Emergency"

Sai 1200x630 (proporcao de og:image). Codigo 0 em sucesso; 1 e mensagem no stderr
em falha — quem chama decide se isso e fatal.
"""
import argparse
import sys
import textwrap

LARGURA, ALTURA = 1200, 630
MARGEM = 72

# Liberation Sans esta no runner do GitHub Actions e nesta maquina; DejaVu e a
# rede de seguranca. Montserrat (a fonte das marcas) nao esta instalada em
# nenhum dos dois, e instalar fonte no CI por causa de um fallback nao se paga.
FONTES_BOLD = [
    "/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf",
    "/usr/share/fonts/truetype/liberation2/LiberationSans-Bold.ttf",
    "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf",
]
FONTES_REGULAR = [
    "/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf",
    "/usr/share/fonts/truetype/liberation2/LiberationSans-Regular.ttf",
    "/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf",
]


def hex_rgb(valor, padrao=(13, 17, 24)):
    v = (valor or "").strip().lstrip("#")
    if len(v) != 6:
        return padrao
    try:
        return tuple(int(v[i:i + 2], 16) for i in (0, 2, 4))
    except ValueError:
        return padrao


def carregar_fonte(caminhos, tamanho):
    from PIL import ImageFont
    for c in caminhos:
        try:
            return ImageFont.truetype(c, tamanho)
        except OSError:
            continue
    return ImageFont.load_default()


def gradiente(img, topo, base):
    """Degrade vertical. Pixel a pixel seria lento em 1200x630; desenha linhas."""
    from PIL import ImageDraw
    d = ImageDraw.Draw(img)
    for y in range(ALTURA):
        t = y / max(1, ALTURA - 1)
        d.line(
            [(0, y), (LARGURA, y)],
            fill=tuple(int(topo[i] + (base[i] - topo[i]) * t) for i in range(3)),
        )


def quebrar(texto, fonte, desenho, largura_max):
    """
    Quebra por medicao real, nao por contagem de caracteres: "MMMM" e "iiii"
    tem o mesmo len() e larguras muito diferentes, e titulo estourando a margem
    e pior que titulo com uma linha a mais.
    """
    palavras = (texto or "").split()
    linhas, atual = [], ""
    for p in palavras:
        teste = f"{atual} {p}".strip()
        if desenho.textlength(teste, font=fonte) <= largura_max or not atual:
            atual = teste
        else:
            linhas.append(atual)
            atual = p
    if atual:
        linhas.append(atual)
    return linhas


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--saida", required=True)
    ap.add_argument("--titulo", required=True)
    ap.add_argument("--eyebrow", default="")
    ap.add_argument("--marca", default="")
    ap.add_argument("--paleta", default="#0d1118,#0f172a,#d89b1a,#f3c65a")
    args = ap.parse_args()

    try:
        from PIL import Image, ImageDraw
    except ImportError:
        print("Pillow ausente — instale python3-pil", file=sys.stderr)
        return 1

    cores = [c for c in args.paleta.split(",") if c.strip()]
    escuro = hex_rgb(cores[0] if cores else "", (13, 17, 24))
    meio = hex_rgb(cores[1] if len(cores) > 1 else "", (15, 23, 42))
    acento = hex_rgb(cores[2] if len(cores) > 2 else "", (216, 155, 26))
    suave = hex_rgb(cores[3] if len(cores) > 3 else "", (243, 198, 90))

    img = Image.new("RGB", (LARGURA, ALTURA), escuro)
    gradiente(img, meio, escuro)
    d = ImageDraw.Draw(img)

    # bloco de acento no canto, pra capa nao parecer slide de PowerPoint
    d.rectangle([(LARGURA - 190, 0), (LARGURA, 10)], fill=acento)
    d.rectangle([(0, ALTURA - 10), (260, ALTURA)], fill=acento)

    y = MARGEM

    if args.eyebrow:
        f = carregar_fonte(FONTES_BOLD, 24)
        texto = args.eyebrow.upper()[:46]
        d.text((MARGEM, y), texto, font=f, fill=suave)
        y += 46
        d.rectangle([(MARGEM, y), (MARGEM + 64, y + 4)], fill=acento)
        y += 34

    # o titulo manda no tamanho: desce a fonte ate caber em 4 linhas, em vez de
    # cortar com "..." — capa com titulo truncado e pior que capa com letra menor
    largura_max = LARGURA - (MARGEM * 2)
    for tamanho in (58, 52, 46, 40, 34):
        f_titulo = carregar_fonte(FONTES_BOLD, tamanho)
        linhas = quebrar(args.titulo, f_titulo, d, largura_max)
        if len(linhas) <= 4:
            break
    else:
        linhas = linhas[:4]

    altura_linha = int(tamanho * 1.25)
    for linha in linhas:
        d.text((MARGEM, y), linha, font=f_titulo, fill=(238, 242, 248))
        y += altura_linha

    if args.marca:
        f = carregar_fonte(FONTES_REGULAR, 26)
        d.text((MARGEM, ALTURA - MARGEM - 10), args.marca, font=f, fill=suave)

    # quality=86: acima disso o arquivo cresce sem diferenca visivel, e a API do
    # emergency recusa cover acima de alguns MB
    img.save(args.saida, "JPEG", quality=86, optimize=True, progressive=True)
    print(args.saida)
    return 0


if __name__ == "__main__":
    sys.exit(main())
