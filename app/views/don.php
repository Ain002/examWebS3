<?php
?>
<div>
    <h1>Don</h1>
    <p>Historique des dons et formulaire de saisie.</p>
    <?php if (!empty($dons) && is_array($dons)): ?>
        <ul>
            <?php foreach ($dons as $d): ?>
                <li>Produit: <?php echo (int)($d->idProduit ?? 0); ?> — Quantité: <?php echo htmlspecialchars($d->quantite ?? ''); ?> — Date: <?php echo htmlspecialchars($d->dateDon ?? ''); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun don enregistré.</p>
    <?php endif; ?>
</div>