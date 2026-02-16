<?php
namespace app\controllers;

use flight\Engine;
use app\models\DonModel;
use app\models\DonDistribueModel;
use app\models\BesoinModel;
use app\models\AttributionModel;
use app\models\BesoinSatisfaitModel;

class DonController {
    protected Engine $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function index() {
        $dons = DonModel::getAll();
        $db = $this->app->db();
        
        // Calculer la quantité restante pour chaque don
        foreach ($dons as $don) {
            $stmt = $db->prepare("
                SELECT COALESCE(SUM(quantite), 0) as total_distribue
                FROM attribution
                WHERE idDon = ?
            ");
            $stmt->execute([$don->id]);
            $totalDistribue = $stmt->fetch(\PDO::FETCH_ASSOC)['total_distribue'];
            
            $don->quantiteRestante = $don->quantite - $totalDistribue;
        }
        
        return $dons;
    }

    public function get($id) {
        return DonModel::getById($id);
    }

    public function create($data) {
        $d = new DonModel($data);
        return $d->save();
    }

    public function delete($id) {
        $d = DonModel::getById($id);
        if ($d) return $d->delete();
        return false;
    }

    public function distribuerDon($id){
        $don = DonModel::getById($id);
        if (!$don) return ['success' => false, 'error' => 'Don non trouvé'];
        
        $db = $this->app->db();
        
        try {
            // Commencer une transaction
            $db->beginTransaction();
            
            // 1. Récupérer tous les besoins non satisfaits pour ce produit, triés par ordre de création
            $stmt = $db->prepare("
                SELECT b.* 
                FROM besoin b
                LEFT JOIN besoinSatisfait bs ON b.id = bs.idBesoin
                WHERE b.idProduit = ? AND bs.id IS NULL
                ORDER BY b.id ASC
            ");
            $stmt->execute([$don->idProduit]);
            $besoins = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $quantiteRestante = $don->quantite;
            
            // 2. Distribuer le don progressivement
            foreach ($besoins as $besoinData) {
                if ($quantiteRestante <= 0) break;
                
                $besoin = new BesoinModel($besoinData);
                
                // Calculer la quantité déjà attribuée à ce besoin
                $stmtAttr = $db->prepare("
                    SELECT COALESCE(SUM(quantite), 0) as total 
                    FROM attribution 
                    WHERE idBesoin = ?
                ");
                $stmtAttr->execute([$besoin->id]);
                $dejaAttribue = $stmtAttr->fetch(\PDO::FETCH_ASSOC)['total'];
                
                // Calculer le reste à satisfaire pour ce besoin
                $resteASatisfaire = $besoin->quantite - $dejaAttribue;
                
                if ($resteASatisfaire > 0) {
                    // Quantité à attribuer = minimum entre ce qui reste du don et ce qui reste à satisfaire
                    $quantiteAAttribuer = min($quantiteRestante, $resteASatisfaire);
                    
                    // Créer l'attribution
                    $attribution = new AttributionModel();
                    $attribution->idBesoin = $besoin->id;
                    $attribution->idDon = $don->id;
                    $attribution->quantite = $quantiteAAttribuer;
                    $attribution->save();
                    
                    // Déduire la quantité attribuée
                    $quantiteRestante -= $quantiteAAttribuer;
                    
                    // Vérifier si le besoin est maintenant complètement satisfait
                    if ($dejaAttribue + $quantiteAAttribuer >= $besoin->quantite) {
                        $besoinSatisfait = new BesoinSatisfaitModel();
                        $besoinSatisfait->idBesoin = $besoin->id;
                        $besoinSatisfait->dateSatisfaction = date('Y-m-d');
                        $besoinSatisfait->save();
                    }
                }
            }
            
            // 3. Enregistrer le don comme distribué
            $donDistribue = new DonDistribueModel();
            $donDistribue->idDon = $don->id;
            $donDistribue->dateDistribution = date('Y-m-d');
            $donDistribue->save();
            
            // Valider la transaction
            $db->commit();
            
            return [
                'success' => true,
                'quantite_distribuee' => $don->quantite - $quantiteRestante,
                'quantite_restante' => $quantiteRestante
            ];
            
        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            $db->rollBack();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    
}
