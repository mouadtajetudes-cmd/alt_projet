<?php

namespace alt\core\application\ports\api;

class CreateCategoryDTO
{
    public string $nom;
    public ?string $description;

    public function __construct(
        string $nom,
        ?string $description = null
    ) {
        $this->nom = $nom;
        $this->description = $description;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['nom'],
            $data['description'] ?? null
        );
    }
}
