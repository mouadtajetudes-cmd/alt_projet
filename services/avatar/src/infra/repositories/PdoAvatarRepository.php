<?php

namespace alt\infra\repositories;

use alt\core\repositories\AvatarRepositoryInterface;
use PDO;

class PdoAvatarRepository implements AvatarRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByUserId(string $userId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM avatars WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        
        $result = $stmt->fetch();
        
        return $result ?: null;
    }
}
