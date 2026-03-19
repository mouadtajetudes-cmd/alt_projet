<?php

namespace alt\infra\repositories;

use alt\core\domain\entities\Avatar;
use alt\core\application\ports\spi\repositoryInterfaces\AvatarRepositoryInterface;
use PDO;

class PdoAvatarRepository implements AvatarRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $sql = '
            SELECT DISTINCT
                a.id_avatar,
                a.nom,
                a.image,
                a.created_at
            FROM avatars a
            WHERE a.id_utilisateur = 0
            ORDER BY a.id_avatar
        ';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findById(int $avatarId): ?array
    {
        $sql = '
            SELECT 
                a.id_avatar,
                a.nom,
                a.image,
                a.id_utilisateur,
                a.created_at
            FROM avatars a
            WHERE a.id_avatar = :id_avatar AND a.id_utilisateur = 0
        ';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_avatar' => $avatarId]);
        $avatar = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$avatar) {
            return null;
        }
        
        $sqlVersions = '
            SELECT 
                av.id_avatar_version,
                av.surnom,
                av.level,
                n.id_niveau,
                n.nom as niveau_nom,
                n.description as niveau_description,
                n.points as points_requis
            FROM avatars_versions av
            JOIN niveaux n ON av.id_niveau = n.id_niveau
            WHERE av.id_avatar = :id_avatar
            ORDER BY av.level
        ';
        $stmtVersions = $this->pdo->prepare($sqlVersions);
        $stmtVersions->execute(['id_avatar' => $avatarId]);
        $avatar['versions'] = $stmtVersions->fetchAll(PDO::FETCH_ASSOC);
        
        return $avatar;
    }

    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM avatars WHERE id_utilisateur = :id_utilisateur');
        $stmt->execute(['id_utilisateur' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows ?: null;
    }

    public function create(Avatar $avatar): array
    {
        $stmt = $this->pdo->prepare('INSERT INTO avatars (nom, image, id_utilisateur) VALUES (:nom, :image, :id_utilisateur)');
        $stmt->execute([
            'nom' => $avatar->getNom(),
            'image' => $avatar->getImage(),
            'id_utilisateur' => $avatar->getIdUtilisateur()
        ]);
        return [
            'id_avatar' => (int)$this->pdo->lastInsertId(),
            'nom' => $avatar->getNom(),
            'image' => $avatar->getImage(),
            'id_utilisateur' => $avatar->getIdUtilisateur()
        ];
    }

    public function update(int $id_avatar, Avatar $avatar): bool
    {
        $stmt = $this->pdo->prepare('UPDATE avatars SET nom = :nom, image = :image WHERE id_avatar = :id_avatar');
        return $stmt->execute([
            'id_avatar' => $id_avatar,
            'nom' => $avatar->getNom(),
            'image' => $avatar->getImage()
        ]);
    }
    
    public function createSimple(string $nom, string $image, int $id_utilisateur): array
    {
        $stmt = $this->pdo->prepare('INSERT INTO avatars (nom, image, id_utilisateur) VALUES (:nom, :image, :id_utilisateur)');
        $stmt->execute([
            'nom' => $nom,
            'image' => $image,
            'id_utilisateur' => $id_utilisateur
        ]);
        return [
            'id_avatar' => (int)$this->pdo->lastInsertId(),
            'nom' => $nom,
            'image' => $image,
            'id_utilisateur' => $id_utilisateur
        ];
    }
}
