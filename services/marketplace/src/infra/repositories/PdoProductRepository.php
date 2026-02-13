<?php

namespace alt\infra\repositories;

use alt\core\application\ports\spi\repositoryInterfaces\ProductRepositoryInterface;
use alt\core\domain\entities\Product;
use PDO;

class PdoProductRepository implements ProductRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(array $filters = []): array
    {
        $query = 'SELECT * FROM produits WHERE 1=1';
        $params = [];

        if (isset($filters['categorie'])) {
            $query .= ' AND id_categorie = :categorie';
            $params['categorie'] = $filters['categorie'];
        }

        if (isset($filters['prix_min'])) {
            $query .= ' AND prix >= :prix_min';
            $params['prix_min'] = $filters['prix_min'];
        }

        if (isset($filters['prix_max'])) {
            $query .= ' AND prix <= :prix_max';
            $params['prix_max'] = $filters['prix_max'];
        }

        if (isset($filters['statut'])) {
            $query .= ' AND statut = :statut';
            $params['statut'] = $filters['statut'];
        }

        if (isset($filters['user_id'])) {
            $query .= ' AND id_utilisateur = :user_id';
            $params['user_id'] = $filters['user_id'];
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $query .= ' AND (nom ILIKE :search OR description ILIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $query .= ' ORDER BY date_publication DESC';

        if (isset($filters['limit'])) {
            $query .= ' LIMIT :limit';
            $params['limit'] = (int) $filters['limit'];
        }

        if (isset($filters['offset'])) {
            $query .= ' OFFSET :offset';
            $params['offset'] = (int) $filters['offset'];
        }

        $stmt = $this->pdo->prepare($query);
        
        foreach ($params as $key => $value) {
            if ($key === 'limit' || $key === 'offset') {
                $stmt->bindValue(':' . $key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(':' . $key, $value);
            }
        }
        
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map([Product::class, 'fromArray'], $rows);
    }

    public function findById(int $id): ?Product
    {
        $stmt = $this->pdo->prepare('SELECT * FROM produits WHERE id_produit = :id');
        $stmt->execute(['id' => $id]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row ? Product::fromArray($row) : null;
    }

    public function create(Product $product): Product
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO produits (nom, description, prix, statut, quantite, date_publication, id_utilisateur, id_categorie, created_at, updated_at) 
             VALUES (:nom, :description, :prix, :statut, :quantite, NOW(), :id_utilisateur, :id_categorie, NOW(), NOW()) 
             RETURNING *'
        );
        
        $stmt->execute([
            'nom' => $product->getNom(),
            'description' => $product->getDescription(),
            'prix' => $product->getPrix(),
            'statut' => $product->getStatut(),
            'quantite' => $product->getQuantite(),
            'id_utilisateur' => $product->getIdUtilisateur(),
            'id_categorie' => $product->getIdCategorie()
        ]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return Product::fromArray($row);
    }

    public function update(Product $product): Product
    {
        $stmt = $this->pdo->prepare(
            'UPDATE produits 
             SET nom = :nom, description = :description, prix = :prix, statut = :statut, 
                 quantite = :quantite, id_categorie = :id_categorie, updated_at = NOW() 
             WHERE id_produit = :id 
             RETURNING *'
        );
        
        $stmt->execute([
            'id' => $product->getId(),
            'nom' => $product->getNom(),
            'description' => $product->getDescription(),
            'prix' => $product->getPrix(),
            'statut' => $product->getStatut(),
            'quantite' => $product->getQuantite(),
            'id_categorie' => $product->getIdCategorie()
        ]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return Product::fromArray($row);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM produits WHERE id_produit = :id');
        return $stmt->execute(['id' => $id]);
    }
}
