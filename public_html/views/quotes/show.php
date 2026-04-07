<section class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
    <article class="panel-card">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="eyebrow">Orçamento</p>
                <h2 class="panel-title"><?= e(quote_reference($quote['id'])) ?></h2>
                <div class="mt-3"><?= quote_status_badge($quote['status']) ?></div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="<?= route_url(['page' => 'quotes', 'action' => 'pdf', 'id' => $quote['id']]) ?>" class="btn-primary">Gerar PDF</a>
                <?php if ($pdfUrl): ?>
                    <a href="<?= $pdfUrl ?>" target="_blank" rel="noopener" class="btn-secondary">Abrir PDF</a>
                <?php endif; ?>
                <?php if ($whatsAppUrl): ?>
                    <a href="<?= $whatsAppUrl ?>" target="_blank" rel="noopener" class="btn-secondary">Enviar por WhatsApp</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-8 grid gap-5 md:grid-cols-2">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Cliente</p>
                <h3 class="mt-3 text-lg font-semibold text-white"><?= e($quote['client_name']) ?></h3>
                <div class="mt-4 space-y-2 text-sm text-slate-300">
                    <p>E-mail: <?= e($quote['client_email'] ?: '-') ?></p>
                    <p>Telefone: <?= e($quote['client_phone']) ?></p>
                    <p>Empresa: <?= e($quote['client_company'] ?: '-') ?></p>
                </div>
            </div>
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Detalhes</p>
                <div class="mt-4 space-y-2 text-sm text-slate-300">
                    <p>Criado em: <?= e(format_date($quote['created_at'])) ?></p>
                    <p>Total: <strong class="text-white"><?= e(format_money($quote['total_amount'])) ?></strong></p>
                    <p>PDF público: <?= $pdfUrl ? 'Disponível' : 'Ainda não gerado' ?></p>
                </div>
            </div>
        </div>

        <div class="mt-8 overflow-x-auto">
            <table class="w-full min-w-[760px] text-left">
                <thead>
                    <tr class="border-b border-white/10 text-xs uppercase tracking-[0.2em] text-slate-400">
                        <th class="pb-3">Item</th>
                        <th class="pb-3">Descrição</th>
                        <th class="pb-3">Qtd.</th>
                        <th class="pb-3">Preço</th>
                        <th class="pb-3">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-sm text-slate-200">
                    <?php foreach ($quote['items'] as $item): ?>
                        <tr>
                            <td class="py-4 font-semibold"><?= e($item['item_name']) ?></td>
                            <td class="py-4 text-slate-300"><?= e($item['item_description'] ?: '-') ?></td>
                            <td class="py-4"><?= e((string) $item['quantity']) ?></td>
                            <td class="py-4"><?= e(format_money($item['unit_price'])) ?></td>
                            <td class="py-4 font-semibold"><?= e(format_money($item['total'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-8 rounded-3xl border border-white/10 bg-white/5 p-5">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Observações adicionais</p>
            <p class="mt-3 text-sm leading-7 text-slate-300"><?= nl2br(e($quote['notes'] ?: 'Nenhuma observação informada.')) ?></p>
        </div>
    </article>

    <aside class="space-y-6">
        <article class="panel-card">
            <p class="eyebrow">Status</p>
            <h2 class="panel-title">Atualizar etapa</h2>
            <form action="<?= route_url(['page' => 'quotes', 'action' => 'status', 'id' => $quote['id']]) ?>" method="post" class="mt-6 space-y-4">
                <div>
                    <label for="status" class="form-label">Novo status</label>
                    <select id="status" name="status" class="form-input">
                        <?php foreach ($statusOptions as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= $quote['status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn-primary w-full justify-center">Salvar status</button>
            </form>
        </article>

        <article class="panel-card">
            <p class="eyebrow">Compartilhamento</p>
            <h2 class="panel-title">PDF e WhatsApp</h2>
            <div class="mt-6 space-y-4 text-sm leading-7 text-slate-300">
                <p>O PDF será salvo automaticamente como <strong>orcamento-<?= (int) $quote['id'] ?>.pdf</strong> na pasta pública do projeto.</p>
                <p>Depois da geração, o sistema habilita o link público e o botão do WhatsApp com a mensagem pronta.</p>
                <?php if ($pdfUrl): ?>
                    <div class="rounded-3xl border border-cyan-400/20 bg-cyan-400/10 p-4">
                        <p class="text-xs uppercase tracking-[0.25em] text-cyan-100/80">Link público</p>
                        <a href="<?= $pdfUrl ?>" target="_blank" rel="noopener" class="mt-2 block break-all text-sm font-semibold text-cyan-50"><?= e($pdfUrl) ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </article>
    </aside>
</section>
