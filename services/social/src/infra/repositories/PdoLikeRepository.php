<?php
namespace alt\infra\repositories;

use alt\core\repositories\LikeRepositoryInterface;
use PDO;
use alt\core\application\ports\api\CreateLikeDTO;

class PdoLikeRepository implements LikeRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createLike(CreateLikeDTO $like): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO likes (id_post, id_utilisateur) VALUES (:id_post, :id_utilisateur)"
            );
            return $stmt->execute([
                'id_post' => $like->getIdPost(),
                'id_utilisateur' => $like->getIdUtilisateur()
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function deleteLike(int $id_post, int $id_utilisateur): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM likes WHERE id_post = :id_post AND id_utilisateur = :id_utilisateur"
        );
        return $stmt->execute([
            'id_post' => $id_post,
            'id_utilisateur' => $id_utilisateur
        ]);
    }

    public function hasLiked(int $id_post, int $id_utilisateur): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM likes WHERE id_post = :id_post AND id_utilisateur = :id_utilisateur"
        );
        $stmt->execute([
            'id_post' => $id_post,
            'id_utilisateur' => $id_utilisateur
        ]);
        return $stmt->fetchColumn() > 0;
    }

    public function count(int $id_post): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM likes WHERE id_post = :id_post"
        );
        $stmt->execute(['id_post' => $id_post]);
        return (int)$stmt->fetchColumn();
    }
}