<?php

namespace alt\core\application\usecases;

use alt\core\application\ports\spi\repositoryInterfaces\CategoryRepositoryInterface;
use alt\core\application\ports\api\CreateCategoryDTO;
use alt\core\application\ports\api\CategoryServiceInterface;
use alt\core\domain\entities\Category;
use Exception;
use DateTimeImmutable;

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
            throw new Exception("Category not found", 404);
        }

        return $category;
    }

    public function createCategory(CreateCategoryDTO $dto): Category
    {
        $category = new Category(
            0,
            $dto->nom,
            $dto->description,
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        return $this->categoryRepository->create($category);
    }
}
