INSERT INTO BNGRC_config (frais_achat) VALUES (10.00);

-- On reçoit 5 000 000 Ar de dons en argent
INSERT INTO BNGRC_don_collecte (id_article, quantite_recue, donateur) 
VALUES (5, 5000000, 'Donateur Généreux');

-- Ville A : Besoin de Riz (ID 1), mais on a du stock en don
INSERT INTO BNGRC_besoin_ville (id_ville, id_article, quantite_demandee) VALUES (1, 1, 100);
INSERT INTO BNGRC_don_collecte (id_article, quantite_recue) VALUES (1, 50); -- Stock existant !

-- Ville B : Besoin de Tôles (ID 3), aucun stock en don
INSERT INTO BNGRC_besoin_ville (id_ville, id_article, quantite_demandee) VALUES (2, 3, 20);

-- Achat pour la Ville B (Farafangana)
INSERT INTO BNGRC_achat (id_ville, id_article, quantite, montant_argent_utilise) 
VALUES (2, 3, 10, 495000.00);

-- REquete pour la page AJAX: Actualiser
SELECT 
    -- 1. Besoins totaux en montant (Prix unitaire * Quantité demandée)
    SUM(A.prix_unitaire * BV.quantite_demandee) AS montant_total_besoins,

    -- 2. Montant des besoins satisfaits (via distributions OU achats)
    (
        SELECT COALESCE(SUM(D.quantite_attribuee * Art.prix_unitaire), 0)
        FROM BNGRC_distribution D
        JOIN BNGRC_besoin_ville BV2 ON D.id_besoin_ville = BV2.id
        JOIN BNGRC_article Art ON BV2.id_article = Art.id
    ) + 
    (
        SELECT COALESCE(SUM(quantite * Art2.prix_unitaire), 0)
        FROM BNGRC_achat Ach
        JOIN BNGRC_article Art2 ON Ach.id_article = Art2.id
    ) AS montant_satisfait,

    -- 3. Montant restant
    (SUM(A.prix_unitaire * BV.quantite_demandee) - 
        (SELECT COALESCE(SUM(D.quantite_attribuee * Art.prix_unitaire), 0) FROM BNGRC_distribution D JOIN BNGRC_besoin_ville BV2 ON D.id_besoin_ville = BV2.id JOIN BNGRC_article Art ON BV2.id_article = Art.id) -
        (SELECT COALESCE(SUM(quantite * Art2.prix_unitaire), 0) FROM BNGRC_achat Ach JOIN BNGRC_article Art2 ON Ach.id_article = Art2.id)
    ) AS montant_restant

FROM BNGRC_besoin_ville BV
JOIN BNGRC_article A ON BV.id_article = A.id;

Fonctions à créer :

getFraisConfig() : Récupère la valeur dans BNGRC_config.

getDonRestant(id_article) : Calcule (Dons reçus - Distributions faites) pour un article précis.

saveAchat(...) : Insère la ligne d'achat.