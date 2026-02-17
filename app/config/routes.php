<?php

use app\controllers\ApiExampleController;
use app\controllers\BesoinController;
use app\controllers\RecapController;
use app\controllers\DonController;
use app\controllers\AchatController;
use app\controllers\DashboardController;
use app\controllers\DispatchController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function(Router $router) use ($app) {

	// Before defining routes
	$dashboardController = new DashboardController($app);

	$router->get('/', function() use ($app, $dashboardController) {
		$app->render('model', [ 
			'page' => 'index' ,
			'stats' => $dashboardController->getEtatGlobalVilles()
		]);
	});

	$router->get('/hello-world/@name', function($name) {
		echo '<h1>Hello world! Oh hey '.$name.'!</h1>';
	});

	$router->get('/achat/formulaire', function() use ($app){
		$data = Flight::request()->query;
		$app->render('model', [
			'page' => 'achat/formulaire',
			'params' => $data
		]);
	});

	$router->group('/achat', function($router) use ($app) {
		$router->get('/formulaire', [AchatController::class, 'afficherFormulaire']);	
		$router->post('/valider', [AchatController::class, 'validerAchat']);
	});

	$router->group('/don', function() use ($router) {
		$router->get('/formulaire', [ DonController::class, 'showForm' ]);
		$router->post('/insert', [ DonController::class, 'insert' ]);

	});

	$router->group('/besoin', function() use ($router) {
		$router->get('/formulaire', [ BesoinController::class, 'showForm' ]);
		$router->post('/insert', [ BesoinController::class, 'insert' ]);

	});

	$router->group('/recap', function() use ($router) {
		$router->get('/', [ RecapController::class, 'showRecap' ]);
		$router->get('/data', [ RecapController::class, 'getRecapData' ]);
	});


	$router->group('/api', function() use ($router) {
		$router->get('/recap', [DashboardController::class, 'getRecapAjax']);
		$router->get('/users', [ ApiExampleController::class, 'getUsers' ]);
		$router->get('/users/@id:[0-9]', [ ApiExampleController::class, 'getUser' ]);
		$router->post('/users/@id:[0-9]', [ ApiExampleController::class, 'updateUser' ]);
	});

	/*
	 * =====================================================
	 * ROUTES DISPATCH - SIMULATION ET VALIDATION
	 * =====================================================
	 * Anciennement : GET /dispatch/simulate (sauvegardait directement)
	 * Nouvelle API séparée en deux routes :
	 * - GET /dispatch/preview : retourne les propositions SANS sauvegarder
	 * - POST /dispatch/validate : persiste les distributions
	 * =====================================================
	 */
	$router->group('/dispatch', function() use ($router) {
		$router->get('/preview', function() {
			$app = Flight::app();
			$dispatchController = new DispatchController($app);
			$result = $dispatchController->simulateDispatchPreview();
			header('Content-Type: application/json');
			echo json_encode($result);
		});

		$router->post('/validate', function() {
			$app = Flight::app();
			$dispatchController = new DispatchController($app);
			$result = $dispatchController->validateDispatch();
			header('Content-Type: application/json');
			echo json_encode($result);
		});
	});
	
}, [ SecurityHeadersMiddleware::class ]);