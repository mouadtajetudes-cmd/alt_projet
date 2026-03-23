<?php
declare(strict_types=1);

namespace alt\core\application\ports\api;

class CreateGroupDTO
{
    public string $nom;
    public ?string $description = null;
    public ?string $niveau = null;
}
