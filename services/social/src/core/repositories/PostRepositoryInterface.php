<?php

namespace alt\core\repositories;

interface PostRepositoryInterface
{
    public function findById(string $postId): ?array;
}
