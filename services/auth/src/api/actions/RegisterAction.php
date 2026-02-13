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
            $body['telephone'] ?? '',
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
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($e->getCode() ?: 500);
        }
    }
}
