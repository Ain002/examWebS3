<?php
namespace app\controllers;

use flight\Engine;
use app\models\BesoinModel;
use app\models\AchatModel;
use app\models\ConfigFraisAchatModel;
use app\models\ProduitModel;
use app\models\DonModel;
use app\models\AttributionModel;
use app\models\BesoinSatisfaitModel;


class BesoinController
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function index()
    {
        return BesoinModel::getAll();
    }

    public function get($id)
    {
        return BesoinModel::getById($id);
    }

    public function create($data)
    {
        $d = new BesoinModel($data);
        return $d->save();
    }

    public function delete($id)
    {
        $d = BesoinModel::getById($id);
        if ($d)
            return $d->delete();
        return false;
    }

    public function update($id, $data)
    {
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

    public function getByVille($villeId)
    {
        return BesoinModel::getByVille($villeId);
    }
   
    public function achatBesoin($idBesoin)
    {
        $besoin = BesoinModel::getById($idBesoin);
        if (!$besoin)
            return ['success' => false, 'error' => 'Besoin non trouvé'];

        $db = $this->app->db();

        try {
            // 1. Vérifier si le besoin n'a pas déjà été acheté
            $achatExistant = AchatModel::getByBesoin($idBesoin);
            if ($achatExistant) {
                return ['success' => false, 'error' => 'Ce besoin a déjà été acheté'];
            }

            // 2. Vérifier qu'aucun don du même produit n'est disponible
            // 2. Vérifier qu'aucun don du même produit n'est disponible
            if (DonModel::donsDisponiblesPourProduit($besoin->idProduit)) {
                return [
                    'success' => false,
                    'error' => 'Des dons du même produit sont encore disponibles. Utilisez les dons avant d\'acheter.'
                ];
            }


            // 3. Récupérer le prix unitaire du produit
            $produit = ProduitModel::getById($besoin->idProduit);
            if (!$produit) {
                return ['success' => false, 'error' => 'Produit non trouvé'];
            }

            // 4. Récupérer la taxe (pourcentage)
            $config = ConfigFraisAchatModel::getLatest();
            if (!$config) {
                return ['success' => false, 'error' => 'Configuration des frais non trouvée'];
            }

            // 5. Calculer le montant: quantité * prix unitaire * (1 + pourcentage/100)
            $montantBase = $besoin->quantite * $produit->pu;
            $montantTotal = $montantBase * (1 + $config->pourcentage / 100);

            // 6. Vérifier le total des dons en argent disponibles
            $argentDisponible = DonModel::getTotalArgentDisponible();

            if ($argentDisponible < $montantTotal) {
                return [
                    'success' => false,
                    'error' => 'Fonds insuffisants',
                    'disponible' => $argentDisponible,
                    'requis' => $montantTotal
                ];
            }

            // 7. Transaction: effectuer l'achat
            $db->beginTransaction();

            // Créer l'achat
            $achat = new AchatModel();
            $achat->idBesoin = $idBesoin;
            $achat->montant = $montantTotal;
            $achat->save();

            // Marquer le besoin comme satisfait
            $besoinSatisfait = new BesoinSatisfaitModel();
            $besoinSatisfait->idBesoin = $idBesoin;
            $besoinSatisfait->dateSatisfaction = date('Y-m-d');
            $besoinSatisfait->save();

            // Déduire le montant des dons en argent (créer des attributions)
            $stmt = $db->prepare("
                SELECT d.id, d.quantite, COALESCE(SUM(a.quantite), 0) as distribue
                FROM don d
                LEFT JOIN attribution a ON d.id = a.idDon
                WHERE d.idProduit = 4
                GROUP BY d.id, d.quantite
                HAVING d.quantite - distribue > 0
                ORDER BY d.dateDon ASC
            ");

            $stmt->execute();
            $donsArgent = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $montantRestant = $montantTotal;
            foreach ($donsArgent as $donData) {
                if ($montantRestant <= 0)
                    break;

                $disponible = $donData['quantite'] - $donData['distribue'];
                $aDeduire = min($montantRestant, $disponible);

                // Créer une attribution
                $attribution = new AttributionModel();
                $attribution->idBesoin = $idBesoin;
                $attribution->idDon = $donData['id'];
                $attribution->quantite = $aDeduire;
                $attribution->save();

                $montantRestant -= $aDeduire;
            }

            $db->commit();

            return [
                'success' => true,
                'montant' => $montantTotal,
                'montant_base' => $montantBase,
                'taxe_pourcent' => $config->pourcentage
            ];

        } catch (\Throwable $e) {
            // rollback only if a transaction is active to avoid "There is no active transaction"
            try {
                if (isset($db) && $db instanceof \PDO && $db->inTransaction()) {
                    $db->rollBack();
                }
            } catch (\Throwable $rollbackEx) {
                // Ne pas masquer l'erreur initiale ; inclure info de rollback si utile
                return [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'rollback_error' => $rollbackEx->getMessage()
                ];
            }

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
