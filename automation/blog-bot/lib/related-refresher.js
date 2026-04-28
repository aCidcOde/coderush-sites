const fs = require("node:fs");
const path = require("node:path");
const { buildCrossSiteIndex, pickRelatedFromOtherSites, pickSameSiteRelated } = require("./cross-site");
const {
  SAME_SITE_MARKERS,
  CROSS_SITE_MARKERS,
  renderSameSiteSection,
  renderCrossSiteSection
} = require("./related-renderer");

function escapeRegex(value) {
  return String(value).replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
}

function listPostFiles(rootDir, sitesConfig) {
  const files = [];
  for (const site of sitesConfig.sites) {
    const siteRoot = path.resolve(rootDir, site.siteRoot);
    if (!fs.existsSync(siteRoot)) continue;
    const ext = site.renderExtension || "php";
    walkPostFiles(siteRoot, ext).forEach((filePath) => {
      files.push({ siteId: site.id, filePath });
    });
  }
  return files;
}

function walkPostFiles(siteRoot, ext) {
  const out = [];
  const yearRegex = /^\d{4}$/;
  const monthDayRegex = /^\d{2}$/;

  if (!fs.existsSync(siteRoot)) return out;
  for (const yearEntry of fs.readdirSync(siteRoot)) {
    if (!yearRegex.test(yearEntry)) continue;
    const yearPath = path.join(siteRoot, yearEntry);
    if (!fs.statSync(yearPath).isDirectory()) continue;
    for (const monthEntry of fs.readdirSync(yearPath)) {
      if (!monthDayRegex.test(monthEntry)) continue;
      const monthPath = path.join(yearPath, monthEntry);
      if (!fs.statSync(monthPath).isDirectory()) continue;
      for (const dayEntry of fs.readdirSync(monthPath)) {
        if (!monthDayRegex.test(dayEntry)) continue;
        const dayPath = path.join(monthPath, dayEntry);
        if (!fs.statSync(dayPath).isDirectory()) continue;
        for (const slugEntry of fs.readdirSync(dayPath)) {
          const slugPath = path.join(dayPath, slugEntry);
          if (!fs.statSync(slugPath).isDirectory()) continue;
          const candidate = path.join(slugPath, `index.${ext}`);
          if (fs.existsSync(candidate)) {
            out.push(candidate);
          }
        }
      }
    }
  }
  return out;
}

function postPathFromFile(filePath, siteRoot) {
  const rel = path.relative(siteRoot, filePath).replace(/\\/g, "/");
  const match = rel.match(/^(\d{4}\/\d{2}\/\d{2}\/[^/]+)\/index\.[a-z]+$/);
  return match ? `${match[1]}/` : "";
}

function extractTitle(content) {
  const titleMatch = content.match(/<title>([\s\S]*?)<\/title>/i);
  if (titleMatch) {
    return titleMatch[1].split(" | ")[0].replace(/\s+/g, " ").trim();
  }
  const h1Match = content.match(/<h1[^>]*>([\s\S]*?)<\/h1>/i);
  if (h1Match) {
    return h1Match[1].replace(/<[^>]+>/g, " ").replace(/\s+/g, " ").trim();
  }
  return "";
}

function extractSummary(content) {
  const descMatch = content.match(/<meta\s+name="description"\s+content="([^"]+)"/i);
  if (descMatch) return descMatch[1];
  const firstP = content.match(/<p[^>]*>([\s\S]*?)<\/p>/i);
  if (firstP) return firstP[1].replace(/<[^>]+>/g, " ").trim();
  return "";
}

function replaceMarkedSegment(content, markers, replacement) {
  const regex = new RegExp(`${escapeRegex(markers.start)}[\\s\\S]*?${escapeRegex(markers.end)}`);
  if (regex.test(content)) {
    return { updated: content.replace(regex, replacement), inserted: false };
  }
  return { updated: content, inserted: true };
}

