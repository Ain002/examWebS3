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
        
        // 3. Statistiques par type de besoin
        $stmt = $db->query("
            SELECT 
                tb.description as type,
                COUNT(b.id) as nombre,
                SUM(b.quantite * p.pu) as montant,
                COUNT(bs.id) as nombre_satisfait
            FROM besoin b
            INNER JOIN produit p ON b.idProduit = p.id
            INNER JOIN typeBesoin tb ON b.idType = tb.id
            LEFT JOIN besoinSatisfait bs ON b.id = bs.idBesoin
            GROUP BY tb.id, tb.description
        ");
        $parType = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // 4. Statistiques des dons
        $stmt = $db->query("
            SELECT 
                COUNT(DISTINCT d.id) as nombre_dons,
                SUM(d.quantite * p.pu) as valeur_totale_dons,
                COUNT(DISTINCT dd.idDon) as dons_distribues
            FROM don d
            INNER JOIN produit p ON d.idProduit = p.id
            LEFT JOIN donDistribue dd ON d.id = dd.idDon
        ");
        $donStats = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // 5. Statistiques des achats
        $stmt = $db->query("
            SELECT 
                COUNT(a.id) as nombre_achats,
                SUM(a.montant) as montant_total_achats
            FROM achat a
        ");
        $achatStats = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // 6. Top 5 des villes avec le plus de besoins
        $stmt = $db->query("
            SELECT 
                v.nom as ville,
                COUNT(b.id) as nombre_besoins,
                SUM(b.quantite * p.pu) as montant_besoins
            FROM besoin b
            INNER JOIN ville v ON b.idVille = v.id
            INNER JOIN produit p ON b.idProduit = p.id
            GROUP BY v.id, v.nom
            ORDER BY montant_besoins DESC
            LIMIT 5
        ");
        $topVilles = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // 7. Argent disponible
        $stmt = $db->prepare("
            SELECT SUM(d.quantite - COALESCE(a.total_attribue, 0)) as argent_disponible
            FROM don d
            LEFT JOIN (
                SELECT idDon, SUM(quantite) as total_attribue
                FROM attribution
                GROUP BY idDon
            ) a ON d.id = a.idDon
            WHERE d.idProduit = 4
        ");
        $stmt->execute();
        $argentDispo = $stmt->fetch(\PDO::FETCH_ASSOC);
        
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
            'pourcentage_satisfait' => $nombreTotal > 0 ? round(($nombreSatisfait / $nombreTotal) * 100, 2) : 0,
            'par_type' => $parType,
            'nombre_dons' => $donStats['nombre_dons'] ?? 0,
            'valeur_totale_dons' => $donStats['valeur_totale_dons'] ?? 0,
            'dons_distribues' => $donStats['dons_distribues'] ?? 0,
            'nombre_achats' => $achatStats['nombre_achats'] ?? 0,
            'montant_total_achats' => $achatStats['montant_total_achats'] ?? 0,
            'top_villes' => $topVilles,
            'argent_disponible' => $argentDispo['argent_disponible'] ?? 0
        ];
    }
}
