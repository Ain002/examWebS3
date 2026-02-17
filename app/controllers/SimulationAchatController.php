<?php

namespace app\controllers;

use flight\Engine;
use app\models\BesoinModel;
use app\models\ProduitModel;
use app\models\ConfigFraisAchatModel;
use app\models\DonModel;

class SimulationAchatController {
    protected Engine $app;

    public function __construct($app) {
        $this->app = $app;
    }

    // Page simulation
    public function index($idBesoin): array { // <- retourne array maintenant
        $besoin = BesoinModel::getById($idBesoin);
        if (!$besoin) die("Besoin introuvable");

        $produit = ProduitModel::getById($besoin->idProduit);
        $config = ConfigFraisAchatModel::getLatest();

        $montantBase = $besoin->quantite * $produit->pu;
        $montantTotal = $montantBase * (1 + $config->pourcentage / 100);

        $argentDisponible = DonModel::getTotalArgentDisponible();

        return [
            'besoin' => $besoin,
            'produit' => $produit,
            'montantBase' => $montantBase,
            'montantTotal' => $montantTotal,
            'taxe' => $config->pourcentage,
            'argentDisponible' => $argentDisponible,
            'reste' => $argentDisponible - $montantTotal
        ];
    }
}



