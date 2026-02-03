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
use alt\core\application\ports\api\UserServiceInterface;
use alt\core\application\ports\api\GroupServiceInterface;
use alt\core\application\ports\api\AdServiceInterface;

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
];