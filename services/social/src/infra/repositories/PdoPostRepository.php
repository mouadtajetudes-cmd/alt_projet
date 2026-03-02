<?php

namespace alt\infra\repositories;

<<<<<<< HEAD
use alt\core\application\ports\api\CreatePostDTO;
use alt\core\domain\entities\Post;
use alt\core\repositories\PostRepositoryInterface;
=======
use alt\core\application\dto\CreatePostDTO;
use alt\core\application\ports\spi\PostRepositoryInterface;
use alt\core\domain\entities\Post;
>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9
use PDO;

class PdoPostRepository implements PostRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

<<<<<<< HEAD
public function findAll(int $page, int $limit): array
{
    $offset = ($page - 1) * $limit;

    $stmt = $this->pdo->prepare(
        'SELECT p.id_post, p.titre, p.description, p.date_publication, u.nom,u.prenom ,p.type
         FROM posts p
         JOIN utilisateurs u ON p.id_utilisateur = u.id_utilisateur
         ORDER BY date_publication DESC
         LIMIT :limit OFFSET :offset'
    );

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($posts as &$post) {

        if (!empty($post['description']) && !str_starts_with($post['description'], 'http')) {

            if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $post['description'])) {
                $post['type'] = 'image';
                $post['description'] = 'http://localhost:6085/uploads/images/' . $post['description'];

            } elseif (preg_match('/\.(mp4|webm|ogg)$/i', $post['description'])) {
                $post['type'] = 'video';
                $post['description'] = 'http://localhost:6085/uploads/videos/' . $post['description'];

            } else {
                $post['type'] = 'text';
            }
        }
    }

    return $posts;
}

=======
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

>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9
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
<<<<<<< HEAD
            'INSERT INTO posts (type, description, id_utilisateur)
             VALUES (:type, :description, :id_utilisateur)'
        );

        $stmt->execute([
            'type' => $post->getType(),
            'description' => $post->getDescription(),
            'id_utilisateur' => $post->getIdUtilisateur()
        ]);

        return new CreatePostDTO(
            $post->getType(),
            $post->getDescription(),
            $post->getIdUtilisateur()
=======
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

>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9
        );
    }

}
