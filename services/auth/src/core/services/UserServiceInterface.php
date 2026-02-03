<?php

namespace alt\core\services;

interface UserServiceInterface
{
    public function getUserById(string $userId): array;
}
