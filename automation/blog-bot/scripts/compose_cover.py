#!/usr/bin/env python3

import argparse
import os

from PIL import Image, ImageDraw, ImageEnhance, ImageFilter, ImageFont, ImageOps


SIZE = (1200, 630)
PANEL_RECT = (54, 54, 560, 576)
HERO_RECT = (625, 78, 1134, 438)

FONT_CANDIDATES_BOLD = [
    "/System/Library/Fonts/Supplemental/Verdana Bold.ttf",
    "/System/Library/Fonts/Supplemental/Arial Bold.ttf",
    "/System/Library/Fonts/Supplemental/Tahoma Bold.ttf",
]

FONT_CANDIDATES_REGULAR = [
    "/System/Library/Fonts/Supplemental/Verdana.ttf",
    "/System/Library/Fonts/Supplemental/Arial.ttf",
    "/System/Library/Fonts/Supplemental/Tahoma.ttf",
]


def parse_args():
    parser = argparse.ArgumentParser()
    parser.add_argument("--source", required=True)
    parser.add_argument("--target", required=True)
    parser.add_argument("--title", required=True)
    parser.add_argument("--site-name", required=True)
    parser.add_argument("--label", required=True)
    parser.add_argument("--accent", required=True)
    parser.add_argument("--accent-soft", required=True)
    parser.add_argument("--background", required=True)
    parser.add_argument("--surface", required=True)
    parser.add_argument("--label-fill", required=True)
    parser.add_argument("--label-text", required=True)
    parser.add_argument("--variant", required=True)
    return parser.parse_args()


def hex_to_rgb(value):
    cleaned = value.strip().lstrip("#")
    return tuple(int(cleaned[index:index + 2], 16) for index in (0, 2, 4))


def rgba(value, alpha):
    return (*hex_to_rgb(value), alpha)


def load_font(size, bold=True):
    candidates = FONT_CANDIDATES_BOLD if bold else FONT_CANDIDATES_REGULAR
    for candidate in candidates:
        if os.path.exists(candidate):
            try:
                return ImageFont.truetype(candidate, size=size)
            except OSError:
                continue
    return ImageFont.load_default()


def fit_cover(image, size):
    return ImageOps.fit(image.convert("RGB"), size, method=Image.Resampling.LANCZOS, centering=(0.5, 0.5))


def rounded_mask(size, radius):
    mask = Image.new("L", size, 0)
    draw = ImageDraw.Draw(mask)
    draw.rounded_rectangle((0, 0, size[0], size[1]), radius=radius, fill=255)
    return mask


def paste_rounded(base, image, rect, radius):
    width = rect[2] - rect[0]
    height = rect[3] - rect[1]
    fitted = fit_cover(image, (width, height)).convert("RGBA")
    mask = rounded_mask((width, height), radius)
    base.paste(fitted, (rect[0], rect[1]), mask)


def text_width(draw, text, font):
    bbox = draw.textbbox((0, 0), text, font=font)
    return bbox[2] - bbox[0]


def wrap_text(draw, text, font, max_width):
    words = text.split()
    lines = []
    current = ""

    for word in words:
        candidate = f"{current} {word}".strip()
        if current and text_width(draw, candidate, font) > max_width:
            lines.append(current)
            current = word
        else:
            current = candidate

    if current:
        lines.append(current)

    return lines or [text]


def clamp_lines(draw, lines, font, max_width, limit):
    if len(lines) <= limit:
        return lines

    trimmed = lines[:limit]
    last = trimmed[-1]
    while last and text_width(draw, f"{last}...", font) > max_width:
        pieces = last.split()
        if len(pieces) <= 1:
            last = last[:-1].rstrip()
        else:
            last = " ".join(pieces[:-1]).strip()

    trimmed[-1] = f"{last}..."
    return trimmed


def resolve_title_layout(draw, title):
    max_width = PANEL_RECT[2] - PANEL_RECT[0] - 72
    max_height = 250

    for size in range(58, 35, -2):
        font = load_font(size, bold=True)
        lines = wrap_text(draw, title, font, max_width)
        lines = clamp_lines(draw, lines, font, max_width, 4)
        spacing = max(12, int(size * 0.2))
        bbox = draw.multiline_textbbox((0, 0), "\n".join(lines), font=font, spacing=spacing)
        height = bbox[3] - bbox[1]
        if height <= max_height:
            return font, lines, spacing

    font = load_font(34, bold=True)
    lines = clamp_lines(draw, wrap_text(draw, title, font, max_width), font, max_width, 4)
    return font, lines, 10


def add_glow(canvas, accent_hex, accent_soft_hex):
    glow = Image.new("RGBA", SIZE, (0, 0, 0, 0))
    draw = ImageDraw.Draw(glow)
    draw.ellipse((730, -30, 1170, 300), fill=rgba(accent_hex, 64))
    draw.ellipse((40, 390, 330, 680), fill=rgba(accent_soft_hex, 38))
    glow = glow.filter(ImageFilter.GaussianBlur(70))
    return Image.alpha_composite(canvas, glow)


