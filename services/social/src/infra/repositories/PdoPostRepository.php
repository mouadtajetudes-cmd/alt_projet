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
                p.titre AS post_titre,
                p.description,
                p.date_publication,
                u.nom,
                u.prenom,
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
        if ($row['media_type'] === null) {
            $row['media_type'] = 'text';
            $row['titre'] = $row['post_titre'];
            $row['media_url'] = null;
        }

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

    public function create(CreatePostDTO $post):CreatePostDTO
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO posts (titre, description, id_utilisateur)
             VALUES (:titre, :description, :id_utilisateur)'
        );

        $description = $post->getDescription();
        $titre = $description !== null && trim($description) !== ''
            ? trim($description)
            : 'Nouveau post';

        $stmt->execute([
            'titre' => mb_substr($titre, 0, 255),
            'description' => $description,
            'id_utilisateur' => $post->getIdUtilisateur()
        ]);

        return new CreatePostDTO(
            $post->getType(),
            $post->getDescription(),
            $post->getIdUtilisateur()
        );
    }

}
