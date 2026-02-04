<?php

namespace alt\core\domain\entities;

class Media
{
    private ?int $id;
    private string $titre;
    private string $url;
    private ?string $type;
    private ?\DateTimeInterface $createdAt;

    public function __construct(
        ?int $id,
        string $titre,
        string $url,
        ?string $type = null,
        ?\DateTimeInterface $createdAt = null
    ) {
        $this->id = $id;
        $this->titre = $titre;
        $this->url = $url;
        $this->type = $type;
        $this->createdAt = $createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id_media' => $this->id,
            'titre' => $this->titre,
            'url' => $this->url,
            'type' => $this->type,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id_media'] ?? null,
            $data['titre'],
            $data['url'],
            $data['type'] ?? null,
            isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : null
        );
    }
}
