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
        $body = $request->getParsedBody();
        
        $dto = new UpdateUserDTO();
        $dto->nom = $body['nom'] ?? '';
        $dto->prenom = $body['prenom'] ?? '';
        $dto->email = $body['email'] ?? '';
        $dto->telephone = $body['telephone'] ?? '';
        
        $user = $this->userService->updateUser($id, $dto);
        
        $response->getBody()->write(json_encode($user));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
