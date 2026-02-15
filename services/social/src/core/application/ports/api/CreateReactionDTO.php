<?php
namespace alt\core\application\ports\api;


class CreateReactionDTO
{ 
    public int $post;
    public int $idUtilisateur;
    public string $type;

    public function __construct( int $post, int $idUtilisateur, string $type)
    {
        $this->post = $post;
        $this->idUtilisateur = $idUtilisateur;
        $this->type = $type;
    }
    public function getPost(): int
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