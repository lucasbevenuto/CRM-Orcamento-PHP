<?php
$menu = [
    'dashboard' => ['label' => 'Dashboard', 'url' => route_url(['page' => 'dashboard'])],
    'clients' => ['label' => 'Clientes', 'url' => route_url(['page' => 'clients'])],
    'products' => ['label' => 'Produtos e Servicos', 'url' => route_url(['page' => 'products'])],
    'quotes' => ['label' => 'Orcamentos', 'url' => route_url(['page' => 'quotes'])],
    'whatsapp' => ['label' => 'WhatsApp', 'url' => route_url(['page' => 'whatsapp'])],
    'settings' => ['label' => 'Configuracoes', 'url' => route_url(['page' => 'settings'])],
];
?>
<aside id="app-sidebar" class="sidebar-shell fixed inset-y-0 left-0 z-40 w-72 -translate-x-full transition-transform duration-300 lg:translate-x-0">
    <div class="flex h-full flex-col border-r border-white/10 bg-slate-950/90 px-5 pb-5 pt-6 backdrop-blur-xl">
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-cyan-400 via-sky-500 to-blue-600 text-lg font-bold text-white shadow-lg shadow-sky-900/30">
                <?php if (!empty(app_config('company.logo')) && is_file(public_path(app_config('company.logo')))): ?>
                    <img src="<?= asset_url(app_config('company.logo')) ?>" alt="Logo da empresa" class="h-full w-full object-cover">
                <?php else: ?>
                    CRM
                <?php endif; ?>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Painel</p>
                <h2 class="text-lg font-semibold text-white"><?= e(app_config('company.name')) ?></h2>
            </div>
        </div>

        <a href="<?= route_url(['page' => 'quotes', 'action' => 'create']) ?>" class="btn-primary mt-8 text-center">
            Novo Orcamento
        </a>

        <nav class="mt-8 space-y-2">
            <?php foreach ($menu as $key => $item): ?>
                <?php $active = $currentPage === $key; ?>
                <a href="<?= $item['url'] ?>" class="<?= $active ? 'menu-link-active' : 'menu-link' ?>">
                    <?= e($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="mt-auto rounded-3xl border border-white/10 bg-white/5 p-4">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Dados da empresa</p>
            <p class="mt-2 text-sm leading-6 text-slate-200"><?= e(app_config('company.phone')) ?></p>
            <p class="text-sm leading-6 text-slate-400"><?= e(app_config('company.email')) ?></p>
        </div>
    </div>
</aside>
