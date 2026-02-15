<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\AuthServiceInterface;
use alt\core\application\ports\api\CreateUserDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RegisterAction
{
    public function __construct(
        private AuthServiceInterface $authService
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        
        $dto = new CreateUserDTO(
            $body['nom'] ?? '',
            $body['prenom'] ?? '',
            $body['email'] ?? '',
            $body['password'] ?? '',
            !empty($body['telephone']) ? $body['telephone'] : null,
            'false', 
            'false'  
        );

        try {
            $result = $this->authService->register($dto);
            $response->getBody()->write(json_encode($result));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
        } catch (\Exception $e) {
            // Ensure we only use valid HTTP status codes (200-599)
            $statusCode = 500;
            $errorCode = $e->getCode();
            
            // Check if it's a valid HTTP status code
            if (is_int($errorCode) && $errorCode >= 200 && $errorCode < 600) {
                $statusCode = $errorCode;
            }
            // Check for common database/validation errors
            elseif (is_string($e->getMessage())) {
                $message = strtolower($e->getMessage());
                // Duplicate entry errors
                if (strpos($message, 'already exists') !== false || 
                    strpos($message, 'duplicate') !== false || 
                    strpos($message, 'unique') !== false ||
                    strpos($message, '23505') !== false) { // PostgreSQL unique violation
                    $statusCode = 409; // Conflict
                }
                // Validation errors
                elseif (strpos($message, 'invalid') !== false || 
                        strpos($message, 'required') !== false ||
                        strpos($message, 'missing') !== false) {
                    $statusCode = 400; // Bad Request
                }
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
