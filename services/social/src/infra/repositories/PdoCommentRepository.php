<?php

namespace alt\infra\repositories;

use alt\core\application\dto\CreateCommentDTO;
use alt\core\application\ports\spi\CommentRepositoryInterface;
use alt\core\domain\entities\Commentaire;
use PDO;

class PdoCommentRepository implements CommentRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByPost(int $idPost): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_commentaire, details, created_at, id_post, id_utilisateur
             FROM commentaires
             WHERE id_post = :post
             ORDER BY created_at ASC'
        );

        $stmt->execute(['post' => $idPost]);

        $commentaires = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $commentaires[] = $this->mapToCommentaire($row);
        }

        return $commentaires;
    }

    public function create(CreateCommentDTO $commentaire): CreateCommentDTO
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO commentaires (details, id_post, id_utilisateur)
             VALUES (:details, :post, :user)'
        );

        $stmt->execute([
            'details' => $commentaire->getDetails(),
            'post' => $commentaire->getIdPost(),
            'user' => $commentaire->getIdUtilisateur(),
        ]);

        $id = (int) $this->pdo->lastInsertId();

        return new CreateCommentDTO(
            $id,
            $commentaire->getIdUtilisateur(),
            $commentaire->getIdPost(),
            $commentaire->getDetails()
        );
    }

    private function mapToCommentaire(array $row): Commentaire
    {
        return new Commentaire(
            (int) $row['id_commentaire'],
            $row['details'],
            $row['id_utilisateur'],
            $row['id_post'],
           $row['created_at'],
        );
    }
}
