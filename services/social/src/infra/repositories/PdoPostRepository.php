<?php

namespace alt\infra\repositories;

use alt\core\application\ports\api\CreatePostDTO;
use alt\core\application\ports\api\UpdateAdDTO;
use alt\core\application\ports\api\UpdatePostDTO;
use alt\core\domain\entities\Post;
use alt\core\repositories\PostRepositoryInterface;
use PDO;

class PdoPostRepository implements PostRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

public function findAll(int $page, int $limit): array
{
    $offset = ($page - 1) * $limit;

    $stmt = $this->pdo->prepare(
        'SELECT p.id_post,
                p.description,
                p.date_publication,
                p.is_draft,
                u.id_utilisateur,
                u.nom,
                u.prenom,
                u.banner_url,
                m.id_media,
                m.titre,
                m.url AS media_url,
                m.type AS media_type
         FROM posts p
         JOIN utilisateurs u ON p.id_utilisateur = u.id_utilisateur
         LEFT JOIN post_medias pm ON p.id_post = pm.id_post
         LEFT JOIN medias m ON pm.id_media = m.id_media
         WHERE P.is_draft=TRUE
         ORDER BY p.date_publication DESC
         LIMIT :limit OFFSET :offset'
    );

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as &$row) {

        if ($row['media_type'] === 'image') {
            $row['media_url'] = 'http://localhost:6085/uploads/images/' . $row['titre'];
        }

        if ($row['media_type'] === 'video') {
            $row['media_url'] = 'http://localhost:6085/uploads/videos/' . $row['titre'];
        }
    }

    return $rows;
}    public function findById(int $idPost): Post
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_post, titre, description, date_publication, id_utilisateur,is_draft
             FROM posts
             WHERE id_post = :id'
        );

        $stmt->execute(['id' => $idPost]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new \RuntimeException('Post introuvable');
        }

        return new Post(
        (int) $row['id_post'],
        $row['is_draft'],
        $row['description'],
        (int) $row['id_utilisateur'],
        $row['titre'],
    );
    }

    public function findByIdWithStats(int $idPost): array
    {
        $post = $this->findById($idPost);

        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM reactions WHERE id_post = :id'
        );
        $stmt->execute(['id' => $idPost]);
        $reactions = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM commentaires WHERE id_post = :id'
        );
        $stmt->execute(['id' => $idPost]);
        $comments =$stmt->fetchColumn();

        return [
            'post' => $post,
            'stats' => [
                'reactions' => $reactions,
                'comments' => $comments,
            ],
        ];
    }

