<div>
    <h1>Formulaire d'insertion de dons</h1>

    <form action="/don" method="post">
        <label for="produit">Le produit à donner</label>
        <select name="idProduit" id="produit" required>
            <option value="">Produits</option>
            <?php if (!empty($produits)) { ?>
                <?php foreach ($produits as $pd) { ?>
                    <option value="<?= htmlspecialchars($pd->id) ?>">
                        <?= htmlspecialchars($pd->description) ?>
                    </option>
                <?php } ?>
            <?php } ?>

        </select>

        <label for="quantite">La quantité à donner</label>
        <input type="number" name="quantite" id="quantite" min="1" required>

        <label for="dateDon">La date du don</label>
        <input type="date" name="dateDon" id="dateDon" required>

        <br><br>
        <input type="submit" value="Donner">
    </form>
</div>