<?php

namespace alt\core\application\ports\spi\repositoryInterfaces;

interface UserSelectedAvatarRepositoryInterface
{
    public function findByUserId(int $userId): ?array;

    public function selectAvatar(int $userId, int $avatarVersionId, int $currentPoints = 0): array;

    public function changeAvatar(int $userId, int $newAvatarVersionId): bool;

    public function updatePoints(int $userId, int $points): bool;

    public function levelUp(int $userId, int $newVersionId): bool;

    public function hasAvatar(int $userId): bool;
}
