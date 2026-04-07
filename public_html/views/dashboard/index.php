<section class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
    <article class="stat-card">
        <p class="stat-label">Orcamentos criados</p>
        <h2 class="stat-value"><?= (int) $summary['total_quotes'] ?></h2>
        <p class="stat-copy"><?= (int) $clientCount ?> clientes e <?= (int) $productCount ?> itens no catalogo.</p>
    </article>
    <article class="stat-card">
        <p class="stat-label">Vendas aprovadas</p>
        <h2 class="stat-value text-emerald-300"><?= e(format_money($summary['approved_revenue'])) ?></h2>
        <p class="stat-copy"><?= (int) $summary['approved_quotes'] ?> propostas aprovadas.</p>
    </article>
    <article class="stat-card">
        <p class="stat-label">Lucro estimado</p>
        <h2 class="stat-value text-cyan-300"><?= e(format_money($summary['approved_profit'])) ?></h2>
        <p class="stat-copy">Calculado a partir do preco de venda menos custo dos itens aprovados.</p>
    </article>
    <article class="stat-card">
        <p class="stat-label">Conversao</p>
        <h2 class="stat-value text-amber-300"><?= e(number_format((float) $summary['conversion_rate'], 1, ',', '.')) ?>%</h2>
        <p class="stat-copy">Ticket medio: <?= e(format_money($summary['average_ticket'])) ?></p>
    </article>
</section>

<section class="mt-8 grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">
    <article class="panel-card">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="eyebrow">Controle de vendas</p>
                <h2 class="panel-title">Faturamento, lucro e volume</h2>
            </div>
            <a href="<?= route_url(['page' => 'quotes', 'action' => 'create']) ?>" class="btn-primary">Novo Orcamento</a>
        </div>

        <div class="mt-8 overflow-hidden rounded-[2rem] border border-white/10 bg-slate-950/50 p-5">
            <div class="flex flex-wrap items-center gap-4 text-xs uppercase tracking-[0.2em] text-slate-400">
                <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-cyan-400"></span>Receita aprovada</span>
                <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-emerald-400"></span>Lucro estimado</span>
                <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-amber-400"></span>Qtd. de orcamentos</span>
            </div>
            <div id="sales-chart" class="mt-6 h-[340px]" data-chart="sales" data-payload='<?= dashboard_json([
                'labels' => $monthlyData['labels'],
                'revenue' => $monthlyData['revenue'],
                'profit' => $monthlyData['profit'],
                'quotes' => $monthlyData['quotes'],
            ]) ?>'></div>
        </div>
    </article>

    <article class="panel-card">
        <p class="eyebrow">Resumo comercial</p>
        <h2 class="panel-title">Saude da operacao</h2>
        <div class="mt-6 space-y-4">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Pipeline enviado</p>
                <p class="mt-2 text-2xl font-bold text-white"><?= e(format_money($summary['pipeline_revenue'])) ?></p>
                <p class="mt-2 text-sm text-slate-400"><?= (int) $summary['sent_quotes'] ?> propostas aguardando retorno.</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Clientes ativos</p>
                <p class="mt-2 text-2xl font-bold text-white"><?= (int) $summary['active_clients'] ?></p>
                <p class="mt-2 text-sm text-slate-400">Clientes que ja receberam pelo menos um orcamento.</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Propostas recusadas</p>
                <p class="mt-2 text-2xl font-bold text-white"><?= (int) $summary['rejected_quotes'] ?></p>
                <p class="mt-2 text-sm text-slate-400">Use essa metrica para revisar preco, prazo e abordagem.</p>
            </div>
        </div>
    </article>
</section>

<section class="mt-8 grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
    <article class="panel-card">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="eyebrow">Base comercial</p>
                <h2 class="panel-title">Novos clientes por mes</h2>
            </div>
            <a href="<?= route_url(['page' => 'whatsapp']) ?>" class="btn-secondary">Ver WhatsApps</a>
        </div>

        <div id="clients-chart" class="mt-6 h-[300px]" data-chart="bars" data-payload='<?= dashboard_json([
            'labels' => $monthlyData['labels'],
            'values' => $clientGrowth,
            'seriesLabel' => 'Clientes cadastrados',
            'color' => '#38bdf8',
        ]) ?>'></div>
    </article>

    <article class="panel-card">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">Melhores clientes</p>
                <h2 class="panel-title">Receita aprovada por cliente</h2>
            </div>
            <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-300">Top 5</span>
        </div>

        <div class="mt-6 space-y-4">
            <?php foreach ($topClients as $client): ?>
                <?php
                $revenue = (float) $client['approved_revenue'];
                $maxRevenue = (float) ($topClients[0]['approved_revenue'] ?? 0);
                $width = $maxRevenue > 0 ? max(8, (int) round(($revenue / $maxRevenue) * 100)) : 8;
                ?>
                <div class="rounded-3xl border border-white/10 bg-white/5 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-white"><?= e($client['client_name']) ?></p>
                            <p class="text-xs text-slate-400"><?= (int) $client['quotes_count'] ?> orcamento(s)</p>
                        </div>
                        <p class="text-sm font-semibold text-cyan-200"><?= e(format_money($revenue)) ?></p>
                    </div>
                    <div class="mt-3 h-2 rounded-full bg-white/5">
                        <div class="h-2 rounded-full bg-gradient-to-r from-cyan-400 to-blue-500" style="width: <?= $width ?>%"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </article>
</section>

<section class="mt-8 grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
    <article class="panel-card">
        <p class="eyebrow">Distribuicao</p>
        <h2 class="panel-title">Status das propostas</h2>
        <div id="status-chart" class="mt-6 h-[280px]" data-chart="donut" data-payload='<?= dashboard_json([
            'labels' => array_keys($statusBreakdown),
            'values' => array_values($statusBreakdown),
            'colors' => ['#38bdf8', '#34d399', '#fb7185'],
        ]) ?>'></div>
    </article>

    <article class="panel-card">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">Resumo rapido</p>
                <h2 class="panel-title">Ultimos orcamentos</h2>
            </div>
            <a href="<?= route_url(['page' => 'quotes']) ?>" class="btn-secondary">Ver todos</a>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="w-full min-w-[640px] text-left">
                <thead>
                    <tr class="border-b border-white/10 text-xs uppercase tracking-[0.2em] text-slate-400">
                        <th class="pb-3">Ref.</th>
                        <th class="pb-3">Cliente</th>
                        <th class="pb-3">Total</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3">Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-sm text-slate-200">
                    <?php if ($recentQuotes): ?>
                        <?php foreach ($recentQuotes as $quote): ?>
                            <tr>
                                <td class="py-4 font-semibold"><?= e(quote_reference($quote['id'])) ?></td>
                                <td class="py-4"><?= e($quote['client_name']) ?></td>
                                <td class="py-4"><?= e(format_money($quote['total_amount'])) ?></td>
                                <td class="py-4"><?= quote_status_badge($quote['status']) ?></td>
                                <td class="py-4 text-slate-400"><?= e(format_date($quote['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">Nenhum orcamento cadastrado ainda.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </article>
</section>
