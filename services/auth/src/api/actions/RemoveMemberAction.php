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
        
        $members = $this->groupService->getGroupMembers($groupId);
        $memberToRemove = array_filter($members, fn($m) => $m['id_utilisateur'] == $userId);
        
        if (!empty($memberToRemove)) {
            $member = array_values($memberToRemove)[0];
            if ($member['role'] === 'owner') {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Cannot remove the owner of the group'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
            }
        }
        
        $success = $this->groupService->removeMember($groupId, $userId);
        
        $response->getBody()->write(json_encode([
            'success' => $success,
            'message' => $success ? 'Member removed successfully' : 'Failed to remove member'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
