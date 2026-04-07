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
                    }
                }
            }
        };
    </script>
    <link rel="stylesheet" href="<?= asset_url('assets/css/app.css') ?>">
</head>
<body class="min-h-screen overflow-x-hidden bg-slate-950 font-sans text-white">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_top,_rgba(34,211,238,0.22),_transparent_30%),radial-gradient(circle_at_bottom_left,_rgba(14,165,233,0.18),_transparent_30%),linear-gradient(160deg,_#020617,_#111827_55%,_#0f172a)]"></div>
    <main class="mx-auto flex min-h-screen max-w-6xl items-center px-5 py-10">
        <div class="grid w-full gap-8 lg:grid-cols-[1.1fr_0.9fr]">
            <section class="rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-2xl shadow-cyan-950/25 backdrop-blur-xl lg:p-12">
                <p class="text-xs uppercase tracking-[0.45em] text-cyan-200/80">CRM Hostinger Ready</p>
                <h1 class="mt-5 max-w-xl font-display text-4xl font-bold leading-tight text-white lg:text-5xl">
                    Controle clientes, produtos e propostas em um painel só.
                </h1>
                <p class="mt-6 max-w-2xl text-base leading-8 text-slate-300">
                    Sistema em PHP puro com MySQL, geração de PDF via dompdf e botão de compartilhamento no WhatsApp com link público do orçamento.
                </p>

                <div class="mt-10 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-5">
                        <p class="text-sm font-semibold text-white">Clientes</p>
                        <p class="mt-2 text-sm text-slate-400">Cadastro rápido com histórico de propostas.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-5">
                        <p class="text-sm font-semibold text-white">Produtos</p>
                        <p class="mt-2 text-sm text-slate-400">Tabela de preço pronta para múltiplos itens.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-5">
                        <p class="text-sm font-semibold text-white">PDF + WhatsApp</p>
                        <p class="mt-2 text-sm text-slate-400">Geração do arquivo e link em um clique.</p>
                    </div>
                </div>
            </section>

            <section class="rounded-[2rem] border border-white/10 bg-slate-950/70 p-8 shadow-2xl shadow-black/30 backdrop-blur-xl lg:p-10">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Acesso</p>
                <h2 class="mt-3 font-display text-3xl font-bold text-white">Entrar no painel</h2>
                <p class="mt-3 text-sm leading-7 text-slate-400">Use o usuário padrão importado pelo SQL ou edite a tabela `users` conforme preferir.</p>

                <?php if ($flash): ?>
                    <?php
                    $alertClasses = [
                        'success' => 'border-emerald-400/30 bg-emerald-500/10 text-emerald-100',
                        'error' => 'border-rose-400/30 bg-rose-500/10 text-rose-100',
                    ];
                    ?>
                    <div class="mt-6 rounded-3xl border px-5 py-4 <?= $alertClasses[$flash['type']] ?? $alertClasses['success'] ?>">
                        <p class="text-sm font-medium"><?= e($flash['message']) ?></p>
                    </div>
                <?php endif; ?>

                <form action="<?= route_url(['page' => 'login', 'action' => 'authenticate']) ?>" method="post" class="mt-8 space-y-5">
                    <div>
                        <label for="username" class="mb-2 block text-sm font-semibold text-slate-200">Usuário</label>
                        <input id="username" name="username" type="text" value="<?= e($old['username'] ?? '') ?>" class="form-input" placeholder="Digite seu usuário" required>
                    </div>
                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-slate-200">Senha</label>
                        <input id="password" name="password" type="password" class="form-input" placeholder="Digite sua senha" required>
                    </div>
                    <button type="submit" class="btn-primary w-full justify-center text-center">Entrar</button>
                </form>

                <div class="mt-8 rounded-3xl border border-cyan-400/20 bg-cyan-400/10 p-5">
                    <p class="text-xs uppercase tracking-[0.3em] text-cyan-100/80">Acesso inicial</p>
                    <p class="mt-2 text-sm text-cyan-50">Usuário padrão: <strong>admin</strong> | Senha padrão: <strong>admin123</strong></p>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
