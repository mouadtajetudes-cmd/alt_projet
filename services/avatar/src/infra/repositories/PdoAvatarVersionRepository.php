<?php

namespace alt\infra\repositories;

use alt\core\application\ports\spi\repositoryInterfaces\AvatarVersionRepositoryInterface;
use PDO;

class PdoAvatarVersionRepository implements AvatarVersionRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByAvatarId(int $id_avatar): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM avatars_versions WHERE id_avatar = :id_avatar');
        $stmt->execute(['id_avatar' => $id_avatar]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows ?: null;
    }

    public function create(array $avatarVersionData): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) VALUES (:surnom, :level, :id_avatar, :id_niveau)');
        $stmt->execute([
            'surnom' => $avatarVersionData['surnom'] ?? null,
            'level' => $avatarVersionData['level'] ?? 1,
            'id_avatar' => $avatarVersionData['id_avatar'],
            'id_niveau' => $avatarVersionData['id_niveau']
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function levelUp(int $id_avatar_version): bool
    {
        $stmt = $this->pdo->prepare('UPDATE avatars_versions SET level = level + 1 WHERE id_avatar_version = :id_avatar_version');
        return $stmt->execute(['id_avatar_version' => $id_avatar_version]);
    }
}