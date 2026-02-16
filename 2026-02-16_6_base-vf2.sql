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
