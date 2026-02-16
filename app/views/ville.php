<?php
?>
<div>
    <h1>Ville</h1>
    <p>Gérez les villes associées aux régions. Recherche et filtres disponibles.</p>
    <?php if (!empty($villes) && is_array($villes)): ?>
        <ul>
            <?php foreach ($villes as $v): ?>
                <li>
                    <?= htmlspecialchars($v->nom ?? ''); ?> (id: <?= (int)($v->id ?? 0); ?>, région: <?= (int)($v->idRegion ?? 0); ?>)
                    <a href="<?= BASE_URL ?>/besoin/<?= (int)($v->id ?? 0); ?>">Voir les besoins</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucune ville trouvée.</p>
    <?php endif; ?>
</div>