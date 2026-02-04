<?php
declare(strict_types=1);

namespace alt\api\middlewares;

use alt\core\application\ports\api\provider\AuthProviderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Psr7\Response;

class AuthMiddleware implements MiddlewareInterface
{
    private AuthProviderInterface $authProvider;

    public function __construct(AuthProviderInterface $authProvider)
    {
        $this->authProvider = $authProvider;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        
        if (!$request->hasHeader('Authorization')) {
            return $this->unauthorizedResponse('Missing authorization header');
        }

        $authHeader = $request->getHeaderLine('Authorization');
        $token = sscanf($authHeader, "Bearer %s")[0] ?? null;
        
        if (!$token) {
            return $this->unauthorizedResponse('Invalid authorization format');
        }

        $payload = $this->authProvider->validateToken($token);
        
        if (!$payload) {
            return $this->unauthorizedResponse('Invalid or expired token');
        }

        // Add user data to request attributes
        $request = $request->withAttribute('authenticated', true);
        $request = $request->withAttribute('user_id', $payload['sub'] ?? null);
        $request = $request->withAttribute('user', $payload['user'] ?? null);

        return $handler->handle($request);
    }

    private function unauthorizedResponse(string $message): ResponseInterface
    {
        $response = new Response();
        $response->getBody()->write(json_encode([
            'error' => 'Unauthorized',
            'message' => $message
        ]));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }
}
