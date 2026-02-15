<?php
namespace alt\core\repositories;

use alt\core\application\ports\api\CreateCommentDTO;
interface CommentRepositoryInterface{
    public function findByPost(int $idPost):array;
    public function create(CreateCommentDTO $commentaire):CreateCommentDTO;
}