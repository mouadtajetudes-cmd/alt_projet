<?php
namespace alt\api\actions;

use alt\core\application\ports\api\FollowerServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DeleteFollowerAction
{
    private FollowerServiceInterface $followerService;

    public function __construct(FollowerServiceInterface $followerService)
    {
        $this->followerService = $followerService;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $followerId = $args['followerId'] ?? null;
        $followingId = $args['followingId'] ?? null;

        if (!$followerId || !$followingId) {
            $response->getBody()->write(json_encode([
                "error" => "Missing followerId or followingId"
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        $success = $this->followerService->deleteFollow($followerId, $followingId);

        if ($success) {

            $response->getBody()->write(json_encode([
                "message" => "Unfollowed successfully"
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }

        $response->getBody()->write(json_encode([
            "error" => "Failed to unfollow"
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
}