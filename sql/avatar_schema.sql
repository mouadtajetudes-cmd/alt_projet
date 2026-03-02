CREATE TABLE niveaux (
id_niveau SERIAL PRIMARY KEY,
nom VARCHAR(100) NOT NULL,
description TEXT,
points INTEGER DEFAULT 0
);

CREATE TABLE avatars (
id_avatar SERIAL PRIMARY KEY,
nom VARCHAR(255) NOT NULL,
image TEXT NOT NULL,
id_utilisateur INTEGER NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE avatars_versions (
id_avatar_version SERIAL PRIMARY KEY,
surnom VARCHAR(255),
level INTEGER DEFAULT 1,
id_avatar INTEGER NOT NULL,
id_niveau INTEGER,
FOREIGN KEY (id_avatar) REFERENCES avatars(id_avatar),
FOREIGN KEY (id_niveau) REFERENCES niveaux(id_niveau)
);