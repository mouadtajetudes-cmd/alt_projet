<?php

namespace alt\core\services;

use alt\core\application\ports\api\CreateReactionDTO;
use alt\core\application\ports\api\ReactionServiceInterface;
use alt\core\domain\entities\Post;
use alt\core\repositories\ReactionRepositoryInterface;

class ReactionService implements ReactionServiceInterface
{
    private ReactionRepositoryInterface $reactionRepository;

    public function __construct(ReactionRepositoryInterface $reactionRepository)
    {
        $this->reactionRepository = $reactionRepository;
    }

    public function getReactionsByPost(int $idPost): array
    {
        return $this->reactionRepository->findByPost($idPost);
    }

    public function createReaction(CreateReactionDTO $dto):CreateReactionDTO
    {
             $created = $this->reactionRepository->create($dto);
             return $created;
    }

    public function deleteReaction(int $idReaction, int $idUtilisateur): void
    {
        $this->reactionRepository->delete($idReaction, $idUtilisateur);
    }
}
