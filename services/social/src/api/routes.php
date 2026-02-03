<?php
declare(strict_types=1);

use alt\api\actions\GetPostAction;
use alt\api\middlewares\AuthMiddleware;

return function(\Slim\App $app): \Slim\App {

    $app->get('/', function ($request, $response) {
        $response->getBody()->write(json_encode([
            'service' => 'alt-social',
            'status' => 'running',
            'version' => '1.0.0'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/posts/{id}', GetPostAction::class)
        ->add(AuthMiddleware::class);

    return $app;
};
