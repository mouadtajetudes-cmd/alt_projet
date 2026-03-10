<?php

namespace alt\core\application\usecases;

use Exception;
use alt\core\application\ports\api\ProductFiltersDTO;
use alt\core\application\ports\api\ProductServiceInterface;
use alt\infra\ports\ProductRepositoryInterface;
use alt\core\domain\entities\Product;

class ProductService implements ProductServiceInterface
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAllProducts(ProductFiltersDTO $filters): array
    {
        return $this->productRepository->findAll([
            'page' => $filters->page ?? 1,
            'limit' => $filters->limit ?? 12,
            'search' => $filters->search ?? null,
            'categorie' => $filters->categorie ?? null,
            'min_price' => $filters->prixMin ?? null,
            'max_price' => $filters->prixMax ?? null,
            'statut' => $filters->statut ?? null,
            'userId' => $filters->userId ?? null,
        ]);
    }

    public function getProductById(int $id): array
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            throw new Exception('Produit introuvable');
        }

        return $product;
    }

    public function createProduct(array $data): Product
    {
        $product = Product::fromArray([
            'id_produit' => 0,
            'nom' => $data['nom'] ?? '',
            'description' => $data['description'] ?? '',
            'prix' => $data['prix'] ?? 0,
            'statut' => $data['statut'] ?? 'disponible',
            'quantite' => $data['quantite'] ?? 0,
            'id_utilisateur' => $data['id_utilisateur'] ?? null,
            'id_categorie' => $data['id_categorie'] ?? null,
        ]);

        return $this->productRepository->create($product);
    }

    public function updateProduct(int $id, array $data): Product
    {
        $existing = $this->productRepository->findById($id);

        if (!$existing) {
            throw new Exception('Produit introuvable');
        }

        $product = Product::fromArray($existing);

        if (isset($data['nom'])) {
            $product->setNom($data['nom']);
        }

        if (isset($data['prix'])) {
            $product->setPrix((float) $data['prix']);
        }

        if (isset($data['id_categorie'])) {
            $product->setIdCategorie((int) $data['id_categorie']);
        }

        if (isset($data['description'])) {
            $product->setDescription($data['description']);
        }

        if (isset($data['statut'])) {
            $product->setStatut($data['statut']);
        }

        if (isset($data['quantite'])) {
            $product->setQuantite((int) $data['quantite']);
        }

        return $this->productRepository->update($product);
    }

    public function deleteProduct(int $id): bool
    {
        $existing = $this->productRepository->findById($id);

        if (!$existing) {
            throw new Exception('Produit introuvable');
        }

        return $this->productRepository->delete($id);
    }
}