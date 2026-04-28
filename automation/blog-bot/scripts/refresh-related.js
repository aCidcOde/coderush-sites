#!/usr/bin/env node

const fs = require("node:fs");
const path = require("node:path");
const { loadEnvFiles } = require("../lib/env-loader");
const { refreshAllPosts } = require("../lib/related-refresher");

const ROOT = path.resolve(__dirname, "..", "..", "..");
const CONFIG_PATH = path.resolve(__dirname, "..", "config", "sites.json");

loadEnvFiles(ROOT);

function main() {
  const sitesConfig = JSON.parse(fs.readFileSync(CONFIG_PATH, "utf8"));
  const result = refreshAllPosts(ROOT, sitesConfig);
  console.log(JSON.stringify(result, null, 2));
  if (result.errors.length > 0) {
    process.exit(1);
  }
}

main();
