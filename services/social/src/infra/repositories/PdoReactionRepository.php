<?php

namespace alt\infra\repositories;

<<<<<<< HEAD
use alt\core\application\ports\api\CreateReactionDTO;
use alt\core\domain\entities\Post;
use alt\core\domain\entities\Reaction;
use alt\core\repositories\ReactionRepositoryInterface;
=======
use alt\core\application\dto\CreateReactionDTO;
use alt\core\application\ports\spi\ReactionRepositoryInterface;
use alt\core\domain\entities\Post;
use alt\core\domain\entities\Reaction;
>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9
use PDO;

class PdoReactionRepository implements ReactionRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
<<<<<<< HEAD
    public function findByPost(int $post): array
=======

    /**
     * Retourne toutes les réactions d’un post
     */
    public function findByPost(Post $post): array
>>>>>>> 12cf330f2b803327b9789fc239e81dd5bfbec9a9
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_reaction, type, id_post, id_utilisateur
             FROM reactions
             WHERE id_post = :post'
        );

        $stmt->execute(['post' => $post->getIdPost()]);

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
           $reactions[] = new Reaction(
            $row['id_reaction'],
            $row['type'],
            $row['id_utilisateur'],
            $post 
        );
        }

        return $reactions;
    }

    public function create(CreateReactionDTO $react):CreateReactionDTO
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO reactions (type, id_post, id_utilisateur)
             VALUES (:type, :post, :user)'
        );

        $stmt->execute([
            'type' => $react->getType(),
            'post' => $react->getPost(),
            'user' => $react->getIdUtilisateur(),
        ]);
        

        return new CreateReactionDTO(
            $react->getPost(),
            $react->getIdUtilisateur(),
            $react->getType()
        );
    }

    public function delete(int $idReaction, int $idUtilisateur): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM reactions
             WHERE id_reaction = :id
             AND id_utilisateur = :user'
        );

        $stmt->execute([
            'id' => $idReaction,
            'user' => $idUtilisateur,
        ]);
    }


}