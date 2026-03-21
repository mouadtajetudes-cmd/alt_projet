<?php
namespace alt\api\middlewares;

use alt\api\provider\jwt\JwtManagerInterface;
use Exception;
use Psr\Http\Message\ServerRequestInterface ;
use Psr\Http\Message\ResponseInterface ;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface
{
    private JwtManagerInterface $jwtManager;

    public function __construct(JwtManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$request->hasHeader('Authorization')) {
            throw new Exception("Missing authorization header", 401);
        }

        $authHeader = $request->getHeaderLine('Authorization');
        $token = sscanf($authHeader, "Bearer %s")[0] ?? null;

        if (!$token) {
            throw new Exception("Invalid authorization format", 401);
        }

        try {
            $payloadArray = $this->jwtManager->validate($token);

            if (!isset($payloadArray['userId'])) {
                throw new Exception("Token invalide: id_utilisateur manquant", 401);
            }

            $userId = (int) $payloadArray['userId'];
            $request = $request->withAttribute('id_utilisateur', $userId);

        } catch (\Exception $e) {
            throw new Exception("Authentication failed: " . $e->getMessage(), 401);
        }

        return $handler->handle($request);
    }
}