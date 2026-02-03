<?php

namespace alt\core\services;

interface AvatarServiceInterface
{
    public function getAvatarByUserId(string $userId): array;
}
