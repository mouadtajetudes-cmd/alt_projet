<?php
namespace alt\core\application\ports\api;


interface CommentServiceInterface{
    public function getCommentsByPost(int $idPost):array;
    public function createComment(CreateCommentDTO $commentaire):CreateCommentDTO;
}