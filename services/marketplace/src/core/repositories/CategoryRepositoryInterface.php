<?php

namespace alt\core\repositories;

use alt\core\domain\entities\Category;

interface CategoryRepositoryInterface
{
    public function findAll(): array;

    public function findById(int $id): ?Category;

    public function create(Category $category): Category;
}
