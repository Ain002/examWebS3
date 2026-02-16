<?php

use app\controllers\ApiExampleController;
use app\controllers\DashboardController;
use app\controllers\RegionController;
use app\controllers\VilleController;
use app\controllers\DonController;
use app\controllers\BesoinController;
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

	$router->get('/api/dons/@id', function($id) use ($app) {
		$ctrl = new DonController($app);
		$app->json($ctrl->get($id));
	});

	// page affichant les besoins pour une ville donnée (fragment)
	$router->get('/besoin/@id', function($id) use ($app) {
		$list = new BesoinController($app);
		$data = $list->getByVille($id);
		// récupérer les produits pour le formulaire
		$produits = \app\models\ProduitModel::getAll();
		// s'assurer que la vue reçoit une variable nommée 'besoins' (iterable), l'id de la ville et la liste de produits
		$app->render('besoin', [ 'besoins' => $data, 'villeId' => (int)$id, 'produits' => $produits ]);
	});

	// Routes server-side (form POST) pour gérer les besoins sans JSON
	$router->post('/besoin/save', function() use ($app) {
		$payload = $app->request()->data->getData();
		$ctrl = new BesoinController($app);
		// si un id est fourni, on met à jour, sinon on crée
		if (!empty($payload['id'])) {
			$ctrl->update((int)$payload['id'], (array)$payload);
		} else {
			$ctrl->create((array)$payload);
		}
		$villeId = (int)($payload['idVille'] ?? 0);
		$besoins = $ctrl->getByVille($villeId);
		// rendre le fragment mis à jour
		$app->render('besoin', [ 'besoins' => $besoins, 'villeId' => $villeId ]);
	});

	$router->post('/besoin/delete/@id', function($id) use ($app) {
		$payload = $app->request()->data->getData();
		$ctrl = new BesoinController($app);
		$ctrl->delete($id);
		$villeId = (int)($payload['idVille'] ?? 0);
		$besoins = $ctrl->getByVille($villeId);
		$app->render('besoin', [ 'besoins' => $besoins, 'villeId' => $villeId ]);
	});

	// API pour gérer les besoins (JSON)
	$router->post('/api/besoins', function() use ($app) {
		$payload = $app->request()->data->getData();
		$ctrl = new BesoinController($app);
		$res = $ctrl->create((array)$payload);
		$app->json($res);
	});

	$router->put('/api/besoins/@id', function($id) use ($app) {
		$payload = $app->request()->data->getData();
		$ctrl = new BesoinController($app);
		$res = $ctrl->update($id, (array)$payload);
		$app->json($res);
	});

	$router->delete('/api/besoins/@id', function($id) use ($app) {
		$ctrl = new BesoinController($app);
		$res = $ctrl->delete($id);
		$app->json($res);
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