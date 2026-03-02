<?php
declare(strict_types=1);

use alt\api\actions\CountPostAction;
use alt\api\actions\CreateCommentAction;
use alt\api\actions\CreateLikeAction;
use alt\api\actions\CreatePostAction;
use alt\api\actions\CreateReactionAction;
use alt\api\actions\DeleteLikeAction;
use alt\api\actions\DeleteReactionAction;
use alt\api\actions\GetByIdAction;
use alt\api\actions\GetByIdwithStatusAction;
use alt\api\actions\GetCommentsByPostAction;
use alt\api\actions\GetReactionsByPostAction;
use alt\api\actions\HasLikeAction;

return function(\Slim\App $app): \Slim\App {

    $app->get('/', function ($request, $response) {
        $response->getBody()->write(json_encode([
            'service' => 'alt-social',
            'status' => 'running',
            'version' => '1.0.0'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/posts/{id}', GetByIdAction::class);
    $app->get('/posts',\alt\api\actions\GetAllPostsAction::class);
    $app->get('/posts/{id}/stats',GetByIdwithStatusAction::class);
    $app->post('/posts',CreatePostAction::class);


    $app->get('/posts/{id}/reactions', GetReactionsByPostAction::class);
    $app->post('/posts/{id}/reactions',CreateReactionAction::class);
    $app->delete('/reactions/{id}', DeleteReactionAction::class);

    $app->get('/posts/{id}/comments', GetCommentsByPostAction::class);
    $app->post('/posts/{id}/comments',CreateCommentAction::class);

    $app->post('/signin', alt\api\actions\SignInAction::class);
    $app->post('/posts/{id}/likes', CreateLikeAction::class);
    $app->delete('/posts/{id}/likes',DeleteLikeAction::class);
    $app->get('/posts/{postId}/liked/{userId}', HasLikeAction::class);
    $app->get('/posts/{id}/likes/count', CountPostAction::class);
$app->get('/uploads/{folder}/{filename}', function ($request, $response, $args) {

    $folder = basename($args['folder']);
    $filename = basename($args['filename']);

    $filePath = __DIR__ . "/uploads/$folder/$filename";

    if (!file_exists($filePath)) {
        $response->getBody()->write('Fichier introuvable');
        return $response->withStatus(404);
    }

    $stream = new \Slim\Psr7\Stream(fopen($filePath, 'rb'));

    return $response
        ->withBody($stream)
        ->withHeader('Content-Type', mime_content_type($filePath));
});


    return $app;
};
