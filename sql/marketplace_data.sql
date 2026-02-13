INSERT INTO categories (nom, description) VALUES
('Électronique', 'Appareils électroniques, smartphones, ordinateurs et accessoires'),
('Mode & Vêtements', 'Vêtements, chaussures et accessoires de mode'),
('Maison & Jardin', 'Meubles, décoration et équipement pour la maison et le jardin'),
('Sports & Loisirs', 'Équipements sportifs, vélos, fitness et activités de loisirs'),
('Livres & Médias', 'Livres, films, musique et jeux vidéo'),
('Jouets & Enfants', 'Jouets, vêtements et accessoires pour enfants'),
('Automobile & Moto', 'Véhicules, pièces détachées et accessoires automobiles'),
('Beauté & Santé', 'Produits de beauté, cosmétiques et bien-être'),
('Alimentation & Boissons', 'Produits alimentaires, boissons et épicerie fine'),
('Art & Artisanat', 'Œuvres d''art, créations artisanales et fournitures créatives');

INSERT INTO medias (titre, url, type) VALUES
('Smartphone Pro 2025', '/images/products/smartphone-pro.jpg', 'image/jpeg'),
('Laptop Ultra', '/images/products/laptop.jpg', 'image/jpeg'),
('T-Shirt Designer', '/images/products/tshirt.jpg', 'image/jpeg'),
('Canapé Moderne', '/images/products/canape.jpg', 'image/jpeg'),
('Vélo VTT Sport', '/images/products/vtt.jpg', 'image/jpeg'),
('Roman Bestseller', '/images/products/livre.jpg', 'image/jpeg'),
('Jouet Éducatif', '/images/products/jouet.jpg', 'image/jpeg'),
('Voiture Électrique', '/images/products/voiture.jpg', 'image/jpeg'),
('Crème Visage Bio', '/images/products/creme.jpg', 'image/jpeg'),
('Café Premium', '/images/products/cafe.jpg', 'image/jpeg');

INSERT INTO produits (nom, description, prix, statut, quantite, id_utilisateur, id_categorie) VALUES
('Smartphone Pro Max 2025', 'Dernier modèle avec écran OLED, 256GB, 5G et appareil photo 108MP', 899.99, 'disponible', 15, 2, 1),
('Laptop Ultra Performance', 'Ordinateur portable haut de gamme, 32GB RAM, SSD 1TB, écran 15.6"', 1499.99, 'disponible', 8, 2, 1),
('T-Shirt Bio Designer', 'T-shirt en coton bio avec design exclusif, tailles S-XXL', 29.99, 'disponible', 50, 3, 2),
('Canapé d''Angle Moderne', 'Grand canapé confortable 5 places, tissu premium, gris anthracite', 1299.99, 'disponible', 3, 2, 3),
('VTT Sport Carbon', 'Vélo VTT professionnel, cadre carbone, 27 vitesses, suspensions hydrauliques', 2499.99, 'disponible', 5, 3, 4),
('Collection Romans Classiques', 'Coffret de 10 romans classiques français reliés cuir', 149.99, 'disponible', 12, 4, 5),
('Kit Jouets Éducatifs', 'Ensemble de jouets éducatifs pour enfants 3-6 ans, certifié CE', 79.99, 'disponible', 25, 2, 6),
('Casque de Moto Intégral', 'Casque homologué ECE, visière anti-buée, plusieurs coloris', 189.99, 'disponible', 10, 3, 7),
('Coffret Cosmétiques Bio', 'Kit complet de soins visage bio et naturels, testé dermatologiquement', 69.99, 'disponible', 20, 4, 8),
('Grains de Café Premium', 'Café arabica 100% origine Colombie, torréfaction artisanale 1kg', 24.99, 'disponible', 40, 2, 9),
('Tableau Peinture Abstraite', 'Toile originale peinte à la main, 80x60cm, signée par l''artiste', 450.00, 'disponible', 1, 3, 10),
('Montre Connectée Sport', 'Smartwatch avec GPS, cardiofréquencemètre, étanche 50m', 299.99, 'vendu', 0, 2, 1),
('Robe Été Florale', 'Robe légère imprimé floral, 100% coton, tailles 36-44', 59.99, 'disponible', 18, 4, 2);

-- Insert sample product-media associations
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1),
(6, 6, 1),
(7, 7, 1),
(8, 8, 1),
(9, 9, 1),
(10, 10, 1);
