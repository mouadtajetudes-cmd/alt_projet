<?php
declare(strict_types=1);

use alt\core\application\ports\spi\repositoryInterfaces\UserRepositoryInterface;
use alt\core\application\ports\spi\repositoryInterfaces\GroupRepositoryInterface;
use alt\core\application\ports\spi\repositoryInterfaces\AdRepositoryInterface;
use alt\core\application\ports\api\UserServiceInterface;
use alt\core\application\ports\api\GroupServiceInterface;
use alt\core\application\ports\api\AdServiceInterface;
use alt\core\application\ports\api\AuthServiceInterface;
use alt\core\application\ports\api\provider\AuthProviderInterface;
use alt\core\application\ports\api\provider\jwt\JwtManagerInterface;
use alt\core\application\usecases\UserService;
use alt\core\application\usecases\GroupService;
use alt\core\application\usecases\AdService;
use alt\core\application\usecases\AuthService;
use alt\infra\repositories\PdoUserRepository;
use alt\infra\repositories\PdoGroupRepository;
use alt\infra\repositories\PdoAdRepository;
use alt\infra\auth\jwt\JWTManager;
use alt\infra\auth\jwt\JWTAuthProvider;

return [
    'pdo' => static function ($c): \PDO {
        $dbConfig = $c->get('settings')['database'];
        $driver  = $dbConfig['driver'] ?? 'pgsql';
        $host    = $dbConfig['host'] ?? 'alt.db';
        $dbname  = $dbConfig['database'] ?? 'alt';
        $user    = $dbConfig['username'] ?? 'alt';
        $pass    = $dbConfig['password'] ?? 'alt';

        $dsn = "{$driver}:host={$host};dbname={$dbname}";
        
        $pdo = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ]);

        return $pdo;
    },
    
    UserRepositoryInterface::class => function ($c) {
        return new PdoUserRepository($c->get('pdo'));
    },
    
    GroupRepositoryInterface::class => function ($c) {
        return new PdoGroupRepository($c->get('pdo'));
    },
    
    AdRepositoryInterface::class => function ($c) {
        return new PdoAdRepository($c->get('pdo'));
    },
    
    UserServiceInterface::class => function ($c) {
        return new UserService(
            $c->get(UserRepositoryInterface::class)
        );
    },
    
    GroupServiceInterface::class => function ($c) {
        return new GroupService(
            $c->get(GroupRepositoryInterface::class)
        );
    },
    
    AdServiceInterface::class => function ($c) {
        return new AdService(
            $c->get(AdRepositoryInterface::class)
        );
    },
    
    JwtManagerInterface::class => function ($c) {
        $secret = $_ENV['JWT_SECRET'];
        return new JWTManager($secret);
    },
    
    AuthProviderInterface::class => function ($c) {
        return new JWTAuthProvider(
            $c->get(UserRepositoryInterface::class),
            $c->get(JwtManagerInterface::class)
        );
    },
    
    AuthServiceInterface::class => function ($c) {
        return new AuthService(
            $c->get(UserRepositoryInterface::class),
            $c->get(AuthProviderInterface::class)
        );
    },
];