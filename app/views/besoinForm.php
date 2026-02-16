<?php
// Variables attendues :
// $besoin (BesoinModel ou null)
// $villeId
// $types (liste TypeBesoinModel)
// $produits (liste ProduitModel)

$id = $besoin->id ?? '';
$idType = $besoin->idType ?? '';
$idProduit = $besoin->idProduit ?? '';
$quantite = $besoin->quantite ?? '';
?>

<div>
    <h1><?= $id ? 'Modifier le besoin' : 'Ajouter un besoin' ?></h1>

    <form method="post" action="<?= BASE_URL ?>/besoin/save">

        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
        <input type="hidden" name="idVille" value="<?= (int)$villeId ?>">

        <!-- TYPE -->
        <div style="margin-bottom:12px;">
            <label>Type :</label>
            <select name="idType" required style="width:100%;padding:8px;">
                <?php foreach ($types as $t): ?>
                    <option value="<?= (int)$t->id ?>"
                        <?= ((int)$t->id === (int)$idType) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t->description) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- PRODUIT -->
        <div style="margin-bottom:12px;">
            <label>Produit :</label>
            <select name="idProduit" required style="width:100%;padding:8px;">
                <?php foreach ($produits as $p): ?>
                    <option value="<?= (int)$p->id ?>"
                        <?= ((int)$p->id === (int)$idProduit) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p->description) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- QUANTITE -->
        <div style="margin-bottom:12px;">
            <label>Quantité :</label>
            <input type="number"
                   name="quantite"
                   value="<?= htmlspecialchars($quantite) ?>"
                   min="0"
                   required
                   style="width:100%;padding:8px;">
        </div>

        <div style="display:flex;gap:10px;">
            <button type="submit"
                    style="padding:8px 12px;background:#2563eb;color:white;border:none;border-radius:6px;">
                Enregistrer
            </button>

            <a href="<?= BASE_URL ?>/besoin/<?= (int)$villeId ?>"
               style="padding:8px 12px;background:#6b7280;color:white;border-radius:6px;text-decoration:none;">
                Annuler
            </a>
        </div>

    </form>
</div>
