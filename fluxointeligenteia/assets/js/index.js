/* ─── CURSOR ──────────────────────────────────────────── */
const dot = document.getElementById('cursor-dot');
const ring = document.getElementById('cursor-ring');
let mx = 0, my = 0, rx = 0, ry = 0;
document.addEventListener('mousemove', e => {
  mx = e.clientX; my = e.clientY;
  dot.style.left = mx + 'px'; dot.style.top = my + 'px';
});
(function animateCursor() {
  rx += (mx - rx) * 0.12; ry += (my - ry) * 0.12;
  ring.style.left = rx + 'px'; ring.style.top = ry + 'px';
  requestAnimationFrame(animateCursor);
})();
document.addEventListener('mouseover', e => {
  if (e.target.closest('a,button,input,.sl-arrow,.sl-dot,.wa-circle')) {
    dot.style.transform = 'translate(-50%,-50%) scale(1.8)';
    dot.style.background = 'var(--a1)';
  } else {
    dot.style.transform = 'translate(-50%,-50%) scale(1)';
    dot.style.background = 'var(--a3)';
  }
});

/* ─── PARTICLES ───────────────────────────────────────── */
const canvas = document.getElementById('particles-canvas');
const ctx = canvas.getContext('2d');
let W, H, particles = [];
function resizeCanvas() { W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; }
resizeCanvas(); window.addEventListener('resize', resizeCanvas);
function mkP() {
  return { x:Math.random()*W, y:Math.random()*H, vx:(Math.random()-.5)*.28, vy:(Math.random()-.5)*.28,
    r:Math.random()*1.4+.4, op:Math.random()*.35+.08, hue:Math.random()>.5?'0,255,122':'125,255,77' };
}
for(let i=0;i<90;i++) particles.push(mkP());
(function drawP() {
  ctx.clearRect(0,0,W,H);
  particles.forEach(p=>{
    p.x+=p.vx; p.y+=p.vy;
    if(p.x<0)p.x=W; if(p.x>W)p.x=0; if(p.y<0)p.y=H; if(p.y>H)p.y=0;
    ctx.beginPath(); ctx.arc(p.x,p.y,p.r,0,Math.PI*2);
    ctx.fillStyle='rgba('+p.hue+','+p.op+')'; ctx.fill();
  });
  for(let i=0;i<particles.length;i++) for(let j=i+1;j<particles.length;j++){
    const dx=particles[i].x-particles[j].x, dy=particles[i].y-particles[j].y;
    const d=Math.sqrt(dx*dx+dy*dy);
    if(d<110){ ctx.beginPath(); ctx.moveTo(particles[i].x,particles[i].y);
      ctx.lineTo(particles[j].x,particles[j].y);
      ctx.strokeStyle='rgba(0,255,122,'+(0.08*(1-d/110))+')'; ctx.lineWidth=.5; ctx.stroke(); }
  }
  requestAnimationFrame(drawP);
})();

/* ─── SCROLL ──────────────────────────────────────────── */
const bar = document.getElementById('scroll-progress');
const hdr = document.getElementById('header');
const stickyBtn = document.getElementById('sticky-cta');
window.addEventListener('scroll', () => {
  const sy = window.scrollY, sh = document.body.scrollHeight - window.innerHeight;
  bar.style.width = (sy / sh * 100) + '%';
  if (hdr) hdr.classList.toggle('scrolled', sy > 50);
  stickyBtn.classList.toggle('visible', sy > window.innerHeight * 0.8);
}, {passive:true});

/* ─── SCROLL REVEAL ───────────────────────────────────── */
const revObs = new IntersectionObserver(entries=>{
  entries.forEach(e=>{ if(e.isIntersecting){e.target.classList.add('in');revObs.unobserve(e.target);} });
},{threshold:.1});
document.querySelectorAll('.rv,.rv-scale').forEach(el=>revObs.observe(el));

/* ─── SPOTLIGHT ───────────────────────────────────────── */
document.querySelectorAll('.val-card,.feat-card').forEach(card=>{
  card.addEventListener('mousemove', e=>{
    const r=card.getBoundingClientRect();
    card.style.setProperty('--mx',((e.clientX-r.left)/r.width*100).toFixed(1)+'%');
    card.style.setProperty('--my',((e.clientY-r.top)/r.height*100).toFixed(1)+'%');
  });
});

