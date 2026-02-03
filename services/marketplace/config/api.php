<?php

use alt\api\actions\GetProductAction;
use alt\api\middlewares\AuthMiddleware;
use alt\core\services\ProductServiceInterface;

return [
    GetProductAction::class => function ($c) {
        return new GetProductAction(
            $c->get(ProductServiceInterface::class)
        );
    },

    AuthMiddleware::class => function ($c) {
        return new AuthMiddleware();
    },
];
