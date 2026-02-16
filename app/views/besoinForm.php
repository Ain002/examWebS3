<?php
$id = $besoin->id ?? '';
$idVille = $besoin->idVille ?? ($villeId ?? 0);
$idProduit = $besoin->idProduit ?? '';
$quantite = $besoin->quantite ?? '';
?>

<form id="besoinForm" method="post" action="/besoin/save">
	<input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
	<input type="hidden" name="idVille" value="<?= (int)$idVille ?>">

	<div style="margin-bottom:8px;">
		<label>Produit:
			<?php if (!empty($produits) && is_iterable($produits)): ?>
				<select name="idProduit" style="width:100%;padding:6px;">
					<?php foreach ($produits as $p): ?>
						<option value="<?= (int)$p->id ?>" <?= ((int)$p->id === (int)$idProduit) ? 'selected' : '' ?>><?= htmlspecialchars($p->description ?? ('Produit #'.(int)$p->id)) ?></option>
					<?php endforeach; ?>
				</select>
			<?php else: ?>
				<input type="number" name="idProduit" value="<?= htmlspecialchars($idProduit) ?>" style="width:100%;padding:6px;">
			<?php endif; ?>
		</label>
	</div>

	<div style="margin-bottom:8px;">
		<label>Quantité <input type="number" name="quantite" value="<?= htmlspecialchars($quantite) ?>" min="0" style="width:100%;padding:6px;"></label>
	</div>

	<div style="display:flex;gap:8px;justify-content:flex-end">
		<button type="submit" style="padding:8px 12px;background:#2563eb;color:#fff;border:none;border-radius:6px">Enregistrer</button>
	</div>
</form>