<?php

namespace alt\core\application\ports\api;

class ProductFiltersDTO
{
    public ?int $categorie;
    public ?float $prixMin;
    public ?float $prixMax;
    public ?string $statut;
    public int $page;
    public int $limit;
    public ?string $search;
    public ?int $userId;

    public function __construct(
        ?int $categorie = null,
        ?float $prixMin = null,
        ?float $prixMax = null,
        ?string $statut = null,
        int $page = 1,
        int $limit = 20,
        ?string $search = null,
        ?int $userId = null
    ) {
        $this->categorie = $categorie;
        $this->prixMin = $prixMin;
        $this->prixMax = $prixMax;
        $this->statut = $statut;
        $this->page = max(1, $page);
        $this->limit = min(100, max(1, $limit));
        $this->search = $search;
        $this->userId = $userId;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['categorie']) ? (int) $data['categorie'] : null,
            isset($data['prix_min']) ? (float) $data['prix_min'] : null,
            isset($data['prix_max']) ? (float) $data['prix_max'] : null,
            $data['statut'] ?? null,
            isset($data['page']) ? (int) $data['page'] : 1,
            isset($data['limit']) ? (int) $data['limit'] : 20,
            $data['search'] ?? null,
            isset($data['user_id']) ? (int) $data['user_id'] : null
        );
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->limit;
    }
}
