<section class="panel-card">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="eyebrow">Cadastro</p>
            <h2 class="panel-title">Clientes</h2>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row">
            <form action="<?= route_url(['page' => 'clients']) ?>" method="get" class="flex gap-3">
                <input type="hidden" name="page" value="clients">
                <input type="text" name="q" value="<?= e($search) ?>" class="form-input min-w-[240px]" placeholder="Pesquisar por nome">
                <button type="submit" class="btn-secondary">Buscar</button>
            </form>
            <a href="<?= route_url(['page' => 'clients', 'action' => 'create']) ?>" class="btn-primary">Novo Cliente</a>
        </div>
    </div>

    <div class="mt-6 overflow-x-auto">
        <table class="w-full min-w-[900px] text-left">
            <thead>
                <tr class="border-b border-white/10 text-xs uppercase tracking-[0.2em] text-slate-400">
                    <th class="pb-3">Nome</th>
                    <th class="pb-3">Empresa</th>
                    <th class="pb-3">E-mail</th>
                    <th class="pb-3">Telefone</th>
                    <th class="pb-3">Orçamentos</th>
                    <th class="pb-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-sm text-slate-200">
                <?php if ($clients): ?>
                    <?php foreach ($clients as $client): ?>
                        <tr>
                            <td class="py-4 font-semibold"><?= e($client['name']) ?></td>
                            <td class="py-4"><?= e($client['company'] ?: '-') ?></td>
                            <td class="py-4"><?= e($client['email'] ?: '-') ?></td>
                            <td class="py-4"><?= e($client['phone']) ?></td>
                            <td class="py-4"><?= (int) $client['quote_count'] ?></td>
                            <td class="py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="<?= route_url(['page' => 'clients', 'action' => 'history', 'id' => $client['id']]) ?>" class="btn-secondary">Histórico</a>
                                    <a href="<?= route_url(['page' => 'clients', 'action' => 'edit', 'id' => $client['id']]) ?>" class="btn-secondary">Editar</a>
                                    <form action="<?= route_url(['page' => 'clients', 'action' => 'delete', 'id' => $client['id']]) ?>" method="post" onsubmit="return confirm('Deseja excluir este cliente?');">
                                        <button type="submit" class="btn-danger">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="py-10 text-center text-slate-400">Nenhum cliente encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
