<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use alt\api\middlewares\CorsMiddleware;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/settings.php');
$builder->addDefinitions(__DIR__ . '/services.php');
$builder->addDefinitions(__DIR__ . '/api.php');
$container = $builder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->add(new CorsMiddleware());

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$settings = $container->get('settings') ?? [];
$errorMw = $app->addErrorMiddleware(
    $settings['displayErrorDetails'] ?? true,
    $settings['logError'] ?? true,
    $settings['logErrorDetails'] ?? true
);
$errorMw->getDefaultErrorHandler()->forceContentType('application/json');

$app->options('/{routes:.+}', function ($request, $response) {
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
});

$app = (require __DIR__ . '/../src/api/routes.php')($app);

return $app;