/* ─── FORM HELPERS (cliente; com backend: revalidar no servidor, HTTPS, rate limit, CAPTCHA/honeypot server-side, CSRF se cookies de sessão) ─ */
function digitsOnly(str) {
  return String(str || '').replace(/\D/g, '');
}
function sanitizePersonName(str) {
  return String(str || '').replace(/[^\p{L}\s'-]/gu, '').replace(/\s{2,}/g, ' ');
}
function normalizeEmailInput(str) {
  return String(str || '').replace(/\s+/g, '').toLowerCase();
}
function formatPhoneBRMask(raw) {
  const d = digitsOnly(raw).slice(0, 13);
  const hasCountry = d.startsWith('55') && d.length >= 12;
  const local = hasCountry ? d.slice(2) : d;
  const ddd = local.slice(0, 2);
  const first = local.length > 10 ? local.slice(2, 7) : local.slice(2, 6);
  const last = local.length > 10 ? local.slice(7, 11) : local.slice(6, 10);
  let out = '';
  if (hasCountry) out += '+55 ';
  if (ddd) out += `(${ddd}`;
  if (ddd.length === 2) out += ') ';
  if (first) out += first;
  if (last) out += '-' + last;
  return out;
}
function validateEmailStrict(email) {
  const s = String(email || '').trim();
  if (s.length < 5 || s.length > 254) return false;
  const at = s.lastIndexOf('@');
  if (at <= 0 || at === s.length - 1) return false;
  const local = s.slice(0, at);
  const domain = s.slice(at + 1);
  if (local.length < 1 || local.length > 64) return false;
  if (domain.length < 3 || domain.length > 253 || !domain.includes('.')) return false;
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(s);
}
function validateBRPhoneDigits(d) {
  const n = d.length;
  if (n < 10 || n > 13) return false;
  if (n >= 12 && !d.startsWith('55')) return false;
  return true;
}
function setNlFieldState(groupId, hasError) {
  const g = document.getElementById(groupId);
  if (!g) return;
  const inp = g.querySelector('.nl-input');
  if (!inp) return;
  g.classList.toggle('has-error', hasError);
  inp.classList.toggle('nl-invalid', hasError);
  inp.setAttribute('aria-invalid', hasError ? 'true' : 'false');
}

/* ─── NEWSLETTER INTERACTION ─────────────────────────── */
const newsletterShell = document.getElementById('newsletter-shell');
const newsletterForm = document.getElementById('newsletter-form');
const newsletterSuccess = document.getElementById('nl-success');
const newsletterEmail = document.getElementById('nl-email');
const newsletterName = document.getElementById('nl-name');
const newsletterSuccessTitle = document.getElementById('nl-success-title');
const newsletterSuccessText = document.getElementById('nl-success-text');
const newsletterSuccessBadge = document.getElementById('nl-success-badge');

if (newsletterShell) {
  newsletterShell.addEventListener('mousemove', e => {
    const r = newsletterShell.getBoundingClientRect();
    newsletterShell.style.setProperty('--mx', ((e.clientX - r.left) / r.width * 100).toFixed(1) + '%');
    newsletterShell.style.setProperty('--my', ((e.clientY - r.top) / r.height * 100).toFixed(1) + '%');
  });
}
if (newsletterForm) {
  const nlHp = document.getElementById('flux-nl-hp');
  const nlSuccessVariants = [
    {
      title: 'Perfeito, {name}! sua trilha premium foi ativada.',
      text: 'Você vai receber conteúdos práticos para transformar operação em resultado mensurável.',
      badge: 'Flux Executive Signals'
    },
    {
      title: '{name}, inscrição confirmada com prioridade.',
      text: 'A partir de agora você recebe insights curados para decisões mais rápidas e inteligentes.',
      badge: 'VIP Strategy Feed'
    },
    {
      title: 'Excelente escolha, {name}.',
      text: 'Seu feed agora inclui playbooks aplicáveis, tendências e movimentos que já geram ganho real.',
      badge: 'Premium Ops Intelligence'
    }
  ];
  const clearNlErrors = () => {
    setNlFieldState('nl-fg-name', false);
    setNlFieldState('nl-fg-email', false);
  };
  if (newsletterSuccess && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    newsletterSuccess.addEventListener('mousemove', e => {
      const r = newsletterSuccess.getBoundingClientRect();
      const x = ((e.clientX - r.left) / r.width * 100).toFixed(1) + '%';
      const y = ((e.clientY - r.top) / r.height * 100).toFixed(1) + '%';
      newsletterSuccess.style.setProperty('--sx', x);
      newsletterSuccess.style.setProperty('--sy', y);
    });
    newsletterSuccess.addEventListener('mouseleave', () => {
      newsletterSuccess.style.setProperty('--sx', '50%');
      newsletterSuccess.style.setProperty('--sy', '50%');
    });
  }
  newsletterForm.addEventListener('submit', e => {
    e.preventDefault();
    if (nlHp && nlHp.value.trim() !== '') nlHp.value = '';
    clearNlErrors();
    const emailVal = normalizeEmailInput(newsletterEmail?.value || '');
    const nameVal = sanitizePersonName(newsletterName?.value || '').trim();
    if (newsletterEmail) newsletterEmail.value = emailVal;
    if (newsletterName) newsletterName.value = nameVal;
    const okEmail = validateEmailStrict(emailVal);
    const okName = nameVal.length >= 2;
    if (!okName) {
      setNlFieldState('nl-fg-name', true);
      newsletterName?.focus();
    }
    if (!okEmail) {
      setNlFieldState('nl-fg-email', true);
      if (okName) newsletterEmail?.focus();
    }
    if (!okName || !okEmail) return;
    const firstName = nameVal.split(/\s+/)[0] || 'você';
    const variant = nlSuccessVariants[Math.floor(Math.random() * nlSuccessVariants.length)];
    if (newsletterSuccessTitle) newsletterSuccessTitle.textContent = variant.title.replace('{name}', firstName);
    if (newsletterSuccessText) newsletterSuccessText.textContent = variant.text;
    if (newsletterSuccessBadge) newsletterSuccessBadge.textContent = variant.badge;
    if (newsletterSuccess) {
      newsletterSuccess.classList.remove('show');
      newsletterSuccess.classList.remove('is-burst');
      void newsletterSuccess.offsetWidth;
      newsletterSuccess.classList.add('show');
      newsletterSuccess.classList.add('is-burst');
      window.setTimeout(() => newsletterSuccess.classList.remove('is-burst'), 650);
    }
    newsletterForm.reset();
    clearNlErrors();
    window.setTimeout(() => newsletterSuccess?.classList.remove('show'), 5200);
  });
  newsletterName?.addEventListener('input', () => {
    newsletterName.value = sanitizePersonName(newsletterName.value);
    setNlFieldState('nl-fg-name', false);
  });
  newsletterEmail?.addEventListener('input', () => {
    newsletterEmail.value = normalizeEmailInput(newsletterEmail.value);
    setNlFieldState('nl-fg-email', false);
  });
}

/* ─── FAQ ACCORDION ───────────────────────────────────── */
const faqList = document.getElementById('faq-list');
if (faqList) {
  const faqItems = Array.from(faqList.querySelectorAll('.faq-item'));
  const openFaqItem = item => {
    faqItems.forEach(other => {
      const answer = other.querySelector('.faq-a');
      if (!answer) return;
      if (other === item) {
        other.classList.add('open');
        answer.style.maxHeight = answer.scrollHeight + 'px';
      } else {
        other.classList.remove('open');
        answer.style.maxHeight = '0px';
      }
    });
  };
  faqItems.forEach(item => {
    const questionBtn = item.querySelector('.faq-q');
    const answer = item.querySelector('.faq-a');
    if (!questionBtn || !answer) return;
    if (item.classList.contains('open')) {
      answer.style.maxHeight = answer.scrollHeight + 'px';
    } else {
      answer.style.maxHeight = '0px';
    }
    questionBtn.addEventListener('click', () => {
      const isOpen = item.classList.contains('open');
      if (isOpen) {
        item.classList.remove('open');
        answer.style.maxHeight = '0px';
      } else {
        openFaqItem(item);
      }
    });
  });
}

/* ─── HERO BACKGROUND INTERACTION ─────────────────────── */
const heroSection = document.getElementById('hero');
let heroParallaxRaf = null;
let heroPointerX = 0;
let heroPointerY = 0;

if (heroSection && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
  heroSection.addEventListener('mousemove', e => {
    const rect = heroSection.getBoundingClientRect();
    heroPointerX = (e.clientX - rect.left) / rect.width;
    heroPointerY = (e.clientY - rect.top) / rect.height;
    if (heroParallaxRaf) return;
    heroParallaxRaf = requestAnimationFrame(() => {
      const tx = ((heroPointerX - 0.5) * 22).toFixed(2) + 'px';
      const ty = ((heroPointerY - 0.5) * 16).toFixed(2) + 'px';
      heroSection.style.setProperty('--hero-tilt-x', tx);
      heroSection.style.setProperty('--hero-tilt-y', ty);
      heroSection.style.setProperty('--hx', (heroPointerX * 100).toFixed(1) + '%');
      heroSection.style.setProperty('--hy', (heroPointerY * 100).toFixed(1) + '%');
      heroParallaxRaf = null;
    });
  }, { passive: true });

  heroSection.addEventListener('mouseleave', () => {
    heroSection.style.setProperty('--hero-tilt-x', '0px');
    heroSection.style.setProperty('--hero-tilt-y', '0px');
    heroSection.style.setProperty('--hx', '50%');
    heroSection.style.setProperty('--hy', '44%');
  });
}

/* ─── HERO STATE SYSTEM ──────────────────────────────── */
let heroState = 0;
let heroAutoTimer = null;
const HERO_TAB_MS = 4800;

function getHeroTabs() {
  return Array.from(document.querySelectorAll('.htab'));
}

function syncHeroTabProgress(activeBtn) {
  if (!activeBtn) return;
  getHeroTabs().forEach(b => b.classList.remove('is-ticking'));
  void activeBtn.offsetWidth;
  activeBtn.classList.add('is-ticking');
}

function applyHeroState(idx, btn) {
  // Deactivate all
  document.querySelectorAll('.hstate').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.hvisual').forEach(v => v.classList.remove('active'));
  const btns = getHeroTabs();
  btns.forEach(b => b.classList.remove('active'));

  // Activate target
  const states = document.querySelectorAll('.hstate');
  const visuals = document.querySelectorAll('.hvisual');
  const totalStates = Math.min(states.length, visuals.length, btns.length);
  if (!totalStates) return;
  const safeIdx = ((idx % totalStates) + totalStates) % totalStates;
  const targetBtn = btn || btns[safeIdx];
  if (states[safeIdx]) states[safeIdx].classList.add('active');
  if (visuals[safeIdx]) visuals[safeIdx].classList.add('active');
  if (targetBtn) {
    targetBtn.classList.add('active');
    syncHeroTabProgress(targetBtn);
  }
  heroState = safeIdx;
}

function setHeroState(idx, btn) {
  applyHeroState(idx, btn);
  // Reinicia o autoplay a cada interação manual para manter cadência previsível.
  startHeroAutoplay();
}

function startHeroAutoplay() {
  if (heroAutoTimer) clearInterval(heroAutoTimer);
  const btns = getHeroTabs();
  if (!btns.length) return;
  const currentBtn = btns[heroState] || btns[0];
  if (currentBtn && heroState >= btns.length) heroState = 0;
  if (currentBtn) syncHeroTabProgress(currentBtn);
  heroAutoTimer = setInterval(() => {
    const next = (heroState + 1) % btns.length;
    if (btns[next]) applyHeroState(next, btns[next]);
  }, HERO_TAB_MS);
}

// Keyboard navigation for hero tabs
const heroRoot = document.getElementById('hero');
if (heroRoot) {
  const heroTabsRoot = document.getElementById('hero-tabs');
  if (heroTabsRoot) {
    heroTabsRoot.addEventListener('keydown', e => {
      const btns = getHeroTabs();
      if (!btns.length) return;
      if (e.key === 'ArrowRight') {
        e.preventDefault();
        const next = (heroState + 1) % btns.length;
        setHeroState(next, btns[next]);
      }
      if (e.key === 'ArrowLeft') {
        e.preventDefault();
        const prev = (heroState - 1 + btns.length) % btns.length;
        setHeroState(prev, btns[prev]);
      }
    });
    // Pausa apenas ao interagir com as tabs (não com o hero inteiro).
    heroTabsRoot.addEventListener('mouseenter', () => {
      if (heroAutoTimer) clearInterval(heroAutoTimer);
      getHeroTabs().forEach(b => b.classList.remove('is-ticking'));
    });
    heroTabsRoot.addEventListener('mouseleave', startHeroAutoplay);
    heroTabsRoot.addEventListener('focusin', () => {
      if (heroAutoTimer) clearInterval(heroAutoTimer);
    });
    heroTabsRoot.addEventListener('focusout', e => {
      if (!heroTabsRoot.contains(e.relatedTarget)) startHeroAutoplay();
    });
  }
  applyHeroState(heroState);
  startHeroAutoplay();
}

document.querySelectorAll('.hero-card').forEach(card=>{
  if (!window.matchMedia('(hover:hover) and (pointer:fine)').matches) return;
  let tiltRaf = null;
  let tiltX = 0;
  let tiltY = 0;
  card.addEventListener('mousemove', e=>{
    const r=card.getBoundingClientRect();
    tiltX=(e.clientX-r.left)/r.width-.5;
    tiltY=(e.clientY-r.top)/r.height-.5;
    if (tiltRaf) return;
    tiltRaf = requestAnimationFrame(()=>{
      card.style.transform='perspective(800px) rotateX('+(-tiltY*4)+'deg) rotateY('+(tiltX*5)+'deg) translateY(-2px)';
      tiltRaf = null;
    });
  });
  card.addEventListener('mouseleave',()=>{
    card.style.transition='transform 0.45s var(--ease-out)';
    card.style.transform='';
  });
  card.addEventListener('mouseenter',()=>{
    card.style.transition='transform 0.12s linear';
  });
});

const integrationsChip = document.getElementById('chip-integrations');
const integrationsRadial = document.getElementById('chip-integrations-radial');
const integrationsTip = document.getElementById('chip-integrations-tip');
if (integrationsChip && integrationsRadial && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
  integrationsChip.addEventListener('mousemove', e => {
    const r = integrationsChip.getBoundingClientRect();
    const nx = ((e.clientX - r.left) / r.width) - 0.5;
    const ny = ((e.clientY - r.top) / r.height) - 0.5;
    integrationsRadial.style.setProperty('--ry', `${(nx * 11).toFixed(2)}deg`);
    integrationsRadial.style.setProperty('--rx', `${(-ny * 11).toFixed(2)}deg`);
    const angle = 346 + Math.round((nx + 0.5) * 10);
    const clampedAngle = Math.max(342, Math.min(356, angle));
    integrationsRadial.style.setProperty('--angle', `${clampedAngle}deg`);
    if (integrationsTip) {
      const availability = (95 + ((clampedAngle - 342) / 14) * 4.8).toFixed(1);
      const online = Math.max(10, Math.min(12, Math.round(availability / 8.3)));
      integrationsTip.textContent = `${online}/12 online · ${availability}%`;
    }
  });
  integrationsChip.addEventListener('mouseleave', () => {
    integrationsRadial.style.setProperty('--rx', '0deg');
    integrationsRadial.style.setProperty('--ry', '0deg');
    integrationsRadial.style.setProperty('--angle', '352deg');
    if (integrationsTip) integrationsTip.textContent = '12/12 online · 99.8%';
  });
}

/* ─── HERO MINI-APP INTERACTIONS ──────────────────────── */
document.querySelectorAll('.app-side-list').forEach(list=>{
  list.querySelectorAll('.app-side-item').forEach(item=>{
    item.addEventListener('click', ()=>{
      list.querySelectorAll('.app-side-item').forEach(i=>i.classList.remove('active'));
      item.classList.add('active');
    });
  });
});

document.querySelectorAll('.pipeline-list').forEach(list=>{
  list.querySelectorAll('.pipeline-item').forEach((item, idx)=>{
    if (idx === 0) item.classList.add('is-selected');
    item.addEventListener('click', ()=>{
      list.querySelectorAll('.pipeline-item').forEach(i=>i.classList.remove('is-selected'));
      item.classList.add('is-selected');
    });
  });
});

document.querySelectorAll('.integration-grid').forEach(grid=>{
  grid.querySelectorAll('.integration-item').forEach(item=>{
    item.addEventListener('click', ()=>{
      if (!item.classList.contains('add')) {
        item.classList.toggle('is-selected');
      } else {
        item.classList.add('is-selected');
        setTimeout(()=>item.classList.remove('is-selected'), 280);
      }
    });
  });
});

/* ─── ICON UPGRADE (emoji -> svg) ─────────────────────── */
function iconSvg(path, viewBox = '0 0 24 24') {
  return '<svg class="ui-icon" viewBox="' + viewBox + '" aria-hidden="true" focusable="false">' + path + '</svg>';
}
const ICONS = {
  '🧠': iconSvg('<path d="M9 7a3 3 0 0 1 6 0v10a3 3 0 0 1-6 0Z"/><path d="M9 10H7a2 2 0 0 1 0-4h1"/><path d="M15 10h2a2 2 0 1 0 0-4h-1"/><path d="M9 14H6a2 2 0 1 0 0 4h3"/><path d="M15 14h3a2 2 0 1 1 0 4h-3"/>'),
  '⚡': iconSvg('<path d="M13 2 4 14h6l-1 8 9-12h-6z"/>'),
  '🔐': iconSvg('<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V8a4 4 0 1 1 8 0v3"/>'),
  '🔒': iconSvg('<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V8a4 4 0 1 1 8 0v3"/>'),
  '📡': iconSvg('<path d="M3 12a9 9 0 0 1 9-9"/><path d="M3 16A13 13 0 0 1 16 3"/><circle cx="7" cy="17" r="2"/><path d="m8.5 15.5 7-7"/>'),
  '🚀': iconSvg('<path d="M14 3c3 0 6 3 6 6-3 1-6 4-7 7-3 0-5-2-5-5 3-1 6-4 6-8Z"/><path d="m5 19-2 2 4-1"/><circle cx="15" cy="8" r="1.5"/>'),
  '💬': iconSvg('<path d="M4 5h16v10H9l-5 4z"/>'),
  '📈': iconSvg('<path d="M4 19h16"/><path d="m6 15 4-4 3 3 5-6"/>'),
  '🎯': iconSvg('<circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="4"/><circle cx="12" cy="12" r="1.5"/>'),
  '✂️': iconSvg('<circle cx="6" cy="6" r="2.5"/><circle cx="6" cy="18" r="2.5"/><path d="M8 7.5 20 4"/><path d="M8 16.5 20 20"/>'),
  '⏱️': iconSvg('<circle cx="12" cy="13" r="8"/><path d="M12 13V9"/><path d="m12 13 3 2"/><path d="M9 3h6"/><path d="m15.5 5.5 1.5-1.5"/>'),
  '🔗': iconSvg('<path d="M10 14 8 16a3 3 0 1 1-4-4l2-2"/><path d="m14 10 2-2a3 3 0 1 1 4 4l-2 2"/><path d="m9 15 6-6"/>'),
  '🗄️': iconSvg('<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M4 10h16"/><path d="M10 7h4"/><path d="M10 13h4"/><path d="M10 17h4"/>'),
  '📱': iconSvg('<rect x="7" y="2.5" width="10" height="19" rx="2"/><path d="M11 5h2"/><circle cx="12" cy="18.5" r="1"/>'),
  '🛡️': iconSvg('<path d="M12 3 5 6v5c0 5 3.5 8 7 10 3.5-2 7-5 7-10V6z"/><path d="m9 12 2 2 4-4"/>'),
  '🗂️': iconSvg('<path d="M3 7h7l2 2h9v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M3 7V5a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v2"/>'),
  '🔧': iconSvg('<path d="m14 6 4 4"/><path d="m12 8 4-4"/><path d="m3 21 7-7"/><path d="M7 17 5 15"/>'),
  '📋': iconSvg('<rect x="6" y="4" width="12" height="17" rx="2"/><path d="M9 4.5h6v3H9z"/><path d="M9 12h6"/><path d="M9 16h6"/>'),
  '👥': iconSvg('<circle cx="9" cy="9" r="3"/><circle cx="17" cy="10" r="2.5"/><path d="M4 20a5 5 0 0 1 10 0"/><path d="M13 20a4 4 0 0 1 8 0"/>'),
  '🤝': iconSvg('<path d="M4 10 8 6l4 4 4-4 4 4"/><path d="M8 14h8"/><path d="M7 16h10"/><path d="M9 18h6"/>'),
  '🔄': iconSvg('<path d="M20 7v5h-5"/><path d="M4 17v-5h5"/><path d="M7 7a7 7 0 0 1 10 1"/><path d="M17 17a7 7 0 0 1-10-1"/>'),
  '📊': iconSvg('<path d="M4 20h16"/><path d="M7 17v-6"/><path d="M12 17V8"/><path d="M17 17v-3"/>'),
  '✓': iconSvg('<path d="m5 12 4 4 10-10"/>'),
  'USR': iconSvg('<circle cx="12" cy="8" r="3"/><path d="M6 20a6 6 0 0 1 12 0"/>'),
  '☰': iconSvg('<path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h16"/>'),
  '✕': iconSvg('<path d="M6 6 18 18"/><path d="M18 6 6 18"/>'),
  'OA': iconSvg('<circle cx="12" cy="12" r="8"/><path d="M9 9h6v6H9z"/>'),
  'AN': iconSvg('<path d="M5 19 12 5l7 14"/><path d="M8 14h8"/>'),
  'G': iconSvg('<circle cx="12" cy="12" r="8"/><path d="M12 12h4"/><path d="M16 12v4"/>'),
  'n8': iconSvg('<path d="M5 7v10"/><path d="M19 7v10"/><path d="M5 7h5l4 5 5-5h0"/>'),
  'PG': iconSvg('<ellipse cx="12" cy="6" rx="6" ry="2.5"/><path d="M6 6v8c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5V6"/>'),
  'LC': iconSvg('<path d="M7 5h10v4H7z"/><path d="M5 11h14v8H5z"/><path d="M9 15h6"/>'),
  'WA': iconSvg('<path d="M12 4a8 8 0 0 0-6.8 12.2L4 20l4-1.1A8 8 0 1 0 12 4z"/><path d="M9 10.5c1.5 2.2 3.1 3.3 5.2 4"/>'),
  'SL': iconSvg('<path d="M9 4h3v5H9z"/><path d="M4 9h5v3H4z"/><path d="M12 15h3v5h-3z"/><path d="M15 12h5v3h-5z"/>'),
  'GS': iconSvg('<rect x="6" y="4" width="12" height="16" rx="2"/><path d="M9 9h6"/><path d="M9 13h6"/><path d="M9 17h4"/>'),
  'WH': iconSvg('<path d="M12 4v16"/><path d="M4 12h16"/><circle cx="12" cy="12" r="8"/>'),
  'CR': iconSvg('<path d="M5 6h14v12H5z"/><path d="M9 10h6"/><path d="M9 14h4"/>'),
  'ER': iconSvg('<rect x="5" y="5" width="14" height="14" rx="2"/><path d="M9 9h6"/><path d="M9 13h6"/><path d="M9 17h3"/>')
};
document.querySelectorAll('.val-icon,.feat-icon-wrap,.hb-icon,.fb-icon,.sb-icon,.tc-icon,.int-icon,.tl-icon,.av-user,.form-success-icon,.menu-toggle,.drawer-close').forEach(el=>{
  const key = el.textContent.trim();
  if (ICONS[key]) el.innerHTML = ICONS[key];
});

/* ─── TABS ────────────────────────────────────────────── */
function switchTab(id, btn) {
  document.querySelectorAll('.tab-pane').forEach(p=>p.classList.remove('active'));
  document.querySelectorAll('.tn').forEach(b=>b.classList.remove('active'));
  document.querySelectorAll('.tab-shot').forEach(s=>s.classList.remove('active'));
  const pane = document.getElementById('tab-'+id);
  if(pane) pane.classList.add('active');
  const shot = document.querySelector('.tab-shot[data-shot="'+id+'"]');
  if(shot) shot.classList.add('active');
  if(btn) btn.classList.add('active');
}

/* ─── STEPS ───────────────────────────────────────────── */
const stepData = [
  { label:'agent.config.ts', lines:['<span class="ck">const</span> agent = <span class="ck">new</span> FluxoAgent({','  <span class="cs">name</span>: <span class="cv">"Link Jurídico"</span>,','  <span class="cs">model</span>: <span class="cv">"gpt-4o"</span>,','  <span class="cs">rag</span>: <span class="ck">true</span>,','  <span class="cs">channels</span>: [<span class="cv">"chat"</span>],','  <span class="cs">tools</span>: [crm, docs, erp],','  <span class="cs">audit</span>: <span class="ck">true</span>,','});<span class="cc"> // diagnóstico completo</span>'], tags:['Mapeamento','Processos','ROI estimado','Gargalos'] },
  { label:'persona.config.ts', lines:['<span class="ck">const</span> persona = {','  <span class="cs">role</span>: <span class="cv">"assistente jurídico"</span>,','  <span class="cs">scope</span>: [<span class="cv">"contratos"</span>],','  <span class="cs">canDo</span>: [<span class="cv">"read"</span>, <span class="cv">"summarize"</span>],','  <span class="cs">cannotDo</span>: [<span class="cv">"delete"</span>],','  <span class="cs">version</span>: <span class="cv">"v2.4"</span>,','}; <span class="cc">// persona definida</span>'], tags:['Persona','Permissões','Tools','Canais'] },
  { label:'integrations.ts', lines:['<span class="ck">await</span> connect({','  <span class="cs">crm</span>: <span class="cv">"salesforce"</span>,','  <span class="cs">erp</span>: <span class="cv">"sap"</span>,','  <span class="cs">docs</span>: <span class="cv">"sharepoint"</span>,','  <span class="cs">channels</span>: [<span class="cv">"whatsapp"</span>],','  <span class="cs">auth</span>: <span class="cv">"oauth2"</span>,','}); <span class="cc">// 5 sistemas ✓</span>'], tags:['APIs','CRM','ERP','WhatsApp'] },
  { label:'deploy.sh', lines:['<span class="ck">$</span> <span class="cv">fluxo deploy</span> --env=prod','  <span class="c-ok">✓ Health check passed</span>','  <span class="c-ok">✓ Audit log active</span>','  <span class="c-ok">✓ LGPD compliant</span>','  <span class="c-ok">✓ Monitoring enabled</span>','<span class="ck">$</span> Agent live at <span class="cv">link.empresa.com</span>'], tags:['Deploy','Monitoramento','Evolução contínua'] }
];
function activateStep(el, idx) {
  document.querySelectorAll('.step').forEach(s=>s.classList.remove('active'));
  el.classList.add('active');
  const body=document.getElementById('code-body'), label=document.getElementById('code-label'), tags=document.getElementById('code-tags'), d=stepData[idx];
  if(!body||!label||!tags) return;
  body.style.opacity='0';
  body.style.transition='opacity 0.25s';
  setTimeout(()=>{
    label.textContent=d.label;
    body.innerHTML=d.lines.map((l,i)=>'<div class="cl"><span class="ln">'+(i+1)+'</span><span>'+l+'</span></div>').join('');
    tags.innerHTML=d.tags.map(t=>'<div class="ctag">'+t+'</div>').join('');
    body.style.opacity='1';
  },200);
}

/* ─── COUNTERS ────────────────────────────────────────── */
function animateCounter(el) {
  const target=parseInt(el.dataset.target), suffix=el.dataset.suffix, dur=1800, start=performance.now();
  (function tick(now){
    const p=Math.min((now-start)/dur,1), ease=1-Math.pow(1-p,3);
    el.textContent=Math.floor(ease*target)+suffix; if(p<1) requestAnimationFrame(tick);
  })(performance.now());
}
const ctrObs=new IntersectionObserver(entries=>{
  entries.forEach(e=>{ if(e.isIntersecting){ document.querySelectorAll('[data-target]').forEach(animateCounter); ctrObs.disconnect(); } });
},{threshold:.4});
const metricsEl=document.querySelector('.metrics'); if(metricsEl) ctrObs.observe(metricsEl);

/* ─── MAGNETIC BUTTONS ────────────────────────────────── */
document.querySelectorAll('.btn-primary').forEach(btn=>{
  btn.addEventListener('mousemove', e=>{
    const r=btn.getBoundingClientRect();
    btn.style.transform='translate('+(e.clientX-r.left-r.width/2)*.18+'px,'+(e.clientY-r.top-r.height/2)*.18+'px) translateY(-3px) scale(1.03)';
  });
  btn.addEventListener('mouseleave',()=>{ btn.style.transform=''; });
});

/* ─── RIPPLE ──────────────────────────────────────────── */
const rs=document.createElement('style');
rs.textContent='@keyframes rippleAnim{to{transform:scale(60);opacity:0}}';
document.head.appendChild(rs);
document.addEventListener('click', e=>{
  const btn=e.target.closest('.btn');
  if(!btn) return;
  const r=btn.getBoundingClientRect(), rp=document.createElement('div');
  rp.style.cssText='position:absolute;border-radius:50%;background:rgba(255,255,255,.2);width:4px;height:4px;left:'+(e.clientX-r.left-2)+'px;top:'+(e.clientY-r.top-2)+'px;transform:scale(0);animation:rippleAnim .6s ease-out forwards;pointer-events:none;z-index:10;';
  btn.appendChild(rp); setTimeout(()=>rp.remove(),700);
});

/* ─── 3D TILT ─────────────────────────────────────────── */
document.querySelectorAll('.tc-card,.mc-card').forEach(card=>{
  card.addEventListener('mousemove', e=>{
    const r=card.getBoundingClientRect(), x=(e.clientX-r.left)/r.width-.5, y=(e.clientY-r.top)/r.height-.5;
    card.style.transition='transform 0.1s';
    card.style.transform='translateY(-5px) perspective(600px) rotateX('+(-y*5)+'deg) rotateY('+(x*6)+'deg)';
  });
  card.addEventListener('mouseleave',()=>{ card.style.transition='transform 0.5s var(--ease-out)'; card.style.transform=''; });
});

/* ─── MOBILE DRAWER ───────────────────────────────────── */
const drawer = document.getElementById('mobile-drawer');
const drawerPanel = document.getElementById('drawer-panel');
const drawerBackdrop = document.getElementById('drawer-backdrop');
const menuToggleBtn = document.getElementById('menu-toggle');
const drawerCloseBtn = document.getElementById('drawer-close-btn');
const drawerLinks = drawer ? Array.from(drawer.querySelectorAll('a,button,[tabindex]:not([tabindex="-1"])')) : [];
const drawerSectionLinks = drawer ? Array.from(drawer.querySelectorAll('.drawer-link[data-section]')) : [];
const drawerFluxText = document.getElementById('drawer-flux-text');
let lastFocusedElement = null;
let touchStartX = 0;
let touchCurrentX = 0;
let isDraggingDrawer = false;
let drawerObserver = null;
const drawerFluxTips = {
  value: 'Comece por Benefícios para enxergar os ganhos de eficiência e custo logo no início.',
  how: 'Em Como Funciona, você entende o método de implantação com segurança e governança.',
  features: 'Recursos mostra os controles operacionais que reduzem risco e aumentam escala.',
  interactive: 'Plataforma é o melhor ponto para ver o fluxo real e sentir o nível de automação.',
  testimonials: 'Cases traz prova de resultado e contexto de aplicação no mundo real.',
  faq: 'No FAQ você resolve dúvidas práticas sobre prazo, governança, integrações, segurança e ROI.',
  newsletter: 'Na Newsletter você recebe conteúdos aplicáveis para manter evolução contínua da operação.',
  blog: 'No Blog, você aprofunda estratégia e boas práticas para evoluir com consistência.'
};

function isDrawerOpen() {
  return drawer && drawer.classList.contains('open');
}
function setDrawerState(open) {
  if (!drawer || !menuToggleBtn) return;
  drawer.classList.toggle('open', open);
  drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
  menuToggleBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
  document.body.style.overflow = open ? 'hidden' : '';
  document.body.classList.toggle('drawer-open', open);
  if (!open && drawerPanel) drawerPanel.style.transform = '';
  if (!open && drawerBackdrop) drawerBackdrop.style.opacity = '';
}
function openDrawer() {
  if (!drawer) return;
  lastFocusedElement = document.activeElement;
  setDrawerState(true);
  window.setTimeout(() => {
    if (drawerCloseBtn) drawerCloseBtn.focus();
  }, 120);
}
function closeDrawer() {
  if (!drawer) return;
  setDrawerState(false);
  if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
    lastFocusedElement.focus();
  } else if (menuToggleBtn) {
    menuToggleBtn.focus();
  }
}
function toggleNav() {
  isDrawerOpen() ? closeDrawer() : openDrawer();
}
function trapDrawerFocus(event) {
  if (!isDrawerOpen() || event.key !== 'Tab' || drawerLinks.length === 0) return;
  const first = drawerLinks[0];
  const last = drawerLinks[drawerLinks.length - 1];
  if (event.shiftKey && document.activeElement === first) {
    event.preventDefault();
    last.focus();
  } else if (!event.shiftKey && document.activeElement === last) {
    event.preventDefault();
    first.focus();
  }
}
function updateDrawerFluxGuide(sectionId) {
  if (!drawerFluxText) return;
  drawerFluxText.textContent = drawerFluxTips[sectionId] || 'Navegue pelas seções para descobrir recomendações rápidas do Flux.';
}

if (menuToggleBtn) menuToggleBtn.addEventListener('click', toggleNav);
if (drawerCloseBtn) drawerCloseBtn.addEventListener('click', closeDrawer);
if (drawerBackdrop) drawerBackdrop.addEventListener('click', closeDrawer);
if (drawer) {
  drawer.querySelectorAll('a').forEach(link => link.addEventListener('click', closeDrawer));
}
document.addEventListener('keydown', e => {
  if (e.key === 'Escape' && isDrawerOpen()) closeDrawer();
  trapDrawerFocus(e);
});
window.addEventListener('resize', () => {
  if (window.innerWidth > 768 && isDrawerOpen()) closeDrawer();
});
if (drawerPanel) {
  drawerPanel.addEventListener('touchstart', e => {
    touchStartX = e.changedTouches[0].clientX;
    touchCurrentX = touchStartX;
    isDraggingDrawer = false;
  }, { passive: true });
  drawerPanel.addEventListener('touchmove', e => {
    if (!isDrawerOpen()) return;
    touchCurrentX = e.changedTouches[0].clientX;
    const distance = Math.max(0, touchCurrentX - touchStartX);
    if (distance > 6) isDraggingDrawer = true;
    if (!isDraggingDrawer) return;
    drawerPanel.style.transform = `translateX(${Math.min(distance, 160)}px)`;
    if (drawerBackdrop) drawerBackdrop.style.opacity = `${Math.max(0.35, 1 - distance / 190)}`;
  }, { passive: true });
  drawerPanel.addEventListener('touchend', () => {
    const distance = touchCurrentX - touchStartX;
    if (distance > 72 && isDrawerOpen()) {
      closeDrawer();
    } else if (isDrawerOpen()) {
      drawerPanel.style.transform = '';
      if (drawerBackdrop) drawerBackdrop.style.opacity = '';
    }
    isDraggingDrawer = false;
  });
}

if ('IntersectionObserver' in window && drawerSectionLinks.length) {
  drawerObserver = new IntersectionObserver(entries => {
    let bestEntry = null;
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      if (!bestEntry || entry.intersectionRatio > bestEntry.intersectionRatio) bestEntry = entry;
    });
    if (!bestEntry) return;
    const activeId = bestEntry.target.id;
    drawerSectionLinks.forEach(link => {
      link.classList.toggle('is-active', link.dataset.section === activeId);
    });
    updateDrawerFluxGuide(activeId);
  }, { threshold: [0.35, 0.6, 0.85], rootMargin: '-20% 0px -55% 0px' });

  drawerSectionLinks.forEach(link => {
    const sectionId = link.dataset.section;
    const section = sectionId ? document.getElementById(sectionId) : null;
    if (section) drawerObserver.observe(section);
  });
}

