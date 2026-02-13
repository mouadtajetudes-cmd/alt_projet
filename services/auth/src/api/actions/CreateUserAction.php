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
        
        $dto = new CreateUserDTO(
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['password'],
            $data['telephone'] ?? '',
            $data['administrateur'] ?? false,
            $data['premium'] ?? false
        );
        
        $user = $this->userService->createUser($dto);
        
        $response->getBody()->write(json_encode([
            'id_utilisateur' => $user->id_utilisateur,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'telephone' => $user->telephone ?? '',
            'administrateur' => $user->administrateur,
            'premium' => $user->premium
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}
