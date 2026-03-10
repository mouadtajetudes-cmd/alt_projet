<?php

namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PDO;

class CreateProductAction
{
    private function getPdo(): PDO
    {
        $host = getenv('DB_HOST') ?: 'alt.db';
        $port = getenv('DB_PORT') ?: '5432';
        $name = getenv('DB_NAME') ?: 'alt';
        $user = getenv('DB_USER') ?: 'alt';
        $pass = getenv('DB_PASSWORD') ?: 'alt';

        return new PDO(
            "pgsql:host={$host};port={$port};dbname={$name}",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    private function categoryExists(PDO $pdo, int $idCategorie): bool
    {
        $st = $pdo->prepare("SELECT 1 FROM categories WHERE id_categorie = :id");
        $st->execute([':id' => $idCategorie]);
        return (bool) $st->fetchColumn();
    }

    private function userExists(PDO $pdo, int $idUtilisateur): bool
    {
        $st = $pdo->prepare("SELECT 1 FROM utilisateurs WHERE id_utilisateur = :id");
        $st->execute([':id' => $idUtilisateur]);
        return (bool) $st->fetchColumn();
    }

    private function detectMediaType(string $url): string
    {
        $lower = strtolower($url);

        if (str_ends_with($lower, '.png')) return 'image/png';
        if (str_ends_with($lower, '.gif')) return 'image/gif';
        if (str_ends_with($lower, '.webp')) return 'image/webp';
        if (str_ends_with($lower, '.jpeg')) return 'image/jpeg';
        if (str_ends_with($lower, '.jpg')) return 'image/jpeg';

        return 'image/jpeg';
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $pdo = null;

        try {
            $data = $request->getParsedBody() ?? [];

            $nom = trim((string) ($data['nom'] ?? ''));
            $description = trim((string) ($data['description'] ?? ''));
            $prix = (float) ($data['prix'] ?? 0);
            $quantite = (int) ($data['quantite'] ?? 0);
            $statut = trim((string) ($data['statut'] ?? 'disponible'));
            $idCategorie = isset($data['id_categorie']) && $data['id_categorie'] !== ''
                ? (int) $data['id_categorie']
                : null;
            $idUtilisateur = (int) ($data['id_utilisateur'] ?? 1);
            $mediaUrls = is_array($data['media_urls'] ?? null) ? $data['media_urls'] : [];

            $statutsAutorises = ['disponible', 'reserve', 'indisponible', 'vendu'];

            if ($nom === '') {
                throw new \Exception('Le nom du produit est obligatoire');
            }

            if ($description === '') {
                throw new \Exception('La description du produit est obligatoire');
            }

            if ($prix < 0) {
                throw new \Exception('Le prix est invalide');
            }

            if ($quantite < 0) {
                throw new \Exception('La quantité est invalide');
            }

            if ($idCategorie === null || $idCategorie <= 0) {
                throw new \Exception('La catégorie est obligatoire');
            }

            if ($idUtilisateur <= 0) {
                throw new \Exception('Utilisateur invalide');
            }

            if (!in_array($statut, $statutsAutorises, true)) {
                $statut = 'disponible';
            }

            $pdo = $this->getPdo();

            if (!$this->categoryExists($pdo, $idCategorie)) {
                throw new \Exception('La catégorie sélectionnée n’existe pas');
            }

            if (!$this->userExists($pdo, $idUtilisateur)) {
                throw new \Exception('L’utilisateur sélectionné n’existe pas');
            }

            $pdo->beginTransaction();

            $sqlProduit = "
                INSERT INTO produits (
                    nom,
                    description,
                    prix,
                    statut,
                    quantite,
                    id_utilisateur,
                    id_categorie
                )
                VALUES (
                    :nom,
                    :description,
                    :prix,
                    :statut,
                    :quantite,
                    :id_utilisateur,
                    :id_categorie
                )
                RETURNING id_produit
            ";

            $st = $pdo->prepare($sqlProduit);
            $st->execute([
                ':nom' => $nom,
                ':description' => $description,
                ':prix' => $prix,
                ':statut' => $statut,
                ':quantite' => $quantite,
                ':id_utilisateur' => $idUtilisateur,
                ':id_categorie' => $idCategorie
            ]);

            $idProduit = (int) $st->fetchColumn();

            $ordre = 0;

            foreach ($mediaUrls as $url) {
                $url = trim((string) $url);

                if ($url === '') {
                    continue;
                }

                $mediaType = $this->detectMediaType($url);
                $titreMedia = $nom . ' - image ' . ($ordre + 1);

                $stMedia = $pdo->prepare("
                    INSERT INTO medias (titre, url, type)
                    VALUES (:titre, :url, :type)
                    RETURNING id_media
                ");
                $stMedia->execute([
                    ':titre' => $titreMedia,
                    ':url' => $url,
                    ':type' => $mediaType
                ]);

                $idMedia = (int) $stMedia->fetchColumn();

                $stPm = $pdo->prepare("
                    INSERT INTO produit_medias (
                    id_media,
                    id_produit,
                    ordre
                )
                VALUES (
                    :id_media,
                    :id_produit,
                    :ordre
                )
            ");
            $stPm->execute([
                ':id_media' => $idMedia,
                ':id_produit' => $idProduit,
                ':ordre' => $ordre
            ]);

                $ordre++;
            }

            $pdo->commit();

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'Annonce créée avec succès',
                'data' => [
                    'id_produit' => $idProduit,
                    'media_count' => count($mediaUrls)
                ]
            ], JSON_UNESCAPED_UNICODE));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);

        } catch (\Throwable $e) {
            if ($pdo && $pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log('CREATE PRODUCT ERROR: ' . $e->getMessage());

            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}