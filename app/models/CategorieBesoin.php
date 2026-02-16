<?php

namespace app\models;

use flight\ActiveRecord;

class CategorieBesoin extends ActiveRecord
{
    protected static $table = 'BNGRC_categorie_besoin';
    protected static $primaryKey = 'id';
    
    private $id;
    private $label;

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

    public static function getAll()
    {
        return self::find();
    }

    public static function getById($id)
    {
        return self::findFirst(['id' => $id]);
    }

    public static function create($label)
    {
        $categorie = new self();
        $categorie->label = $label;
        return $categorie->save();
    }

    public static function update($id, $label)
    {
        $categorie = self::findFirst(['id' => $id]);
        if ($categorie) {
            $categorie->label = $label;
            return $categorie->save();
        }
        return false;
    }

    public static function delete($id)
    {
        $categorie = self::findFirst(['id' => $id]);
        if ($categorie) {
            return $categorie->delete();
        }
        return false;
    }

    public function getArticles()
    {
        return Article::find(['id_categorie' => $this->id]);
    }
}
