<?php

namespace app\controllers;

use flight;
use flight\Engine;
use app\models\Article;


class ArticleController {

    protected $db;

	public function __construct($db) {
		$this->db = $db;
	}


    public static function getAll() {
        $db = Flight::db();
        $list = Article::readAll($db);
        return $list;
    }

    public function getById($id) {
        $article = new Article($this->db);
        $article->read($id);
        return $article;
    }
}
?>