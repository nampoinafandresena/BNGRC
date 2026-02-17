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