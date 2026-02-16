<?php
namespace alt\api\actions;

use alt\core\application\ports\api\PostServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetAllPostsAction extends JsonError
{
    private PostServiceInterface $postService;

    public function __construct(PostServiceInterface $postService)
    {
        $this->postService = $postService;
    }

public function __invoke(ServerRequestInterface $request,ResponseInterface $response ): ResponseInterface {
        try {
           
            $queryParams = $request->getQueryParams();

            $page  = isset($queryParams['page']) ? (int) $queryParams['page'] : 1;
            $limit = isset($queryParams['limit']) ? (int) $queryParams['limit'] : 5;

            if ($page <= 0 || $limit <= 0) {
                throw new \InvalidArgumentException('Page ou limite invalide');
            }

            $posts = $this->postService->getAllPosts($page, $limit);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $posts
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (\InvalidArgumentException $e) {

            return $this->jsonError($response, $e->getMessage(), 400);

        } catch (\RuntimeException $e) {

            return $this->jsonError(
                $response,
                'Erreur lors de la récupération des posts',
                500
            );

        } catch (\Exception $e) {

            return $this->jsonError(
                $response,
                'Erreur interne du serveur',
                500
            );
        }
    }
}
