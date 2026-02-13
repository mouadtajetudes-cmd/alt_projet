<?php

return [
    'settings' => [
        'displayErrorDetails' => $_ENV['APP_ENV'] !== 'production',
        'logError' => true,
        'logErrorDetails' => true,
        
        'database' => [
            'driver' => $_ENV['DB_DRIVER'] ?? 'pgsql',
            'host' => $_ENV['DB_HOST'] ?? 'alt-db',
            'port' => $_ENV['DB_PORT'] ?? '5432',
            'database' => $_ENV['DB_NAME'] ?? 'marketplace_db',
            'username' => $_ENV['DB_USER'] ?? 'marketplace_user',
            'password' => $_ENV['DB_PASSWORD'] ?? 'marketplace_password',
            'charset' => 'utf8',
        ],
    ],
];
