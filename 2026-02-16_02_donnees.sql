-- Régions
INSERT INTO region (nom) VALUES ('Analamanga'), ('Atsinanana'), ('Vakinankaratra');

-- Villes
INSERT INTO ville (nom, id_region) VALUES 
('Antananarivo', 1), ('Ambohidratrimo', 1), 
('Toamasina', 2), ('Brickaville', 2),
('Antsirabe', 3);

-- Catégories de besoins
INSERT INTO categorie_besoin (label) VALUES ('Produits de première nécessité'), ('Matériaux de construction'), ('Aide Financière');


-- Articles
INSERT INTO article (id_categorie, label, prix_unitaire) VALUES 
(1, 'Riz (kg)', 3200),
(1, 'Huile (Litre)', 9000),
(1, 'Savon (pièce)', 1500),
(2, 'Tôle (feuille)', 45000),
(2, 'Clous (kg)', 8000),
(3, 'Aide monétaire (Ar)', 1); -- Prix unitaire à 1 pour faciliter le calcul du montant total


-- Besoins des villes
INSERT INTO besoin_ville (id_ville, id_article, quantite_demandee) VALUES 
(3, 1, 5000),  -- Toamasina a besoin de 5000kg de riz
(3, 4, 200),   -- Toamasina a besoin de 200 tôles
(4, 2, 300),   -- Brickaville a besoin de 300L d'huile
(1, 6, 1000000); -- Antananarivo a besoin de 1.000.000 Ar


-- Collecte des dons
INSERT INTO don_collecte (id_article, quantite_reçue, date_reception, donateur) VALUES 
(1, 2000, '2024-03-01 08:30:00', 'Particulier'),
(1, 4000, '2024-03-02 10:00:00', 'ONG Aide'),
(4, 150, '2024-03-02 14:00:00', 'Quincaillerie Centrale'),
(6, 500000, '2024-03-03 09:15:00', 'Banque Mondiale');


SELECT 
    v.nom AS Ville, 
    a.label AS Article, 
    bv.quantite_demandee AS Besoins,
    IFNULL(SUM(d.quantite_attribuee), 0) AS Recu,
    (bv.quantite_demandee - IFNULL(SUM(d.quantite_attribuee), 0)) AS Reste_a_combler
FROM besoin_ville bv
JOIN ville v ON bv.id_ville = v.id
JOIN article a ON bv.id_article = a.id
LEFT JOIN distribution d ON d.id_besoin_ville = bv.id
GROUP BY bv.id;