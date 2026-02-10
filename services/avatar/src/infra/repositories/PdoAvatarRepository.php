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
            SELECT 
                a.id_avatar,
                a.nom,
                a.image,
                a.id_utilisateur,
                COALESCE(av.level, 1) as niveau,
                COALESCE(n.points, 0) as points
            FROM avatars a
            LEFT JOIN avatars_versions av ON a.id_avatar = av.id_avatar
            LEFT JOIN niveaux n ON av.id_niveau = n.id_niveau
            ORDER BY a.id_avatar
        ';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
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
}
