-- ============================================
-- ALT Friends System - Database Schema
-- ============================================

-- Table: amities (friendships)
CREATE TABLE IF NOT EXISTS amities (
    id_amitie SERIAL PRIMARY KEY,
    utilisateur_id INTEGER NOT NULL REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    ami_id INTEGER NOT NULL REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    statut VARCHAR(20) DEFAULT 'en_attente' CHECK (statut IN ('en_attente', 'accepte', 'refuse', 'bloque')),
    date_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_reponse TIMESTAMP,
    -- Constraint: pas de double demande
    CONSTRAINT unique_friendship UNIQUE (utilisateur_id, ami_id),
    -- Constraint: pas d'amitié avec soi-même
    CONSTRAINT no_self_friendship CHECK (utilisateur_id != ami_id)
);

-- Index pour performance
CREATE INDEX IF NOT EXISTS idx_amities_utilisateur ON amities(utilisateur_id);
CREATE INDEX IF NOT EXISTS idx_amities_ami ON amities(ami_id);
CREATE INDEX IF NOT EXISTS idx_amities_statut ON amities(statut);

-- Vue pour obtenir tous les amis acceptés (bidirectionnel)
CREATE OR REPLACE VIEW amis_acceptes AS
SELECT DISTINCT
    CASE 
        WHEN a.utilisateur_id < a.ami_id THEN a.utilisateur_id
        ELSE a.ami_id
    END as user1_id,
    CASE 
        WHEN a.utilisateur_id < a.ami_id THEN a.ami_id
        ELSE a.utilisateur_id
    END as user2_id,
    a.date_reponse as date_amitie
FROM amities a
WHERE a.statut = 'accepte';

-- Fonction pour vérifier si deux users sont amis
CREATE OR REPLACE FUNCTION sont_amis(user1_id INTEGER, user2_id INTEGER)
RETURNS BOOLEAN AS $$
BEGIN
    RETURN EXISTS (
        SELECT 1 FROM amities
        WHERE statut = 'accepte'
        AND (
            (utilisateur_id = user1_id AND ami_id = user2_id)
            OR (utilisateur_id = user2_id AND ami_id = user1_id)
        )
    );
END;
$$ LANGUAGE plpgsql;

COMMENT ON TABLE amities IS 'Table des relations d''amitié entre utilisateurs';
COMMENT ON COLUMN amities.statut IS 'en_attente: demande envoyée, accepte: amis, refuse: demande refusée, bloque: utilisateur bloqué';
