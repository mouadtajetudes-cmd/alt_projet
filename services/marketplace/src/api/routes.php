<?php
declare(strict_types=1);

use alt\api\actions\GetAllProductsAction;
use alt\api\actions\GetProductByIdAction;
use alt\api\actions\CreateProductAction;
use alt\api\actions\UpdateProductAction;
use alt\api\actions\GetCategoriesAction;
use alt\api\actions\CreateCategoryAction;
use alt\api\actions\UploadMediaAction;
use alt\api\actions\DeleteProductAction;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

return function (\Slim\App $app) {
    $app->get('/', function (ServerRequestInterface $_request, ResponseInterface $response) {
        $_request->getMethod(); 
        
        $response->getBody()->write(json_encode([
            'service' => 'alt-marketplace',
            'status' => 'running',
            'version' => '1.0.0'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/categories', GetCategoriesAction::class);
    $app->post('/categories', CreateCategoryAction::class);

    $app->post('/upload', UploadMediaAction::class);

    $app->get('/products', GetAllProductsAction::class);
    $app->get('/products/{id}', GetProductByIdAction::class);
    $app->post('/products', CreateProductAction::class);
    $app->put('/products/{id}', UpdateProductAction::class);
    $app->delete('/products/{id}', DeleteProductAction::class);

    return $app;
};
