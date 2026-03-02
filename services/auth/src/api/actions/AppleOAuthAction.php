<?php
declare(strict_types=1);

namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Container\ContainerInterface;
use alt\core\application\ports\api\AuthServiceInterface;
use alt\core\domain\entities\User;
use alt\core\application\ports\spi\repositoryInterfaces\UserRepositoryInterface;

class AppleOAuthAction
{
    private AuthServiceInterface $authService;
    private ContainerInterface $container;

    public function __construct(AuthServiceInterface $authService, ContainerInterface $container)
    {
        $this->authService = $authService;
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getParsedBody();
        
        if (!isset($body['id_token'])) {
            $response->getBody()->write(json_encode([
                'error' => 'Missing id_token'
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        try {
            $parts = explode('.', $body['id_token']);
            if (count($parts) !== 3) {
                throw new \Exception('Invalid token format');
            }

            $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
            
            if (!isset($payload['email'])) {
                throw new \Exception('Email not found in token');
            }

            $email = $payload['email'];
            $sub = $payload['sub']; 
            
            $nom = $body['nom'] ?? 'User';
            $prenom = $body['prenom'] ?? '';

            $userRepo = $this->container->get(UserRepositoryInterface::class);
            $existingUser = $userRepo->findByEmail($email);

            if ($existingUser) {
                $result = $this->authService->generateTokensForUser($existingUser);
                
                $response->getBody()->write(json_encode($result));
                return $response->withHeader('Content-Type', 'application/json');
            }

            $user = new User(
                null,
                $nom,
                $prenom,
                $email,
                '',      
                null,    
                'false', 
                'false', 
                'apple',
                0,
                null
            );

            $createdUser = $userRepo->create($user);
            
            $result = $this->authService->generateTokensForUser($createdUser);

            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Apple Sign In failed',
                'message' => $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
