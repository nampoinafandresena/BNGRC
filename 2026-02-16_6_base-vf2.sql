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
