<?php
namespace alt\core\application\ports\api;

interface FollowerServiceInterface{
    public function createFollow(int $followerId, int $followingId): bool;
    public function deleteFollow(int $followerId, int $followingId): bool;
     public function isFollowing(int $followerId, int $followingId): bool;
      public function getFollowers(int $userId): array;
      public function getFollowing(int $userId): array;
}