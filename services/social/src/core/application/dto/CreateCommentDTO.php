<?php
namespace alt\core\application\dto;

use OpenApi\Attributes\Post;

class CreateCommentDTO{
    private int $idCommentaire;
    private string $details;
    private int $idUtilisateur;
    private Post $idPost;

    public function __construct(int $idCommentaire, int $idUtilisateur, Post $idPost, string $details)
    {
        $this->idCommentaire = $idCommentaire;
        $this->details = $details;
        $this->idUtilisateur = $idUtilisateur;
        $this->idPost = $idPost;
    }
    public function getIdCommentaire(): int
    {
        return $this->idCommentaire;
    }

    public function getDetails(): string
    {
        return $this->details;
    }

    public function getIdUtilisateur(): int
    {
        return $this->idUtilisateur;
    }

    public function getIdPost(): Post
    {
        return $this->idPost;
    }
}