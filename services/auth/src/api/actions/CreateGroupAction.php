<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\GroupServiceInterface;
use alt\core\application\ports\api\CreateGroupDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CreateGroupAction
{
    public function __construct(
        private GroupServiceInterface $groupService
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody();
        
        $userId = $request->getAttribute('user_id');
        
        if (!$userId) {
            $response->getBody()->write(json_encode(['error' => 'User ID not found']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        $dto = new CreateGroupDTO();
        $dto->nom = $data['nom'];
        $dto->description = $data['description'] ?? '';
        $dto->niveau = $data['niveau'] ?? 'beginner';
        
        $group = $this->groupService->createGroup($dto);
        
        if ($group->id_groupe) {
            $this->groupService->addMember($group->id_groupe, (int)$userId, 'owner');
        }
        
        $response->getBody()->write(json_encode($group));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}
