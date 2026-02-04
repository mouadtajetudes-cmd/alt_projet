<?php
declare(strict_types=1);

namespace alt\infra\repositories;

use alt\core\application\ports\spi\repositoryInterfaces\GroupRepositoryInterface;
use alt\core\domain\entities\Group;
use PDO;

class PdoGroupRepository implements GroupRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM groupes ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_CLASS, Group::class);
    }

    public function findById(int $id): Group
    {
        $stmt = $this->pdo->prepare('SELECT * FROM groupes WHERE id_groupe = ?');
        $stmt->execute([$id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Group::class);
        return $stmt->fetch();
    }

    public function create(Group $group): Group
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO groupes (nom, description, niveau)
            VALUES (?, ?, ?)
            RETURNING *
        ');
        
        $stmt->execute([
            $group->nom,
            $group->description,
            $group->niveau
        ]);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS, Group::class);
        return $stmt->fetch();
    }

    public function addMember(int $groupId, int $userId): bool
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO membre_groupe (id_groupe, id_utilisateur, role)
            VALUES (?, ?, ?)
        ');
        
        return $stmt->execute([$groupId, $userId, 'member']);
    }

    public function getMembers(int $groupId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT u.* 
            FROM utilisateurs u
            INNER JOIN membre_groupe mg ON u.id_utilisateur = mg.id_utilisateur
            WHERE mg.id_groupe = ?
        ');
        
        $stmt->execute([$groupId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
