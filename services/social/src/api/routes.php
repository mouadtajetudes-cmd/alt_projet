<?php
declare(strict_types=1);

use alt\api\actions\CreateReactionAction;
use alt\api\actions\GetReactionsByPostAction;
use alt\core\application\action\CreatePostAction;
use alt\core\application\action\GetAllPostsAction;
use alt\core\application\action\GetByIdAction;
use alt\core\application\action\GetByIdwithStatusAction;

return function(\Slim\App $app): \Slim\App {

    $app->get('/', function ($request, $response) {
        $response->getBody()->write(json_encode([
            'service' => 'alt-social',
            'status' => 'running',
            'version' => '1.0.0'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/posts/{id}', GetByIdAction::class);
    $app->get('/posts',GetAllPostsAction::class);
    $app->get('/posts/{id}/stats',GetByIdwithStatusAction::class);
    $app->post('/posts',CreatePostAction::class);


    $app->get('/posts/{id}/reactions', GetReactionsByPostAction::class);
    $app->post('/posts/{id}/reactions',CreateReactionAction::class);


    return $app;
};
