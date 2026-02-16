<?php

namespace app\controllers;

use flight\Engine;
use app\models\ProduitModel;

class ProduitController {

    protected Engine $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function index() {
        return ProduitModel::getAll();
    }


    public function get($id) {
        return ProduitModel::getById($id);
    }

 
    public function create($data) {
        $produit = new ProduitModel($data);
        return $produit->save();
    }

  
    public function update($id, $data) {
        $produit = ProduitModel::getById($id);
        if (!$produit) {
            return false;
        }

        $produit->hydrate($data);
        return $produit->save();
    }

    public function delete($id) {
        $produit = ProduitModel::getById($id);
        if ($produit) {
            return $produit->delete();
        }
        return false;
    }
}
