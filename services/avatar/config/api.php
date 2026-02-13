<?php

use alt\api\actions\GetAvatarAction;
use alt\api\middlewares\AuthMiddleware;
use alt\core\services\AvatarServiceInterface;

return [
    GetAvatarAction::class => function ($c) {
        return new GetAvatarAction(
            $c->get(AvatarServiceInterface::class)
        );
    },

];
