<?php
declare(strict_types=1);

use alt\api\actions\CreateAvatarAction;
use alt\api\actions\UpdateAvatarAction;
use alt\api\actions\GetUserAvatarAction;
use alt\api\actions\GetAllAvatarsAction;
use alt\api\actions\GetLevelsAction;
use alt\api\actions\LevelUpAvatarAction;
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

    $app->get('/avatars', GetAllAvatarsAction::class);
        //->add(AuthMiddleware::class);
    $app->post('/avatars', CreateAvatarAction::class);
        //->add(AuthMiddleware::class);
    $app->get('/users/{userId}/avatars', GetUserAvatarAction::class);
        //->add(AuthMiddleware::class);
    $app->put('/avatars/{avatarId}', UpdateAvatarAction::class);
        //->add(AuthMiddleware::class);
    
    $app->get('/levels', GetLevelsAction::class);
        //->add(AuthMiddleware::class);

    $app->post('/avatar-versions/{versionId}/level-up', LevelUpAvatarAction::class);
        //->add(AuthMiddleware::class);

    return $app;
};
