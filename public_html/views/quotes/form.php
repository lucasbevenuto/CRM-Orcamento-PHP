<?php
$selectedClient = $old['client_id'] ?? '';
$notesValue = $old['notes'] ?? '';
?>
<section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
    <article class="panel-card">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">Criação</p>
                <h2 class="panel-title">Novo Orçamento</h2>
            </div>
            <a href="<?= route_url(['page' => 'quotes']) ?>" class="btn-secondary">Voltar</a>
        </div>

        <?php if (!$clients || !$products): ?>
            <div class="mt-8 rounded-3xl border border-amber-300/20 bg-amber-400/10 p-6 text-amber-50">
                <p class="text-base font-semibold">Cadastre pelo menos um cliente e um produto antes de criar um orçamento.</p>
                <div class="mt-4 flex flex-wrap gap-3">
                    <a href="<?= route_url(['page' => 'clients', 'action' => 'create']) ?>" class="btn-secondary">Cadastrar cliente</a>
                    <a href="<?= route_url(['page' => 'products', 'action' => 'create']) ?>" class="btn-secondary">Cadastrar produto</a>
                </div>
            </div>
        <?php else: ?>
            <form action="<?= route_url(['page' => 'quotes', 'action' => 'store']) ?>" method="post" data-quote-form class="mt-8 space-y-8">
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="client_id" class="form-label">Cliente</label>
                        <select id="client_id" name="client_id" class="form-input" required>
                            <option value="">Selecione um cliente</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?= (int) $client['id'] ?>" <?= (string) $selectedClient === (string) $client['id'] ? 'selected' : '' ?>>
                                    <?= e($client['name']) ?><?= $client['company'] ? ' - ' . e($client['company']) : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div>
                            <p class="form-label !mb-1">Itens do orçamento</p>
                            <p class="text-sm text-slate-400">Adicione múltiplos produtos ou serviços e ajuste quantidade e preço se necessário.</p>
                        </div>
                        <button type="button" data-add-quote-item class="btn-secondary">Adicionar item</button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[900px] text-left">
                            <thead>
                                <tr class="border-b border-white/10 text-xs uppercase tracking-[0.2em] text-slate-400">
                                    <th class="pb-3">Produto/Serviço</th>
                                    <th class="pb-3">Descrição</th>
                                    <th class="pb-3">Preço</th>
                                    <th class="pb-3">Qtd.</th>
                                    <th class="pb-3">Total</th>
                                    <th class="pb-3"></th>
                                </tr>
                            </thead>
                            <tbody id="quote-items-body" class="divide-y divide-white/5"></tbody>
                        </table>
                    </div>
                </div>

                <div>
                    <label for="notes" class="form-label">Observações adicionais</label>
                    <textarea id="notes" name="notes" rows="5" class="form-input" placeholder="Prazo, condições, detalhes comerciais..."><?= e($notesValue) ?></textarea>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <button type="submit" class="btn-primary">Salvar Orçamento</button>
                    <a href="<?= route_url(['page' => 'quotes']) ?>" class="btn-secondary">Cancelar</a>
                </div>
            </form>
        <?php endif; ?>
    </article>

    <aside class="panel-card">
        <p class="eyebrow">Resumo</p>
        <h2 class="panel-title">Total automático</h2>
        <div class="mt-8 rounded-[2rem] border border-cyan-400/20 bg-cyan-400/10 p-6">
            <p class="text-xs uppercase tracking-[0.35em] text-cyan-100/80">Valor total</p>
            <p id="quote-grand-total" class="mt-3 font-display text-4xl font-bold text-white">R$ 0,00</p>
        </div>
        <div class="mt-6 space-y-4 text-sm leading-7 text-slate-300">
            <p>O status inicial do orçamento é salvo como <strong>Enviado</strong>.</p>
            <p>Depois de salvar, você poderá gerar o PDF e abrir o link oficial do WhatsApp com a mensagem pronta.</p>
        </div>
    </aside>
</section>

<script id="quote-products-data" type="application/json"><?= $productsJson ?></script>
<template id="quote-item-template">
    <tr class="quote-item-row align-top">
        <td class="py-4 pr-4">
            <select name="items[__INDEX__][product_id]" class="form-input quote-product-select" required>
                <option value="">Selecione</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= (int) $product['id'] ?>"><?= e($product['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="py-4 pr-4">
            <textarea name="items[__INDEX__][item_description]" rows="3" class="form-input quote-description-input" placeholder="Descrição do item"></textarea>
        </td>
        <td class="py-4 pr-4">
            <input name="items[__INDEX__][unit_price]" type="number" step="0.01" min="0.01" class="form-input quote-unit-price" required>
        </td>
        <td class="py-4 pr-4">
            <input name="items[__INDEX__][quantity]" type="number" step="0.01" min="0.01" value="1" class="form-input quote-quantity" required>
        </td>
        <td class="py-4 pr-4">
            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold text-white quote-line-total">R$ 0,00</div>
        </td>
        <td class="py-4">
            <button type="button" class="btn-danger w-full quote-remove-item">Remover</button>
        </td>
    </tr>
</template>
