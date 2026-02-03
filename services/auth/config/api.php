<?php

use alt\api\actions\GetUserAction;
use alt\api\middlewares\AuthMiddleware;
use alt\core\services\UserServiceInterface;

return [
    GetUserAction::class => function ($c) {
        return new GetUserAction(
            $c->get(UserServiceInterface::class)
        );
    },

    AuthMiddleware::class => function ($c) {
        return new AuthMiddleware();
    },
];