<?php
declare(strict_types=1);

namespace alt\core\application\ports\api;

class UpdateUserDTO
{
    public string $nom;
    public string $prenom;
    public string $email;
    public string $telephone;
}
