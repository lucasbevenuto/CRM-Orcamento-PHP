<?php
$settingsData = [
    'name' => $old['name'] ?? $settings['name'] ?? '',
    'document' => $old['document'] ?? $settings['document'] ?? '',
    'email' => $old['email'] ?? $settings['email'] ?? '',
    'phone' => $old['phone'] ?? $settings['phone'] ?? '',
    'address' => $old['address'] ?? $settings['address'] ?? '',
    'logo' => $settings['logo'] ?? '',
];
?>
<section class="grid gap-6 xl:grid-cols-[1fr_0.8fr]">
    <article class="panel-card">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">Engrenagem</p>
                <h2 class="panel-title">Configuracoes da empresa</h2>
            </div>
            <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-300">PDF + painel</span>
        </div>

        <form action="<?= route_url(['page' => 'settings', 'action' => 'update']) ?>" method="post" enctype="multipart/form-data" class="mt-8 grid gap-5 md:grid-cols-2">
            <input type="hidden" name="current_logo" value="<?= e($settingsData['logo']) ?>">
            <div class="md:col-span-2">
                <label for="name" class="form-label">Nome da empresa</label>
                <input id="name" name="name" type="text" value="<?= e($settingsData['name']) ?>" class="form-input" required>
            </div>
            <div>
                <label for="document" class="form-label">CNPJ / Documento</label>
                <input id="document" name="document" type="text" value="<?= e($settingsData['document']) ?>" class="form-input">
            </div>
            <div>
                <label for="phone" class="form-label">Telefone / WhatsApp</label>
                <input id="phone" name="phone" type="text" value="<?= e($settingsData['phone']) ?>" class="form-input" required>
            </div>
            <div class="md:col-span-2">
                <label for="email" class="form-label">Email comercial</label>
                <input id="email" name="email" type="email" value="<?= e($settingsData['email']) ?>" class="form-input" required>
            </div>
            <div class="md:col-span-2">
                <label for="address" class="form-label">Endereco completo</label>
                <textarea id="address" name="address" rows="4" class="form-input" required><?= e($settingsData['address']) ?></textarea>
            </div>
            <div class="md:col-span-2">
                <label for="logo" class="form-label">Foto / logo da empresa</label>
                <input id="logo" name="logo" type="file" accept=".png,.jpg,.jpeg,.svg,.webp" class="form-input">
                <p class="mt-2 text-xs text-slate-400">Use PNG, JPG, SVG ou WEBP com ate 2 MB.</p>
            </div>
            <div class="md:col-span-2 flex flex-col gap-3 sm:flex-row">
                <button type="submit" class="btn-primary">Salvar configuracoes</button>
                <a href="<?= route_url(['page' => 'dashboard']) ?>" class="btn-secondary">Voltar ao dashboard</a>
            </div>
        </form>
    </article>

    <aside class="space-y-6">
        <article class="panel-card">
            <p class="eyebrow">Preview</p>
            <h2 class="panel-title">Como aparece</h2>

            <div class="mt-6 rounded-[2rem] border border-white/10 bg-white/5 p-6">
                <div class="flex items-center gap-4">
                    <div class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-3xl border border-white/10 bg-slate-950/50">
                        <?php if (!empty($settingsData['logo']) && is_file(public_path($settingsData['logo']))): ?>
                            <img src="<?= asset_url($settingsData['logo']) ?>" alt="Logo atual" class="h-full w-full object-cover">
                        <?php else: ?>
                            <span class="text-xs uppercase tracking-[0.2em] text-slate-500">Sem logo</span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-white"><?= e($settingsData['name']) ?></p>
                        <p class="mt-2 text-sm text-slate-300"><?= e($settingsData['document']) ?></p>
                        <p class="text-sm text-slate-300"><?= e($settingsData['phone']) ?> | <?= e($settingsData['email']) ?></p>
                        <p class="text-sm text-slate-400"><?= e($settingsData['address']) ?></p>
                    </div>
                </div>
            </div>
        </article>

        <article class="panel-card">
            <p class="eyebrow">Impacto</p>
            <h2 class="panel-title">Onde atualiza</h2>
            <div class="mt-6 space-y-3 text-sm leading-7 text-slate-300">
                <p>Os dados aparecem no PDF dos orcamentos, no menu lateral e nas acoes comerciais.</p>
                <p>A imagem enviada passa a ser usada como foto da empresa no painel e no PDF.</p>
            </div>
        </article>

        <article class="panel-card border border-rose-400/20 bg-rose-500/10">
            <p class="eyebrow text-rose-200">Zona de perigo</p>
            <h2 class="panel-title">Apagar todas as informacoes</h2>
            <div class="mt-5 space-y-3 text-sm leading-7 text-rose-100/90">
                <p>Essa acao remove todos os clientes, produtos, orcamentos, PDFs gerados e redefine os dados da empresa para o padrao.</p>
                <p>O usuario de login continua ativo para que voce possa entrar novamente no sistema depois da limpeza.</p>
            </div>

            <form action="<?= route_url(['page' => 'settings', 'action' => 'reset_data']) ?>" method="post" class="mt-6 space-y-4">
                <div>
                    <label for="confirmation_text" class="form-label text-rose-100">Digite APAGAR TUDO para confirmar</label>
                    <input
                        id="confirmation_text"
                        name="confirmation_text"
                        type="text"
                        class="form-input border-rose-400/30 bg-slate-950/70"
                        placeholder="APAGAR TUDO"
                        required
                    >
                </div>
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-2xl border border-rose-300/30 bg-rose-500/20 px-5 py-3 text-sm font-semibold text-rose-50 transition hover:bg-rose-500/30"
                    onclick="return confirm('Tem certeza que deseja apagar todas as informacoes cadastradas? Essa acao nao pode ser desfeita.');"
                >
                    Apagar tudo
                </button>
            </form>
        </article>
    </aside>
</section>
