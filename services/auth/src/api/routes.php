<?php
declare(strict_types=1);

use alt\api\actions\GetUserAction;
use alt\api\middlewares\AuthMiddleware;

return function(\Slim\App $app): \Slim\App {

    $app->get('/', function ($request, $response) {
        $response->getBody()->write(json_encode([
            'service' => 'alt-auth',
            'status' => 'running',
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    });


    return $app;
};
