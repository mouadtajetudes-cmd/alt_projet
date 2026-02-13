<?php

namespace alt\infra\repositories;

use alt\core\application\ports\spi\repositoryInterfaces\MediaRepositoryInterface;
use alt\core\domain\entities\Media;
use PDO;

class PdoMediaRepository implements MediaRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?Media
    {
        $stmt = $this->pdo->prepare('SELECT * FROM medias WHERE id_media = :id');
        $stmt->execute(['id' => $id]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row ? Media::fromArray($row) : null;
    }

    public function create(Media $media): Media
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO medias (titre, url, type, created_at) 
             VALUES (:titre, :url, :type, NOW()) 
             RETURNING *'
        );
        
        $stmt->execute([
            'titre' => $media->getTitre(),
            'url' => $media->getUrl(),
            'type' => $media->getType()
        ]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return Media::fromArray($row);
    }

    public function attachToProduct(int $mediaId, int $productId, int $ordre = 0): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO produit_medias (id_media, id_produit, ordre) 
             VALUES (:media_id, :product_id, :ordre)
             ON CONFLICT (id_media, id_produit) DO UPDATE SET ordre = :ordre'
        );
        
        return $stmt->execute([
            'media_id' => $mediaId,
            'product_id' => $productId,
            'ordre' => $ordre
        ]);
    }

    public function detachFromProduct(int $mediaId, int $productId): bool
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM produit_medias WHERE id_media = :media_id AND id_produit = :product_id'
        );
        
        return $stmt->execute([
            'media_id' => $mediaId,
            'product_id' => $productId
        ]);
    }

    public function findByProductId(int $productId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT m.* FROM medias m
             INNER JOIN produit_medias pm ON m.id_media = pm.id_media
             WHERE pm.id_produit = :product_id
             ORDER BY pm.ordre ASC'
        );
        
        $stmt->execute(['product_id' => $productId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map([Media::class, 'fromArray'], $rows);
    }
}
