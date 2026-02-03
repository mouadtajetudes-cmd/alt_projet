<?php

use alt\core\repositories\ProductRepositoryInterface;
use alt\core\services\ProductService;
use alt\core\services\ProductServiceInterface;
use alt\infra\repositories\PdoProductRepository;
use PDO;

return [
    'pdo' => static function ($c): PDO {
        $dbConfig = $c->get('settings')['database'];
        $driver  = $dbConfig['driver'] ?? 'pgsql';
        $host    = $dbConfig['host'] ?? 'alt.db';
        $dbname  = $dbConfig['database'] ?? 'alt_marketplace';
        $user    = $dbConfig['username'] ?? 'alt';
        $pass    = $dbConfig['password'] ?? 'alt';

        $dsn = "{$driver}:host={$host};dbname={$dbname}";
        
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        return $pdo;
    },
    
    ProductRepositoryInterface::class => function ($c) {
        return new PdoProductRepository($c->get('pdo'));
    },
    
    ProductServiceInterface::class => function ($c) {
        return new ProductService(
            $c->get(ProductRepositoryInterface::class)
        );
    },
];
