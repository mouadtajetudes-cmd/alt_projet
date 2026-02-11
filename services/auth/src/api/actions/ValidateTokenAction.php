<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\provider\AuthProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ValidateTokenAction
{
    private AuthProviderInterface $authProvider;

    public function __construct(AuthProviderInterface $authProvider)
    {
        $this->authProvider = $authProvider;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');
        $token = sscanf($authHeader, "Bearer %s")[0] ?? null;
        
        if (!$token) {
            $response->getBody()->write(json_encode([
                'valid' => false,
                'error' => 'No token provided'
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $payload = $this->authProvider->validateToken($token);
        
        if (!$payload) {
            $response->getBody()->write(json_encode([
                'valid' => false,
                'error' => 'Invalid or expired token'
            ]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode([
            'valid' => true,
            'user' => $payload['user'] ?? null,
            'user_id' => $payload['sub'] ?? null
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
