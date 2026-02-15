<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\AuthServiceInterface;
use alt\core\application\ports\api\LoginDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginAction
{
    public function __construct(
        private AuthServiceInterface $authService
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        
        $dto = new LoginDTO(
            $body['email'] ?? '',
            $body['password'] ?? ''
        );
        
        try {
            $result = $this->authService->login($dto);
            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Ensure we only use valid HTTP status codes (200-599)
            $statusCode = 500;
            $errorCode = $e->getCode();
            
            // Check if it's a valid HTTP status code
            if (is_int($errorCode) && $errorCode >= 200 && $errorCode < 600) {
                $statusCode = $errorCode;
            } elseif ($e->getMessage() && (
                str_contains($e->getMessage(), 'Invalid') ||
                str_contains($e->getMessage(), 'incorrect') ||
                str_contains($e->getMessage(), 'not found')
            )) {
                // Invalid credentials - Unauthorized
                $statusCode = 401;
            }
            
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($statusCode);
        }
    }
}
