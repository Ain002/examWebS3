<?php ?><div>
    <h2>Simulation d'achat</h2>

    <p><strong>Produit :</strong> <?= htmlspecialchars($produit->description) ?></p>
    <p><strong>Quantité :</strong> <?= $besoin->quantite ?></p>
    <p><strong>Prix unitaire :</strong> <?= $produit->pu ?></p>

    <hr>

    <p>Montant de base : <strong><?= $montantBase ?></strong></p>
    <p>Frais (<?= $taxe ?>%) : appliqués</p>
    <p>Montant total : <strong><?= $montantTotal ?></strong></p>

    <hr>

    <p>Argent disponible : <?= $argentDisponible ?></p>
    <p>Reste après achat : 
        <strong style="color:<?= $reste < 0 ? 'red' : 'green' ?>">
            <?= $reste ?>
        </strong>
    </p>

    <?php if($achatAutorise): ?>
        <form method="post" action="/besoin/acheter/<?= $besoin->id ?>">
            <button style="padding:8px 15px;background:#10b981;color:white;border:none;border-radius:6px;">
                Valider l'achat
            </button>
        </form>
    <?php else: ?>
        <p style="color:red;">Fonds insuffisants</p>
    <?php endif; ?>
    <?php if(!$achatAutorise): ?>
    <p style="color:red; font-weight:bold;">
        <?= htmlspecialchars($messageErreur) ?>
    </p>
<?php endif; ?>

    <br>
    <a href="/besoin/restant">← Retour</a>
</div>

