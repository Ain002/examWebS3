<?php

use app\controllers\ApiExampleController;
use app\controllers\DashboardController;
use app\controllers\RegionController;
use app\controllers\VilleController;
use app\controllers\DonController;
use app\controllers\ProduitController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function(Router $router) use ($app) {

	// Pages (render views) using controllers to fetch data
	$router->get('/', function() use ($app) {
		$dashboard = new DashboardController($app);
		$data = $dashboard->index();
		$app->render('index', $data);
	});

	// route dédiée pour retourner uniquement le fragment du dashboard (utilisée par le menu JS)
	$router->get('/dashboard', function() use ($app) {
		$dashboard = new DashboardController($app);
		$data = $dashboard->index();
		// rend uniquement le fragment 'dashboard'
		$app->render('dashboard', $data);
	});

	$router->get('/region', function() use ($app) {
		$ctrl = new RegionController($app);
		$regions = $ctrl->index();
		$app->render('region', [ 'regions' => $regions ]);
	});

	$router->get('/ville', function() use ($app) {
		$ctrl = new VilleController($app);
		$villes = $ctrl->index();
		$app->render('ville', [ 'villes' => $villes ]);
	});

	$router->get('/don', function() use ($app) {
		$ctrl = new DonController($app);
		$dons = $ctrl->index();
		$app->render('don', [ 'dons' => $dons ]);
	});

	$router->get('/produit', function() use ($app) {
		$ctrl = new ProduitController($app);
		$produits = $ctrl->index();
		$app->render('insertDon', ['produits' => $produits]);
	});
	
	$router->post('/don', function() use ($app) {

		$data = [
			'idProduit' => $_POST['idProduit'] ?? null,
			'quantite'  => $_POST['quantite'] ?? null,
			'dateDon'   => $_POST['dateDon'] ?? null
		];
	
		if ($data['idProduit'] && $data['quantite'] && $data['dateDon']) {
			$ctrl = new DonController($app);
			$ctrl->create($data);
		}
	
		flight::redirect('/produit');
	});
	
	// Simple API endpoints (JSON)
	$router->get('/api/regions', function() use ($app) {
		$ctrl = new RegionController($app);
		$app->json($ctrl->index());
	});

	$router->get('/api/regions/@id', function($id) use ($app) {
		$ctrl = new RegionController($app);
		$app->json($ctrl->get($id));
	});

	$router->get('/api/villes', function() use ($app) {
		$ctrl = new VilleController($app);
		$app->json($ctrl->index());
	});

	$router->get('/api/villes/@id', function($id) use ($app) {
		$ctrl = new VilleController($app);
		$app->json($ctrl->get($id));
	});

	$router->get('/api/dons', function() use ($app) {
		$ctrl = new DonController($app);
		$app->json($ctrl->index());
	});

	$router->get('/api/dons/@id', function($id) use ($app) {
		$ctrl = new DonController($app);
		$app->json($ctrl->get($id));
	});

	$router->post('/api/dons/@id/distribuer', function($id) use ($app) {
		$ctrl = new DonController($app);
		$result = $ctrl->distribuerDon($id);
		
		// Redirection vers la page des dons avec un message
		if ($result['success']) {
			$app->redirect('/don?message=Distribution réussie&distribue=' . $result['quantite_distribuee'] . '&restant=' . $result['quantite_restante']);
		} else {
			$app->redirect('/don?error=' . urlencode($result['error'] ?? 'Erreur inconnue'));
		}
	});

}, [ SecurityHeadersMiddleware::class ]);