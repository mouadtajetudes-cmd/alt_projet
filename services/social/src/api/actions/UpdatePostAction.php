<?php

namespace alt\api\actions;

use alt\core\application\ports\api\PostServiceInterface;
use alt\core\application\ports\api\UpdatePostDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UpdatePostAction 
{
    private PostServiceInterface $postService;

    public function __construct(PostServiceInterface $postService)
    {
        $this->postService = $postService;
    }

public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
{
    $idPost = $args['id'];
    $data = $request->getParsedBody();
    $uploadedFiles = $request->getUploadedFiles();
    $currentUserId = (int)$request->getAttribute('id_utilisateur');

if (!$currentUserId) {
    $response->getBody()->write(json_encode(['error' => 'Utilisateur non authentifié']));
    return $response->withStatus(401)
                    ->withHeader('Content-Type','application/json');
}

    if (empty($data['description']) && empty($uploadedFiles['file'])) {
        $response->getBody()->write(json_encode(['error' => 'Nothing to update']));
        return $response->withStatus(400)
                        ->withHeader('Content-Type', 'application/json');
    }

    try {
$fileArray = null;
if (!empty($uploadedFiles['file']) && $uploadedFiles['file']->getError() === UPLOAD_ERR_OK) {
    $fileObj = $uploadedFiles['file'];
    $mediaType = $fileObj->getClientMediaType();
    $fileArray = [
        'name' => $fileObj->getClientFilename(),
        'type' => explode('/', $mediaType)[0],
        'tmp_name' => $fileObj->getStream()->getMetadata('uri'),
        'folder' => str_starts_with($mediaType, 'video') ? 'videos' : 'images'
    ];
}

$updateDTO = new UpdatePostDTO(
    isDraft : isset($data['is_draft']) ? (bool)$data['is_draft'] : true,
    description: $data['description'] ?? null,
    file: $fileArray
);

$updatedPost = $this->postService->updatePost($idPost, $updateDTO, $currentUserId, $fileArray);
        $response->getBody()->write(json_encode([
    'is_draft'=>$updatedPost->getIsDraft(),
    'description' => $updatedPost->getDescription(),
    'file'=>$updateDTO->getFile(),
]));
        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(200);

    } catch (\Exception $e) {
        $response->getBody()->write(json_encode([
            'error' => 'An error occurred while updating the post',
            'details' => $e->getMessage()
        ]));

        return $response->withStatus(500)
                        ->withHeader('Content-Type', 'application/json');
    }
}}
