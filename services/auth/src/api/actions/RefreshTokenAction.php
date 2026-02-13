<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\AuthServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RefreshTokenAction
{
    private AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody();
        $refreshToken = $body['refresh_token'] ?? '';
        
        if (empty($refreshToken)) {
            $response->getBody()->write(json_encode([
                'error' => 'Refresh token is required'
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        try {
            $result = $this->authService->refreshToken($refreshToken);
            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));
            return $response
                ->withStatus($e->getCode() === 401 ? 401 : 500)
                ->withHeader('Content-Type', 'application/json');
        }
    }
}
