<?php

namespace alt\core\services;

use alt\core\repositories\CategoryRepositoryInterface;
use alt\core\domain\dto\CreateCategoryDTO;
use alt\core\domain\entities\Category;

class CategoryService implements CategoryServiceInterface
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories(): array
    {
        return $this->categoryRepository->findAll();
    }

    public function getCategoryById(int $id): Category
    {
        $category = $this->categoryRepository->findById($id);
        
        if (!$category) {
            throw new \Exception("Category not found", 404);
        }

        return $category;
    }

    public function createCategory(CreateCategoryDTO $dto): Category
    {
        $category = new Category(
            null,
            $dto->nom,
            $dto->description,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );

        return $this->categoryRepository->create($category);
    }
}
