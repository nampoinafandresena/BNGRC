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
		$router->get('/liste', [AchatController::class, 'listeAchats']);
		// $router->get('/liste/@id_ville', [AchatController::class, 'listeAchats']);
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
		$router->get('/dons', function() {
			$app = Flight::app();
			$donController = new DonController($app);
			$dons = $donController->getDonsWithDistributions();
			header('Content-Type: application/json');
			echo json_encode($dons);
		});
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
		// dispatch par date
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

		$router->post('/reset', function() {
			$app = Flight::app();
			$dispatchController = new DispatchController($app);
			$result = $dispatchController->ResetDispatch();
			header('Content-Type: application/json');
			echo json_encode($result);
		});
		// Nouvelles routes (minimum)
        $router->get('/preview-minimum', function() {
            $app = Flight::app();
            $dispatchController = new DispatchController($app);
            $result = $dispatchController->previewMinimumDispatch();
            header('Content-Type: application/json');
            echo json_encode($result);
        });

        $router->post('/validate-minimum', function() {
            $app = Flight::app();
            $dispatchController = new DispatchController($app);
            $result = $dispatchController->validateMinimumDispatch();
            header('Content-Type: application/json');
            echo json_encode($result);
        });

		// routes proportion
        $router->get('/preview-proportion', function() {
            $app = Flight::app();
            $dispatchController = new DispatchController($app);
            $result = $dispatchController->simulateDispatchProportion();
            // Récupérer l'état simulé
            $simulatedStats = $dispatchController->getSimulatedState($result['propositions']);
            header('Content-Type: application/json');
            echo json_encode([
                'propositions' => $result['propositions'],
                'stats' => $result['stats'],
                'simulated_state' => $simulatedStats
            ]);
        });

        $router->post('/validate-proportion', function() {
            $app = Flight::app();
            $dispatchController = new DispatchController($app);
            $result = $dispatchController->simulateDispatchProportion();
            $propositions = $result['propositions'];
            
            $validationResult = [
                'success' => false,
                'attributions_creees' => 0,
                'quantite_totale_attribuee' => 0,
                'erreurs' => []
            ];

            foreach ($propositions as $prop) {
                $distribution = new \app\models\Distribution($app->db());
                $distribution
                    ->setIdDon($prop['id_don'])
                    ->setIdBesoinVille($prop['id_besoin_ville'])
                    ->setQuantiteAttribuee($prop['quantite_attribuee']);

                if ($distribution->create()) {
                    $validationResult['attributions_creees']++;
                    $validationResult['quantite_totale_attribuee'] += $prop['quantite_attribuee'];
                } else {
                    $validationResult['erreurs'][] = "Erreur création don #{$prop['id_don']}";
                }
            }

            $validationResult['success'] = count($validationResult['erreurs']) === 0;
            header('Content-Type: application/json');
            echo json_encode($validationResult);
        });
	});
	
}, [ SecurityHeadersMiddleware::class ]);