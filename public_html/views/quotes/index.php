<section class="panel-card">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="eyebrow">Propostas</p>
            <h2 class="panel-title">Orçamentos</h2>
        </div>
        <a href="<?= route_url(['page' => 'quotes', 'action' => 'create']) ?>" class="btn-primary">Novo Orçamento</a>
    </div>

    <div class="mt-6 overflow-x-auto">
        <table class="w-full min-w-[920px] text-left">
            <thead>
                <tr class="border-b border-white/10 text-xs uppercase tracking-[0.2em] text-slate-400">
                    <th class="pb-3">Ref.</th>
                    <th class="pb-3">Cliente</th>
                    <th class="pb-3">Total</th>
                    <th class="pb-3">Status</th>
                    <th class="pb-3">PDF</th>
                    <th class="pb-3">Criado em</th>
                    <th class="pb-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-sm text-slate-200">
                <?php if ($quotes): ?>
                    <?php foreach ($quotes as $quote): ?>
                        <tr>
                            <td class="py-4 font-semibold"><?= e(quote_reference($quote['id'])) ?></td>
                            <td class="py-4"><?= e($quote['client_name']) ?></td>
                            <td class="py-4"><?= e(format_money($quote['total_amount'])) ?></td>
                            <td class="py-4"><?= quote_status_badge($quote['status']) ?></td>
                            <td class="py-4"><?= !empty($quote['pdf_path']) ? 'Gerado' : 'Pendente' ?></td>
                            <td class="py-4 text-slate-400"><?= e(format_date($quote['created_at'])) ?></td>
                            <td class="py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="<?= route_url(['page' => 'quotes', 'action' => 'show', 'id' => $quote['id']]) ?>" class="btn-secondary">Abrir</a>
                                    <a href="<?= route_url(['page' => 'quotes', 'action' => 'pdf', 'id' => $quote['id']]) ?>" class="btn-secondary">Gerar PDF</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="py-10 text-center text-slate-400">Nenhum orçamento criado ainda.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
