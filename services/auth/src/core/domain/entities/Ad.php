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
}
