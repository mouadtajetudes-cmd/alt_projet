<?php
namespace alt\core\domain\entities;

class Commentaire
{
    private int $idCommentaire;
    private string $details;
    private int $idUtilisateur;
    private Post $idPost;
    private \DateTime $createdAt;

    public function __construct(
        int $idCommentaire,
        string $details,
        int $idUtilisateur,
        Post $idPost,
        \DateTime $createdAt,
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

    public function getIdPost(): Post
    {
        return $this->idPost;
    }
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

}