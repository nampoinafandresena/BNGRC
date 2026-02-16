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
        $besoin_ville_model = new BesoinVille(Flight::db());
        $stats = $besoin_ville_model->getEtatGlobalVilles();
        return $stats;
    }

	
}