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

INSERT INTO BNGRC_config (frais_achat) VALUES (10.00);

alter table BNGRC_don_collecte add column id_categorie INT;
alter table BNGRC_don_collecte add foreign key (id_categorie) references BNGRC_categorie_besoin(id);

