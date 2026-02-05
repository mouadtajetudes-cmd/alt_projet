<?php
namespace alt\core\application\dto;

use alt\core\domain\entities\Post;

class CreateReactionDTO
{ 
    public Post $post;
    public int $idUtilisateur;
    public string $type;

    public function __construct( Post $post, int $idUtilisateur, string $type)
    {
        $this->post = $post;
        $this->idUtilisateur = $idUtilisateur;
        $this->type = $type;
    }
    public function getPost(): Post
    {
        return $this->post;
    }
    public function getIdUtilisateur(): int
    {
        return $this->idUtilisateur;
    }
    public function getType(): string
    {
        return $this->type;
    }
}