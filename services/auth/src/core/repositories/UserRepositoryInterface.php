<?php

namespace alt\core\repositories;

interface UserRepositoryInterface
{
    public function findById(string $userId): ?array;
    public function save(array $userData): bool;
}
