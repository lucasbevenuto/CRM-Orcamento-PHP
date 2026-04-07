<?php
$productData = [
    'name' => $old['name'] ?? $product['name'] ?? '',
    'description' => $old['description'] ?? $product['description'] ?? '',
    'unit_price' => $old['unit_price'] ?? $product['unit_price'] ?? '',
    'cost_price' => $old['cost_price'] ?? $product['cost_price'] ?? '',
];
?>
<section class="panel-card max-w-4xl">
    <div>
        <p class="eyebrow">Catalogo</p>
        <h2 class="panel-title"><?= e($pageTitle) ?></h2>
    </div>

    <form action="<?= $formAction ?>" method="post" class="mt-8 grid gap-5 md:grid-cols-2">
        <div class="md:col-span-2">
            <label for="name" class="form-label">Nome</label>
            <input id="name" name="name" type="text" value="<?= e($productData['name']) ?>" class="form-input" required>
        </div>
        <div class="md:col-span-2">
            <label for="description" class="form-label">Descricao</label>
            <textarea id="description" name="description" rows="5" class="form-input"><?= e($productData['description']) ?></textarea>
        </div>
        <div>
            <label for="unit_price" class="form-label">Preco de venda</label>
            <input id="unit_price" name="unit_price" type="number" step="0.01" min="0.01" value="<?= e((string) $productData['unit_price']) ?>" class="form-input" required>
        </div>
        <div>
            <label for="cost_price" class="form-label">Custo interno</label>
            <input id="cost_price" name="cost_price" type="number" step="0.01" min="0" value="<?= e((string) $productData['cost_price']) ?>" class="form-input">
        </div>
        <div class="md:col-span-2 flex flex-col gap-3 sm:flex-row">
            <button type="submit" class="btn-primary">Salvar Item</button>
            <a href="<?= route_url(['page' => 'products']) ?>" class="btn-secondary">Voltar</a>
        </div>
    </form>
</section>
