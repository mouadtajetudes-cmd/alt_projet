<?php

use alt\core\repositories\UserRepositoryInterface;
use alt\core\services\UserService;
use alt\core\services\UserServiceInterface;
use alt\infra\repositories\PdoUserRepository;
use PDO;

return [
    'pdo' => static function ($c): PDO {
        $dbConfig = $c->get('settings')['database'];
        $driver  = $dbConfig['driver'] ?? 'pgsql';
        $host    = $dbConfig['host'] ?? 'alt.db';
        $dbname  = $dbConfig['database'] ?? 'alt_auth';
        $user    = $dbConfig['username'] ?? 'alt';
        $pass    = $dbConfig['password'] ?? 'alt';

        $dsn = "{$driver}:host={$host};dbname={$dbname}";
        
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        return $pdo;
    },
    
    UserRepositoryInterface::class => function ($c) {
        return new PdoUserRepository($c->get('pdo'));
    },
    
    UserServiceInterface::class => function ($c) {
        return new UserService(
            $c->get(UserRepositoryInterface::class)
        );
    },
];
