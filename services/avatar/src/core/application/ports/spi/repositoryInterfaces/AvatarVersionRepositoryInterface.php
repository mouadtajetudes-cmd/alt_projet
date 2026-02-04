<?php

namespace alt\core\application\ports\spi\repositoryInterfaces;

interface AvatarVersionRepositoryInterface
{
    public function findByAvatarId(int $id_avatar): ?array;
    public function create(array $avatarVersionData): int;
    public function levelUp(int $id_avatar_version): bool;
}
