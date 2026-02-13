<?php

namespace alt\api\actions;

use alt\core\application\ports\api\PostServiceInterface;

class GetPostsByUserId{
    private PostServiceInterface $postService;

    public function __construct(PostServiceInterface $postService)
    {
        $this->postService = $postService;
    }

    public function __invoke($request, $response, $args)
    {
        $idUtilisateur = (int)$args['idUtilisateur'];
        $page = isset($args['page']) ? (int)$args['page'] : 1;
        $limit = isset($args['limit']) ? (int)$args['limit'] : 5;

        try {
            $posts = $this->postService->getPostsByUserId($idUtilisateur, $page, $limit);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $posts
            ]));

            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'An error occurred while fetching posts'
            ]));

            return $response->withStatus(500)
                            ->withHeader('Content-Type', 'application/json');
        }
    }
}