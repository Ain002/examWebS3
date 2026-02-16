<?php
namespace app\controllers;

use flight\Engine;
use app\models\RegionModel;
use app\models\VilleModel;
use app\models\DonModel;
use app\models\BesoinModel;
use app\models\AttributionModel;
use app\models\ProduitModel;

class DashboardController {
    protected Engine $app;

    public function __construct($app) {
        $this->app = $app;
    }

    /**
     * Construit les données pour la page tableau de bord.
     * Retourne un tableau contenant les compteurs et une liste de villes
     * avec leurs besoins et les attributions (dons) associées.
     *
     * @return array
     */
    public function index(): array {
        $regions = RegionModel::getAll();
        $villes = VilleModel::getAll();
        $dons = DonModel::getAll();

        $regions_count = is_array($regions) ? count($regions) : 0;
        $villes_count = is_array($villes) ? count($villes) : 0;
        $dons_count = is_array($dons) ? count($dons) : 0;

        $cities = [];
        foreach ($villes as $ville) {
            $besoins = BesoinModel::getByVille($ville->id);
            $besoinsData = [];

            foreach ($besoins as $besoin) {
                $produit = $besoin->getProduit();
                $attributions = AttributionModel::getByBesoin($besoin->id);
                $attributionsData = [];

                foreach ($attributions as $att) {
                    $don = $att->getDon();
                    $attributionsData[] = [
                        'attribution' => $att,
                        'don' => $don,
                        'produit' => $don ? $don->getProduit() : null,
                    ];
                }

                $besoinsData[] = [
                    'besoin' => $besoin,
                    'produit' => $produit,
                    'attributions' => $attributionsData,
                ];
            }

            $cities[] = [
                'ville' => $ville,
                'besoins' => $besoinsData,
            ];
        }

        return [
            'regions_count' => $regions_count,
            'villes_count' => $villes_count,
            'dons_count' => $dons_count,
            'cities' => $cities,
            // expose aussi les listes brutes si besoin
            'regions' => $regions,
            'villes' => $villes,
            'dons' => $dons,
        ];
    }
}
