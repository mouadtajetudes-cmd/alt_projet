<?php

use alt\api\actions\CreateAvatarAction;
use alt\api\actions\UpdateAvatarAction;
use alt\api\actions\GetUserAvatarAction;
use alt\api\actions\GetLevelsAction;
use alt\api\actions\LevelUpAvatarAction;
use alt\api\middlewares\AuthMiddleware;
use alt\core\application\ports\api\AvatarServiceInterface;
use alt\core\application\ports\api\AvatarVersionServiceInterface;
use alt\core\application\ports\api\LevelServiceInterface;

return [
    CreateAvatarAction::class => function ($c) {
        return new CreateAvatarAction(
            $c->get(AvatarServiceInterface::class)
        );
    },
    UpdateAvatarAction::class => function ($c) {
        return new UpdateAvatarAction(
            $c->get(AvatarServiceInterface::class)
        );
    },
    GetUserAvatarAction::class => function ($c) {
        return new GetUserAvatarAction(
            $c->get(AvatarServiceInterface::class)
        );
    },
    GetLevelsAction::class => function ($c) {
        return new GetLevelsAction(
            $c->get(LevelServiceInterface::class)
        );
    },
    LevelUpAvatarAction::class => function ($c) {
        return new LevelUpAvatarAction(
            $c->get(AvatarVersionServiceInterface::class)
        );
    },
];
