<?php
declare(strict_types=1);

namespace alt\infra\repositories;

use alt\core\application\ports\spi\repositoryInterfaces\AdRepositoryInterface;
use alt\core\domain\entities\Ad;
use PDO;

class PdoAdRepository implements AdRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM publicites ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_CLASS, Ad::class);
    }

    public function findById(int $id): Ad
    {
        $stmt = $this->pdo->prepare('SELECT * FROM publicites WHERE id_publicite = ?');
        $stmt->execute([$id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Ad::class);
        return $stmt->fetch();
    }

    public function create(Ad $ad): Ad
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO publicites (titre, description, image, lien, actif)
            VALUES (?, ?, ?, ?, ?)
            RETURNING *
        ');
        
        $stmt->execute([
            $ad->titre,
            $ad->description,
            $ad->image,
            $ad->lien,
            $ad->actif
        ]);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS, Ad::class);
        return $stmt->fetch();
    }

    public function update(int $id, array $data): Ad
    {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        
        $values[] = $id;
        
        $sql = 'UPDATE publicites SET ' . implode(', ', $fields) . ' WHERE id_publicite = ? RETURNING *';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS, Ad::class);
        return $stmt->fetch();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM publicites WHERE id_publicite = ?');
        return $stmt->execute([$id]);
    }
}
