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
        
        $dto = new CreateGroupDTO();
        $dto->nom = $data['nom'];
        $dto->description = $data['description'] ?? '';
        $dto->niveau = $data['niveau'] ?? 'beginner';
        
        $group = $this->groupService->createGroup($dto);
        
        $response->getBody()->write(json_encode($group));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}
