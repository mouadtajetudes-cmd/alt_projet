<?php
namespace alt\infra\repositories;

use alt\core\repositories\FollowerRepositoryInterface;
use PDO;

class PdoFollowerRepository implements FollowerRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function follow(int $followerId, int $followingId): bool
    {
        if ($followerId === $followingId) {
            return false; 
        }

        $stmt = $this->pdo->prepare(
            "SELECT 1 FROM followers WHERE follower_id = :follower AND following_id = :following"
        );
        $stmt->execute([
            'follower' => $followerId,
            'following' => $followingId
        ]);

        if ($stmt->fetch()) return false; 

        $stmt = $this->pdo->prepare(
            "INSERT INTO followers (follower_id, following_id) VALUES (:follower, :following)"
        );

        return $stmt->execute([
            'follower' => $followerId,
            'following' => $followingId
        ]);
    }

    public function unfollow(int $followerId, int $followingId): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM followers WHERE follower_id = :follower AND following_id = :following"
        );

        return $stmt->execute([
            'follower' => $followerId,
            'following' => $followingId
        ]);
    }

    public function isFollowing(int $followerId, int $followingId): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT 1 FROM followers WHERE follower_id = :follower AND following_id = :following"
        );

        $stmt->execute([
            'follower' => $followerId,
            'following' => $followingId
        ]);

        return (bool)$stmt->fetch();
    }

    // Récupérer tous les followers d'un utilisateur
    public function findFollowers(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT u.* FROM followers f
             JOIN utilisateurs u ON u.id_utilisateur = f.follower_id
             WHERE f.following_id = :userId"
        );

        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer tous les utilisateurs suivis par un utilisateur
    public function findFollowing(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT u.* FROM followers f
             JOIN utilisateurs u ON u.id_utilisateur = f.following_id
             WHERE f.follower_id = :userId"
        );

        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}