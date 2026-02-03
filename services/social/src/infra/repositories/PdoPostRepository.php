<?php

namespace alt\infra\repositories;

use alt\core\repositories\PostRepositoryInterface;
use PDO;

class PdoPostRepository implements PostRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(string $postId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM posts WHERE id = :id');
        $stmt->execute(['id' => $postId]);
        
        $result = $stmt->fetch();
        
        return $result ?: null;
    }
}
