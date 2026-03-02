<?php

namespace alt\core\services;

<<<<<<< HEAD
use alt\core\application\ports\api\CommentServiceInterface;
use alt\core\application\ports\api\CreateCommentDTO;
=======
use alt\core\application\ports\api\CreateCommentDTO;
use alt\core\application\ports\api\CommentServiceInterface;
>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9
use alt\core\repositories\CommentRepositoryInterface;

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
