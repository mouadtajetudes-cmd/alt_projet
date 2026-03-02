-- ============================================
-- Migration: Add missing social tables and user profile fields
-- Run this on alt.db database
-- ============================================

-- Add missing columns to utilisateurs table (if they don't exist)
DO $$ 
BEGIN
    -- Add bio column
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns 
                   WHERE table_name='utilisateurs' AND column_name='bio') THEN
        ALTER TABLE utilisateurs ADD COLUMN bio TEXT;
    END IF;
    
    -- Add banner_url column
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns 
                   WHERE table_name='utilisateurs' AND column_name='banner_url') THEN
        ALTER TABLE utilisateurs ADD COLUMN banner_url TEXT;
    END IF;
    
    -- Add statut_personnalise column
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns 
                   WHERE table_name='utilisateurs' AND column_name='statut_personnalise') THEN
        ALTER TABLE utilisateurs ADD COLUMN statut_personnalise VARCHAR(255);
    END IF;
END $$;

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

-- table:blocages (user blocks)
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

-- Display success message
DO $$ 
BEGIN 
    RAISE NOTICE 'Migration completed successfully!';
END $$;
