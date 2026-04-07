<?php

return [
    'app' => [
        'name' => 'CRM de Orcamentos',
        'base_url' => '',
        'timezone' => 'America/Sao_Paulo',
        'currency' => 'BRL',
        'public_pdf_path' => 'pdf',
    ],
    'database' => [
        'host' => '127.0.0.1',
        'port' => '3307',
        'name' => 'crm_orcamentos',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'company' => [
        'name' => 'Sua Empresa',
        'document' => '00.000.000/0001-00',
        'email' => 'contato@suaempresa.com',
        'phone' => '(11) 99999-9999',
        'address' => 'Rua Exemplo, 123 - Centro - Sao Paulo/SP',
        'logo' => 'assets/img/logo.svg',
    ],
    'auth' => [
        'session_name' => 'crm_orcamentos_session',
    ],
];
