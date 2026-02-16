<?php

namespace app\models;

use PDO;

class Region
{
    public $db;
    public $id;
    public $nom;

    public function __construct($db = null)
    {
        $this->db = $db;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
        return $this;
    }

    public function create()
    {
        $query = $this->db->prepare(
            "INSERT INTO BNGRC_region (nom) VALUES (:nom)"
        );

        if ($query->execute([':nom' => $this->nom])) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    public function read($id)
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_region WHERE id = :id");
        $query->execute([':id' => $id]);

        $data = $query->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $this->id = $data['id'];
            $this->nom = $data['nom'];
            return $this;
        }
        return null;
    }

    public function readAll()
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_region ORDER BY nom ASC");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = $this->db->prepare(
            "UPDATE BNGRC_region SET nom = :nom WHERE id = :id"
        );

        return $query->execute([
            ':id' => $this->id,
            ':nom' => $this->nom
        ]);
    }

    public function delete($id)
    {
        $query = $this->db->prepare("DELETE FROM BNGRC_region WHERE id = :id");
        return $query->execute([':id' => $id]);
    }

    public function getVilles()
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_ville WHERE id_region = :id_region ORDER BY nom ASC");
        $query->execute([':id_region' => $this->id]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
