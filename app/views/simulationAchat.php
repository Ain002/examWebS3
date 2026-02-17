<div class="container mt-5">
    <div class="card shadow-sm p-4">

        <h2 class="mb-4">Simulation d'achat</h2>

        <div class="mb-3">
            <p><strong>Produit :</strong> <?= htmlspecialchars($produit->description) ?></p>
            <p><strong>Quantité :</strong> <?= $besoin->quantite ?></p>
            <p><strong>Prix unitaire :</strong> <?= $produit->pu ?></p>
        </div>

        <hr>

        <div class="mb-3">
            <p>Montant de base : 
                <strong><?= $montantBase ?></strong>
            </p>

            <p>Frais (<?= $taxe ?>%) : appliqués</p>

            <p>Montant total : 
                <strong class="text-primary"><?= $montantTotal ?></strong>
            </p>
        </div>

        <hr>

        <div class="mb-3">
            <p>Argent disponible : <?= $argentDisponible ?></p>

            <p>Reste après achat :
                <strong class="<?= $reste < 0 ? 'text-danger' : 'text-success' ?>">
                    <?= $reste ?>
                </strong>
            </p>
        </div>

        <?php if($achatAutorise): ?>
            <form method="post" action="/besoin/acheter/<?= $besoin->id ?>">
                <button class="btn btn-success">
                    Valider l'achat
                </button>
            </form>
        <?php else: ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($messageErreur) ?>
            </div>
        <?php endif; ?>

        <a href="/besoin/restant" class="btn btn-secondary mt-3">
            ← Retour
        </a>

    </div>
</div>
