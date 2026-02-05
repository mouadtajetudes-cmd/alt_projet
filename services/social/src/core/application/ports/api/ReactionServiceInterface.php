<?php
namespace alt\core\application\ports\api;

use alt\core\application\dto\CreateReactionDTO;
use alt\core\domain\entities\Post;
use alt\core\domain\entities\Reaction;

interface ReactionServiceInterface{
    public function getReactionsByPost(Post $idPost):array;
    public function createReaction(CreateReactionDTO $reaction):CreateReactionDTO;
    public function deleteReaction(int $idReaction, int $idUtilisateur):void;
}