-- Insertion des Régions
INSERT INTO BNGRC_region (nom) VALUES ('Atsinanana'), ('Vatovavy'), ('Atsimo-Atsinanana'), ('Diana'), ('Menabe');

-- Insertion des Villes avec liaison Région
INSERT INTO BNGRC_ville (nom, id_region) VALUES 
('Toamasina', (SELECT id FROM BNGRC_region WHERE nom='Atsinanana')),
('Mananjary', (SELECT id FROM BNGRC_region WHERE nom='Vatovavy')),
('Farafangana', (SELECT id FROM BNGRC_region WHERE nom='Atsimo-Atsinanana')),
('Nosy Be', (SELECT id FROM BNGRC_region WHERE nom='Diana')),
('Morondava', (SELECT id FROM BNGRC_region WHERE nom='Menabe'));



-- Insertion des Catégories
INSERT INTO BNGRC_categorie_besoin (label) VALUES ('nature'), ('materiel'), ('argent');

-- Insertion des Articles avec prix unitaires
INSERT INTO BNGRC_article (id_categorie, label, prix_unitaire) VALUES 
((SELECT id FROM BNGRC_categorie_besoin WHERE label='nature'), 'Riz (kg)', 3000),
((SELECT id FROM BNGRC_categorie_besoin WHERE label='nature'), 'Eau (L)', 1000),
((SELECT id FROM BNGRC_categorie_besoin WHERE label='nature'), 'Huile (L)', 6000),
((SELECT id FROM BNGRC_categorie_besoin WHERE label='nature'), 'Haricots', 4000),
((SELECT id FROM BNGRC_categorie_besoin WHERE label='materiel'), 'Tôle', 25000),
((SELECT id FROM BNGRC_categorie_besoin WHERE label='materiel'), 'Bâche', 15000),
((SELECT id FROM BNGRC_categorie_besoin WHERE label='materiel'), 'Clous (kg)', 8000),
((SELECT id FROM BNGRC_categorie_besoin WHERE label='materiel'), 'Bois', 10000),
((SELECT id FROM BNGRC_categorie_besoin WHERE label='materiel'), 'groupe', 6750000),
((SELECT id FROM BNGRC_categorie_besoin WHERE label='argent'), 'Argent', 1);


-- TOAMASINA
INSERT INTO BNGRC_besoin_ville (id_ville, id_article, quantite_demandee, date_demande) VALUES 
((SELECT id FROM BNGRC_ville WHERE nom='Toamasina'), (SELECT id FROM BNGRC_article WHERE label='Riz (kg)'), 800, '2026-02-16'),
((SELECT id FROM BNGRC_ville WHERE nom='Toamasina'), (SELECT id FROM BNGRC_article WHERE label='Eau (L)'), 1500, '2026-02-15'),
((SELECT id FROM BNGRC_ville WHERE nom='Toamasina'), (SELECT id FROM BNGRC_article WHERE label='Tôle'), 120, '2026-02-16'),
((SELECT id FROM BNGRC_ville WHERE nom='Toamasina'), (SELECT id FROM BNGRC_article WHERE label='Bâche'), 200, '2026-02-15'),
((SELECT id FROM BNGRC_ville WHERE nom='Toamasina'), (SELECT id FROM BNGRC_article WHERE label='Argent'), 12000000, '2026-02-16'),
((SELECT id FROM BNGRC_ville WHERE nom='Toamasina'), (SELECT id FROM BNGRC_article WHERE label='groupe'), 3, '2026-02-15');

-- MANANJARY
INSERT INTO BNGRC_besoin_ville (id_ville, id_article, quantite_demandee, date_demande) VALUES 
((SELECT id FROM BNGRC_ville WHERE nom='Mananjary'), (SELECT id FROM BNGRC_article WHERE label='Riz (kg)'), 500, '2026-02-15'),
((SELECT id FROM BNGRC_ville WHERE nom='Mananjary'), (SELECT id FROM BNGRC_article WHERE label='Huile (L)'), 120, '2026-02-16'),
((SELECT id FROM BNGRC_ville WHERE nom='Mananjary'), (SELECT id FROM BNGRC_article WHERE label='Tôle'), 80, '2026-02-15'),
((SELECT id FROM BNGRC_ville WHERE nom='Mananjary'), (SELECT id FROM BNGRC_article WHERE label='Clous (kg)'), 60, '2026-02-16'),
((SELECT id FROM BNGRC_ville WHERE nom='Mananjary'), (SELECT id FROM BNGRC_article WHERE label='Argent'), 6000000, '2026-02-15');

