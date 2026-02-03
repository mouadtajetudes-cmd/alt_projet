<?php

namespace alt\core\repositories;

interface AvatarRepositoryInterface
{
    public function findByUserId(string $userId): ?array;

    public function create(array $avatarData): int;

    public function update(int $id_avatar, array $avatarData): bool;
}
