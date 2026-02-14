<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\UserServiceInterface;
use alt\core\application\ports\api\UpdateUserDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UpdateUserAction
{
    public function __construct(
        private UserServiceInterface $userService
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int)$args['id'];
        $data = $request->getParsedBody();
        
        $dto = new UpdateUserDTO(
            $data['nom'] ?? null,
            $data['prenom'] ?? null,
            $data['email'] ?? null,
            $data['telephone'] ?? null,
            $data['administrateur'] ?? null,
            $data['premium'] ?? null,
            $data['password'] ?? null
        );
        
        $user = $this->userService->updateUser($id, $dto);
        
        $response->getBody()->write(json_encode([
            'id_utilisateur' => $user->id_utilisateur,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'telephone' => $user->telephone ?? '',
            'administrateur' => $user->administrateur,
            'premium' => $user->premium
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
