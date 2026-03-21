<?php

use alt\api\provider\jwt\JwtManager;
use alt\api\provider\jwt\JwtManagerInterface;
use alt\core\application\ports\api\CommentServiceInterface;
use alt\core\application\ports\api\FollowerServiceInterface;
use alt\core\application\ports\api\ReactionServiceInterface;
use alt\core\repositories\CommentRepositoryInterface;
use alt\core\repositories\FollowerRepositoryInterface;
use alt\core\repositories\LikeRepositoryInterface;
use alt\core\repositories\PostRepositoryInterface;
use alt\core\services\FollowerService;
use alt\infra\repositories\PdoFollowerRepository;
use alt\infra\repositories\PdoLikeRepository;
use alt\core\application\ports\api\PostServiceInterface;
use alt\core\repositories\ReactionRepositoryInterface;
use alt\core\services\CommentService;
use alt\core\services\LikeService;
use alt\core\services\PostService;
use alt\core\services\ReactionService;
use alt\infra\repositories\PdoCommentRepository;
use alt\core\application\ports\api\LikeServiceInterface;
use alt\infra\repositories\PdoPostRepository;
use alt\infra\repositories\PdoReactionRepository;


return [
    'pdo' => static function ($c): \PDO {
        $dbConfig = $c->get('settings')['database'];
        $host = $dbConfig['host'] ?? 'alt_db';
        $port = $dbConfig['port'] ?? 5432; 
        $dbname = $dbConfig['database'] ?? 'alt_social';
        $user = $dbConfig['username'] ?? 'alt';
        $pass = $dbConfig['password'] ?? 'alt';

        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";

        $pdo = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ]);

        return $pdo;
    },
    //reposository

    
    PostRepositoryInterface::class => function ($c) {
        return new PdoPostRepository($c->get('pdo'));
    },
    ReactionRepositoryInterface::class => function ($c){
        return new PdoReactionRepository($c->get('pdo'));
    },
    CommentRepositoryInterface::class =>function ($c){
        return new PdoCommentRepository($c->get('pdo'));
    },
    LikeRepositoryInterface::class => function ($c){
        return new PdoLikeRepository($c->get('pdo'));
    },
    FollowerRepositoryInterface::class => function($c){
        return new PdoFollowerRepository($c->get('pdo'));
    },


    //service
    PostServiceInterface::class => function ($c) {
        return new PostService(
            $c->get(PostRepositoryInterface::class)
        );
    },
    ReactionServiceInterface::class=>function($c){
        return new ReactionService(
            $c->get(ReactionRepositoryInterface::class)
        );
    },
    CommentServiceInterface::class =>function($c){
        return new CommentService($c->get(CommentRepositoryInterface::class));
    },
    LikeServiceInterface::class => function ($c){
        return new LikeService($c->get(LikeRepositoryInterface::class));
    },
    FollowerServiceInterface::class => function($c){
        return new FollowerService($c->get(FollowerRepositoryInterface::class));
    },
    JwtManagerInterface::class=>function($c){
        return new JwtManager("341e24419bac01ddffd0964991bc701b");
    },

];
