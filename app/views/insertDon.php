<div class="container mt-5">
    <div class="card shadow-sm p-4">
        <h2 class="mb-4">Formulaire d'insertion de dons</h2>

    <form action="/don" method="post">
        <label for="produit">Produit</label>
        <select name="idProduit" id="produit" required>
            <option value="">Produits</option>
            <?php if (!empty($produits)) { ?>
                <?php foreach ($produits as $pd) { ?>
                    <option value="<?= htmlspecialchars($pd->id) ?>">
                        <?= htmlspecialchars($pd->description) ?>
                    </option>
                <?php } ?>
            <?php } ?>

            <div class="mb-3">
                <label for="produit" class="form-label">Produit</label>
                <select name="idProduit" id="produit" class="form-select" required>
                    <option value="">Choisir un produit</option>
                    <?php if (!empty($produits)) { ?>
                        <?php foreach ($produits as $pd) { ?>
                            <option value="<?= htmlspecialchars($pd->id) ?>">
                                <?= htmlspecialchars($pd->description) ?>
                            </option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>

        <label for="quantite">Quantité</label>
        <input type="number" name="quantite" id="quantite" min="1" required>

            <div class="mb-3">
                <label for="dateDon" class="form-label">Date du don</label>
                <input type="date" name="dateDon" id="dateDon" 
                       class="form-control" required>
            </div>

        <br><br>
        <input type="submit" value="Enregistrer">
    </form>
</div>
