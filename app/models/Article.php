<?php

namespace app\models;

use PDO;

class Article
{
    public $db;
    public $id;
    public $id_categorie;
    public $label;
    public $prix_unitaire;

    public function __construct($db = null)
    {
        $this->db = $db;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdCategorie()
    {
        return $this->id_categorie;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getPrixUnitaire()
    {
        return $this->prix_unitaire;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setIdCategorie($id_categorie)
    {
        $this->id_categorie = $id_categorie;
        return $this;
    }

    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    public function setPrixUnitaire($prix_unitaire)
    {
        $this->prix_unitaire = $prix_unitaire;
        return $this;
    }

    public function create()
    {
        $query = $this->db->prepare(
            "INSERT INTO BNGRC_article (id_categorie, label, prix_unitaire) 
             VALUES (:id_categorie, :label, :prix_unitaire)"
        );

        if ($query->execute([
            ':id_categorie' => $this->id_categorie,
            ':label' => $this->label,
            ':prix_unitaire' => $this->prix_unitaire
        ])) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    public function read($id)
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_article WHERE id = :id");
        $query->execute([':id' => $id]);

        $data = $query->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $this->id = $data['id'];
            $this->id_categorie = $data['id_categorie'];
            $this->label = $data['label'];
            $this->prix_unitaire = $data['prix_unitaire'];
            return $this;
        }
        return null;
    }

    public static function readAll($db)
    {
        $query = $db->prepare("SELECT * FROM BNGRC_article ORDER BY label ASC");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readByCategorie($id_categorie)
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_article WHERE id_categorie = :id_categorie ORDER BY label ASC");
        $query->execute([':id_categorie' => $id_categorie]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = $this->db->prepare(
            "UPDATE BNGRC_article SET id_categorie = :id_categorie, label = :label, prix_unitaire = :prix_unitaire WHERE id = :id"
        );

        return $query->execute([
            ':id' => $this->id,
            ':id_categorie' => $this->id_categorie,
            ':label' => $this->label,
            ':prix_unitaire' => $this->prix_unitaire
        ]);
    }

    public function delete($id)
    {
        $query = $this->db->prepare("DELETE FROM BNGRC_article WHERE id = :id");
        return $query->execute([':id' => $id]);
    }

    public function getCategorie()
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_categorie_besoin WHERE id = :id");
        $query->execute([':id' => $this->id_categorie]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getDons()
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_don_collecte WHERE id_article = :id_article");
        $query->execute([':id_article' => $this->id]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalCollected()
    {
        $query = $this->db->prepare("SELECT SUM(quantite_recue) as total FROM BNGRC_don_collecte WHERE id_article = :id_article");
        $query->execute([':id_article' => $this->id]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
}
