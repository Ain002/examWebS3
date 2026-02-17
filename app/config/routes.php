<?php

use app\controllers\DashboardController;
use app\controllers\RegionController;
use app\controllers\VilleController;
use app\controllers\DonController;
use app\controllers\BesoinController;
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

	// Liste par ville
	$router->get('/besoin/@idVille', function($idVille){
		$ctrl = new BesoinController(Flight::app());
		$besoins = $ctrl->getByVille($idVille);
		$villeId = $idVille;
		$produits = ProduitModel::getAll();
		$types = TypeBesoinModel::getAll();

		require __DIR__ . '/../views/besoin.php';
;
	});

    $router->get('/don', function() use ($app) {
        $ctrl = new DonController($app);
        $dons = $ctrl->index();
        renderPage('don', ['dons' => $dons]);
    });

		require __DIR__ . '/../views/besoinForm.php';
;
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


}, [SecurityHeadersMiddleware::class, InjectCssMiddleware::class]);