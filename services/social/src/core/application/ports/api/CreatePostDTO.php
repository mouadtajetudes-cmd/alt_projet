<?php
namespace alt\core\application\ports\api;

class CreatePostDTO
{
   
    public string $type;
    public ?string $description;
    public int $idUtilisateur;

    public function __construct( string $type, ?string $description, int $idUtilisateur)
    {
       
        $this->type = $type;
        $this->description = $description;
        $this->idUtilisateur = $idUtilisateur;
    }

    public function getType(): string
    {
        return $this->type;
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