<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use alt\gateway\middlewares\CorsMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';

$builder = new ContainerBuilder();

$builder->addDefinitions(__DIR__ . '/services.php');

$container = $builder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->add(new CorsMiddleware());
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$app = (require __DIR__ . '/routes.php')($app);



$app->addErrorMiddleware(true, true, true);

return $app;
