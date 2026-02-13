<?php
declare(strict_types=1);

use alt\api\actions\GetAllProductsAction;
use alt\api\actions\GetProductByIdAction;
use alt\api\actions\CreateProductAction;
use alt\api\actions\UpdateProductAction;
use alt\api\actions\GetCategoriesAction;
use alt\api\actions\CreateCategoryAction;

return function(\Slim\App $app): \Slim\App {

    $app->get('/', function ($request, $response) {
        $response->getBody()->write(json_encode([
            'service' => 'alt-marketplace',
            'status' => 'running',
            'version' => '1.0.0'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/categories', GetCategoriesAction::class);
    $app->post('/categories', CreateCategoryAction::class);

    $app->get('/products', GetAllProductsAction::class);
    $app->get('/products/{id}', GetProductByIdAction::class);
    $app->post('/products', CreateProductAction::class);
    $app->put('/products/{id}', UpdateProductAction::class);

    return $app;
};
