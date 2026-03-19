<?php

use alt\api\actions\GetAllProductsAction;
use alt\api\actions\GetProductByIdAction;
use alt\api\actions\CreateProductAction;
use alt\api\actions\UpdateProductAction;
use alt\api\actions\GetCategoriesAction;
use alt\api\actions\CreateCategoryAction;
use alt\api\actions\UploadMediaAction;
use alt\api\middlewares\AuthMiddleware;
use alt\core\application\ports\api\ProductServiceInterface;
use alt\core\application\ports\api\CategoryServiceInterface;
use alt\core\application\ports\spi\repositoryInterfaces\MediaRepositoryInterface;

return [
    GetAllProductsAction::class => function ($c) {
        return new GetAllProductsAction(
            $c->get(ProductServiceInterface::class)
        );
    },

    GetProductByIdAction::class => function ($c) {
        return new GetProductByIdAction(
            $c->get(ProductServiceInterface::class)
        );
    },

    CreateProductAction::class => function ($c) {
    return new CreateProductAction();
    },

    UpdateProductAction::class => function ($c) {
        return new UpdateProductAction(
            $c->get(ProductServiceInterface::class)
        );
    },

    GetCategoriesAction::class => function ($c) {
        return new GetCategoriesAction(
            $c->get(CategoryServiceInterface::class)
        );
    },

    CreateCategoryAction::class => function ($c) {
        return new CreateCategoryAction(
            $c->get(CategoryServiceInterface::class)
        );
    },

    UploadMediaAction::class => function ($c) {
        return new UploadMediaAction(
            $c->get(MediaRepositoryInterface::class)
        );
    },
];
