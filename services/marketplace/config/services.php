<?php

use alt\core\application\ports\spi\repositoryInterfaces\CategoryRepositoryInterface;
use alt\core\application\ports\spi\repositoryInterfaces\ProductRepositoryInterface;
use alt\core\application\ports\spi\repositoryInterfaces\MediaRepositoryInterface;
use alt\core\application\ports\api\CategoryServiceInterface;
use alt\core\application\ports\api\ProductServiceInterface;
use alt\core\application\ports\api\MediaServiceInterface;
use alt\core\application\usecases\CategoryService;
use alt\core\application\usecases\ProductService;
use alt\core\application\usecases\MediaService;
use alt\infra\repositories\PdoCategoryRepository;
use alt\infra\repositories\PdoProductRepository;
use alt\infra\repositories\PdoMediaRepository;


return [
    'pdo' => static function ($c): \PDO {
        $dbConfig = $c->get('settings')['database'];
        $driver = $dbConfig['driver'] ?? 'pgsql';
        $host = $dbConfig['host'] ?? 'alt-db';
        $port = $dbConfig['port'] ?? '5432';
        $dbname = $dbConfig['database'] ?? 'marketplace_db';
        $user = $dbConfig['username'] ?? 'marketplace_user';
        $pass = $dbConfig['password'] ?? 'marketplace_password';

        $dsn = "{$driver}:host={$host};port={$port};dbname={$dbname}";
        
        $pdo = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ]);

        return $pdo;
    },
    
    CategoryRepositoryInterface::class => function ($c) {
        return new PdoCategoryRepository($c->get('pdo'));
    },
    
    ProductRepositoryInterface::class => function ($c) {
        return new PdoProductRepository($c->get('pdo'));
    },
    
    MediaRepositoryInterface::class => function ($c) {
        return new PdoMediaRepository($c->get('pdo'));
    },
    
    CategoryServiceInterface::class => function ($c) {
        return new CategoryService(
            $c->get(CategoryRepositoryInterface::class)
        );
    },
    
    ProductServiceInterface::class => function ($c) {
        return new ProductService(
            $c->get(ProductRepositoryInterface::class),
            $c->get(MediaRepositoryInterface::class)
        );
    },
    
    MediaServiceInterface::class => function ($c) {
        return new MediaService(
            $c->get(MediaRepositoryInterface::class)
        );
    },
];
