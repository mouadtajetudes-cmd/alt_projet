<?php
namespace alt\core\application\ports\api;

class CreatePostDTO
{
    public ?string $description;
    public int $idUtilisateur;

    public function __construct(?string $description, int $idUtilisateur)
    {
        $this->description = $description;
        $this->idUtilisateur = $idUtilisateur;
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getIdUtilisateur(): int
    {
        return $this->idUtilisateur;
    }

}