function findLegacyLeiaTambemSection(content) {
  const heading = /<section[^>]*>[\s\S]*?<h2[^>]*>\s*Leia\s+(tamb[ée]m|Tamb[ée]m)\s*<\/h2>/i;
  const match = content.match(heading);
  if (!match) return null;
  const start = match.index;
  const closing = "</section>";
  const close = content.indexOf(closing, start);
  if (close === -1) return null;
  return { start, end: close + closing.length };
}

function ensureSameSiteSection(content, sectionHtml) {
  if (!sectionHtml) return content;
  const replaced = replaceMarkedSegment(content, SAME_SITE_MARKERS, sectionHtml);
  if (!replaced.inserted) return replaced.updated;

  const legacy = findLegacyLeiaTambemSection(content);
  if (legacy) {
    return content.slice(0, legacy.start) + sectionHtml + content.slice(legacy.end);
  }
  const mainCloseIndex = content.lastIndexOf("</main>");
  if (mainCloseIndex === -1) return content;
  return `${content.slice(0, mainCloseIndex)}${sectionHtml}\n${content.slice(mainCloseIndex)}`;
}

function ensureCrossSiteSection(content, sectionHtml) {
  if (!sectionHtml) return content;
  const replaced = replaceMarkedSegment(content, CROSS_SITE_MARKERS, sectionHtml);
  if (!replaced.inserted) return replaced.updated;

  const sameSiteEndIndex = content.indexOf(SAME_SITE_MARKERS.end);
  if (sameSiteEndIndex !== -1) {
    const insertAt = sameSiteEndIndex + SAME_SITE_MARKERS.end.length;
    return `${content.slice(0, insertAt)}\n${sectionHtml}${content.slice(insertAt)}`;
  }
  const mainCloseIndex = content.lastIndexOf("</main>");
  if (mainCloseIndex === -1) return content;
  return `${content.slice(0, mainCloseIndex)}${sectionHtml}\n${content.slice(mainCloseIndex)}`;
}

function refreshPostContent({ content, currentSite, currentPostPath, currentTitle, currentText, index }) {
  const sameCards = pickSameSiteRelated({
    currentSiteId: currentSite.id,
    currentPostPath,
    currentTitle,
    currentText,
    index,
    max: 3
  });
  const crossCards = pickRelatedFromOtherSites({
    currentSiteId: currentSite.id,
    currentTitle,
    currentText,
    index,
    max: 3
  });

  const sitePathPrefix = "../../../../";
  const blogHref = `${sitePathPrefix}blog/`;

  const sameSection = renderSameSiteSection(sameCards, sitePathPrefix, blogHref);
  const crossSection = renderCrossSiteSection(crossCards);

  let updated = content;
  updated = ensureSameSiteSection(updated, sameSection);
  updated = ensureCrossSiteSection(updated, crossSection);

  return {
    content: updated,
    sameCount: sameCards.length,
    crossCount: crossCards.length
  };
}

function refreshAllPosts(rootDir, sitesConfig) {
  const index = buildCrossSiteIndex(rootDir, sitesConfig);
  const files = listPostFiles(rootDir, sitesConfig);
  const sitesById = Object.fromEntries(sitesConfig.sites.map((s) => [s.id, s]));

  let touched = 0;
  let unchanged = 0;
  const errors = [];

  for (const { siteId, filePath } of files) {
    const site = sitesById[siteId];
    if (!site) continue;

    try {
      const content = fs.readFileSync(filePath, "utf8");
      const siteRoot = path.resolve(rootDir, site.siteRoot);
      const currentPostPath = postPathFromFile(filePath, siteRoot);
      const currentTitle = extractTitle(content);
      const currentText = extractSummary(content);

      const result = refreshPostContent({
        content,
        currentSite: site,
        currentPostPath,
        currentTitle,
        currentText,
        index
      });

      if (result.content !== content) {
        fs.writeFileSync(filePath, result.content, "utf8");
        touched += 1;
      } else {
        unchanged += 1;
      }
    } catch (error) {
      errors.push({ filePath, error: error.message || String(error) });
    }
  }

  return { touched, unchanged, errors, totalCards: index.length, totalFiles: files.length };
}

module.exports = {
  refreshAllPosts,
  refreshPostContent,
  listPostFiles
};
