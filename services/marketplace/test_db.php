<?php

require __DIR__ . '/vendor/autoload.php';

use alt\infra\repositories\PdoCategoryRepository;
use alt\infra\repositories\PdoProductRepository;
use alt\infra\repositories\PdoMediaRepository;

$host = getenv('DB_HOST') ?: 'alt-db';
$port = getenv('DB_PORT') ?: '5432';
$dbname = 'marketplace_db';
$user = getenv('DB_USER') ?: 'marketplace_user';
$password = getenv('DB_PASSWORD') ?: 'marketplace_password';

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "✓ Connexion reussie a la base de donnees\n\n";
    
    $categoryRepo = new PdoCategoryRepository($pdo);
    $productRepo = new PdoProductRepository($pdo);
    $mediaRepo = new PdoMediaRepository($pdo);
    
    echo "=== Test Categories ===\n";
    $categories = $categoryRepo->findAll();
    echo "Nombre de categories: " . count($categories) . "\n";
    if (count($categories) > 0) {
        $first = $categories[0];
        echo "Premiere categorie: " . $first->getNom() . "\n";
        echo "ID: " . $first->getId() . "\n";
    }
    echo "\n";
    
    echo "=== Test Products ===\n";
    $products = $productRepo->findAll();
    echo "Nombre de produits: " . count($products) . "\n";
    if (count($products) > 0) {
        $first = $products[0];
        echo "Premier produit: " . $first->getNom() . "\n";
        echo "Prix: " . $first->getPrix() . " EUR\n";
        echo "Statut: " . $first->getStatut() . "\n";
    }
    echo "\n";
    
    echo "=== Test Filtres ===\n";
    $filteredProducts = $productRepo->findAll(['statut' => 'disponible']);
    echo "Produits disponibles: " . count($filteredProducts) . "\n";
    
    $filteredProducts = $productRepo->findAll(['limit' => 5]);
    echo "5 premiers produits: " . count($filteredProducts) . "\n";
    echo "\n";
    
    echo "=== Test Medias ===\n";
    if (count($products) > 0) {
        $firstProductId = $products[0]->getId();
        $medias = $mediaRepo->findByProductId($firstProductId);
        echo "Medias du premier produit: " . count($medias) . "\n";
        if (count($medias) > 0) {
            $firstMedia = $medias[0];
            echo "Premier media: " . $firstMedia->getTitre() . "\n";
            echo "URL: " . $firstMedia->getUrl() . "\n";
        }
    }
    
    echo "\n✓ Tous les tests passes!\n";
    
} catch (PDOException $e) {
    echo "✗ Erreur connexion: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "✗ Erreur: " . $e->getMessage() . "\n";
}
