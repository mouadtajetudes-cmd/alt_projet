-- Table Salles (chaque salle = conversation de groupe)
CREATE TABLE IF NOT EXISTS salles (
    id_salle SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    id_groupe INTEGER NOT NULL REFERENCES groupes(id_groupe) ON DELETE CASCADE,
    description TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index pour performance
CREATE INDEX IF NOT EXISTS idx_salles_groupe ON salles(id_groupe);
CREATE INDEX IF NOT EXISTS idx_groupes_createur ON groupes(createur_id);

-- Table pour les conversations épinglées (pin feature)
CREATE TABLE IF NOT EXISTS conversations_epinglees (
    id_utilisateur INTEGER NOT NULL REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    conversation_id VARCHAR(24) NOT NULL, -- MongoDB ObjectId as string
    date_epingle TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_utilisateur, conversation_id)
);

-- Fonction pour mettre à jour date_modification automatiquement
CREATE OR REPLACE FUNCTION update_modified_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.date_modification = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Trigger pour auto-update date_modification
CREATE TRIGGER update_groupes_modtime
    BEFORE UPDATE ON groupes
    FOR EACH ROW
    EXECUTE FUNCTION update_modified_column();