-- FARAFANGANA
INSERT INTO BNGRC_besoin_ville (id_ville, id_article, quantite_demandee, date_demande) VALUES 
((SELECT id FROM BNGRC_ville WHERE nom='Farafangana'), (SELECT id FROM BNGRC_article WHERE label='Riz (kg)'), 600, '2026-02-16'),
((SELECT id FROM BNGRC_ville WHERE nom='Farafangana'), (SELECT id FROM BNGRC_article WHERE label='Eau (L)'), 1000, '2026-02-15'),
((SELECT id FROM BNGRC_ville WHERE nom='Farafangana'), (SELECT id FROM BNGRC_article WHERE label='Bâche'), 150, '2026-02-16'),
((SELECT id FROM BNGRC_ville WHERE nom='Farafangana'), (SELECT id FROM BNGRC_article WHERE label='Bois'), 100, '2026-02-15'),
((SELECT id FROM BNGRC_ville WHERE nom='Farafangana'), (SELECT id FROM BNGRC_article WHERE label='Argent'), 8000000, '2026-02-16');

-- NOSY BE
INSERT INTO BNGRC_besoin_ville (id_ville, id_article, quantite_demandee, date_demande) VALUES 
((SELECT id FROM BNGRC_ville WHERE nom='Nosy Be'), (SELECT id FROM BNGRC_article WHERE label='Riz (kg)'), 300, '2026-02-15'),
((SELECT id FROM BNGRC_ville WHERE nom='Nosy Be'), (SELECT id FROM BNGRC_article WHERE label='Haricots'), 200, '2026-02-16'),
((SELECT id FROM BNGRC_ville WHERE nom='Nosy Be'), (SELECT id FROM BNGRC_article WHERE label='Tôle'), 40, '2026-02-15'),
((SELECT id FROM BNGRC_ville WHERE nom='Nosy Be'), (SELECT id FROM BNGRC_article WHERE label='Clous (kg)'), 30, '2026-02-16'),
((SELECT id FROM BNGRC_ville WHERE nom='Nosy Be'), (SELECT id FROM BNGRC_article WHERE label='Argent'), 4000000, '2026-02-15');

-- MORONDAVA
INSERT INTO BNGRC_besoin_ville (id_ville, id_article, quantite_demandee, date_demande) VALUES 
((SELECT id FROM BNGRC_ville WHERE nom='Morondava'), (SELECT id FROM BNGRC_article WHERE label='Riz (kg)'), 700, '2026-02-16'),
((SELECT id FROM BNGRC_ville WHERE nom='Morondava'), (SELECT id FROM BNGRC_article WHERE label='Eau (L)'), 1200, '2026-02-15'),
((SELECT id FROM BNGRC_ville WHERE nom='Morondava'), (SELECT id FROM BNGRC_article WHERE label='Bâche'), 180, '2026-02-16'),
((SELECT id FROM BNGRC_ville WHERE nom='Morondava'), (SELECT id FROM BNGRC_article WHERE label='Bois'), 150, '2026-02-15'),
((SELECT id FROM BNGRC_ville WHERE nom='Morondava'), (SELECT id FROM BNGRC_article WHERE label='Argent'), 10000000, '2026-02-16');



-- dons
INSERT INTO BNGRC_don_collecte (date_reception, id_categorie, id_article, quantite_recue) VALUES 
-- Dons en Argent (Catégorie 3, Article 10)
('2026-02-16', 3, 10, 5000000),
('2026-02-16', 3, 10, 3000000),
('2026-02-17', 3, 10, 4000000),
('2026-02-17', 3, 10, 1500000),
('2026-02-17', 3, 10, 6000000),
('2026-02-19', 3, 10, 20000000),

-- Dons en Nature (Catégorie 1)
('2026-02-16', 1, 1, 400),    -- Riz (kg)
('2026-02-16', 1, 2, 600),    -- Eau (L)
('2026-02-17', 1, 4, 100),    -- Haricots
('2026-02-18', 1, 1, 2000),   -- Riz (kg)
('2026-02-18', 1, 2, 5000),   -- Eau (L)
('2026-02-17', 1, 4, 88),     -- Haricots

-- Dons en Matériel (Catégorie 2)
('2026-02-17', 2, 5, 50),     -- Tôle
('2026-02-17', 2, 6, 70),     -- Bâche
('2026-02-18', 2, 5, 300),    -- Tôle
('2026-02-19', 2, 6, 500);    -- Bâche