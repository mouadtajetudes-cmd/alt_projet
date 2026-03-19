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

    public function createPost(CreatePostDTO $dto, $file): Post
    {
        return $this->postRepository->create($dto, $file);
    }
    public function getByUserPosts(int $idUser): array
    {
        return $this->postRepository->findByUserPosts($idUser);
    }
public function deletePost(int $idPost, int $currentUserId): bool
{
    $post = $this->postRepository->findById($idPost);

    if (!$post) {
        throw new \Exception("Post introuvable");
    }

    // Vérification sécurité
    if ($post->getIdUtilisateur() !== $currentUserId) {
        throw new \Exception("Non autorisé");
    }

    return $this->postRepository->delete($idPost);
}}