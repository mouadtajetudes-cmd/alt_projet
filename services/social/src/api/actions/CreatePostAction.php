<?php

namespace alt\api\actions;

use alt\core\application\ports\api\CreatePostDTO;
use alt\core\application\ports\api\PostServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CreatePostAction extends JsonError
{
    private PostServiceInterface $postService;

    public function __construct(PostServiceInterface $postService)
    {
        $this->postService = $postService;
    }

<<<<<<< HEAD
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {

            $parsedBody = $request->getParsedBody() ;
             if (empty($parsedBody)) {
    $parsedBody = $_POST  ;
}
if (empty($parsedBody)) {
    $parsedBody = $_REQUEST;
}
           
            $files = $request->getUploadedFiles();
            $type = $parsedBody['type'] ?? null;
            $idUtilisateur = $parsedBody['id_utilisateur'] ?? null;
           

            if (!$type || !$idUtilisateur) {
                throw new \InvalidArgumentException('Paramètres manquants');
            }

            $description = $parsedBody['description'] ?? '';

            if ($type === 'image' || $type === 'video') {
                if (!isset($files['file'])) {
                    throw new \InvalidArgumentException('Fichier manquant');
                }

                $uploadedFile = $files['file'];

                if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
                    throw new \RuntimeException('Erreur upload fichier');
                }

                $folder = $type === 'image' ? 'images' : 'videos';
                $uploadPath = __DIR__ . "/../uploads/$folder/";

                if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

                $filename = uniqid() . "_" . $uploadedFile->getClientFilename();
                $uploadedFile->moveTo($uploadPath . $filename);

                $description = $filename;
            }

            $dto = new CreatePostDTO($type, $description, $idUtilisateur);
            $post = $this->postService->createPost($dto);
           
=======
    public function __invoke(ServerRequestInterface $request,ResponseInterface $response): ResponseInterface {
        try {
           
            $body = $request->getParsedBody();

            if (!isset($body['titre']) || !isset($body['description']) || !isset($body['idUtilisateur'])) {
                throw new \InvalidArgumentException('Paramètres manquants');
            }

            $dto = new CreatePostDTO(
                $body['titre'],
                $body['description'],
                $body['idUtilisateur']
            );

            
            $post = $this->postService->createPost($dto);
>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $post
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);

        } catch (\InvalidArgumentException $e) {
            return $this->jsonError($response, $e->getMessage(), 400);

        } catch (\RuntimeException $e) {
<<<<<<< HEAD
            return $this->jsonError($response, $e->getMessage(), 500);

        } catch (\Exception $e) {
            return $this->jsonError($response, 'Erreur interne serveur', 500);
=======
            return $this->jsonError($response, 'Impossible de créer le post', 500);

        } catch (\Exception $e) {
            return $this->jsonError($response, 'Erreur interne du serveur', 500);
>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9
        }
    }
}
