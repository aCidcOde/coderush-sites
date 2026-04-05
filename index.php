<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>CodeRush — Hub de Tecnologia</title>
  <meta name="description" content="CodeRush é um hub central de tecnologia que reúne múltiplas empresas e serviços especializados em vendas diretas, desenvolvimento de software, WordPress, automação com IA e design digital." />
  <meta name="keywords" content="CodeRush, cases de sucesso, software sob medida, vendas diretas, WordPress, automação com IA, Laravel, n8n, consultoria tecnológica, Brasil" />
  <meta name="robots" content="index, follow" />
  <link rel="stylesheet" href="css/site-tailwind.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet" />
  <style>
    html {
      scroll-behavior: smooth;
      scroll-padding-top: 6.25rem;
    }
    @media (prefers-reduced-motion: reduce) {
      html { scroll-behavior: auto; }
    }
    body { background: #020b1a; color: #f0f4ff; }

    /* Header — adesivo central (pill) + microinterações */
    .site-header {
      position: sticky;
      top: 0;
      z-index: 50;
      padding: 0.75rem 0.75rem 0.5rem;
      background: transparent;
      pointer-events: none;
    }
    @media (min-width: 640px) {
      .site-header { padding-left: 1rem; padding-right: 1rem; }
    }
    .site-header-pill {
      pointer-events: auto;
      position: relative;
      margin-left: auto;
      margin-right: auto;
      width: fit-content;
      max-width: calc(100% - 1rem);
      min-width: min(100%, 17.5rem);
      overflow: hidden;
      border-radius: 9999px;
      border: 1px solid rgba(255, 255, 255, 0.12);
      background: rgba(2, 11, 26, 0.72);
      backdrop-filter: blur(20px) saturate(1.25);
      -webkit-backdrop-filter: blur(20px) saturate(1.25);
      box-shadow:
        0 4px 6px -1px rgba(0, 0, 0, 0.22),
        0 16px 40px -12px rgba(0, 0, 0, 0.45),
        0 0 0 1px rgba(0, 74, 173, 0.07),
        inset 0 1px 0 rgba(255, 255, 255, 0.09);
      transition: border-color 0.45s ease, box-shadow 0.5s cubic-bezier(0.22, 1, 0.36, 1), background 0.45s ease, transform 0.5s cubic-bezier(0.22, 1, 0.36, 1);
      animation: header-sticker-float 5.5s ease-in-out infinite;
    }
    @keyframes header-sticker-float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-4px); }
    }
    .site-header-pill::before {
      content: "";
      position: absolute;
      left: 8%;
      right: 8%;
      top: 0;
      height: 1px;
      border-radius: 9999px;
      background: linear-gradient(90deg, transparent 0%, rgba(0, 74, 173, 0.45) 22%, rgba(96, 165, 250, 0.55) 50%, rgba(0, 74, 173, 0.45) 78%, transparent 100%);
      background-size: 200% 100%;
      opacity: 0.95;
      pointer-events: none;
      animation: header-top-shine 9s ease-in-out infinite;
    }
    @keyframes header-top-shine {
      0%, 100% { background-position: 0% center; opacity: 0.7; }
      50% { background-position: 100% center; opacity: 1; }
    }
    .site-header-pill::after {
      content: "";
      position: absolute;
      inset: 0;
      border-radius: inherit;
      background: radial-gradient(ellipse 85% 120% at 50% -40%, rgba(0, 74, 173, 0.16), transparent 55%);
      pointer-events: none;
      opacity: 0.75;
      transition: opacity 0.45s ease;
    }
    .site-header.is-scrolled .site-header-pill {
      animation: none;
      transform: translateY(0);
      border-color: rgba(255, 255, 255, 0.14);
      background: rgba(2, 11, 26, 0.88);
      box-shadow:
        0 8px 12px -4px rgba(0, 0, 0, 0.35),
        0 20px 50px -16px rgba(0, 0, 0, 0.55),
        0 0 0 1px rgba(0, 74, 173, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }
    .site-header.is-scrolled .site-header-pill::after {
      opacity: 0.4;
    }
    .site-header-inner {
      position: relative;
      z-index: 1;
    }
    .header-brand-mark {
      position: relative;
      display: flex;
      height: 2.25rem;
      width: 2.25rem;
      align-items: center;
      justify-content: center;
      border-radius: 0.625rem;
      background: linear-gradient(145deg, #004aad 0%, #1d4ed8 55%, #2563eb 100%);
      font-family: Montserrat, system-ui, sans-serif;
      font-size: 0.8125rem;
      font-weight: 800;
      color: #fff;
      box-shadow:
        0 0 0 1px rgba(255, 255, 255, 0.12) inset,
        0 10px 28px -10px rgba(0, 74, 173, 0.55);
      transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.35s ease;
    }
    .header-brand-mark::after {
      content: "";
      position: absolute;
      inset: -2px;
      border-radius: inherit;
      background: linear-gradient(135deg, rgba(96, 165, 250, 0.5), transparent 45%, rgba(0, 74, 173, 0.4));
      opacity: 0;
      z-index: -1;
      transition: opacity 0.35s ease;
    }
    .header-brand:hover .header-brand-mark {
      transform: translateY(-1px) scale(1.04);
      box-shadow:
        0 0 0 1px rgba(255, 255, 255, 0.18) inset,
        0 14px 36px -8px rgba(0, 74, 173, 0.6);
    }
    .header-brand:hover .header-brand-mark::after {
      opacity: 1;
    }
    .header-brand-text {
      font-family: Montserrat, system-ui, sans-serif;
      font-size: 1.125rem;
      font-weight: 800;
      letter-spacing: -0.03em;
      color: rgba(255, 255, 255, 0.96);
      transition: color 0.25s ease;
    }
    .header-brand-text span {
      font-weight: 700;
      color: rgba(147, 197, 253, 0.92);
      transition: color 0.25s ease;
    }
    .header-brand:hover .header-brand-text span {
      color: rgba(186, 230, 253, 0.98);
    }
    .header-nav-link {
      position: relative;
      padding: 0.4rem 0.2rem;
      font-size: 0.8125rem;
      font-weight: 500;
      letter-spacing: 0.01em;
      color: rgba(255, 255, 255, 0.68);
      transition: color 0.22s ease;
    }
    .header-nav-link::after {
      content: "";
      position: absolute;
      left: 50%;
      bottom: 0.05rem;
      width: 0;
      height: 2px;
      border-radius: 2px;
      background: linear-gradient(90deg, #004aad, #60a5fa);
      transform: translateX(-50%);
      transition: width 0.32s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.2s ease;
      opacity: 0.9;
    }
    .header-nav-link:hover {
      color: rgba(255, 255, 255, 0.98);
    }
    .header-nav-link:hover::after {
      width: calc(100% - 0.1rem);
    }
    .header-nav-link:focus-visible::after {
      width: calc(100% - 0.1rem);
      opacity: 1;
    }
    .header-btn-secondary {
      position: relative;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      border-radius: 9999px;
      border: 1px solid rgba(255, 255, 255, 0.22);
      padding: 0.5rem 1rem;
      font-size: 0.8125rem;
      font-weight: 600;
      color: rgba(255, 255, 255, 0.92);
      transition: border-color 0.25s ease, background 0.25s ease, transform 0.25s ease, box-shadow 0.3s ease;
    }
    .header-btn-secondary:hover {
      border-color: rgba(147, 197, 253, 0.45);
      background: rgba(255, 255, 255, 0.06);
      transform: translateY(-1px);
      box-shadow: 0 8px 28px -12px rgba(0, 74, 173, 0.35);
    }
    .header-btn-primary {
      position: relative;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      border-radius: 9999px;
      padding: 0.5rem 1.15rem;
      font-size: 0.8125rem;
      font-weight: 600;
      color: #fff;
      background: linear-gradient(135deg, #004aad 0%, #1d4ed8 52%, #2563eb 100%);
      box-shadow: 0 4px 20px -6px rgba(0, 74, 173, 0.55), 0 0 0 1px rgba(255, 255, 255, 0.1) inset;
      transition: transform 0.25s ease, box-shadow 0.3s ease, filter 0.2s ease;
    }
    .header-btn-primary:hover {
      transform: translateY(-1px);
      filter: brightness(1.06);
      box-shadow: 0 10px 32px -8px rgba(0, 74, 173, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.14) inset;
    }
    .header-btn-primary-shine {
      position: absolute;
      inset: 0;
      transform: translateX(-100%);
      background: linear-gradient(100deg, transparent 0%, rgba(255, 255, 255, 0.18) 50%, transparent 100%);
      transition: transform 0.65s cubic-bezier(0.22, 1, 0.36, 1);
      pointer-events: none;
    }
    .header-btn-primary:hover .header-btn-primary-shine {
      transform: translateX(100%);
    }
    .header-menu-btn {
      position: relative;
      overflow: hidden;
      transition: border-color 0.25s ease, background 0.25s ease, transform 0.2s ease;
    }
    .header-menu-btn:hover {
      transform: scale(1.03);
    }
    .header-menu-btn:active {
      transform: scale(0.97);
    }
    .header-mobile-panel {
      pointer-events: auto;
      margin-left: auto;
      margin-right: auto;
      margin-top: 0.5rem;
      width: 100%;
      max-width: calc(100% - 1rem);
      border-radius: 1.25rem;
      box-shadow:
        0 12px 40px -16px rgba(0, 0, 0, 0.5),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
    }
    #mobile-menu:not(.hidden) {
      animation: header-mobile-open 0.4s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }
    @keyframes header-mobile-open {
      from {
        opacity: 0;
        transform: translateY(-6px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .header-mobile-link {
      position: relative;
      border-radius: 0.5rem;
      transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }
    .header-mobile-link:hover {
      transform: translateX(4px);
    }
    @media (prefers-reduced-motion: reduce) {
      .site-header-pill { animation: none !important; }
      .site-header-pill::before { animation: none; opacity: 0.9; }
      .site-header-pill,
      .site-header.is-scrolled .site-header-pill {
        transition: none;
      }
      .header-brand:hover .header-brand-mark {
        transform: none;
      }
      .header-nav-link::after {
        transition: none;
        width: 0;
      }
      .header-nav-link:hover::after {
        width: calc(100% - 0.1rem);
      }
      .header-btn-secondary:hover,
      .header-btn-primary:hover,
      .header-mobile-link:hover {
        transform: none;
      }
      .header-btn-primary:hover .header-btn-primary-shine {
        transform: translateX(-100%);
      }
      #mobile-menu:not(.hidden) {
        animation: none;
      }
    }

    .card-hover { transition: transform 0.25s ease, box-shadow 0.25s ease; }
    .card-hover:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(0,74,173,0.25); }

    /* Portfólio — cards */
    .portfolio-section { position: relative; }
    .portfolio-card {
      transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.3s ease, border-color 0.3s ease, background-color 0.3s ease;
    }
    .portfolio-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 24px 56px -16px rgba(0, 74, 173, 0.35);
    }
    .portfolio-card:focus-visible {
      outline: none;
      box-shadow: 0 0 0 2px #020b1a, 0 0 0 4px rgba(96, 165, 250, 0.55);
    }
    .portfolio-card-static { cursor: default; }

    /* Carrossel de clientes (marquee infinito) */
    .clients-strip { position: relative; }
    .clients-marquee {
      overflow: hidden;
      mask-image: linear-gradient(90deg, transparent, #000 6%, #000 94%, transparent);
      -webkit-mask-image: linear-gradient(90deg, transparent, #000 6%, #000 94%, transparent);
    }
    .clients-track {
      display: flex;
      width: max-content;
      animation: clients-marquee-scroll 55s linear infinite;
    }
    .clients-track--alt {
      animation-duration: 62s;
      animation-direction: reverse;
    }
    .clients-marquee:hover .clients-track {
      animation-play-state: paused;
    }
    @keyframes clients-marquee-scroll {
      0% { transform: translateX(0); }
      100% { transform: translateX(-50%); }
    }
    .clients-group {
      display: flex;
      align-items: stretch;
      gap: 1.25rem;
      padding-right: 1.25rem;
    }
    .clients-logo {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      white-space: nowrap;
      border-radius: 0.875rem;
      border: 1px solid rgba(255, 255, 255, 0.08);
      background: linear-gradient(145deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0.02));
      padding: 0.85rem 1.75rem;
      font-family: Montserrat, system-ui, sans-serif;
      font-size: 0.8125rem;
      font-weight: 700;
      letter-spacing: 0.14em;
      text-transform: uppercase;
      color: rgba(240, 244, 255, 0.38);
      transition: color 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease, transform 0.25s ease;
      box-shadow: 0 0 0 1px rgba(0, 74, 173, 0.08);
    }
    .clients-logo:hover {
      color: rgba(240, 244, 255, 0.65);
      border-color: rgba(96, 165, 250, 0.35);
      box-shadow: 0 12px 40px -20px rgba(0, 74, 173, 0.45);
      transform: translateY(-2px);
    }
    @media (prefers-reduced-motion: reduce) {
      .clients-marquee {
        mask-image: none;
        -webkit-mask-image: none;
      }
      .clients-track {
        animation: none !important;
        flex-wrap: wrap;
        justify-content: center;
        width: 100% !important;
        max-width: 56rem;
        margin-left: auto;
        margin-right: auto;
      }
      .clients-group-dup { display: none !important; }
    }

    /* FAQ — acordeão escuro */
    .faq-section { position: relative; }
    .faq-item {
      overflow: hidden;
      border-radius: 1rem;
      border: 1px solid rgba(255, 255, 255, 0.08);
      background: linear-gradient(165deg, rgba(15, 23, 42, 0.85) 0%, rgba(2, 11, 26, 0.98) 100%);
      box-shadow: 0 4px 24px -8px rgba(0, 0, 0, 0.45);
      transition: border-color 0.35s ease, box-shadow 0.35s ease, transform 0.35s ease;
    }
    .faq-item:hover {
      border-color: rgba(255, 255, 255, 0.12);
    }
    .faq-item[open] {
      border-color: rgba(59, 130, 246, 0.45);
      box-shadow:
        0 0 0 1px rgba(59, 130, 246, 0.12),
        0 16px 48px -12px rgba(0, 74, 173, 0.35);
    }
    .faq-summary {
      display: flex;
      cursor: pointer;
      list-style: none;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      padding: 1.1rem 1.25rem;
      font-family: Montserrat, system-ui, sans-serif;
      font-size: 0.9375rem;
      font-weight: 600;
      color: #f0f4ff;
      user-select: none;
    }
    .faq-summary::-webkit-details-marker,
    .faq-summary::marker {
      display: none;
    }
    .faq-summary:focus-visible {
      outline: none;
      box-shadow: inset 0 0 0 2px rgba(96, 165, 250, 0.45);
      border-radius: 0.75rem;
    }
    .faq-chevron {
      display: flex;
      flex-shrink: 0;
      align-items: center;
      justify-content: center;
      width: 2rem;
      height: 2rem;
      border-radius: 0.5rem;
      background: rgba(255, 255, 255, 0.06);
      color: rgba(147, 197, 253, 0.9);
      transition: transform 0.4s cubic-bezier(0.22, 1, 0.36, 1), background 0.25s ease;
    }
    .faq-item[open] .faq-chevron {
      transform: rotate(180deg);
      background: rgba(59, 130, 246, 0.2);
      color: #93c5fd;
    }
    .faq-panel {
      display: grid;
      grid-template-rows: 0fr;
      transition: grid-template-rows 0.45s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .faq-item[open] .faq-panel {
      grid-template-rows: 1fr;
    }
    .faq-panel-inner {
      min-height: 0;
      overflow: hidden;
    }
    .faq-answer {
      border-top: 1px solid rgba(255, 255, 255, 0.06);
      background: rgba(3, 7, 18, 0.85);
      padding: 0 1.25rem 1.15rem;
      padding-top: 0.75rem;
      font-size: 0.875rem;
      line-height: 1.65;
      color: rgba(226, 232, 240, 0.78);
    }
    .faq-reveal {
      opacity: 0;
      transform: translateY(0.75rem);
      transition: opacity 0.55s cubic-bezier(0.22, 1, 0.36, 1), transform 0.55s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .faq-section.faq-visible .faq-reveal {
      opacity: 1;
      transform: translateY(0);
    }
    .faq-d-1 { transition-delay: 0.04s; }
    .faq-d-2 { transition-delay: 0.1s; }
    .faq-d-3 { transition-delay: 0.16s; }
    .faq-d-4 { transition-delay: 0.22s; }
    .faq-d-5 { transition-delay: 0.28s; }
    .faq-d-6 { transition-delay: 0.34s; }
    @media (prefers-reduced-motion: reduce) {
      .faq-panel { transition: none !important; }
      .faq-item[open] .faq-chevron { transition: none; }
      .faq-section .faq-reveal {
        opacity: 1 !important;
        transform: none !important;
        transition: none !important;
      }
    }

    /* Chat flutuante — conversão */
    .chat-backdrop {
      position: fixed;
      inset: 0;
      z-index: 90;
      background: rgba(2, 11, 26, 0.55);
      backdrop-filter: blur(4px);
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.35s ease, visibility 0.35s ease;
    }
    .chat-backdrop.is-open {
      opacity: 1;
      visibility: visible;
    }
    /* Dock: voltar ao topo + chat — variáveis globais para alinhar o painel */
    :root {
      --fab-from-bottom: 2rem;
      --fab-gap: 0.75rem;
      --fab-back-size: 3.25rem;
      --fab-chat-size: 3.75rem;
    }
    @media (min-width: 480px) {
      :root { --fab-from-bottom: 2.25rem; }
    }
    .fab-dock {
      position: fixed;
      z-index: 101;
      right: 1rem;
      bottom: var(--fab-from-bottom);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: var(--fab-gap);
    }
    @media (min-width: 480px) {
      .fab-dock { right: 1.5rem; }
    }
    .chat-panel {
      position: fixed;
      z-index: 100;
      display: flex;
      flex-direction: column;
      right: 1rem;
      bottom: calc(var(--fab-from-bottom) + var(--fab-chat-size) + var(--fab-gap) + var(--fab-back-size) + 0.75rem);
      width: min(100vw - 2rem, 22rem);
      max-height: min(34rem, calc(100vh - 12rem));
      border-radius: 1.25rem;
      border: 1px solid rgba(255, 255, 255, 0.1);
      background: linear-gradient(165deg, rgba(15, 23, 42, 0.98) 0%, rgba(2, 11, 26, 0.99) 100%);
      box-shadow: 0 24px 64px -12px rgba(0, 0, 0, 0.55), 0 0 0 1px rgba(59, 130, 246, 0.08);
      transform: translateY(1rem) scale(0.96);
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
      transition: transform 0.4s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.35s ease, visibility 0.35s ease;
    }
    @media (min-width: 480px) {
      .chat-panel { width: min(100vw - 2rem, 24rem); right: 1.5rem; }
    }
    .chat-panel.is-open {
      transform: translateY(0) scale(1);
      opacity: 1;
      visibility: visible;
      pointer-events: auto;
    }
    .back-to-top-fab {
      display: flex;
      height: var(--fab-back-size, 3.25rem);
      width: var(--fab-back-size, 3.25rem);
      flex-shrink: 0;
      align-items: center;
      justify-content: center;
      border-radius: 9999px;
      border: 1px solid rgba(255, 255, 255, 0.14);
      background: linear-gradient(165deg, rgba(30, 41, 59, 0.95) 0%, rgba(15, 23, 42, 0.98) 100%);
      color: rgba(241, 245, 249, 0.95);
      box-shadow: 0 10px 36px -12px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.06) inset;
      transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease, opacity 0.25s ease;
    }
    .back-to-top-fab:hover {
      transform: translateY(-2px);
      border-color: rgba(96, 165, 250, 0.4);
      box-shadow: 0 14px 40px -10px rgba(0, 74, 173, 0.35), 0 0 0 1px rgba(147, 197, 253, 0.12) inset;
    }
    .back-to-top-fab:focus-visible {
      outline: none;
      box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.55), 0 10px 36px -12px rgba(0, 0, 0, 0.45);
    }
    .back-to-top-fab.is-at-top {
      opacity: 0.4;
      pointer-events: none;
    }
    .chat-launcher {
      position: relative;
      display: flex;
      height: var(--fab-chat-size, 3.75rem);
      width: var(--fab-chat-size, 3.75rem);
      flex-shrink: 0;
      align-items: center;
      justify-content: center;
      border-radius: 9999px;
      border: 1px solid rgba(255, 255, 255, 0.12);
      background: linear-gradient(145deg, #004aad 0%, #1d4ed8 50%, #2563eb 100%);
      color: #fff;
      box-shadow: 0 12px 40px -8px rgba(0, 74, 173, 0.55), 0 0 0 1px rgba(255, 255, 255, 0.08) inset;
      transition: transform 0.25s ease, box-shadow 0.25s ease;
      animation: chat-launcher-pulse 3s ease-in-out infinite;
    }
    .chat-launcher:hover {
      transform: scale(1.06);
      box-shadow: 0 16px 48px -6px rgba(0, 74, 173, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.12) inset;
    }
    .chat-launcher.is-open {
      animation: none;
    }
    @keyframes chat-launcher-pulse {
      0%, 100% { box-shadow: 0 12px 40px -8px rgba(0, 74, 173, 0.55), 0 0 0 1px rgba(255, 255, 255, 0.08) inset; }
      50% { box-shadow: 0 18px 52px -4px rgba(37, 99, 235, 0.5), 0 0 0 1px rgba(147, 197, 253, 0.15) inset; }
    }
    .chat-msg {
      max-width: 92%;
      border-radius: 1rem;
      padding: 0.65rem 0.85rem;
      font-size: 0.8125rem;
      line-height: 1.5;
    }
    .chat-msg--bot {
      align-self: flex-start;
      border: 1px solid rgba(255, 255, 255, 0.08);
      background: rgba(255, 255, 255, 0.06);
      color: rgba(226, 232, 240, 0.92);
    }
    .chat-msg--user {
      align-self: flex-end;
      background: linear-gradient(135deg, rgba(0, 74, 173, 0.9), rgba(37, 99, 235, 0.85));
      color: #fff;
    }
    .chat-msg.chat-typing {
      display: flex;
      align-items: center;
      gap: 0.35rem;
    }
    .chat-typing span {
      display: inline-block;
      width: 0.35rem;
      height: 0.35rem;
      margin: 0 0.1rem;
      border-radius: 9999px;
      background: rgba(148, 163, 184, 0.9);
      animation: chat-dot 1.2s ease-in-out infinite;
    }
    .chat-typing span:nth-child(2) { animation-delay: 0.15s; }
    .chat-typing span:nth-child(3) { animation-delay: 0.3s; }
    @keyframes chat-dot {
      0%, 80%, 100% { transform: translateY(0); opacity: 0.4; }
      40% { transform: translateY(-4px); opacity: 1; }
    }
    .chat-chip {
      display: inline-flex;
      cursor: pointer;
      align-items: center;
      border-radius: 9999px;
      border: 1px solid rgba(96, 165, 250, 0.35);
      background: rgba(59, 130, 246, 0.12);
      padding: 0.35rem 0.75rem;
      font-size: 0.6875rem;
      font-weight: 600;
      color: #93c5fd;
      transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
    }
    .chat-chip:hover {
      border-color: rgba(147, 197, 253, 0.55);
      background: rgba(59, 130, 246, 0.22);
      transform: translateY(-1px);
    }
    .chat-input {
      flex: 1;
      resize: none;
      border-radius: 0.75rem;
      border: 1px solid rgba(255, 255, 255, 0.12);
      background: rgba(3, 7, 18, 0.85);
      padding: 0.5rem 0.75rem;
      font-size: 0.8125rem;
      color: #f0f4ff;
      min-height: 2.5rem;
      max-height: 5rem;
    }
    .chat-input::placeholder { color: rgba(240, 244, 255, 0.35); }
    .chat-input:focus {
      outline: none;
      border-color: rgba(96, 165, 250, 0.5);
      box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }
    @media (prefers-reduced-motion: reduce) {
      .chat-launcher { animation: none !important; }
      .chat-panel { transition: opacity 0.2s ease; transform: none !important; }
      .chat-panel.is-open { transform: none !important; }
      .chat-typing span { animation: none !important; opacity: 0.7; }
      .back-to-top-fab:hover { transform: none; }
    }

    /* Contato — formulário */
    .contact-section { position: relative; overflow: hidden; }
    .contact-ambient {
      pointer-events: none;
      position: absolute;
      inset: 0;
      background:
        radial-gradient(ellipse 70% 45% at 15% 80%, rgba(0, 74, 173, 0.18), transparent 50%),
        radial-gradient(ellipse 50% 40% at 90% 20%, rgba(139, 92, 246, 0.12), transparent 45%);
    }
    .contact-reveal {
      opacity: 0;
      transform: translateY(1.5rem);
      transition: opacity 0.75s cubic-bezier(0.22, 1, 0.36, 1), transform 0.75s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .contact-section.contact-visible .contact-reveal {
      opacity: 1;
      transform: translateY(0);
    }
    .contact-form-delay-1 { transition-delay: 0.06s; }
    .contact-form-delay-2 { transition-delay: 0.14s; }
    .contact-input {
      width: 100%;
      border-radius: 0.75rem;
      border: 1px solid rgba(255, 255, 255, 0.12);
      background: rgba(255, 255, 255, 0.04);
      padding: 0.65rem 1rem;
      font-size: 0.9375rem;
      color: #f0f4ff;
      transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }
    .contact-input::placeholder { color: rgba(240, 244, 255, 0.35); }
    .contact-input:hover { border-color: rgba(255, 255, 255, 0.2); background: rgba(255, 255, 255, 0.06); }
    .contact-input:focus {
      outline: none;
      border-color: rgba(96, 165, 250, 0.55);
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }
    select.contact-input {
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m19.5 8.25-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 0.75rem center;
      background-size: 1rem 1rem;
      padding-right: 2.5rem;
    }
    select.contact-input::-ms-expand { display: none; }
    .contact-label { display: block; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.02em; color: rgba(240, 244, 255, 0.55); }
    @keyframes contact-btn-shine {
      0% { transform: translateX(-100%); }
      100% { transform: translateX(100%); }
    }
    .contact-submit-shine::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.12) 50%, transparent 60%);
      transform: translateX(-100%);
      animation: contact-btn-shine 2.5s ease-in-out infinite;
    }
    @media (prefers-reduced-motion: reduce) {
      .contact-section .contact-reveal {
        opacity: 1 !important;
        transform: none !important;
        transition: none !important;
      }
      .contact-submit-shine::after { animation: none; }
    }

    /* Footer — conversão + refinamento */
    .footer-site {
      position: relative;
      overflow: hidden;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      background: linear-gradient(180deg, rgba(2, 11, 26, 0.98) 0%, #020b1a 45%, #010812 100%);
    }
    .footer-top-glow {
      pointer-events: none;
      position: absolute;
      left: 0;
      right: 0;
      top: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(0, 74, 173, 0.5), rgba(96, 165, 250, 0.35), rgba(0, 74, 173, 0.5), transparent);
      opacity: 0.9;
    }
    .footer-ambient {
      pointer-events: none;
      position: absolute;
      inset: 0;
      background:
        radial-gradient(ellipse 55% 60% at 18% 100%, rgba(0, 74, 173, 0.14), transparent 52%),
        radial-gradient(ellipse 42% 48% at 92% 8%, rgba(139, 92, 246, 0.09), transparent 46%);
    }
    .footer-reveal {
      opacity: 0;
      transform: translateY(1rem);
      transition: opacity 0.65s cubic-bezier(0.22, 1, 0.36, 1), transform 0.65s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .footer-site.footer-visible .footer-reveal {
      opacity: 1;
      transform: translateY(0);
    }
    .footer-stagger-1 { transition-delay: 0.06s; }
    .footer-stagger-2 { transition-delay: 0.12s; }
    .footer-stagger-3 { transition-delay: 0.18s; }
    .footer-stagger-4 { transition-delay: 0.24s; }
    .footer-nav-link {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      color: rgba(255, 255, 255, 0.55);
      transition: color 0.2s ease, transform 0.2s ease;
    }
    .footer-nav-link:hover {
      color: rgba(255, 255, 255, 0.92);
      transform: translateX(3px);
    }
    .footer-eco-pill {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      border-radius: 9999px;
      border: 1px solid rgba(255, 255, 255, 0.12);
      background: rgba(255, 255, 255, 0.03);
      padding: 0.4rem 0.85rem;
      font-size: 0.6875rem;
      color: rgba(255, 255, 255, 0.55);
      transition: border-color 0.25s ease, background 0.25s ease, color 0.25s ease, transform 0.2s ease, box-shadow 0.25s ease;
    }
    .footer-eco-pill:hover {
      border-color: rgba(96, 165, 250, 0.45);
      background: rgba(0, 74, 173, 0.12);
      color: rgba(255, 255, 255, 0.88);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px -8px rgba(0, 74, 173, 0.35);
    }
    .footer-cta-primary {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.4rem;
      border-radius: 9999px;
      padding: 0.65rem 1.15rem;
      font-size: 0.8125rem;
      font-weight: 600;
      color: #fff;
      background: linear-gradient(135deg, #004AAD 0%, #1d4ed8 50%, #2563eb 100%);
      box-shadow: 0 10px 32px -10px rgba(0, 74, 173, 0.55);
      transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
    }
    .footer-cta-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 14px 40px -8px rgba(0, 74, 173, 0.65);
      filter: brightness(1.05);
    }
    .footer-cta-secondary {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.35rem;
      border-radius: 9999px;
      border: 1px solid rgba(255, 255, 255, 0.18);
      padding: 0.6rem 1rem;
      font-size: 0.8125rem;
      font-weight: 500;
      color: rgba(255, 255, 255, 0.85);
      transition: border-color 0.2s ease, background 0.2s ease, transform 0.2s ease;
    }
    .footer-cta-secondary:hover {
      border-color: rgba(96, 165, 250, 0.45);
      background: rgba(255, 255, 255, 0.06);
      transform: translateY(-2px);
    }
    @media (prefers-reduced-motion: reduce) {
      .footer-site .footer-reveal {
        opacity: 1 !important;
        transform: none !important;
        transition: none !important;
      }
      .footer-nav-link:hover,
      .footer-eco-pill:hover,
      .footer-cta-primary:hover,
      .footer-cta-secondary:hover {
        transform: none;
      }
    }

    /* Quem somos — sobre */
    .sobre-section { position: relative; overflow: hidden; }
    .sobre-ambient {
      pointer-events: none;
      position: absolute;
      inset: 0;
      background:
        radial-gradient(ellipse 65% 50% at 12% 25%, rgba(0, 74, 173, 0.18), transparent 55%),
        radial-gradient(ellipse 50% 42% at 88% 70%, rgba(139, 92, 246, 0.11), transparent 50%);
    }
    .sobre-reveal {
      opacity: 0;
      transform: translateY(1.25rem);
      transition: opacity 0.72s cubic-bezier(0.22, 1, 0.36, 1), transform 0.72s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .sobre-section.sobre-visible .sobre-reveal {
      opacity: 1;
      transform: translateY(0);
    }
    .sobre-d-1 { transition-delay: 0.04s; }
    .sobre-d-2 { transition-delay: 0.1s; }
    .sobre-d-3 { transition-delay: 0.16s; }
    .sobre-d-4 { transition-delay: 0.22s; }
    .sobre-d-5 { transition-delay: 0.28s; }
    .sobre-d-6 { transition-delay: 0.34s; }
    .sobre-d-7 { transition-delay: 0.4s; }
    .sobre-d-8 { transition-delay: 0.46s; }
    .sobre-stat {
      border-radius: 1rem;
      border: 1px solid rgba(255, 255, 255, 0.1);
      background: linear-gradient(165deg, rgba(255, 255, 255, 0.07) 0%, rgba(255, 255, 255, 0.02) 100%);
      transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1), border-color 0.3s ease, box-shadow 0.35s ease;
    }
    .sobre-stat:hover {
      transform: translateY(-5px);
      border-color: rgba(96, 165, 250, 0.35);
      box-shadow: 0 20px 48px -24px rgba(0, 74, 173, 0.45);
    }
    .sobre-pillar {
      border-radius: 1rem;
      border: 1px solid rgba(255, 255, 255, 0.09);
      background: rgba(255, 255, 255, 0.03);
      transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1), border-color 0.25s ease, background 0.25s ease;
    }
    .sobre-pillar:hover {
      transform: translateY(-4px);
      border-color: rgba(255, 255, 255, 0.16);
      background: rgba(255, 255, 255, 0.05);
    }
    .sobre-ring {
      pointer-events: none;
      position: absolute;
      right: -18%;
      top: 8%;
      width: min(42vw, 22rem);
      height: min(42vw, 22rem);
      border-radius: 9999px;
      border: 1px solid rgba(96, 165, 250, 0.12);
      opacity: 0.65;
      animation: sobre-ring-drift 22s ease-in-out infinite;
    }
    @keyframes sobre-ring-drift {
      0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.55; }
      50% { transform: translate(-3%, 4%) scale(1.03); opacity: 0.75; }
    }
    @media (prefers-reduced-motion: reduce) {
      .sobre-section .sobre-reveal {
        opacity: 1 !important;
        transform: none !important;
        transition: none !important;
      }
      .sobre-stat:hover,
      .sobre-pillar:hover {
        transform: none;
        box-shadow: none;
      }
      .sobre-ring { animation: none; opacity: 0.4; }
    }

    /* Cases — revelação no scroll + destaques */
    .cases-section { position: relative; }
    .cases-ambient {
      pointer-events: none;
      position: absolute;
      inset: 0;
      background:
        radial-gradient(ellipse 80% 50% at 50% -20%, rgba(0, 74, 173, 0.22), transparent 55%),
        radial-gradient(ellipse 60% 40% at 100% 50%, rgba(139, 92, 246, 0.12), transparent 50%);
    }
    .cases-reveal {
      opacity: 0;
      transform: translateY(1.75rem);
      transition: opacity 0.75s cubic-bezier(0.22, 1, 0.36, 1), transform 0.75s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .cases-section.cases-visible .cases-reveal {
      opacity: 1;
      transform: translateY(0);
    }
    .cases-d-1 { transition-delay: 0.05s; }
    .cases-d-2 { transition-delay: 0.12s; }
    .cases-d-3 { transition-delay: 0.2s; }
    .cases-d-4 { transition-delay: 0.28s; }
    .cases-d-5 { transition-delay: 0.36s; }
    .cases-d-6 { transition-delay: 0.44s; }
    .cases-d-7 { transition-delay: 0.52s; }
    .cases-d-8 { transition-delay: 0.6s; }
    .cases-card {
      transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.3s ease, border-color 0.3s ease;
    }
    .cases-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 20px 50px -20px rgba(0, 74, 173, 0.4);
    }
    @keyframes cases-shine {
      0% { background-position: 200% center; }
      100% { background-position: -200% center; }
    }
    .cases-featured-bar {
      background: linear-gradient(90deg, transparent, rgba(96, 165, 250, 0.35), transparent);
      background-size: 200% 100%;
      animation: cases-shine 8s linear infinite;
    }
    @media (prefers-reduced-motion: reduce) {
      .cases-section .cases-reveal {
        opacity: 1 !important;
        transform: none !important;
        transition: none !important;
      }
      .cases-card:hover { transform: none; box-shadow: none; }
      .cases-featured-bar { animation: none; }
    }

    /* Depoimentos */
    .testimonials-section { position: relative; }
    .testimonials-ambient {
      pointer-events: none;
      position: absolute;
      inset: 0;
      background:
        radial-gradient(ellipse 70% 55% at 50% 0%, rgba(245, 158, 11, 0.08), transparent 55%),
        radial-gradient(ellipse 50% 40% at 0% 80%, rgba(0, 74, 173, 0.12), transparent 50%),
        radial-gradient(ellipse 45% 35% at 100% 60%, rgba(139, 92, 246, 0.1), transparent 48%);
    }
    .testimonials-reveal {
      opacity: 0;
      transform: translateY(1.5rem);
      transition: opacity 0.75s cubic-bezier(0.22, 1, 0.36, 1), transform 0.75s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .testimonials-section.testimonials-visible .testimonials-reveal {
      opacity: 1;
      transform: translateY(0);
    }
    .t-d-1 { transition-delay: 0.05s; }
    .t-d-2 { transition-delay: 0.12s; }
    .t-d-3 { transition-delay: 0.2s; }
    .t-d-4 { transition-delay: 0.28s; }
    .t-d-5 { transition-delay: 0.36s; }
    .t-d-6 { transition-delay: 0.44s; }
    .testimonial-card {
      position: relative;
      overflow: hidden;
      border-radius: 1.25rem;
      border: 1px solid rgba(255, 255, 255, 0.1);
      background: linear-gradient(165deg, rgba(255, 255, 255, 0.06) 0%, rgba(255, 255, 255, 0.02) 100%);
      transition: transform 0.4s cubic-bezier(0.22, 1, 0.36, 1), border-color 0.35s ease, box-shadow 0.4s ease;
    }
    .testimonial-card::before {
      content: "";
      position: absolute;
      left: 0;
      right: 0;
      top: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(251, 191, 36, 0.45), rgba(96, 165, 250, 0.35), transparent);
      opacity: 0.75;
    }
    .testimonial-card:hover {
      transform: translateY(-6px);
      border-color: rgba(251, 191, 36, 0.22);
      box-shadow: 0 24px 56px -28px rgba(0, 74, 173, 0.35);
    }
    .testimonial-quote-deco {
      position: absolute;
      right: 1rem;
      top: 0.75rem;
      font-family: Georgia, "Times New Roman", serif;
      font-size: 4.5rem;
      line-height: 1;
      color: rgba(255, 255, 255, 0.04);
      pointer-events: none;
      animation: testimonial-quote-float 7s ease-in-out infinite;
    }
    @keyframes testimonial-quote-float {
      0%, 100% { transform: translateY(0) rotate(-2deg); opacity: 0.35; }
      50% { transform: translateY(-4px) rotate(1deg); opacity: 0.55; }
    }
    .testimonials-scroller {
      display: flex;
      gap: 1.25rem;
      overflow-x: auto;
      overscroll-behavior-x: contain;
      scroll-snap-type: x mandatory;
      scroll-padding-inline: 1rem;
      padding-bottom: 0.35rem;
      -webkit-overflow-scrolling: touch;
    }
    .testimonials-scroller::-webkit-scrollbar {
      height: 4px;
    }
    .testimonials-scroller::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.12);
      border-radius: 9999px;
    }
    @media (min-width: 1024px) {
      .testimonials-scroller {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        overflow: visible;
        scroll-snap-type: none;
        gap: 1.5rem;
        padding-bottom: 0;
      }
    }
    .testimonial-card-wrap {
      flex: 0 0 min(88vw, 22rem);
      scroll-snap-align: center;
    }
    @media (min-width: 640px) {
      .testimonial-card-wrap { flex-basis: min(70vw, 24rem); }
    }
    @media (min-width: 1024px) {
      .testimonial-card-wrap {
        flex: none;
        min-width: 0;
        scroll-snap-align: unset;
      }
    }
    @media (prefers-reduced-motion: reduce) {
      .testimonials-section .testimonials-reveal {
        opacity: 1 !important;
        transform: none !important;
        transition: none !important;
      }
      .testimonial-card:hover { transform: none; box-shadow: none; }
      .testimonial-quote-deco { animation: none; }
    }

    /* Hero — referência: mesh + grid + motion suave (landings modernas) */
    .hero-section { position: relative; min-height: min(92vh, 56rem); display: flex; flex-direction: column; justify-content: center; padding-top: 2.5rem; padding-bottom: 3.5rem; }
    @media (min-width: 768px) {
      .hero-section { padding-top: 3rem; padding-bottom: 4.5rem; min-height: min(90vh, 58rem); }
    }
    .hero-ambient { pointer-events: none; position: absolute; inset: 0; overflow: hidden; }
    .hero-grid {
      position: absolute; inset: -30%;
      background-image: radial-gradient(rgba(148, 163, 184, 0.11) 1px, transparent 1px);
      background-size: 28px 28px;
      mask-image: radial-gradient(ellipse 75% 65% at 50% 35%, #000 15%, transparent 72%);
      opacity: 0.55;
      animation: hero-grid-drift 70s linear infinite;
    }
    @keyframes hero-grid-drift {
      0% { transform: translate(0, 0); }
      100% { transform: translate(28px, 28px); }
    }
    .hero-blob {
      position: absolute;
      border-radius: 50%;
      filter: blur(100px);
      will-change: transform, opacity;
    }
    .hero-blob-1 {
      width: min(110vw, 52rem); height: min(110vw, 52rem);
      left: 50%; top: -18%;
      transform: translateX(-50%);
      background: radial-gradient(circle at 30% 30%, rgba(0, 74, 173, 0.55), rgba(56, 189, 248, 0.12) 45%, transparent 68%);
      opacity: 0.75;
      animation: hero-drift-a 24s ease-in-out infinite;
    }
    .hero-blob-2 {
      width: min(95vw, 40rem); height: min(95vw, 40rem);
      right: -12%; bottom: -5%;
      background: radial-gradient(circle at 60% 50%, rgba(139, 92, 246, 0.42), rgba(59, 130, 246, 0.1) 50%, transparent 65%);
      opacity: 0.65;
      animation: hero-drift-b 28s ease-in-out infinite;
    }
    .hero-blob-3 {
      width: min(70vw, 28rem); height: min(70vw, 28rem);
      left: -8%; top: 42%;
      background: radial-gradient(circle at 50% 50%, rgba(34, 211, 238, 0.28), transparent 62%);
      opacity: 0.5;
      animation: hero-drift-c 20s ease-in-out infinite;
    }
    @keyframes hero-drift-a {
      0%, 100% { transform: translateX(-50%) translate(0, 0) scale(1); }
      40% { transform: translateX(-50%) translate(2%, 5%) scale(1.06); }
      70% { transform: translateX(-50%) translate(-3%, 2%) scale(0.98); }
    }
    @keyframes hero-drift-b {
      0%, 100% { transform: translate(0, 0) scale(1); }
      50% { transform: translate(-4%, -6%) scale(1.08); }
    }
    @keyframes hero-drift-c {
      0%, 100% { transform: translate(0, 0); }
      50% { transform: translate(8%, -4%); }
    }
    .hero-vignette {
      position: absolute; inset: 0;
      background: radial-gradient(ellipse 90% 55% at 50% 0%, transparent 0%, rgba(2, 11, 26, 0.35) 55%, rgba(2, 11, 26, 0.92) 100%);
      pointer-events: none;
    }
    .hero-noise {
      position: absolute; inset: 0; opacity: 0.035; mix-blend-mode: overlay;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
      pointer-events: none;
    }

    .gradient-text {
      background: linear-gradient(120deg, #60a5fa 0%, #a78bfa 35%, #38bdf8 55%, #818cf8 75%, #60a5fa 100%);
      background-size: 220% auto;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      animation: hero-gradient-shift 10s ease-in-out infinite;
    }
    @keyframes hero-gradient-shift {
      0%, 100% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
    }

    .hero-badge {
      display: inline-flex; align-items: center; gap: 0.5rem;
      border: 1px solid rgba(96, 165, 250, 0.35);
      background: linear-gradient(135deg, rgba(59, 130, 246, 0.18), rgba(139, 92, 246, 0.1));
      box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.04) inset, 0 12px 40px -12px rgba(0, 74, 173, 0.35);
      animation: hero-badge-glow 4.5s ease-in-out infinite;
    }
    @keyframes hero-badge-glow {
      0%, 100% { box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.04) inset, 0 12px 40px -12px rgba(0, 74, 173, 0.35); border-color: rgba(96, 165, 250, 0.35); }
      50% { box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.06) inset, 0 16px 48px -8px rgba(59, 130, 246, 0.45); border-color: rgba(147, 197, 253, 0.45); }
    }

    @keyframes hero-reveal {
      from { opacity: 0; transform: translateY(1.5rem); }
      to { opacity: 1; transform: translateY(0); }
    }
    .hero-reveal-item {
      opacity: 0;
      animation: hero-reveal 0.95s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }
    .hero-delay-1 { animation-delay: 0.06s; }
    .hero-delay-2 { animation-delay: 0.18s; }
    .hero-delay-3 { animation-delay: 0.32s; }
    .hero-delay-4 { animation-delay: 0.44s; }
    .hero-delay-5 { animation-delay: 0.56s; }

    @keyframes hero-bounce-soft {
      0%, 100% { transform: translateY(0); opacity: 0.55; }
      50% { transform: translateY(6px); opacity: 1; }
    }
    .hero-scroll-hint svg { animation: hero-bounce-soft 2.4s ease-in-out infinite; }

    @media (prefers-reduced-motion: reduce) {
      .hero-grid, .hero-blob-1, .hero-blob-2, .hero-blob-3 { animation: none !important; }
      .gradient-text { animation: none !important; background-size: 100% auto; background-position: 0 0; }
      .hero-badge { animation: none !important; }
      .hero-reveal-item { opacity: 1 !important; animation: none !important; transform: none !important; }
      .hero-scroll-hint svg { animation: none !important; }
      .portfolio-card:hover { transform: none; box-shadow: none; }
      .cases-card:hover { transform: none; box-shadow: none; }
      .contact-section .contact-reveal { opacity: 1 !important; transform: none !important; }
      .sobre-section .sobre-reveal { opacity: 1 !important; transform: none !important; }
      .testimonials-section .testimonials-reveal { opacity: 1 !important; transform: none !important; }
      .footer-site .footer-reveal { opacity: 1 !important; transform: none !important; }
    }
  </style>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "ItemList",
    "name": "Cases de sucesso — ecossistema CodeRush",
    "description": "Destaques de projetos em vendas diretas, desenvolvimento de software, WordPress e automação com IA no Brasil.",
    "numberOfItems": 4,
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "item": {
          "@type": "CreativeWork",
          "name": "Plataforma nacional de vendas diretas e MMN",
          "abstract": "Escala de rede, comissionamento e lojas integradas com alta disponibilidade.",
          "keywords": "vendas diretas, MMN, plataforma, integração"
        }
      },
      {
        "@type": "ListItem",
        "position": 2,
        "item": {
          "@type": "CreativeWork",
          "name": "Software sob medida com IA e integrações",
          "abstract": "Backoffice, APIs e entregas iterativas com stack Laravel e automações.",
          "keywords": "Laravel, IA, software sob medida, API"
        }
      },
      {
        "@type": "ListItem",
        "position": 3,
        "item": {
          "@type": "CreativeWork",
          "name": "WordPress corporativo com ERP e pagamentos",
          "abstract": "E-commerce e catálogos B2B com sincronização de estoque e pedidos.",
          "keywords": "WordPress, WooCommerce, ERP, B2B"
        }
      },
      {
        "@type": "ListItem",
        "position": 4,
        "item": {
          "@type": "CreativeWork",
          "name": "Automação comercial e agentes com n8n",
          "abstract": "Fluxos inteligentes ligando CRM, mensageria e IA aplicada.",
          "keywords": "n8n, automação, IA, CRM"
        }
      }
    ]
  }
  </script>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
      {
        "@type": "Question",
        "name": "O que é o ecossistema CodeRush?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "É um hub que reúne empresas de tecnologia com focos diferentes — vendas diretas, software, WordPress e automação com IA — compartilhando processos e visão de entrega."
        }
      },
      {
        "@type": "Question",
        "name": "Como escolho qual empresa do grupo fala com o meu projeto?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Pelo tipo de necessidade: MMN e plataforma de vendas; desenvolvimento sob medida; WordPress e lojas; ou automação e IA. O formulário de contato também ajuda a direcionar seu briefing."
        }
      },
      {
        "@type": "Question",
        "name": "Os sites e sistemas são seguros?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "As soluções seguem boas práticas de segurança, HTTPS e controles de acesso conforme o escopo de cada projeto. Detalhes são alinhados no discovery com a equipe."
        }
      },
      {
        "@type": "Question",
        "name": "Como funciona o contato comercial?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Você pode usar o formulário nesta página, o e-mail contato@coderush.com.br ou o site da empresa do grupo mais alinhada ao seu caso. Resposta humana, sem fila automática genérica."
        }
      },
      {
        "@type": "Question",
        "name": "Vocês trabalham com IA e automação?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Sim. Há times dedicados a software com IA, integrações, WordPress e fluxos com ferramentas como n8n, além de consultoria em automação comercial."
        }
      },
      {
        "@type": "Question",
        "name": "Atendem empresas fora do Brasil?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "O foco principal é o mercado brasileiro; projetos internacionais podem ser avaliados caso a caso, conforme fuso, idioma e requisitos legais."
        }
      }
    ]
  }
  </script>
  <noscript>
    <style>
      .cases-section .cases-reveal { opacity: 1 !important; transform: none !important; transition: none !important; }
      .contact-section .contact-reveal { opacity: 1 !important; transform: none !important; transition: none !important; }
      .faq-section .faq-reveal { opacity: 1 !important; transform: none !important; transition: none !important; }
      .testimonials-section .testimonials-reveal { opacity: 1 !important; transform: none !important; transition: none !important; }
    </style>
  </noscript>
