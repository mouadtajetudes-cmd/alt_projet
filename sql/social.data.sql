
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

-- Medias sociaux (IDs 11-24 car marketplace occupe 1-10)
INSERT INTO medias (titre, url, type) VALUES
  ('img1.jpeg', '/uploads/images/img1.jpeg', 'image'),
  ('img2.jpeg', '/uploads/images/img2.jpeg', 'image'),
  ('img3.png',  '/uploads/images/img3.png',  'image'),
  ('img4.png',  '/uploads/images/img4.png',  'image'),
  ('Document 1','/uploads/docs/document1.pdf','texte'),
  ('img5.png',  '/uploads/images/img5.png',  'image'),
  ('img6.PNG',  '/uploads/images/img6.png',  'image'),
  ('video1.mp4','/uploads/videos/video1.mp4','video'),
  ('video2.mp4','/uploads/videos/video2.mp4','video'),
  ('video3.mp4','/uploads/videos/video3.mp4','video'),
  ('video4.mp4','/uploads/videos/video4.mp4','video'),
  ('video5.mp4','/uploads/videos/video5.mp4','video'),
  ('video6.mp4','/uploads/videos/video6.mp4','video'),
  ('video7.mp4','/uploads/videos/video7.mp4','video');

-- Liens post <-> medias (IDs medias décalés de 10 car marketplace occupe 1-10)
INSERT INTO post_medias (id_media, id_post) VALUES
  (11, 1),
  (12, 1),
  (11, 2),
  (13, 3),
  (14, 4),
  (15, 5),
  (16, 6),
  (12, 7),
  (13, 8),
  (11, 9),
  (17, 2),
  (18, 4),
  (19, 2),
  (20, 10),
  (23, 5),
  (24, 8);

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
INSERT INTO likes (id_post, id_utilisateur) VALUES (1, 1) ON CONFLICT (id_post, id_utilisateur) DO NOTHING;
INSERT INTO likes (id_post, id_utilisateur) VALUES (1, 2) ON CONFLICT (id_post, id_utilisateur) DO NOTHING;
INSERT INTO likes (id_post, id_utilisateur) VALUES (1, 3) ON CONFLICT (id_post, id_utilisateur) DO NOTHING;
INSERT INTO likes (id_post, id_utilisateur) VALUES (2, 2) ON CONFLICT (id_post, id_utilisateur) DO NOTHING;
INSERT INTO likes (id_post, id_utilisateur) VALUES (2, 4) ON CONFLICT (id_post, id_utilisateur) DO NOTHING;
INSERT INTO likes (id_post, id_utilisateur) VALUES (3, 1) ON CONFLICT (id_post, id_utilisateur) DO NOTHING;
INSERT INTO likes (id_post, id_utilisateur) VALUES (3, 3) ON CONFLICT (id_post, id_utilisateur) DO NOTHING;
INSERT INTO likes (id_post, id_utilisateur) VALUES (4, 2) ON CONFLICT (id_post, id_utilisateur) DO NOTHING;
INSERT INTO likes (id_post, id_utilisateur) VALUES (4, 2) ON CONFLICT (id_post, id_utilisateur) DO NOTHING;
INSERT INTO likes (id_post, id_utilisateur) VALUES (5, 1) ON CONFLICT (id_post, id_utilisateur) DO NOTHING;
INSERT INTO likes (id_post, id_utilisateur) VALUES (6, 3) ON CONFLICT (id_post, id_utilisateur) DO NOTHING;
INSERT INTO likes (id_post, id_utilisateur) VALUES (6, 4) ON CONFLICT (id_post, id_utilisateur) DO NOTHING;
INSERT INTO likes (id_post, id_utilisateur) VALUES (7, 3) ON CONFLICT (id_post, id_utilisateur) DO NOTHING;