/* ─── FLUX GUIDE ──────────────────────────────────────── */
const fluxGuide = document.getElementById('flux-guide');
const fluxBtn = document.getElementById('flux-avatar-btn');
const fluxText = document.getElementById('flux-text');
const fluxTag = document.getElementById('flux-section-tag');
const fluxLinkMain = document.getElementById('flux-link-main');
const fluxSectionMap = [
  { id:'hero', label:'Início', msg:'Aqui está sua proposta principal. As abas mostram cenários de uso em tempo real.', action:'#interactive', actionLabel:'Explorar cenários' },
  { id:'value', label:'Valor', msg:'Nesta seção você mostra os diferenciais da solução de forma objetiva e corporativa.', action:'#how', actionLabel:'Ver implementação' },
  { id:'how', label:'Como Funciona', msg:'Aqui você detalha o método: diagnóstico, desenho, integração e evolução contínua.', action:'#features', actionLabel:'Ver recursos' },
  { id:'features', label:'Recursos', msg:'Repare como os recursos reforçam governança, integração e controle operacional.', action:'#interactive', actionLabel:'Ver demo' },
  { id:'interactive', label:'Plataforma', msg:'Este é o bloco mais interativo. Mostra o agente em ação com exemplos claros.', action:'#testimonials', actionLabel:'Ver resultados' },
  { id:'testimonials', label:'Resultados', msg:'Prova social e métricas concretas fortalecem credibilidade e intenção de contato.', action:'blog/', actionLabel:'Ver blog' },
  { id:'blog', label:'Blog', msg:'Aqui você publica conteúdos estratégicos e educativos para fortalecer autoridade da marca.', action:'#form-section', actionLabel:'Falar com especialista' },
  { id:'faq', label:'FAQ', msg:'Aqui você encontra respostas objetivas sobre implantação, governança, integrações, segurança e ROI.', action:'#form-section', actionLabel:'Tirar dúvidas com especialista' },
  { id:'form-section', label:'Contato', msg:'Essa é a conversão principal. Formulário curto e proposta de resposta rápida.', action:'#cta', actionLabel:'Ver CTA final' },
  { id:'cta', label:'Próximo Passo', msg:'Fechamento da narrativa com CTA direto para conversa comercial.', action:'#footer', actionLabel:'Ver rodapé completo' },
  { id:'newsletter', label:'Newsletter', msg:'Aqui você se inscreve para receber insights semanais e playbooks de IA aplicados ao negócio real.', action:'#footer', actionLabel:'Ver rodapé completo' },
  { id:'footer', label:'Rodapé', msg:'Aqui você encontra atalhos estratégicos, conteúdo e canal direto com o time. Flux recomenda começar pela demonstração.', action:'#interactive', actionLabel:'Ir para demo' }
];
let fluxActiveIdx = 0;
let fluxTimer = null;

