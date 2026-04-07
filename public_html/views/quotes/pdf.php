<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 12px; margin: 0; }
        .page { padding: 28px 32px; }
        .header { background: linear-gradient(135deg, #0f172a, #0f766e); color: #fff; padding: 24px; border-radius: 20px; }
        .header-table, .meta-table, .items-table { width: 100%; border-collapse: collapse; }
        .header-title { font-size: 24px; font-weight: bold; margin: 0 0 6px; }
        .muted { color: #cbd5e1; }
        .section { margin-top: 22px; }
        .card { border: 1px solid #e2e8f0; border-radius: 16px; padding: 16px; }
        .label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.18em; color: #64748b; }
        .value { margin-top: 6px; font-size: 13px; color: #0f172a; }
        .items-table thead th { background: #e2e8f0; color: #334155; text-transform: uppercase; font-size: 10px; letter-spacing: 0.14em; padding: 12px 10px; text-align: left; }
        .items-table tbody td { padding: 12px 10px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .totals { margin-top: 20px; text-align: right; }
        .totals .amount { font-size: 24px; font-weight: bold; color: #0f766e; }
        .footer { margin-top: 24px; font-size: 11px; color: #475569; }
        .status { display: inline-block; padding: 6px 12px; border-radius: 999px; font-size: 11px; font-weight: bold; background: #e0f2fe; color: #0369a1; }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td style="width: 80px; vertical-align: top;">
                        <?php if ($logoData): ?>
                            <img src="<?= $logoData ?>" alt="Logo" style="max-width: 64px; max-height: 64px;">
                        <?php endif; ?>
                    </td>
                    <td>
                        <p class="header-title"><?= e($company['name']) ?></p>
                        <p class="muted" style="margin: 0;"><?= e($company['document']) ?> | <?= e($company['phone']) ?> | <?= e($company['email']) ?></p>
                        <p class="muted" style="margin: 6px 0 0;"><?= e($company['address']) ?></p>
                    </td>
                    <td style="text-align: right; vertical-align: top;">
                        <p style="margin: 0; font-size: 10px; letter-spacing: 0.18em; text-transform: uppercase;">Orçamento</p>
                        <p style="margin: 8px 0 0; font-size: 20px; font-weight: bold;"><?= e(quote_reference($quote['id'])) ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <table class="meta-table">
                <tr>
                    <td style="width: 50%; padding-right: 8px; vertical-align: top;">
                        <div class="card">
                            <div class="label">Cliente</div>
                            <div class="value">
                                <strong><?= e($quote['client_name']) ?></strong><br>
                                <?= e($quote['client_company'] ?: '-') ?><br>
                                <?= e($quote['client_email'] ?: '-') ?><br>
                                <?= e($quote['client_phone']) ?>
                            </div>
                        </div>
                    </td>
                    <td style="width: 50%; padding-left: 8px; vertical-align: top;">
                        <div class="card">
                            <div class="label">Dados da proposta</div>
                            <div class="value">
                                Data: <?= e(date('d/m/Y', strtotime($quote['created_at']))) ?><br>
                                Status:
                                <span class="status"><?= e($statusMeta['label']) ?></span><br>
                                Total: <strong><?= e(format_money($quote['total_amount'])) ?></strong>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Descrição</th>
                        <th>Qtd.</th>
                        <th>Preço</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quote['items'] as $item): ?>
                        <tr>
                            <td><?= e($item['item_name']) ?></td>
                            <td><?= e($item['item_description'] ?: '-') ?></td>
                            <td><?= e((string) $item['quantity']) ?></td>
                            <td><?= e(format_money($item['unit_price'])) ?></td>
                            <td><?= e(format_money($item['total'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="totals">
            <div class="label">Valor final</div>
            <div class="amount"><?= e(format_money($quote['total_amount'])) ?></div>
        </div>

        <div class="section">
            <div class="card">
                <div class="label">Observações</div>
                <div class="value"><?= nl2br(e($quote['notes'] ?: 'Nenhuma observação adicional.')) ?></div>
            </div>
        </div>

        <div class="footer">
            Documento gerado automaticamente pelo <?= e(app_config('app.name')) ?> em <?= e(date('d/m/Y H:i')) ?>.
        </div>
    </div>
</body>
</html>
