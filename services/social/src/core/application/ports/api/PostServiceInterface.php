<?php
namespace alt\core\application\ports\api;

<<<<<<< HEAD
use alt\core\application\ports\api\CreatePostDTO;
=======
use alt\core\application\dto\CreatePostDTO;
>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9
use alt\core\domain\entities\Post;

interface PostServiceInterface{
    public function getAllPosts(int $page, int $limit):array;
    public function getById(int $idPost):Post;
    public function getByIdwithStatus(int $idPost):array;
    public function createPost(CreatePostDTO $post):CreatePostDTO;
}