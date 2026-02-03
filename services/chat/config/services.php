<?php

use alt\core\repositories\MessageRepositoryInterface;
use alt\core\services\MessageService;
use alt\core\services\MessageServiceInterface;
use alt\infra\repositories\MongoMessageRepository;
use MongoDB\Client;

return [
    'mongodb' => static function ($c): Client {
        $mongoUri = $_ENV['MONGODB_URI'] ?? 'mongodb://alt.mongo:27017';
        return new Client($mongoUri);
    },
    
    MessageRepositoryInterface::class => function ($c) {
        return new MongoMessageRepository(
            $c->get('mongodb'),
            $_ENV['MONGODB_DATABASE'] ?? 'chat_db'
        );
    },
    
    MessageServiceInterface::class => function ($c) {
        return new MessageService(
            $c->get(MessageRepositoryInterface::class)
        );
    },
];
