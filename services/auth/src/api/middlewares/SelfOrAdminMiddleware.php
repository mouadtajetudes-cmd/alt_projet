<?php
declare(strict_types=1);

namespace alt\api\middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Psr7\Response;

class SelfOrAdminMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        
        $user = $request->getAttribute('user');
        $userId = $request->getAttribute('user_id');
        
        $route = $request->getAttribute('__route__');
        $requestedId = null;
        if ($route) {
            $args = $route->getArguments();
            $requestedId = isset($args['id']) ? (int)$args['id'] : null;
        }
        
        if ($user && is_array($user)) {
            if ($user['administrateur'] === 'true') {
                return $handler->handle($request);
            }
        }
        
        if ($userId && $requestedId && (int)$userId === $requestedId) {
            return $handler->handle($request);
        }
        
        $response = new Response();
        $response->getBody()->write(json_encode([
            'error' => 'Forbidden',
            'message' => 'You can only access your own data'
        ]));
        return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
    }
}
