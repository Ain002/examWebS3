<?php

use app\controllers\DashboardController;
use app\controllers\RegionController;
use app\controllers\VilleController;
use app\controllers\DonController;
use app\controllers\BesoinController;
use app\controllers\SimulationAchatController;
use app\controllers\ProduitController;
use app\controllers\RecapitulatifController;
use app\models\ProduitModel;
use app\models\TypeBesoinModel;
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
        $data = (new DashboardController($app))->index();
        renderPage('dashboard', $data);
    });

    $router->get('/dashboard', function() use ($app) {
        $data = (new DashboardController($app))->index();
        renderPage('dashboard', $data);
    });

    // ================= REGION =================
    $router->get('/region', function() use ($app) {
        renderPage('region', [
            'regions' => (new RegionController($app))->index()
        ]);
    });

    // ================= VILLE =================
    $router->get('/ville', function() use ($app) {
        renderPage('ville', [
            'villes' => (new VilleController($app))->index()
        ]);
    });

    // ================= DON =================
    $router->get('/don', function() use ($app) {
        renderPage('don', [
            'dons' => (new DonController($app))->index()
        ]);
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

    // ================= API =================
    $router->get('/api/regions', function() use ($app) {
        $app->json((new RegionController($app))->index());
    });

    $router->get('/api/regions/@id', function($id) use ($app) {
        $app->json((new RegionController($app))->get($id));
    });

    $router->get('/api/villes', function() use ($app) {
        $app->json((new VilleController($app))->index());
    });

    // ================= PRODUIT =================
    $router->get('/produit', function() use ($app) {
        renderPage('insertDon', [
            'produits' => (new ProduitController($app))->index()
        ]);
    });

    // ================= RECAP =================
    $router->get('/recapitulatif', function() use ($app) {
        renderPage('recapitulatif',
            (new RecapitulatifController($app))->getStats()
        );
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

    // ================= SIMULATION =================
    $router->get('/simulation/@id', function($id) use ($app) {
        (new SimulationAchatController($app))->index($id);
    });

}, [SecurityHeadersMiddleware::class, InjectCssMiddleware::class]);
