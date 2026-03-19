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

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {

            $parsedBody = $request->getParsedBody() ?: $_POST ?: $_REQUEST;
            $files = $_FILES;
            var_dump($files);
            $idUtilisateur = $parsedBody['id_utilisateur'] ?? null;
            $file = null;

            if (!$idUtilisateur) {
                throw new \InvalidArgumentException('Utilisateur manquant');
            }

            $description = $parsedBody['description'] ?? '';
if (isset($files['file'])) {

    $uploadedFile = $files['file'];

    $filename = uniqid() . "_" . $uploadedFile['name'];
    $mime = $uploadedFile['type'];

    if (str_starts_with($mime, 'image/')) {
        $type = 'image';
        $folder = 'images';
    } elseif (str_starts_with($mime, 'video/')) {
        $type = 'video';
        $folder = 'videos';
    } else {
        throw new \InvalidArgumentException('Type non supporté');
    }

    $uploadPath = __DIR__ . "/../uploads/$folder/";

    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    move_uploaded_file(
        $uploadedFile['tmp_name'],
        $uploadPath . $filename
    );

    $file = [
        'name' => $filename,
        'type' => $type,
        'folder' => $folder
    ];
}
            $dto = new CreatePostDTO($description, $idUtilisateur);
            $post = $this->postService->createPost($dto, $file);

            // --- Construire le retour complet pour le frontend ---
$postArray = [
    'id_post' => $post->getIdPost(),
    'description' => $post->getDescription(),
    'id_utilisateur' => $post->getIdUtilisateur(),
    'titre' => $post->getTitre(),
    'media_type' => $post->getMediaType(),
    'media_url' => $post->getMediaUrl(),
    'nom' => $parsedBody['nom'] ?? 'Utilisateur',
    'prenom' => $parsedBody['prenom'] ?? ''
];            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $postArray
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);

        } catch (\InvalidArgumentException $e) {
            return $this->jsonError($response, $e->getMessage(), 400);
        } catch (\RuntimeException $e) {
            return $this->jsonError($response, $e->getMessage(), 500);
        } catch (\Exception $e) {
            return $this->jsonError($response, 'Erreur interne serveur', 500);
        }
    }
}