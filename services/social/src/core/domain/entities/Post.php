<?php


namespace alt\core\domain\entities;

use function DI\string;

class Post
{
    private int $idPost;
    private string $titre;
    private ?string $description;
    private string $datePublication;
    private int $idUtilisateur;

    public function __construct(
        int $idPost,
        string $titre,
        ?string $description,
        string $datePublication,
        int $idUtilisateur
    ) {
        $this->idPost = $idPost;
        $this->titre = $titre;
        $this->description = $description;
        $this->datePublication = $datePublication;
        $this->idUtilisateur = $idUtilisateur;
    }

    // ===== Getters =====

    public function getIdPost(): int
    {
        return $this->idPost;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDatePublication(): string
    {
        return $this->datePublication;
    }

    public function getIdUtilisateur(): int
    {
        return $this->idUtilisateur;
    }
}
