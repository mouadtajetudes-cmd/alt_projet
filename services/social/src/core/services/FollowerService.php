<?php
namespace alt\core\services;

use alt\core\application\ports\api\FollowerServiceInterface;
use alt\infra\repositories\PdoFollowerRepository;

class FollowerService implements FollowerServiceInterface{
    private  PdoFollowerRepository $followerRepository;
    public function __construct(PdoFollowerRepository $followerRepository) {
        $this->followerRepository = $followerRepository;
    }
    public function createFollow(int $followerId, int $followingId): bool
    {
        return $this->followerRepository->follow($followerId, $followingId);
    }
    public function deleteFollow(int $followerId, int $followingId): bool
    {
        return $this->followerRepository->unfollow($followerId, $followingId);
    }
    public function isFollowing(int $followerId, int $followingId): bool
    {
        return $this->followerRepository->isFollowing($followerId, $followingId);
    }
     public function getFollowers(int $userId): array
     {
         return $this->followerRepository->findFollowers($userId);
     }
      public function getFollowing(int $userId): array
     {
         return $this->followerRepository->findFollowing($userId);
     }
}