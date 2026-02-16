<?php

namespace app\models;

use flight\ActiveRecord;

class Region extends ActiveRecord
{
    protected static $table = 'BNGRC_region';
    protected static $primaryKey = 'id';
    
    private $id;
    private $nom;

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

    public static function getAll()
    {
        return self::find();
    }

    public static function getById($id)
    {
        return self::findFirst(['id' => $id]);
    }

    public static function create($nom)
    {
        $region = new self();
        $region->nom = $nom;
        return $region->save();
    }

    public static function update($id, $nom)
    {
        $region = self::findFirst(['id' => $id]);
        if ($region) {
            $region->nom = $nom;
            return $region->save();
        }
        return false;
    }

    public static function delete($id)
    {
        $region = self::findFirst(['id' => $id]);
        if ($region) {
            return $region->delete();
        }
        return false;
    }

    public function getVilles()
    {
        return Ville::find(['id_region' => $this->id]);
    }
}
