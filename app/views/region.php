<?php
?>
<div>
    <h1>Région</h1>
    <p>Gérez les régions. Liste, création et statistiques par région.</p>
    <?php if (!empty($regions) && is_array($regions)): ?>
        <ul>
            <?php foreach ($regions as $r): ?>
                <li><?php echo htmlspecialchars($r->nom ?? ''); ?> (id: <?php echo (int)($r->id ?? 0); ?>)</li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucune région trouvée.</p>
    <?php endif; ?>
</div>