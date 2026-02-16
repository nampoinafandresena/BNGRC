<?php

namespace app\controllers;

use flight;
use flight\Engine;


class DonController {

	// protected Engine $app;

	// public function __construct($app) {
	// 	$this->app = $app;
	// }

    protected $db;

	public function __construct($db) {
		$this->db = $db;
	}


    public static function showForm() {
        
        Flight::render('model', [
            'page' => 'don/formulaire'
        ]);
    }

}
?>