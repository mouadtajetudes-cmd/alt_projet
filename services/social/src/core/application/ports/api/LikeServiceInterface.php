<?php
namespace alt\core\application\ports\api;

interface LikeServiceInterface
{
    public function like(CreateLikeDTO$like): bool;

    public function unlike(int $id_post, int $id_utilisateur): bool;

    public function hasLiked(int $id_post, int $id_utilisateur): bool;

    public function countLikes(int $id_post): int;
}