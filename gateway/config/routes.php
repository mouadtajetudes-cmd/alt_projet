<?php
use Slim\App;
use alt\gateway\actions\RoutedProxyAction;
use alt\gateway\middlewares\AuthMiddleware;
use Psr\Container\ContainerInterface;

return function (App $app): App {
    $container = $app->getContainer();

    $app->get('/', function ($request, $response, $args) {
        $response->getBody()->write(json_encode(['service' => 'gateway', 'status' => 'running']));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->any('/auth[/{params:.*}]', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'auth.client', '/auth');
        return $action($request, $response, $args);
    });

    $app->any('/avatar[/{params:.*}]', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'avatar.client', '/avatar');
        return $action($request, $response, $args);
    });

    $app->any('/chat[/{params:.*}]', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'chat.client', '/chat');
        return $action($request, $response, $args);
    });

    $app->any('/marketplace[/{params:.*}]', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'marketplace.client', '/marketplace');
        return $action($request, $response, $args);
    });

    $app->any('/social[/{params:.*}]', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'social.client', '/social');
        return $action($request, $response, $args);
    });

    return $app;
};
