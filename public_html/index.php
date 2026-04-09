<?php

$config = require __DIR__ . '/config.php';
date_default_timezone_set($config['app']['timezone'] ?? 'UTC');

$sessionName = $config['auth']['session_name'] ?? 'crm_session';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name($sessionName);
    session_start();
}

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

bootstrap_application_schema($config);

$page = $_GET['page'] ?? (is_logged_in() ? 'dashboard' : 'login');
$action = $_GET['action'] ?? 'index';

try {
    switch ($page) {
        case 'login':
            $controller = new AuthController($config);
            $method = $action === 'authenticate' ? 'login' : 'showLogin';
            break;
        case 'logout':
            $controller = new AuthController($config);
            $method = 'logout';
            break;
        case 'dashboard':
            require_auth();
            $controller = new DashboardController($config);
            $method = 'index';
            break;
        case 'clients':
            require_auth();
            $controller = new ClientController($config);
            $map = [
                'index' => 'index',
                'create' => 'create',
                'store' => 'store',
                'edit' => 'edit',
                'update' => 'update',
                'delete' => 'delete',
                'history' => 'history',
            ];
            $method = $map[$action] ?? null;
            break;
        case 'products':
            require_auth();
            $controller = new ProductController($config);
            $map = [
                'index' => 'index',
                'create' => 'create',
                'store' => 'store',
                'edit' => 'edit',
                'update' => 'update',
                'delete' => 'delete',
            ];
            $method = $map[$action] ?? null;
            break;
        case 'quotes':
            require_auth();
            $controller = new QuoteController($config);
            $map = [
                'index' => 'index',
                'create' => 'create',
                'store' => 'store',
                'show' => 'show',
                'status' => 'updateStatus',
                'pdf' => 'generatePdf',
            ];
            $method = $map[$action] ?? null;
            break;
        case 'settings':
            require_auth();
            $controller = new SettingsController($config);
            $map = [
                'index' => 'index',
                'update' => 'update',
                'reset_data' => 'resetData',
            ];
            $method = $map[$action] ?? null;
            break;
        case 'whatsapp':
            require_auth();
            $controller = new WhatsAppController($config);
            $map = [
                'index' => 'index',
            ];
            $method = $map[$action] ?? null;
            break;
        default:
            http_response_code(404);
            echo 'Pagina nao encontrada.';
            exit;
    }

    if (empty($method) || !method_exists($controller, $method)) {
        http_response_code(404);
        echo 'Pagina nao encontrada.';
        exit;
    }

    $controller->{$method}();
} catch (Throwable $exception) {
    http_response_code(500);
    ?>
    <!doctype html>
    <html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Erro de aplicacao</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100">
        <main class="mx-auto flex min-h-screen max-w-3xl items-center px-6 py-16">
            <div class="w-full rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl shadow-black/30 backdrop-blur">
                <p class="mb-3 text-sm uppercase tracking-[0.3em] text-rose-300">Erro interno</p>
                <h1 class="text-3xl font-semibold">Nao foi possivel carregar o CRM.</h1>
                <p class="mt-4 text-slate-300">Verifique o banco de dados em <strong>config.php</strong> e confirme se o MySQL esta acessivel.</p>
                <pre class="mt-6 overflow-auto rounded-2xl bg-slate-900/80 p-4 text-sm text-slate-200"><?= e($exception->getMessage()) ?></pre>
            </div>
        </main>
    </body>
    </html>
    <?php
}
