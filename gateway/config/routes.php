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

    $app->get('/images/products/{filename}', function ($request, $response, $args) {
        $filename = $args['filename'];
        $filepath = __DIR__ . '/../images/products/' . $filename;
        
        if (!file_exists($filepath)) {
            $response->getBody()->write(json_encode(['error' => 'Image non trouvée']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filepath);
        finfo_close($finfo);
        
        $stream = fopen($filepath, 'r');
        return $response
            ->withBody(new \Slim\Psr7\Stream($stream))
            ->withHeader('Content-Type', $mimeType)
            ->withHeader('Cache-Control', 'public, max-age=31536000');
    });

    $app->any('/auth[/{params:.*}]', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'auth.client', '');
        return $action($request, $response, $args);
    });

    $app->any('/avatar[/{params:.*}]', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'avatar.client', '');
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
