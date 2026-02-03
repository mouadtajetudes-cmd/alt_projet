<?php

use alt\api\actions\GetPostAction;
use alt\api\middlewares\AuthMiddleware;
use alt\core\services\PostServiceInterface;

return [
    GetPostAction::class => function ($c) {
        return new GetPostAction(
            $c->get(PostServiceInterface::class)
        );
    },

    AuthMiddleware::class => function ($c) {
        return new AuthMiddleware();
    },
];
