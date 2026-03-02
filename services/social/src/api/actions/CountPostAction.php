<?php
namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use alt\core\services\LikeService;
class CountPostAction
{
     private LikeService $likeService;
    public function __construct(LikeService $likeService)
    {
        $this->likeService=$likeService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $postId=$request->getAttribute("id");
        $count=$this->likeService->countLikes($postId);

         $response->getBody()->write(json_encode(["count" => $count]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}