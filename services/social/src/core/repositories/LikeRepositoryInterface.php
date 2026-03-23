<?php
namespace alt\core\repositories;

use alt\core\application\ports\api\CreateLikeDTO;

interface LikeRepositoryInterface {

    public function createLike(CreateLikeDTO $like): bool ;
    public function deleteLike(int $id_post, int $id_utilisateur): bool;
    public function hasLiked(int $id_post, int $id_utilisateur): bool;
     public function count(int $id_post): int;
}