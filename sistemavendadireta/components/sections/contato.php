<?php
$contactMailStatus = isset($mailStatus) && is_string($mailStatus) && in_array($mailStatus, ['ok', 'erro'], true) ? $mailStatus : '';
$contactRedirectValue = isset($contactRedirect) && is_string($contactRedirect) && $contactRedirect !== ''
    ? $contactRedirect
    : ((dirname($_SERVER['SCRIPT_NAME'] ?? '/') === DIRECTORY_SEPARATOR ? '' : rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/')) . '/');
?>
    <section id="form" class="scroll-mt-28 py-10">
      <h4 id="contato" class="font-[var(--font-heading)] text-[30px] font-semibold">Fale conosco</h4>
      <div class="mt-2 h-1 w-[72px] rounded-full bg-white"></div>

      <?php if ($contactMailStatus === 'ok'): ?>
        <div class="mt-6 rounded-2xl border border-emerald-300/35 bg-emerald-500/10 px-4 py-3 text-sm text-white/90">
          Recebemos seus dados. Vamos abrir o WhatsApp com uma mensagem pronta para agilizar seu atendimento.
        </div>
      <?php elseif ($contactMailStatus === 'erro'): ?>
        <div class="mt-6 rounded-2xl border border-rose-300/35 bg-rose-500/10 px-4 py-3 text-sm text-white/90">
          Nao conseguimos enviar seus dados agora. Revise o WhatsApp informado ou use o atalho direto para falar com o comercial.
        </div>
      <?php endif; ?>

      <div class="mt-6 grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
        <div class="space-y-5">
          <h2 class="text-base leading-relaxed text-white/90 sm:text-lg">
            Preencha seu nome e WhatsApp para nosso time comercial retornar com mais contexto sobre seu projeto.
            Assim conseguimos alinhar objetivo, prazo e proximos passos sem perder tempo na primeira conversa.
          </h2>
          <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Atalho direto</p>
            <h3 class="mt-2 text-lg font-semibold">Prefere falar agora?</h3>
            <p class="mt-2 text-sm text-white/85">Se quiser pular o formulario, fale direto com nossa equipe comercial no WhatsApp.</p>
            <a href="https://wa.me/5511994566726?text=Ol%C3%A1%2C%20quero%20um%20or%C3%A7amento%20e%20acesso%20%C3%A0%20demonstra%C3%A7%C3%A3o%20do%20Sistema%20Venda%20Direta." target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex w-full items-center justify-center rounded-full border border-white/70 px-4 py-2.5 text-sm font-semibold hover:bg-white/10">Falar no WhatsApp agora</a>
            <p class="mt-3 text-sm text-white/70"><b>Telefone para Contato:</b> 11 99456-6726</p>
          </div>
        </div>

        <div class="rounded-[28px] border border-white/20 bg-white/5 p-5 sm:p-6">
          <h3 class="text-lg font-semibold">Receber contato comercial</h3>
          <p class="mt-2 text-sm text-white/85">Enviamos seu lead para a equipe e, na sequencia, abrimos o WhatsApp com uma mensagem pronta para acelerar o atendimento.</p>

          <form id="contact-lead-form" action="enviar-contato.php" method="post" class="mt-5 space-y-4" data-whatsapp-phone="5511994566726" data-whatsapp-message-template="Ola, vim pelo site da Sistema Venda Direta. Meu nome e {nome} e meu WhatsApp e {whatsapp}. Quero um orcamento e uma demonstracao.">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($contactRedirectValue, ENT_QUOTES, 'UTF-8') ?>" />
            <input type="hidden" name="origem" value="home-svd" />
            <input type="hidden" name="servico" value="Sistema Venda Direta" />
            <input type="hidden" name="mensagem" value="Lead enviado pela home" />

            <div>
              <label for="contact-nome" class="mb-2 block text-sm font-medium text-white/90">Nome</label>
              <input id="contact-nome" name="nome" type="text" required autocomplete="name" placeholder="Seu nome" class="w-full rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-white/45 focus:border-white/60 focus:outline-none focus:ring-2 focus:ring-white/20" />
            </div>

            <div>
              <label for="contact-whatsapp" class="mb-2 block text-sm font-medium text-white/90">WhatsApp</label>
              <input id="contact-whatsapp" name="whatsapp" type="tel" required autocomplete="tel" inputmode="tel" placeholder="(11) 99999-9999" class="w-full rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-white/45 focus:border-white/60 focus:outline-none focus:ring-2 focus:ring-white/20" />
            </div>

            <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-white px-5 py-3 text-sm font-semibold uppercase tracking-wide text-brand transition hover:-translate-y-0.5 hover:bg-white/90">Quero falar com o comercial</button>
          </form>

          <a href="https://wa.me/5511994566726?text=Ol%C3%A1%2C%20quero%20um%20or%C3%A7amento%20e%20acesso%20%C3%A0%20demonstra%C3%A7%C3%A3o%20do%20Sistema%20Venda%20Direta." target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex w-full items-center justify-center rounded-full border border-white/25 px-4 py-2.5 text-sm font-medium text-white/90 transition hover:border-white/45 hover:bg-white/10">Ou falar direto no WhatsApp</a>

          <p class="mt-3 text-xs text-white/65">Usamos seus dados apenas para retorno comercial sobre o Sistema Venda Direta.</p>

          <?php if ($contactMailStatus === 'ok'): ?>
            <div class="mt-4 rounded-2xl border border-emerald-300/30 bg-emerald-500/10 p-4">
              <p class="text-sm font-semibold text-white">Lead enviado com sucesso.</p>
              <p class="mt-1 text-sm text-white/80">Se o WhatsApp nao abrir automaticamente, use o botao abaixo.</p>
              <a id="contact-success-whatsapp-link" href="https://wa.me/5511994566726" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex w-full items-center justify-center rounded-full border border-emerald-200/45 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10">Abrir WhatsApp agora</a>
            </div>
          <?php elseif ($contactMailStatus === 'erro'): ?>
            <div class="mt-4 rounded-2xl border border-rose-300/30 bg-rose-500/10 p-4">
              <p class="text-sm font-semibold text-white">Nao foi possivel concluir o envio.</p>
              <p class="mt-1 text-sm text-white/80">Tente novamente em instantes ou use o atalho direto para falar com nossa equipe.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>
