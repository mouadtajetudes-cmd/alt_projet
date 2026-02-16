-- table:posts
CREATE TABLE posts ( 
id_post SERIAL PRIMARY KEY, 
titre VARCHAR(255) NOT NULL, 
description TEXT, 
date_publication TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
id_utilisateur INTEGER NOT NULL, 
FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur)
);
-- table:medias
CREATE TABLE medias ( 
id_media SERIAL PRIMARY KEY, 
titre VARCHAR(255)
);
-- table:post_medias
CREATE TABLE post_medias ( 
id_media INTEGER, 
id_post INTEGER, 
PRIMARY KEY (id_media, id_post), 
FOREIGN KEY (id_media) REFERENCES medias(id_media), 
FOREIGN KEY (id_post) REFERENCES posts(id_post) 
); 
--table:commentaires
CREATE TABLE commentaires ( 
id_commentaire SERIAL PRIMARY KEY, 
details TEXT NOT NULL, 
id_utilisateur INTEGER NOT NULL, 
id_post INTEGER NOT NULL, 
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
FOREIGN KEY (id_post) REFERENCES posts(id_post) ,
FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur)


);
-- table:reactions
CREATE TABLE reactions ( 
id_reaction SERIAL PRIMARY KEY, 
type VARCHAR(50) NOT NULL, 
id_utilisateur INTEGER NOT NULL, 
id_post INTEGER NOT NULL, 
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
FOREIGN KEY (id_post) REFERENCES posts(id_post), 
FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur), 
UNIQUE(id_utilisateur, id_post) 
);

-- table:amities (friendships/relationships)
CREATE TABLE IF NOT EXISTS amities (
    id_amitie SERIAL PRIMARY KEY,
    utilisateur_id INTEGER NOT NULL,
    ami_id INTEGER NOT NULL,
    statut VARCHAR(50) DEFAULT 'en_attente', -- 'en_attente', 'accepte', 'refuse', 'bloque'
    date_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_reponse TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (ami_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    UNIQUE(utilisateur_id, ami_id)
);

-- table:notifications
CREATE TABLE IF NOT EXISTS notifications (
    id_notification SERIAL PRIMARY KEY,
    id_utilisateur INTEGER NOT NULL,
    type VARCHAR(50) NOT NULL, -- 'friend_request', 'friend_accept', 'group_invite', 'message', etc.
    titre VARCHAR(255) NOT NULL,
    contenu TEXT,
    data JSONB,
    lue BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
);

-- table:salles (group channels)
CREATE TABLE IF NOT EXISTS salles (
    id_salle SERIAL PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    id_groupe INTEGER NOT NULL,
    description TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_groupe) REFERENCES groupes(id_groupe) ON DELETE CASCADE
);

-- table:visites_profil (profile visits)
CREATE TABLE IF NOT EXISTS visites_profil (
    id_visite SERIAL PRIMARY KEY,
    id_visiteur INTEGER NOT NULL,
    id_profil_visite INTEGER NOT NULL,
    date_visite TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_visiteur) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (id_profil_visite) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
);

-- table:blocages (user blocks - duplicate with amities.statut='bloque', but can be separate)
CREATE TABLE IF NOT EXISTS blocages (
    id_blocage SERIAL PRIMARY KEY,
    id_bloqueur INTEGER NOT NULL,
    id_bloque INTEGER NOT NULL,
    date_blocage TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_bloqueur) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (id_bloque) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    UNIQUE(id_bloqueur, id_bloque)
);

-- table:conversations_epinglees (pinned conversations)
CREATE TABLE IF NOT EXISTS conversations_epinglees (
    id_epingle SERIAL PRIMARY KEY,
    id_utilisateur INTEGER NOT NULL,
    id_conversation VARCHAR(255) NOT NULL, -- Can be another user's ID or group chat ID
    type_conversation VARCHAR(50) NOT NULL, -- 'direct', 'group', 'salle'
    date_epingle TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    UNIQUE(id_utilisateur, id_conversation, type_conversation)
);

-- Indexes for performance
CREATE INDEX IF NOT EXISTS idx_amities_utilisateur ON amities(utilisateur_id);
CREATE INDEX IF NOT EXISTS idx_amities_ami ON amities(ami_id);
CREATE INDEX IF NOT EXISTS idx_amities_statut ON amities(statut);
CREATE INDEX IF NOT EXISTS idx_notifications_utilisateur ON notifications(id_utilisateur);
CREATE INDEX IF NOT EXISTS idx_notifications_lue ON notifications(lue);
CREATE INDEX IF NOT EXISTS idx_salles_groupe ON salles(id_groupe);
CREATE INDEX IF NOT EXISTS idx_visites_profil_visiteur ON visites_profil(id_visiteur);
CREATE INDEX IF NOT EXISTS idx_visites_profil_visite ON visites_profil(id_profil_visite);

