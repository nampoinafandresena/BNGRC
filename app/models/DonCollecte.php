<?php

namespace app\models;

use PDO;

class DonCollecte
{
    public $db;
    public $id;
    public $id_article;
    public $quantite_recue;
    public $date_reception;
    public $donateur;

    public function __construct($db = null)
    {
        $this->db = $db;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdArticle()
    {
        return $this->id_article;
    }

    public function getQuantiteRecue()
    {
        return $this->quantite_recue;
    }

    public function getDateReception()
    {
        return $this->date_reception;
    }

    public function getDonateur()
    {
        return $this->donateur;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setIdArticle($id_article)
    {
        $this->id_article = $id_article;
        return $this;
    }

    public function setQuantiteRecue($quantite_recue)
    {
        $this->quantite_recue = $quantite_recue;
        return $this;
    }

    public function setDateReception($date_reception)
    {
        $this->date_reception = $date_reception;
        return $this;
    }

    public function setDonateur($donateur)
    {
        $this->donateur = $donateur;
        return $this;
    }

    public function create()
    {
        $query = $this->db->prepare(
            "INSERT INTO BNGRC_don_collecte (id_article, quantite_recue, donateur) 
             VALUES (:id_article, :quantite_recue, :donateur)"
        );

        if ($query->execute([
            ':id_article' => $this->id_article,
            ':quantite_recue' => $this->quantite_recue,
            ':donateur' => $this->donateur
        ])) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    public function read($id)
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_don_collecte WHERE id = :id");
        $query->execute([':id' => $id]);

        $data = $query->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $this->id = $data['id'];
            $this->id_article = $data['id_article'];
            $this->quantite_recue = $data['quantite_recue'];
            $this->date_reception = $data['date_reception'];
            $this->donateur = $data['donateur'];
            return $this;
        }
        return null;
    }

    public function readAll()
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_don_collecte ORDER BY date_reception DESC");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readByArticle($id_article)
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_don_collecte WHERE id_article = :id_article ORDER BY date_reception DESC");
        $query->execute([':id_article' => $id_article]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readByDonateur($donateur)
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_don_collecte WHERE donateur = :donateur ORDER BY date_reception DESC");
        $query->execute([':donateur' => $donateur]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = $this->db->prepare(
            "UPDATE BNGRC_don_collecte SET id_article = :id_article, quantite_recue = :quantite_recue, donateur = :donateur WHERE id = :id"
        );

        return $query->execute([
            ':id' => $this->id,
            ':id_article' => $this->id_article,
            ':quantite_recue' => $this->quantite_recue,
            ':donateur' => $this->donateur
        ]);
    }

    public function delete($id)
    {
        $query = $this->db->prepare("DELETE FROM BNGRC_don_collecte WHERE id = :id");
        return $query->execute([':id' => $id]);
    }

    public function getArticle()
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_article WHERE id = :id");
        $query->execute([':id' => $this->id_article]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getDistributions()
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_distribution WHERE id_don = :id_don");
        $query->execute([':id_don' => $this->id]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuantiteDistribuee()
    {
        $query = $this->db->prepare("SELECT SUM(quantite_attribuee) as total FROM BNGRC_distribution WHERE id_don = :id_don");
        $query->execute([':id_don' => $this->id]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function getResteDisponible()
    {
        return $this->quantite_recue - $this->getQuantiteDistribuee();
    }


    public function getQuantiteRestante($id_don)
    {
        // Quantité reçue - somme attribuée
        $query = $this->db->prepare(
            "SELECT 
                dc.quantite_recue - COALESCE(SUM(d.quantite_attribuee), 0) as reste
            FROM BNGRC_don_collecte dc
            LEFT JOIN BNGRC_distribution d ON dc.id = d.id_don
            WHERE dc.id = :id_don
            GROUP BY dc.id"
        );
        $query->execute([':id_don' => $id_don]);
        return $query->fetch(PDO::FETCH_ASSOC)['reste'];
    }

    public function getDonRestant($id_article)
    {
        // Calcule le total des dons reçus pour cet article
        $donQuery = $this->db->prepare(
            "SELECT SUM(quantite_recue) as total_dons FROM BNGRC_don_collecte WHERE id_article = :id_article"
        );
        $donQuery->execute([':id_article' => $id_article]);
        $donResult = $donQuery->fetch(PDO::FETCH_ASSOC);
        $totalDons = $donResult['total_dons'] ?? 0;

        // Calcule le total des distributions faites pour les dons de cet article
        $distQuery = $this->db->prepare(
            "SELECT SUM(d.quantite_attribuee) as total_distributions FROM BNGRC_distribution d
             INNER JOIN BNGRC_don_collecte dc ON d.id_don = dc.id
             WHERE dc.id_article = :id_article"
        );
        $distQuery->execute([':id_article' => $id_article]);
        $distResult = $distQuery->fetch(PDO::FETCH_ASSOC);
        $totalDistributions = $distResult['total_distributions'] ?? 0;

        return $totalDons - $totalDistributions;
    }
}
