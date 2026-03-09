<?php

use alt\api\actions\CreateCommentAction;
use alt\api\actions\GetCommentsByPostAction;
use alt\api\actions\GetReactionsByPostAction;
use alt\core\application\ports\api\CommentServiceInterface;
use alt\core\application\ports\api\PostServiceInterface;
<<<<<<< HEAD
use alt\api\middlewares\AuthMiddleware;
=======
use alt\core\application\action\GetByIdAction;
>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9
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
<<<<<<< HEAD
    },
    GetCommentsByPostAction::class =>function($c){
        return new GetCommentsByPostAction($c->get(CommentServiceInterface::class));
    },
    CreateCommentAction::class=>function($c){
        return new CreateCommentAction($c->get(CommentServiceInterface::class));
    },

    AuthMiddleware::class => function ($c) {
        return new AuthMiddleware();
=======
>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9
    },
    GetCommentsByPostAction::class =>function($c){
        return new GetCommentsByPostAction($c->get(CommentServiceInterface::class));
    },
    CreateCommentAction::class=>function($c){
        return new CreateCommentAction($c->get(CommentServiceInterface::class));
    },

];
