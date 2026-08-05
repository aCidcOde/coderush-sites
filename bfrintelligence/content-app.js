(() => {
  'use strict';

  const pathHasSitePrefix = window.location.pathname === '/site' || window.location.pathname.startsWith('/site/');
  const sitePrefix = pathHasSitePrefix ? '/site' : '';
  const apiUrl = `${sitePrefix}/api.php`;

  function element(tag, className, text) {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (text !== undefined && text !== null) node.textContent = text;
    return node;
  }

  function articleUrl(slug) {
    return `${sitePrefix}/conteudos/artigo.html?slug=${encodeURIComponent(slug)}`;
  }

  function homeUrl(hash = '') {
    return `${sitePrefix}/index.html${hash}`;
  }

  function safeHttpUrl(value) {
    if (!value) return null;
    try {
      const parsed = new URL(value, window.location.origin);
      return ['http:', 'https:'].includes(parsed.protocol) ? parsed.href : null;
    } catch {
      return null;
    }
  }

  function formatDate(value) {
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '';
    return new Intl.DateTimeFormat('pt-BR', {day: '2-digit', month: 'short', year: 'numeric'}).format(date);
  }

  function cover(post, className) {
    const link = element('a', className);
    link.href = articleUrl(post.slug);
    const imageUrl = safeHttpUrl(post.featured_image_url);
    if (imageUrl) {
      const image = document.createElement('img');
      image.src = imageUrl;
      image.alt = post.featured_image_alt || '';
      image.loading = 'lazy';
      link.appendChild(image);
    } else {
      const placeholder = element('span', 'content-cover-placeholder');
      placeholder.append(element('b', '', 'BFR'), document.createTextNode(' INSIGHT'));
      link.appendChild(placeholder);
    }
    return link;
  }

  function meta(post) {
    const wrapper = element('div', 'content-card-meta');
    wrapper.append(
      element('span', '', formatDate(post.published_at)),
      element('span', '', `${post.reading_time_minutes} min`),
    );
    return wrapper;
  }

  function card(post) {
    const article = element('article', 'content-card');
    const copy = element('div', 'content-card-copy');
    const heading = element('h3');
    const title = element('a', '', post.title);
    title.href = articleUrl(post.slug);
    heading.appendChild(title);
    copy.append(
      element('span', 'content-card-category', post.category),
      heading,
      element('p', '', post.excerpt),
      meta(post),
    );
    article.append(cover(post, 'content-card-media'), copy);
    return article;
  }

  function featured(post) {
    const article = element('article', 'content-featured-card');
    const copy = element('div', 'content-featured-copy');
    const heading = element('h2');
    const title = element('a', '', post.title);
    title.href = articleUrl(post.slug);
    heading.appendChild(title);
    const readLink = element('a', 'content-read-link', 'Ler artigo ');
    readLink.href = articleUrl(post.slug);
    readLink.appendChild(element('span', '', '→'));
    copy.append(
      element('span', 'content-card-category', `Em destaque · ${post.category}`),
      heading,
      element('p', '', post.excerpt),
      meta(post),
      readLink,
    );
    article.append(cover(post, 'content-featured-media'), copy);
    return article;
  }

  async function request(url) {
    const response = await fetch(url, {headers: {Accept: 'application/json'}});
    const payload = await response.json().catch(() => ({}));
    if (!response.ok) throw new Error(payload.message || 'Conteúdo indisponível.');
    return payload;
  }

  async function loadIndex() {
    const root = document.getElementById('contentIndexRoot');
    if (!root) return;

    try {
      const payload = await request(`${apiUrl}?limit=30`);
      const posts = Array.isArray(payload.data) ? payload.data : [];
      root.replaceChildren();

      if (posts.length === 0) {
        const empty = element('div', 'content-empty-state');
        empty.append(
          element('span', 'content-eyebrow', 'Biblioteca BFR'),
          element('h2', '', 'Os primeiros conteúdos estão sendo preparados.'),
          element('p', '', 'A estrutura editorial já está pronta para receber publicações automaticamente pela API.'),
        );
        root.appendChild(empty);
        return;
      }

      root.appendChild(featured(posts[0]));
      if (posts.length > 1) {
        const heading = element('div', 'content-section-heading');
        const headingCopy = element('div');
        headingCopy.append(element('span', 'content-eyebrow', 'Publicações'), element('h2', '', 'Últimos conteúdos'));
        heading.append(headingCopy, element('span', '', `${posts.length} artigos`));
        const grid = element('div', 'content-card-grid');
        posts.slice(1).forEach(post => grid.appendChild(card(post)));
        root.append(heading, grid);
      }
    } catch (error) {
      root.replaceChildren(element('div', 'content-home-empty', error.message));
    }
  }

  function appendInline(parent, source) {
    const pattern = /(\*\*([^*]+)\*\*|`([^`]+)`|\[([^\]]+)\]\((https?:\/\/[^)\s]+)\))/g;
    let cursor = 0;
    let match;
    while ((match = pattern.exec(source)) !== null) {
      if (match.index > cursor) parent.appendChild(document.createTextNode(source.slice(cursor, match.index)));
      if (match[2]) parent.appendChild(element('strong', '', match[2]));
      else if (match[3]) parent.appendChild(element('code', '', match[3]));
      else if (match[4] && safeHttpUrl(match[5])) {
        const link = element('a', '', match[4]);
        link.href = match[5];
        link.rel = 'noopener noreferrer';
        parent.appendChild(link);
      }
      cursor = pattern.lastIndex;
    }
    if (cursor < source.length) parent.appendChild(document.createTextNode(source.slice(cursor)));
  }

  function renderMarkdown(markdown) {
    const container = element('div', 'content-prose');
    const lines = String(markdown || '').replace(/\r\n/g, '\n').split('\n');
    let index = 0;

    while (index < lines.length) {
      const line = lines[index].trim();
      if (!line) { index++; continue; }

      const codeFence = line.match(/^```/);
      if (codeFence) {
        const codeLines = [];
        index++;
        while (index < lines.length && !lines[index].trim().startsWith('```')) codeLines.push(lines[index++]);
        index++;
        const pre = element('pre');
        pre.appendChild(element('code', '', codeLines.join('\n')));
        container.appendChild(pre);
        continue;
      }

      const image = line.match(/^!\[([^\]]*)\]\((https?:\/\/[^)\s]+)\)$/);
      if (image && safeHttpUrl(image[2])) {
        const picture = document.createElement('img');
        picture.src = image[2];
        picture.alt = image[1];
        picture.loading = 'lazy';
        container.appendChild(picture);
        index++;
        continue;
      }

      const heading = line.match(/^(#{1,3})\s+(.+)$/);
      if (heading) {
        const node = element(heading[1].length === 3 ? 'h3' : 'h2');
        appendInline(node, heading[2]);
        container.appendChild(node);
        index++;
        continue;
      }

      if (/^[-*]\s+/.test(line)) {
        const list = element('ul');
        while (index < lines.length && /^[-*]\s+/.test(lines[index].trim())) {
          const item = element('li');
          appendInline(item, lines[index].trim().replace(/^[-*]\s+/, ''));
          list.appendChild(item);
          index++;
        }
        container.appendChild(list);
        continue;
      }

      if (/^\d+\.\s+/.test(line)) {
        const list = element('ol');
        while (index < lines.length && /^\d+\.\s+/.test(lines[index].trim())) {
          const item = element('li');
          appendInline(item, lines[index].trim().replace(/^\d+\.\s+/, ''));
          list.appendChild(item);
          index++;
        }
        container.appendChild(list);
        continue;
      }

      if (line.startsWith('> ')) {
        const quote = element('blockquote');
        appendInline(quote, line.slice(2));
        container.appendChild(quote);
        index++;
        continue;
      }

      const paragraphLines = [line];
      index++;
      while (index < lines.length) {
        const next = lines[index].trim();
        if (!next || /^(#{1,3})\s+|^[-*]\s+|^\d+\.\s+|^>\s+|^```|^!\[/.test(next)) break;
        paragraphLines.push(next);
        index++;
      }
      const paragraph = element('p');
      appendInline(paragraph, paragraphLines.join(' '));
      container.appendChild(paragraph);
    }

    return container;
  }

  function articleHeader(post) {
    const header = element('header', 'content-article-header content-shell-narrow');
    const breadcrumbs = element('nav', 'content-breadcrumbs');
    breadcrumbs.setAttribute('aria-label', 'Navegação estrutural');
    const home = element('a', '', 'Início'); home.href = homeUrl();
    const contents = element('a', '', 'Conteúdos'); contents.href = `${sitePrefix}/conteudos/index.html`;
    breadcrumbs.append(home, element('span', '', '→'), contents);
    const metaLine = element('div', 'content-article-meta');
    metaLine.append(
      element('span', '', `Por ${post.author_name}`),
      element('span', '', formatDate(post.published_at)),
      element('span', '', `${post.reading_time_minutes} min de leitura`),
    );
    header.append(
      breadcrumbs,
      element('span', 'content-eyebrow', post.category),
      element('h1', '', post.title),
      element('p', 'content-article-lead', post.excerpt),
      metaLine,
    );
    return header;
  }

  function articleAside(post) {
    const aside = element('aside', 'content-article-aside');
    const cta = element('div', 'content-aside-card');
    const link = element('a', '', 'Conversar com especialista ');
    link.href = homeUrl('#contato');
    link.appendChild(element('span', '', '→'));
    cta.append(
      element('span', 'content-eyebrow', 'Aplicação real'),
      element('h2', '', 'Leve este tema para a sua operação.'),
      element('p', '', 'Mapeamos contexto, dados, integrações e controles antes de colocar o agente em produção.'),
      link,
    );
    aside.appendChild(cta);
    if (Array.isArray(post.tags) && post.tags.length) {
      const tags = element('div', 'content-tags');
      tags.setAttribute('aria-label', 'Temas deste artigo');
      post.tags.forEach(tag => tags.appendChild(element('span', '', tag)));
      aside.appendChild(tags);
    }
    return aside;
  }

  async function loadArticle() {
    const root = document.getElementById('contentArticleRoot');
    if (!root) return;
    const slug = new URLSearchParams(window.location.search).get('slug') || '';
    if (!slug) {
      root.replaceChildren(element('div', 'content-home-empty', 'Informe o conteúdo que deseja abrir.'));
      return;
    }

    try {
      const payload = await request(`${apiUrl}?slug=${encodeURIComponent(slug)}`);
      const post = payload.data;
      document.title = `${post.meta_title || post.title} | BFR Intelligence`;
      const description = document.querySelector('meta[name="description"]');
      if (description) description.content = post.meta_description || post.excerpt;

      const article = element('article', 'content-article');
      article.appendChild(articleHeader(post));
      const imageUrl = safeHttpUrl(post.featured_image_url);
      if (imageUrl) {
        const figure = element('figure', 'content-article-cover content-shell');
        const image = document.createElement('img');
        image.src = imageUrl;
        image.alt = post.featured_image_alt || '';
        figure.appendChild(image);
        article.appendChild(figure);
      }
      const layout = element('div', 'content-article-layout content-shell');
      layout.append(renderMarkdown(post.body), articleAside(post));
      article.appendChild(layout);
      root.replaceChildren(article);

      const related = Array.isArray(payload.related) ? payload.related : [];
      if (related.length) {
        const section = element('section', 'content-related');
        const shell = element('div', 'content-shell');
        const heading = element('div', 'content-section-heading');
        const copy = element('div');
        copy.append(element('span', 'content-eyebrow', 'Continue explorando'), element('h2', '', 'Conteúdos relacionados'));
        heading.appendChild(copy);
        const grid = element('div', 'content-card-grid');
        related.forEach(post => grid.appendChild(card(post)));
        shell.append(heading, grid);
        section.appendChild(shell);
        root.appendChild(section);
      }
    } catch (error) {
      root.replaceChildren(element('div', 'content-home-empty', error.message));
    }
  }

  function initNavigation() {
    const menuButton = document.getElementById('ham');
    const menuLinks = document.getElementById('navLinks');
    if (!menuButton || !menuLinks) return;
    menuButton.addEventListener('click', () => {
      const isOpen = menuLinks.classList.toggle('open');
      menuButton.setAttribute('aria-expanded', String(isOpen));
      menuButton.setAttribute('aria-label', isOpen ? 'Fechar menu' : 'Abrir menu');
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    initNavigation();
    loadIndex();
    loadArticle();
  });

  window.BfrContent = {apiUrl, articleUrl, card};
})();
