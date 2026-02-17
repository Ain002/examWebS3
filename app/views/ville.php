<?php
?>
<div class="container">
    <h1>Villes</h1>
    <p class="text-muted">Gérez les villes associées aux régions.</p>

    <?php if (!empty($villes) && is_array($villes)): ?>
        <div class="ville-grid">
            <?php foreach ($villes as $v): ?>
                <div class="ville-card">
                    <div class="title"><?= htmlspecialchars($v->nom ?? ''); ?></div>
                    <div class="sub">— Région: <?= (int)($v->idRegion ?? 0); ?></div>
                    <div class="controls">
                        <a class="btn-sm btn-view" href="<?= BASE_URL ?>/besoin/<?= (int)($v->id ?? 0); ?>">Voir les besoins</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Aucune ville trouvée.</p>
    <?php endif; ?>
</div>
