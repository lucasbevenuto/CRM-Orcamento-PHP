<section class="panel-card">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="eyebrow">Catalogo</p>
            <h2 class="panel-title">Produtos e Servicos</h2>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row">
            <form action="<?= route_url(['page' => 'products']) ?>" method="get" class="flex gap-3">
                <input type="hidden" name="page" value="products">
                <input type="text" name="q" value="<?= e($search) ?>" class="form-input min-w-[240px]" placeholder="Pesquisar por nome">
                <button type="submit" class="btn-secondary">Buscar</button>
            </form>
            <a href="<?= route_url(['page' => 'products', 'action' => 'create']) ?>" class="btn-primary">Novo Item</a>
        </div>
    </div>

    <div class="mt-6 overflow-x-auto">
        <table class="w-full min-w-[980px] text-left">
            <thead>
                <tr class="border-b border-white/10 text-xs uppercase tracking-[0.2em] text-slate-400">
                    <th class="pb-3">Nome</th>
                    <th class="pb-3">Descricao</th>
                    <th class="pb-3">Preco</th>
                    <th class="pb-3">Custo</th>
                    <th class="pb-3">Margem</th>
                    <th class="pb-3 text-right">Acoes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-sm text-slate-200">
                <?php if ($products): ?>
                    <?php foreach ($products as $product): ?>
                        <?php $margin = (float) $product['unit_price'] - (float) ($product['cost_price'] ?? 0); ?>
                        <tr>
                            <td class="py-4 font-semibold"><?= e($product['name']) ?></td>
                            <td class="py-4 text-slate-300"><?= e($product['description'] ?: '-') ?></td>
                            <td class="py-4"><?= e(format_money($product['unit_price'])) ?></td>
                            <td class="py-4"><?= e(format_money($product['cost_price'] ?? 0)) ?></td>
                            <td class="py-4 text-cyan-200"><?= e(format_money($margin)) ?></td>
                            <td class="py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="<?= route_url(['page' => 'products', 'action' => 'edit', 'id' => $product['id']]) ?>" class="btn-secondary">Editar</a>
                                    <form action="<?= route_url(['page' => 'products', 'action' => 'delete', 'id' => $product['id']]) ?>" method="post" onsubmit="return confirm('Deseja excluir este item?');">
                                        <button type="submit" class="btn-danger">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="py-10 text-center text-slate-400">Nenhum produto ou servico encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
