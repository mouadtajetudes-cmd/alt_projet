<?php
declare(strict_types=1);

namespace alt\api\middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Psr7\Response;

class AdminMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        
        $user = $request->getAttribute('user');
        
        if ($user && is_array($user)) {
            $isAdmin = $user['administrateur'] == 'true';
            if ($isAdmin) {
                return $handler->handle($request);
            }
        }
        
        $response = new Response();
        $response->getBody()->write(json_encode([
            'admin' => $user['administrateur'],
            'error' => 'Forbidden',
            'message' => 'Administrator privileges required'
        ]));
        return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
    }
}
