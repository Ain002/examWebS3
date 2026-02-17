<?php
namespace app\controllers;

use flight\Engine;
use flight;
use app\models\BesoinSatisfaitModel;

// Controller to compute remaining besoins (demandes non satisfaites)

class BesoinSatisfaitController {
    protected Engine $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function getAll () {
        return BesoinSatisfaitModel::getAll();
    }

    public function getByVille($villeId) {
        return BesoinSatisfaitModel::getByVille($villeId);
    }

    public function getBesoinRestant ($idVille = null) {
        // support optional filter via explicit parameter or GET
        $idVille = $idVille ?? ($_GET['idVille'] ?? null);
        return BesoinSatisfaitModel::getBesoinRestant($idVille);
    }
}
