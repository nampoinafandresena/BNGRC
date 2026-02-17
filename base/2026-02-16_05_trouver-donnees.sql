SELECT 
    V.nom AS Ville, 
    A.label AS Ressource, 
    BV.quantite_demandee AS Besoins,
    COALESCE(SUM(D.quantite_attribuee), 0) AS "Dons Reçus",
    (BV.quantite_demandee - COALESCE(SUM(D.quantite_attribuee), 0)) AS "Reste à combler"
FROM BNGRC_besoin_ville BV
JOIN BNGRC_ville V ON BV.id_ville = V.id
JOIN BNGRC_article A ON BV.id_article = A.id
LEFT JOIN BNGRC_distribution D ON D.id_besoin_ville = BV.id
GROUP BY BV.id
ORDER BY V.nom ASC;