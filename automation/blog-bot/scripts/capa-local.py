#!/usr/bin/env python3
"""
[Modulo Blog Bot — capa gerada localmente, sem IA]
@Author: Andre Gomes ( @acidcode )
@since 2026-10-05

POR QUE ISSO EXISTE: entre 22/09 e 05/10/2026 o bot nao publicou uma linha. O saldo
do OpenRouter zerou e a geracao de imagem passou a responder 402. Mas o diagnostico
que importa veio depois: a capa de IA custava dinheiro, falhava, as vezes vazava
texto alucinado na arte — e nao e ela que faz a pessoa clicar. Quem faz e o titulo.

Entao a capa deixou de ser ilustracao e virou tipografia: fundo construido por
codigo, titulo grande e legivel, marca embaixo. Sem custo, sem rede, deterministico,
e o mesmo resultado em toda execucao.

O FUNDO NAO E CHAPADO E NAO E ALEATORIO. Sao quatro camadas sobre a paleta da
marca — gradiente diagonal, halos radiais, malha de rede e vinheta — com posicao
derivada do HASH DO TITULO. Dois posts diferentes sempre dao capas diferentes; o
mesmo post sempre da a mesma capa. Aleatorio de verdade quebraria idempotencia:
republicar o mesmo post geraria arte nova e sujaria o diff a cada execucao.

A malha de nos ligados nao e enfeite: o produto dos sites e rede de distribuidores
e agente conversando com sistema. O fundo fala disso sem desenhar pessoa nenhuma,
que e justamente a regra de arte do hub.

Uso:
  python3 capa-local.py --saida=/caminho/capa.jpg --titulo="..." \
      --eyebrow="..." --paleta="#0d1118,#0f172a,#d89b1a,#f3c65a" --marca="Emergency"

Sai 1200x630 (proporcao de og:image). Codigo 0 em sucesso; 1 e mensagem no stderr
em falha — quem chama decide se isso e fatal.
"""
import argparse
import hashlib
import math
import sys

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


def mistura(a, b, t):
    t = max(0.0, min(1.0, t))
    return tuple(int(a[i] + (b[i] - a[i]) * t) for i in range(3))


def legivel_sobre(fundo):
    """
    Texto claro ou escuro conforme a luminancia do fundo.

    Nem toda marca do hub e escura: a paleta do SVD tem branco, e cravar texto
    branco deixaria o titulo invisivel se a cor base mudar. Formula de luminancia
    relativa simplificada (ITU-R BT.601), que basta pra decidir entre dois polos.
    """
    lum = (fundo[0] * 299 + fundo[1] * 587 + fundo[2] * 114) / 1000
    return (17, 22, 30) if lum > 150 else (240, 244, 250)


def carregar_fonte(caminhos, tamanho):
    from PIL import ImageFont
    for c in caminhos:
        try:
            return ImageFont.truetype(c, tamanho)
        except OSError:
            continue
    return ImageFont.load_default()


def semente(texto):
    """Hash estavel do titulo — mesma entrada, mesma capa, sempre."""
    return int(hashlib.sha1((texto or "capa").encode("utf-8")).hexdigest()[:12], 16)


def fundo_gradiente(img, canto_a, canto_b):
    """
    Gradiente DIAGONAL, nao vertical.

    Vertical e o que todo gerador de capa faz e por isso parece template. A
    diagonal muda o eixo de leitura e combina melhor com o titulo alinhado a
    esquerda. Desenhado por linha (630 operacoes) em vez de pixel a pixel
    (756 mil) — a diferenca e de milissegundos para quase um segundo por capa.
    """
    from PIL import ImageDraw
    d = ImageDraw.Draw(img)
    diagonal = LARGURA + ALTURA
    for y in range(ALTURA):
        # cada linha recebe o gradiente deslocado pela propria altura
        t0 = y / diagonal
        t1 = (y + LARGURA) / diagonal
        d.line([(0, y), (LARGURA, y)], fill=mistura(canto_a, canto_b, (t0 + t1) / 2))


def halos(img, cor, rnd):
    """
    Dois halos radiais suaves, posicionados pelo hash.

    Sao desenhados num layer em escala reduzida e ampliados depois: circulo com
    alpha baixo em tamanho real vira borda serrilhada, e o upscale com BICUBIC
    dissolve isso de graca. Mais barato que aplicar GaussianBlur em 1200x630.
    """
    from PIL import Image, ImageDraw
    escala = 8
    lw, lh = LARGURA // escala, ALTURA // escala
    layer = Image.new("L", (lw, lh), 0)
    d = ImageDraw.Draw(layer)
    for i in range(2):
        cx = (rnd >> (i * 7 + 3)) % lw
        cy = (rnd >> (i * 5 + 11)) % lh
        raio = lh // 2 + ((rnd >> (i * 3)) % (lh // 3))
        for passo in range(10, 0, -1):
            r = raio * passo / 10
            d.ellipse([cx - r, cy - r, cx + r, cy + r], fill=int(70 * (1 - passo / 10)))
    layer = layer.resize((LARGURA, ALTURA), Image.BICUBIC)
    img.paste(Image.new("RGB", (LARGURA, ALTURA), cor), (0, 0), layer)


