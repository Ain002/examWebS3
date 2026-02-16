<?php
?>
<div>
    <h1>Ville</h1>
    <p>Gérez les villes associées aux régions. Recherche et filtres disponibles.</p>
    <?php if (!empty($villes) && is_array($villes)): ?>
        <ul>
            <?php foreach ($villes as $v): ?>
                <li><?php echo htmlspecialchars($v->nom ?? ''); ?> (id: <?php echo (int)($v->id ?? 0); ?>, région: <?php echo (int)($v->idRegion ?? 0); ?>)</li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucune ville trouvée.</p>
    <?php endif; ?>
</div>