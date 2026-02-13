<?php
declare(strict_types=1);

namespace alt\core\domain\entities;

class Ad
{
    public int $id_publicite;
    public string $titre;
    public string $description;
    public string $image;
    public string $lien;
    public bool $actif;
    public string $created_at;

    public function __construct($id_publicite, $titre, $description, $image, $lien, $actif, $created_at)
    {
        $this->id_publicite = $id_publicite;
        $this->titre = $titre;
        $this->description = $description;
        $this->image = $image;
        $this->lien = $lien;
        $this->actif = $actif;
        $this->created_at = $created_at;
    }
}
