<?php

use alt\core\repositories\AvatarRepositoryInterface;
use alt\core\services\AvatarService;
use alt\core\services\AvatarServiceInterface;
use alt\infra\repositories\PdoAvatarRepository;
use PDO;

return [
    'pdo' => static function ($c): PDO {
        $dbConfig = $c->get('settings')['database'];
        $driver  = $dbConfig['driver'] ?? 'pgsql';
        $host    = $dbConfig['host'] ?? 'alt.db';
        $dbname  = $dbConfig['database'] ?? 'alt_avatar';
        $user    = $dbConfig['username'] ?? 'alt';
        $pass    = $dbConfig['password'] ?? 'alt';

        $dsn = "{$driver}:host={$host};dbname={$dbname}";
        
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        return $pdo;
    },
    
    AvatarRepositoryInterface::class => function ($c) {
        return new PdoAvatarRepository($c->get('pdo'));
    },
    
    AvatarServiceInterface::class => function ($c) {
        return new AvatarService(
            $c->get(AvatarRepositoryInterface::class)
        );
    },
];
