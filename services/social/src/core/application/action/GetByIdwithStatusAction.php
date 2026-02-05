<?php
namespace alt\core\application\action;

use alt\core\application\ports\api\PostServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetByIdwithStatusAction extends JsonError{
    private PostServiceInterface $postService;
    public function __construct(PostServiceInterface $postService)
    {
        $this->postService = $postService;
    }
    public function __invoke(ServerRequestInterface $request,ResponseInterface $response,array $args ): ResponseInterface {
        try {
            $idPost = (int) $args['id'];

            if ($idPost <= 0) {
                throw new \InvalidArgumentException('ID du post invalide');
            }

            $result = $this->postService->getByIdwithStatus($idPost);

            $post = $result['post'];
            $stats = $result['stats'];

            $postArray = [
                'id' => $post->getIdPost(),
                'titre' => $post->getTitre(),
                'description' => $post->getDescription(),
                'date_publication' => $post->getDatePublication(),
                'id_utilisateur' => $post->getIdUtilisateur(),
            ];$response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => [
                    'post' => $postArray,
                    'stats' => $stats
                ]
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (\InvalidArgumentException $e) {

            return $this->jsonError($response, $e->getMessage(), 400);

        } catch (\RuntimeException $e) {

            return $this->jsonError($response, 'Post introuvable', 404);

        } catch (\Exception $e) {

            return $this->jsonError($response, 'Erreur interne du serveur', 500);
        }
    }
}