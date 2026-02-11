-- ============================================
-- ALT Auth Service - Database Schema
-- ============================================

-- Table: utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
    id_utilisateur SERIAL PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    telephone VARCHAR(50),
    password VARCHAR(255) NOT NULL,
    administrateur BOOLEAN DEFAULT FALSE,
    premium BOOLEAN DEFAULT FALSE,
    auth_provider VARCHAR(50) DEFAULT 'local',
    points INTEGER DEFAULT 0,
    id_avatar INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: groupes
CREATE TABLE IF NOT EXISTS groupes (
    id_groupe SERIAL PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    niveau VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: membre_groupe
CREATE TABLE IF NOT EXISTS membre_groupe (
    id_groupe INTEGER NOT NULL,
    id_utilisateur INTEGER NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role VARCHAR(50) DEFAULT 'member',
    PRIMARY KEY (id_groupe, id_utilisateur),
    FOREIGN KEY (id_groupe) REFERENCES groupes(id_groupe) ON DELETE CASCADE,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
);

-- Table: publicites
CREATE TABLE IF NOT EXISTS publicites (
    id_publicite SERIAL PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    image TEXT,
    lien TEXT,
    actif BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index pour améliorer les performances
CREATE INDEX idx_users_email ON utilisateurs(email);
CREATE INDEX idx_membre_groupe_user ON membre_groupe(id_utilisateur);
CREATE INDEX idx_membre_groupe_group ON membre_groupe(id_groupe);
