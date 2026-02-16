<?php
?>
<div>
    <h1>Besoins restants</h1>

    <form method="get" action="/besoin/restant" style="margin-bottom:12px;">
        <label>Filtrer par ville: </label>
        <select name="idVille" onchange="this.form.submit()">
            <option value="">Toutes</option>
            <?php foreach($villes as $ville): ?>
                <?php $selected = (isset($_GET['idVille']) && $_GET['idVille'] == $ville->id) ? 'selected' : ''; ?>
                <option value="<?= $ville->id ?>" <?= $selected ?>><?= htmlspecialchars($ville->nom) ?></option>
            <?php endforeach; ?>
        </select>
    </form>

    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="background:#f3f4f6;text-align:left;">
                <th style="padding:8px;border:1px solid #e5e7eb;">Ville</th>
                <th style="padding:8px;border:1px solid #e5e7eb;">Produit</th>
                <th style="padding:8px;border:1px solid #e5e7eb;">PU</th>
                <th style="padding:8px;border:1px solid #e5e7eb;">Demandé</th>
                <th style="padding:8px;border:1px solid #e5e7eb;">Attribué</th>
                <th style="padding:8px;border:1px solid #e5e7eb;">Restant</th>
                <th style="padding:8px;border:1px solid #e5e7eb;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($besoins)): ?>
                <tr><td colspan="7" style="padding:12px;border:1px solid #e5e7eb;">Aucun besoin restant.</td></tr>
            <?php else: ?>
                <?php foreach($besoins as $b): ?>
                    <?php if(isset($_GET['idVille']) && $_GET['idVille'] !== '' && $_GET['idVille'] != $b['idVille']) continue; ?>
                    <tr>
                        <td style="padding:8px;border:1px solid #e5e7eb;"><?= htmlspecialchars($b['ville']) ?></td>
                        <td style="padding:8px;border:1px solid #e5e7eb;"><?= htmlspecialchars($b['produit']) ?></td>
                        <td style="padding:8px;border:1px solid #e5e7eb;"><?= htmlspecialchars($b['pu']) ?></td>
                        <td style="padding:8px;border:1px solid #e5e7eb; text-align:center;"><?= htmlspecialchars($b['quantite_demande']) ?></td>
                        <td style="padding:8px;border:1px solid #e5e7eb; text-align:center;"><?= htmlspecialchars($b['quantite_attribue']) ?></td>
                        <td style="padding:8px;border:1px solid #e5e7eb; text-align:center;"><?= htmlspecialchars($b['quantite_restante']) ?></td>
                        <td style="padding:8px;border:1px solid #e5e7eb;">
                            <a href="/besoin/restant?action=acheter&ville=<?= $b['idVille'] ?>&produit=<?= $b['idProduit'] ?>&besoin=<?= $b['id'] ?>" style="margin-left:8px;padding:6px 10px;background:#10b981;color:#fff;border-radius:6px;text-decoration:none;">Acheter</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