def malha(img, cor, rnd):
    """
    Nos ligados por linhas finas, concentrados no lado direito.

    Fica a direita de proposito: o texto ocupa a esquerda, e grafismo atras de
    titulo e ruido, nao design. A ligacao so e desenhada entre nos proximos —
    ligar todos com todos vira teia densa e suja a capa.
    """
    from PIL import Image, ImageDraw
    layer = Image.new("RGBA", (LARGURA, ALTURA), (0, 0, 0, 0))
    d = ImageDraw.Draw(layer)

    nos = []
    for i in range(14):
        x = int(LARGURA * 0.52) + ((rnd >> (i * 3 + 2)) % int(LARGURA * 0.46))
        y = 40 + ((rnd >> (i * 2 + 5)) % (ALTURA - 80))
        nos.append((x, y))

    limite = 240
    for i, (x1, y1) in enumerate(nos):
        for x2, y2 in nos[i + 1:]:
            dist = math.hypot(x2 - x1, y2 - y1)
            if dist < limite:
                alpha = int(46 * (1 - dist / limite))
                d.line([(x1, y1), (x2, y2)], fill=cor + (alpha,), width=1)
    for i, (x, y) in enumerate(nos):
        r = 3 + (i % 3)
        d.ellipse([x - r, y - r, x + r, y + r], fill=cor + (96,))

    img.paste(Image.alpha_composite(img.convert("RGBA"), layer).convert("RGB"), (0, 0))


def vinheta(img, escuro):
    """Escurece as bordas pra empurrar o olho pro centro-esquerda, onde esta o texto."""
    from PIL import Image, ImageDraw
    escala = 6
    lw, lh = LARGURA // escala, ALTURA // escala
    layer = Image.new("L", (lw, lh), 0)
    d = ImageDraw.Draw(layer)
    for passo in range(12):
        margem = passo * 2
        d.rectangle([margem, margem, lw - margem, lh - margem], outline=0, width=1)
        d.rectangle([-margem, -margem, lw + margem, lh + margem], outline=int(9 * (12 - passo) / 12), width=3)
    layer = layer.resize((LARGURA, ALTURA), Image.BICUBIC)
    img.paste(Image.new("RGB", (LARGURA, ALTURA), escuro), (0, 0), layer)


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

    rnd = semente(args.titulo)

    img = Image.new("RGB", (LARGURA, ALTURA), escuro)
    fundo_gradiente(img, meio, escuro)
    halos(img, mistura(meio, acento, 0.22), rnd)
    malha(img, acento, rnd)
    vinheta(img, escuro)

    d = ImageDraw.Draw(img)
    tinta = legivel_sobre(mistura(meio, escuro, 0.5))
    # sobre fundo escuro o acento ja contrasta; sobre fundo claro ele some,
    # entao o eyebrow cai pra mesma tinta do titulo
    tinta_acento = suave if tinta[0] > 128 else mistura(acento, (0, 0, 0), 0.25)

    # faixas de acento nos cantos opostos: dao assinatura visual sem competir
    # com o texto, e repetem em todo post do hub
    d.rectangle([(LARGURA - 190, 0), (LARGURA, 9)], fill=acento)
    d.rectangle([(0, ALTURA - 9), (260, ALTURA)], fill=acento)

    y = MARGEM

    if args.eyebrow:
        f = carregar_fonte(FONTES_BOLD, 23)
        d.text((MARGEM, y), args.eyebrow.upper()[:46], font=f, fill=tinta_acento)
        y += 44
        d.rectangle([(MARGEM, y), (MARGEM + 64, y + 4)], fill=acento)
        y += 36

    # o titulo manda no tamanho: desce a fonte ate caber em 4 linhas, em vez de
    # cortar com "..." — capa com titulo truncado e pior que capa com letra menor.
    # Largura limitada a 62% pra nao invadir a malha do lado direito.
    largura_max = int(LARGURA * 0.62)
    for tamanho in (58, 52, 46, 40, 34):
        f_titulo = carregar_fonte(FONTES_BOLD, tamanho)
        linhas = quebrar(args.titulo, f_titulo, d, largura_max)
        if len(linhas) <= 4:
            break
    else:
        linhas = linhas[:4]

    altura_linha = int(tamanho * 1.24)
    for linha in linhas:
        # sombra de 2px: garante leitura mesmo quando um halo claro passa atras
        d.text((MARGEM + 2, y + 2), linha, font=f_titulo, fill=(0, 0, 0) if tinta[0] > 128 else (6, 9, 14))
        d.text((MARGEM, y), linha, font=f_titulo, fill=tinta)
        y += altura_linha

    if args.marca:
        f = carregar_fonte(FONTES_REGULAR, 25)
        d.text((MARGEM, ALTURA - MARGEM - 8), args.marca, font=f, fill=tinta_acento)

    # quality=86: acima disso o arquivo cresce sem diferenca visivel, e a API do
    # emergency recusa cover acima de alguns MB
    img.save(args.saida, "JPEG", quality=86, optimize=True, progressive=True)
    print(args.saida)
    return 0


if __name__ == "__main__":
    sys.exit(main())