function setFluxContent(idx) {
  if (!fluxText || !fluxSectionMap[idx]) return;
  const item = fluxSectionMap[idx];
  fluxText.textContent = item.msg;
  if (fluxTag) fluxTag.textContent = item.label;
  if (fluxLinkMain) {
    fluxLinkMain.setAttribute('href', item.action);
    fluxLinkMain.textContent = item.actionLabel;
  }
}

function startFluxCycleFallback() {
  if (!fluxText || !fluxSectionMap.length) return;
  if (fluxTimer) clearInterval(fluxTimer);
  fluxTimer = setInterval(() => {
    fluxActiveIdx = (fluxActiveIdx + 1) % fluxSectionMap.length;
    setFluxContent(fluxActiveIdx);
  }, 5200);
}

if (fluxGuide && fluxBtn) {
  const isFluxMobile = () => window.matchMedia('(max-width: 768px), (hover: none) and (pointer: coarse)').matches;

  fluxBtn.addEventListener('click', e => {
    e.stopPropagation();
    if (isFluxMobile()) {
      fluxGuide.classList.toggle('mobile-open');
      fluxGuide.classList.remove('min');
      return;
    }
    fluxGuide.classList.toggle('min');
  });
  fluxGuide.addEventListener('mouseenter', () => {
    if (fluxTimer) clearInterval(fluxTimer);
  });
  fluxGuide.addEventListener('mouseleave', startFluxCycleFallback);
  document.addEventListener('click', e => {
    if (!isFluxMobile() || !fluxGuide.classList.contains('mobile-open')) return;
    if (!fluxGuide.contains(e.target)) fluxGuide.classList.remove('mobile-open');
  });
  window.addEventListener('resize', () => {
    if (!isFluxMobile()) fluxGuide.classList.remove('mobile-open');
  });
  setFluxContent(0);
  startFluxCycleFallback();

  const fluxObs = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const idx = fluxSectionMap.findIndex(s => s.id === entry.target.id);
        if (idx >= 0) {
          fluxActiveIdx = idx;
          setFluxContent(idx);
        }
      }
    });
  }, { threshold: 0.45 });
  fluxSectionMap.forEach(s => {
    const el = document.getElementById(s.id);
    if (el) fluxObs.observe(el);
  });
}

