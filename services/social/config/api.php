<?php

use alt\api\actions\CreateCommentAction;
use alt\api\actions\CreateFollowerAction;
use alt\api\actions\CreatePostAction;
use alt\api\actions\DeleteFollowerAction;
use alt\api\actions\DeletePostAction;
use alt\api\actions\GetAllPostsAction;
use alt\api\actions\GetCommentsByPostAction;
use alt\api\actions\GetFollowingAction;
use alt\api\actions\GetReactionsByPostAction;
use alt\api\actions\IsFollowingAction;
use alt\api\provider\jwt\JwtManagerInterface;
use alt\core\application\ports\api\CommentServiceInterface;
use alt\core\application\ports\api\PostServiceInterface;
use alt\api\middlewares\AuthMiddleware;
use alt\core\application\ports\api\ReactionServiceInterface;
use alt\api\actions\GetByUserPostsAction;
use alt\api\actions\GetFollowerAction;
use alt\core\application\ports\api\FollowerServiceInterface;


return [
    GetAllPostsAction::class => function ($c) {
        return new GetAllPostsAction($c->get(PostServiceInterface::class));
    },
    CreatePostAction::class => function ($c) {
        return new CreatePostAction($c->get(PostServiceInterface::class));
    },
    DeletePostAction::class => function ($c) {
        return new DeletePostAction($c->get(PostServiceInterface::class));
    },

    GetByUserPostsAction::class => function ($c) {
        return new GetByUserPostsAction($c->get(PostServiceInterface::class));
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
        return new AuthMiddleware($c->get(JwtManagerInterface::class));
    },
    GetByUserPostsAction::class => function ($c) {
        return new GetByUserPostsAction($c->get(PostServiceInterface::class));
    },
    GetFollowerAction::class =>function($c){
        return new GetFollowerAction($c->get(FollowerServiceInterface::class));
    },
        GetFollowingAction::class =>function($c){
        return new GetFollowingAction($c->get(FollowerServiceInterface::class));
    },
    CreateFollowerAction::class => function($c){
        return new CreateFollowerAction($c->get(FollowerServiceInterface::class));
    },
    DeleteFollowerAction::class => function($c){
        return new DeleteFollowerAction($c->get(FollowerServiceInterface::class));
    },
    IsFollowingAction::class => function($c){
        return new IsFollowingAction($c->get(FollowerServiceInterface::class));
    },





];
