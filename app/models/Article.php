<?php

namespace app\models;

use flight\ActiveRecord;

class Article extends ActiveRecord
{
    protected static $table = 'BNGRC_article';
    protected static $primaryKey = 'id';
    
    private $id;
    private $id_categorie;
    private $label;
    private $prix_unitaire;

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

    public static function getAll()
    {
        return self::find();
    }

    public static function getById($id)
    {
        return self::findFirst(['id' => $id]);
    }

    public static function create($id_categorie, $label, $prix_unitaire = 0)
    {
        $article = new self();
        $article->id_categorie = $id_categorie;
        $article->label = $label;
        $article->prix_unitaire = $prix_unitaire;
        return $article->save();
    }

    public static function update($id, $id_categorie, $label, $prix_unitaire = 0)
    {
        $article = self::findFirst(['id' => $id]);
        if ($article) {
            $article->id_categorie = $id_categorie;
            $article->label = $label;
            $article->prix_unitaire = $prix_unitaire;
            return $article->save();
        }
        return false;
    }

    public static function delete($id)
    {
        $article = self::findFirst(['id' => $id]);
        if ($article) {
            return $article->delete();
        }
        return false;
    }

    public function getCategorie()
    {
        return CategorieBesoin::findFirst(['id' => $this->id_categorie]);
    }

    public function getDons()
    {
        return DonCollecte::find(['id_article' => $this->id]);
    }

    public static function getByCategorie($id_categorie)
    {
        return self::find(['id_categorie' => $id_categorie]);
    }

    public function getTotalCollected()
    {
        $dons = $this->getDons();
        $total = 0;
        foreach ($dons as $don) {
            $total += $don->quantite_recue;
        }
        return $total;
    }
}
