<?php
namespace alt\core\application\ports\api;

class CreatePostDTO
{
   
    public string $titre;
    public ?string $description;
    public int $idUtilisateur;

    public function __construct( string $titre, ?string $description, int $idUtilisateur)
    {
       
        $this->titre = $titre;
        $this->description = $description;
        $this->idUtilisateur = $idUtilisateur;
    }

    public function getTitre(): string
    {
        return $this->titre;
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