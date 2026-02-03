<?php

namespace alt\infra\repositories;

use alt\core\repositories\UserRepositoryInterface;
use PDO;

class PdoUserRepository implements UserRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(string $userId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $userId]);
        
        $result = $stmt->fetch();
        
        return $result ?: null;
    }

    public function save(array $userData): bool
    {
        return true;
    }
}
