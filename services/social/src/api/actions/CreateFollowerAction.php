<?php
namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use alt\core\services\FollowerService;

class CreateFollowerAction
{
    private FollowerService $followerService;

    public function __construct(FollowerService $followerService)
    {
        $this->followerService = $followerService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();
        $followerId = $body['follower_id'] ?? null;    
        $followingId = $body['following_id'] ?? null;  

        if (!$followerId || !$followingId) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Les deux IDs sont requis.'
            ]));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(400);
        }

        try {
            $result = $this->followerService->createFollow($followerId, $followingId);

            if ($result) {
                $payload = [
                    'success' => true,
                    'message' => 'Utilisateur suivi avec succès.'
                ];
                $status = 201;
            } else {
                $payload = [
                    'success' => false,
                    'message' => 'Impossible de suivre cet utilisateur (déjà suivi ou auto-follow).'
                ];
                $status = 409;
            }

            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus($status);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(500);
        }
    }
}