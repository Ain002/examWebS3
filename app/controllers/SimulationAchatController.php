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
    public function index($idBesoin): array
    {
        $besoin = BesoinModel::getById($idBesoin);
        if (!$besoin) die("Besoin introuvable");
    
        $produit = ProduitModel::getById($besoin->idProduit);
        $config  = ConfigFraisAchatModel::getLatest();
    
        $montantBase  = $besoin->quantite * $produit->pu;
        $montantTotal = $montantBase * (1 + $config->pourcentage / 100);
    
        $donsNatureDisponibles = DonModel::donsDisponiblesPourProduit($produit->id);
    
        $achatAutorise = true;
        $messageErreur = null;
        $argentDisponible = 0;
        $reste = 0;
    
        if ($donsNatureDisponibles) {
            $achatAutorise = false;
            $messageErreur = "Des dons en nature sont encore disponibles pour ce produit.Vous devez les utiliser avant d'acheter.";
        } else {
            $argentDisponible = DonModel::getTotalArgentDisponible();
    
            if ($argentDisponible < $montantTotal) {
                $achatAutorise = false;
                $messageErreur = "Fonds insuffisants";
            }
    
            $reste = $argentDisponible - $montantTotal;
        }
    
        return [
            'besoin' => $besoin,
            'produit' => $produit,
            'montantBase' => $montantBase,
            'montantTotal' => $montantTotal,
            'taxe' => $config->pourcentage,
            'argentDisponible' => $argentDisponible,
            'reste' => $reste,
            'achatAutorise' => $achatAutorise,
            'messageErreur' => $messageErreur
        ];
    }
    
}



