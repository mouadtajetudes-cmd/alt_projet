<?php

namespace alt\core\repositories;

interface AvatarRepositoryInterface
{
    public function findByUserId(string $userId): ?array;
}
