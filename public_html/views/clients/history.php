<section class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
    <article class="panel-card">
        <p class="eyebrow">Cliente</p>
        <h2 class="panel-title"><?= e($client['name']) ?></h2>
        <dl class="mt-6 space-y-4 text-sm text-slate-300">
            <div>
                <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Empresa</dt>
                <dd class="mt-1"><?= e($client['company'] ?: '-') ?></dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">E-mail</dt>
                <dd class="mt-1"><?= e($client['email'] ?: '-') ?></dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Telefone</dt>
                <dd class="mt-1"><?= e($client['phone']) ?></dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Observações</dt>
                <dd class="mt-1 leading-7"><?= nl2br(e($client['notes'] ?: 'Sem observações.')) ?></dd>
            </div>
        </dl>
    </article>

    <article class="panel-card">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">Histórico</p>
                <h2 class="panel-title">Orçamentos do cliente</h2>
            </div>
            <a href="<?= route_url(['page' => 'clients']) ?>" class="btn-secondary">Voltar</a>
        </div>

        <div class="mt-6 space-y-4">
            <?php if ($quotes): ?>
                <?php foreach ($quotes as $quote): ?>
                    <div class="quick-card">
                        <strong><?= e(quote_reference($quote['id'])) ?> - <?= e(format_money($quote['total_amount'])) ?></strong>
                        <span><?= e(quote_status_meta($quote['status'])['label']) ?> | <?= e(format_date($quote['created_at'])) ?> | <?= (int) $quote['items_count'] ?> item(ns)</span>
                        <a href="<?= route_url(['page' => 'quotes', 'action' => 'show', 'id' => $quote['id']]) ?>" class="mt-3 inline-flex text-sm font-semibold text-cyan-200">Abrir orçamento</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="rounded-3xl border border-dashed border-white/10 px-5 py-8 text-center text-slate-400">
                    Este cliente ainda não possui orçamentos.
                </div>
            <?php endif; ?>
        </div>
    </article>
</section>
