<?php
/**
 * Secção "Empresas que já atendemos" — mesmo padrão visual da secção Integrações (tags + carrosséis com nomes em texto).
 * Opcional: $svd_empresas_include_header = false para emitir só tags + marquees (ex.: página IA com título próprio).
 * Lista: $clientesMarqueeBrands (array de strings); se ausente, usa lista padrão abaixo.
 */
if (!isset($clientesMarqueeBrands) || !is_array($clientesMarqueeBrands) || $clientesMarqueeBrands === []) {
    $clientesMarqueeBrands = [
        'Mauá Bank',
        'Game Station',
        'AZE Games',
        'Boliche Game Station',
        'Bolix boliche',
        'SP Diversões',
        'TELSAN',
        'Moda do Chef',
        'TOP GAME',
        'journeybike',
        'BILLIONS Investimentos',
        'ECOTREND',
        'ELIBELL',
        'organofoods',
        'SCIENCE LIFE WORLD',
        'HAIFLEX',
        'BBOM+',
        'SUSHITOP',
        'econ',
        'Clean Bit',
        'XLR INVEST',
        'UZI NATURAL BRASIL',
        'moove',
    ];
}
$svd_empresas_include_header = isset($svd_empresas_include_header) ? (bool) $svd_empresas_include_header : true;
$svd_empresas_wrap = $svd_empresas_include_header ? 'section' : 'div';
$svd_empresas_wrap_class = $svd_empresas_include_header
  ? 'svd-empresas scroll-mt-28 py-12 md:py-20'
  : 'svd-empresas';
?>
<<?php echo $svd_empresas_wrap; ?>
  <?php if ($svd_empresas_include_header) : ?>id="empresas-atendidas"<?php endif; ?>
  class="<?php echo htmlspecialchars($svd_empresas_wrap_class, ENT_QUOTES, 'UTF-8'); ?>"
  <?php if ($svd_empresas_include_header) : ?>aria-labelledby="heading-empresas-atendidas"<?php else : ?>aria-label="Empresas atendidas"<?php endif; ?>
  data-svd-empresas-section
>
  <?php if ($svd_empresas_include_header) : ?>
  <header class="svd-empresas__intro svd-section-head max-w-3xl">
    <p class="svd-section-head__eyebrow">Marcas e operações</p>
    <h2 id="heading-empresas-atendidas" class="svd-section-head__title">Empresas que nós já atendemos</h2>
    <p class="svd-section-head__lead">
      Operações reais que já rodam com a plataforma — de bancos e entretenimento a suplementos, energia e investimentos.
    </p>
  </header>
  <?php endif; ?>

  <div class="svd-empresas__tags mt-6 flex flex-wrap gap-2" aria-hidden="true">
    <span class="rounded-full border border-white/12 bg-white/5 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-white/65">MMN &amp; venda direta</span>
    <span class="rounded-full border border-white/12 bg-white/5 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-white/65">Entretenimento</span>
    <span class="rounded-full border border-white/12 bg-white/5 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-white/65">Varejo &amp; food</span>
    <span class="rounded-full border border-white/12 bg-white/5 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-white/65">Investimentos</span>
    <span class="rounded-full border border-white/12 bg-white/5 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-white/65">Saúde &amp; energia</span>
  </div>

  <div class="svd-empresas__marquees <?php echo $svd_empresas_include_header ? 'mt-10' : 'mt-6'; ?> space-y-4">
    <div class="integrations-marquee-wrap" role="region" aria-label="Empresas em rotação contínua">
      <div class="integrations-marquee-inner">
        <div class="integrations-marquee-segment">
          <?php foreach ($clientesMarqueeBrands as $brandLabel) : ?>
            <div class="integrations-marquee-item">
              <p class="integrations-marquee-item--text"><?= htmlspecialchars($brandLabel, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="integrations-marquee-segment" aria-hidden="true">
          <?php foreach ($clientesMarqueeBrands as $brandLabel) : ?>
            <div class="integrations-marquee-item">
              <p class="integrations-marquee-item--text"><?= htmlspecialchars($brandLabel, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="integrations-marquee-wrap" aria-hidden="true">
      <div class="integrations-marquee-inner integrations-marquee-inner--alt">
        <div class="integrations-marquee-segment">
          <?php foreach ($clientesMarqueeBrands as $brandLabel) : ?>
            <div class="integrations-marquee-item">
              <p class="integrations-marquee-item--text"><?= htmlspecialchars($brandLabel, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="integrations-marquee-segment" aria-hidden="true">
          <?php foreach ($clientesMarqueeBrands as $brandLabel) : ?>
            <div class="integrations-marquee-item">
              <p class="integrations-marquee-item--text"><?= htmlspecialchars($brandLabel, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <?php if ($svd_empresas_include_header) : ?>
  <a
    href="#form"
    class="svd-empresas__cta mt-10 inline-flex min-h-[48px] items-center justify-center rounded-full border border-white/65 bg-white/10 px-6 py-3 text-sm font-bold uppercase tracking-[0.12em] text-white shadow-[0_12px_36px_rgba(0,0,0,0.2)] backdrop-blur-sm transition duration-300 hover:-translate-y-0.5 hover:border-white/85 hover:bg-white/18 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
  >
    Solicitar orçamento
  </a>
  <?php endif; ?>
</<?php echo $svd_empresas_wrap; ?>>
