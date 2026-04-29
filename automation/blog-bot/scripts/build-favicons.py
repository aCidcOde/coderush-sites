#!/usr/bin/env python3
"""Gera favicon.svg + favicon-32.png + apple-touch-icon.png para cada site.
Estilo minimalista: cor da marca + inicial(s) brancas."""

import os
from pathlib import Path
from PIL import Image, ImageDraw, ImageFont

ROOT = Path(__file__).resolve().parents[3]

SITES = {
    "coderush": {
        "site_root": "",
        "bg": "#020b1a",
        "fg": "#60a5fa",
        "letter": "C",
    },
    "codafacil": {
        "site_root": "codafacil",
        "bg": "#04110d",
        "fg": "#8b5cf6",
        "letter": "Cf",
    },
    "fluxointeligenteia": {
        "site_root": "fluxointeligenteia",
        "bg": "#04110d",
        "fg": "#34d399",
        "letter": "F",
    },
    "sistemavendadireta": {
        "site_root": "sistemavendadireta",
        "bg": "#12356b",
        "fg": "#ffffff",
        "letter": "SVD",
    },
}

SVG_TPL = """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
  <rect width="64" height="64" rx="12" fill="{bg}"/>
  <text x="50%" y="50%" dominant-baseline="central" text-anchor="middle"
        font-family="-apple-system,Segoe UI,Roboto,Inter,Helvetica,Arial,sans-serif"
        font-weight="700" font-size="{font_size}" fill="{fg}">{letter}</text>
</svg>
"""

def font_size_for_letter(letter):
    return {1: 36, 2: 26, 3: 20}.get(len(letter), 18)


def find_font(size):
    candidates = [
        "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf",
        "/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf",
        "/usr/share/fonts/TTF/DejaVuSans-Bold.ttf",
    ]
    for c in candidates:
        if Path(c).exists():
            return ImageFont.truetype(c, size)
    return ImageFont.load_default()


def render_png(size, bg, fg, letter, out_path):
    img = Image.new("RGB", (size, size), bg)
    draw = ImageDraw.Draw(img)
    radius = int(size * 0.18)
    # rounded mask
    mask = Image.new("L", (size, size), 0)
    md = ImageDraw.Draw(mask)
    md.rounded_rectangle((0, 0, size, size), radius=radius, fill=255)
    canvas = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    canvas.paste(img, (0, 0), mask)
    base_size_factor = {1: 0.62, 2: 0.42, 3: 0.32}.get(len(letter), 0.28)
    font_pixel = max(8, int(size * base_size_factor))
    font = find_font(font_pixel)
    bbox = draw.textbbox((0, 0), letter, font=font)
    tw, th = bbox[2] - bbox[0], bbox[3] - bbox[1]
    pos = ((size - tw) / 2 - bbox[0], (size - th) / 2 - bbox[1])
    cd = ImageDraw.Draw(canvas)
    cd.text(pos, letter, fill=fg, font=font)
    canvas.save(out_path, "PNG", optimize=True)


def main():
    for site_id, cfg in SITES.items():
        site_dir = ROOT / cfg["site_root"] if cfg["site_root"] else ROOT
        if not site_dir.exists():
            print(f"skip {site_id}: dir nao existe ({site_dir})")
            continue

        svg = SVG_TPL.format(
            bg=cfg["bg"], fg=cfg["fg"],
            letter=cfg["letter"],
            font_size=font_size_for_letter(cfg["letter"])
        )
        (site_dir / "favicon.svg").write_text(svg, encoding="utf-8")
        render_png(32, cfg["bg"], cfg["fg"], cfg["letter"], site_dir / "favicon-32.png")
        render_png(180, cfg["bg"], cfg["fg"], cfg["letter"], site_dir / "apple-touch-icon.png")
        # favicon.ico (multi-size)
        ico_img = Image.open(site_dir / "favicon-32.png").convert("RGBA")
        ico_img.save(site_dir / "favicon.ico", sizes=[(16, 16), (32, 32)])
        print(f"ok {site_id} -> {site_dir}")


if __name__ == "__main__":
    main()
