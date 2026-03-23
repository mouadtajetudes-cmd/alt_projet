<?php

namespace alt\core\application\ports\spi\repositoryInterfaces;

interface LevelRepositoryInterface
{
    public function findAll(): array;
    public function findById(int $id_niveau): ?array;
}
