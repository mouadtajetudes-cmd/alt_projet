<?php
namespace alt\core\application\dto;

use OpenApi\Attributes\Post;

class CreateCommentDTO{
    private string $details;
    private int $idUtilisateur;
    private int $idPost;

    public function __construct( int $idUtilisateur, int $idPost, string $details)
    {
        $this->details = $details;
        $this->idUtilisateur = $idUtilisateur;
        $this->idPost = $idPost;
    }

    public function getDetails(): string
    {
        return $this->details;
    }

    public function getIdUtilisateur(): int
    {
        return $this->idUtilisateur;
    }

    public function getIdPost(): int
    {
        return $this->idPost;
    }
}