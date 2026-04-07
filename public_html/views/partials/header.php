<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | <?= e(app_config('app.name')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Manrope', 'sans-serif'],
                        display: ['Plus Jakarta Sans', 'sans-serif']
                    },
                    colors: {
                        brand: {
                            50: '#ecfeff',
                            100: '#cffafe',
                            500: '#06b6d4',
                            600: '#0891b2',
                            700: '#0e7490',
                            900: '#164e63'
                        }
                    },
                    boxShadow: {
                        glow: '0 25px 60px rgba(14, 116, 144, 0.22)'
                    }
                }
            }
        };
    </script>
    <link rel="stylesheet" href="<?= asset_url('assets/css/app.css') ?>">
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-100">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.15),_transparent_30%),radial-gradient(circle_at_bottom_right,_rgba(59,130,246,0.14),_transparent_28%),linear-gradient(180deg,_#020617,_#0f172a)]"></div>
    <div class="fixed inset-0 -z-10 opacity-30 [background-image:linear-gradient(rgba(148,163,184,0.08)_1px,transparent_1px),linear-gradient(90deg,rgba(148,163,184,0.08)_1px,transparent_1px)] [background-size:36px_36px]"></div>

    <div data-sidebar-overlay class="fixed inset-0 z-30 hidden bg-slate-950/70 backdrop-blur-sm lg:hidden"></div>
    <?php require __DIR__ . '/sidebar.php'; ?>

    <div class="lg:pl-72">
        <header class="fixed inset-x-0 top-0 z-30 border-b border-white/10 bg-slate-950/70 backdrop-blur-xl lg:left-72">
            <div class="flex items-center justify-between gap-4 px-4 py-4 lg:px-8">
                <div class="flex items-center gap-3">
                    <button type="button" data-sidebar-toggle class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white lg:hidden">
                        <span class="text-xl leading-none">&#9776;</span>
                    </button>
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">CRM de Orcamentos</p>
                        <h1 class="font-display text-xl font-bold text-white"><?= e($pageTitle) ?></h1>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="<?= route_url(['page' => 'settings']) ?>" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white transition hover:bg-white/10" title="Configuracoes">
                        <span class="text-xl leading-none">&#9881;</span>
                    </a>
                    <div class="hidden rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-right sm:block">
                        <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Usuario</p>
                        <p class="text-sm font-semibold text-white"><?= e(current_user()['name'] ?? '') ?></p>
                    </div>
                    <a href="<?= route_url(['page' => 'logout']) ?>" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-100 transition hover:bg-white/10">
                        Sair
                    </a>
                </div>
            </div>
        </header>

        <main class="px-4 pb-10 pt-28 lg:px-8">
            <?php if ($flash): ?>
                <?php
                $alertClasses = [
                    'success' => 'border-emerald-400/30 bg-emerald-500/10 text-emerald-100',
                    'error' => 'border-rose-400/30 bg-rose-500/10 text-rose-100',
                ];
                ?>
                <div class="mb-6 flex items-start justify-between gap-4 rounded-3xl border px-5 py-4 shadow-glow <?= $alertClasses[$flash['type']] ?? $alertClasses['success'] ?>">
                    <div>
                        <p class="text-xs uppercase tracking-[0.25em] text-white/60">Notificacao</p>
                        <p class="mt-1 text-sm font-medium"><?= e($flash['message']) ?></p>
                    </div>
                    <button type="button" data-dismiss class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white/80">Fechar</button>
                </div>
            <?php endif; ?>
