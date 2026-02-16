<?php

namespace app\models;

use flight\ActiveRecord;

class BesoinVille extends ActiveRecord
{
    protected static $table = 'BNGRC_besoin_ville';
    protected static $primaryKey = 'id';
    
    private $id;
    private $id_ville;
    private $id_article;
    private $quantite_demandee;
    private $date_demande;

    public function getId()
    {
        return $this->id;
    }

    public function getIdVille()
    {
        return $this->id_ville;
    }

    public function getIdArticle()
    {
        return $this->id_article;
    }

    public function getQuantiteDemandee()
    {
        return $this->quantite_demandee;
    }

    public function getDateDemande()
    {
        return $this->date_demande;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setIdVille($id_ville)
    {
        $this->id_ville = $id_ville;
        return $this;
    }

    public function setIdArticle($id_article)
    {
        $this->id_article = $id_article;
        return $this;
    }

    public function setQuantiteDemandee($quantite_demandee)
    {
        $this->quantite_demandee = $quantite_demandee;
        return $this;
    }

    public function setDateDemande($date_demande)
    {
        $this->date_demande = $date_demande;
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

    public static function create($id_ville, $id_article, $quantite_demandee)
    {
        $besoin = new self();
        $besoin->id_ville = $id_ville;
        $besoin->id_article = $id_article;
        $besoin->quantite_demandee = $quantite_demandee;
        return $besoin->save();
    }

    public static function update($id, $id_ville, $id_article, $quantite_demandee)
    {
        $besoin = self::findFirst(['id' => $id]);
        if ($besoin) {
            $besoin->id_ville = $id_ville;
            $besoin->id_article = $id_article;
            $besoin->quantite_demandee = $quantite_demandee;
            return $besoin->save();
        }
        return false;
    }

    public static function delete($id)
    {
        $besoin = self::findFirst(['id' => $id]);
        if ($besoin) {
            return $besoin->delete();
        }
        return false;
    }

    public function getVille()
    {
        return Ville::findFirst(['id' => $this->id_ville]);
    }

    public function getArticle()
    {
        return Article::findFirst(['id' => $this->id_article]);
    }

    public function getDistributions()
    {
        return Distribution::find(['id_besoin_ville' => $this->id]);
    }

    public static function getByVille($id_ville)
    {
        return self::find(['id_ville' => $id_ville]);
    }

    public static function getByArticle($id_article)
    {
        return self::find(['id_article' => $id_article]);
    }

    public function getQuantiteDistribuee()
    {
        $distributions = $this->getDistributions();
        $total = 0;
        foreach ($distributions as $dist) {
            $total += $dist->quantite_attribuee;
        }
        return $total;
    }

    public function getResteADistribuer()
    {
        return $this->quantite_demandee - $this->getQuantiteDistribuee();
    }
}
