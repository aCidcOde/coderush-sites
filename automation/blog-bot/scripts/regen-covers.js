#!/usr/bin/env node

const fs = require("node:fs");
const path = require("node:path");

const ROOT = path.resolve(__dirname, "..", "..", "..");
const { loadEnvFiles } = require(path.resolve(__dirname, "..", "lib", "env-loader"));
loadEnvFiles(ROOT);

const { resolveAiConfig } = require(path.resolve(__dirname, "..", "lib", "ai-writer"));
const { generateCover } = require(path.resolve(__dirname, "..", "lib", "cover-agent"));

const CONFIG = JSON.parse(
  fs.readFileSync(path.resolve(__dirname, "..", "config", "sites.json"), "utf8")
);

function parseArgs() {
  const args = process.argv.slice(2);
  const filterSites = args
    .find((a) => a.startsWith("--sites="))
    ?.split("=")[1]
    ?.split(",")
    .map((s) => s.trim())
    .filter(Boolean);
  const filterSlug = args.find((a) => a.startsWith("--slug="))?.split("=")[1];
  const dryRun = args.includes("--dry-run");
  return { filterSites, filterSlug, dryRun };
}

function decode(value) {
  return String(value || "")
    .replace(/&quot;/g, '"')
    .replace(/&#039;/g, "'")
    .replace(/&amp;/g, "&")
    .replace(/&lt;/g, "<")
    .replace(/&gt;/g, ">")
    .trim();
}

function stripTags(value) {
  return decode(String(value || "").replace(/<[^>]+>/g, " "))
    .replace(/\s+/g, " ")
    .trim();
}

function extractMetaFromHtml(html) {
  const titleMatch =
    html.match(/<meta\s+property="og:title"\s+content="([^"]+)"/i) ||
    html.match(/<title>([\s\S]*?)<\/title>/i);
  const descMatch =
    html.match(/<meta\s+name="description"\s+content="([^"]+)"/i) ||
    html.match(/<meta\s+property="og:description"\s+content="([^"]+)"/i);
  const h1Match = html.match(/<h1[^>]*>([\s\S]*?)<\/h1>/i);

  const rawTitle = decode(titleMatch?.[1] || "");
  const cleanedTitle = rawTitle.includes("|") ? rawTitle.split("|")[0].trim() : rawTitle;

  return {
    headline: stripTags(h1Match?.[1] || cleanedTitle),
    summary: decode(descMatch?.[1] || "")
  };
}

function findPostsForSite(site) {
  const siteRoot = path.resolve(ROOT, site.siteRoot);
  const yearDirs = fs
    .readdirSync(siteRoot, { withFileTypes: true })
    .filter((d) => d.isDirectory() && /^\d{4}$/.test(d.name))
    .map((d) => d.name);

  const posts = [];
  for (const year of yearDirs) {
    const yearDir = path.join(siteRoot, year);
    const months = fs.readdirSync(yearDir).filter((m) => /^\d{2}$/.test(m));
    for (const month of months) {
      const monthDir = path.join(yearDir, month);
      const days = fs.readdirSync(monthDir).filter((d) => /^\d{2}$/.test(d));
      for (const day of days) {
        const dayDir = path.join(monthDir, day);
        const slugs = fs
          .readdirSync(dayDir, { withFileTypes: true })
          .filter((d) => d.isDirectory());
        for (const slugEntry of slugs) {
          const slug = slugEntry.name;
          const indexFile = path.join(
            dayDir,
            slug,
            site.renderExtension === "html" ? "index.html" : "index.php"
          );
          if (fs.existsSync(indexFile)) {
            posts.push({
              slug,
              date: `${year}-${month}-${day}`,
              indexFile
            });
          }
        }
      }
    }
  }
  return posts;
}

function loadDraft(site, post) {
  const draftPath = path.resolve(
    ROOT,
    "automation",
    "blog-bot",
    "out",
    site.id,
    post.date,
    `${post.slug}.json`
  );
  if (!fs.existsSync(draftPath)) return null;
  try {
    const draft = JSON.parse(fs.readFileSync(draftPath, "utf8"));
    if (draft?.content?.headline) return draft;
  } catch {
    return null;
  }
  return null;
}

