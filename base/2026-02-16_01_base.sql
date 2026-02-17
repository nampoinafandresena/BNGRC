CREATE DATABASE BNGRC;
USE BNGRC;

-- 1. Localisation
CREATE TABLE region (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL
);

CREATE TABLE ville (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    id_region INT,
    FOREIGN KEY (id_region) REFERENCES region(id)
);

-- 2. Nomenclature des articles
CREATE TABLE categorie_besoin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    label VARCHAR(100) -- Nature, Matériaux, Argent
);

CREATE TABLE article (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_categorie INT,
    label VARCHAR(100),
    prix_unitaire DECIMAL(10, 2) DEFAULT 0,
    FOREIGN KEY (id_categorie) REFERENCES categorie_besoin(id)
);

-- 3. Besoins exprimés par les villes
CREATE TABLE besoin_ville (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_ville INT,
    id_article INT,
    quantite_demandee DECIMAL(10, 2),
    date_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ville) REFERENCES ville(id),
    FOREIGN KEY (id_article) REFERENCES article(id)
);

-- 4. Collecte des dons (Entrée en stock)
CREATE TABLE don_collecte (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_article INT,
    quantite_reçue DECIMAL(10, 2),
    date_reception DATETIME DEFAULT CURRENT_TIMESTAMP,
    donateur VARCHAR(100) DEFAULT 'Anonyme',
    FOREIGN KEY (id_article) REFERENCES article(id)
);

-- 5. Distribution (Résultat de la simulation de dispatch)
CREATE TABLE distribution (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_don INT,
    id_besoin_ville INT,
    quantite_attribuee DECIMAL(10, 2),
    date_attribution TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_don) REFERENCES don_collecte(id),
    FOREIGN KEY (id_besoin_ville) REFERENCES besoin_ville(id)
);