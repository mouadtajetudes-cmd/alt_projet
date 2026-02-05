<?php
namespace alt\core\application\ports\api;

use alt\core\application\dto\CreateCommentDTO;

interface CommentServiceInterface{
    public function getCommentsByPost(int $idPost):array;
    public function createComment(CreateCommentDTO $commentaire):CreateCommentDTO;
}