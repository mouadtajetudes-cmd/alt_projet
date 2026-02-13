<?php

namespace alt\core\application\usecases;

use alt\core\application\ports\spi\repositoryInterfaces\ProductRepositoryInterface;
use alt\core\application\ports\spi\repositoryInterfaces\MediaRepositoryInterface;
use alt\core\application\ports\api\CreateProductDTO;
use alt\core\application\ports\api\UpdateProductDTO;
use alt\core\application\ports\api\ProductFiltersDTO;
use alt\core\application\ports\api\ProductServiceInterface;
use alt\core\domain\entities\Product;
use Exception;
use DateTimeImmutable;

class ProductService implements ProductServiceInterface
{
    private ProductRepositoryInterface $productRepository;
    private MediaRepositoryInterface $mediaRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        MediaRepositoryInterface $mediaRepository
    ) {
        $this->productRepository = $productRepository;
        $this->mediaRepository = $mediaRepository;
    }

    public function getAllProducts(ProductFiltersDTO $filters): array
    {
        $filterArray = [
            'categorie' => $filters->categorie,
            'prix_min' => $filters->prixMin,
            'prix_max' => $filters->prixMax,
            'statut' => $filters->statut,
            'search' => $filters->search,
            'user_id' => $filters->userId,
            'limit' => $filters->limit,
            'offset' => $filters->getOffset()
        ];

        return $this->productRepository->findAll($filterArray);
    }

    public function getProductById(int $id): Product
    {
        $product = $this->productRepository->findById($id);
        
        if (!$product) {
            throw new Exception("Product not found", 404);
        }

        $medias = $this->mediaRepository->findByProductId($id);
        $product->setMedias($medias);

        return $product;
    }

    public function createProduct(CreateProductDTO $dto): Product
    {
        $product = new Product(
            0,
            $dto->nom,
            $dto->prix,
            $dto->idUtilisateur,
            $dto->idCategorie,
            $dto->description,
            $dto->statut,
            $dto->quantite,
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $createdProduct = $this->productRepository->create($product);

        foreach ($dto->mediaIds as $index => $mediaId) {
            $this->mediaRepository->attachToProduct($mediaId, $createdProduct->getId(), $index);
        }

        return $createdProduct;
    }

    public function updateProduct(UpdateProductDTO $dto): Product
    {
        $product = $this->productRepository->findById($dto->id);
        
        if (!$product) {
            throw new Exception("Product not found", 404);
        }

        if ($dto->nom !== null) {
            $product->setNom($dto->nom);
        }
        if ($dto->prix !== null) {
            $product->setPrix($dto->prix);
        }
        if ($dto->idCategorie !== null) {
            $product->setIdCategorie($dto->idCategorie);
        }
        if ($dto->description !== null) {
            $product->setDescription($dto->description);
        }
        if ($dto->statut !== null) {
            $product->setStatut($dto->statut);
        }
        if ($dto->quantite !== null) {
            $product->setQuantite($dto->quantite);
        }

        $updatedProduct = $this->productRepository->update($product);

        if ($dto->mediaIds !== null) {
            $currentMedias = $this->mediaRepository->findByProductId($dto->id);
            foreach ($currentMedias as $media) {
                $this->mediaRepository->detachFromProduct($media->getId(), $dto->id);
            }

            foreach ($dto->mediaIds as $index => $mediaId) {
                $this->mediaRepository->attachToProduct($mediaId, $dto->id, $index);
            }
        }

        return $updatedProduct;
    }

    public function deleteProduct(int $id): bool
    {
        $product = $this->productRepository->findById($id);
        
        if (!$product) {
            throw new Exception("Product not found", 404);
        }

        return $this->productRepository->delete($id);
    }
}
