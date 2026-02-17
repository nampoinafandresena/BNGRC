<?php

namespace app\controllers;

use app\models\DonCollecte;
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
        $articles = ArticleController::getAll();
        Flight::render('model', [
            'page' => 'don/formulaire',
            'articles' => $articles
        ]);
    }

    public static function insert() {
        $data = Flight::request()->data;

        $id_article = $data["id_article"];
        $quantite = $data["quantite"];
        $date_reception = $data["date_reception"];
        $donateur = $data["donateur"];

        $model = new DonCollecte(Flight::db());
        $model->setIdArticle($id_article);
        $model->setQuantiteRecue($quantite);
        $model->setDateReception($date_reception);
        $model->setDonateur($donateur);
        $model->create();
        Flight::redirect('/');

    }

}
?>