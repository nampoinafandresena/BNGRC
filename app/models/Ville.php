<?php

namespace app\models;

use flight\ActiveRecord;

class Ville extends ActiveRecord
{
    protected static $table = 'BNGRC_ville';
    protected static $primaryKey = 'id';
    
    private $id;
    private $nom;
    private $id_region;

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

    public static function getAll()
    {
        return self::find();
    }

    public static function getById($id)
    {
        return self::findFirst(['id' => $id]);
    }

    public static function create($nom, $id_region)
    {
        $ville = new self();
        $ville->nom = $nom;
        $ville->id_region = $id_region;
        return $ville->save();
    }

    public static function update($id, $nom, $id_region)
    {
        $ville = self::findFirst(['id' => $id]);
        if ($ville) {
            $ville->nom = $nom;
            $ville->id_region = $id_region;
            return $ville->save();
        }
        return false;
    }

    public static function delete($id)
    {
        $ville = self::findFirst(['id' => $id]);
        if ($ville) {
            return $ville->delete();
        }
        return false;
    }

    public function getRegion()
    {
        return Region::findFirst(['id' => $this->id_region]);
    }

    public function getBesoins()
    {
        return BesoinVille::find(['id_ville' => $this->id]);
    }

    public static function getByRegion($id_region)
    {
        return self::find(['id_region' => $id_region]);
    }
}
