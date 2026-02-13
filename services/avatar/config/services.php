<?php

use alt\core\application\ports\spi\repositoryInterfaces\AvatarRepositoryInterface;
use alt\core\application\ports\spi\repositoryInterfaces\AvatarVersionRepositoryInterface;
use alt\core\application\ports\spi\repositoryInterfaces\LevelRepositoryInterface;
use alt\core\application\ports\api\AvatarServiceInterface;
use alt\core\application\ports\api\AvatarVersionServiceInterface;
use alt\core\application\ports\api\LevelServiceInterface;
use alt\core\application\usecases\AvatarService;
use alt\core\application\usecases\AvatarVersionService;
use alt\core\application\usecases\LevelService;
use alt\infra\repositories\PdoAvatarRepository;
use alt\infra\repositories\PdoAvatarVersionRepository;
use alt\infra\repositories\PdoLevelRepository;

return [
    'pdo' => static function ($c): \PDO {
        $dbConfig = $c->get('settings')['database'];
        $driver  = $dbConfig['driver'] ?? 'pgsql';
        $host    = $dbConfig['host'] ?? 'alt.db';
        $dbname  = $dbConfig['database'] ?? 'alt_avatar';
        $user    = $dbConfig['username'] ?? 'alt';
        $pass    = $dbConfig['password'] ?? 'alt';

        $dsn = "{$driver}:host={$host};dbname={$dbname}";
        
        $pdo = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ]);

        return $pdo;
    },
    
    AvatarRepositoryInterface::class => function ($c) {
        return new PdoAvatarRepository($c->get('pdo'));
    },
    
    AvatarVersionRepositoryInterface::class => function ($c) {
        return new PdoAvatarVersionRepository($c->get('pdo'));
    },
    
    LevelRepositoryInterface::class => function ($c) {
        return new PdoLevelRepository($c->get('pdo'));
    },
    
    AvatarServiceInterface::class => function ($c) {
        return new AvatarService(
            $c->get(AvatarRepositoryInterface::class)
        );
    },
    
    AvatarVersionServiceInterface::class => function ($c) {
        return new AvatarVersionService(
            $c->get(AvatarVersionRepositoryInterface::class)
        );
    },
    
    LevelServiceInterface::class => function ($c) {
        return new LevelService(
            $c->get(LevelRepositoryInterface::class)
        );
    },
];
