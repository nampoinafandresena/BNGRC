<?php

use app\controllers\ApiExampleController;
use app\controllers\DonController;
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

	$router->group('/don', function() use ($router) {
		$router->get('/formulaire', [ DonController::class, 'showForm' ]);
	});

	$router->group('/api', function() use ($router) {
		$router->get('/users', [ ApiExampleController::class, 'getUsers' ]);
		$router->get('/users/@id:[0-9]', [ ApiExampleController::class, 'getUser' ]);
		$router->post('/users/@id:[0-9]', [ ApiExampleController::class, 'updateUser' ]);
	});

	$router->group('/dispatch', function() use ($router) {
		$router->get('/simulate', function() {
			$app = Flight::app();
			$dispatchController = new DispatchController($app);
			$result = $dispatchController->simulateDispatch();
			header('Content-Type: application/json');
			echo json_encode($result);
		});
	});
	
}, [ SecurityHeadersMiddleware::class ]);