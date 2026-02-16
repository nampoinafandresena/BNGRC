<?php

namespace app\models;

use PDO;

class Ville
{
    public $db;
    public $id;
    public $nom;
    public $id_region;

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

    public function getIdRegion()
    {
        return $this->id_region;
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

    public function setIdRegion($id_region)
    {
        $this->id_region = $id_region;
        return $this;
    }

    public function create()
    {
        $query = $this->db->prepare(
            "INSERT INTO BNGRC_ville (nom, id_region) VALUES (:nom, :id_region)"
        );

        if ($query->execute([
            ':nom' => $this->nom,
            ':id_region' => $this->id_region
        ])) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    public function read($id)
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_ville WHERE id = :id");
        $query->execute([':id' => $id]);

        $data = $query->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $this->id = $data['id'];
            $this->nom = $data['nom'];
            $this->id_region = $data['id_region'];
            return $this;
        }
        return null;
    }

    public function readAll()
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_ville ORDER BY nom ASC");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readByRegion($id_region)
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_ville WHERE id_region = :id_region ORDER BY nom ASC");
        $query->execute([':id_region' => $id_region]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = $this->db->prepare(
            "UPDATE BNGRC_ville SET nom = :nom, id_region = :id_region WHERE id = :id"
        );

        return $query->execute([
            ':id' => $this->id,
            ':nom' => $this->nom,
            ':id_region' => $this->id_region
        ]);
    }

    public function delete($id)
    {
        $query = $this->db->prepare("DELETE FROM BNGRC_ville WHERE id = :id");
        return $query->execute([':id' => $id]);
    }

    public function getRegion()
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_region WHERE id = :id");
        $query->execute([':id' => $this->id_region]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getBesoins()
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_besoin_ville WHERE id_ville = :id_ville");
        $query->execute([':id_ville' => $this->id]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
