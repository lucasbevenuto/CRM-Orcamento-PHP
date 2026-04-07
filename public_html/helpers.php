<?php

spl_autoload_register(function (string $class): void {
    $directories = [
        __DIR__ . '/controllers/' . $class . '.php',
        __DIR__ . '/models/' . $class . '.php',
    ];

    foreach ($directories as $file) {
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

function app_config(?string $key = null, $default = null)
{
    global $config;

    if ($key === null) {
        return $config;
    }

    $segments = explode('.', $key);
    $value = $config;

    foreach ($segments as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }

        $value = $value[$segment];
    }

    return $value;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function request_method(): string
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
}

function is_post(): bool
{
    return request_method() === 'POST';
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user']);
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function get_flash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function redirect_back(string $fallback): void
{
    $target = $_SERVER['HTTP_REFERER'] ?? $fallback;
    redirect($target);
}

function detect_base_url(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = trim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    $base = $scheme . '://' . $host;

    if ($scriptDir !== '' && $scriptDir !== '.') {
        $base .= '/' . $scriptDir;
    }

    return rtrim($base, '/');
}

function app_url(string $path = ''): string
{
    $baseUrl = rtrim(app_config('app.base_url') ?: detect_base_url(), '/');
    $path = ltrim($path, '/');

    if ($path === '') {
        return $baseUrl;
    }

    return $baseUrl . '/' . $path;
}

function asset_url(string $path): string
{
    return app_url($path);
}

function public_path(string $path = ''): string
{
    $base = __DIR__;
    $path = ltrim(str_replace('\\', '/', $path), '/');

    if ($path === '') {
        return $base;
    }

    return $base . '/' . $path;
}

function route_url(array $params = []): string
{
    $base = app_url('index.php');

    if ($params === []) {
        return $base;
    }

    return $base . '?' . http_build_query($params);
}

function format_money($value): string
{
    return 'R$ ' . number_format((float) $value, 2, ',', '.');
}

function format_date(?string $date): string
{
    if (empty($date)) {
        return '-';
    }

    return date('d/m/Y H:i', strtotime($date));
}

function quote_reference($quoteId): string
{
    return '#' . str_pad((string) $quoteId, 5, '0', STR_PAD_LEFT);
}

function quote_status_meta(string $status): array
{
    $map = [
        'enviado' => [
            'label' => 'Enviado',
            'badge' => 'bg-sky-100 text-sky-700 ring-sky-200',
            'dot' => 'bg-sky-500',
        ],
        'aprovado' => [
            'label' => 'Aprovado',
            'badge' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
            'dot' => 'bg-emerald-500',
        ],
        'recusado' => [
            'label' => 'Recusado',
            'badge' => 'bg-rose-100 text-rose-700 ring-rose-200',
            'dot' => 'bg-rose-500',
        ],
    ];

    return $map[$status] ?? $map['enviado'];
}

function quote_status_badge(string $status): string
{
    $meta = quote_status_meta($status);

    return sprintf(
        '<span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset %s"><span class="h-2.5 w-2.5 rounded-full %s"></span>%s</span>',
        $meta['badge'],
        $meta['dot'],
        e($meta['label'])
    );
}

function quote_pdf_url(array $quote): ?string
{
    if (empty($quote['pdf_path'])) {
        return null;
    }

    return app_url($quote['pdf_path']);
}

function whatsapp_number(?string $phone): string
{
    $digits = preg_replace('/\D+/', '', (string) $phone);

    if ($digits === '') {
        return '';
    }

    if (strlen($digits) === 10 || strlen($digits) === 11) {
        return '55' . $digits;
    }

    return $digits;
}

function quote_whatsapp_url(array $quote): ?string
{
    $number = whatsapp_number($quote['client_phone'] ?? '');
    $pdfUrl = quote_pdf_url($quote);

    if ($number === '' || empty($pdfUrl)) {
        return null;
    }

    $message = sprintf(
        'Ola %s! Segue o orçamento %s da %s: %s',
        $quote['client_name'],
        quote_reference($quote['id']),
        app_config('company.name'),
        $pdfUrl
    );

    return 'https://wa.me/' . $number . '?text=' . rawurlencode($message);
}

function require_auth(): void
{
    if (!is_logged_in()) {
        flash('error', 'Faça login para continuar.');
        redirect(route_url(['page' => 'login']));
    }
}

function old_input(string $key, $default = '')
{
    return $_SESSION['old'][$key] ?? $default;
}

function with_old_input(array $data): void
{
    $_SESSION['old'] = $data;
}

function consume_old_input(): array
{
    $old = $_SESSION['old'] ?? [];
    unset($_SESSION['old']);

    return $old;
}

function status_select_options(): array
{
    return [
        'enviado' => 'Enviado',
        'aprovado' => 'Aprovado',
        'recusado' => 'Recusado',
    ];
}

function bootstrap_application_schema(array &$config): void
{
    $db = Database::connection();
    $databaseName = $config['database']['name'];

    $db->exec(
        'CREATE TABLE IF NOT EXISTS settings (
            key_name VARCHAR(120) PRIMARY KEY,
            value_text TEXT NULL,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    ensure_table_column(
        $db,
        $databaseName,
        'products',
        'cost_price',
        'ALTER TABLE products ADD COLUMN cost_price DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER unit_price'
    );

    ensure_table_column(
        $db,
        $databaseName,
        'quote_items',
        'cost_price',
        'ALTER TABLE quote_items ADD COLUMN cost_price DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER unit_price'
    );

    $defaults = [
        'company_name' => $config['company']['name'] ?? '',
        'company_document' => $config['company']['document'] ?? '',
        'company_email' => $config['company']['email'] ?? '',
        'company_phone' => $config['company']['phone'] ?? '',
        'company_address' => $config['company']['address'] ?? '',
        'company_logo' => $config['company']['logo'] ?? '',
    ];

    $checkStmt = $db->prepare('SELECT value_text FROM settings WHERE key_name = :key_name LIMIT 1');
    $insertStmt = $db->prepare('INSERT INTO settings (key_name, value_text) VALUES (:key_name, :value_text)');

    foreach ($defaults as $key => $value) {
        $checkStmt->execute(['key_name' => $key]);
        $exists = $checkStmt->fetchColumn();

        if ($exists === false) {
            $insertStmt->execute([
                'key_name' => $key,
                'value_text' => $value,
            ]);
        }
    }

    $stmt = $db->query('SELECT key_name, value_text FROM settings');
    $settings = [];
    foreach ($stmt->fetchAll() as $row) {
        $settings[$row['key_name']] = $row['value_text'];
    }

    $config['company'] = array_merge($config['company'], [
        'name' => $settings['company_name'] ?? $config['company']['name'],
        'document' => $settings['company_document'] ?? $config['company']['document'],
        'email' => $settings['company_email'] ?? $config['company']['email'],
        'phone' => $settings['company_phone'] ?? $config['company']['phone'],
        'address' => $settings['company_address'] ?? $config['company']['address'],
        'logo' => $settings['company_logo'] ?? $config['company']['logo'],
    ]);

    $uploadDir = public_path('uploads/company');
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }
}

function ensure_table_column(PDO $db, string $databaseName, string $table, string $column, string $alterSql): void
{
    $stmt = $db->prepare(
        'SELECT COUNT(*)
         FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = :schema_name AND TABLE_NAME = :table_name AND COLUMN_NAME = :column_name'
    );
    $stmt->execute([
        'schema_name' => $databaseName,
        'table_name' => $table,
        'column_name' => $column,
    ]);

    if ((int) $stmt->fetchColumn() === 0) {
        $db->exec($alterSql);
    }
}

function dashboard_json($value): string
{
    return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
}
