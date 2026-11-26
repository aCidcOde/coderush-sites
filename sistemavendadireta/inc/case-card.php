<?php
/*
[Modulo Cases SVD — card de um case]
@Author: Andre Gomes ( @acidcode )
@since 2026-11-26
Markup de UM case, usado por cases/index.php nas duas listas (sistemas em producao e
produtos em desenvolvimento). Espera $case no escopo. Os caminhos sao relativos a /cases/.

Campos opcionais alem dos de inc/cases.php: badge (selo acima do segmento), urlLabel
(texto do botao), semLinkLabel (texto quando nao ha link), highlightsLabel (rotulo acima da lista), detailsLabel (titulo do bloco
"O que foi entregue"), shots (fotos).
*/
if (!isset($case) || !is_array($case)) {
    http_response_code(404);
    return;
}
?>
      <section id="<?= htmlspecialchars($case['slug'], ENT_QUOTES, 'UTF-8') ?>" class="scroll-mt-28 py-6">
        <article class="rounded-[30px] border border-white/25 bg-white/[0.06] p-6 sm:p-8">
          <div class="grid gap-6 lg:grid-cols-[auto_1fr] lg:items-center">
            <div class="flex w-full max-w-[360px] items-center justify-center rounded-2xl <?= (($case['logoBg'] ?? '') === 'escuro') ? 'bg-slate-900' : 'bg-white' ?> px-6 py-5">
              <?php if (empty($case['logoFallback'])): ?>
                <?php /* sem logo em arquivo (26/11/2026: Isy Middleware, Wellby) — o nome
                         em texto e melhor que logo inventado */ ?>
                <span class="font-[var(--font-heading)] text-2xl font-semibold" style="color:<?= (($case['logoBg'] ?? '') === 'escuro') ? '#fff' : '#0f172a' ?>"><?= htmlspecialchars($case['name'], ENT_QUOTES, 'UTF-8') ?></span>
              <?php else: ?>
              <picture>
                <?php /* source so quando o arquivo existe: <source> apontando pra 404 NAO
         cai no <img>, mostra imagem quebrada */ ?>
                <?php if (is_file(__DIR__ . '/../' . $case['logo'])): ?>
                <source srcset="../<?= htmlspecialchars($case['logo'], ENT_QUOTES, 'UTF-8') ?>" type="image/webp" />
                <?php endif; ?>
                <img
                  src="../<?= htmlspecialchars($case['logoFallback'], ENT_QUOTES, 'UTF-8') ?>"
                  alt="<?= htmlspecialchars($case['name'], ENT_QUOTES, 'UTF-8') ?>"
                  class="h-16 w-auto max-w-full object-contain sm:h-16"
                  width="<?= (int) $case['logoWidth'] ?>"
                  height="<?= (int) $case['logoHeight'] ?>"
                  loading="lazy"
                />
              </picture>
              <?php endif; ?>
            </div>

            <div>
              <?php if (!empty($case['badge'])): ?>
                <p class="inline-flex rounded-full border border-amber-300/60 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-200" style="margin-bottom:.75rem;background:rgba(252,211,77,.1)"><?= htmlspecialchars($case['badge'], ENT_QUOTES, 'UTF-8') ?></p>
              <?php endif; ?>
              <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/70">
                <?= htmlspecialchars($case['segment'], ENT_QUOTES, 'UTF-8') ?> • <?= htmlspecialchars($case['period'], ENT_QUOTES, 'UTF-8') ?>
              </p>
              <h2 class="mt-2 font-[var(--font-heading)] text-2xl font-semibold sm:text-[30px]"><?= htmlspecialchars($case['name'], ENT_QUOTES, 'UTF-8') ?></h2>
              <p class="mt-3 max-w-3xl text-base leading-relaxed text-white/90"><?= htmlspecialchars($case['summary'], ENT_QUOTES, 'UTF-8') ?></p>
              <?php if (!empty($case['url'])): ?>
                <a
                  href="<?= htmlspecialchars($case['url'], ENT_QUOTES, 'UTF-8') ?>"
                  target="_blank"
                  rel="noopener"
                  class="mt-5 inline-flex rounded-full border border-white/70 px-5 py-2.5 text-sm font-semibold uppercase tracking-wide hover:bg-white/10"
                >
                  <?= htmlspecialchars($case['urlLabel'] ?? 'Visitar operação', ENT_QUOTES, 'UTF-8') ?>
                </a>
              <?php else: ?>
                <p class="mt-5 inline-flex rounded-full border border-white/30 px-5 py-2.5 text-sm font-semibold uppercase tracking-wide text-white/70">
                  <?= htmlspecialchars($case['semLinkLabel'] ?? 'Sistema interno do cliente', ENT_QUOTES, 'UTF-8') ?>
                </p>
              <?php endif; ?>
            </div>
          </div>

          <?php if (!empty($case['highlights'])): ?>
            <?php if (!empty($case['highlightsLabel'])): ?>
              <p class="mt-6 text-xs font-semibold uppercase tracking-[0.18em] text-white/70"><?= htmlspecialchars($case['highlightsLabel'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
            <ul class="<?= !empty($case['highlightsLabel']) ? 'mt-3' : 'mt-6' ?> grid gap-2 text-sm text-white/90 sm:grid-cols-2">
              <?php foreach ($case['highlights'] as $highlight): ?>
                <li class="flex items-start gap-2 rounded-xl border border-white/20 bg-white/5 px-3 py-2"><svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg><span><?= htmlspecialchars($highlight, ENT_QUOTES, 'UTF-8') ?></span></li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>

          <?php if (!empty($case['metrics'])): ?>
            <div class="mt-7 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
              <?php foreach ($case['metrics'] as $metric): ?>
                <div class="rounded-2xl border border-white/20 bg-white/5 px-4 py-3">
                  <p class="font-[var(--font-heading)] text-2xl font-semibold"><?= htmlspecialchars($metric['value'], ENT_QUOTES, 'UTF-8') ?></p>
                  <p class="mt-1 text-sm text-white/80"><?= htmlspecialchars($metric['label'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <?php /* Fotos do projeto no ar (26/11/2026, pedido do usuario: "tira fotos dos
                   projetos pra completar o conteudo"). So paginas publicas — tela de login
                   nao entra, e nenhuma foto mostra dado pessoal. */ ?>
          <?php if (!empty($case['shots'])): ?>
            <div class="mt-7 grid gap-4<?= count($case['shots']) > 1 ? ' md:grid-cols-2' : ' max-w-3xl' ?>">
              <?php foreach ($case['shots'] as $shot): ?>
                <figure class="overflow-hidden rounded-2xl border border-white/20 bg-white/5">
                  <img src="../<?= htmlspecialchars($shot['src'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($shot['alt'], ENT_QUOTES, 'UTF-8') ?>" class="block w-full" style="height:auto" width="1200" height="750" loading="lazy" decoding="async" />
                  <?php if (!empty($shot['legenda'])): ?>
                    <figcaption class="px-4 py-3 text-xs leading-6 text-white/70 sm:text-sm"><?= htmlspecialchars($shot['legenda'], ENT_QUOTES, 'UTF-8') ?></figcaption>
                  <?php endif; ?>
                </figure>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($case['details'])): ?>
          <h3 class="mt-8 font-[var(--font-heading)] text-xl font-semibold"><?= htmlspecialchars($case['detailsLabel'] ?? 'O que foi entregue', ENT_QUOTES, 'UTF-8') ?></h3>
          <div class="mt-4 grid gap-4<?= count($case['details']) > 1 ? ' md:grid-cols-2' : '' ?>">
            <?php foreach ($case['details'] as $detail): ?>
              <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
                <h4 class="font-[var(--font-heading)] text-base font-semibold"><?= htmlspecialchars($detail['title'], ENT_QUOTES, 'UTF-8') ?></h4>
                <p class="mt-2 text-sm leading-relaxed text-white/90"><?= htmlspecialchars($detail['body'], ENT_QUOTES, 'UTF-8') ?></p>
              </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </article>
      </section>
