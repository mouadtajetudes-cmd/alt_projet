-- ============================================
-- ALT Auth Service - Sample Data
-- ============================================

-- Insert sample users
INSERT INTO utilisateurs (nom, prenom, email, password, administrateur,telephone, premium, points) VALUES
('Admin', 'System', 'admin@alt.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE, '0612345678', TRUE, 1000),
('Doe', 'John', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', FALSE, '0987654321', TRUE, 500),
('Smith', 'Jane', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', FALSE, '0777777777', FALSE, 150),
('Martin', 'Paul', 'paul@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', FALSE, '0611223344', FALSE, 75);

-- Insert sample groups
INSERT INTO groupes (nom, description, niveau) VALUES
('Développeurs', 'Groupe des développeurs ALT', 'advanced'),
('Marketing', 'Équipe marketing', 'intermediate'),
('Support', 'Équipe support client', 'beginner');

-- Insert sample group members
INSERT INTO membre_groupe (id_groupe, id_utilisateur, role) VALUES
(1, 1, 'admin'),
(1, 2, 'member'),
(2, 3, 'admin'),
(3, 4, 'member');

-- Insert sample ads
INSERT INTO publicites (titre, description, image, lien, actif) VALUES
('Premium Account', 'Upgrade to premium for exclusive features!', '/images/premium-ad.jpg', '/premium', TRUE),
('Summer Sale', 'Get 50% off on all products', '/images/summer-sale.jpg', '/marketplace', TRUE),
('New Features', 'Discover our latest updates', '/images/features.jpg', '/features', TRUE);
