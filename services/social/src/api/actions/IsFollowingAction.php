<?php
namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

Class IsFollowingAction
{
    private $followerService;

    public function __construct($followerService)
    {
        $this->followerService = $followerService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $followerId = (int) $request->getAttribute('followerId');
            $followingId = (int) $request->getAttribute('followingId');

            $isFollowing = $this->followerService->isFollowing($followerId, $followingId);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'isFollowing' => $isFollowing
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (\Throwable $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Erreur interne du serveur'
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}