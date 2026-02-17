<?php

namespace app\models;

use PDO;

class CategorieBesoin
{
    public $db;
    public $id;
    public $label;

    public function __construct($db = null)
    {
        $this->db = $db;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    public function create()
    {
        $query = $this->db->prepare(
            "INSERT INTO BNGRC_categorie_besoin (label) VALUES (:label)"
        );

        if ($query->execute([':label' => $this->label])) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    public function read($id)
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_categorie_besoin WHERE id = :id");
        $query->execute([':id' => $id]);

        $data = $query->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $this->id = $data['id'];
            $this->label = $data['label'];
            return $this;
        }
        return null;
    }

    public function readAll()
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_categorie_besoin ORDER BY label ASC");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = $this->db->prepare(
            "UPDATE BNGRC_categorie_besoin SET label = :label WHERE id = :id"
        );

        return $query->execute([
            ':id' => $this->id,
            ':label' => $this->label
        ]);
    }

    public function delete($id)
    {
        $query = $this->db->prepare("DELETE FROM BNGRC_categorie_besoin WHERE id = :id");
        return $query->execute([':id' => $id]);
    }

    public function getArticles()
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_article WHERE id_categorie = :id_categorie");
        $query->execute([':id_categorie' => $this->id]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
