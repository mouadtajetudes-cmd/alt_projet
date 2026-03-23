<?php
declare(strict_types=1);

use alt\core\application\ports\spi\repositoryInterfaces\MessageRepositoryInterface;
use alt\core\application\ports\spi\repositoryInterfaces\ConversationRepositoryInterface;
use alt\core\application\ports\api\MessageServiceInterface;
use alt\core\application\ports\api\ConversationServiceInterface;
use alt\core\application\usecases\MessageService;
use alt\core\application\usecases\ConversationService;
use alt\infra\repositories\MongoMessageRepository;
use alt\infra\repositories\MongoConversationRepository;
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
    
    ConversationRepositoryInterface::class => function ($c) {
        return new MongoConversationRepository(
            $c->get('mongodb'),
            $_ENV['MONGODB_DATABASE'] ?? 'chat_db'
        );
    },
    
    MessageServiceInterface::class => function ($c) {
        return new MessageService(
            $c->get(MessageRepositoryInterface::class),
            $c->get(ConversationRepositoryInterface::class)
        );
    },
    
    ConversationServiceInterface::class => function ($c) {
        return new ConversationService(
            $c->get(ConversationRepositoryInterface::class)
        );
    },
];
