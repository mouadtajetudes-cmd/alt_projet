<?php

namespace alt\core\repositories;

use alt\core\domain\entities\Post;
use alt\core\application\ports\api\CreatePostDTO;
interface PostRepositoryInterface
{
    public function findAll(int $page, int $limit): array;
    public function findById(int $idPost): Post;
    public function findByIdWithStats(int $idPost): array;
    public function create(CreatePostDTO $post): CreatePostDTO;
}
