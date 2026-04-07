<section class="panel-card">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="eyebrow">Relacionamento</p>
            <h2 class="panel-title">Diretorio de WhatsApp</h2>
        </div>
        <form action="<?= route_url(['page' => 'whatsapp']) ?>" method="get" class="flex flex-col gap-3 sm:flex-row">
            <input type="hidden" name="page" value="whatsapp">
            <input type="text" name="q" value="<?= e($search) ?>" class="form-input min-w-[280px]" placeholder="Pesquisar por nome ou numero">
            <button type="submit" class="btn-secondary">Buscar</button>
        </form>
    </div>

    <div class="mt-6 overflow-x-auto">
        <table class="w-full min-w-[980px] text-left">
            <thead>
                <tr class="border-b border-white/10 text-xs uppercase tracking-[0.2em] text-slate-400">
                    <th class="pb-3">Cliente</th>
                    <th class="pb-3">Empresa</th>
                    <th class="pb-3">WhatsApp</th>
                    <th class="pb-3">Ultimo orcamento</th>
                    <th class="pb-3">Qtd. orcamentos</th>
                    <th class="pb-3 text-right">Acoes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-sm text-slate-200">
                <?php if ($contacts): ?>
                    <?php foreach ($contacts as $contact): ?>
                        <?php $waNumber = whatsapp_number($contact['phone']); ?>
                        <tr>
                            <td class="py-4 font-semibold"><?= e($contact['name']) ?></td>
                            <td class="py-4"><?= e($contact['company'] ?: '-') ?></td>
                            <td class="py-4"><?= e($contact['phone']) ?></td>
                            <td class="py-4 text-slate-400"><?= e(format_date($contact['last_quote_at'])) ?></td>
                            <td class="py-4"><?= (int) $contact['quotes_count'] ?></td>
                            <td class="py-4">
                                <div class="flex justify-end gap-2">
                                    <?php if ($waNumber !== ''): ?>
                                        <a href="https://wa.me/<?= e($waNumber) ?>" target="_blank" rel="noopener" class="btn-secondary">Abrir WhatsApp</a>
                                    <?php endif; ?>
                                    <a href="<?= route_url(['page' => 'clients', 'action' => 'history', 'id' => $contact['id']]) ?>" class="btn-secondary">Historico</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="py-10 text-center text-slate-400">Nenhum cliente com WhatsApp encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
