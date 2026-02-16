<?php

use app\controllers\ApiExampleController;
use app\controllers\DashboardController;
use app\controllers\RegionController;
use app\controllers\VilleController;
use app\controllers\DonController;
use app\controllers\BesoinController;
use app\controllers\RecapitulatifController;
use app\models\ProduitModel;
use app\models\TypeBesoinModel;
use app\controllers\ProduitController;
use app\controllers\BesoinSatisfaitController;
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

	$router->get('/recapitulatif', function() use ($app) {
		$ctrl = new RecapitulatifController($app);
		$stats = $ctrl->getStats();
		$app->render('recapitulatif', $stats);
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
	
		flight::redirect('/don');
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

	// Distribution des dons
	$router->post('/api/dons/@id/distribuer', function($id) use ($app) {
		$ctrl = new DonController($app);
		$result = $ctrl->distribuerDon($id);
		
		if ($result['success']) {
			flight::redirect('/don?success=' . urlencode($result['message']));
		} else {
			flight::redirect('/don?error=' . urlencode($result['message']));
		}
	});

	// Liste par ville

	// Besoins restants (page HTML, non JSON) - placer avant la route dynamique /besoin/@idVille
	$router->get('/besoin/restant', function() use ($app) {
		$ctrl = new app\controllers\BesoinSatisfaitController(Flight::app());
		$besoins = $ctrl->getBesoinRestant();
		$villes = (new app\controllers\VilleController(Flight::app()))->index();
		require __DIR__ . '/../views/besoinRestant.php';
	});

	$router->get('/besoin/@idVille', function($idVille){
		$ctrl = new BesoinController(Flight::app());
		$besoins = $ctrl->getByVille($idVille);
		$villeId = $idVille;
		$produits = ProduitModel::getAll();
		$types = TypeBesoinModel::getAll();

		require __DIR__ . '/../views/besoin.php';

	});

	// Form ajout
	$router->get('/besoin/form/@idVille', function($idVille){
		$besoin = null;
		$villeId = $idVille;
		$types = TypeBesoinModel::getAll();
		$produits = ProduitModel::getAll();

		require __DIR__ . '/../views/besoinForm.php';

	});

	// Form modification
	$router->get('/besoin/form/@idVille/@id', function($idVille, $id){
		$ctrl = new BesoinController(Flight::app());
		$besoin = $ctrl->get($id);
		$villeId = $idVille;
		$types = TypeBesoinModel::getAll();
		$produits = ProduitModel::getAll();

		require 'app/views/besoinForm.php';
	});

	// Save
	$router->post('/besoin/save', function(){
		$ctrl = new BesoinController(Flight::app());
		$payload = $_POST;

		if (!empty($payload['id'])) {
			$ctrl->update((int)$payload['id'], $payload);
		} else {
			$ctrl->create($payload);
		}

		Flight::redirect(BASE_URL . '/besoin/ville/' . $payload['idVille']);
	});

	// Delete
	$router->post('/besoin/delete/@id', function($id){
		$ctrl = new BesoinController(Flight::app());
		$d = $ctrl->get($id);
		if($d){
			$idVille = $d->idVille;
			$ctrl->delete($id);
			Flight::redirect(BASE_URL . '/besoin/ville/' . $idVille);
		}
	});

	// API endpoint pour récapitulatif (AJAX)
	$router->get('/api/recapitulatif', function() use ($app) {
		$ctrl = new RecapitulatifController($app);
		$stats = $ctrl->getStats();
		$app->json($stats);
	});




}, [ SecurityHeadersMiddleware::class ]);