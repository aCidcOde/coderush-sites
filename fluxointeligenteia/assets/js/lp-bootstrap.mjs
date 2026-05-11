/**
 * Modo dev opcional (exige servidor HTTP — file:// falha no fetch).
 * A LP publicada deve usar index.html gerado por: python tools/build-index.py
 * (vide tools/build-index.py; o index na raiz já vem consolidado).
 *
 * 1) Injeta componentes globais (pages/componentes/*/fragment.html)
 * 2) Carrega site-layout.js (header/footer nos slots)
 * 3) Injeta seções da LP (pages/lp/sections/*/section.html)
 * 4) Carrega index.js
 */

const COMPONENTS = [
  ['lp-cmp-cursor', 'cursor'],
  ['lp-cmp-scroll-progress', 'scroll-progress'],
  ['lp-cmp-slot-header', 'slot-header'],
  ['lp-cmp-sticky-cta', 'sticky-cta'],
  ['lp-cmp-slot-footer', 'slot-footer'],
  ['lp-cmp-whatsapp', 'whatsapp'],
  ['lp-cmp-flux-guide', 'flux-guide'],
  ['lp-cmp-mobile-drawer', 'mobile-drawer']
];

const SECTION_ORDER = [
  'hero',
  'success-modal',
  'trust',
  'value',
  'how',
  'features',
  'interactive',
  'testimonials',
  'blog',
  'faq',
  'form-section',
  'cta',
  'newsletter'
];

const baseCmp = new URL('../../pages/componentes/', import.meta.url);
const baseSec = new URL('../../pages/lp/sections/', import.meta.url);

function loadScript(srcUrl) {
  return new Promise((resolve, reject) => {
    const s = document.createElement('script');
    s.src = srcUrl;
    s.onload = () => resolve();
    s.onerror = () => reject(new Error('Falha ao carregar ' + srcUrl));
    document.body.appendChild(s);
  });
}

async function injectBeforeMount(mountId, folder) {
  const el = document.getElementById(mountId);
  if (!el) throw new Error('[lp-bootstrap] #' + mountId + ' não encontrado');
  const url = new URL(folder + '/fragment.html', baseCmp);
  const res = await fetch(url);
  if (!res.ok) throw new Error(`Componente: ${url.href} → ${res.status}`);
  el.insertAdjacentHTML('beforebegin', await res.text());
  el.remove();
}

for (const [mountId, folder] of COMPONENTS) {
  await injectBeforeMount(mountId, folder);
}

await loadScript(new URL('./site-layout.js', import.meta.url).href);

const mount = document.getElementById('lp-sections-mount');
if (!mount) {
  console.error('[lp-bootstrap] #lp-sections-mount não encontrado');
} else {
  for (const id of SECTION_ORDER) {
    const url = new URL(`${id}/section.html`, baseSec);
    const res = await fetch(url);
    if (!res.ok) throw new Error(`LP section: ${url.href} → ${res.status}`);
    mount.insertAdjacentHTML('beforeend', await res.text());
  }
  mount.removeAttribute('aria-busy');
}

await loadScript(new URL('./index.js', import.meta.url).href);
