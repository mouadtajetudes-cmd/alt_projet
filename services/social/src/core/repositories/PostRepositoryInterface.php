<?php

namespace alt\core\repositories;

use alt\core\application\ports\api\UpdatePostDTO;
use alt\core\domain\entities\Post;
use alt\core\application\ports\api\CreatePostDTO;
interface PostRepositoryInterface
{
    public function findAll(int $page, int $limit): array;
    public function findById(int $idPost): Post;
    public function findByIdWithStats(int $idPost): array;
    public function create(CreatePostDTO $post,?array $file = null): Post;
    public function findByUserPosts(int $idUser): array;
    public function delete(int $idPost,int $currentUserId): bool;
    public function update(int $idPost, UpdatePostDTO $postDTO,int $currentUserId, ?array $file = null): Post;
    public function findDrafts(int $idUser): array;
    public function createDraft(int $idpost): Post;
}
