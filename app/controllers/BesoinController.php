<?php

namespace app\controllers;

use app\models\BesoinVille;
use app\models\Ville;
use flight;
use flight\Engine;


class BesoinController {

	// protected Engine $app;

	// public function __construct($app) {
	// 	$this->app = $app;
	// }

    protected $db;

	public function __construct($db) {
		$this->db = $db;
	}


    public static function showForm() {
        $db = Flight::db();
        $villes = Ville::readAll($db);
        $articles = ArticleController::getAll();
        Flight::render('model', [
            'page' => 'besoin/formulaire',
            'villes' => $villes,
            'articles' => $articles
        ]);
    }

    public static function insert() {
        $data = Flight::request()->data;

        $id_ville = $data["id_ville"];
        $id_article = $data["id_article"];
        $quantite = $data["quantite"];

        $model = new BesoinVille(Flight::db());
        $model->setIdVille($id_ville);
        $model->setIdArticle($id_article);
        $model->setQuantiteDemandee($quantite);
        $model->create();
        Flight::redirect(BASE_URL . "/" );

    }

}
?>