public function create(CreatePostDTO $postDTO, ?array $file = null): Post
{
    try {
        $this->pdo->beginTransaction();

        $stmt = $this->pdo->prepare(
            'INSERT INTO posts (description, id_utilisateur,is_draft)
             VALUES (:description, :id_utilisateur,:is_draft)
             RETURNING id_post, description, id_utilisateur,is_draft'
        );
        $stmt->bindValue(':description', $postDTO->getDescription(), PDO::PARAM_STR);
        $stmt->bindValue(':id_utilisateur', $postDTO->getIdUtilisateur(), PDO::PARAM_INT);
        $stmt->bindValue(':is_draft', $postDTO->getIsDraft(), PDO::PARAM_BOOL);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $postId = (int) $row['id_post'];

        $titre = null;
        $mediaType = null;
        $mediaUrl = null;

        if ($file !== null) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO medias (titre, type)
                 VALUES (:titre, :type)
                 RETURNING id_media, titre, type'
            );
            $stmt->execute([
                'titre' => $file['name'],
                'type' => $file['type']
            ]);
            $mediaRow = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $this->pdo->prepare(
                'INSERT INTO post_medias (id_post, id_media)
                 VALUES (:post, :media)'
            );
            $stmt->execute([
                'post' => $postId,
                'media' => $mediaRow['id_media']
            ]);

            $titre = $mediaRow['titre'];
            $mediaType = $mediaRow['type'];
           $mediaUrl = isset($file['folder'], $file['name'])
                ? "http://localhost:6085/uploads/{$file['folder']}/{$file['name']}"
                : null;
        }

        $this->pdo->commit();

        return new Post(
            $postId,
            $row['is_draft'] ,                
            $row['description'],       
            (int)$row['id_utilisateur'], 
            $titre,                   
            $mediaType,
            $mediaUrl              
        );
    } catch (\Exception $e) {
        $this->pdo->rollBack();
        throw $e;
    }
}   public function findByUserPosts(int $idUser): array
{
    $sql = "SELECT 
                p.*, 
                m.url AS media_url,
                m.type AS media_type,
                m.titre,
                u.id_utilisateur,
                u.nom,
                u.prenom,
                u.id_avatar,
                u.banner_url,
                (SELECT COUNT(*) FROM likes l WHERE l.id_post = p.id_post) AS likes_count
            FROM posts p
            JOIN utilisateurs u ON p.id_utilisateur = u.id_utilisateur
            LEFT JOIN post_medias pm ON p.id_post = pm.id_post
            LEFT JOIN medias m ON pm.id_media = m.id_media      
            WHERE p.id_utilisateur = :idUser AND p.is_draft= true
            ORDER BY p.date_publication DESC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as &$row) {

        if ($row['media_type'] === 'image') {
            $row['media_url'] = 'http://localhost:6085/uploads/images/' . $row['titre'];
        }

        if ($row['media_type'] === 'video') {
            $row['media_url'] = 'http://localhost:6085/uploads/videos/' . $row['titre'];
        }
    }

    return $rows;
}
public function delete(int $idPost, int $currentUserId): bool
{
    $this->pdo->beginTransaction();

    try {
        $stmt = $this->pdo->prepare('SELECT id_utilisateur FROM posts WHERE id_post = :id');
        $stmt->execute(['id' => $idPost]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$post) return false;

        if ((int)$post['id_utilisateur'] !== $currentUserId) {
            throw new \RuntimeException("Non autorisé");
        }

        $stmt = $this->pdo->prepare('SELECT id_media FROM post_medias WHERE id_post = :id');
        $stmt->execute(['id' => $idPost]);
        $medias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare('DELETE FROM post_medias WHERE id_post = :id');
        $stmt->execute(['id' => $idPost]);

        if (!empty($medias)) {
            $ids = array_column($medias, 'id_media');
            $in  = str_repeat('?,', count($ids) - 1) . '?';
            $stmt = $this->pdo->prepare("DELETE FROM medias WHERE id_media IN ($in)");
            $stmt->execute($ids);
        }

        $stmt = $this->pdo->prepare('DELETE FROM posts WHERE id_post = :id');
        $stmt->execute(['id' => $idPost]);

        $this->pdo->commit();

        return true;

    } catch (\Exception $e) {
        $this->pdo->rollBack();
        throw $e;
    }
}
public function update(int $idPost,UpdatePostDTO $postDTO,int $currentUserId,?array $file = null): Post {

    $this->pdo->beginTransaction();

    try {

        $stmt = $this->pdo->prepare(
            'SELECT * FROM posts WHERE id_post = :id'
        );
        $stmt->execute(['id' => $idPost]);

        $existingPost = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$existingPost) {
            throw new \RuntimeException('Post introuvable');
        }

        if ((int)$existingPost['id_utilisateur'] !== $currentUserId) {
            throw new \RuntimeException('Non autorisé');
        }

        $description = $postDTO->getDescription() ?? $existingPost['description'];
        $isdraft=(bool) $postDTO->getIsDraft();

        $stmt = $this->pdo->prepare(
            'UPDATE posts
             SET description = :description,is_draft
             WHERE id_post = :id
             RETURNING id_post, description, id_utilisateur'
        );

        $stmt->execute([
            'id_draft'=>$isdraft,
            'description' => $description,
            'id' => $idPost
        ]);

        $postRow = $stmt->fetch(PDO::FETCH_ASSOC);

        $titre = null;
        $mediaType = null;
        $mediaUrl = null;

        if($file !== null && isset($file['tmp_name'], $file['name'])) {

            $stmt = $this->pdo->prepare(
                'SELECT m.id_media, m.titre
                 FROM medias m
                 JOIN post_medias pm ON pm.id_media = m.id_media
                 WHERE pm.id_post = :id'
            );

            $stmt->execute(['id' => $idPost]);
            $oldMedia = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($oldMedia) {

                $oldPath = __DIR__ . '/../../api/uploads/'. $file['folder'] .'/' . $oldMedia['titre'];

                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }

                $stmt = $this->pdo->prepare(
                    'DELETE FROM post_medias WHERE id_post = :id'
                );
                $stmt->execute(['id' => $idPost]);

                $stmt = $this->pdo->prepare(
                    'DELETE FROM medias WHERE id_media = :id'
                );
                $stmt->execute(['id' => $oldMedia['id_media']]);
            }

            $uploadDir = __DIR__ . '/../../api/uploads/'. $file['folder'] .'/';
            if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
                  mkdir($uploadDir, 0777, true);
            }
            $fileName = uniqid() . '_' . basename($file['name']);
            $destination = $uploadDir . $fileName;

            move_uploaded_file($file['tmp_name'], $destination);

            $stmt = $this->pdo->prepare(
                'INSERT INTO medias (titre, type)
                 VALUES (:titre, :type)
                 RETURNING id_media, titre, type'
            );

            $stmt->execute([
                'titre' => $fileName,
                'type' => $file['type']
            ]);

            $mediaRow = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $this->pdo->prepare(
                'INSERT INTO post_medias (id_post, id_media)
                 VALUES (:post, :media)'
            );

            $stmt->execute([
                'post' => $idPost,
                'media' => $mediaRow['id_media']
            ]);
            $titre = $mediaRow['titre'];
            $mediaType = $mediaRow['type'];

            $mediaUrl = "http://localhost:6085/uploads/" . $file['folder'] . "/" . $fileName;
        }
    

        $this->pdo->commit();

        return new Post(
            (int)$postRow['id_post'],
            $postRow['is_draft'],
            $postRow['description'],
            (int)$currentUserId,
            $titre,
            $mediaType,
            $mediaUrl
        );

    } catch (\Exception $e) {

        $this->pdo->rollBack();
        throw $e;
    }
}
public function findDrafts(int $idUser): array
{
    $sql = "
        SELECT 
            p.id_post,
            p.description,
            p.date_publication,
            m.titre,
            m.url AS media_url,
            m.type AS media_type
        FROM posts p
        LEFT JOIN post_medias pm ON p.id_post = pm.id_post
        LEFT JOIN medias m ON pm.id_media = m.id_media
        WHERE p.id_utilisateur = :id
        AND p.is_draft = FALSE
        ORDER BY p.date_publication DESC
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['id' => $idUser]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$row) {

        if ($row['media_type'] === 'image') {
            $row['media_url'] = 'http://localhost:6085/uploads/images/' . $row['titre'];
        }

        if ($row['media_type'] === 'video') {
            $row['media_url'] = 'http://localhost:6085/uploads/videos/' . $row['titre'];
        }
    }


    return $rows;
}
public function createDraft(int $idpost): Post
{
    $stmt = $this->pdo->prepare(
        "UPDATE posts 
         SET is_draft = TRUE, updated_at = NOW() 
         WHERE id_post = :id"
    );
    $stmt->execute(['id' => $idpost]);

    $stmt = $this->pdo->prepare(
        "SELECT 
            p.id_post,
            p.description,
            p.is_draft,
            p.date_publication,
            m.url AS media_url,
            m.type AS media_type
        FROM posts p
        LEFT JOIN post_medias pm ON p.id_post = pm.id_post
        LEFT JOIN medias m ON pm.id_media = m.id_media
         WHERE p.id_post = :id"
    );
    $stmt->execute(['id' => $idpost]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        throw new \Exception("Post non trouvé");
    }

    $post = new Post(
        idPost: $data['id_post'],
        isDraft: (bool)$data['is_draft'],
        description: $data['description'],
        idUtilisateur: $data['id_utilisateur'],
        mediaUrl: $data['media_url'] ?? null,
        mediaType: $data['media_type'] ?? null
    );

    return $post;
}
}