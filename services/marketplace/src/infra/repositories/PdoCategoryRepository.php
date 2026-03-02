<?php

namespace alt\infra\repositories;

use alt\core\application\ports\spi\repositoryInterfaces\CategoryRepositoryInterface;
use alt\core\domain\entities\Category;
use PDO;

class PdoCategoryRepository implements CategoryRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM categories ORDER BY nom ASC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map([Category::class, 'fromArray'], $rows);
    }

    public function findById(int $id): ?Category
    {
        $stmt = $this->pdo->prepare('SELECT * FROM categories WHERE id_categorie = :id');
        $stmt->execute(['id' => $id]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row ? Category::fromArray($row) : null;
    }

    public function create(Category $category): Category
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO categories (nom, description, created_at, updated_at) 
             VALUES (:nom, :description, NOW(), NOW()) 
             RETURNING *'
        );
        
        $stmt->execute([
            'nom' => $category->getNom(),
            'description' => $category->getDescription()
        ]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return Category::fromArray($row);
    }
}
