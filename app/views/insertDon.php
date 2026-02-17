<div class="container mt-5">
    <div class="card shadow-sm p-4">
        <h2 class="mb-4">Formulaire d'insertion de dons</h2>

    <form action="<?= BASE_URL ?>/don" method="post">

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

            <div class="mb-3">
                <label for="quantite" class="form-label">Quantité</label>
                <input type="number" name="quantite" id="quantite" 
                       class="form-control" min="1" required>
            </div>

            <div class="mb-3">
                <label for="dateDon" class="form-label">Date du don</label>
                <input type="date" name="dateDon" id="dateDon" 
                       class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">
                Enregistrer
            </button>
        </form>
    </div>
</div>
