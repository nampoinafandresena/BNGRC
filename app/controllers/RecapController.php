<?php

namespace app\controllers;

use app\models\BesoinVille;
use app\models\Ville;
use flight;
use flight\Engine;


class RecapController {

	// protected Engine $app;

	// public function __construct($app) {
	// 	$this->app = $app;
	// }

    protected $db;

	public function __construct($db) {
		$this->db = $db;
	}


    public static function showRecap() {
        Flight::render('model', [
            'page' => 'recapitulation/recap'
        ]);
    }



}
?>