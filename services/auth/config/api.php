<?php
declare(strict_types=1);

use alt\api\actions\GetAllUsersAction;
use alt\api\actions\GetUserByIdAction;
use alt\api\actions\CreateUserAction;
use alt\api\actions\UpdateUserAction;
use alt\api\actions\UploadAvatarAction;
use alt\api\actions\UploadBannerAction;
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
use alt\core\application\ports\api\UserServiceInterface;
use alt\core\application\ports\api\GroupServiceInterface;
use alt\core\application\ports\api\AdServiceInterface;
use alt\core\application\ports\api\AuthServiceInterface;
use alt\core\application\ports\api\provider\AuthProviderInterface;

return [

    GetAllUsersAction::class => function ($c) {
        return new GetAllUsersAction(
            $c->get(UserServiceInterface::class)
        );
    },

    GetUserByIdAction::class => function ($c) {
        return new GetUserByIdAction(
            $c->get(UserServiceInterface::class)
        );
    },

    CreateUserAction::class => function ($c) {
        return new CreateUserAction(
            $c->get(UserServiceInterface::class)
        );
    },

    UpdateUserAction::class => function ($c) {
        return new UpdateUserAction(
            $c->get(UserServiceInterface::class)
        );
    },

    UploadAvatarAction::class => function ($c) {
        return new UploadAvatarAction(
            $c->get(UserServiceInterface::class)
        );
    },

    UploadBannerAction::class => function ($c) {
        return new UploadBannerAction(
            $c->get(UserServiceInterface::class)
        );
    },

    GetAllGroupsAction::class => function ($c) {
        return new GetAllGroupsAction(
            $c->get(GroupServiceInterface::class)
        );
    },

    CreateGroupAction::class => function ($c) {
        return new CreateGroupAction(
            $c->get(GroupServiceInterface::class)
        );
    },

    AddMemberToGroupAction::class => function ($c) {
        return new AddMemberToGroupAction(
            $c->get(GroupServiceInterface::class)
        );
    },

    GetAllAdsAction::class => function ($c) {
        return new GetAllAdsAction(
            $c->get(AdServiceInterface::class)
        );
    },

    CreateAdAction::class => function ($c) {
        return new CreateAdAction(
            $c->get(AdServiceInterface::class)
        );
    },

    UpdateAdAction::class => function ($c) {
        return new UpdateAdAction(
            $c->get(AdServiceInterface::class)
        );
    },

    DeleteAdAction::class => function ($c) {
        return new DeleteAdAction(
            $c->get(AdServiceInterface::class)
        );
    },

    LoginAction::class => function ($c) {
        return new LoginAction(
            $c->get(AuthServiceInterface::class)
        );
    },

    RegisterAction::class => function ($c) {
        return new RegisterAction(
            $c->get(AuthServiceInterface::class)
        );
    },

    ValidateTokenAction::class => function ($c) {
        return new ValidateTokenAction(
            $c->get(AuthProviderInterface::class)
        );
    },

    RefreshTokenAction::class => function ($c) {
        return new RefreshTokenAction(
            $c->get(AuthServiceInterface::class)
        );
    },

    GetUserGroupsAction::class => function ($c) {
        return new GetUserGroupsAction(
            $c->get(GroupServiceInterface::class)
        );
    },

    GetGroupMembersAction::class => function ($c) {
        return new GetGroupMembersAction(
            $c->get(GroupServiceInterface::class)
        );
    },

    RemoveMemberAction::class => function ($c) {
        return new RemoveMemberAction(
            $c->get(GroupServiceInterface::class)
        );
    },

    AuthMiddleware::class => function ($c) {
        return new AuthMiddleware(
            $c->get(AuthProviderInterface::class)
        );
    },

    AdminMiddleware::class => function ($c) {
        return new AdminMiddleware();
    },

    PremiumMiddleware::class => function ($c) {
        return new PremiumMiddleware();
    },
];