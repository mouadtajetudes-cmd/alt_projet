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
        
        if (!$user || !isset($user['administrateur']) || !$user['administrateur']) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                'error' => 'Forbidden',
                'message' => 'Administrator privileges required'
            ]));
            return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
        }

        return $handler->handle($request);
    }
}
