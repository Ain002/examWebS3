<?php
namespace app\controllers;

use flight\Engine;
use app\models\BesoinModel;

class BesoinController {
    protected Engine $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function index() {
        return BesoinModel::getAll();
    }

    public function get($id) {
        return BesoinModel::getById($id);
    }

    public function create($data) {
        $d = new BesoinModel($data);
        return $d->save();
    }

    public function delete($id) {
        $d = BesoinModel::getById($id);
        if ($d) return $d->delete();
        return false;
    }

    public function update($id, $data) {
        $d = BesoinModel::getById($id);
        if ($d) {
            // appliquer les valeurs fournies
            $d->idType = $data['idType'] ?? $d->idType;
            $d->idVille = $data['idVille'] ?? $d->idVille;
            $d->idProduit = $data['idProduit'] ?? $d->idProduit;
            $d->quantite = $data['quantite'] ?? $d->quantite;
            return $d->save();
        }
        return false;
    }

    public function getByVille($villeId) {
        return BesoinModel::getByVille($villeId);
    }
}
