<?php
namespace alt\core\application\ports\spi;

use alt\core\application\dto\CreateCommentDTO;
interface CommentRepositoryInterface{
    public function findByPost(int $idPost):array;
    public function create(CreateCommentDTO $commentaire):CreateCommentDTO;
}