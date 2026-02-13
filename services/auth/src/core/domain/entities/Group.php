<?php
declare(strict_types=1);

namespace alt\core\domain\entities;

class Group implements \JsonSerializable
{
    public ?int $id_groupe = null;
    public string $nom = '';
    public ?string $description = null;
    public ?string $niveau = null;
    public ?string $created_at = null;

    public function jsonSerialize(): mixed
    {
        return [
            'id_groupe' => $this->id_groupe,
            'nom' => $this->nom,
            'description' => $this->description,
            'niveau' => $this->niveau,
            'created_at' => $this->created_at
        ];
    }
}
