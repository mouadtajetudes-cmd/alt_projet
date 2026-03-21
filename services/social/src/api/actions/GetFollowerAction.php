<?php

namespace alt\api\actions;

use alt\core\services\FollowerService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetFollowerAction
{
    private FollowerService $followerService;

    public function __construct(FollowerService $followerService)
    {
        $this->followerService = $followerService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $userId = (int)$args['id_utilisateur']; 

        try {
            $followers = $this->followerService->getFollowers($userId);

            $payload = [
                'success' => true,
                'data' => $followers
            ];

            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(200);

        } catch (\Exception $e) {
            $payload = [
                'success' => false,
                'message' => 'Erreur récupération followers : ' . $e->getMessage()
            ];

            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(500);
        }
    }
}