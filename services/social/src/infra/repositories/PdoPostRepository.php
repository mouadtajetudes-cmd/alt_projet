<?php

namespace alt\infra\repositories;

use alt\core\application\dto\CreatePostDTO;
use alt\core\application\ports\spi\PostRepositoryInterface;
use alt\core\domain\entities\Post;
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
            'SELECT id_post, titre, description, date_publication, id_utilisateur
             FROM posts
             ORDER BY date_publication DESC
             LIMIT :limit OFFSET :offset'
        );

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC) ;

        return $posts;
    }

    public function findById(int $idPost): Post
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
            'INSERT INTO posts (titre, description,  id_utilisateur)
             VALUES (:titre, :description, :user)'
        );

        $stmt->execute([
            'titre' => $post->getTitre(),
            'description' => $post->getDescription(),
            'user' => $post->getIdUtilisateur(),
        ]);

        return new CreatePostDTO(
            $post->getTitre(),
            $post->getDescription(),
            $post->getIdUtilisateur()

        );
    }

}
