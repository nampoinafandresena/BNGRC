<?php

namespace app\controllers;

use flight;
use app\models\Achat;
use app\models\Config;
use app\models\DonCollecte;

class AchatController {

    protected $db;

    public function __construct() {
        $this->db = Flight::db();
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
            !isset($data['quantite']) || !isset($data['montant_argent_utilise'])) {
            return ['success' => false, 'message' => 'Données manquantes'];
        }

        $achat = new Achat($this->db);
        $result = $achat->saveAchat(
            $data['id_ville'],
            $data['id_article'],
            $data['quantite'],
            $data['montant_argent_utilise']
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

    public function afficherFormulaire() {
        // 1. Récupérer les paramètres de l'URL
        $params = Flight::request()->query;

        // 2. Récupérer les données nécessaires via les modèles
        $frais = $this->getFraisConfig(); // Utilise ta fonction existante
        
        // On passe les données à la vue
        Flight::render('model', [
            'page' => 'achat/formulaire',
            'params' => $params,
            'frais' => $frais
        ]);
    }

    public function validerAchat() {
        $data = Flight::request()->data;

        // --- RÈGLE DE GESTION 1 : Vérifier si le don existe encore ---
        $stockNature = $this->getDonRestant($data->id_article);
        if ($stockNature > 0) {
            // Au lieu de sauver, on redirige avec un message d'erreur
            // (Tu peux aussi utiliser Flight::json si tu es en Ajax)
            Flight::halt(400, "Impossible d'acheter : il reste encore " . $stockNature . " unités en don pour cet article.");
            return;
        }

        // --- RÈGLE DE GESTION 2 : Calcul du montant avec frais ---
        // 0.1
        $fraisPourcent = $this->getFraisConfig();
        $montantHT = (float)$data->quantite * (float)$data->prix_unitaire;
        $montantTotal = $montantHT * (1 + ($fraisPourcent));

        // On prépare les données pour le modèle Achat
        $achatData = [
            'id_ville' => $data->id_ville,
            'id_article' => $data->id_article,
            'quantite' => $data->quantite,
            'montant_argent_utilise' => $montantTotal
        ];

        // --- SAUVEGARDE ---
        $result = $this->saveAchat($achatData);

        if ($result['success']) {
            // --- ATTRIBUTION AUTOMATIQUE : Créer une distribution pour le besoin-ville ---
            $distribution = new \app\models\Distribution($this->db);
            $distribution
                ->setIdDon(null) // Pas de don, c'est un achat
                ->setIdBesoinVille($data->id_besoin_ville)
                ->setQuantiteAttribuee($data->quantite);
            
            if (!$distribution->create()) {
                Flight::halt(500, "L'achat a été créé mais l'attribution au besoin a échoué.");
                return;
            }

            Flight::redirect('/'); // Retour au dashboard
        } else {
            Flight::halt(500, "Erreur lors de l'enregistrement de l'achat.");
        }
    }

    public function listeAchats() {
        $db = Flight::db();

        $id_ville = Flight::request()->query->id_ville;
        if($id_ville == "") { $id_ville = null; }
        
        // 1. Récupérer la liste des achats (filtrée ou non)
        $achats = Achat::getAllWithDetails($db, $id_ville);
        
        // 2. Récupérer la liste des villes pour le menu déroulant du filtre
        $stmtVilles = $db->query("SELECT * FROM BNGRC_ville ORDER BY nom");
        $villes = $stmtVilles->fetchAll(\PDO::FETCH_ASSOC);

        Flight::render('model', [
            'page' => 'achat/liste',
            'achats' => $achats,
            'villes' => $villes,
            'id_ville_selectionnee' => $id_ville
        ]);
    }

}