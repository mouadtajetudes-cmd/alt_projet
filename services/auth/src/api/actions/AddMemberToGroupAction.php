<?php
declare(strict_types=1);

namespace alt\api\actions;

use alt\core\application\ports\api\GroupServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AddMemberToGroupAction
{
    public function __construct(
        private GroupServiceInterface $groupService
    ) {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $groupId = (int) $args['id'];
        $data = $request->getParsedBody();
        $userId = (int) $data['user_id'];
        
        $success = $this->groupService->addMember($groupId, $userId);
        
        $response->getBody()->write(json_encode(['success' => $success]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
