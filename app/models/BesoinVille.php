<?php

namespace app\models;

use PDO;

class BesoinVille
{
    public $db;
    public $id;
    public $id_ville;
    public $id_article;
    public $quantite_demandee;
    public $date_demande;

    public function __construct($db = null)
    {
        $this->db = $db;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdVille()
    {
        return $this->id_ville;
    }

    public function getIdArticle()
    {
        return $this->id_article;
    }

    public function getQuantiteDemandee()
    {
        return $this->quantite_demandee;
    }

    public function getDateDemande()
    {
        return $this->date_demande;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setIdVille($id_ville)
    {
        $this->id_ville = $id_ville;
        return $this;
    }

    public function setIdArticle($id_article)
    {
        $this->id_article = $id_article;
        return $this;
    }

    public function setQuantiteDemandee($quantite_demandee)
    {
        $this->quantite_demandee = $quantite_demandee;
        return $this;
    }

    public function setDateDemande($date_demande)
    {
        $this->date_demande = $date_demande;
        return $this;
    }

    public function create()
    {
        $query = $this->db->prepare(
            "INSERT INTO BNGRC_besoin_ville (id_ville, id_article, quantite_demandee, date_demande) 
             VALUES (:id_ville, :id_article, :quantite_demandee, now())"
        );

        if ($query->execute([
            ':id_ville' => $this->id_ville,
            ':id_article' => $this->id_article,
            ':quantite_demandee' => $this->quantite_demandee
        ])) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    public function read($id)
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_besoin_ville WHERE id = :id");
        $query->execute([':id' => $id]);

        $data = $query->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $this->id = $data['id'];
            $this->id_ville = $data['id_ville'];
            $this->id_article = $data['id_article'];
            $this->quantite_demandee = $data['quantite_demandee'];
            $this->date_demande = $data['date_demande'];
            return $this;
        }
        return null;
    }

    public static function readAll($db)
    {
        $query = $db->prepare("SELECT * FROM BNGRC_besoin_ville ORDER BY date_demande DESC");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readByVille($id_ville)
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_besoin_ville WHERE id_ville = :id_ville ORDER BY date_demande DESC");
        $query->execute([':id_ville' => $id_ville]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readByArticle($id_article)
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_besoin_ville WHERE id_article = :id_article ORDER BY date_demande DESC");
        $query->execute([':id_article' => $id_article]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = $this->db->prepare(
            "UPDATE BNGRC_besoin_ville SET id_ville = :id_ville, id_article = :id_article, quantite_demandee = :quantite_demandee WHERE id = :id"
        );

        return $query->execute([
            ':id' => $this->id,
            ':id_ville' => $this->id_ville,
            ':id_article' => $this->id_article,
            ':quantite_demandee' => $this->quantite_demandee
        ]);
    }

    public function delete($id)
    {
        $query = $this->db->prepare("DELETE FROM BNGRC_besoin_ville WHERE id = :id");
        return $query->execute([':id' => $id]);
    }

    public function getVille()
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_ville WHERE id = :id");
        $query->execute([':id' => $this->id_ville]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getArticle()
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_article WHERE id = :id");
        $query->execute([':id' => $this->id_article]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getDistributions()
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_distribution WHERE id_besoin_ville = :id_besoin_ville");
        $query->execute([':id_besoin_ville' => $this->id]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuantiteDistribuee()
    {
        $query = $this->db->prepare("SELECT SUM(quantite_attribuee) as total FROM BNGRC_distribution WHERE id_besoin_ville = :id_besoin_ville");
        $query->execute([':id_besoin_ville' => $this->id]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function getResteADistribuer()
    {
        return $this->quantite_demandee - $this->getQuantiteDistribuee();
    }

    public function getEtatGlobalVilles() {
        $sql = "SELECT 
                        V.nom AS ville_nom, 
                        R.nom AS region_nom,
                        A.label AS article_label, 
                        A.prix_unitaire,
                        BV.quantite_demandee,
                        COALESCE(SUM(D.quantite_attribuee), 0) AS quantite_recue,
                        (BV.quantite_demandee - COALESCE(SUM(D.quantite_attribuee), 0)) AS reste
                    FROM BNGRC_besoin_ville BV
                    JOIN BNGRC_ville V ON BV.id_ville = V.id
                    JOIN BNGRC_region R ON V.id_region = R.id
                    JOIN BNGRC_article A ON BV.id_article = A.id
                    LEFT JOIN BNGRC_distribution D ON D.id_besoin_ville = BV.id
                    GROUP BY BV.id, V.nom, R.nom, A.label, A.prix_unitaire, BV.quantite_demandee
                    ORDER BY R.nom, V.nom ASC;";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function getQuantiteRestante($id_besoin)
    {
        // Quantité demandée - somme attribuée
        $query = $this->db->prepare(
            "SELECT 
                bv.quantite_demandee - COALESCE(SUM(d.quantite_attribuee), 0) as reste
            FROM BNGRC_besoin_ville bv
            LEFT JOIN BNGRC_distribution d ON bv.id = d.id_besoin_ville
            WHERE bv.id = :id_besoin
            GROUP BY bv.id"
        );
        $query->execute([':id_besoin' => $id_besoin]);
        return $query->fetch(PDO::FETCH_ASSOC)['reste'];
    }

    public function getPrixUnitaire() {
        $query = $this->db->prepare("SELECT prix_unitaire FROM BNGRC_article WHERE id = :id_article");
        $query->execute([':id_article' => $this->id_article]);
        return $query->fetch(PDO::FETCH_ASSOC)['prix_unitaire'];
    }
}
