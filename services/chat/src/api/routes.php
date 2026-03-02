<?php
declare(strict_types=1);

use alt\api\actions\GetMessagesAction;
use alt\api\actions\CreateMessageAction;
use alt\api\actions\GetConversationsAction;
use alt\api\actions\GetConversationByIdAction;
use alt\api\actions\CreateConversationAction;
use alt\api\middlewares\AuthMiddleware;

return function(\Slim\App $app): \Slim\App {

    $app->get('/', function ($request, $response) {
        $response->getBody()->write(json_encode([
            'service' => 'alt-chat',
            'status' => 'running',
            'version' => '1.0.0',
            'database' => 'mongodb'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Message
    $app->get('/messages', GetMessagesAction::class);
        // ->add(AuthMiddleware::class);

    $app->post('/messages', CreateMessageAction::class);
        // ->add(AuthMiddleware::class);

    // Conversation endpoints
    $app->get('/users/{userId}/conversations', GetConversationsAction::class);
        // ->add(AuthMiddleware::class);

    $app->get('/conversations/{id}', GetConversationByIdAction::class);
        // ->add(AuthMiddleware::class);

    $app->post('/conversations', CreateConversationAction::class);
        // ->add(AuthMiddleware::class);

    return $app;
};
