<?php

namespace alt\api\actions;

use alt\core\application\ports\api\ReactionServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class DeleteReactionAction
{
    private ReactionServiceInterface $reactionService;

    public function __construct(ReactionServiceInterface $reactionService)
    {
        $this->reactionService = $reactionService;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {

        try {
            
            $reactionId = $request->getAttribute('id');
            $idUtilisateur=$request->getAttribute('id');

            if ($reactionId === null) {
                throw new \InvalidArgumentException('reactionId manquant');
            }

            
            $this->reactionService->deleteReaction($reactionId,$idUtilisateur);

            
            return $response->withStatus(204);

        } catch (\InvalidArgumentException $e) {

            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));

            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');

        } catch (Throwable $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Erreur interne du serveur'
            ]));

            return $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json');
        }
    }
}
