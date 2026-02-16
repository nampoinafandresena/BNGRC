<?php

namespace app\controllers;

use flight;
use app\models\Achat;
use app\models\Config;
use app\models\DonCollecte;

class AchatController {

    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public static function getAll() {
        $db = Flight::db();
        return Achat::readAll($db);
    }

    public function getById($id) {
        $achat = new Achat($this->db);
        $achat->read($id);
        return $achat;
    }

    public function getAchatsWithDetails() {
        return Achat::getAllWithDetails($this->db);
    }

    public function getStatsAchats() {
        return Achat::getStats($this->db);
    }

    public function getAchatsByPeriode($date_debut, $date_fin) {
        return Achat::getByPeriode($this->db, $date_debut, $date_fin);
    }

    public function getAchatsByArticle($id_article) {
        return Achat::getByArticle($this->db, $id_article);
    }

    public function getAchatsByVille($id_ville) {
        return Achat::getByVille($this->db, $id_ville);
    }

    public function getMontantTotalByArticle($id_article) {
        return Achat::getMontantTotalByArticle($this->db, $id_article);
    }

    public function getFraisConfig() {
        $config = new Config($this->db);
        return $config->getFraisConfig();
    }

    public function getDonRestant($id_article) {
        $donCollecte = new DonCollecte($this->db);
        return $donCollecte->getDonRestant($id_article);
    }

    public function saveAchat($data) {
        if (!isset($data['id_ville']) || !isset($data['id_article']) || 
            !isset($data['quantite']) || !isset($data['prix_unitaire'])) {
            return ['success' => false, 'message' => 'Données manquantes'];
        }

        $achat = new Achat($this->db);
        $result = $achat->saveAchat(
            $data['id_ville'],
            $data['id_article'],
            $data['quantite'],
            $data['prix_unitaire']
        );

        if ($result) {
            return ['success' => true, 'message' => 'Achat enregistré avec succès', 'id' => $achat->getId()];
        }
        return ['success' => false, 'message' => 'Erreur lors de l\'enregistrement'];
    }

    public function create() {
        $request = Flight::request();
        return $this->saveAchat($request->data->getData());
    }

    public function update($id) {
        $request = Flight::request();
        $data = $request->data->getData();
        
        $achat = new Achat($this->db);
        $achat->read($id);
        
        if (!$achat->getId()) {
            return ['success' => false, 'message' => 'Achat non trouvé'];
        }

        if (isset($data['id_ville'])) {
            $achat->setIdVille($data['id_ville']);
        }
        if (isset($data['id_article'])) {
            $achat->setIdArticle($data['id_article']);
        }
        if (isset($data['quantite'])) {
            $achat->setQuantite($data['quantite']);
        }
        if (isset($data['montant_argent_utilise'])) {
            $achat->setMontantArgentUtilise($data['montant_argent_utilise']);
        }

        $result = $achat->update();
        
        return $result 
            ? ['success' => true, 'message' => 'Achat mis à jour avec succès']
            : ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
    }

    public function delete($id) {
        $achat = new Achat($this->db);
        $result = $achat->delete($id);
        
        return $result 
            ? ['success' => true, 'message' => 'Achat supprimé avec succès']
            : ['success' => false, 'message' => 'Erreur lors de la suppression'];
    }

    public function checkModifiable($id) {
        $achat = new Achat($this->db);
        $achat->read($id);
        
        if (!$achat->getId()) {
            return ['success' => false, 'message' => 'Achat non trouvé'];
        }
        
        return [
            'success' => true,
            'modifiable' => $achat->isModifiable(),
            'date_achat' => $achat->getDateAchat()
        ];
    }
}