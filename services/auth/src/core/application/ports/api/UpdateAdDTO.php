<?php
declare(strict_types=1);

namespace alt\core\application\ports\api;

class UpdateAdDTO
{
    public string $titre;
    public string $description;
    public string $image;
    public string $lien;
    public bool $actif;
}
