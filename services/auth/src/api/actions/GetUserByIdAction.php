<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\UserServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetUserByIdAction
{
    public function __construct(
        private UserServiceInterface $userService
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $user = $this->userService->getUserById($id);
        
        $response->getBody()->write(json_encode([
            'id_utilisateur' => $user->id_utilisateur,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'telephone' => $user->telephone ?? '',
            'bio' => $user->bio ?? '',
            'statut_personnalise' => $user->statut_personnalise ?? '',
            'banner_url' => $user->banner_url ?? null,
            'administrateur' => $user->administrateur,
            'premium' => $user->premium,
            'auth_provider' => $user->auth_provider ?? 'local',
            'points' => $user->points ?? 0,
            'id_avatar' => $user->id_avatar ?? null,
            'created_at' => $user->created_at ?? null,
            'updated_at' => $user->updated_at ?? null
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
