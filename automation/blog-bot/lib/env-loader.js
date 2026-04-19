const fs = require("node:fs");
const path = require("node:path");

const DEFAULT_ENV_FILES = [
  ".env",
  ".env.local",
  "sistemavendadireta/.env",
  "sistemavendadireta/.env.local",
  "codafacil/.env",
  "codafacil/.env.local",
  "fluxointeligenteia/.env",
  "fluxointeligenteia/.env.local"
];

function stripInlineComment(value) {
  if (!value.includes("#")) {
    return value;
  }

  let quoted = false;
  let quoteChar = "";
  let result = "";

  for (let index = 0; index < value.length; index += 1) {
    const char = value[index];
    const previous = value[index - 1];

    if ((char === '"' || char === "'") && previous !== "\\") {
      if (!quoted) {
        quoted = true;
        quoteChar = char;
      } else if (quoteChar === char) {
        quoted = false;
        quoteChar = "";
      }
    }

    if (char === "#" && !quoted) {
      break;
    }

    result += char;
  }

  return result.trim();
}

function parseEnvValue(rawValue) {
  const trimmed = stripInlineComment(rawValue).trim();
  if (!trimmed) {
    return "";
  }

  if (
    (trimmed.startsWith('"') && trimmed.endsWith('"')) ||
    (trimmed.startsWith("'") && trimmed.endsWith("'"))
  ) {
    return trimmed
      .slice(1, -1)
      .replace(/\\n/g, "\n")
      .replace(/\\"/g, '"')
      .replace(/\\'/g, "'");
  }

  return trimmed;
}

function loadEnvFiles(root, fileNames = DEFAULT_ENV_FILES) {
  for (const fileName of fileNames) {
    const filePath = path.resolve(root, fileName);
    if (!fs.existsSync(filePath)) {
      continue;
    }

    const content = fs.readFileSync(filePath, "utf8");
    for (const line of content.split(/\r?\n/)) {
      const trimmed = line.trim();
      if (!trimmed || trimmed.startsWith("#")) {
        continue;
      }

      const match = trimmed.match(/^(?:export\s+)?([A-Za-z_][A-Za-z0-9_]*)\s*=\s*(.*)$/);
      if (!match) {
        continue;
      }

      const [, key, rawValue] = match;
      if (process.env[key] !== undefined) {
        continue;
      }

      process.env[key] = parseEnvValue(rawValue);
    }
  }
}

module.exports = {
  DEFAULT_ENV_FILES,
  loadEnvFiles
};
