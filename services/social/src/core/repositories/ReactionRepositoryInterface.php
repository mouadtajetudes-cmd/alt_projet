<?php

namespace alt\core\repositories;

use alt\core\application\ports\api\CreateReactionDTO;

interface ReactionRepositoryInterface{
    public function findByPost(int $post):array;
    public function create(CreateReactionDTO $react):CreateReactionDTO;
    public function delete(int $idReaction,int $idUtilisateur):void;
}