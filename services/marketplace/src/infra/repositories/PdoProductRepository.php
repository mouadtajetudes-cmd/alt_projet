<?php

namespace alt\infra\repositories;

use alt\core\repositories\ProductRepositoryInterface;
use PDO;

class PdoProductRepository implements ProductRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(string $productId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute(['id' => $productId]);
        
        $result = $stmt->fetch();
        
        return $result ?: null;
    }
}
