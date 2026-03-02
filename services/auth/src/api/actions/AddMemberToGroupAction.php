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
        $userId = (int) ($data['userId'] ?? $data['user_id'] ?? 0);
        
        if ($userId === 0) {
            $response->getBody()->write(json_encode(['error' => 'Missing userId']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        $currentUser = $request->getAttribute('user');
        $currentUserId = $currentUser['id_utilisateur'] ?? $currentUser['id'] ?? 0;
        $isAdmin = ($currentUser['administrateur'] ?? false) === 'true';
        $memberRole = $this->groupService->getMemberRole($groupId, $currentUserId);
        $isGroupAdmin = in_array($memberRole, ['admin', 'owner']);
        
        if (!$isAdmin && !$isGroupAdmin) {
            $response->getBody()->write(json_encode([
                'error' => 'Forbidden',
                'message' => 'Vous devez être administrateur du groupe ou de la plateforme'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }
        
        $success = $this->groupService->addMember($groupId, $userId);
        
        $response->getBody()->write(json_encode(['success' => $success]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
