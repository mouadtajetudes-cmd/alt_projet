<?php
declare(strict_types=1);

use alt\api\actions\GetAvatarAction;
use alt\api\middlewares\AuthMiddleware;

return function(\Slim\App $app): \Slim\App {

    $app->get('/', function ($request, $response) {
        $response->getBody()->write(json_encode([
            'service' => 'alt-avatar',
            'status' => 'running',
            'version' => '1.0.0'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/avatars/{userId}', GetAvatarAction::class)
        ->add(AuthMiddleware::class);

    return $app;
};
