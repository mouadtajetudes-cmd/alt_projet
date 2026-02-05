
-- Posts 
INSERT INTO posts (titre, description, date_publication, id_utilisateur) VALUES
  ('Post 1', 'Contenu du post 1', '2026-02-03 10:00:00', 1),
  ('Post 2', 'Contenu du post 2', '2026-02-03 11:00:00', 2),
  ('Post 3', 'Contenu du post 3', '2026-02-03 12:00:00', 3),
  ('Post 4', 'Contenu du post 4', '2026-02-03 13:00:00', 4),
  ('Post 5', 'Contenu du post 5', '2026-02-03 14:00:00', 1),
  ('Post 6', 'Contenu du post 6', '2026-02-03 15:00:00', 2),
  ('Post 7', 'Contenu du post 7', '2026-02-03 16:00:00', 3),
  ('Post 8', 'Contenu du post 8', '2026-02-03 17:00:00', 4),
  ('Post 9', 'Contenu du post 9', '2026-02-03 18:00:00', 1),
  ('Post 10', 'Contenu du post 10', '2026-02-03 19:00:00', 3);

-- Medias
INSERT INTO medias (titre) VALUES
  ('Image 1'),
  ('Image 2'),
  ('Image 3'),
  ('Video 1'),
  ('Document 1'),
  ('GIF 1');

-- Liens post <-> medias 
INSERT INTO post_medias (id_media, id_post) VALUES
  (1, 1),
  (2, 1),
  (1, 2),
  (3, 3),
  (4, 4),
  (5, 5),
  (6, 6),
  (2, 7),
  (3, 8),
  (1, 9);

-- Commentaires 
INSERT INTO commentaires (details, id_utilisateur, id_post, created_at) VALUES
  ('Très bon post!', 2, 1, '2026-02-03 11:00:00'),
  ('Merci pour le partage', 3, 1, '2026-02-03 11:10:00'),
  ('Je suis d''accord', 1, 2, '2026-02-03 12:00:00'),
  ('Super utile', 4, 3, '2026-02-03 13:00:00'),
  ('J''aime bien', 2, 4, '2026-02-03 14:00:00'),
  ('Encore plus de détails svp', 3, 5, '2026-02-03 15:00:00'),
  ('Très intéressant', 1, 6, '2026-02-03 16:00:00'),
  ('Bonne question', 4, 7, '2026-02-03 17:00:00'),
  ('Réponse: voici...', 2, 8, '2026-02-03 18:00:00'),
  ('Merci!', 3, 9, '2026-02-03 19:00:00');

-- Reactions 
INSERT INTO reactions (type, id_utilisateur, id_post, created_at) VALUES
  ('like', 2, 1, '2026-02-03 11:05:00'),
  ('unlike', 3, 1, '2026-02-03 11:15:00'),
  ('like', 1, 2, '2026-02-03 12:05:00'),
  ('unlike', 4, 3, '2026-02-03 13:05:00'),
  ('like', 2, 4, '2026-02-03 14:05:00'),
  ('unlike', 3, 5, '2026-02-03 15:05:00'),
  ('like', 1, 6, '2026-02-03 16:05:00'),
  ('unlike', 4, 7, '2026-02-03 17:05:00'),
  ('like', 2, 8, '2026-02-03 18:05:00'),
  ('like', 3, 9, '2026-02-03 19:05:00');