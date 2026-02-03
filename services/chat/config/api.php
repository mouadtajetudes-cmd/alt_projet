<?php

use alt\api\actions\GetMessagesAction;
use alt\api\middlewares\AuthMiddleware;
use alt\core\services\MessageServiceInterface;

return [
    GetMessagesAction::class => function ($c) {
        return new GetMessagesAction(
            $c->get(MessageServiceInterface::class)
        );
    },

    AuthMiddleware::class => function ($c) {
        return new AuthMiddleware();
    },
];
