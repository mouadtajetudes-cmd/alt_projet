<?php

namespace alt\core\application\ports\api;

use alt\core\domain\entities\Category;

interface CategoryServiceInterface
{
    public function getAllCategories(): array;

    public function getCategoryById(int $id): Category;

    public function createCategory(CreateCategoryDTO $dto): Category;
}