</head>
<body id="top" class="min-h-screen antialiased">

  <!-- Header -->
  <header class="site-header" data-site-header>
    <div class="site-header-pill">
    <div class="site-header-inner mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-2.5 sm:gap-4 sm:px-6 sm:py-3 md:py-3.5">
      <a href="#top" class="header-brand group flex shrink-0 items-center gap-2.5 rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/55 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
        <span class="header-brand-mark" aria-hidden="true">CR</span>
        <span class="header-brand-text">Code<span>Rush</span></span>
      </a>

      <div class="hidden items-center gap-5 lg:gap-7 md:flex">
        <nav class="flex flex-wrap items-center justify-end gap-x-1 gap-y-1 sm:gap-x-2 lg:gap-x-3 xl:gap-x-4" aria-label="Seções da página">
          <a href="#clientes" class="header-nav-link focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Marcas</a>
          <a href="#sobre" class="header-nav-link focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Sobre</a>
          <a href="#empresas" class="header-nav-link focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Empresas</a>
          <a href="#cases" class="header-nav-link focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Cases</a>
          <a href="#depoimentos" class="header-nav-link focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Depoimentos</a>
          <a href="#faq" class="header-nav-link focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">FAQ</a>
          <a href="#contato" class="header-nav-link font-medium text-blue-400/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a] hover:text-blue-300">Contato</a>
        </nav>
        <div class="flex items-center gap-2.5 pl-2 lg:border-l lg:border-white/[0.08] lg:pl-5">
          <a href="#contato" class="header-btn-primary inline-flex items-center justify-center focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/55 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
            <span class="header-btn-primary-shine" aria-hidden="true"></span>
            <span class="relative z-[1]">Fale conosco</span>
          </a>
        </div>
      </div>

      <button type="button" id="mobile-menu-btn" class="header-menu-btn relative z-[60] flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/14 bg-white/[0.03] text-white/90 md:hidden focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]" aria-expanded="false" aria-controls="mobile-menu" aria-label="Abrir menu de navegação">
        <span class="sr-only">Alternar menu</span>
        <svg id="mobile-menu-icon-open" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
        <svg id="mobile-menu-icon-close" class="hidden h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
      </button>
    </div>
    </div>

    <div id="mobile-menu" class="header-mobile-panel hidden border border-white/[0.1] bg-[#020b1a]/94 backdrop-blur-xl md:hidden">
      <nav class="mx-auto flex max-w-6xl flex-col gap-0.5 px-6 py-4" aria-label="Menu mobile">
        <a href="#clientes" class="header-mobile-link px-3 py-3 text-sm font-medium text-white/85 hover:bg-white/[0.05] hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-inset">Marcas</a>
        <a href="#sobre" class="header-mobile-link px-3 py-3 text-sm font-medium text-white/85 hover:bg-white/[0.05] hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-inset">Sobre</a>
        <a href="#empresas" class="header-mobile-link px-3 py-3 text-sm font-medium text-white/85 hover:bg-white/[0.05] hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-inset">Empresas</a>
        <a href="#cases" class="header-mobile-link px-3 py-3 text-sm font-medium text-white/85 hover:bg-white/[0.05] hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-inset">Cases</a>
        <a href="#depoimentos" class="header-mobile-link px-3 py-3 text-sm font-medium text-white/85 hover:bg-white/[0.05] hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-inset">Depoimentos</a>
        <a href="#faq" class="header-mobile-link px-3 py-3 text-sm font-medium text-white/85 hover:bg-white/[0.05] hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-inset">FAQ</a>
        <a href="#contato" class="header-mobile-link px-3 py-3 text-sm font-semibold text-blue-400/95 hover:bg-blue-500/10 hover:text-blue-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-inset">Contato</a>
        <div class="mt-3 flex flex-col gap-2.5 border-t border-white/10 pt-4">
          <a href="#contato" class="header-btn-primary flex w-full items-center justify-center px-4 py-3 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/55 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
            <span class="header-btn-primary-shine" aria-hidden="true"></span>
            <span class="relative z-[1]">Fale conosco</span>
          </a>
        </div>
      </nav>
    </div>
  </header>

  <!-- Hero -->
  <section class="hero-section relative overflow-hidden px-6 text-center" aria-labelledby="hero-heading">
    <div class="hero-ambient" aria-hidden="true">
      <div class="hero-grid"></div>
      <div class="hero-blob hero-blob-1"></div>
      <div class="hero-blob hero-blob-2"></div>
      <div class="hero-blob hero-blob-3"></div>
      <div class="hero-vignette"></div>
      <div class="hero-noise"></div>
    </div>

    <div class="relative z-10 mx-auto w-full max-w-3xl">
      <div class="hero-reveal-item hero-delay-1">
        <span class="hero-badge rounded-full px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-blue-200">Hub de Tecnologia</span>
      </div>

      <h1 id="hero-heading" class="hero-reveal-item hero-delay-2 mt-7 font-heading text-4xl font-extrabold leading-[1.12] tracking-tight sm:text-5xl md:text-6xl md:leading-[1.1]">
        <span class="block text-white">Soluções digitais</span>
        <span class="gradient-text mt-2 block sm:mt-3">que geram resultado</span>
      </h1>

      <p class="hero-reveal-item hero-delay-3 mx-auto mt-7 max-w-2xl text-base leading-relaxed text-white/75 sm:text-lg">
        CodeRush é um hub central de tecnologia que reúne empresas especializadas em vendas diretas, desenvolvimento de software, WordPress, automação com IA e design digital.
      </p>

      <div class="hero-reveal-item hero-delay-4 mt-10 flex flex-wrap items-center justify-center gap-4">
        <a href="#empresas" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-brand px-8 py-3.5 text-sm font-semibold text-white shadow-[0_0_0_1px_rgba(255,255,255,0.08),0_16px_40px_-12px_rgba(0,74,173,0.65)] transition duration-300 hover:-translate-y-0.5 hover:bg-blue-600 hover:shadow-[0_0_0_1px_rgba(255,255,255,0.12),0_22px_48px_-8px_rgba(0,74,173,0.55)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/60 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
          <span class="relative z-10">Conheça nossas empresas</span>
          <span class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/15 to-transparent transition duration-700 group-hover:translate-x-full" aria-hidden="true"></span>
        </a>
        <a href="#contato" class="inline-flex items-center justify-center rounded-full border border-white/25 bg-white/[0.03] px-8 py-3.5 text-sm font-semibold text-white/95 backdrop-blur-sm transition duration-300 hover:-translate-y-0.5 hover:border-white/45 hover:bg-white/[0.08] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Fale conosco</a>
      </div>

      <div class="hero-reveal-item hero-delay-5 mt-14 flex flex-col items-center gap-2 md:mt-20">
        <a href="#sobre" class="hero-scroll-hint group inline-flex flex-col items-center gap-2 rounded-lg px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-white/45 transition hover:text-white/75 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
          <span>Explorar ecossistema</span>
          <svg class="h-5 w-5 text-blue-400/80 group-hover:text-blue-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
        </a>
      </div>
    </div>
  </section>

  <!-- Carrossel de clientes (mock) -->
  <section id="clientes" class="clients-strip border-t border-white/[0.06] bg-[#020b1a] py-10 md:py-14" aria-label="Marcas parceiras do ecossistema">
    <div class="mx-auto max-w-6xl px-6">
      <p class="text-center text-[11px] font-semibold uppercase tracking-[0.2em] text-white/40">Confiam em soluções do grupo</p>
      <p class="mx-auto mt-2 max-w-xl text-center text-xs text-white/45">Ilustração com marcas fictícias — substitua por logos reais quando disponível.</p>
    </div>
    <div class="clients-marquee mx-auto mt-8 max-w-[100vw]">
      <div class="clients-track">
        <div class="clients-group">
          <span class="clients-logo">Vértice</span>
          <span class="clients-logo">Nexus Labs</span>
          <span class="clients-logo">Atlas B2B</span>
          <span class="clients-logo">Fluxo·One</span>
          <span class="clients-logo">Ørbita</span>
          <span class="clients-logo">Prime MMN</span>
          <span class="clients-logo">Bluestack</span>
          <span class="clients-logo">NeoVendas</span>
        </div>
        <div class="clients-group clients-group-dup" role="presentation" aria-hidden="true">
          <span class="clients-logo">Vértice</span>
          <span class="clients-logo">Nexus Labs</span>
          <span class="clients-logo">Atlas B2B</span>
          <span class="clients-logo">Fluxo·One</span>
          <span class="clients-logo">Ørbita</span>
          <span class="clients-logo">Prime MMN</span>
          <span class="clients-logo">Bluestack</span>
          <span class="clients-logo">NeoVendas</span>
        </div>
      </div>
    </div>
    <div class="clients-marquee mx-auto mt-5 max-w-[100vw]">
      <div class="clients-track clients-track--alt">
        <div class="clients-group">
          <span class="clients-logo">Lumina</span>
          <span class="clients-logo">Kairós</span>
          <span class="clients-logo">Voltstack</span>
          <span class="clients-logo">Meridian</span>
          <span class="clients-logo">Sólis</span>
          <span class="clients-logo">Quantex</span>
          <span class="clients-logo">Gravity</span>
          <span class="clients-logo">Apex IA</span>
        </div>
        <div class="clients-group clients-group-dup" aria-hidden="true">
          <span class="clients-logo">Lumina</span>
          <span class="clients-logo">Kairós</span>
          <span class="clients-logo">Voltstack</span>
          <span class="clients-logo">Meridian</span>
          <span class="clients-logo">Sólis</span>
          <span class="clients-logo">Quantex</span>
          <span class="clients-logo">Gravity</span>
          <span class="clients-logo">Apex IA</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Quem somos -->
  <section id="sobre" class="sobre-section border-t border-white/[0.06] px-6 py-20 md:py-28" aria-labelledby="sobre-heading">
    <div class="sobre-ambient" aria-hidden="true"></div>
    <div class="sobre-ring hidden lg:block" aria-hidden="true"></div>
    <div class="pointer-events-none absolute inset-x-0 top-0 mx-auto h-px max-w-3xl bg-gradient-to-r from-transparent via-blue-500/35 to-transparent" aria-hidden="true"></div>
    <div class="relative mx-auto max-w-6xl">
      <div class="grid gap-14 lg:grid-cols-12 lg:items-start lg:gap-12">
        <div class="lg:col-span-5">
          <p class="sobre-reveal sobre-d-1 font-heading text-[11px] font-semibold uppercase tracking-[0.22em] text-blue-400/95">Quem somos</p>
          <h2 id="sobre-heading" class="sobre-reveal sobre-d-2 mt-3 font-heading text-3xl font-bold tracking-tight text-white sm:text-4xl">
            Um hub de tecnologia que une especialistas e propósito
          </h2>
          <p class="sobre-reveal sobre-d-3 mt-5 text-base leading-relaxed text-white/68">
            O CodeRush reúne <strong class="font-semibold text-white/90">várias empresas e serviços</strong> sob um ecossistema coordenado. Cada marca mantém sua identidade e foco de mercado, com metodologias e qualidade de entrega alinhadas.
          </p>
          <p class="sobre-reveal sobre-d-4 mt-4 text-base leading-relaxed text-white/60">
            Do MMN e vendas diretas ao desenvolvimento sob medida, WordPress, automação e IA aplicada — você encontra o time certo sem perder tempo em burocracia entre fornecedores soltos.
          </p>
          <div class="sobre-reveal sobre-d-5 mt-8 flex flex-wrap gap-3">
            <a href="#empresas" class="inline-flex items-center justify-center gap-2 rounded-full bg-brand px-6 py-3 text-sm font-semibold text-white shadow-[0_12px_36px_-12px_rgba(0,74,173,0.65)] transition hover:-translate-y-0.5 hover:bg-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/60 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
              <i data-lucide="building-2" class="h-4 w-4" aria-hidden="true"></i>
              Conhecer as empresas
            </a>
            <a href="#contato" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/22 bg-white/[0.04] px-6 py-3 text-sm font-semibold text-white/92 backdrop-blur-sm transition hover:-translate-y-0.5 hover:border-white/40 hover:bg-white/[0.08] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
              <i data-lucide="message-square" class="h-4 w-4" aria-hidden="true"></i>
              Falar com o time
            </a>
          </div>
        </div>
        <div class="lg:col-span-7">
          <div class="grid grid-cols-3 gap-3 sm:gap-4">
            <div class="sobre-reveal sobre-d-3 sobre-stat px-3 py-5 text-center sm:px-4 sm:py-6">
              <p class="font-heading text-2xl font-extrabold tabular-nums text-blue-400 sm:text-3xl">5</p>
              <p class="mt-1.5 text-[11px] font-medium leading-snug text-white/55 sm:text-xs">Empresas no grupo</p>
            </div>
            <div class="sobre-reveal sobre-d-4 sobre-stat px-3 py-5 text-center sm:px-4 sm:py-6">
              <p class="font-heading text-2xl font-extrabold tabular-nums text-blue-400 sm:text-3xl">+20</p>
              <p class="mt-1.5 text-[11px] font-medium leading-snug text-white/55 sm:text-xs">Anos de experiência</p>
            </div>
            <div class="sobre-reveal sobre-d-5 sobre-stat px-3 py-5 text-center sm:px-4 sm:py-6">
              <p class="font-heading text-2xl font-extrabold text-blue-400 sm:text-3xl">IA</p>
              <p class="mt-1.5 text-[11px] font-medium leading-snug text-white/55 sm:text-xs">No centro da operação</p>
            </div>
          </div>
          <div class="mt-4 grid gap-3 sm:grid-cols-2">
            <article class="sobre-reveal sobre-d-4 sobre-pillar flex gap-4 p-4 sm:p-5">
              <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-500/15 text-blue-200 ring-1 ring-blue-400/20">
                <i data-lucide="network" class="h-5 w-5" aria-hidden="true"></i>
              </span>
              <div>
                <h3 class="font-heading text-sm font-bold text-white">Ecossistema integrado</h3>
                <p class="mt-1.5 text-xs leading-relaxed text-white/55">Processos e visão comuns entre as marcas — menos atrito, mais velocidade no seu projeto.</p>
              </div>
            </article>
            <article class="sobre-reveal sobre-d-5 sobre-pillar flex gap-4 p-4 sm:p-5">
              <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-500/15 text-violet-200 ring-1 ring-violet-400/20">
                <i data-lucide="crosshair" class="h-5 w-5" aria-hidden="true"></i>
              </span>
              <div>
                <h3 class="font-heading text-sm font-bold text-white">Foco no negócio</h3>
                <p class="mt-1.5 text-xs leading-relaxed text-white/55">Tecnologia escolhida pelo resultado: conversão, escala, segurança e time-to-market.</p>
              </div>
            </article>
            <article class="sobre-reveal sobre-d-6 sobre-pillar flex gap-4 p-4 sm:p-5">
              <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-500/15 text-sky-200 ring-1 ring-sky-400/20">
                <i data-lucide="shield-check" class="h-5 w-5" aria-hidden="true"></i>
              </span>
              <div>
                <h3 class="font-heading text-sm font-bold text-white">Entrega responsável</h3>
                <p class="mt-1.5 text-xs leading-relaxed text-white/55">Boas práticas de segurança, LGPD e acompanhamento humano nas fases críticas.</p>
              </div>
            </article>
            <article class="sobre-reveal sobre-d-7 sobre-pillar flex gap-4 p-4 sm:p-5">
              <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-200 ring-1 ring-emerald-400/20">
                <i data-lucide="sparkles" class="h-5 w-5" aria-hidden="true"></i>
              </span>
              <div>
                <h3 class="font-heading text-sm font-bold text-white">IA onde faz sentido</h3>
                <p class="mt-1.5 text-xs leading-relaxed text-white/55">Automação e inteligência aplicadas ao funil e à operação — sem hype vazio.</p>
              </div>
            </article>
          </div>
          <p class="sobre-reveal sobre-d-8 mt-6 text-center text-[11px] text-white/38 sm:text-left">
            Hub corporativo — cada empresa com site e time próprios, alinhados ao mesmo padrão de excelência.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Empresas -->
  <section id="empresas" class="portfolio-section border-t border-white/[0.06] px-6 py-20 md:py-24" aria-labelledby="portfolio-heading">
    <div class="pointer-events-none absolute inset-x-0 top-0 mx-auto h-px max-w-3xl bg-gradient-to-r from-transparent via-blue-500/40 to-transparent" aria-hidden="true"></div>
    <div class="relative mx-auto max-w-6xl">
      <div class="mx-auto mb-14 max-w-2xl text-center md:mb-16">
        <p class="text-xs font-semibold uppercase tracking-widest text-blue-400">Portfólio</p>
        <h2 id="portfolio-heading" class="mt-3 font-heading text-3xl font-bold tracking-tight text-white sm:text-4xl">Nossas empresas</h2>
        <p class="mt-4 text-base leading-relaxed text-white/60">Cada marca com foco próprio, integrada ao ecossistema CodeRush. Use <span class="text-white/80">Visitar</span> para ir ao site da empresa.</p>
      </div>

      <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
        <!-- Sistema Venda Direta -->
        <a href="/sistemavendadireta/" class="portfolio-card group relative flex h-full flex-col rounded-2xl border border-white/10 bg-gradient-to-b from-white/[0.07] to-white/[0.02] p-6 shadow-lg shadow-black/25 backdrop-blur-sm hover:border-blue-500/35">
          <div class="mb-5 flex items-start justify-between gap-3">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-500/15 ring-1 ring-blue-400/25 text-blue-300">
              <i data-lucide="store" class="h-6 w-6" aria-hidden="true"></i>
            </span>
            <span class="shrink-0 rounded-full bg-blue-500/10 px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-blue-300/90">Vendas &amp; MMN</span>
          </div>
          <h3 class="font-heading text-lg font-bold text-white transition group-hover:text-blue-200">Sistema Venda Direta</h3>
          <p class="mt-3 flex-grow text-sm leading-relaxed text-white/65">Plataforma completa para vendas diretas e marketing multinível: força de vendas, lojas virtuais, backoffice e relatórios gerenciais.</p>
          <div class="mt-6 flex items-center justify-between gap-3 border-t border-white/10 pt-5">
            <span class="truncate text-xs text-white/45" title="sistemavendadireta.com.br">sistemavendadireta.com.br</span>
            <span class="inline-flex shrink-0 items-center gap-1.5 text-sm font-semibold text-blue-400 transition group-hover:text-blue-300">
              Visitar
              <i data-lucide="arrow-right" class="h-4 w-4 transition group-hover:translate-x-0.5" aria-hidden="true"></i>
            </span>
          </div>
        </a>

        <!-- Codafacil -->
        <a href="/codafacil/" class="portfolio-card group relative flex h-full flex-col rounded-2xl border border-white/10 bg-gradient-to-b from-white/[0.07] to-white/[0.02] p-6 shadow-lg shadow-black/25 backdrop-blur-sm hover:border-violet-500/35">
          <div class="mb-5 flex items-start justify-between gap-3">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-500/15 ring-1 ring-violet-400/25 text-violet-200">
              <i data-lucide="code-xml" class="h-6 w-6" aria-hidden="true"></i>
            </span>
            <span class="shrink-0 rounded-full bg-violet-500/10 px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-violet-200/90">Dev &amp; IA</span>
          </div>
          <h3 class="font-heading text-lg font-bold text-white transition group-hover:text-violet-200">Codafacil.dev</h3>
          <p class="mt-3 flex-grow text-sm leading-relaxed text-white/65">Software sob medida com IA, entregas ágeis e stack moderna — Laravel, Livewire e Tailwind.</p>
          <div class="mt-6 flex items-center justify-between gap-3 border-t border-white/10 pt-5">
            <span class="truncate text-xs text-white/45" title="codafacil.dev">codafacil.dev</span>
            <span class="inline-flex shrink-0 items-center gap-1.5 text-sm font-semibold text-violet-400 transition group-hover:text-violet-300">
              Visitar
              <i data-lucide="arrow-right" class="h-4 w-4 transition group-hover:translate-x-0.5" aria-hidden="true"></i>
            </span>
          </div>
        </a>

        <!-- WordPress Consultoria -->
        <a href="/wordpressconsultoria/" class="portfolio-card group relative flex h-full flex-col rounded-2xl border border-white/10 bg-gradient-to-b from-white/[0.07] to-white/[0.02] p-6 shadow-lg shadow-black/25 backdrop-blur-sm hover:border-sky-500/35">
          <div class="mb-5 flex items-start justify-between gap-3">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-sky-500/15 ring-1 ring-sky-400/25 text-sky-200">
              <i data-lucide="puzzle" class="h-6 w-6" aria-hidden="true"></i>
            </span>
            <span class="shrink-0 rounded-full bg-sky-500/10 px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-sky-200/90">WordPress</span>
          </div>
          <h3 class="font-heading text-lg font-bold text-white transition group-hover:text-sky-200">WordPress Consultoria</h3>
          <p class="mt-3 flex-grow text-sm leading-relaxed text-white/65">Plugins sob medida, gateways, integrações com ERP, multi-lojas e temas personalizados.</p>
          <div class="mt-6 flex items-center justify-between gap-3 border-t border-white/10 pt-5">
            <span class="truncate text-xs text-white/45" title="wordpressconsultoria.com.br">wordpressconsultoria.com.br</span>
            <span class="inline-flex shrink-0 items-center gap-1.5 text-sm font-semibold text-sky-400 transition group-hover:text-sky-300">
              Visitar
              <i data-lucide="arrow-right" class="h-4 w-4 transition group-hover:translate-x-0.5" aria-hidden="true"></i>
            </span>
          </div>
        </a>
      </div>

      <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:mx-auto lg:max-w-4xl">
        <!-- FluxoInteligente IA -->
        <a href="/fluxointeligenteia/" class="portfolio-card group relative flex h-full flex-col rounded-2xl border border-white/10 bg-gradient-to-b from-white/[0.07] to-white/[0.02] p-6 shadow-lg shadow-black/25 backdrop-blur-sm hover:border-emerald-500/35">
          <div class="mb-5 flex items-start justify-between gap-3">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-500/15 ring-1 ring-emerald-400/25 text-emerald-200">
              <i data-lucide="workflow" class="h-6 w-6" aria-hidden="true"></i>
            </span>
            <span class="shrink-0 rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-emerald-200/90">Automação</span>
          </div>
          <h3 class="font-heading text-lg font-bold text-white transition group-hover:text-emerald-200">FluxoInteligente IA</h3>
          <p class="mt-3 flex-grow text-sm leading-relaxed text-white/65">Processos complexos viram fluxos inteligentes: n8n, agentes e IA aplicada ao negócio.</p>
          <div class="mt-6 flex items-center justify-between gap-3 border-t border-white/10 pt-5">
            <span class="truncate text-xs text-white/45" title="fluxointeligenteia.com.br">fluxointeligenteia.com.br</span>
            <span class="inline-flex shrink-0 items-center gap-1.5 text-sm font-semibold text-emerald-400 transition group-hover:text-emerald-300">
              Visitar
              <i data-lucide="arrow-right" class="h-4 w-4 transition group-hover:translate-x-0.5" aria-hidden="true"></i>
            </span>
          </div>
        </a>

        <!-- Traço Creative Lab -->
        <div class="portfolio-card-static group relative flex h-full flex-col rounded-2xl border border-dashed border-white/15 bg-white/[0.02] p-6 opacity-90" role="group" aria-label="Traço Creative Lab — em breve">
          <div class="mb-5 flex items-start justify-between gap-3">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-rose-500/12 ring-1 ring-rose-400/20 text-rose-200/90">
              <i data-lucide="palette" class="h-6 w-6" aria-hidden="true"></i>
            </span>
            <span class="shrink-0 rounded-full border border-white/15 bg-white/[0.04] px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-white/50">Em breve</span>
          </div>
          <h3 class="font-heading text-lg font-bold text-white/95">Traço Creative Lab</h3>
          <p class="mt-3 flex-grow text-sm leading-relaxed text-white/55">Design, UX, prototipagem e front-end para web e mobile — do conceito à implementação.</p>
          <div class="mt-6 border-t border-white/10 pt-5">
            <p class="text-xs text-white/40">Em breve no ecossistema CodeRush.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Cases -->
  <section id="cases" class="cases-section relative border-t border-white/[0.06] px-6 py-20 md:py-28" aria-labelledby="cases-heading">
    <div class="cases-ambient" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-6xl">
      <header class="cases-reveal cases-d-1 mx-auto mb-12 max-w-3xl text-center md:mb-16">
        <p class="text-xs font-semibold uppercase tracking-widest text-emerald-400/90">Resultados &amp; prova social</p>
        <h2 id="cases-heading" class="mt-3 font-heading text-3xl font-bold tracking-tight text-white sm:text-4xl">Cases de sucesso no ecossistema CodeRush</h2>
        <p class="mt-4 text-base leading-relaxed text-white/65">
          Selecionamos <strong class="font-semibold text-white/85">destaques reais</strong> de entregas feitas pelas empresas do grupo: <strong class="font-semibold text-white/85">performance</strong>, <strong class="font-semibold text-white/85">SEO técnico</strong>, integrações e foco em <strong class="font-semibold text-white/85">conversão</strong>. Use estes cenários para comparar com o seu próximo projeto.
        </p>
      </header>

      <div class="mb-12 grid grid-cols-2 gap-3 md:grid-cols-4 md:gap-4">
        <div class="cases-reveal cases-d-2 rounded-2xl border border-white/10 bg-white/[0.04] p-4 text-center backdrop-blur-sm">
          <p class="font-heading text-2xl font-extrabold text-blue-400 md:text-3xl">+120</p>
          <p class="mt-1 text-[11px] font-medium uppercase tracking-wide text-white/50 md:text-xs">Projetos entregues</p>
        </div>
        <div class="cases-reveal cases-d-2 rounded-2xl border border-white/10 bg-white/[0.04] p-4 text-center backdrop-blur-sm">
          <p class="font-heading text-2xl font-extrabold text-violet-400 md:text-3xl">+20</p>
          <p class="mt-1 text-[11px] font-medium uppercase tracking-wide text-white/50 md:text-xs">Anos combinados</p>
        </div>
        <div class="cases-reveal cases-d-2 rounded-2xl border border-white/10 bg-white/[0.04] p-4 text-center backdrop-blur-sm">
          <p class="font-heading text-2xl font-extrabold text-emerald-400 md:text-3xl">99,9%</p>
          <p class="mt-1 text-[11px] font-medium uppercase tracking-wide text-white/50 md:text-xs">Meta de uptime*</p>
        </div>
        <div class="cases-reveal cases-d-2 rounded-2xl border border-white/10 bg-white/[0.04] p-4 text-center backdrop-blur-sm">
          <p class="font-heading text-2xl font-extrabold text-sky-400 md:text-3xl">Brasil</p>
          <p class="mt-1 text-[11px] font-medium uppercase tracking-wide text-white/50 md:text-xs">Atuação nacional</p>
        </div>
      </div>
      <p class="cases-reveal cases-d-2 mb-12 text-center text-[11px] text-white/35">* Meta operacional em projetos de plataforma crítica; disponibilidade depende de infraestrutura do cliente.</p>

      <article class="cases-reveal cases-d-3 cases-card group relative overflow-hidden rounded-3xl border border-blue-500/25 bg-gradient-to-br from-blue-950/50 via-[#020b1a] to-[#020b1a] p-6 shadow-xl shadow-blue-950/40 sm:p-8 md:p-10">
        <div class="cases-featured-bar absolute left-0 right-0 top-0 h-px opacity-80" aria-hidden="true"></div>
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
          <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 rounded-full border border-blue-400/30 bg-blue-500/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-blue-200">
              <i data-lucide="trending-up" class="h-3.5 w-3.5" aria-hidden="true"></i>
              Case em destaque
            </div>
            <h3 class="mt-4 font-heading text-2xl font-bold text-white md:text-3xl">Plataforma nacional de vendas diretas e MMN</h3>
            <p class="mt-3 text-sm leading-relaxed text-white/70 md:text-base">
              Operação com <strong class="text-white/90">milhões de pedidos</strong> processados, comissionamento multi-nível, lojas por consultor e <strong class="text-white/90">backoffice</strong> para franqueadora. Foco em <strong class="text-white/90">SEO técnico</strong> (Core Web Vitals), escalabilidade e relatórios gerenciais em tempo real.
            </p>
            <ul class="mt-5 flex flex-wrap gap-2" aria-label="Destaques do case">
              <li class="rounded-full border border-white/15 bg-white/[0.05] px-3 py-1 text-xs font-medium text-white/75">Alta escala</li>
              <li class="rounded-full border border-white/15 bg-white/[0.05] px-3 py-1 text-xs font-medium text-white/75">Comissionamento</li>
              <li class="rounded-full border border-white/15 bg-white/[0.05] px-3 py-1 text-xs font-medium text-white/75">Multi-loja</li>
              <li class="rounded-full border border-white/15 bg-white/[0.05] px-3 py-1 text-xs font-medium text-white/75">Relatórios</li>
            </ul>
          </div>
          <div class="flex w-full shrink-0 flex-col gap-3 sm:flex-row sm:items-center lg:w-auto lg:flex-col">
            <a href="/sistemavendadireta/" class="inline-flex items-center justify-center gap-2 rounded-full bg-brand px-6 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/60 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
              Ver solução SVD
              <i data-lucide="arrow-right" class="h-4 w-4 transition group-hover:translate-x-0.5" aria-hidden="true"></i>
            </a>
            <a href="#contato" class="inline-flex items-center justify-center rounded-full border border-white/25 px-6 py-3 text-sm font-semibold text-white/90 transition hover:border-white/50 hover:bg-white/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Quero algo parecido</a>
          </div>
        </div>
      </article>

      <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <article class="cases-reveal cases-d-4 cases-card flex h-full flex-col rounded-2xl border border-white/10 bg-gradient-to-b from-white/[0.06] to-transparent p-6">
          <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-violet-500/15 text-violet-200 ring-1 ring-violet-400/25">
            <i data-lucide="cpu" class="h-5 w-5" aria-hidden="true"></i>
          </div>
          <h3 class="font-heading text-lg font-bold text-white">Software sob medida com IA</h3>
          <p class="mt-2 flex-grow text-sm leading-relaxed text-white/65">
            Sistemas web com <strong class="font-medium text-white/80">Laravel</strong>, painéis administrativos, integrações via API e automações com IA. Entregas quinzenais e stack moderna (Livewire, Tailwind) para <strong class="font-medium text-white/80">time-to-market</strong> previsível.
          </p>
          <a href="/codafacil/" class="mt-6 inline-flex items-center gap-1.5 text-sm font-semibold text-violet-400 transition hover:text-violet-300">
            Codafacil.dev
            <i data-lucide="external-link" class="h-4 w-4" aria-hidden="true"></i>
          </a>
        </article>

        <article class="cases-reveal cases-d-5 cases-card flex h-full flex-col rounded-2xl border border-white/10 bg-gradient-to-b from-white/[0.06] to-transparent p-6">
          <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-sky-500/15 text-sky-200 ring-1 ring-sky-400/25">
            <i data-lucide="shopping-bag" class="h-5 w-5" aria-hidden="true"></i>
          </div>
          <h3 class="font-heading text-lg font-bold text-white">WordPress &amp; loja com ERP</h3>
          <p class="mt-2 flex-grow text-sm leading-relaxed text-white/65">
            WooCommerce e catálogos <strong class="font-medium text-white/80">B2B</strong>, gateways de pagamento, plugins sob medida e <strong class="font-medium text-white/80">sincronização com ERP</strong>. SEO on-page, performance e segurança para conversão.
          </p>
          <a href="/wordpressconsultoria/" class="mt-6 inline-flex items-center gap-1.5 text-sm font-semibold text-sky-400 transition hover:text-sky-300">
            WordPress Consultoria
            <i data-lucide="external-link" class="h-4 w-4" aria-hidden="true"></i>
          </a>
        </article>

        <article class="cases-reveal cases-d-6 cases-card flex h-full flex-col rounded-2xl border border-white/10 bg-gradient-to-b from-white/[0.06] to-transparent p-6">
          <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-200 ring-1 ring-emerald-400/25">
            <i data-lucide="git-branch" class="h-5 w-5" aria-hidden="true"></i>
          </div>
          <h3 class="font-heading text-lg font-bold text-white">Automação com n8n &amp; IA</h3>
          <p class="mt-2 flex-grow text-sm leading-relaxed text-white/65">
            Fluxos inteligentes ligando <strong class="font-medium text-white/80">CRM</strong>, mensageria e sistemas legados. Agentes e <strong class="font-medium text-white/80">IA aplicada</strong> ao funil comercial para reduzir tarefas manuais e aumentar resposta.
          </p>
          <a href="/fluxointeligenteia/" class="mt-6 inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-400 transition hover:text-emerald-300">
            FluxoInteligente IA
            <i data-lucide="external-link" class="h-4 w-4" aria-hidden="true"></i>
          </a>
        </article>
      </div>

      <div class="cases-reveal cases-d-7 mt-10 rounded-2xl border border-white/10 bg-white/[0.03] p-6 md:p-8">
        <p class="text-center text-xs font-semibold uppercase tracking-widest text-white/45">Linhas de atuação cobertas pelos cases</p>
        <div class="mt-4 flex flex-wrap justify-center gap-2 md:gap-3">
          <span class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1.5 text-xs text-white/75">Vendas diretas &amp; MMN</span>
          <span class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1.5 text-xs text-white/75">Desenvolvimento Laravel</span>
          <span class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1.5 text-xs text-white/75">WordPress &amp; WooCommerce</span>
          <span class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1.5 text-xs text-white/75">Automação &amp; n8n</span>
          <span class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1.5 text-xs text-white/75">IA aplicada ao negócio</span>
        </div>
      </div>

      <div class="cases-reveal cases-d-8 mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
        <a href="#contato" class="inline-flex w-full items-center justify-center rounded-full bg-brand px-8 py-3.5 text-sm font-semibold text-white shadow-lg transition hover:bg-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/60 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a] sm:w-auto">
          Falar com um especialista
        </a>
        <a href="#empresas" class="inline-flex w-full items-center justify-center rounded-full border border-white/25 px-8 py-3.5 text-sm font-semibold text-white/90 transition hover:border-white/45 hover:bg-white/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a] sm:w-auto">
          Ver todas as empresas
        </a>
      </div>
    </div>
  </section>

  <!-- Depoimentos -->
  <section id="depoimentos" class="testimonials-section relative border-t border-white/[0.06] px-6 py-20 md:py-28" aria-labelledby="depoimentos-heading">
    <div class="testimonials-ambient" aria-hidden="true"></div>
    <div class="pointer-events-none absolute inset-x-0 top-0 mx-auto h-px max-w-2xl bg-gradient-to-r from-transparent via-amber-400/25 to-transparent" aria-hidden="true"></div>
    <div class="relative mx-auto max-w-6xl">
      <header class="testimonials-reveal t-d-1 mx-auto mb-12 max-w-3xl text-center md:mb-16">
        <p class="font-heading text-[11px] font-semibold uppercase tracking-[0.2em] text-amber-400/95">Depoimentos</p>
        <h2 id="depoimentos-heading" class="mt-3 font-heading text-3xl font-bold tracking-tight text-white sm:text-4xl">Quem trabalhou com o ecossistema recomenda</h2>
        <p class="mt-4 text-base leading-relaxed text-white/60">
          Histórias de projetos reais — <span class="text-white/75">perfis ilustrativos</span> para demonstrar tom e formato; substitua por depoimentos verificáveis quando desejar.
        </p>
      </header>

      <div class="testimonials-scroller -mx-4 px-4 sm:mx-0 sm:px-0">
        <div class="testimonial-card-wrap">
          <article class="testimonial-card testimonials-reveal t-d-2 flex h-full flex-col p-6 sm:p-7">
            <span class="testimonial-quote-deco" aria-hidden="true">“</span>
            <div class="relative flex items-center gap-1 text-amber-400/95" aria-label="5 de 5 estrelas">
              <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400/85" aria-hidden="true"></i>
              <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400/85" aria-hidden="true"></i>
              <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400/85" aria-hidden="true"></i>
              <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400/85" aria-hidden="true"></i>
              <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400/85" aria-hidden="true"></i>
            </div>
            <blockquote class="relative mt-4 flex-grow">
              <p class="text-sm leading-relaxed text-white/78">
                A plataforma de MMN saiu do papel em tempo recorde. O time entendeu regras de bonificação, performance sob carga e o que o campo precisa ver no app.
              </p>
            </blockquote>
            <footer class="relative mt-6 flex items-center gap-3 border-t border-white/10 pt-5">
              <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/30 to-blue-600/10 font-heading text-sm font-bold text-blue-100 ring-1 ring-blue-400/25" aria-hidden="true">RC</span>
              <div class="min-w-0">
                <p class="truncate font-heading text-sm font-semibold text-white">Renata Costa</p>
                <p class="truncate text-xs text-white/50">Diretora de Operações · marca fictícia</p>
              </div>
            </footer>
          </article>
        </div>
        <div class="testimonial-card-wrap">
          <article class="testimonial-card testimonials-reveal t-d-3 flex h-full flex-col p-6 sm:p-7">
            <span class="testimonial-quote-deco" aria-hidden="true">“</span>
            <div class="relative flex items-center gap-1 text-amber-400/95" aria-label="5 de 5 estrelas">
              <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400/85" aria-hidden="true"></i>
              <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400/85" aria-hidden="true"></i>
              <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400/85" aria-hidden="true"></i>
              <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400/85" aria-hidden="true"></i>
              <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400/85" aria-hidden="true"></i>
            </div>
            <blockquote class="relative mt-4 flex-grow">
              <p class="text-sm leading-relaxed text-white/78">
                Integração com ERP e CRM sem gambiarra. Documentação clara, deploy previsível e suporte quando a operação aperta — raro em fornecedor de software.
              </p>
            </blockquote>
            <footer class="relative mt-6 flex items-center gap-3 border-t border-white/10 pt-5">
              <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500/30 to-violet-600/10 font-heading text-sm font-bold text-violet-100 ring-1 ring-violet-400/25" aria-hidden="true">LM</span>
              <div class="min-w-0">
                <p class="truncate font-heading text-sm font-semibold text-white">Lucas Monteiro</p>
                <p class="truncate text-xs text-white/50">CTO · startup B2B fictícia</p>
              </div>
            </footer>
          </article>
        </div>
        <div class="testimonial-card-wrap">
          <article class="testimonial-card testimonials-reveal t-d-4 flex h-full flex-col p-6 sm:p-7">
            <span class="testimonial-quote-deco" aria-hidden="true">“</span>
            <div class="relative flex items-center gap-1 text-amber-400/95" aria-label="5 de 5 estrelas">
              <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400/85" aria-hidden="true"></i>
              <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400/85" aria-hidden="true"></i>
              <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400/85" aria-hidden="true"></i>
              <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400/85" aria-hidden="true"></i>
              <i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400/85" aria-hidden="true"></i>
            </div>
            <blockquote class="relative mt-4 flex-grow">
              <p class="text-sm leading-relaxed text-white/78">
                WooCommerce B2B com catálogos complexos e SEO técnico que realmente move agulha. O time entende conversão, não só tema bonito.
              </p>
            </blockquote>
            <footer class="relative mt-6 flex items-center gap-3 border-t border-white/10 pt-5">
              <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500/30 to-emerald-600/10 font-heading text-sm font-bold text-emerald-100 ring-1 ring-emerald-400/25" aria-hidden="true">FS</span>
              <div class="min-w-0">
                <p class="truncate font-heading text-sm font-semibold text-white">Fernanda Souza</p>
                <p class="truncate text-xs text-white/50">Head de E-commerce · distribuidora fictícia</p>
              </div>
            </footer>
          </article>
        </div>
      </div>

      <div class="testimonials-reveal t-d-5 mt-12 flex flex-col items-center justify-center gap-4 rounded-2xl border border-white/10 bg-white/[0.03] px-6 py-8 text-center md:flex-row md:justify-between md:text-left">
        <div class="flex max-w-xl flex-col items-center gap-3 md:flex-row md:items-center md:gap-4">
          <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-500/15 text-amber-200 ring-1 ring-amber-400/25">
            <i data-lucide="mic-2" class="h-6 w-6" aria-hidden="true"></i>
          </span>
          <p class="text-sm leading-relaxed text-white/65">
            <span class="font-semibold text-white/85">Quer ver seu projeto aqui?</span> Envie o briefing e, com autorização, destacamos resultados no site e nos cases.
          </p>
        </div>
        <a href="#contato" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-full bg-brand px-6 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/60 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
          Quero aparecer nos destaques
          <i data-lucide="arrow-right" class="h-4 w-4" aria-hidden="true"></i>
        </a>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section id="faq" class="faq-section relative border-t border-white/[0.06] px-6 py-20 md:py-24" aria-labelledby="faq-heading">
    <div class="pointer-events-none absolute inset-x-0 top-0 mx-auto h-px max-w-lg bg-gradient-to-r from-transparent via-violet-500/30 to-transparent" aria-hidden="true"></div>
    <div class="relative mx-auto max-w-3xl">
      <div class="text-center">
        <p class="text-xs font-semibold uppercase tracking-widest text-violet-400/90">Dúvidas frequentes</p>
        <h2 id="faq-heading" class="mt-3 font-heading text-3xl font-bold tracking-tight text-white sm:text-4xl">Perguntas e respostas</h2>
        <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-white/55">Tudo em um só lugar. Abra um tópico por vez — o painel usa tema escuro para leitura confortável.</p>
      </div>

      <div class="mt-12 space-y-3">
        <details name="coderush-faq" class="faq-item faq-reveal faq-d-1">
          <summary class="faq-summary">
            <span>O que é o ecossistema CodeRush?</span>
            <span class="faq-chevron"><i data-lucide="chevron-down" class="h-4 w-4" aria-hidden="true"></i></span>
          </summary>
          <div class="faq-panel">
            <div class="faq-panel-inner">
              <div class="faq-answer">
                <p>É um <strong class="font-medium text-white/90">hub de tecnologia</strong> que reúne empresas com especialidades distintas — vendas diretas e MMN, desenvolvimento de software, WordPress e automação com IA — com processos e visão de entrega alinhados.</p>
              </div>
            </div>
          </div>
        </details>

        <details name="coderush-faq" class="faq-item faq-reveal faq-d-2">
          <summary class="faq-summary">
            <span>Como escolho qual empresa do grupo fala com o meu projeto?</span>
            <span class="faq-chevron"><i data-lucide="chevron-down" class="h-4 w-4" aria-hidden="true"></i></span>
          </summary>
          <div class="faq-panel">
            <div class="faq-panel-inner">
              <div class="faq-answer">
                <p>Pelo <strong class="font-medium text-white/90">tipo de necessidade</strong>: plataforma de MMN e vendas diretas; software sob medida; WordPress e e-commerce; ou automação com n8n e IA. Você também pode usar o <a href="#contato" class="font-medium text-blue-400 underline decoration-blue-400/40 underline-offset-2 hover:text-blue-300">formulário de contato</a> para descrever o briefing e receber direcionamento.</p>
              </div>
            </div>
          </div>
        </details>

        <details name="coderush-faq" class="faq-item faq-reveal faq-d-3">
          <summary class="faq-summary">
            <span>Os sites e sistemas são seguros?</span>
            <span class="faq-chevron"><i data-lucide="chevron-down" class="h-4 w-4" aria-hidden="true"></i></span>
          </summary>
          <div class="faq-panel">
            <div class="faq-panel-inner">
              <div class="faq-answer">
                <p>As entregas seguem <strong class="font-medium text-white/90">boas práticas</strong> de segurança (HTTPS, controles de acesso e revisão de dependências conforme o escopo). Requisitos específicos — LGPD, auditoria, SSO — são tratados na fase de discovery com a equipe.</p>
              </div>
            </div>
          </div>
        </details>

        <details name="coderush-faq" class="faq-item faq-reveal faq-d-4">
          <summary class="faq-summary">
            <span>Como funciona o contato comercial?</span>
            <span class="faq-chevron"><i data-lucide="chevron-down" class="h-4 w-4" aria-hidden="true"></i></span>
          </summary>
          <div class="faq-panel">
            <div class="faq-panel-inner">
              <div class="faq-answer">
                <p>Você pode usar o <strong class="font-medium text-white/90">formulário</strong> nesta página, o e-mail <a href="mailto:contato@coderush.com.br" class="font-medium text-blue-400 hover:text-blue-300">contato@coderush.com.br</a> ou ir direto ao site da empresa do grupo que melhor encaixa no seu caso. Priorizamos retorno humano e objetivo.</p>
              </div>
            </div>
          </div>
        </details>

        <details name="coderush-faq" class="faq-item faq-reveal faq-d-5">
          <summary class="faq-summary">
            <span>Vocês trabalham com IA e automação?</span>
            <span class="faq-chevron"><i data-lucide="chevron-down" class="h-4 w-4" aria-hidden="true"></i></span>
          </summary>
          <div class="faq-panel">
            <div class="faq-panel-inner">
              <div class="faq-answer">
                <p>Sim. Há ofertas de <strong class="font-medium text-white/90">software com IA</strong>, integrações, WordPress avançado e fluxos com <strong class="font-medium text-white/90">n8n</strong> e agentes, além de consultoria em automação comercial — sempre com foco em resultado mensurável.</p>
              </div>
            </div>
          </div>
        </details>

        <details name="coderush-faq" class="faq-item faq-reveal faq-d-6">
          <summary class="faq-summary">
            <span>Atendem empresas fora do Brasil?</span>
            <span class="faq-chevron"><i data-lucide="chevron-down" class="h-4 w-4" aria-hidden="true"></i></span>
          </summary>
          <div class="faq-panel">
            <div class="faq-panel-inner">
              <div class="faq-answer">
                <p>O <strong class="font-medium text-white/90">foco principal</strong> é o mercado brasileiro. Projetos com atuação internacional podem ser avaliados caso a caso (idioma, fuso, compliance e contratos).</p>
              </div>
            </div>
          </div>
        </details>
      </div>

      <p class="mt-10 text-center text-sm text-white/45">Não encontrou o que precisa? <a href="#contato" class="font-semibold text-blue-400 underline decoration-blue-400/35 underline-offset-2 transition hover:text-blue-300">Fale conosco</a>.</p>
    </div>
  </section>

  <!-- Contato -->
  <section id="contato" class="contact-section border-t border-white/[0.06] px-6 py-20 md:py-28" aria-labelledby="contato-heading">
    <div class="contact-ambient" aria-hidden="true"></div>
    <div class="relative mx-auto max-w-6xl">
      <div class="grid items-start gap-12 lg:grid-cols-12 lg:gap-16">
        <div class="contact-reveal contact-form-delay-1 lg:col-span-5">
          <p class="text-xs font-semibold uppercase tracking-widest text-blue-400">Contato comercial</p>
          <h2 id="contato-heading" class="mt-3 font-heading text-3xl font-bold tracking-tight text-white sm:text-4xl">Vamos tirar seu projeto do papel</h2>
          <p class="mt-4 text-base leading-relaxed text-white/65">
            Conte o que você precisa: uma empresa do grupo ou o hub central pode direcionar a melhor equipe. Resposta humana, sem fila robótica.
          </p>
          <ul class="mt-8 space-y-4 text-sm text-white/70">
            <li class="flex gap-3">
              <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-400">
                <i data-lucide="check-circle-2" class="h-4 w-4" aria-hidden="true"></i>
              </span>
              <span><strong class="font-semibold text-white/85">Briefing guiado</strong> — entendemos escopo, prazo e stack antes de propor solução.</span>
            </li>
            <li class="flex gap-3">
              <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-400">
                <i data-lucide="check-circle-2" class="h-4 w-4" aria-hidden="true"></i>
              </span>
              <span><strong class="font-semibold text-white/85">Dados protegidos</strong> — uso responsável das informações; confira a caixa de consentimento abaixo.</span>
            </li>
            <li class="flex gap-3">
              <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-400">
                <i data-lucide="check-circle-2" class="h-4 w-4" aria-hidden="true"></i>
              </span>
              <span><strong class="font-semibold text-white/85">Canal direto</strong> — prefere e-mail? <a href="mailto:contato@coderush.com.br" class="text-blue-400 underline decoration-blue-400/40 underline-offset-2 transition hover:text-blue-300">contato@coderush.com.br</a></span>
            </li>
          </ul>
        </div>

        <div class="contact-reveal contact-form-delay-2 lg:col-span-7">
          <div id="form-feedback" class="mb-6 hidden rounded-2xl border p-4 text-sm" role="alert"></div>

          <div class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-white/[0.08] to-white/[0.02] p-6 shadow-2xl shadow-black/40 backdrop-blur-md sm:p-8">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-brand/20 blur-3xl" aria-hidden="true"></div>
            <form id="form-contato" class="relative space-y-5" action="/enviar-contato.php" method="post" novalidate>
              <input type="hidden" name="redirect" value="/" />
              <input type="hidden" name="origem" value="coderush-hub" />
              <div class="absolute left-[-9999px] top-auto h-0 w-0 overflow-hidden" aria-hidden="true">
                <label for="website">Não preencha</label>
                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off" />
              </div>

              <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                  <label class="contact-label" for="nome">Nome completo <abbr title="obrigatório" class="text-blue-400 no-underline">*</abbr></label>
                  <input class="contact-input mt-1.5" type="text" id="nome" name="nome" required autocomplete="name" placeholder="Seu nome" />
                </div>
                <div>
                  <label class="contact-label" for="email">E-mail corporativo <abbr title="obrigatório" class="text-blue-400 no-underline">*</abbr></label>
                  <input class="contact-input mt-1.5" type="email" id="email" name="email" required autocomplete="email" inputmode="email" placeholder="voce@empresa.com.br" />
                </div>
                <div>
                  <label class="contact-label" for="telefone">WhatsApp / telefone</label>
                  <input class="contact-input mt-1.5" type="tel" id="telefone" name="telefone" autocomplete="tel" inputmode="tel" placeholder="(00) 00000-0000" />
                </div>
                <div class="sm:col-span-2">
                  <label class="contact-label" for="empresa">Empresa / projeto</label>
                  <input class="contact-input mt-1.5" type="text" id="empresa" name="empresa" autocomplete="organization" placeholder="Nome fantasia ou projeto" />
                </div>
                <div class="sm:col-span-2">
                  <label class="contact-label" for="interesse">O que você busca? <abbr title="obrigatório" class="text-blue-400 no-underline">*</abbr></label>
                  <select class="contact-input mt-1.5 cursor-pointer" id="interesse" name="interesse" required>
                    <option value="" disabled selected>Selecione uma linha</option>
                    <option value="Vendas diretas / MMN">Vendas diretas / MMN</option>
                    <option value="Software sob medida &amp; IA">Software sob medida &amp; IA</option>
                    <option value="WordPress &amp; e-commerce">WordPress &amp; e-commerce</option>
                    <option value="Automação &amp; n8n / IA">Automação &amp; n8n / IA</option>
                    <option value="Outro / ainda não sei">Outro / ainda não sei</option>
                  </select>
                </div>
                <div class="sm:col-span-2">
                  <label class="contact-label" for="mensagem">Mensagem <abbr title="obrigatório" class="text-blue-400 no-underline">*</abbr></label>
                  <textarea class="contact-input mt-1.5 min-h-[8rem] resize-y" id="mensagem" name="mensagem" required minlength="10" rows="5" placeholder="Descreva objetivo, prazo desejado e qualquer integração (ERP, pagamento, etc.). Mínimo 10 caracteres."></textarea>
                </div>
              </div>

              <div class="flex gap-3 rounded-xl border border-white/10 bg-white/[0.03] p-4">
                <input type="checkbox" id="consent" name="consent" value="1" required class="mt-1 h-4 w-4 shrink-0 rounded border-white/20 bg-white/10 text-brand focus:ring-blue-500/40" />
                <label for="consent" class="text-xs leading-relaxed text-white/60">
                  Autorizo o uso dos dados informados para retorno sobre meu pedido, conforme a <strong class="text-white/75">política de privacidade</strong> do ecossistema CodeRush. Não enviamos spam.
                </label>
              </div>

              <button type="submit" id="form-submit-btn" class="contact-submit-shine relative w-full overflow-hidden rounded-full bg-brand py-3.5 text-sm font-semibold text-white shadow-lg transition hover:bg-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/60 focus-visible:ring-offset-2 focus-visible:ring-offset-[#0a1628] disabled:cursor-not-allowed disabled:opacity-70">
                <span class="relative z-10 inline-flex items-center justify-center gap-2">
                  <i data-lucide="send" class="h-4 w-4" aria-hidden="true"></i>
                  Enviar mensagem
                </span>
              </button>
              <p class="text-center text-[11px] text-white/35">Ao enviar, você será redirecionado de volta com a confirmação na página.</p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer id="site-footer" class="footer-site relative px-6 pb-10 pt-14 md:pb-14 md:pt-16" aria-label="Rodapé">
    <div class="footer-top-glow" aria-hidden="true"></div>
    <div class="footer-ambient" aria-hidden="true"></div>
    <div class="relative mx-auto max-w-6xl">
      <div class="grid gap-12 md:grid-cols-12 md:gap-10 lg:gap-14">
        <div class="footer-reveal md:col-span-5">
          <p class="font-heading text-[10px] font-semibold uppercase tracking-[0.2em] text-white/35">Próximo passo</p>
          <h2 class="mt-2 font-heading text-xl font-bold tracking-tight text-white md:text-2xl">Vamos conversar sobre o seu projeto</h2>
          <p class="mt-3 max-w-md text-sm leading-relaxed text-white/55">
            Resposta humana, sem fila genérica. Escolha o canal que preferir — ou explore as empresas do ecossistema.
          </p>
          <div class="mt-6 flex flex-wrap gap-3">
            <a href="#contato" class="footer-cta-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/60 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
              <i data-lucide="arrow-right" class="h-4 w-4" aria-hidden="true"></i>
              Fale conosco
            </a>
          </div>
          <a href="mailto:contato@coderush.com.br" class="mt-6 inline-flex items-center gap-2 text-sm font-medium text-blue-400/90 transition hover:text-blue-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
            <i data-lucide="mail" class="h-4 w-4 shrink-0" aria-hidden="true"></i>
            contato@coderush.com.br
          </a>
        </div>
        <nav class="footer-reveal footer-stagger-1 md:col-span-3" aria-label="Mapa do site">
          <p class="font-heading text-[10px] font-semibold uppercase tracking-[0.2em] text-white/35">Navegação</p>
          <ul class="mt-4 space-y-2.5 text-sm">
            <li><a href="#clientes" class="footer-nav-link rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Marcas</a></li>
            <li><a href="#sobre" class="footer-nav-link rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Sobre</a></li>
            <li><a href="#empresas" class="footer-nav-link rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Empresas</a></li>
            <li><a href="#cases" class="footer-nav-link rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Cases</a></li>
            <li><a href="#depoimentos" class="footer-nav-link rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Depoimentos</a></li>
            <li><a href="#faq" class="footer-nav-link rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">FAQ</a></li>
            <li><a href="#contato" class="footer-nav-link rounded-md font-medium text-blue-400/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a] hover:text-blue-300">Contato</a></li>
          </ul>
        </nav>
        <div class="footer-reveal footer-stagger-2 md:col-span-4">
          <p class="font-heading text-[10px] font-semibold uppercase tracking-[0.2em] text-white/35">Ecossistema CodeRush</p>
          <p class="mt-2 text-xs leading-relaxed text-white/45">Marcas do grupo — cada uma com expertise própria.</p>
          <div class="mt-4 flex flex-wrap gap-2">
            <a href="https://sistemavendadireta.com.br" target="_blank" rel="noopener noreferrer" class="footer-eco-pill focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
              <span class="max-w-[11rem] truncate sm:max-w-none">sistemavendadireta.com.br</span>
              <i data-lucide="external-link" class="h-3 w-3 shrink-0 opacity-70" aria-hidden="true"></i>
            </a>
            <a href="https://codafacil.dev" target="_blank" rel="noopener noreferrer" class="footer-eco-pill focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
              codafacil.dev
              <i data-lucide="external-link" class="h-3 w-3 shrink-0 opacity-70" aria-hidden="true"></i>
            </a>
            <a href="https://wordpressconsultoria.com.br" target="_blank" rel="noopener noreferrer" class="footer-eco-pill focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
              <span class="max-w-[12rem] truncate sm:max-w-none">wordpressconsultoria.com.br</span>
              <i data-lucide="external-link" class="h-3 w-3 shrink-0 opacity-70" aria-hidden="true"></i>
            </a>
            <a href="https://fluxointeligenteia.com.br" target="_blank" rel="noopener noreferrer" class="footer-eco-pill focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">
              <span class="max-w-[12rem] truncate sm:max-w-none">fluxointeligenteia.com.br</span>
              <i data-lucide="external-link" class="h-3 w-3 shrink-0 opacity-70" aria-hidden="true"></i>
            </a>
          </div>
        </div>
      </div>
      <div class="footer-reveal footer-stagger-3 mt-12 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-8 text-xs text-white/40 md:flex-row md:items-center">
        <p>© <?= date('Y') ?> CodeRush — Todos os direitos reservados.</p>
        <p class="text-center text-white/35 md:text-right">
          <span class="hidden sm:inline">Hub do ecossistema · </span>
          <a href="#contato" class="font-medium text-blue-400/80 transition hover:text-blue-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 focus-visible:ring-offset-2 focus-visible:ring-offset-[#020b1a]">Solicitar contato</a>
        </p>
      </div>
    </div>
  </footer>

  <!-- Chat flutuante (assistente guiado) -->
  <div id="chat-backdrop" class="chat-backdrop" aria-hidden="true" tabindex="-1"></div>
  <div
    id="chat-panel"
    class="chat-panel flex flex-col"
    role="dialog"
    aria-modal="true"
    aria-labelledby="chat-widget-title"
    aria-hidden="true"
  >
    <div class="flex shrink-0 items-center justify-between rounded-t-[1.25rem] border-b border-white/10 bg-gradient-to-r from-blue-950/60 via-[#0f172a]/90 to-slate-900/50 px-4 py-3">
      <div class="min-w-0 pr-2">
        <p id="chat-widget-title" class="font-heading text-sm font-bold tracking-tight text-white">Assistente CodeRush</p>
        <p class="truncate text-[10px] text-white/45">Respostas instantâneas · time humano no formulário</p>
      </div>
      <button
        type="button"
        id="chat-close"
        class="shrink-0 rounded-lg p-2 text-white/65 transition hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/60"
        aria-label="Fechar assistente"
      >
        <i data-lucide="x" class="h-5 w-5" aria-hidden="true"></i>
      </button>
    </div>
    <div id="chat-messages" class="flex min-h-0 flex-1 flex-col gap-3 overflow-y-auto overscroll-contain p-4"></div>
    <div class="shrink-0 border-t border-white/10 p-3">
      <div id="chat-quick" class="mb-3 flex flex-wrap gap-2"></div>
      <form id="chat-form" class="flex items-end gap-2">
        <label for="chat-input" class="sr-only">Sua mensagem para o assistente</label>
        <textarea
          id="chat-input"
          class="chat-input"
          rows="1"
          placeholder="Ex.: preciso de um sistema com integração ERP…"
          maxlength="600"
          autocomplete="off"
        ></textarea>
        <button
          type="submit"
          class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand text-white shadow-md transition hover:bg-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/60"
          aria-label="Enviar mensagem"
        >
          <i data-lucide="send" class="h-4 w-4" aria-hidden="true"></i>
        </button>
      </form>
    </div>
  </div>
  <div class="fab-dock" role="group" aria-label="Ações rápidas">
    <button
      type="button"
      id="back-to-top"
      class="back-to-top-fab is-at-top"
      aria-label="Voltar ao topo da página"
    >
      <i data-lucide="chevron-up" class="h-6 w-6" aria-hidden="true"></i>
    </button>
    <button
      type="button"
      id="chat-launcher"
      class="chat-launcher"
      aria-expanded="false"
      aria-controls="chat-panel"
      aria-haspopup="dialog"
    >
      <span id="chat-launcher-label-open" class="sr-only">Abrir assistente virtual CodeRush</span>
      <span id="chat-launcher-label-close" class="sr-only hidden">Fechar assistente virtual</span>
      <i id="chat-launcher-icon-msg" data-lucide="message-circle" class="h-7 w-7" aria-hidden="true"></i>
      <i id="chat-launcher-icon-x" data-lucide="x" class="hidden h-7 w-7" aria-hidden="true"></i>
    </button>
  </div>

  <script>
    (function () {
      var btn = document.getElementById('back-to-top');
      if (!btn) return;
      var threshold = 120;
      function sync() {
        var atTop = window.scrollY < threshold;
        btn.classList.toggle('is-at-top', atTop);
        btn.setAttribute('aria-disabled', atTop ? 'true' : 'false');
        if (atTop) {
          btn.setAttribute('tabindex', '-1');
        } else {
          btn.removeAttribute('tabindex');
        }
      }
      btn.addEventListener('click', function () {
        if (btn.classList.contains('is-at-top')) return;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
          window.scrollTo(0, 0);
        } else {
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }
      });
      window.addEventListener('scroll', sync, { passive: true });
      sync();
    })();
  </script>
  <script>
    (function () {
      var header = document.querySelector('[data-site-header]');
      if (!header) return;
      function onScroll() {
        header.classList.toggle('is-scrolled', window.scrollY > 16);
      }
      onScroll();
      window.addEventListener('scroll', onScroll, { passive: true });
    })();
  </script>
  <script>
    (function () {
      var btn = document.getElementById('mobile-menu-btn');
      var menu = document.getElementById('mobile-menu');
      var iconOpen = document.getElementById('mobile-menu-icon-open');
      var iconClose = document.getElementById('mobile-menu-icon-close');
      if (!btn || !menu) return;

      function setOpen(open) {
        menu.classList.toggle('hidden', !open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        btn.setAttribute('aria-label', open ? 'Fechar menu de navegação' : 'Abrir menu de navegação');
        document.body.classList.toggle('overflow-hidden', open);
        if (iconOpen && iconClose) {
          iconOpen.classList.toggle('hidden', open);
          iconClose.classList.toggle('hidden', !open);
        }
      }

      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        setOpen(menu.classList.contains('hidden'));
      });

      menu.querySelectorAll('a[href^="#"]').forEach(function (a) {
        a.addEventListener('click', function () {
          setOpen(false);
        });
      });

      document.addEventListener('click', function (e) {
        if (menu.classList.contains('hidden')) return;
        if (!menu.contains(e.target) && !btn.contains(e.target)) {
          setOpen(false);
        }
      });

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') setOpen(false);
      });

      window.addEventListener('resize', function () {
        if (window.matchMedia('(min-width: 768px)').matches) setOpen(false);
      });
    })();
  </script>
  <script>
    (function () {
      var section = document.getElementById('cases');
      if (!section) return;
      function reveal() {
        section.classList.add('cases-visible');
      }
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        reveal();
        return;
      }
      if (typeof IntersectionObserver === 'undefined') {
        reveal();
        return;
      }
      var obs = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              reveal();
              obs.disconnect();
            }
          });
        },
        { threshold: 0.1, rootMargin: '0px 0px -6% 0px' }
      );
      obs.observe(section);
    })();
  </script>
  <script>
    (function () {
      var section = document.getElementById('depoimentos');
      if (!section) return;
      function reveal() {
        section.classList.add('testimonials-visible');
      }
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        reveal();
        return;
      }
      if (typeof IntersectionObserver === 'undefined') {
        reveal();
        return;
      }
      var obs = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              reveal();
              obs.disconnect();
            }
          });
        },
        { threshold: 0.1, rootMargin: '0px 0px -7% 0px' }
      );
      obs.observe(section);
    })();
  </script>
  <script>
    (function () {
      var section = document.getElementById('faq');
      if (!section) return;
      function reveal() {
        section.classList.add('faq-visible');
      }
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        reveal();
        return;
      }
      if (typeof IntersectionObserver === 'undefined') {
        reveal();
        return;
      }
      var obs = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              reveal();
              obs.disconnect();
            }
          });
        },
        { threshold: 0.12, rootMargin: '0px 0px -8% 0px' }
      );
      obs.observe(section);
    })();
  </script>
  <script>
    (function () {
      var section = document.getElementById('contato');
      if (!section) return;
      function reveal() {
        section.classList.add('contact-visible');
      }
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        reveal();
        return;
      }
      if (typeof IntersectionObserver === 'undefined') {
        reveal();
        return;
      }
      var obs = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              reveal();
              obs.disconnect();
            }
          });
        },
        { threshold: 0.08, rootMargin: '0px 0px -5% 0px' }
      );
      obs.observe(section);
    })();
  </script>
  <script>
    (function () {
      var section = document.getElementById('sobre');
      if (!section) return;
      function reveal() {
        section.classList.add('sobre-visible');
      }
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        reveal();
        return;
      }
      if (typeof IntersectionObserver === 'undefined') {
        reveal();
        return;
      }
      var obs = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              reveal();
              obs.disconnect();
            }
          });
        },
        { threshold: 0.1, rootMargin: '0px 0px -6% 0px' }
      );
      obs.observe(section);
    })();
  </script>
  <script>
    (function () {
      var section = document.getElementById('site-footer');
      if (!section) return;
      function reveal() {
        section.classList.add('footer-visible');
      }
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        reveal();
        return;
      }
      if (typeof IntersectionObserver === 'undefined') {
        reveal();
        return;
      }
      var obs = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              reveal();
              obs.disconnect();
            }
          });
        },
        { threshold: 0.08, rootMargin: '0px 0px -4% 0px' }
      );
      obs.observe(section);
    })();
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var params = new URLSearchParams(window.location.search);
      var mail = params.get('mail');
      var feedback = document.getElementById('form-feedback');
      if (mail && feedback) {
        feedback.classList.remove('hidden');
        if (mail === 'ok') {
          feedback.className =
            'mb-6 rounded-2xl border border-emerald-500/40 bg-emerald-500/[0.12] p-4 text-sm text-emerald-100';
          feedback.textContent =
            'Mensagem enviada com sucesso. Nossa equipe retornará em breve pelo e-mail ou telefone informados.';
        } else {
          feedback.className =
            'mb-6 rounded-2xl border border-red-500/40 bg-red-500/[0.12] p-4 text-sm text-red-100';
          feedback.textContent =
            'Não foi possível concluir o envio. Tente novamente em instantes ou escreva para contato@coderush.com.br.';
        }
        if (window.location.hash !== '#contato') {
          window.location.hash = '#contato';
        }
        if (window.history && window.history.replaceState) {
          window.history.replaceState(null, '', window.location.pathname + '#contato');
        }
        feedback.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      }

      var form = document.getElementById('form-contato');
      var btn = document.getElementById('form-submit-btn');
      if (form && btn) {
        form.addEventListener('submit', function () {
          btn.disabled = true;
          btn.setAttribute('aria-busy', 'true');
        });
      }
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/lucide@0.469.0/dist/umd/lucide.min.js" crossorigin="anonymous"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
        lucide.createIcons();
      }

      (function initCodeRushChat() {
        var backdrop = document.getElementById('chat-backdrop');
        var panel = document.getElementById('chat-panel');
        var launcher = document.getElementById('chat-launcher');
        var closeBtn = document.getElementById('chat-close');
        var messagesEl = document.getElementById('chat-messages');
        var quickEl = document.getElementById('chat-quick');
        var form = document.getElementById('chat-form');
        var input = document.getElementById('chat-input');
        var iconMsg = document.getElementById('chat-launcher-icon-msg');
        var iconX = document.getElementById('chat-launcher-icon-x');
        var labelOpen = document.getElementById('chat-launcher-label-open');
        var labelClose = document.getElementById('chat-launcher-label-close');
        if (!backdrop || !panel || !launcher || !messagesEl || !quickEl || !form || !input) return;

        var chatOpen = false;
        var initialized = false;
        var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        function icons() {
          if (typeof lucide !== 'undefined' && lucide.createIcons) lucide.createIcons();
        }

        function scrollChatBottom() {
          messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function appendBot(text) {
          var div = document.createElement('div');
          div.className = 'chat-msg chat-msg--bot';
          div.textContent = text;
          messagesEl.appendChild(div);
          scrollChatBottom();
        }

        function appendUser(text) {
          var div = document.createElement('div');
          div.className = 'chat-msg chat-msg--user';
          div.textContent = text;
          messagesEl.appendChild(div);
          scrollChatBottom();
        }

        function appendBotWithCta(text, cta) {
          var wrap = document.createElement('div');
          wrap.className = 'chat-msg chat-msg--bot';
          var p = document.createElement('p');
          p.className = 'mb-2 last:mb-0';
          p.textContent = text;
          wrap.appendChild(p);
          if (cta && cta.href) {
            var a = document.createElement('a');
            a.href = cta.href;
            a.className = 'inline-flex items-center gap-1 font-semibold text-blue-400 underline decoration-blue-400/40 underline-offset-2 hover:text-blue-300';
            a.textContent = cta.label || 'Abrir';
            if (cta.external) {
              a.target = '_blank';
              a.rel = 'noopener noreferrer';
            }
            wrap.appendChild(a);
          }
          messagesEl.appendChild(wrap);
          scrollChatBottom();
        }

        function showTyping(then) {
          var row = document.createElement('div');
          row.className = 'chat-msg chat-msg--bot chat-typing';
          row.setAttribute('aria-hidden', 'true');
          row.innerHTML = '<span></span><span></span><span></span>';
          messagesEl.appendChild(row);
          scrollChatBottom();
          var ms = reduceMotion ? 80 : 520;
          setTimeout(function () {
            if (row.parentNode) row.parentNode.removeChild(row);
            then();
          }, ms);
        }

        function clearQuick() {
          quickEl.innerHTML = '';
        }

        function renderQuick(items) {
          clearQuick();
          items.forEach(function (item) {
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'chat-chip';
            b.textContent = item.label;
            b.addEventListener('click', function () {
              item.onClick();
            });
            quickEl.appendChild(b);
          });
        }

        function setOpen(open) {
          chatOpen = open;
          backdrop.classList.toggle('is-open', open);
          panel.classList.toggle('is-open', open);
          backdrop.setAttribute('aria-hidden', open ? 'false' : 'true');
          panel.setAttribute('aria-hidden', open ? 'false' : 'true');
          launcher.setAttribute('aria-expanded', open ? 'true' : 'false');
          launcher.classList.toggle('is-open', open);
          document.body.classList.toggle('overflow-hidden', open);
          if (iconMsg && iconX) {
            iconMsg.classList.toggle('hidden', open);
            iconX.classList.toggle('hidden', !open);
          }
          if (labelOpen && labelClose) {
            labelOpen.classList.toggle('hidden', open);
            labelClose.classList.toggle('hidden', !open);
          }
          icons();
          if (open) {
            if (!initialized) {
              initialized = true;
              appendBot(
                'Olá! Sou o assistente virtual do ecossistema CodeRush. Posso direcionar você para a linha certa — escolha um tema ou escreva sua dúvida abaixo.'
              );
              renderQuick([
                {
                  label: 'Vendas diretas / MMN',
                  onClick: function () {
                    topicFlow('mmn', 'Quero falar sobre vendas diretas / MMN');
                  },
                },
                {
                  label: 'Software & IA',
                  onClick: function () {
                    topicFlow('dev', 'Preciso de software sob medida / IA');
                  },
                },
                {
                  label: 'WordPress & loja',
                  onClick: function () {
                    topicFlow('wp', 'Tenho projeto WordPress / e-commerce');
                  },
                },
                {
                  label: 'Automação & n8n',
                  onClick: function () {
                    topicFlow('auto', 'Quero automação com IA / n8n');
                  },
                },
                {
                  label: 'Falar com humano',
                  onClick: function () {
                    topicFlow('human', 'Quero falar com um especialista');
                  },
                },
                {
                  label: 'Ver empresas do grupo',
                  onClick: function () {
                    appendUser('Ver empresas do grupo');
                    showTyping(function () {
                      appendBotWithCta(
                        'Aqui estão as marcas do ecossistema. Cada uma tem site próprio — clique em Visitar no portfólio.',
                        { href: '#empresas', label: 'Ir para Empresas', external: false }
                      );
                      renderQuick([ctaFormChip()]);
                    });
                    window.location.hash = '#empresas';
                  },
                },
              ]);
            }
            setTimeout(function () {
              input.focus();
            }, reduceMotion ? 0 : 280);
          }
        }

        function ctaFormChip() {
          return {
            label: 'Abrir formulário de contato',
            onClick: function () {
              window.location.hash = '#contato';
              setOpen(false);
              var el = document.getElementById('contato');
              if (el) el.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth' });
            },
          };
        }

        function topicFlow(key, userLine) {
          clearQuick();
          appendUser(userLine);
          var copy = {
            mmn: {
              t:
                'Para plataformas de vendas diretas e MMN, o **Sistema Venda Direta** oferece comissionamento, lojas e backoffice em escala. O próximo passo ideal é conversar com o time pelo formulário.',
              link: { href: '/sistemavendadireta/', label: 'Visitar sistemavendadireta.com.br', external: false },
            },
            dev: {
              t:
                'Para software sob medida, IA e integrações, a **Codafacil.dev** trabalha com Laravel, entregas ágeis e stack moderna. Deixe seu briefing no formulário para retorno técnico.',
              link: { href: '/codafacil/', label: 'Ver Codafacil.dev', external: false },
            },
            wp: {
              t:
                'Para WordPress, WooCommerce e integrações (ERP, pagamentos), a **WordPress Consultoria** desenvolve plugins e lojas sob medida.',
              link: { href: '/wordpressconsultoria/', label: 'Ver WordPress Consultoria', external: false },
            },
            auto: {
              t:
                'Para automação com **n8n**, agentes e IA aplicada ao negócio, o **FluxoInteligente IA** desenha fluxos e integrações.',
              link: { href: '/fluxointeligenteia/', label: 'Ver FluxoInteligente IA', external: false },
            },
            human: {
              t:
                'Perfeito. O melhor canal para um retorno personalizado é o **formulário de contato** com nome, e-mail e interesse — nossa equipe responde sem fila robótica.',
              link: null,
            },
          };
          var pack = copy[key] || copy.human;
          showTyping(function () {
            var text = pack.t.replace(/\*\*(.+?)\*\*/g, '$1');
            appendBot(text);
            if (pack.link) {
              var a = document.createElement('div');
              a.className = 'chat-msg chat-msg--bot mt-2';
              var link = document.createElement('a');
              link.href = pack.link.href;
              link.className = 'font-semibold text-blue-400 underline decoration-blue-400/40 underline-offset-2 hover:text-blue-300';
              link.textContent = pack.link.label;
              if (pack.link.external) {
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
              }
              a.appendChild(link);
              messagesEl.appendChild(a);
              scrollChatBottom();
            }
            renderQuick([ctaFormChip(), { label: 'Outra dúvida', onClick: function () { renderQuick(resetMainMenu()); } }]);
          });
        }

        function resetMainMenu() {
          return [
            {
              label: 'Vendas diretas / MMN',
              onClick: function () {
                topicFlow('mmn', 'Quero falar sobre vendas diretas / MMN');
              },
            },
            {
              label: 'Software & IA',
              onClick: function () {
                topicFlow('dev', 'Preciso de software sob medida / IA');
              },
            },
            {
              label: 'WordPress & loja',
              onClick: function () {
                topicFlow('wp', 'Tenho projeto WordPress / e-commerce');
              },
            },
            {
              label: 'Automação & n8n',
              onClick: function () {
                topicFlow('auto', 'Quero automação com IA / n8n');
              },
            },
            {
              label: 'Falar com humano',
              onClick: function () {
                topicFlow('human', 'Quero falar com um especialista');
              },
            },
          ];
        }

        function handleFreeText(raw) {
          var t = raw.trim();
          if (t.length < 2) return;
          appendUser(t);
          input.value = '';
          var lower = t.toLowerCase();
          showTyping(function () {
            if (/orçamento|orcamento|preço|preco|proposta|contratar|projeto/i.test(lower)) {
              appendBot(
                'Entendi que você busca uma proposta ou orçamento. O formulário de contato reúne escopo, prazo e canal — nossa equipe retorna com o próximo passo.'
              );
            } else if (/prazo|urgente|quando/i.test(lower)) {
              appendBot(
                'Prazos variam por escopo. Informe datas desejadas e complexidade no formulário — assim conseguimos alinhar expectativa e prioridade.'
              );
            } else {
              appendBot(
                'Obrigado pela mensagem! Para direcionar ao time certo, use o formulário abaixo com nome, e-mail e tipo de projeto — resposta humana e objetiva.'
              );
            }
            clearQuick();
            renderQuick([ctaFormChip(), { label: 'Ver FAQ', onClick: function () { window.location.hash = '#faq'; setOpen(false); } }]);
          });
        }

        launcher.addEventListener('click', function () {
          setOpen(!chatOpen);
        });
        if (closeBtn) {
          closeBtn.addEventListener('click', function () {
            setOpen(false);
          });
        }
        backdrop.addEventListener('click', function () {
          setOpen(false);
        });
        form.addEventListener('submit', function (e) {
          e.preventDefault();
          handleFreeText(input.value);
        });

        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape' && chatOpen) {
            setOpen(false);
          }
        });

        input.addEventListener('input', function () {
          input.style.height = 'auto';
          input.style.height = Math.min(input.scrollHeight, 120) + 'px';
        });
      })();
    });
  </script>

</body>
</html>
