<?php

namespace alt\core\application\useCases;

use alt\core\application\dto\CreateReactionDTO;
use alt\core\application\ports\api\ReactionServiceInterface;
use alt\core\application\ports\spi\ReactionRepositoryInterface;
use alt\core\domain\entities\Post;
use alt\core\domain\entities\Reaction;

class ReactionService implements ReactionServiceInterface
{
    private ReactionRepositoryInterface $reactionRepository;

    public function __construct(ReactionRepositoryInterface $reactionRepository)
    {
        $this->reactionRepository = $reactionRepository;
    }

    public function getReactionsByPost(Post $idPost): array
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
