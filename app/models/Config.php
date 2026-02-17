<?php

namespace app\models;

use PDO;

class Config
{
    public $db;
    public $id;
    public $frais_achat;

    public function __construct($db = null)
    {
        $this->db = $db;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFraisAchat()
    {
        return $this->frais_achat;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setFraisAchat($frais_achat)
    {
        $this->frais_achat = $frais_achat;
        return $this;
    }

    public function create()
    {
        $query = $this->db->prepare(
            "INSERT INTO BNGRC_config (frais_achat) 
             VALUES (:frais_achat)"
        );

        if ($query->execute([
            ':frais_achat' => $this->frais_achat
        ])) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    public function read($id)
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_config WHERE id = :id");
        $query->execute([':id' => $id]);

        $data = $query->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $this->id = $data['id'];
            $this->frais_achat = $data['frais_achat'];
            return $this;
        }
        return null;
    }

    public static function readAll($db)
    {
        $query = $db->prepare("SELECT * FROM BNGRC_config");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = $this->db->prepare(
            "UPDATE BNGRC_config SET frais_achat = :frais_achat WHERE id = :id"
        );

        return $query->execute([
            ':id' => $this->id,
            ':frais_achat' => $this->frais_achat
        ]);
    }

    public function delete($id)
    {
        $query = $this->db->prepare("DELETE FROM BNGRC_config WHERE id = :id");
        return $query->execute([':id' => $id]);
    }

    public function getFraisConfig()
    {
        $query = $this->db->prepare("SELECT frais_achat FROM BNGRC_config LIMIT 1");
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $this->frais_achat = $result['frais_achat'];
            return $this->frais_achat;
        }
        return null;
    }
}
