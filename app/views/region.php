<?php
?>
<div class="container">
    <h1>Régions</h1>
    <p class="text-muted">Gérez les régions.</p>

    <?php if (!empty($regions) && is_array($regions)): ?>
        <div class="region-list">
            <?php foreach ($regions as $r): ?>
                <div class="region-item">
                    <div>
                        <h3><?= htmlspecialchars($r->nom ?? '') ?></h3>
                    </div>
                    <div class="actions">
                        <a class="btn-sm btn-view" href="<?= BASE_URL ?>/ville">Voir les villes</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Aucune région trouvée.</p>
    <?php endif; ?>
</div>
