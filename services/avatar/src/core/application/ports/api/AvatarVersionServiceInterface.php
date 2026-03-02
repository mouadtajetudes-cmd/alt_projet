<?php

namespace alt\core\application\ports\api;

interface AvatarVersionServiceInterface
{
    public function getVersionsByAvatarId(int $id_avatar): array;
    public function createVersion(array $avatarVersionData): int;
    public function levelUp(LevelUpDTO $dto): bool;
}
