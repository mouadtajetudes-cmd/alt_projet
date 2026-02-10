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
        
        // Get requested ID from route
        $route = $request->getAttribute('__route__');
        $requestedId = null;
        if ($route) {
            $args = $route->getArguments();
            $requestedId = isset($args['id']) ? (int)$args['id'] : null;
        }
        
        // Admin can access everything
        if ($user) {
            $isAdmin = is_array($user) ? ($user['administrateur'] ?? false) : ($user->administrateur ?? false);
            if ($isAdmin) {
                return $handler->handle($request);
            }
        }
        
        // User can access their own data
        if ($userId && $requestedId && (int)$userId === $requestedId) {
            return $handler->handle($request);
        }
        
        // Forbidden
        $response = new Response();
        $response->getBody()->write(json_encode([
            'error' => 'Forbidden',
            'message' => 'You can only access your own data'
        ]));
        return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
    }
}
