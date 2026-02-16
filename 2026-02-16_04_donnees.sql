-- Insertion des régions et villes
INSERT INTO BNGRC_region (nom) VALUES ('Analamanga'), ('Atsinanana'), ('Vakinankaratra');

INSERT INTO BNGRC_ville (nom, id_region) VALUES 
('Antananarivo', 1), ('Ambohidratrimo', 1), 
('Toamasina', 2), ('Brickaville', 2),
('Antsirabe', 3);

-- Catégories et Articles
INSERT INTO BNGRC_categorie_besoin (label) VALUES ('Nature'), ('Matériaux'), ('Argent');

INSERT INTO BNGRC_article (id_categorie, label, prix_unitaire) VALUES 
(1, 'Riz (kg)', 3200),
(1, 'Huile (Litre)', 9500),
(2, 'Tôle (unité)', 45000),
(2, 'Clous (kg)', 8000),
(3, 'Fonds de secours (Ar)', 1);

-- Simulation des Besoins par ville
INSERT INTO BNGRC_besoin_ville (id_ville, id_article, quantite_demandee, date_demande) VALUES 
(3, 1, 1000, '2024-03-01 08:00:00'), -- Toamasina a besoin de 1000kg Riz
(4, 1, 500, '2024-03-01 09:00:00'),  -- Brickaville a besoin de 500kg Riz
(3, 3, 100, '2024-03-01 10:00:00'),  -- Toamasina a besoin de 100 tôles
(1, 5, 2000000, '2024-03-02 11:00:00'); -- Tana a besoin de 2M d'Ariary

-- Simulation des Dons reçus (Ordre chronologique)
INSERT INTO BNGRC_don_collecte (id_article, quantite_recue, date_reception, donateur) VALUES 
(1, 1200, '2024-03-03 14:00:00', 'Donateur A'), -- 1200kg de riz arrivent
(3, 50, '2024-03-03 15:00:00', 'Entreprise B'),   -- 50 tôles arrivent
(5, 500000, '2024-03-04 09:00:00', 'Anonyme');     -- 500k Ar arrivent