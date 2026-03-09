<?php
namespace alt\core\application\ports\api;

<<<<<<< HEAD
=======
use alt\core\application\dto\CreateCommentDTO;
>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9

interface CommentServiceInterface{
    public function getCommentsByPost(int $idPost):array;
    public function createComment(CreateCommentDTO $commentaire):CreateCommentDTO;
}