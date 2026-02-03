<?php
declare(strict_types=1);

use alt\api\actions\GetMessagesAction;
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

    $app->get('/messages/{roomId}', GetMessagesAction::class)
        ->add(AuthMiddleware::class);

    return $app;
};
