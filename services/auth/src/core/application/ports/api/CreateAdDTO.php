<?php
declare(strict_types=1);

namespace alt\core\application\ports\api;

class CreateAdDTO
{
    public string $titre;
    public string $description;
    public string $image;
    public string $lien;
}
