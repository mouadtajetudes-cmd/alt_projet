<?php

namespace alt\core\repositories;

use alt\core\domain\entities\Follower;

interface FollowerRepositoryInterface{
    public function follow(int $followerId, int $followingId): bool;
    public function unfollow(int $followerId, int $followingId): bool;
     public function isFollowing(int $followerId, int $followingId): bool;
      public function findFollowers(int $userId): array;
      public function findFollowing(int $userId): array;



}
