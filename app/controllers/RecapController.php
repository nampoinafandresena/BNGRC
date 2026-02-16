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


    public function calcVMallbesoins() {
        $allbesoins = BesoinController::getAll();
        $total = 0;
        foreach ($allbesoins as $b) {
            $model = new BesoinVille($this->db);
            $model->read($b['id']);
            $total += BesoinController::calcValeurMonetaire($model);
        }
        return $total;
    }

    public function calcVMsatisfiedbesoin() {
        $allbesoins = BesoinController::getAll();
        $total = 0;
        foreach ($allbesoins as $b) {
            $model = new BesoinVille($this->db);
            $model->read($b['id']);
            $total += BesoinController::calcValeurMonetaire($model);
        }
        return $total;
    }

    public function showRecap() {
        $this->db = Flight::db();
        $total = $this->calcVMallbesoins();

        Flight::render('model', [
            'page' => 'recapitulation/recap',
            'total'=> $total
        ]);
    }




}
?>