<?php

return [
    'settings' => [
        'displayErrorDetails' => $_ENV['APP_ENV'] !== 'production',
        'logError' => true,
        'logErrorDetails' => true,
        
        'database' => [
            'driver' => $_ENV['DB_DRIVER'] ?? 'pgsql',
            'host' => $_ENV['DB_HOST'] ?? 'alt.db',
            'database' => $_ENV['DB_NAME'] ?? 'alt_social',
            'username' => $_ENV['DB_USER'] ?? 'alt',
            'password' => $_ENV['DB_PASSWORD'] ?? 'alt',
            'charset' => 'utf8',
        ],
    ],
];
