<?php

namespace alt\infra\repositories;

use PDO;
use alt\infra\ports\ProductRepositoryInterface;
use alt\core\domain\entities\Product;

class PdoProductRepository implements ProductRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(array $filters = []): array
    {
        $query = 'SELECT 
            p.id_produit, p.nom, p.description, p.prix, p.statut, 
            p.quantite, p.id_categorie, p.id_utilisateur, p.date_publication,
            c.nom AS categorie, m.url AS image_url
          FROM produits p
          LEFT JOIN categories c ON c.id_categorie = p.id_categorie
          LEFT JOIN produit_medias pm ON pm.id_produit = p.id_produit AND pm.ordre = 0
          LEFT JOIN medias m ON m.id_media = pm.id_media
          WHERE 1=1';
        
        $params = [];

        if (isset($filters['search']) && !empty($filters['search'])) {
            $query .= ' AND (p.nom ILIKE :search OR p.description ILIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $categoryId = $filters['categorie'] ?? $filters['category_id'] ?? null;
        if ($categoryId !== null && $categoryId !== '') {
            $query .= ' AND p.id_categorie = :category_id';
            $params['category_id'] = $categoryId;
        }

        $userId = $filters['userId'] ?? $filters['user_id'] ?? null;
        if ($userId !== null && $userId !== '') {
            $query .= ' AND p.id_utilisateur = :user_id';
            $params['user_id'] = (int) $userId;
        }

        if (isset($filters['min_price']) && $filters['min_price'] !== null) {
            $query .= ' AND p.prix >= :min_price';
            $params['min_price'] = $filters['min_price'];
        }

        if (isset($filters['max_price']) && $filters['max_price'] !== null) {
            $query .= ' AND p.prix <= :max_price';
            $params['max_price'] = $filters['max_price'];
        }

        $query .= ' ORDER BY p.date_publication DESC';

        $limit = isset($filters['limit']) ? (int)$filters['limit'] : 12;
        $page = isset($filters['page']) ? (int)$filters['page'] : 1;
        $offset = ($page - 1) * $limit;

        $query .= ' LIMIT :limit OFFSET :offset';
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $stmt = $this->pdo->prepare($query);

        foreach ($params as $key => $value) {
            $type = in_array($key, ['limit', 'offset']) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue(":$key", $value, $type);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
    $stmt = $this->pdo->prepare(
        'SELECT 
            p.id_produit,
            p.nom,
            p.description,
            p.prix,
            p.statut,
            p.quantite,
            p.id_categorie,
            p.id_utilisateur,
            p.date_publication,
            p.created_at,
            p.updated_at,
            c.nom AS categorie,
            m.url AS image_url
         FROM produits p
         LEFT JOIN categories c ON c.id_categorie = p.id_categorie
         LEFT JOIN produit_medias pm ON pm.id_produit = p.id_produit AND pm.ordre = 0
         LEFT JOIN medias m ON m.id_media = pm.id_media
         WHERE p.id_produit = :id'
    );

    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return null;
    }

    $stmtMedias = $this->pdo->prepare(
        'SELECT 
            m.id_media,
            m.titre,
            m.url,
            m.type,
            m.created_at
         FROM produit_medias pm
         INNER JOIN medias m ON m.id_media = pm.id_media
         WHERE pm.id_produit = :id
         ORDER BY pm.ordre ASC, m.id_media ASC'
    );

    $stmtMedias->execute(['id' => $id]);
    $row['medias'] = $stmtMedias->fetchAll(PDO::FETCH_ASSOC);

    return $row;
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
