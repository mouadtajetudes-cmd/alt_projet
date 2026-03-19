<?php

use alt\api\actions\CreateAvatarAction;
use alt\api\actions\UpdateAvatarAction;
use alt\api\actions\GetUserAvatarAction;
use alt\api\actions\GetLevelsAction;
use alt\api\actions\LevelUpAvatarAction;
use alt\api\middlewares\AuthMiddleware;
use alt\core\application\ports\api\AvatarServiceInterface;
use alt\core\application\ports\api\LevelServiceInterface;
use alt\infra\repositories\PdoUserSelectedAvatarRepository;
use alt\infra\repositories\PdoAvatarVersionRepository;
use alt\infra\repositories\PdoLevelRepository;
use alt\infra\repositories\PdoAvatarRepository;

return [
    CreateAvatarAction::class => function ($c) {
        return new CreateAvatarAction(
            new PdoUserSelectedAvatarRepository($c->get('pdo')),
            new PdoAvatarVersionRepository($c->get('pdo')),
            new PdoAvatarRepository($c->get('pdo'))
        );
    },
    UpdateAvatarAction::class => function ($c) {
        return new UpdateAvatarAction(
            $c->get(AvatarServiceInterface::class)
        );
    },
    GetUserAvatarAction::class => function ($c) {
        return new GetUserAvatarAction(
            new PdoUserSelectedAvatarRepository($c->get('pdo'))
        );
    },
    GetLevelsAction::class => function ($c) {
        return new GetLevelsAction(
            $c->get(LevelServiceInterface::class)
        );
    },
    LevelUpAvatarAction::class => function ($c) {
        return new LevelUpAvatarAction(
            new PdoUserSelectedAvatarRepository($c->get('pdo')),
            new PdoAvatarVersionRepository($c->get('pdo')),
            new PdoLevelRepository($c->get('pdo'))
        );
    },
];
