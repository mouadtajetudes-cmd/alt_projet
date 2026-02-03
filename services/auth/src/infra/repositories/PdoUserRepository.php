<?php
declare(strict_types=1);

namespace alt\infra\repositories;

use alt\core\application\ports\spi\repositoryInterfaces\UserRepositoryInterface;
use alt\core\domain\entities\User;
use PDO;

class PdoUserRepository implements UserRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM utilisateurs ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_CLASS, User::class);
    }

    public function findById(int $id): User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM utilisateurs WHERE id_utilisateur = ?');
        $stmt->execute([$id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, User::class);
        return $stmt->fetch();
    }

    public function create(User $user): User
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO utilisateurs (nom, prenom, email, telephone, password, administrateur, premium, points)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            RETURNING *
        ');
        
        $stmt->execute([
            $user->nom,
            $user->prenom,
            $user->email,
            $user->telephone,
            $user->password,
            $user->administrateur,
            $user->premium,
            $user->points
        ]);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS, User::class);
        return $stmt->fetch();
    }

    public function update(int $id, array $data): User
    {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        
        $values[] = $id;
        
        $sql = 'UPDATE utilisateurs SET ' . implode(', ', $fields) . ' WHERE id_utilisateur = ? RETURNING *';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS, User::class);
        return $stmt->fetch();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM utilisateurs WHERE id_utilisateur = ?');
        return $stmt->execute([$id]);
    }
}
