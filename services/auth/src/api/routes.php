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
use alt\api\actions\ValidateTokenAction;
use alt\api\actions\RefreshTokenAction;
use alt\api\actions\GetUserGroupsAction;
use alt\api\actions\GetGroupMembersAction;
use alt\api\actions\RemoveMemberAction;
use alt\api\middlewares\AuthMiddleware;
use alt\api\middlewares\AdminMiddleware;
use alt\api\middlewares\PremiumMiddleware;
use alt\api\middlewares\SelfOrAdminMiddleware;

return function(\Slim\App $app): \Slim\App {

    $app->get('/', function ($request, $response) {
        $response->getBody()->write(json_encode([
            'service' => 'alt-auth',
            'status' => 'running',
            'version' => '1.0.0'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Public routes 
    $app->post('/auth/login', LoginAction::class);
    $app->post('/auth/register', RegisterAction::class);
    $app->post('/auth/tokens/validate', ValidateTokenAction::class);
    $app->post('/auth/refresh', RefreshTokenAction::class);

    // Protected routes - User management
    $app->get('/users', GetAllUsersAction::class);
        // ->add(AuthMiddleware::class);

    $app->get('/users/{id}', GetUserByIdAction::class)
        ->add(SelfOrAdminMiddleware::class)
        ->add(AuthMiddleware::class);

    $app->get('/users/{id}/groups', GetUserGroupsAction::class)
        ->add(SelfOrAdminMiddleware::class)
        ->add(AuthMiddleware::class);
    
    $app->get('/groups', GetAllGroupsAction::class)->add(AuthMiddleware::class);
    $app->get('/groups/{id}/members', GetGroupMembersAction::class)->add(AuthMiddleware::class);
    
    $app->get('/ads', GetAllAdsAction::class)->add(AuthMiddleware::class);

    // Admin only
    $app->post('/users', CreateUserAction::class)
        ->add(AdminMiddleware::class)
        ->add(AuthMiddleware::class);
        
    $app->put('/users/{id}', UpdateUserAction::class)
        ->add(SelfOrAdminMiddleware::class)
        ->add(AuthMiddleware::class);
    
    $app->post('/groups', CreateGroupAction::class)->add(AdminMiddleware::class)->add(AuthMiddleware::class);
    $app->post('/groups/{id}/members', AddMemberToGroupAction::class)->add(AdminMiddleware::class)->add(AuthMiddleware::class);
    $app->delete('/groups/{id}/members/{userId}', RemoveMemberAction::class)->add(AdminMiddleware::class)->add(AuthMiddleware::class);
    
    $app->post('/ads', CreateAdAction::class)->add(AdminMiddleware::class)->add(AuthMiddleware::class);
    $app->put('/ads/{id}', UpdateAdAction::class)->add(AdminMiddleware::class)->add(AuthMiddleware::class);
    $app->delete('/ads/{id}', DeleteAdAction::class)->add(AdminMiddleware::class)->add(AuthMiddleware::class);

    return $app;
};
