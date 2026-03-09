<?php

namespace alt\infra\repositories;

<<<<<<< HEAD
use alt\core\application\ports\api\CreateCommentDTO;
use alt\core\repositories\CommentRepositoryInterface;
=======
use alt\core\application\dto\CreateCommentDTO;
use alt\core\application\ports\spi\CommentRepositoryInterface;
use alt\core\domain\entities\Commentaire;
>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9
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
<<<<<<< HEAD
    'SELECT 
        c.id_commentaire, 
        c.details, 
        c.created_at, 
        c.id_post, 
        c.id_utilisateur,
        p.titre AS post_titre, 
        p.description AS post_description,
        u.nom AS user_nom,
        u.prenom AS user_prenom,
        u.image AS user_image
     FROM commentaires c
     INNER JOIN posts p ON c.id_post = p.id_post
     INNER JOIN utilisateurs u ON c.id_utilisateur = u.id_utilisateur
     WHERE c.id_post = :post
     ORDER BY c.created_at ASC'
);

=======
            'SELECT c.id_commentaire, c.details, c.created_at, c.id_post, c.id_utilisateur,
                    p.titre AS post_titre, p.description AS post_description
             FROM commentaires c
             INNER JOIN posts p ON c.id_post = p.id_post
             WHERE c.id_post = :post
             ORDER BY c.created_at ASC'
        );
>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9

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

<<<<<<< HEAD
=======
        $id = (int) $this->pdo->lastInsertId();

>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9
        return new CreateCommentDTO(
            $commentaire->getIdUtilisateur(),
            $commentaire->getIdPost(),
            $commentaire->getDetails()
        );
    }

}
