<?php

namespace alt\core\application\ports\spi;

use alt\core\application\dto\CreateReactionDTO;
use alt\core\domain\entities\Post;
use alt\core\domain\entities\Reaction;

interface ReactionRepositoryInterface{
    public function findByPost(Post $post):array;
    public function create(CreateReactionDTO $react):CreateReactionDTO;
    public function delete(int $idReaction,int $idUtilisateur):void;
}