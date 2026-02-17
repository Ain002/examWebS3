<?php

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
use app\models\DonModel;

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

    // ── Accueil → dashboard ──
    $router->get('/', function() use ($app) {
        $ctrl = new DashboardController($app);
        $data = $ctrl->index();
        renderPage('dashboard', $data);
    });

    $router->get('/dashboard', function() use ($app) {
        $ctrl = new DashboardController($app);
        $data = $ctrl->index();
        renderPage('dashboard', $data);
    });

    $router->get('/region', function() use ($app) {
        $ctrl = new RegionController($app);
        $regions = $ctrl->index();
        renderPage('region', ['regions' => $regions]);
    });

    $router->get('/ville', function() use ($app) {
        $ctrl = new VilleController($app);
        $villes = $ctrl->index();
        renderPage('ville', ['villes' => $villes]);
    });

    $router->get('/don', function() use ($app) {
        $ctrl = new DonController($app);
        $dons = $ctrl->index();
        renderPage('don', ['dons' => $dons]);
    });

    $router->get('/recapitulatif', function() use ($app) {
        $ctrl = new RecapitulatifController($app);
        $stats = $ctrl->getStats();
        renderPage('recapitulatif', $stats);
    });

    $router->get('/produit', function() use ($app) {
        $ctrl = new ProduitController($app);
        $produits = $ctrl->index();
        renderPage('insertDon', ['produits' => $produits]);
    });

    $router->post('/don', function() use ($app) {
        $data = [
            'idProduit' => $_POST['idProduit'] ?? null,
            'quantite'  => $_POST['quantite']  ?? null,
            'dateDon'   => $_POST['dateDon']   ?? null,
        ];
        if ($data['idProduit'] && $data['quantite'] && $data['dateDon']) {
            $ctrl = new DonController($app);
            $ctrl->create($data);
        }
        Flight::redirect('/don');
    });

    // ── API simulation besoin (GET, sans toucher à la BDD) ──
    $router->get('/api/besoins/@id/simuler', function($id) use ($app) {
        $ctrl   = new BesoinController($app);
        $result = $ctrl->simulerAchatBesoin((int)$id);
        $app->json($result);
    });

    // ── Page de simulation don (HTML, sans toucher à la BDD) ──
    $router->get('/don/@id/simuler', function($id) use ($app) {
        $method = isset($_GET['method']) ? trim($_GET['method']) : 'fifo';
        $ctrl   = new DonController($app);
        $result = $ctrl->simuler((int)$id, $method);

        // Normaliser le plan pour la vue :
        // - les simulations FIFO/MIN retournent 'plan'
        // - la simulation proportionnelle retourne 'par_ville' avec 'details'
        $plan = $result['plan'] ?? [];
        if (empty($plan) && !empty($result['par_ville'])) {
            foreach ($result['par_ville'] as $pv) {
                foreach ($pv['details'] as $d) {
                    $deja = isset($d['deja_attribue']) ? (int)$d['deja_attribue'] : 0;
                    $reste_avant = isset($d['reste_avant']) ? (int)$d['reste_avant'] : 0;
                    $alloue = isset($d['alloue']) ? (int)$d['alloue'] : 0;
                    $plan[] = [
                        'idBesoin' => (int)$d['idBesoin'],
                        'ville' => $pv['ville'] ?? '',
                        'quantite_demande' => $deja + $reste_avant,
                        'deja_attribue' => $deja,
                        'attribuer' => $alloue,
                        'restant_apres' => max(0, $reste_avant - $alloue),
                    ];
                }
            }
        }

        // Always provide a DonModel instance to the view so templates using ->id work
        $donForView = DonModel::getById((int)$id);
        renderPage('donSimulation', [
            'don'                  => $donForView,
            'plan'                 => $plan,
            'quantite_distribuee'  => $result['quantite_distribuee'] ?? 0,
            'quantite_restante'    => $result['quantite_restante'] ?? 0,
            'method'               => $method,
        ]);
    });

    // ── API JSON ──
    $router->get('/api/regions', function() use ($app) {
        $app->json((new RegionController($app))->index());
    });

    $router->get('/api/regions/@id', function($id) use ($app) {
        $app->json((new RegionController($app))->get($id));
    });

    $router->get('/api/villes', function() use ($app) {
        $app->json((new VilleController($app))->index());
    });

    $router->get('/api/villes/@id', function($id) use ($app) {
        $app->json((new VilleController($app))->get($id));
    });

    $router->get('/api/dons', function() use ($app) {
        $app->json((new DonController($app))->index());
    });

    $router->get('/api/dons/@id', function($id) use ($app) {
        $app->json((new DonController($app))->get($id));
    });

    // API: simulation de distribution d'un don (ne touche pas à la BDD)
    $router->get('/api/dons/@id/simuler', function($id) use ($app) {
        $method = isset($_GET['method']) ? trim($_GET['method']) : 'fifo';
        $ctrl = new DonController($app);
        $res = $ctrl->simuler((int)$id, $method);
        $app->json($res);
    });

    $router->post('/api/dons/@id/distribuer', function($id) use ($app) {
        $ctrl = new DonController($app);
        $result = $ctrl->distribuerDon($id);
        if ($result['success']) {
            Flight::redirect('/don?success=' . urlencode($result['message']));
        } else {
            Flight::redirect('/don?error=' . urlencode($result['message']));
        }
    });

    // ── Besoins (restant AVANT /@idVille) ──
    $router->get('/besoin/restant', function() use ($app) {
        $ctrl    = new BesoinSatisfaitController($app);
        $besoins = $ctrl->getBesoinRestant();
        $villes  = (new VilleController($app))->index();
        renderPage('besoinRestant', ['besoins' => $besoins, 'villes' => $villes]);
    });

    $router->get('/besoin/acheter/@id', function($id) use ($app) {
        $ctrl = new BesoinController($app);
        $res  = $ctrl->achatBesoin($id);
        if (is_array($res) && !empty($res['success'])) {
            $query = 'success=1';
        } else {
            $msg   = is_array($res) && isset($res['error']) ? $res['error'] : 'Erreur';
            $query = 'error=' . rawurlencode($msg);
        }
        $app->redirect(BASE_URL . '/besoin/restant?' . $query);
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
        $ctrl    = new BesoinController($app);
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
        $d    = $ctrl->get($id);
        if ($d) {
            $ctrl->delete($id);
            Flight::redirect(BASE_URL . '/besoin/' . $d->idVille);
        }
    });

    $router->get('/api/recapitulatif', function() use ($app) {
        $ctrl = new RecapitulatifController($app);
        $app->json($ctrl->getStats());
    });

}, [SecurityHeadersMiddleware::class]);
