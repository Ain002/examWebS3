<?php

use app\controllers\DashboardController;
use app\controllers\RegionController;
use app\controllers\VilleController;
use app\controllers\DonController;
use app\controllers\BesoinController;
use app\controllers\SimulationAchatController;
use app\models\ProduitModel;
use app\models\TypeBesoinModel;
use app\controllers\ProduitController;
use app\middlewares\SecurityHeadersMiddleware;
use app\middlewares\InjectCssMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

function isAjax(): bool {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function renderPage(string $view, array $data = []): void {
    $viewsDir = __DIR__ . '/../views';

    if (isAjax()) {
        extract($data, EXTR_SKIP);
        require $viewsDir . '/' . $view . '.php';
    } else {
        $data['currentView'] = $view;
        extract($data, EXTR_SKIP);
        require $viewsDir . '/index.php';
    }
}


$router->group('', function(Router $router) use ($app) {

    // ================= DASHBOARD =================
    $router->get('/', function() use ($app) {
        $dashboard = new DashboardController($app);
        $data = $dashboard->index();
        renderPage('dashboard', $data);
    });

    $router->get('/dashboard', function() use ($app) {
        $dashboard = new DashboardController($app);
        $data = $dashboard->index();
        renderPage('dashboard', $data);
    });

    // ================= REGION =================
    $router->get('/region', function() use ($app) {
        $ctrl = new RegionController($app);
        renderPage('region', ['regions' => $ctrl->index()]);
    });

    // ================= VILLE =================
    $router->get('/ville', function() use ($app) {
        $ctrl = new VilleController($app);
        renderPage('ville', ['villes' => $ctrl->index()]);
    });

    // ================= DON =================
    $router->get('/don', function() use ($app) {
        $ctrl = new DonController($app);
        renderPage('don', ['dons' => $ctrl->index()]);
    });

    $router->post('/don', function() use ($app) {
        $data = [
            'idProduit' => $_POST['idProduit'] ?? null,
            'quantite'  => $_POST['quantite'] ?? null,
            'dateDon'   => $_POST['dateDon'] ?? null,
        ];

        if ($data['idProduit'] && $data['quantite'] && $data['dateDon']) {
            (new DonController($app))->create($data);
        }

        Flight::redirect('/don');
    });

    // ================= PRODUIT =================
    $router->get('/produit', function() use ($app) {
        $ctrl = new ProduitController($app);
        renderPage('insertDon', ['produits' => $ctrl->index()]);
    });

    // ================= RECAP =================
    $router->get('/recapitulatif', function() use ($app) {
        $ctrl = new RecapitulatifController($app);
        renderPage('recapitulatif', $ctrl->getStats());
    });

    $router->get('/api/recapitulatif', function() use ($app) {
        $app->json((new RecapitulatifController($app))->getStats());
    });

    // ================= BESOIN =================
    $router->get('/besoin/@idVille', function($idVille) use ($app) {
        $ctrl = new BesoinController($app);
        renderPage('besoin', [
            'besoins'  => $ctrl->getByVille($idVille),
            'villeId'  => $idVille,
            'produits' => ProduitModel::getAll(),
            'types'    => TypeBesoinModel::getAll(),
        ]);
    });

    $router->get('/besoin/form/@idVille', function($idVille) use ($app) {
        renderPage('besoinForm', [
            'besoin'   => null,
            'villeId'  => $idVille,
            'types'    => TypeBesoinModel::getAll(),
            'produits' => ProduitModel::getAll(),
        ]);
    });

    $router->get('/besoin/form/@idVille/@id', function($idVille, $id) use ($app) {
        $ctrl = new BesoinController($app);
        renderPage('besoinForm', [
            'besoin'   => $ctrl->get($id),
            'villeId'  => $idVille,
            'types'    => TypeBesoinModel::getAll(),
            'produits' => ProduitModel::getAll(),
        ]);
    });

    $router->post('/besoin/save', function() use ($app) {
        $ctrl = new BesoinController($app);
        $payload = $_POST;

        if (!empty($payload['id'])) {
            $ctrl->update((int)$payload['id'], $payload);
        } else {
            $ctrl->create($payload);
        }

        Flight::redirect(BASE_URL . '/besoin/' . $payload['idVille']);
    });

    $router->post('/besoin/delete/@id', function($id) use ($app) {
        $ctrl = new BesoinController($app);
        $besoin = $ctrl->get($id);

        if ($besoin) {
            $ctrl->delete($id);
            Flight::redirect(BASE_URL . '/besoin/' . $besoin->idVille);
        }
    });

	$router->get('/simulation/@id', function($id) use ($app) {
		$ctrl = new SimulationAchatController($app);
		$ctrl->index($id);
	});
}, [SecurityHeadersMiddleware::class, InjectCssMiddleware::class]);


