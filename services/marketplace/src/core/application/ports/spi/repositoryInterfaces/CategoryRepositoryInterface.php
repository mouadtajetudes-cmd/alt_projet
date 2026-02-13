<?php

namespace alt\core\application\ports\spi\repositoryInterfaces;

use alt\core\domain\entities\Category;

interface CategoryRepositoryInterface
{
    public function findAll(): array;

    public function findById(int $id): ?Category;

    public function create(Category $category): Category;
}
