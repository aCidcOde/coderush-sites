    <section id="cases" class="py-10">
      <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
          <h3 class="font-[var(--font-heading)] text-[30px] font-semibold">Cases de Sucesso</h3>
          <div class="mt-2 h-1 w-[72px] rounded-full bg-white"></div>
          <p class="mt-4 max-w-2xl text-sm text-white/85 sm:text-base">Mock inicial de cases para você abastecer depois com dados reais, mantendo padrão visual e foco em resultado.</p>
        </div>
        <a href="#contato" class="inline-flex rounded-full border border-white/70 px-5 py-2.5 text-sm font-semibold uppercase tracking-wide hover:bg-white/10">Quero um case assim</a>
      </div>

      <div class="mt-6 flex flex-wrap gap-2" role="tablist" aria-label="Filtrar cases">
        <button type="button" class="case-filter rounded-full border border-white/30 bg-white/15 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white/90" data-case-filter="todos" aria-pressed="true">Todos</button>
        <button type="button" class="case-filter rounded-full border border-white/30 bg-transparent px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white/80" data-case-filter="mmn" aria-pressed="false">MMN</button>
        <button type="button" class="case-filter rounded-full border border-white/30 bg-transparent px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white/80" data-case-filter="ecommerce" aria-pressed="false">E-commerce</button>
        <button type="button" class="case-filter rounded-full border border-white/30 bg-transparent px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white/80" data-case-filter="integracao" aria-pressed="false">Integrações</button>
      </div>

      <div class="mt-4 rounded-2xl border border-white/20 bg-white/[0.05] p-4">
        <div class="flex gap-3 overflow-x-auto pb-1">
          <div class="min-w-[220px] flex-1 rounded-xl border border-white/20 bg-white/10 p-3">
            <p class="text-[11px] text-white/75">Projetos Entregues</p>
            <p class="mt-1 text-xl font-bold" data-case-kpi-target="126">126</p>
          </div>
          <div class="min-w-[220px] flex-1 rounded-xl border border-white/20 bg-white/10 p-3">
            <p class="text-[11px] text-white/75">Tempo Médio de Go-live</p>
            <p class="mt-1 text-xl font-bold" data-case-kpi-target="45" data-case-kpi-suffix=" dias">45 dias</p>
          </div>
          <div class="min-w-[220px] flex-1 rounded-xl border border-white/20 bg-white/10 p-3">
            <p class="text-[11px] text-white/75">Aumento Médio de Eficiência</p>
            <p class="mt-1 text-xl font-bold" data-case-kpi-target="37" data-case-kpi-prefix="+">+37%</p>
          </div>
        </div>
      </div>

      <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3" data-cases-grid>
        <article class="case-card rounded-3xl border border-white/25 bg-white/[0.06] p-5 transition duration-300 hover:-translate-y-1 hover:bg-white/[0.1] hover:shadow-[0_14px_28px_rgba(0,0,0,0.2)]" data-case-category="mmn">
          <div class="flex items-center justify-between">
            <span class="rounded-full border border-white/25 px-2.5 py-1 text-[11px] text-white/80">MMN</span>
            <span class="text-xs text-emerald-200">+38% retenção</span>
          </div>
          <h4 class="mt-4 font-[var(--font-heading)] text-lg font-semibold">Case Rede Global Nutri</h4>
          <p class="mt-2 text-sm text-white/85">Reestruturação de escritório virtual e regra de comissionamento com ganho direto em produtividade da rede.</p>
          <div class="mt-4 grid grid-cols-2 gap-2 text-[11px] text-white/80">
            <div class="rounded-xl border border-white/20 px-2 py-1.5">90 dias</div>
            <div class="rounded-xl border border-white/20 px-2 py-1.5">12 países</div>
          </div>
          <div class="mt-3 grid grid-cols-3 gap-2 text-[11px]">
            <div class="rounded-xl bg-white/10 px-2 py-1.5 text-center">NPS 89</div>
            <div class="rounded-xl bg-white/10 px-2 py-1.5 text-center">ERP</div>
            <div class="rounded-xl bg-white/10 px-2 py-1.5 text-center">Mobile</div>
          </div>
          <details class="mt-3 rounded-xl border border-white/20 bg-white/5 p-3">
            <summary class="cursor-pointer text-xs font-semibold uppercase tracking-wide text-white/90">Ver detalhes do projeto</summary>
            <div class="mt-2 space-y-2 text-sm text-white/85">
              <p><strong>Desafio:</strong> baixa adesão ao escritório virtual e alto retrabalho operacional.</p>
              <p><strong>Solução:</strong> revisão completa da jornada do distribuidor e automação de comissões.</p>
              <p><strong>Resultado:</strong> retenção da rede +38% e redução de chamados em 29%.</p>
            </div>
          </details>
        </article>

        <article class="case-card rounded-3xl border border-white/25 bg-white/[0.06] p-5 transition duration-300 hover:-translate-y-1 hover:bg-white/[0.1] hover:shadow-[0_14px_28px_rgba(0,0,0,0.2)]" data-case-category="ecommerce">
          <div class="flex items-center justify-between">
            <span class="rounded-full border border-white/25 px-2.5 py-1 text-[11px] text-white/80">E-commerce</span>
            <span class="text-xs text-emerald-200">+24% ticket médio</span>
          </div>
          <h4 class="mt-4 font-[var(--font-heading)] text-lg font-semibold">Case Vita Shop</h4>
          <p class="mt-2 text-sm text-white/85">Evolução do checkout e automação de pedidos com redução de abandono e aumento de conversão no mobile.</p>
          <div class="mt-4 grid grid-cols-2 gap-2 text-[11px] text-white/80">
            <div class="rounded-xl border border-white/20 px-2 py-1.5">Checkout otimizado</div>
            <div class="rounded-xl border border-white/20 px-2 py-1.5">Mobile first</div>
          </div>
          <div class="mt-3 grid grid-cols-3 gap-2 text-[11px]">
            <div class="rounded-xl bg-white/10 px-2 py-1.5 text-center">+19% conversão</div>
            <div class="rounded-xl bg-white/10 px-2 py-1.5 text-center">PIX</div>
            <div class="rounded-xl bg-white/10 px-2 py-1.5 text-center">Antifraude</div>
          </div>
          <details class="mt-3 rounded-xl border border-white/20 bg-white/5 p-3">
            <summary class="cursor-pointer text-xs font-semibold uppercase tracking-wide text-white/90">Ver detalhes do projeto</summary>
            <div class="mt-2 space-y-2 text-sm text-white/85">
              <p><strong>Desafio:</strong> abandono alto no checkout e baixa performance mobile.</p>
              <p><strong>Solução:</strong> simplificação de etapas, UX mobile e otimização de aprovação de pagamento.</p>
              <p><strong>Resultado:</strong> ticket médio +24% e redução de abandono em 18%.</p>
            </div>
          </details>
        </article>

        <article class="case-card rounded-3xl border border-white/25 bg-white/[0.06] p-5 transition duration-300 hover:-translate-y-1 hover:bg-white/[0.1] hover:shadow-[0_14px_28px_rgba(0,0,0,0.2)]" data-case-category="integracao">
          <div class="flex items-center justify-between">
            <span class="rounded-full border border-white/25 px-2.5 py-1 text-[11px] text-white/80">Integrações</span>
            <span class="text-xs text-emerald-200">-41% retrabalho</span>
          </div>
          <h4 class="mt-4 font-[var(--font-heading)] text-lg font-semibold">Case Operação Conecta</h4>
          <p class="mt-2 text-sm text-white/85">Sincronização ERP + logística + pagamentos para operação mais previsível e fluxo ponta a ponta rastreável.</p>
          <div class="mt-4 grid grid-cols-2 gap-2 text-[11px] text-white/80">
            <div class="rounded-xl border border-white/20 px-2 py-1.5">API ERP</div>
            <div class="rounded-xl border border-white/20 px-2 py-1.5">Rastreio</div>
          </div>
          <div class="mt-3 grid grid-cols-3 gap-2 text-[11px]">
            <div class="rounded-xl bg-white/10 px-2 py-1.5 text-center">99.9% uptime</div>
            <div class="rounded-xl bg-white/10 px-2 py-1.5 text-center">Webhooks</div>
            <div class="rounded-xl bg-white/10 px-2 py-1.5 text-center">BI</div>
          </div>
          <details class="mt-3 rounded-xl border border-white/20 bg-white/5 p-3">
            <summary class="cursor-pointer text-xs font-semibold uppercase tracking-wide text-white/90">Ver detalhes do projeto</summary>
            <div class="mt-2 space-y-2 text-sm text-white/85">
              <p><strong>Desafio:</strong> falhas de sincronização entre pedidos, faturamento e expedição.</p>
              <p><strong>Solução:</strong> camada de integração com monitoramento e retentativas automáticas.</p>
              <p><strong>Resultado:</strong> retrabalho -41% e tempo de processamento -33%.</p>
            </div>
          </details>
        </article>
      </div>
    </section>
