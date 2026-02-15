<?php

use alt\core\application\ports\api\CommentServiceInterface;
use alt\core\application\ports\api\ReactionServiceInterface;
use alt\core\application\ports\spi\CommentRepositoryInterface;
use alt\core\application\ports\spi\ReactionRepositoryInterface;
use alt\core\application\useCases\CommentService;
use alt\core\application\useCases\ReactionService;
use alt\core\repositories\PostRepositoryInterface;
use alt\core\application\useCases\PostService;
use alt\core\application\ports\api\PostServiceInterface;
use alt\infra\repositories\PdoCommentRepository;
use alt\infra\repositories\PdoPostRepository;
use alt\infra\repositories\PdoReactionRepository;


return [
    'pdo' => static function ($c): \PDO {
        $dbConfig = $c->get('settings')['database'];
        $host = $dbConfig['host'] ?? 'alt.db';
        $port = $dbConfig['port'] ?? 5432; 
        $dbname = $dbConfig['database'] ?? 'alt';
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
    }
];
