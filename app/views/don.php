<?php
?>
<div>
    <h1>Dons Disponibles</h1>
    <p>Liste des dons à distribuer.</p>
    
    <?php if (isset($_GET['message'])): ?>
        <div style="padding:10px;background-color:#d4edda;color:#155724;border:1px solid #c3e6cb;border-radius:4px;margin:10px 0;">
            <?php echo htmlspecialchars($_GET['message']); ?>
            <?php if (isset($_GET['distribue'])): ?>
                - Quantité distribuée: <?php echo htmlspecialchars($_GET['distribue']); ?>
            <?php endif; ?>
            <?php if (isset($_GET['restant'])): ?>
                - Quantité restante: <?php echo htmlspecialchars($_GET['restant']); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div style="padding:10px;background-color:#f8d7da;color:#721c24;border:1px solid #f5c6cb;border-radius:4px;margin:10px 0;">
            Erreur: <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($dons) && is_array($dons)): ?>
        <table border="1" cellpadding="10">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Produit</th>
                    <th>Quantité Initiale</th>
                    <th>Quantité Restante</th>
                    <th>Date Don</th>
                    <th>Date Saisie</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dons as $d): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($d->id ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($d->idProduit ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($d->quantite ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($d->quantiteRestante ?? $d->quantite); ?></td>
                        <td><?php echo htmlspecialchars($d->dateDon ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($d->dateSaisie ?? ''); ?></td>
                        <td>
                            <?php if (isset($d->quantiteRestante) && $d->quantiteRestante > 0): ?>
                                <form method="POST" action="/api/dons/<?php echo $d->id; ?>/distribuer" style="display:inline;">
                                    <button type="submit" class="btn-distribuer" onclick="return confirm('Voulez-vous vraiment distribuer ce don ?');">
                                        Distribuer
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="color:#28a745;font-weight:bold;">✓ Complètement distribué</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun don disponible.</p>
    <?php endif; ?>

    <a href="/produit">Insérer un don</a>
</div>
  <?php include __DIR__ . '/footer.php'; ?>

<style>
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th {
    background-color: #f2f2f2;
    padding: 12px;
    text-align: left;
}

td {
    padding: 10px;
}

.btn-distribuer {
    background-color: #4CAF50;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-distribuer:hover {
    background-color: #45a049;
}
</style>