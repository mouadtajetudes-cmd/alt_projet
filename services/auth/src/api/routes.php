<?php
declare(strict_types=1);

use alt\api\actions\GetAllUsersAction;
use alt\api\actions\GetUserByIdAction;
use alt\api\actions\CreateUserAction;
use alt\api\actions\UpdateUserAction;
use alt\api\actions\GetAllGroupsAction;
use alt\api\actions\CreateGroupAction;
use alt\api\actions\AddMemberToGroupAction;
use alt\api\actions\GetAllAdsAction;
use alt\api\actions\CreateAdAction;
use alt\api\actions\UpdateAdAction;
use alt\api\actions\DeleteAdAction;
use alt\api\actions\LoginAction;
use alt\api\actions\RegisterAction;
use alt\api\actions\GetUserGroupsAction;
use alt\api\actions\GetGroupMembersAction;
use alt\api\actions\RemoveMemberAction;
// use alt\api\middlewares\AuthMiddleware;

return function(\Slim\App $app): \Slim\App {

    $app->get('/', function ($request, $response) {
        $response->getBody()->write(json_encode([
            'service' => 'alt-auth',
            'status' => 'running',
            'version' => '1.0.0'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // (mode dev - auth désactivé)
    $app->get('/users', GetAllUsersAction::class);
    $app->get('/users/{id}', GetUserByIdAction::class);
    $app->post('/users', CreateUserAction::class);
    $app->put('/users/{id}', UpdateUserAction::class);
    $app->get('/users/{id}/groups', GetUserGroupsAction::class);

    $app->get('/groups', GetAllGroupsAction::class);
    $app->post('/groups', CreateGroupAction::class);
    $app->post('/groups/{id}/members', AddMemberToGroupAction::class);
    $app->get('/groups/{id}/members', GetGroupMembersAction::class);
    $app->delete('/groups/{id}/members/{userId}', RemoveMemberAction::class);

    $app->get('/ads', GetAllAdsAction::class);
    $app->post('/ads', CreateAdAction::class);
    $app->put('/ads/{id}', UpdateAdAction::class);
    $app->delete('/ads/{id}', DeleteAdAction::class);

    $app->post('/auth/login', LoginAction::class);
    $app->post('/auth/register', RegisterAction::class);

    // $app->get('/users', GetAllUsersAction::class)->add(AuthMiddleware::class);

    return $app;
};
