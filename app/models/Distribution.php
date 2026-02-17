<?php

namespace app\models;

use PDO;

class Distribution
{
    public $db;
    public $id;
    public $id_don;
    public $id_besoin_ville;
    public $quantite_attribuee;
    public $date_attribution;

    public function __construct($db = null)
    {
        $this->db = $db;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdDon()
    {
        return $this->id_don;
    }

    public function getIdBesoinVille()
    {
        return $this->id_besoin_ville;
    }

    public function getQuantiteAttribuee()
    {
        return $this->quantite_attribuee;
    }

    public function getDateAttribution()
    {
        return $this->date_attribution;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setIdDon($id_don)
    {
        $this->id_don = $id_don;
        return $this;
    }

    public function setIdBesoinVille($id_besoin_ville)
    {
        $this->id_besoin_ville = $id_besoin_ville;
        return $this;
    }

    public function setQuantiteAttribuee($quantite_attribuee)
    {
        $this->quantite_attribuee = $quantite_attribuee;
        return $this;
    }

    public function setDateAttribution($date_attribution)
    {
        $this->date_attribution = $date_attribution;
        return $this;
    }

    public function create()
    {
        $query = $this->db->prepare(
            "INSERT INTO BNGRC_distribution (id_don, id_besoin_ville, quantite_attribuee) 
             VALUES (:id_don, :id_besoin_ville, :quantite_attribuee)"
        );

        if ($query->execute([
            ':id_don' => $this->id_don,
            ':id_besoin_ville' => $this->id_besoin_ville,
            ':quantite_attribuee' => $this->quantite_attribuee
        ])) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    public function read($id)
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_distribution WHERE id = :id");
        $query->execute([':id' => $id]);

        $data = $query->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $this->id = $data['id'];
            $this->id_don = $data['id_don'];
            $this->id_besoin_ville = $data['id_besoin_ville'];
            $this->quantite_attribuee = $data['quantite_attribuee'];
            $this->date_attribution = $data['date_attribution'];
            return $this;
        }
        return null;
    }

    public function readAll()
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_distribution ORDER BY date_attribution DESC");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readByDon($id_don)
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_distribution WHERE id_don = :id_don ORDER BY date_attribution DESC");
        $query->execute([':id_don' => $id_don]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readByBesoinVille($id_besoin_ville)
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_distribution WHERE id_besoin_ville = :id_besoin_ville ORDER BY date_attribution DESC");
        $query->execute([':id_besoin_ville' => $id_besoin_ville]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = $this->db->prepare(
            "UPDATE BNGRC_distribution SET id_don = :id_don, id_besoin_ville = :id_besoin_ville, quantite_attribuee = :quantite_attribuee WHERE id = :id"
        );

        return $query->execute([
            ':id' => $this->id,
            ':id_don' => $this->id_don,
            ':id_besoin_ville' => $this->id_besoin_ville,
            ':quantite_attribuee' => $this->quantite_attribuee
        ]);
    }

    public function delete($id)
    {
        $query = $this->db->prepare("DELETE FROM BNGRC_distribution WHERE id = :id");
        return $query->execute([':id' => $id]);
    }

    public function getDon()
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_don_collecte WHERE id = :id");
        $query->execute([':id' => $this->id_don]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getBesoinVille()
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_besoin_ville WHERE id = :id");
        $query->execute([':id' => $this->id_besoin_ville]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function readByVille($id_ville)
    {
        $query = $this->db->prepare(
            "SELECT d.* FROM BNGRC_distribution d
             INNER JOIN BNGRC_besoin_ville bv ON d.id_besoin_ville = bv.id
             WHERE bv.id_ville = :id_ville ORDER BY d.date_attribution DESC"
        );
        $query->execute([':id_ville' => $id_ville]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readByArticle($id_article)
    {
        $query = $this->db->prepare(
            "SELECT d.* FROM BNGRC_distribution d
             INNER JOIN BNGRC_besoin_ville bv ON d.id_besoin_ville = bv.id
             WHERE bv.id_article = :id_article ORDER BY d.date_attribution DESC"
        );
        $query->execute([':id_article' => $id_article]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
