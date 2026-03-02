<?php

namespace alt\core\domain\entities;

use DateTimeInterface;
use DateTimeImmutable;

class Category
{
    private int $id;
    private string $nom;
    private string $description;
    private DateTimeInterface $createdAt;
    private DateTimeInterface $updatedAt;

    public function __construct(
        int $id,
        string $nom,
        string $description,
        DateTimeInterface $createdAt,
        DateTimeInterface $updatedAt
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        return [
            'id_categorie' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_categorie'],
            $data['nom'],
            $data['description'] ?? '',
            isset($data['created_at']) ? new DateTimeImmutable($data['created_at']) : new DateTimeImmutable(),
            isset($data['updated_at']) ? new DateTimeImmutable($data['updated_at']) : new DateTimeImmutable()
        );
    }
}
