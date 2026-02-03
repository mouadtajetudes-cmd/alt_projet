INSERT INTO niveaux (nom, description, points) VALUES
  ('Débutant', 'Niveau de base', 0),
  ('Intermédiaire', 'Quelques points acquis', 100),
  ('Avancé', 'Bon niveau', 250),
  ('Expert', 'Très bon niveau', 500),
  ('Maître', 'Niveau ultime', 1000);

INSERT INTO avatars (nom, image, id_utilisateur) VALUES
  ('Héros', 'hero.png', 1),
  ('Mage', 'mage.png', 2);

INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) VALUES
  ('Le Brave', 1, 1, 1),
  ('Le Sage', 2, 2, 2);
