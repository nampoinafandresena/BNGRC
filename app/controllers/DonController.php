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

    public function getDonsWithDistributions() {
        $db = Flight::db();
        $query = $db->prepare(
            "SELECT 
                dc.id,
                dc.donateur,
                a.label as article,
                dc.quantite_recue,
                COALESCE(SUM(d.quantite_attribuee), 0) as quantite_distribuee,
                (dc.quantite_recue - COALESCE(SUM(d.quantite_attribuee), 0)) as quantite_restante
            FROM BNGRC_don_collecte dc
            JOIN BNGRC_article a ON dc.id_article = a.id
            LEFT JOIN BNGRC_distribution d ON dc.id = d.id_don
            GROUP BY dc.id, dc.donateur, a.label, dc.quantite_recue
            ORDER BY dc.date_reception DESC"
        );
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

}
?>