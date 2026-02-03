<?php

namespace alt\api\middlewares;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request, 
        RequestHandlerInterface $handler
    ): ResponseInterface {
        
        if (!$request->hasHeader('Authorization')) {
            throw new Exception("Missing authorization header", 401);
        }

        $authHeader = $request->getHeaderLine('Authorization');
        $token = sscanf($authHeader, "Bearer %s")[0] ?? null;
        
        if (!$token) {
            throw new Exception("Invalid authorization format", 401);
        }

        try {
            $request = $request->withAttribute('authenticated', true);
            $request = $request->withAttribute('user_id', 'example-user-id');
            
        } catch (Exception $e) {
            throw new Exception("Authentication failed: " . $e->getMessage(), 401);
        }

        return $handler->handle($request);
    }
}
