const path = require("node:path");
const { execFileSync } = require("node:child_process");

const COVER_STYLES = {
  coderush: {
    accent: "#60a5fa",
    accentSoft: "#a78bfa",
    background: "#020b1a",
    surface: "#0f172a",
    labelFill: "#0b4db6",
    labelText: "#f8fbff",
    variant: "tech"
  },
  codafacil: {
    accent: "#0b4db6",
    accentSoft: "#8b5cf6",
    background: "#04110d",
    surface: "#11211d",
    labelFill: "#f8fbff",
    labelText: "#0b4db6",
    variant: "tech"
  },
  fluxointeligenteia: {
    accent: "#34d399",
    accentSoft: "#38bdf8",
    background: "#04110d",
    surface: "#0d1f1a",
    labelFill: "#05261e",
    labelText: "#d1fae5",
    variant: "tech"
  },
  sistemavendadireta: {
    accent: "#004aad",
    accentSoft: "#bfdafe",
    background: "#12356b",
    surface: "#1c4687",
    labelFill: "#ffffff",
    labelText: "#004aad",
    variant: "institutional"
  }
};

function resolveCoverStyle(siteId) {
  return (
    COVER_STYLES[siteId] || {
      accent: "#58b8ff",
      accentSoft: "#a78bfa",
      background: "#08111f",
      surface: "#111c2d",
      labelFill: "#58b8ff",
      labelText: "#08111f",
      variant: "tech"
    }
  );
}

function composeCoverImage({ root, site, blogName, sourcePath, targetPath, title }) {
  const style = resolveCoverStyle(site.id);
  const scriptPath = path.resolve(root, "automation", "blog-bot", "scripts", "compose_cover.py");

  execFileSync(
    "python3",
    [
      scriptPath,
      "--source",
      sourcePath,
      "--target",
      targetPath,
      "--title",
      title,
      "--site-name",
      site.name,
      "--label",
      blogName || site.name,
      "--accent",
      style.accent,
      "--accent-soft",
      style.accentSoft,
      "--background",
      style.background,
      "--surface",
      style.surface,
      "--label-fill",
      style.labelFill,
      "--label-text",
      style.labelText,
      "--variant",
      style.variant
    ],
    {
      stdio: "pipe"
    }
  );

  return targetPath;
}

module.exports = {
  composeCoverImage
};
