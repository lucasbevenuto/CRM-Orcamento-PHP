<?php
$clientData = [
    'name' => $old['name'] ?? $client['name'] ?? '',
    'email' => $old['email'] ?? $client['email'] ?? '',
    'phone' => $old['phone'] ?? $client['phone'] ?? '',
    'company' => $old['company'] ?? $client['company'] ?? '',
    'notes' => $old['notes'] ?? $client['notes'] ?? '',
];
?>
<section class="panel-card max-w-4xl">
    <div>
        <p class="eyebrow">Cadastro</p>
        <h2 class="panel-title"><?= e($pageTitle) ?></h2>
    </div>

    <form action="<?= $formAction ?>" method="post" class="mt-8 grid gap-5 md:grid-cols-2">
        <div>
            <label for="name" class="form-label">Nome</label>
            <input id="name" name="name" type="text" value="<?= e($clientData['name']) ?>" class="form-input" required>
        </div>
        <div>
            <label for="phone" class="form-label">Telefone</label>
            <input id="phone" name="phone" type="text" value="<?= e($clientData['phone']) ?>" class="form-input" required>
        </div>
        <div>
            <label for="email" class="form-label">E-mail</label>
            <input id="email" name="email" type="email" value="<?= e($clientData['email']) ?>" class="form-input">
        </div>
        <div>
            <label for="company" class="form-label">Empresa</label>
            <input id="company" name="company" type="text" value="<?= e($clientData['company']) ?>" class="form-input">
        </div>
        <div class="md:col-span-2">
            <label for="notes" class="form-label">Observações</label>
            <textarea id="notes" name="notes" rows="5" class="form-input"><?= e($clientData['notes']) ?></textarea>
        </div>
        <div class="md:col-span-2 flex flex-col gap-3 sm:flex-row">
            <button type="submit" class="btn-primary">Salvar Cliente</button>
            <a href="<?= route_url(['page' => 'clients']) ?>" class="btn-secondary">Voltar</a>
        </div>
    </form>
</section>
