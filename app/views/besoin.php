<?php
use app\models\BesoinModel;
?>

<div>
    <h1>Liste des besoins</h1>

    <div style="margin:12px 0;">
        <a href="<?= BASE_URL ?>/besoin/form/<?= (int)$villeId ?>"
           style="padding:8px 12px;border-radius:6px;background:#2563eb;color:#fff;text-decoration:none;">
            Ajouter un besoin
        </a>
    </div>

    <?php if (!empty($besoins)): ?>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f3f4f6;">
                    <th style="padding:8px;border:1px solid #e5e7eb;">Type</th>
                    <th style="padding:8px;border:1px solid #e5e7eb;">Produit</th>
                    <th style="padding:8px;border:1px solid #e5e7eb;">Quantité</th>
                    <th style="padding:8px;border:1px solid #e5e7eb;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($besoins as $b): ?>
                    <tr>
                        <td style="padding:8px;border:1px solid #e5e7eb;">
                            <?= htmlspecialchars($b->getType()?->libelle ?? '') ?>
                        </td>

                        <td style="padding:8px;border:1px solid #e5e7eb;">
                            <?= htmlspecialchars($b->getProduit()?->description ?? '') ?>
                        </td>

                        <td style="padding:8px;border:1px solid #e5e7eb;text-align:center;">
                            <?= (int)$b->quantite ?>
                        </td>

                        <td style="padding:8px;border:1px solid #e5e7eb;">

                            <!-- Modifier -->
                            <a href="<?= BASE_URL ?>/besoin/form/<?= (int)$villeId ?>/<?= (int)$b->id ?>"
                               style="margin-right:6px;padding:6px 10px;background:#f59e0b;color:white;border-radius:6px;text-decoration:none;">
                                Modifier
                            </a>

                            <!-- Supprimer -->
                            <form method="post"
                                  action="<?= BASE_URL ?>/besoin/delete/<?= (int)$b->id ?>"
                                  style="display:inline;"
                                  onsubmit="return confirm('Supprimer ce besoin ?');">
                                <input type="hidden" name="idVille" value="<?= (int)$villeId ?>">
                                <button type="submit"
                                        style="padding:6px 10px;background:#ef4444;color:white;border:none;border-radius:6px;">
                                    Supprimer
                                </button>
                            </form>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun besoin trouvé.</p>
    <?php endif; ?>

    <div style="margin-top:20px;">
        <a href="<?= BASE_URL ?>/ville" style="color:#2563eb;text-decoration:none;">
            ← Retour aux villes
        </a>
    </div>
</div>

