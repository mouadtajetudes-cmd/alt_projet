<?php

namespace alt\infra\repositories;

use alt\core\application\ports\api\CreatePostDTO;
use alt\core\domain\entities\Post;
use alt\core\repositories\PostRepositoryInterface;
use PDO;

class PdoPostRepository implements PostRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

public function findAll(int $page, int $limit): array
{
    $offset = ($page - 1) * $limit;

    $stmt = $this->pdo->prepare(
        'SELECT p.id_post,
                p.description,
                p.date_publication,
                u.id_utilisateur,
                u.nom,
                u.prenom,
                u.banner_url,
                m.id_media,
                m.titre,
                m.url AS media_url,
                m.type AS media_type
         FROM posts p
         JOIN utilisateurs u ON p.id_utilisateur = u.id_utilisateur
         LEFT JOIN post_medias pm ON p.id_post = pm.id_post
         LEFT JOIN medias m ON pm.id_media = m.id_media
         ORDER BY p.date_publication DESC
         LIMIT :limit OFFSET :offset'
    );

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as &$row) {

        if ($row['media_type'] === 'image') {
            $row['media_url'] = 'http://localhost:6085/uploads/images/' . $row['titre'];
        }

        if ($row['media_type'] === 'video') {
            $row['media_url'] = 'http://localhost:6085/uploads/videos/' . $row['titre'];
        }
    }

    return $rows;
}    public function findById(int $idPost): Post
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_post, titre, description, date_publication, id_utilisateur
             FROM posts
             WHERE id_post = :id'
        );

        $stmt->execute(['id' => $idPost]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new \RuntimeException('Post introuvable');
        }

        return new Post(
        (int) $row['id_post'],
        $row['titre'],
        $row['description'],
        $row['date_publication'],
         $row['id_utilisateur']
    );
    }

    public function findByIdWithStats(int $idPost): array
    {
        $post = $this->findById($idPost);

        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM reactions WHERE id_post = :id'
        );
        $stmt->execute(['id' => $idPost]);
        $reactions = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM commentaires WHERE id_post = :id'
        );
        $stmt->execute(['id' => $idPost]);
        $comments =$stmt->fetchColumn();

        return [
            'post' => $post,
            'stats' => [
                'reactions' => $reactions,
                'comments' => $comments,
            ],
        ];
    }

public function create(CreatePostDTO $postDTO, ?array $file = null): Post
{
    try {
        $this->pdo->beginTransaction();

        // --- Insert post ---
        $stmt = $this->pdo->prepare(
            'INSERT INTO posts (description, id_utilisateur)
             VALUES (:description, :id_utilisateur)
             RETURNING id_post, description, id_utilisateur'
        );
        $stmt->execute([
            'description' => $postDTO->getDescription(),
            'id_utilisateur' => $postDTO->getIdUtilisateur()
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $postId = (int) $row['id_post'];

        $titre = null;
        $mediaType = null;
        $mediaUrl = null;

        if ($file !== null) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO medias (titre, type)
                 VALUES (:titre, :type)
                 RETURNING id_media, titre, type'
            );
            $stmt->execute([
                'titre' => $file['name'],
                'type' => $file['type']
            ]);
            $mediaRow = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $this->pdo->prepare(
                'INSERT INTO post_medias (id_post, id_media)
                 VALUES (:post, :media)'
            );
            $stmt->execute([
                'post' => $postId,
                'media' => $mediaRow['id_media']
            ]);

            $titre = $mediaRow['titre'];
            $mediaType = $mediaRow['type'];
           $mediaUrl = isset($file['folder'], $file['name'])
                ? "http://localhost:6085/uploads/{$file['folder']}/{$file['name']}"
                : null;
        }

        $this->pdo->commit();

        return new Post(
            $postId,                 
            $row['description'],       
            (int)$row['id_utilisateur'], 
            $titre,                   
            $mediaType,
            $mediaUrl              
        );
    } catch (\Exception $e) {
        $this->pdo->rollBack();
        throw $e;
    }
}   public function findByUserPosts(int $idUser): array
{
    $sql = "SELECT 
                p.*, 
                m.url AS media_url,
                m.type AS media_type,
                m.titre,
                u.id_utilisateur,
                u.nom,
                u.prenom,
                u.id_avatar,
                u.banner_url,
                (SELECT COUNT(*) FROM likes l WHERE l.id_post = p.id_post) AS likes_count
            FROM posts p
            JOIN utilisateurs u ON p.id_utilisateur = u.id_utilisateur
            LEFT JOIN post_medias pm ON p.id_post = pm.id_post
            LEFT JOIN medias m ON pm.id_media = m.id_media      
            WHERE p.id_utilisateur = :idUser
            ORDER BY p.date_publication DESC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as &$row) {

        if ($row['media_type'] === 'image') {
            $row['media_url'] = 'http://localhost:6085/uploads/images/' . $row['titre'];
        }

        if ($row['media_type'] === 'video') {
            $row['media_url'] = 'http://localhost:6085/uploads/videos/' . $row['titre'];
        }
    }

    return $rows;
}
public function delete(int $idPost): bool
{
    $stmt = $this->pdo->prepare(
        'DELETE FROM posts WHERE id_post = :id'
    );

    $stmt->execute(['id' => $idPost]);

    return $stmt->rowCount() > 0;
}
}
