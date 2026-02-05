<?php
namespace alt\core\application\ports\api;

use alt\core\application\dto\CreatePostDTO;
use alt\core\domain\entities\Post;

interface PostServiceInterface{
    public function getAllPosts(int $page, int $limit):array;
    public function getById(int $idPost):Post;
    public function getByIdwithStatus(int $idPost):array;
    public function createPost(CreatePostDTO $post):CreatePostDTO;
}