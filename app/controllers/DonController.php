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
        if (!$don) return ['success' => false, 'message' => 'Don non trouvé'];
        
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
            
            $qDistrib = $don->quantite - $quantiteRestante;
            $msg = "Distribution effectuée. Quantité distribuée : $qDistrib. Quantité restante : $quantiteRestante.";
            return [
                'success' => true,
                'quantite_distribuee' => $qDistrib,
                'quantite_restante' => $quantiteRestante,
                'message' => $msg
            ];
            
        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            $db->rollBack();
            return [
                'success' => false,
                'message' => 'Erreur lors de la distribution : ' . $e->getMessage()
            ];
        }
    }


    public function simulateDistribuerDon($id)
    {
        try {
            $don = DonModel::getById($id);
            if (!$don) {
                return ['success' => false, 'message' => 'Don non trouvé'];
            }

            $db = $this->app->db();

            $stmt = $db->prepare("
                SELECT b.*
                FROM besoin b
                LEFT JOIN besoinSatisfait bs ON b.id = bs.idBesoin
                WHERE b.idProduit = ? AND bs.id IS NULL
                ORDER BY b.id ASC
            ");
            $stmt->execute([$don->idProduit]);
            $besoins = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $quantiteRestante = (int)$don->quantite;
            $plan = [];

            foreach ($besoins as $besoin) {

                if ($quantiteRestante <= 0) break;

                $stmtAttr = $db->prepare("
                    SELECT COALESCE(SUM(quantite),0) as total
                    FROM attribution
                    WHERE idBesoin = ?
                ");
                $stmtAttr->execute([$besoin['id']]);
                $dejaAttribue = (int)$stmtAttr->fetch(\PDO::FETCH_ASSOC)['total'];

                $reste = (int)$besoin['quantite'] - $dejaAttribue;

                if ($reste <= 0) continue;

                $attribuer = min($quantiteRestante, $reste);

                $plan[] = [
                    'idBesoin' => $besoin['id'],
                    'ville' => $besoin['idVille'],
                    'quantite_demande' => (int)$besoin['quantite'],
                    'deja_attribue' => $dejaAttribue,
                    'attribuer' => $attribuer,
                    'restant_apres' => $reste - $attribuer
                ];

                $quantiteRestante -= $attribuer;
            }

            return [
                'success' => true,
                'don' => [
                    'id' => $don->id,
                    'idProduit' => $don->idProduit,
                    'quantite' => $don->quantite
                ],
                'plan' => $plan,
                'quantite_distribuee' => $don->quantite - $quantiteRestante,
                'quantite_restante' => $quantiteRestante
            ];

        } catch (\Exception $e) {
            $db->rollBack();
            return ['success' => false, 'message' => 'Erreur lors de la distribution proportionnelle : ' . $e->getMessage()];
        }
    }

    /**
     * Dispatcher de simulation : choisit la méthode et retourne un plan sans écrire en base
     * @param int $id
     * @param string $method one of: 'fifo', 'min', 'proportionnel'
     * @return array
     */
    public function simuler($id, $method = 'fifo')
    {
        $method = strtolower(trim($method ?? 'fifo'));
        switch ($method) {
            case 'proportionnel':
            case 'pro rata':
            case 'prorata':
                return $this->simulateDistribuerDonProportionnel($id);
            case 'min':
            case 'smallest':
                return $this->simulateDistribuerDonMin($id);
            case 'fifo':
            default:
                return $this->simulateDistribuerDon($id);
        }
    }

    /**
     * Simulation de la distribution proportionnelle (ne modifie pas la BDD)
     * Retourne une structure proche de `distribuerDonProportionnel` mais sans side-effects
     */
    public function simulateDistribuerDonProportionnel($id)
    {
        $don = DonModel::getById($id);
        if (!$don) return ['success' => false, 'message' => 'Don non trouvé'];

        $db = $this->app->db();

        try {
            // 1) besoins restants par ville
            $stmt = $db->prepare(
                "SELECT v.id as idVille, v.nom as ville, SUM(b.quantite - COALESCE(a.total_attribue,0)) as restant\n"
                . "FROM besoin b\n"
                . "INNER JOIN ville v ON b.idVille = v.id\n"
                . "LEFT JOIN (SELECT idBesoin, SUM(quantite) as total_attribue FROM attribution GROUP BY idBesoin) a ON b.id = a.idBesoin\n"
                . "WHERE b.idProduit = ?\n"
                . "GROUP BY v.id, v.nom\n"
                . "HAVING restant > 0"
            );
            $stmt->execute([$don->idProduit]);
            $villes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $quantiteRestante = (int)$don->quantite;

            if (empty($villes)) {
                return [
                    'success' => true,
                    'don' => ['id' => $don->id, 'idProduit' => $don->idProduit, 'quantite' => $don->quantite],
                    'method' => 'proportionnel',
                    'par_ville' => [],
                    'quantite_distribuee' => 0,
                    'quantite_restante' => $quantiteRestante,
                    'message' => 'Aucun besoin non satisfait pour ce produit.'
                ];
            }

            // calcul proportionnel initial
            $totalRestant = 0.0;
            foreach ($villes as $r) $totalRestant += (float)$r['restant'];

            $allocations = [];
            $allocatedSum = 0;
            foreach ($villes as $r) {
                $restant = (int)$r['restant'];
                $fraction = $totalRestant > 0 ? ((float)$restant / $totalRestant) : 0.0;
                $floatAlloc = $don->quantite * $fraction;
                $floorAlloc = (int)floor($floatAlloc);
                $allocations[] = [
                    'idVille' => (int)$r['idVille'],
                    'ville' => $r['ville'],
                    'restant' => $restant,
                    'floatAlloc' => $floatAlloc,
                    'floorAlloc' => $floorAlloc,
                    'frac' => $floatAlloc - $floorAlloc,
                    'alloc' => $floorAlloc,
                    'part_proportionnelle' => round($floatAlloc, 4),
                    'part_pct' => $totalRestant > 0 ? round(($restant / $totalRestant) * 100, 2) : 0,
                ];
                $allocatedSum += $floorAlloc;
            }

            // distribuer le reste selon les plus grands restes
            $remainder = $don->quantite - $allocatedSum;
            if ($remainder > 0) {
                usort($allocations, function($a, $b) {
                    if ($a['frac'] == $b['frac']) return $a['idVille'] <=> $b['idVille'];
                    return ($a['frac'] > $b['frac']) ? -1 : 1;
                });
                $i = 0;
                while ($remainder > 0) {
                    $allocations[$i]['alloc'] += 1;
                    $remainder -= 1;
                    $i++;
                    if ($i >= count($allocations)) $i = 0;
                }
                usort($allocations, function($a, $b){ return $a['idVille'] <=> $b['idVille']; });
            }

            // caper les allocations et récupérer pool
            $sumAlloc = 0;
            foreach ($allocations as &$a) {
                if ($a['alloc'] > $a['restant']) {
                    $a['alloc'] = $a['restant'];
                }
                $sumAlloc += $a['alloc'];
            }
            unset($a);

            $pool = $don->quantite - $sumAlloc;

            while ($pool > 0) {
                $candidates = [];
                foreach ($allocations as $idx => $a) {
                    $needLeft = $a['restant'] - $a['alloc'];
                    if ($needLeft > 0) $candidates[] = ['idx' => $idx, 'needLeft' => $needLeft, 'idVille' => $a['idVille']];
                }
                if (empty($candidates)) break;

                usort($candidates, function($x, $y) {
                    if ($x['needLeft'] == $y['needLeft']) return $x['idVille'] <=> $y['idVille'];
                    return ($x['needLeft'] > $y['needLeft']) ? -1 : 1;
                });

                $give = min($pool, count($candidates));
                for ($i = 0; $i < $give; $i++) {
                    $allocations[$candidates[$i]['idx']]['alloc'] += 1;
                    $pool -= 1;
                }
            }

            // Remplir le détail par ville (sans insert)
            $perVille = [];
            $quantiteRestante = (int)$don->quantite;
            foreach ($allocations as $a) {
                $qtyForVille = (int)$a['alloc'];
                $distributedInVille = 0;
                $details = [];

                if ($qtyForVille > 0) {
                    $stmtB = $db->prepare("SELECT b.* FROM besoin b LEFT JOIN (SELECT idBesoin, SUM(quantite) as total_attribue FROM attribution GROUP BY idBesoin) a ON b.id = a.idBesoin WHERE b.idProduit = ? AND b.idVille = ? AND (b.quantite - COALESCE(a.total_attribue,0)) > 0 ORDER BY b.id ASC");
                    $stmtB->execute([$don->idProduit, $a['idVille']]);
                    $besoinsVille = $stmtB->fetchAll(\PDO::FETCH_ASSOC);

                    foreach ($besoinsVille as $bData) {
                        if ($qtyForVille <= 0) break;
                        $stmtAttr = $db->prepare("SELECT COALESCE(SUM(quantite),0) as total FROM attribution WHERE idBesoin = ?");
                        $stmtAttr->execute([$bData['id']]);
                        $deja = (int)$stmtAttr->fetch(\PDO::FETCH_ASSOC)['total'];
                        $reste = $bData['quantite'] - $deja;
                        if ($reste <= 0) continue;

                        $toGive = min($qtyForVille, $reste);

                        $qtyForVille -= $toGive;
                        $distributedInVille += $toGive;
                        $quantiteRestante -= $toGive;
                        $details[] = ['idBesoin' => $bData['id'], 'alloue' => $toGive, 'reste_avant' => $reste, 'deja_attribue' => $deja];
                    }
                }

                $perVille[] = [
                    'idVille' => $a['idVille'],
                    'ville' => $a['ville'],
                    'alloue_initial' => $a['alloc'],
                    'distribue' => $distributedInVille,
                    'details' => $details,
                    'part_proportionnelle' => $a['part_proportionnelle'] ?? null,
                    'part_pct' => $a['part_pct'] ?? null,
                ];
            }

            $qDistrib = $don->quantite - $quantiteRestante;
            return [
                'success' => true,
                'don' => ['id' => $don->id, 'idProduit' => $don->idProduit, 'quantite' => $don->quantite],
                'method' => 'proportionnel',
                'par_ville' => $perVille,
                'quantite_distribuee' => $qDistrib,
                'quantite_restante' => $quantiteRestante,
            ];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erreur lors de la simulation proportionnelle : ' . $e->getMessage()];
        }
    }

    /**
     * Simulation de la distribution min (priorité aux besoins les plus petits)
     */
    public function simulateDistribuerDonMin($id)
    {
        $don = DonModel::getById($id);
        if (!$don) return ['success' => false, 'message' => 'Don non trouvé'];

        $db = $this->app->db();

        try {
            $stmt = $db->prepare("SELECT b.* FROM besoin b LEFT JOIN besoinSatisfait bs ON b.id = bs.idBesoin WHERE b.idProduit = ? AND bs.id IS NULL ORDER BY b.quantite ASC");
            $stmt->execute([$don->idProduit]);
            $besoins = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $quantiteRestante = (int)$don->quantite;
            $plan = [];

            foreach ($besoins as $besoin) {
                if ($quantiteRestante <= 0) break;

                $stmtAttr = $db->prepare("SELECT COALESCE(SUM(quantite),0) as total FROM attribution WHERE idBesoin = ?");
                $stmtAttr->execute([$besoin['id']]);
                $dejaAttribue = (int)$stmtAttr->fetch(\PDO::FETCH_ASSOC)['total'];

                $reste = (int)$besoin['quantite'] - $dejaAttribue;
                if ($reste <= 0) continue;

                $attribuer = min($quantiteRestante, $reste);

                $plan[] = [
                    'idBesoin' => $besoin['id'],
                    'ville' => $besoin['idVille'],
                    'quantite_demande' => (int)$besoin['quantite'],
                    'deja_attribue' => $dejaAttribue,
                    'attribuer' => $attribuer,
                    'restant_apres' => $reste - $attribuer
                ];

                $quantiteRestante -= $attribuer;
            }

            return [
                'success' => true,
                'don' => ['id' => $don->id, 'idProduit' => $don->idProduit, 'quantite' => $don->quantite],
                'method' => 'min',
                'plan' => $plan,
                'quantite_distribuee' => $don->quantite - $quantiteRestante,
                'quantite_restante' => $quantiteRestante,
            ];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erreur lors de la simulation min : ' . $e->getMessage()];
        }
    }
    
    public function distribuerDonMin($id){
        $don = DonModel::getById($id);
        if (!$don) return ['success' => false, 'message' => 'Don non trouvé'];
        
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
                ORDER BY b.quantite ASC
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
            
            $qDistrib = $don->quantite - $quantiteRestante;
            $msg = "Distribution effectuée. Quantité distribuée : $qDistrib. Quantité restante : $quantiteRestante.";
            return [
                'success' => true,
                'quantite_distribuee' => $qDistrib,
                'quantite_restante' => $quantiteRestante,
                'message' => $msg
            ];
            
        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            $db->rollBack();
            return [
                'success' => false,
                'message' => 'Erreur lors de la distribution : ' . $e->getMessage()
            ];
        }

    }
    public function distribuerDonProportionnel($id) {
        $don = DonModel::getById($id);
        if (!$don) return ['success' => false, 'message' => 'Don non trouvé'];

        $db = $this->app->db();

        try {
            $db->beginTransaction();

            // verrouiller les besoins concernés (évite race conditions)
            $lockStmt = $db->prepare(
                "SELECT b.id FROM besoin b\n"
                . "LEFT JOIN (SELECT idBesoin, SUM(quantite) as total_attribue FROM attribution GROUP BY idBesoin) a ON b.id = a.idBesoin\n"
                . "WHERE b.idProduit = ? AND (b.quantite - COALESCE(a.total_attribue,0)) > 0 FOR UPDATE"
            );
            $lockStmt->execute([$don->idProduit]);

            // 1) besoins restants par ville
            $stmt = $db->prepare(
                "SELECT v.id as idVille, v.nom as ville, SUM(b.quantite - COALESCE(a.total_attribue,0)) as restant\n"
                . "FROM besoin b\n"
                . "INNER JOIN ville v ON b.idVille = v.id\n"
                . "LEFT JOIN (SELECT idBesoin, SUM(quantite) as total_attribue FROM attribution GROUP BY idBesoin) a ON b.id = a.idBesoin\n"
                . "WHERE b.idProduit = ?\n"
                . "GROUP BY v.id, v.nom\n"
                . "HAVING restant > 0"
            );
            $stmt->execute([$don->idProduit]);
            $villes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $quantiteRestante = (int)$don->quantite;

            if (empty($villes)) {
                // rien à distribuer — éviter duplicate DonDistribue
                $chk = $db->prepare("SELECT id FROM donDistribue WHERE idDon = ? LIMIT 1");
                $chk->execute([$don->id]);
                $exists = $chk->fetchColumn();
                if (!$exists) {
                    $donDistribue = new DonDistribueModel();
                    $donDistribue->idDon = $don->id;
                    $donDistribue->dateDistribution = date('Y-m-d');
                    $donDistribue->save();
                }

                $db->commit();
                return [
                    'success' => true,
                    'quantite_distribuee' => 0,
                    'quantite_restante' => $quantiteRestante,
                    'par_ville' => [] ,
                    'message' => 'Aucun besoin non satisfait pour ce produit.'
                ];
            }

            // 2) calcul proportionnel initial
            $totalRestant = 0.0;
            foreach ($villes as $r) $totalRestant += (float)$r['restant'];

            $allocations = [];
            $allocatedSum = 0;
            foreach ($villes as $r) {
                $restant = (int)$r['restant'];
                $fraction = $totalRestant > 0 ? ((float)$restant / $totalRestant) : 0.0;
                $floatAlloc = $don->quantite * $fraction;
                $floorAlloc = (int)floor($floatAlloc);
                $allocations[] = [
                    'idVille' => (int)$r['idVille'],
                    'ville' => $r['ville'],
                    'restant' => $restant,
                    'floatAlloc' => $floatAlloc,
                    'floorAlloc' => $floorAlloc,
                    'frac' => $floatAlloc - $floorAlloc,
                    'alloc' => $floorAlloc,
                ];
                $allocatedSum += $floorAlloc;
            }

            // distribuer le reste selon les plus grands restes
            $remainder = $don->quantite - $allocatedSum;
            if ($remainder > 0) {
                usort($allocations, function($a, $b) {
                    if ($a['frac'] == $b['frac']) return $a['idVille'] <=> $b['idVille'];
                    return ($a['frac'] > $b['frac']) ? -1 : 1;
                });
                $i = 0;
                while ($remainder > 0) {
                    $allocations[$i]['alloc'] += 1;
                    $remainder -= 1;
                    $i++;
                    if ($i >= count($allocations)) $i = 0;
                }
                usort($allocations, function($a, $b){ return $a['idVille'] <=> $b['idVille']; });
            }

            // 3) caper les allocations au restant de chaque ville, récupérer pool si excédent
            $sumAlloc = 0;
            foreach ($allocations as &$a) {
                if ($a['alloc'] > $a['restant']) {
                    $a['alloc'] = $a['restant'];
                }
                $sumAlloc += $a['alloc'];
            }
            unset($a);

            $pool = $don->quantite - $sumAlloc; // unités à redistribuer après cap

            // Redistribuer le pool en attribuant +1 aux villes ayant le plus de besoin restant non satisfait (restant - alloc)
            while ($pool > 0) {
                // lister candidates
                $candidates = [];
                foreach ($allocations as $idx => $a) {
                    $needLeft = $a['restant'] - $a['alloc'];
                    if ($needLeft > 0) $candidates[] = ['idx' => $idx, 'needLeft' => $needLeft, 'idVille' => $a['idVille']];
                }
                if (empty($candidates)) break;

                usort($candidates, function($x, $y) {
                    if ($x['needLeft'] == $y['needLeft']) return $x['idVille'] <=> $y['idVille'];
                    return ($x['needLeft'] > $y['needLeft']) ? -1 : 1;
                });

                $give = min($pool, count($candidates));
                for ($i = 0; $i < $give; $i++) {
                    $allocations[$candidates[$i]['idx']]['alloc'] += 1;
                    $pool -= 1;
                }
            }

            // 4) distribuer par ville aux besoins de la ville (ordre id asc)
            $perVille = [];
            foreach ($allocations as $a) {
                $qtyForVille = (int)$a['alloc'];
                $distributedInVille = 0;
                $details = [];

                if ($qtyForVille > 0) {
                    // verrouiller les besoins de la ville pendant l'allocation
                    $stmtB = $db->prepare("SELECT b.* FROM besoin b LEFT JOIN (SELECT idBesoin, SUM(quantite) as total_attribue FROM attribution GROUP BY idBesoin) a ON b.id = a.idBesoin WHERE b.idProduit = ? AND b.idVille = ? AND (b.quantite - COALESCE(a.total_attribue,0)) > 0 ORDER BY b.id ASC FOR UPDATE");
                    $stmtB->execute([$don->idProduit, $a['idVille']]);
                    $besoinsVille = $stmtB->fetchAll(\PDO::FETCH_ASSOC);

                    foreach ($besoinsVille as $bData) {
                        if ($qtyForVille <= 0) break;
                        $besoin = new BesoinModel($bData);

                        $stmtAttr = $db->prepare("SELECT COALESCE(SUM(quantite),0) as total FROM attribution WHERE idBesoin = ?");
                        $stmtAttr->execute([$besoin->id]);
                        $deja = (int)$stmtAttr->fetch(\PDO::FETCH_ASSOC)['total'];
                        $reste = $besoin->quantite - $deja;
                        if ($reste <= 0) continue;

                        $toGive = min($qtyForVille, $reste);

                        $attribution = new AttributionModel();
                        $attribution->idBesoin = $besoin->id;
                        $attribution->idDon = $don->id;
                        $attribution->quantite = $toGive;
                        $attribution->save();

                        $qtyForVille -= $toGive;
                        $distributedInVille += $toGive;
                        $quantiteRestante -= $toGive;
                        $details[] = ['idBesoin' => $besoin->id, 'alloue' => $toGive];

                        if ($deja + $toGive >= $besoin->quantite) {
                            $besoinS = new BesoinSatisfaitModel();
                            $besoinS->idBesoin = $besoin->id;
                            $besoinS->dateSatisfaction = date('Y-m-d');
                            $besoinS->save();
                        }
                    }
                }

                $perVille[] = [
                    'idVille' => $a['idVille'],
                    'ville' => $a['ville'],
                    'alloue_initial' => $a['alloc'],
                    'distribue' => $distributedInVille,
                    'details' => $details
                ];
            }

            // 5) enregistrer DonDistribue (si non déjà présent)
            $chk = $db->prepare("SELECT id FROM donDistribue WHERE idDon = ? LIMIT 1");
            $chk->execute([$don->id]);
            $exists = $chk->fetchColumn();
            if (!$exists) {
                $donDistribue = new DonDistribueModel();
                $donDistribue->idDon = $don->id;
                $donDistribue->dateDistribution = date('Y-m-d');
                $donDistribue->save();
            }

            $db->commit();

            $qDistrib = $don->quantite - $quantiteRestante;
            return [
                'success' => true,
                'quantite_distribuee' => $qDistrib,
                'quantite_restante' => $quantiteRestante,
                'par_ville' => $perVille,
                'message' => "Distribution proportionnelle effectuée."
            ];

        } catch (\Exception $e) {
            $db->rollBack();
            return ['success' => false, 'message' => 'Erreur lors de la distribution proportionnelle : ' . $e->getMessage()];
        }
    }
}
