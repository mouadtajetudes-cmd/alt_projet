SET CLIENT_ENCODING TO 'UTF8';

INSERT INTO niveaux (nom, description, points) VALUES
  ('Débutant', 'Niveau de base', 0),
  ('Intermédiaire', 'Quelques points acquis', 100),
  ('Avancé', 'Bon niveau', 250),
  ('Expert', 'Très bon niveau', 500),
  ('Maître', 'Niveau ultime', 1000);

INSERT INTO avatars (nom, image, id_utilisateur) VALUES
  ('Rex le Fidèle', 'chien.png', 0),
  ('Vif-Argent', 'lapin.png', 0),
  ('Grizzly le Sage', 'ours.png', 0),
  ('Nova l''Éclaireur', 'perso5.png', 0),
  ('Aria la Chasseuse', 'perso7.png', 0);

-- REX LE FIDÈLE
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Rex le Jeune', 1, id_avatar, 1 FROM avatars WHERE nom = 'Rex le Fidèle';
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Rex le Gardien', 2, id_avatar, 2 FROM avatars WHERE nom = 'Rex le Fidèle';
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Rex le Protecteur', 3, id_avatar, 3 FROM avatars WHERE nom = 'Rex le Fidèle';
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Rex le Héros', 4, id_avatar, 4 FROM avatars WHERE nom = 'Rex le Fidèle';
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Rex le Légendaire', 5, id_avatar, 5 FROM avatars WHERE nom = 'Rex le Fidèle';

-- VIF-ARGENT
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Lapinou Débutant', 1, id_avatar, 1 FROM avatars WHERE nom = 'Vif-Argent';
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Lapin Agile', 2, id_avatar, 2 FROM avatars WHERE nom = 'Vif-Argent';
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Vif-Argent', 3, id_avatar, 3 FROM avatars WHERE nom = 'Vif-Argent';
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Éclair Blanc', 4, id_avatar, 4 FROM avatars WHERE nom = 'Vif-Argent';
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Fantôme de Lune', 5, id_avatar, 5 FROM avatars WHERE nom = 'Vif-Argent';

-- GRIZZLY LE SAGE
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Ourson Curieux', 1, id_avatar, 1 FROM avatars WHERE nom = 'Grizzly le Sage';
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Ours Expérimenté', 2, id_avatar, 2 FROM avatars WHERE nom = 'Grizzly le Sage';
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Grizzly Puissant', 3, id_avatar, 3 FROM avatars WHERE nom = 'Grizzly le Sage';
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Sage des Bois', 4, id_avatar, 4 FROM avatars WHERE nom = 'Grizzly le Sage';
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Grizzly Ancien', 5, id_avatar, 5 FROM avatars WHERE nom = 'Grizzly le Sage';

-- NOVA L'ÉCLAIREUR
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Nova la Recrue', 1, id_avatar, 1 FROM avatars WHERE nom = 'Nova l''Éclaireur';
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Nova la Scout', 2, id_avatar, 2 FROM avatars WHERE nom = 'Nova l''Éclaireur';
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Nova l''Éclaireur', 3, id_avatar, 3 FROM avatars WHERE nom = 'Nova l''Éclaireur';
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Nova la Pionnière', 4, id_avatar, 4 FROM avatars WHERE nom = 'Nova l''Éclaireur';
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Nova la Visionnaire', 5, id_avatar, 5 FROM avatars WHERE nom = 'Nova l''Éclaireur';

-- ARIA LA CHASSEUSE
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Aria l''Apprentie', 1, id_avatar, 1 FROM avatars WHERE nom = 'Aria la Chasseuse';
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Aria la Tireuse', 2, id_avatar, 2 FROM avatars WHERE nom = 'Aria la Chasseuse';
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Aria la Chasseuse', 3, id_avatar, 3 FROM avatars WHERE nom = 'Aria la Chasseuse';
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Aria la Sniper', 4, id_avatar, 4 FROM avatars WHERE nom = 'Aria la Chasseuse';
INSERT INTO avatars_versions (surnom, level, id_avatar, id_niveau) 
SELECT 'Aria la Légende', 5, id_avatar, 5 FROM avatars WHERE nom = 'Aria la Chasseuse';
