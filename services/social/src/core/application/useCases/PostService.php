<?php

namespace alt\core\application\useCases;

use alt\core\application\dto\CreatePostDTO;
use alt\core\application\ports\api\PostServiceInterface;
use alt\core\application\ports\spi\PostRepositoryInterface;
use alt\core\domain\entities\Post;

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
