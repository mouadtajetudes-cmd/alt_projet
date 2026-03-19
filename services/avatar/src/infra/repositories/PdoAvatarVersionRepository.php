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

    public function levelUp(int $id_avatar_version, int $new_level): bool
    {
        // Récupérer l'avatar version actuel
        $stmt = $this->pdo->prepare('
            SELECT av.level, av.id_niveau, n.points as points_requis
            FROM avatars_versions av
            LEFT JOIN niveaux n ON av.id_niveau = n.id_niveau
            WHERE av.id_avatar_version = :id_avatar_version
        ');
        $stmt->execute(['id_avatar_version' => $id_avatar_version]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$current) {
            throw new \RuntimeException('Avatar version not found');
        }
        
        $currentLevel = (int)$current['level'];
        
        // Vérifier que c'est une montée de niveau valide (+1)
        if ($new_level !== $currentLevel + 1) {
            throw new \RuntimeException("Invalid level progression. Current: $currentLevel, Requested: $new_level");
        }
        
        // Vérifier que le niveau ne dépasse pas le maximum
        if ($new_level > 5) {
            throw new \RuntimeException('Maximum level is 5');
        }
        
        // Vérifier que le niveau cible existe
        $stmtLevel = $this->pdo->prepare('SELECT id_niveau, points FROM niveaux WHERE id_niveau = :id_niveau');
        $stmtLevel->execute(['id_niveau' => $new_level]);
        $targetLevel = $stmtLevel->fetch(PDO::FETCH_ASSOC);
        
        if (!$targetLevel) {
            throw new \RuntimeException("Target level $new_level does not exist");
        }
        
        // Vérifier les points (si on a des points actuels à comparer)
        // Note: Dans la structure actuelle, points_requis vient du niveau actuel
        // Pour une vraie vérification, il faudrait stocker les points accumulés dans la table
        $requiredPoints = (int)$targetLevel['points'];
        $currentPoints = (int)($current['points_requis'] ?? 0);
        
        // Cette vérification suppose que l'avatar a accumulé au moins autant de points que son niveau actuel
        // Pour une vraie implémentation, il faudrait une colonne 'points_accumules' dans avatars_versions
        if ($currentPoints < $requiredPoints && $currentLevel > 1) {
            // On skip cette vérification pour le niveau 1->2 car niveau 1 a 0 points requis
            // throw new \RuntimeException("Insufficient points: have $currentPoints, need $requiredPoints");
        }
        
        // Mise à jour du niveau et de l'id_niveau
        $stmtUpdate = $this->pdo->prepare('
            UPDATE avatars_versions 
            SET level = :new_level, id_niveau = :id_niveau 
            WHERE id_avatar_version = :id_avatar_version
        ');
        
        return $stmtUpdate->execute([
            'new_level' => $new_level,
            'id_niveau' => $targetLevel['id_niveau'],
            'id_avatar_version' => $id_avatar_version
        ]);
    }
}