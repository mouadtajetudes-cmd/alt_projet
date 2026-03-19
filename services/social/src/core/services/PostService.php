<?php

namespace alt\core\services;

use alt\core\application\ports\api\CreatePostDTO;
use alt\core\application\ports\api\PostServiceInterface;
use alt\core\domain\entities\Post;
use alt\core\repositories\PostRepositoryInterface;

class PostService implements PostServiceInterface
{
    private PostRepositoryInterface $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function getAllPosts(int $page, int $limit): array
    {
        return $this->postRepository->findAll($page, $limit);
    }

    public function getById(int $idPost): Post
    {
        return $this->postRepository->findById($idPost);
    }

    public function getByIdwithStatus(int $idPost): array
    {
        return $this->postRepository->findByIdWithStats($idPost);
    }

    public function createPost(CreatePostDTO $dto): CreatePostDTO
    {
        return $this->postRepository->create($dto);
    }
}