function buildContract(site, post) {
  const draft = loadDraft(site, post);
  if (draft) {
    return {
      ...draft,
      slug: post.slug,
      date: post.date
    };
  }

  const html = fs.readFileSync(post.indexFile, "utf8");
  const meta = extractMetaFromHtml(html);
  const themeFromSlug = post.slug.replace(/-\d{4}-\d{2}-\d{2}$/, "").replace(/-/g, " ");

  return {
    siteId: site.id,
    slug: post.slug,
    date: post.date,
    theme: themeFromSlug,
    focus: themeFromSlug,
    content: {
      headline: meta.headline || post.slug,
      summary: meta.summary || `Post de ${site.name} sobre ${themeFromSlug}.`,
      sections: []
    }
  };
}

async function regenerateCoverForPost(site, post, aiConfig, dryRun) {
  const contract = buildContract(site, post);
  const targetPath = path.resolve(
    ROOT,
    site.siteRoot,
    site.assets.postsDir,
    `${post.slug}.jpg`
  );

  if (dryRun) {
    return {
      siteId: site.id,
      slug: post.slug,
      headline: contract.content.headline,
      summary: contract.content.summary?.slice(0, 100),
      targetPath: path.relative(ROOT, targetPath),
      action: "would-regenerate"
    };
  }

  if (fs.existsSync(targetPath)) {
    fs.unlinkSync(targetPath);
  }

  const result = await generateCover({ aiConfig, site, contract, targetPath });
  return {
    siteId: site.id,
    slug: post.slug,
    headline: contract.content.headline,
    model: result.source,
    targetPath: path.relative(ROOT, targetPath)
  };
}

async function main() {
  const { filterSites, filterSlug, dryRun } = parseArgs();
  const aiConfig = resolveAiConfig(process.env);
  if (!aiConfig && !dryRun) {
    throw new Error("Sem aiConfig — configure API_OPENROUTER ou OPENAI_API_KEY no .env");
  }

  const sites = CONFIG.sites.filter(
    (s) => s.enabled && (!filterSites || filterSites.includes(s.id))
  );

  const queue = [];
  for (const site of sites) {
    const posts = findPostsForSite(site);
    for (const post of posts) {
      if (filterSlug && post.slug !== filterSlug) continue;
      queue.push({ site, post });
    }
  }

  console.log(`Total a processar: ${queue.length} posts`);
  if (dryRun) console.log("Modo dry-run: nenhuma capa sera regerada.");

  const results = [];
  let index = 0;
  for (const { site, post } of queue) {
    index += 1;
    const label = `[${index}/${queue.length}] ${site.id}/${post.slug}`;
    process.stdout.write(`${label} ... `);
    try {
      const result = await regenerateCoverForPost(site, post, aiConfig, dryRun);
      console.log(`OK (${result.model || "dry-run"})`);
      results.push({ ok: true, ...result });
    } catch (error) {
      console.log(`FAIL: ${error.message}`);
      results.push({
        ok: false,
        siteId: site.id,
        slug: post.slug,
        error: String(error.message || error)
      });
    }
  }

  const ok = results.filter((r) => r.ok).length;
  const fail = results.filter((r) => !r.ok).length;
  console.log(`\nResumo: ${ok} OK, ${fail} falhas, ${queue.length} total.`);

  if (fail > 0) {
    console.log("\nFalhas:");
    for (const r of results.filter((r) => !r.ok)) {
      console.log(`  - ${r.siteId}/${r.slug}: ${r.error}`);
    }
  }

  const reportPath = path.resolve(
    ROOT,
    "automation",
    "blog-bot",
    "reports",
    `regen-covers-${new Date().toISOString().slice(0, 19).replace(/[:T]/g, "-")}.json`
  );
  fs.writeFileSync(reportPath, JSON.stringify(results, null, 2));
  console.log(`\nRelatorio: ${path.relative(ROOT, reportPath)}`);

  process.exit(fail > 0 ? 1 : 0);
}

main().catch((error) => {
  console.error("Falha fatal:", error);
  process.exit(1);
});
