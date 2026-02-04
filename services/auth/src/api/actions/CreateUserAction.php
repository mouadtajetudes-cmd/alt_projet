<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\UserServiceInterface;
use alt\core\application\ports\api\CreateUserDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CreateUserAction
{
    public function __construct(
        private UserServiceInterface $userService
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody();
        
        $dto = new CreateUserDTO();
        $dto->nom = $data['nom'];
        $dto->prenom = $data['prenom'];
        $dto->email = $data['email'];
        $dto->password = $data['password'];
        $dto->telephone = $data['telephone'] ?? '';
        
        $user = $this->userService->createUser($dto);
        
        $response->getBody()->write(json_encode($user));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}