/* ─── FORM VALIDATION ─────────────────────────────────── */
function setFieldState(groupId, state) {
  const g=document.getElementById(groupId); if(!g) return;
  const inp=g.querySelector('.fl-input'); if(!inp) return;
  if (state === '') {
    g.classList.remove('has-error');
    inp.classList.remove('error','valid');
    inp.removeAttribute('aria-invalid');
    return;
  }
  g.classList.toggle('has-error', state==='error');
  inp.classList.toggle('error', state==='error');
  inp.classList.toggle('valid', state==='valid');
  inp.setAttribute('aria-invalid', state==='error' ? 'true' : 'false');
}
const contactForm = document.getElementById('contact-form');
const successModal = document.getElementById('success-modal');
function openSuccessModal() {
  if (!successModal) return;
  successModal.classList.add('show');
  successModal.setAttribute('aria-hidden','false');
  document.body.classList.add('modal-open');
}
function closeSuccessModal() {
  if (!successModal) return;
  successModal.classList.remove('show');
  successModal.setAttribute('aria-hidden','true');
  document.body.classList.remove('modal-open');
}
document.addEventListener('keydown', e => {
  if (e.key === 'Escape' && successModal && successModal.classList.contains('show')) closeSuccessModal();
});
if(contactForm) {
  const contactHp = document.getElementById('flux-contact-hp');
  contactForm.addEventListener('submit', async function(e){
    e.preventDefault();
    if (contactHp && contactHp.value.trim() !== '') contactHp.value = '';
    let valid=true;
    const nameInp=document.getElementById('f-name');
    const emailInp=document.getElementById('f-email');
    const phoneInp=document.getElementById('f-phone');
    const name=sanitizePersonName(nameInp.value).trim();
    const email=normalizeEmailInput(emailInp.value);
    const phoneDigits=digitsOnly(phoneInp.value);
    nameInp.value = name;
    emailInp.value = email;
    phoneInp.value = formatPhoneBRMask(phoneInp.value);
    if(name.length<2){ setFieldState('fg-name','error'); valid=false; } else setFieldState('fg-name','valid');
    if(!validateEmailStrict(email)){ setFieldState('fg-email','error'); valid=false; } else setFieldState('fg-email','valid');
    if(!validateBRPhoneDigits(phoneDigits)){ setFieldState('fg-phone','error'); valid=false; } else setFieldState('fg-phone','valid');
    if(!valid) return;
    const btn=this.querySelector('.form-submit');
    const btnLabel = btn?.querySelector('.form-submit-inner');
    const originalLabel = btnLabel?.textContent || 'Quero criar meu agente →';
    if (btn) btn.disabled = true;
    if (btnLabel) btnLabel.textContent = 'Enviando...';

    try {
      const response = await fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
      });

      let payload = null;
      try {
        payload = await response.clone().json();
      } catch (_) {
        payload = null;
      }

      if (!response.ok || payload?.ok === false) {
        throw new Error(payload?.message || 'Falha ao enviar contato.');
      }

      this.reset();
      ['fg-name','fg-email','fg-phone'].forEach(id => setFieldState(id, ''));
      openSuccessModal();
    } catch (_) {
      alert('Não foi possível enviar sua mensagem agora. Tente novamente em alguns instantes ou fale pelo WhatsApp.');
    } finally {
      if (btn) btn.disabled = false;
      if (btnLabel) btnLabel.textContent = originalLabel;
    }
  });
  try {
    const params = new URLSearchParams(window.location.search);
    if (params.get('mail') === 'ok') {
      openSuccessModal();
      const url = new URL(window.location.href);
      url.searchParams.delete('mail');
      window.history.replaceState({}, '', url.toString());
    }
  } catch (_) { /* noop */ }
  ['f-name','f-email','f-phone'].forEach(id=>{
    const inp=document.getElementById(id); if(!inp) return;
    inp.addEventListener('blur',()=>{
      if(id==='f-name') setFieldState('fg-name', sanitizePersonName(inp.value).trim().length>=2?'valid':'error');
      if(id==='f-email') setFieldState('fg-email', validateEmailStrict(normalizeEmailInput(inp.value))?'valid':'error');
      if(id==='f-phone') setFieldState('fg-phone', validateBRPhoneDigits(digitsOnly(inp.value))?'valid':'error');
    });
    inp.addEventListener('input',()=>{
      if (id === 'f-phone') {
        const masked = formatPhoneBRMask(inp.value);
        if (inp.value !== masked) inp.value = masked;
      }
      if (id === 'f-email') {
        const normalized = normalizeEmailInput(inp.value);
        if (inp.value !== normalized) inp.value = normalized;
      }
      if (id === 'f-name') {
        const cleanName = sanitizePersonName(inp.value);
        if (inp.value !== cleanName) inp.value = cleanName;
      }
      const gid = inp.closest('.fl-group')?.id;
      if (gid) setFieldState(gid, '');
    });
  });
}

