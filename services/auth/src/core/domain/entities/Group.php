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
}
