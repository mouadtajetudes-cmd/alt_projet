<?php

namespace alt\infra\repositories;

use alt\core\application\ports\spi\repositoryInterfaces\UserSelectedAvatarRepositoryInterface;
use PDO;

class PdoUserSelectedAvatarRepository implements UserSelectedAvatarRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByUserId(int $userId): ?array
    {
        $sql = '
            SELECT 
                usa.id_selection,
                usa.id_utilisateur,
                usa.id_avatar_version,
                usa.current_points,
                usa.selected_at,
                usa.updated_at,
                a.id_avatar,
                a.nom as avatar_nom,
                a.image as avatar_image,
                av.surnom as version_nom,
                av.level as niveau_actuel,
                n.id_niveau,
                n.nom as niveau_nom,
                n.description as niveau_description,
                n.points as points_requis_niveau
            FROM user_selected_avatar usa
            JOIN avatars_versions av ON usa.id_avatar_version = av.id_avatar_version
            JOIN avatars a ON av.id_avatar = a.id_avatar
            JOIN niveaux n ON av.id_niveau = n.id_niveau
            WHERE usa.id_utilisateur = :userId
        ';
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['userId' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    public function selectAvatar(int $userId, int $avatarVersionId, int $currentPoints = 0): array
    {
        $sql = '
            INSERT INTO user_selected_avatar (id_utilisateur, id_avatar_version, current_points)
            VALUES (:userId, :versionId, :points)
            RETURNING id_selection, id_utilisateur, id_avatar_version, current_points, selected_at, updated_at
        ';
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'userId' => $userId,
            'versionId' => $avatarVersionId,
            'points' => $currentPoints
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function changeAvatar(int $userId, int $newAvatarVersionId): bool
    {
        $sql = '
            UPDATE user_selected_avatar 
            SET id_avatar_version = :versionId,
                current_points = 0,
                updated_at = NOW()
            WHERE id_utilisateur = :userId
        ';
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'userId' => $userId,
            'versionId' => $newAvatarVersionId
        ]);
    }

    public function updatePoints(int $userId, int $points): bool
    {
        $sql = '
            UPDATE user_selected_avatar 
            SET current_points = :points,
                updated_at = NOW()
            WHERE id_utilisateur = :userId
        ';
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'userId' => $userId,
            'points' => $points
        ]);
    }

    public function levelUp(int $userId, int $newVersionId): bool
    {
        $sql = '
            UPDATE user_selected_avatar 
            SET id_avatar_version = :versionId,
                updated_at = NOW()
            WHERE id_utilisateur = :userId
        ';
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'userId' => $userId,
            'versionId' => $newVersionId
        ]);
    }

    public function hasAvatar(int $userId): bool
    {
        $sql = 'SELECT COUNT(*) as count FROM user_selected_avatar WHERE id_utilisateur = :userId';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['userId' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return ($result['count'] ?? 0) > 0;
    }
}
