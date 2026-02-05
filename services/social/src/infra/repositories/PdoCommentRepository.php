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
            'SELECT c.id_commentaire, c.details, c.created_at, c.id_post, c.id_utilisateur,
                    p.titre AS post_titre, p.description AS post_description
             FROM commentaires c
             INNER JOIN posts p ON c.id_post = p.id_post
             WHERE c.id_post = :post
             ORDER BY c.created_at ASC'
        );

        $stmt->execute(['post' => $idPost]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            $commentaire->getIdUtilisateur(),
            $commentaire->getIdPost(),
            $commentaire->getDetails()
        );
    }

}
