<?php

namespace alt\core\repositories;

interface LevelRepositoryInterface
{
    public function findAll(): array;
    public function findById(int $id_niveau): ?array;
}
