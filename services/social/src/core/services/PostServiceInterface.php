<?php

namespace alt\core\services;

interface PostServiceInterface
{
    public function getPostById(string $postId): array;
}
