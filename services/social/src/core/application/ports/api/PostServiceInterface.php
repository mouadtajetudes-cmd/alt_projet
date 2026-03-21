<?php
namespace alt\core\application\ports\api;

use alt\core\application\ports\api\CreatePostDTO;
use alt\core\domain\entities\Post;

interface PostServiceInterface{
    public function getAllPosts(int $page, int $limit):array;
    public function getById(int $idPost):Post;
    public function getByUserPosts(int $idUser):array;
    public function getByIdwithStatus(int $idPost):array;
    public function createPost(CreatePostDTO $post,array $file):Post;
    public function deletePost(int $idPost,int $currentUserId):bool;
    public function updatePost(int $idPost, UpdatePostDTO $post, int $currentUserId ,?array $file = null):Post;
    public function getdrafts( int $currentUserId):array;
    public function publishDraft(int $postId): Post;
}