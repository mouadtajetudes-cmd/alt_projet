<?php
declare(strict_types=1);

namespace alt\core\domain\entities;

class Group
{
    public int $id_groupe;
    public string $nom;
    public string $description;
    public string $niveau;
    public string $created_at;

    public function __construct($id_groupe, $nom, $description, $niveau, $created_at)
    {
        $this->id_groupe = $id_groupe;
        $this->nom = $nom;
        $this->description = $description;
        $this->niveau = $niveau;
        $this->created_at = $created_at;
    }
}
