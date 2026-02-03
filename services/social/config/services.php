<?php

use alt\core\repositories\PostRepositoryInterface;
use alt\core\services\PostService;
use alt\core\services\PostServiceInterface;
use alt\infra\repositories\PdoPostRepository;
use PDO;

return [
    'pdo' => static function ($c): PDO {
        $dbConfig = $c->get('settings')['database'];
        $driver  = $dbConfig['driver'] ?? 'pgsql';
        $host    = $dbConfig['host'] ?? 'alt.db';
        $dbname  = $dbConfig['database'] ?? 'alt_social';
        $user    = $dbConfig['username'] ?? 'alt';
        $pass    = $dbConfig['password'] ?? 'alt';

        $dsn = "{$driver}:host={$host};dbname={$dbname}";
        
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        return $pdo;
    },
    
    PostRepositoryInterface::class => function ($c) {
        return new PdoPostRepository($c->get('pdo'));
    },
    
    PostServiceInterface::class => function ($c) {
        return new PostService(
            $c->get(PostRepositoryInterface::class)
        );
    },
];
