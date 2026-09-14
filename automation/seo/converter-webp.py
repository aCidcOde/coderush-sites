#!/usr/bin/env python3
"""
[Modulo SEO — conversao de imagens pra WebP]
@Author: Andre Gomes ( @acidcode )
@since 2026-09-14

POR QUE ISSO EXISTE: metade dos logos de cliente ja tinha .webp gerado e o site
continuava servindo o .png — a conversao foi feita uma vez, na mao, e os clientes
adicionados depois (Accenti, AVIG 360, MedPlant, Zohr) entraram so em PNG. Ficou
um estado hibrido que ninguem percebe olhando a pagina, so pesando ela.

O peso importa aqui por dinheiro, nao por estetica: o Indice de Qualidade do
Google marca "experiencia da pagina" abaixo da media em quase toda palavra da
campanha, e velocidade entra nessa nota. Nota baixa = posicao pior pelo mesmo
lance, ou o mesmo lugar pagando mais caro.

REDIMENSIONAR TAMBEM: capa de post e gerada em 1200x630 e exibida com altura de
176px no card da home. Servir 1200px pra caber em 400 e mandar 4x mais bytes do
que a tela usa.

NAO APAGA O ORIGINAL: o <picture> serve WebP e cai no formato antigo se o
navegador nao suportar. Perder o fallback quebraria a pagina em navegador velho
pra economizar bytes que ele nem ia baixar.

Uso:
  python3 converter-webp.py --dir=sistemavendadireta/imagens/clientes --dry-run
  python3 converter-webp.py --dir=sistemavendadireta/imagens/clientes
  python3 converter-webp.py --dir=sistemavendadireta/imagens/posts --largura=900
"""
import os
import sys

from PIL import Image

QUALIDADE = 82          # acima disso o ganho de bytes some; abaixo aparece artefato
LARGURA_PADRAO = 0      # 0 = nao redimensiona
EXTENSOES = (".png", ".jpg", ".jpeg")


def arg(nome, padrao=None):
    for a in sys.argv[1:]:
        if a.startswith(f"--{nome}="):
            return a.split("=", 1)[1]
    return padrao


def converter(origem, largura, dry):
    destino = os.path.splitext(origem)[0] + ".webp"
    antes = os.path.getsize(origem)
    if os.path.exists(destino) and os.path.getmtime(destino) >= os.path.getmtime(origem):
        return None  # ja convertido e atualizado

    img = Image.open(origem)
    dim_antes = img.size
    if largura and img.width > largura:
        altura = round(img.height * largura / img.width)
        img = img.resize((largura, altura), Image.LANCZOS)

    # PNG com transparencia precisa de RGBA; JPEG vira RGB
    modo = "RGBA" if img.mode in ("RGBA", "LA", "P") and origem.lower().endswith(".png") else "RGB"
    if img.mode != modo:
        img = img.convert(modo)

    if dry:
        return (origem, destino, antes, None, dim_antes, img.size)
    img.save(destino, "WEBP", quality=QUALIDADE, method=6)
    depois = os.path.getsize(destino)
    # WebP nem sempre ganha: em PNG pequeno e ja otimizado ele sai MAIOR. Foi o
    # caso do logo da Haiflex — 2,6 KB de PNG viraram 3,3 KB de WebP. Manter o
    # arquivo faria o <picture> servir a versao pior de proposito, entao ele e
    # descartado e a pagina segue no original.
    if depois >= antes:
        os.remove(destino)
        return (origem, None, antes, depois, dim_antes, img.size)
    return (origem, destino, antes, depois, dim_antes, img.size)


def main():
    alvo = arg("dir")
    if not alvo or not os.path.isdir(alvo):
        sys.exit("informe --dir=<pasta com imagens>")
    largura = int(arg("largura", LARGURA_PADRAO))
    dry = "--dry-run" in sys.argv

    arquivos = sorted(f for f in os.listdir(alvo) if f.lower().endswith(EXTENSOES))
    if not arquivos:
        sys.exit(f"nenhuma imagem em {alvo}")

    total_antes = total_depois = 0
    feitos = descartados = 0
    for nome in arquivos:
        r = converter(os.path.join(alvo, nome), largura, dry)
        if r is None:
            continue
        origem, destino, antes, depois, da, dd = r
        dim = f"{da[0]}x{da[1]}" + (f" -> {dd[0]}x{dd[1]}" if da != dd else "")
        if depois is None:
            feitos += 1
            print(f"  [dry] {os.path.basename(origem):52} {antes/1024:7.1f} KB  {dim}")
        elif destino is None:
            # nao entra na conta de economia: nada foi convertido
            descartados += 1
            print(f"  [=] {os.path.basename(origem):40} WebP sairia MAIOR "
                  f"({antes/1024:.1f} -> {depois/1024:.1f} KB) — mantido o original")
        else:
            feitos += 1
            total_antes += antes
            total_depois += depois
            print(f"  [+] {os.path.basename(destino):52} {antes/1024:7.1f} -> "
                  f"{depois/1024:6.1f} KB  (-{(1 - depois/antes)*100:.0f}%)  {dim}")

    if not feitos and not descartados:
        print("  tudo ja convertido e atualizado")
        return
    if dry:
        print(f"\n  (dry-run — {feitos} arquivo(s) seriam convertidos)")
        return
    if feitos:
        print(f"\n  {feitos} convertido(s): {total_antes/1024:.0f} KB -> {total_depois/1024:.0f} KB "
              f"(-{(1 - total_depois/total_antes)*100:.0f}%)")
        print("  lembre de apontar o markup pro .webp — gerar sem referenciar nao economiza nada")
    if descartados:
        print(f"  {descartados} descartado(s) por nao compensar")


if __name__ == "__main__":
    main()
