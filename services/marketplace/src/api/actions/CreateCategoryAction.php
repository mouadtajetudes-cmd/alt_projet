<?php

namespace alt\api\actions;

use alt\core\application\ports\api\CategoryServiceInterface;
use alt\core\application\ports\api\CreateCategoryDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Exception;

class CreateCategoryAction
{
    private CategoryServiceInterface $categoryService;

    public function __construct(CategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $data = $request->getParsedBody();

            $dto = new CreateCategoryDTO(
                $data['nom'],
                $data['description'] ?? ''
            );

            $category = $this->categoryService->createCategory($dto);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $category->toArray()
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
    }
}
