<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\GroupServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetUserGroupsAction
{
    public function __construct(
        private GroupServiceInterface $groupService
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $userId = (int)$args['id'];
        
        $groups = $this->groupService->getUserGroups($userId);
        
        $response->getBody()->write(json_encode($groups));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
