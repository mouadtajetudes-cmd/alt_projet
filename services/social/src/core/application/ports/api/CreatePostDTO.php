<?php
namespace alt\core\application\ports\api;

class CreatePostDTO
{
    public ?string $description;
    public int $idUtilisateur;
    public bool $isDraft;

    public function __construct(?string $description, int $idUtilisateur, bool $isDraft)
    {
        $this->description = $description;
        $this->idUtilisateur = $idUtilisateur;
        $this->isDraft = $isDraft;
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getIdUtilisateur(): int
    {
        return $this->idUtilisateur;
    }
    public function getIsDraft(): bool
    {
        return $this->isDraft;
    }

}