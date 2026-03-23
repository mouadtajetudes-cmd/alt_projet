<?php
namespace alt\core\services;

use alt\core\application\ports\api\CreateLikeDTO;
use alt\core\application\ports\api\LikeServiceInterface;
use alt\core\repositories\LikeRepositoryInterface;
use alt\core\models\Like;
class LikeService implements LikeServiceInterface
{
    private LikeRepositoryInterface $likeRepository;

    public function __construct(LikeRepositoryInterface $likeRepository)
    {
        $this->likeRepository = $likeRepository;
    }

    public function like(CreateLikeDTO $like): bool
    {
        if ($this->likeRepository->hasLiked($like->getIdPost(), $like->getIdUtilisateur())) {
            return false;
        }

        return $this->likeRepository->createLike($like);
    }

    public function unlike(int $id_post, int $id_utilisateur): bool
    {
        if (!$this->likeRepository->hasLiked($id_post, $id_utilisateur)) {
            return false; 
        }

        return $this->likeRepository->deleteLike($id_post, $id_utilisateur);
    }

    public function hasLiked(int $id_post, int $id_utilisateur): bool
    {
        return $this->likeRepository->hasLiked($id_post, $id_utilisateur);
    }

    public function countLikes(int $id_post): int
    {
        return $this->likeRepository->count($id_post);
    }
}