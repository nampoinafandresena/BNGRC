-- 6. Suivi des achats(pour les besoins non couverts par les dons)
CREATE TABLE BNGRC_achat (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_besoin_ville INT,
    quantite_achetee DECIMAL(15,2),
    montant_article DECIMAL(15,2),
    frais_pourcentage DECIMAL(5,2),
    montant_total DECIMAL(15,2),
    date_achat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_besoin_ville) REFERENCES BNGRC_besoin_ville(id)
);

-- 7. Configuration (pour les frais d'achat)
CREATE TABLE BNGRC_config (
    id INT PRIMARY KEY AUTO_INCREMENT,
    frais_achat DECIMAL(5,2)
);

-- Insertion d'une configuration par défaut
INSERT INTO BNGRC_config (frais_achat)
VALUES (0.10); -- 10% de frais d'achat par défaut

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

-- 6. Suivi des achats
CREATE TABLE BNGRC_achat (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_besoin_ville INT,
    -- On ajoute l'id du don d'argent source pour la traçabilité
    id_don_source_argent INT, 
    quantite_achetee DECIMAL(15,2),
    montant_unitaire_ht DECIMAL(15,2), -- Prix de l'article au moment de l'achat
    frais_pourcentage DECIMAL(5,2),
    montant_total_ttc DECIMAL(15,2),   -- (Quantité * Prix) + Frais
    date_achat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_besoin_ville) REFERENCES BNGRC_besoin_ville(id),
    FOREIGN KEY (id_don_source_argent) REFERENCES BNGRC_don_collecte(id)
);