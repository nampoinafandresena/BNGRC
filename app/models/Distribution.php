<?php

namespace app\models;

use flight\ActiveRecord;

class Distribution extends ActiveRecord
{
    protected static $table = 'BNGRC_distribution';
    protected static $primaryKey = 'id';
    
    private $id;
    private $id_don;
    private $id_besoin_ville;
    private $quantite_attribuee;
    private $date_attribution;

    public function getId()
    {
        return $this->id;
    }

    public function getIdDon()
    {
        return $this->id_don;
    }

    public function getIdBesoinVille()
    {
        return $this->id_besoin_ville;
    }

    public function getQuantiteAttribuee()
    {
        return $this->quantite_attribuee;
    }

    public function getDateAttribution()
    {
        return $this->date_attribution;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setIdDon($id_don)
    {
        $this->id_don = $id_don;
        return $this;
    }

    public function setIdBesoinVille($id_besoin_ville)
    {
        $this->id_besoin_ville = $id_besoin_ville;
        return $this;
    }

    public function setQuantiteAttribuee($quantite_attribuee)
    {
        $this->quantite_attribuee = $quantite_attribuee;
        return $this;
    }

    public function setDateAttribution($date_attribution)
    {
        $this->date_attribution = $date_attribution;
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

    public static function create($id_don, $id_besoin_ville, $quantite_attribuee)
    {
        $distribution = new self();
        $distribution->id_don = $id_don;
        $distribution->id_besoin_ville = $id_besoin_ville;
        $distribution->quantite_attribuee = $quantite_attribuee;
        return $distribution->save();
    }

    public static function update($id, $id_don, $id_besoin_ville, $quantite_attribuee)
    {
        $distribution = self::findFirst(['id' => $id]);
        if ($distribution) {
            $distribution->id_don = $id_don;
            $distribution->id_besoin_ville = $id_besoin_ville;
            $distribution->quantite_attribuee = $quantite_attribuee;
            return $distribution->save();
        }
        return false;
    }

    public static function delete($id)
    {
        $distribution = self::findFirst(['id' => $id]);
        if ($distribution) {
            return $distribution->delete();
        }
        return false;
    }

    public function getDon()
    {
        return DonCollecte::findFirst(['id' => $this->id_don]);
    }

    public function getBesoinVille()
    {
        return BesoinVille::findFirst(['id' => $this->id_besoin_ville]);
    }

    public static function getByDon($id_don)
    {
        return self::find(['id_don' => $id_don]);
    }

    public static function getByBesoinVille($id_besoin_ville)
    {
        return self::find(['id_besoin_ville' => $id_besoin_ville]);
    }

    public static function getByVille($id_ville)
    {
        $besoins = BesoinVille::getByVille($id_ville);
        $distributions = [];
        foreach ($besoins as $besoin) {
            $dists = self::getByBesoinVille($besoin->id);
            $distributions = array_merge($distributions, $dists);
        }
        return $distributions;
    }

    public static function getByArticle($id_article)
    {
        $besoins = BesoinVille::getByArticle($id_article);
        $distributions = [];
        foreach ($besoins as $besoin) {
            $dists = self::getByBesoinVille($besoin->id);
            $distributions = array_merge($distributions, $dists);
        }
        return $distributions;
    }
}
