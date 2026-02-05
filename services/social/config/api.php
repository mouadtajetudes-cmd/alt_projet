<?php

use alt\api\actions\CreateCommentAction;
use alt\api\actions\GetCommentsByPostAction;
use alt\api\actions\GetReactionsByPostAction;
use alt\core\application\ports\api\CommentServiceInterface;
use alt\core\application\ports\api\PostServiceInterface;
use alt\api\middlewares\AuthMiddleware;
use alt\core\application\action\GetByIdAction;
use alt\core\application\ports\api\ReactionServiceInterface;


return [
    GetByIdAction::class => function ($c) {
        return new GetByIdAction(
            $c->get(PostServiceInterface::class)
        );
    },

    GetReactionsByPostAction::class=> function($c){
        return new GetReactionsByPostAction(
            $c->get(ReactionServiceInterface::class),
            $c->get(PostServiceInterface::class)
        );
    },
    GetCommentsByPostAction::class =>function($c){
        return new GetCommentsByPostAction($c->get(CommentServiceInterface::class));
    },
    CreateCommentAction::class=>function($c){
        return new CreateCommentAction($c->get(CommentServiceInterface::class));
    },

    AuthMiddleware::class => function ($c) {
        return new AuthMiddleware();
    },
];
