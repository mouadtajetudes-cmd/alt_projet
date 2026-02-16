<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\GroupServiceInterface;
use alt\core\application\ports\api\CreateGroupDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UpdateGroupAction
{
    public function __construct(
        private GroupServiceInterface $groupService
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $groupId = (int) $args['id'];
        $data = $request->getParsedBody();
        
        // Get the existing group
        $group = $this->groupService->getGroupById($groupId);
        
        // Update fields
        $group->nom = $data['nom'] ?? $group->nom;
        $group->description = $data['description'] ?? $group->description;
        $group->niveau = $data['niveau'] ?? $group->niveau;
        
        // Save via repository
        $updatedGroup = $this->groupService->updateGroup($group);
        
        $response->getBody()->write(json_encode($updatedGroup));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
