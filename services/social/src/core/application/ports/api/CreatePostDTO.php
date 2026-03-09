<?php
namespace alt\core\application\ports\api;

class CreatePostDTO
{
   
<<<<<<< HEAD
    public string $type;
    public ?string $description;
    public int $idUtilisateur;

    public function __construct( string $type, ?string $description, int $idUtilisateur)
    {
       
        $this->type = $type;
=======
    public string $titre;
    public ?string $description;
    public int $idUtilisateur;

    public function __construct( string $titre, ?string $description, int $idUtilisateur)
    {
       
        $this->titre = $titre;
>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9
        $this->description = $description;
        $this->idUtilisateur = $idUtilisateur;
    }

<<<<<<< HEAD
    public function getType(): string
    {
        return $this->type;
=======
    public function getTitre(): string
    {
        return $this->titre;
>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9
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