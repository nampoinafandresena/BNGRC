<?php

namespace app\models;

use flight\ActiveRecord;

class DonCollecte extends ActiveRecord
{
    protected static $table = 'BNGRC_don_collecte';
    protected static $primaryKey = 'id';
    
    private $id;
    private $id_article;
    private $quantite_recue;
    private $date_reception;
    private $donateur;

    public function getId()
    {
        return $this->id;
    }

    public function getIdArticle()
    {
        return $this->id_article;
    }

    public function getQuantiteRecue()
    {
        return $this->quantite_recue;
    }

    public function getDateReception()
    {
        return $this->date_reception;
    }

    public function getDonateur()
    {
        return $this->donateur;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setIdArticle($id_article)
    {
        $this->id_article = $id_article;
        return $this;
    }

    public function setQuantiteRecue($quantite_recue)
    {
        $this->quantite_recue = $quantite_recue;
        return $this;
    }

    public function setDateReception($date_reception)
    {
        $this->date_reception = $date_reception;
        return $this;
    }

    public function setDonateur($donateur)
    {
        $this->donateur = $donateur;
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

    public static function create($id_article, $quantite_recue, $donateur = 'Anonyme')
    {
        $don = new self();
        $don->id_article = $id_article;
        $don->quantite_recue = $quantite_recue;
        $don->donateur = $donateur;
        return $don->save();
    }

    public static function update($id, $id_article, $quantite_recue, $donateur = 'Anonyme')
    {
        $don = self::findFirst(['id' => $id]);
        if ($don) {
            $don->id_article = $id_article;
            $don->quantite_recue = $quantite_recue;
            $don->donateur = $donateur;
            return $don->save();
        }
        return false;
    }

    public static function delete($id)
    {
        $don = self::findFirst(['id' => $id]);
        if ($don) {
            return $don->delete();
        }
        return false;
    }

    public function getArticle()
    {
        return Article::findFirst(['id' => $this->id_article]);
    }

    public function getDistributions()
    {
        return Distribution::find(['id_don' => $this->id]);
    }

    public static function getByArticle($id_article)
    {
        return self::find(['id_article' => $id_article]);
    }

    public static function getByDonateur($donateur)
    {
        return self::find(['donateur' => $donateur]);
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

    public function getResteDisponible()
    {
        return $this->quantite_recue - $this->getQuantiteDistribuee();
    }
}
