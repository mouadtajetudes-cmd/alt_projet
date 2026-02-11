<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\GroupServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RemoveMemberAction
{
    public function __construct(
        private GroupServiceInterface $groupService
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $groupId = (int)$args['id'];
        $userId = (int)$args['userId'];
        
        $success = $this->groupService->removeMember($groupId, $userId);
        
        $response->getBody()->write(json_encode([
            'success' => $success,
            'message' => $success ? 'Member removed successfully' : 'Failed to remove member'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
