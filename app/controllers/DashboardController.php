<?php

namespace app\controllers;

use flight\Engine;
use Flight;
use app\models\Ville;
use app\models\BesoinVille;

use PDO;

class DashboardController {

	protected Engine $app;

	public function __construct($app) {
		$this->app = $app;
	}

	public function getVilles() {
        $ville_model = new Ville($this->db);
        $villes = $ville_model->readAll();
        return $villes;
    }

    public function getEtatGlobalVilles() {
        $db = Flight::db();
        $besoin_ville_model = new BesoinVille($db);
        $stats = $besoin_ville_model->getEtatGlobalVilles();
        
        // Ajouter les dons disponibles pour chaque article (dons reçus - achats utilisés)
        foreach ($stats as &$s) {
            $query = $db->prepare(
                "SELECT COALESCE(SUM(dc.quantite_recue), 0) as total_dons,
                        COALESCE(SUM(ac.quantite), 0) as total_achats
                 FROM BNGRC_don_collecte dc
                 LEFT JOIN BNGRC_achat ac ON dc.id_article = ac.id_article
                 WHERE dc.id_article = :id_article"
            );
            $query->execute([':id_article' => $s['id_article']]);
            $don_data = $query->fetch(PDO::FETCH_ASSOC);
            $s['dons_disponibles'] = ($don_data['total_dons'] ?? 0) - ($don_data['total_achats'] ?? 0);
        }
        return $stats;
    }

    public function getRecapAjax() {
        $db = Flight::db();
        
        // 1. Calcul du montant TOTAL des besoins exprimés
        $sqlTotal = "SELECT SUM(bv.quantite_demandee * a.prix_unitaire) as total 
                    FROM BNGRC_besoin_ville bv 
                    JOIN BNGRC_article a ON bv.id_article = a.id";
        $stmtTotal = $db->query($sqlTotal);
        $totalBesoins = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // 2. Calcul du montant SATISFAIT (Distributions + Achats)
        // Valeur des dons distribués
        $sqlDist = "SELECT SUM(d.quantite_attribuee * a.prix_unitaire) as total 
                    FROM BNGRC_distribution d 
                    JOIN BNGRC_besoin_ville bv ON d.id_besoin_ville = bv.id
                    JOIN BNGRC_article a ON bv.id_article = a.id";
        $stmtDist = $db->query($sqlDist);
        $montantDist = $stmtDist->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Valeur des achats (On prend le montant hors frais car c'est la valeur du besoin comblé)
        $sqlAchat = "SELECT SUM(quantite * a.prix_unitaire) as total 
                    FROM BNGRC_achat ac
                    JOIN BNGRC_article a ON ac.id_article = a.id";
        $stmtAchat = $db->query($sqlAchat);
        $montantAchat = $stmtAchat->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        $totalSatisfait = $montantDist + $montantAchat;
        $montantRestant = $totalBesoins - $totalSatisfait;

        // Envoi de la réponse en JSON pour Ajax
        Flight::json([
            'total_besoins' => number_format($totalBesoins, 2, ',', ' '),
            'total_satisfait' => number_format($totalSatisfait, 2, ',', ' '),
            'montant_restant' => number_format($montantRestant, 2, ',', ' ')
        ]);
    }
	
}