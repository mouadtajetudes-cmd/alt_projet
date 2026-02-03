<?php

return [
    'settings' => [
        'displayErrorDetails' => $_ENV['APP_ENV'] !== 'production',
        'logError' => true,
        'logErrorDetails' => true,
        
        'mongodb' => [
            'uri' => $_ENV['MONGODB_URI'] ?? 'mongodb://alt.mongo:27017',
            'database' => $_ENV['MONGODB_DATABASE'] ?? 'chat_db',
        ],
    ],
];
