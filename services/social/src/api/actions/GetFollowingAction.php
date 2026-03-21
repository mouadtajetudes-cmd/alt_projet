<?php
namespace alt\api\actions;

use alt\core\services\FollowerService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetFollowingAction
{
    private FollowerService $followerService;

    public function __construct(FollowerService $followerService)
    {
        $this->followerService = $followerService;
    }

    public function __invoke( ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $userId = (int)$args['id_utilisateur'];
        $following = $this->followerService->getFollowing($userId);

        $response->getBody()->write(json_encode($following));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}