def draw_shadow(canvas, rect, radius=34):
    shadow = Image.new("RGBA", SIZE, (0, 0, 0, 0))
    draw = ImageDraw.Draw(shadow)
    shadow_rect = (rect[0] + 10, rect[1] + 18, rect[2] + 12, rect[3] + 20)
    draw.rounded_rectangle(shadow_rect, radius=radius, fill=(0, 0, 0, 120))
    shadow = shadow.filter(ImageFilter.GaussianBlur(28))
    return Image.alpha_composite(canvas, shadow)


def compose(args):
    source = Image.open(args.source).convert("RGB")

    background = fit_cover(source, SIZE)
    background = ImageEnhance.Contrast(background).enhance(1.08)
    background = background.filter(ImageFilter.GaussianBlur(28))

    canvas = background.convert("RGBA")
    overlay_alpha = 138 if args.variant == "institutional" else 150
    canvas = Image.alpha_composite(canvas, Image.new("RGBA", SIZE, rgba(args.background, overlay_alpha)))
    canvas = add_glow(canvas, args.accent, args.accent_soft)
    if args.variant != "institutional":
        canvas = draw_shadow(canvas, HERO_RECT)

    draw = ImageDraw.Draw(canvas)

    panel_radius = 24 if args.variant == "institutional" else 34
    hero_radius = 24 if args.variant == "institutional" else 34
    panel_fill_alpha = 230 if args.variant == "institutional" else 208
    panel_outline = rgba(args.accent_soft, 72) if args.variant == "institutional" else (255, 255, 255, 38)
    draw.rounded_rectangle(
        PANEL_RECT,
        radius=panel_radius,
        fill=rgba(args.surface, panel_fill_alpha),
        outline=panel_outline,
        width=2
    )

    label_font = load_font(24, bold=True)
    label_rect = (88, 92, 330, 138)
    draw.rounded_rectangle(label_rect, radius=18, fill=rgba(args.label_fill, 235))
    label_y = label_rect[1] + 9
    draw.text((label_rect[0] + 18, label_y), args.label.upper(), fill=hex_to_rgb(args.label_text), font=label_font)

    meta_font = load_font(20, bold=False)
    meta_copy = "CAPA DO BLOG" if args.variant == "institutional" else "CAPA PADRONIZADA DO BLOG"
    draw.text((88, 166), meta_copy, fill=(255, 255, 255, 168), font=meta_font)

    title_font, title_lines, spacing = resolve_title_layout(draw, args.title)
    title_text = "\n".join(title_lines)
    draw.multiline_text((88, 214), title_text, fill=(255, 255, 255), font=title_font, spacing=spacing)

    accent_line_top = 214 + draw.multiline_textbbox((0, 0), title_text, font=title_font, spacing=spacing)[3] + 26
    draw.rounded_rectangle((88, accent_line_top, 248, accent_line_top + 6), radius=3, fill=rgba(args.accent, 255))

    site_font = load_font(22, bold=False)
    draw.text((88, accent_line_top + 26), args.site_name, fill=(255, 255, 255, 190), font=site_font)

    if args.variant == "institutional":
        draw.rounded_rectangle(
            (HERO_RECT[0] - 6, HERO_RECT[1] - 6, HERO_RECT[2] + 6, HERO_RECT[3] + 6),
            radius=hero_radius,
            fill=rgba(args.background, 140),
            outline=rgba(args.accent_soft, 120),
            width=2
        )
    else:
        canvas = draw_shadow(canvas, HERO_RECT)

    paste_rounded(canvas, source, HERO_RECT, radius=hero_radius)

    draw = ImageDraw.Draw(canvas)
    draw.rounded_rectangle(HERO_RECT, radius=hero_radius, outline=rgba(args.accent, 255), width=4)
    draw.rounded_rectangle(
        (HERO_RECT[0] + 8, HERO_RECT[1] + 8, HERO_RECT[2] - 8, HERO_RECT[3] - 8),
        radius=max(8, hero_radius - 6),
        outline=(255, 255, 255, 92),
        width=2
    )

    badge_rect = (HERO_RECT[0] + 22, HERO_RECT[3] - 70, HERO_RECT[0] + 180, HERO_RECT[3] - 24)
    badge_fill = rgba(args.background, 230 if args.variant == "institutional" else 212)
    draw.rounded_rectangle(badge_rect, radius=16, fill=badge_fill, outline=rgba(args.accent, 220), width=2)
    badge_font = load_font(22, bold=True)
    draw.text((badge_rect[0] + 18, badge_rect[1] + 10), "DESTAQUE", fill=hex_to_rgb(args.accent), font=badge_font)

    outer = (16, 16, SIZE[0] - 16, SIZE[1] - 16)
    inner = (28, 28, SIZE[0] - 28, SIZE[1] - 28)
    outer_radius = 18 if args.variant == "institutional" else 28
    inner_radius = 14 if args.variant == "institutional" else 22
    outer_width = 5 if args.variant == "institutional" else 6
    draw.rounded_rectangle(outer, radius=outer_radius, outline=rgba(args.accent, 255), width=outer_width)
    draw.rounded_rectangle(inner, radius=inner_radius, outline=(255, 255, 255, 66), width=1)

    target_dir = os.path.dirname(args.target)
    if target_dir:
        os.makedirs(target_dir, exist_ok=True)

    canvas.convert("RGB").save(args.target, format="JPEG", quality=94, subsampling=0)


def main():
    compose(parse_args())


if __name__ == "__main__":
    main()
