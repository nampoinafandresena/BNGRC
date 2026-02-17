DROP DATABASE BNGRC;

CREATE DATABASE IF NOT EXISTS BNGRC;
USE BNGRC;

-- 1. Localisation
CREATE TABLE BNGRC_region (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL
);


CREATE TABLE BNGRC_ville (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    id_region INT,
    FOREIGN KEY (id_region) REFERENCES BNGRC_region(id)
);

CREATE TABLE BNGRC_categorie_besoin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    label VARCHAR(100) -- Nature, Matériaux, Argent
);

CREATE TABLE BNGRC_article (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_categorie INT,
    label VARCHAR(100),
    prix_unitaire DECIMAL(15, 2) DEFAULT 0,
    FOREIGN KEY (id_categorie) REFERENCES BNGRC_categorie_besoin(id)
);
CREATE TABLE BNGRC_besoin_ville (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_ville INT,
    id_article INT,
    quantite_demandee DECIMAL(15, 2),
    date_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ville) REFERENCES BNGRC_ville(id),
    FOREIGN KEY (id_article) REFERENCES BNGRC_article(id)
);

-- 4. Collecte des dons (Le stock entrant)
CREATE TABLE BNGRC_don_collecte (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_article INT,
    quantite_recue DECIMAL(15, 2),
    date_reception DATETIME DEFAULT CURRENT_TIMESTAMP,
    donateur VARCHAR(100) DEFAULT 'Anonyme',
    FOREIGN KEY (id_article) REFERENCES BNGRC_article(id)
);


-- 5. Suivi du Dispatch (Distribution effective)
CREATE TABLE BNGRC_distribution (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_don INT,
    id_besoin_ville INT,
    quantite_attribuee DECIMAL(15, 2),
    date_attribution TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_don) REFERENCES BNGRC_don_collecte(id),
    FOREIGN KEY (id_besoin_ville) REFERENCES BNGRC_besoin_ville(id)
);

--Comparaison avec 
-- Table pour tracer les achats effectués avec l'argent des dons
CREATE TABLE BNGRC_achat (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_ville INT,
    id_article INT,
    quantite DECIMAL(15,2),
    montant_argent_utilise DECIMAL(15,2), -- (Prix * Qte) + Frais
    date_achat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ville) REFERENCES BNGRC_ville(id),
    FOREIGN KEY (id_article) REFERENCES BNGRC_article(id)
);

-- 7. Configuration (pour les frais d'achat)
CREATE TABLE BNGRC_config (
    id INT PRIMARY KEY AUTO_INCREMENT,
    frais_achat DECIMAL(5,2)
);

-- -- Insertion des régions et villes
-- INSERT INTO BNGRC_region (nom) VALUES ('Analamanga'), ('Atsinanana'), ('Vakinankaratra');

-- INSERT INTO BNGRC_ville (nom, id_region) VALUES 
-- ('Antananarivo', 1), ('Ambohidratrimo', 1), 
-- ('Toamasina', 2), ('Brickaville', 2),
-- ('Antsirabe', 3);


-- -- 2. Nomenclature des articles (Besoins)

-- -- Catégories et Articles
-- INSERT INTO BNGRC_categorie_besoin (label) VALUES ('Nature'), ('Matériaux'), ('Argent');


-- INSERT INTO BNGRC_article (id_categorie, label, prix_unitaire) VALUES 
-- (1, 'Riz (kg)', 3200),
-- (1, 'Huile (Litre)', 9500),
-- (2, 'Tole (unite)', 45000),
-- (2, 'Clous (kg)', 8000),
-- (3, 'Fonds de secours (Ar)', 1);

-- -- 3. Besoins exprimés par ville

-- -- Simulation des Besoins par ville
-- INSERT INTO BNGRC_besoin_ville (id_ville, id_article, quantite_demandee, date_demande) VALUES 
-- (3, 1, 1000, '2024-03-01 08:00:00'), -- Toamasina a besoin de 1000kg Riz
-- (4, 1, 500, '2024-03-01 09:00:00'),  -- Brickaville a besoin de 500kg Riz
-- (3, 3, 100, '2024-03-01 10:00:00'),  -- Toamasina a besoin de 100 tôles
-- (1, 5, 2000000, '2024-03-02 11:00:00'); -- Tana a besoin de 2M d'Ariary



-- -- Simulation des Dons reçus (Ordre chronologique)
-- INSERT INTO BNGRC_don_collecte (id_article, quantite_recue, date_reception, donateur) VALUES 
-- (1, 1200, '2024-03-03 14:00:00', 'Donateur A'), -- 1200kg de riz arrivent
-- (3, 50, '2024-03-03 15:00:00', 'Entreprise B'),   -- 50 tôles arrivent
-- (5, 500000, '2024-03-04 09:00:00', 'Anonyme');     -- 500k Ar arrivent
-- -- On reçoit 5 000 000 Ar de dons en argent
-- INSERT INTO BNGRC_don_collecte (id_article, quantite_recue, donateur) 
-- VALUES (5, 5000000, 'Donateur Généreux');


-- -- Insertion d'une configuration par défaut
-- INSERT INTO BNGRC_config (frais_achat)
-- VALUES (0.10); -- 10% de frais d'achat par défaut
INSERT INTO BNGRC_config (frais_achat) VALUES (10.00);



-- -- Ville A : Besoin de Riz (ID 1), mais on a du stock en don
-- INSERT INTO BNGRC_besoin_ville (id_ville, id_article, quantite_demandee) VALUES (1, 1, 100);
-- INSERT INTO BNGRC_don_collecte (id_article, quantite_recue) VALUES (1, 50); -- Stock existant !

-- -- Ville B : Besoin de Tôles (ID 3), aucun stock en don
-- INSERT INTO BNGRC_besoin_ville (id_ville, id_article, quantite_demandee) VALUES (2, 3, 20);

-- -- Achat pour la Ville B (Farafangana)
-- INSERT INTO BNGRC_achat (id_ville, id_article, quantite, montant_argent_utilise) 
-- VALUES (2, 3, 10, 495000.00);