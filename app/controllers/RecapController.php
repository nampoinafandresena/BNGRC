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
        $bc = new BesoinController($this->db);
        $satisfied = $bc->getBesoinsSatisfaits();
        $total = 0;
        foreach ($satisfied as $s) {
            $model = new BesoinVille($this->db);
            $model->read($s['id']);
            $total += BesoinController::calcValeurMonetaire($model);
        }
        return $total;
    }

    public function calcVMreste() {
        $total = $this->calcVMallbesoins();
        $satisfied = $this->calcVMsatisfiedbesoin();
        return $total - $satisfied;
    }

    public function showRecap() {
        $this->db = Flight::db();
        $total = $this->calcVMallbesoins();
        $satisfied = $this->calcVMsatisfiedbesoin();
        $reste = $this->calcVMreste();

        Flight::render('model', [
            'page' => 'recapitulation/recap',
            'total'=> $total,
            'satisfied' => $satisfied,
            'reste' => $reste
        ]);
    }

    public function getRecapData()
    {
        $this->db = Flight::db();

        $total = $this->calcVMallbesoins();
        $satisfied = $this->calcVMsatisfiedbesoin();
        $reste = $this->calcVMreste();

        Flight::json([
            'total' => $total,
            'satisfied' => $satisfied,
            'reste' => $reste
        ]);
    }



}
?>