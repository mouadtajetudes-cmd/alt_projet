<?php

namespace alt\api\actions;

use alt\core\application\ports\api\CategoryServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetCategoriesAction
{
    private CategoryServiceInterface $categoryService;

    public function __construct(CategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $categories = $this->categoryService->getAllCategories();
        $data = array_map(fn($c) => $c->toArray(), $categories);

        $response->getBody()->write(json_encode([
            'status' => 'success',
            'data' => $data,
            'count' => count($data)
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
