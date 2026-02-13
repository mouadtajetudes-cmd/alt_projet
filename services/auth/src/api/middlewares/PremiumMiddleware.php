<?php
declare(strict_types=1);

namespace alt\api\middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Psr7\Response;

class PremiumMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        
        $user = $request->getAttribute('user');
        
        if ($user && is_array($user)) {
            if ($user['premium'] === 'true') {
                return $handler->handle($request);
            }
        }
        
        $response = new Response();
        $response->getBody()->write(json_encode([
            'error' => 'Forbidden',
            'message' => 'Premium membership required'
        ]));
        return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
    }
}
