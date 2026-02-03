<?php
declare(strict_types=1);

use alt\api\actions\GetProductAction;
use alt\api\middlewares\AuthMiddleware;

return function(\Slim\App $app): \Slim\App {

    $app->get('/', function ($request, $response) {
        $response->getBody()->write(json_encode([
            'service' => 'alt-marketplace',
            'status' => 'running',
            'version' => '1.0.0'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/products/{id}', GetProductAction::class)
        ->add(AuthMiddleware::class);

    return $app;
};
