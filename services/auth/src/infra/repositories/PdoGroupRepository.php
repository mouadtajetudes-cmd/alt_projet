<?php
declare(strict_types=1);

namespace alt\infra\repositories;

use alt\core\application\ports\spi\repositoryInterfaces\GroupRepositoryInterface;
use alt\core\domain\entities\Group;
use PDO;

class PdoGroupRepository implements GroupRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

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

    public function update(Group $group): Group
    {
        $stmt = $this->pdo->prepare('
            UPDATE groupes 
            SET nom = ?, description = ?, niveau = ?
            WHERE id_groupe = ?
            RETURNING *
        ');
        
        $stmt->execute([
            $group->nom,
            $group->description,
            $group->niveau,
            $group->id_groupe
        ]);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS, Group::class);
        return $stmt->fetch();
    }

    public function addMember(int $groupId, int $userId, string $role = 'member'): bool
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO membre_groupe (id_groupe, id_utilisateur, role)
            VALUES (?, ?, ?)
        ');
        
        return $stmt->execute([$groupId, $userId, $role]);
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

    public function getUserGroups(int $userId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT g.* 
            FROM groupes g
            INNER JOIN membre_groupe mg ON g.id_groupe = mg.id_groupe
            WHERE mg.id_utilisateur = ?
        ');
        
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGroupMembers(int $groupId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT u.*, mg.role, mg.joined_at
            FROM utilisateurs u
            INNER JOIN membre_groupe mg ON u.id_utilisateur = mg.id_utilisateur
            WHERE mg.id_groupe = ?
        ');
        
        $stmt->execute([$groupId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function removeMember(int $groupId, int $userId): bool
    {
        $stmt = $this->pdo->prepare('
            DELETE FROM membre_groupe
            WHERE id_groupe = ? AND id_utilisateur = ?
        ');
        
        return $stmt->execute([$groupId, $userId]);
    }

    public function getMemberRole(int $groupId, int $userId): ?string
    {
        $stmt = $this->pdo->prepare('
            SELECT role FROM membre_groupe
            WHERE id_groupe = ? AND id_utilisateur = ?
        ');
        
        $stmt->execute([$groupId, $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['role'] : null;
    }
}
