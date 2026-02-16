<?php
namespace app\controllers;

use flight\Engine;
use app\models\BesoinModel;
use app\models\ProduitModel;
use app\models\BesoinSatisfaitModel;

class RecapitulatifController {
    protected Engine $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function index() {
        $stats = $this->getStats();
        return $stats;
    }

    public function getStats() {
        $db = $this->app->db();
        
        // 1. Calculer le montant total de tous les besoins
        $stmt = $db->query("
            SELECT 
                SUM(b.quantite * p.pu) as montant_total,
                COUNT(b.id) as nombre_total
            FROM besoin b
            INNER JOIN produit p ON b.idProduit = p.id
        ");
        $totaux = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // 2. Calculer le montant des besoins satisfaits
        $stmt = $db->query("
            SELECT 
                SUM(b.quantite * p.pu) as montant_satisfait,
                COUNT(DISTINCT b.id) as nombre_satisfait
            FROM besoin b
            INNER JOIN produit p ON b.idProduit = p.id
            INNER JOIN besoinSatisfait bs ON b.id = bs.idBesoin
        ");
        $satisfaits = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // 3. Calculer les montants
        $montantTotal = $totaux['montant_total'] ?? 0;
        $montantSatisfait = $satisfaits['montant_satisfait'] ?? 0;
        $montantRestant = $montantTotal - $montantSatisfait;
        
        $nombreTotal = $totaux['nombre_total'] ?? 0;
        $nombreSatisfait = $satisfaits['nombre_satisfait'] ?? 0;
        $nombreRestant = $nombreTotal - $nombreSatisfait;
        
        return [
            'montant_total' => $montantTotal,
            'montant_satisfait' => $montantSatisfait,
            'montant_restant' => $montantRestant,
            'nombre_total' => $nombreTotal,
            'nombre_satisfait' => $nombreSatisfait,
            'nombre_restant' => $nombreRestant,
            'pourcentage_satisfait' => $nombreTotal > 0 ? round(($nombreSatisfait / $nombreTotal) * 100, 2) : 0
        ];
    }
}
