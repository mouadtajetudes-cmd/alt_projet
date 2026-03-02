<?php

namespace alt\core\application\useCases;

use alt\core\application\dto\CreateCommentDTO;
use alt\core\application\ports\api\CommentServiceInterface;
use alt\core\application\ports\spi\CommentRepositoryInterface;

class CommentService implements CommentServiceInterface
{
    private CommentRepositoryInterface $commentRepository;

    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

      public function getCommentsByPost(int $idPost): array
    {
        return $this->commentRepository->findByPost($idPost);
    }

   
    public function createComment(CreateCommentDTO $dto): CreateCommentDTO
    {
        return $this->commentRepository->create($dto);
    }
}
