<?php

namespace alt\core\services;

use alt\core\repositories\PostRepositoryInterface;

class PostService implements PostServiceInterface
{
    private PostRepositoryInterface $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function getPostById(string $postId): array
    {
        $post = $this->postRepository->findById($postId);
        
        if (!$post) {
            throw new \Exception("Post not found", 404);
        }

        return $post;
    }
}
