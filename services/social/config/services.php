<?php

use alt\core\application\ports\api\CommentServiceInterface;
use alt\core\application\ports\api\ReactionServiceInterface;
<<<<<<< HEAD
use alt\core\repositories\CommentRepositoryInterface;
use alt\core\repositories\LikeRepositoryInterface;
use alt\infra\repositories\PdoLikeRepository;
use alt\core\repositories\PostRepositoryInterface;
use alt\core\application\ports\api\PostServiceInterface;
use alt\core\repositories\ReactionRepositoryInterface;
use alt\core\services\CommentService;
use alt\core\services\LikeService;
use alt\core\services\PostService;
use alt\core\services\ReactionService;
use alt\infra\repositories\PdoCommentRepository;
use alt\infra\repositories\PdoPostRepository;
use alt\infra\repositories\PdoReactionRepository;
use alt\core\application\ports\api\LikeServiceInterface;
=======
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
>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9


return [
    'pdo' => static function ($c): \PDO {
        $dbConfig = $c->get('settings')['database'];
<<<<<<< HEAD
        $host = $dbConfig['host'] ?? 'alt_db';
        $port = $dbConfig['port'] ?? 5432; 
        $dbname = $dbConfig['database'] ?? 'alt_social';
=======
        $host = $dbConfig['host'] ?? 'alt.db';
        $port = $dbConfig['port'] ?? 5432; 
        $dbname = $dbConfig['database'] ?? 'alt';
>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9
        $user = $dbConfig['username'] ?? 'alt';
        $pass = $dbConfig['password'] ?? 'alt';

        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";

        $pdo = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ]);

        return $pdo;
    },
<<<<<<< HEAD

=======
>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9
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
<<<<<<< HEAD
    LikeRepositoryInterface::class => function ($c){
        return new PdoLikeRepository($c->get('pdo'));
    },
=======
>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9

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
<<<<<<< HEAD
    },
    LikeServiceInterface::class => function ($c){
        return new LikeService($c->get(LikeRepositoryInterface::class));
=======
>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9
    }
];
