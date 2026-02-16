<?php
namespace alt\core\domain\entities;

class Commentaire
{
    private int $idCommentaire;
    private string $details;
    private int $idUtilisateur;
    private int $idPost;
    private string $createdAt;

    public function __construct(
        int $idCommentaire,
        string $details,
        int $idUtilisateur,
        int $idPost,
        string $createdAt,
    ) {
        $this->idCommentaire = $idCommentaire;
        $this->details = $details;
        $this->idUtilisateur = $idUtilisateur;
        $this->idPost = $idPost;
        $this->createdAt = $createdAt;
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

    public function getIdPost(): int
    {
        return $this->idPost;
    }
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

}