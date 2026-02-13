CREATE TABLE IF NOT EXISTS categories (
    id_categorie SERIAL PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS medias (
    id_media SERIAL PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    url TEXT NOT NULL,
    type VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS produits (
    id_produit SERIAL PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL,
    statut VARCHAR(50) DEFAULT 'disponible',
    date_publication TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    quantite INTEGER DEFAULT 0,
    id_utilisateur INTEGER NOT NULL,
    id_categorie INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (id_categorie) REFERENCES categories(id_categorie) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS produit_medias (
    id_media INTEGER NOT NULL,
    id_produit INTEGER NOT NULL,
    ordre INTEGER DEFAULT 0,
    PRIMARY KEY (id_media, id_produit),
    FOREIGN KEY (id_media) REFERENCES medias(id_media) ON DELETE CASCADE,
    FOREIGN KEY (id_produit) REFERENCES produits(id_produit) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_produits_categorie ON produits(id_categorie);
CREATE INDEX IF NOT EXISTS idx_produits_utilisateur ON produits(id_utilisateur);
CREATE INDEX IF NOT EXISTS idx_produits_statut ON produits(statut);
CREATE INDEX IF NOT EXISTS idx_produits_date ON produits(date_publication);
