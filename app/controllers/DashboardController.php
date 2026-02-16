<?php

namespace app\controllers;

use flight\Engine;

use app\models\Ville;
use app\models\BesoinVille;

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

	
}