<?php
// Variables attendues :
// $don     (DonModel)
// $plan    (array)
// $quantite_distribuee (int)
// $quantite_restante   (int)
?>
<div>

  <div style="margin-bottom:20px;display:flex;align-items:center;gap:12px;">
    <a href="/don"
       style="padding:7px 14px;background:#e2e8f0;color:#0f172a;border-radius:6px;text-decoration:none;font-size:13px;">
      ← Retour aux dons
    </a>
    <h1 style="margin:0;">Simulation — Don #<?= (int)($don->id ?? '') ?></h1>
  </div>

  <!-- Sélecteur de méthode (simple, sans JS) -->
  <form method="get" action="/don/<?= (int)($don->id ?? '') ?>/simuler" style="margin-bottom:18px;display:flex;gap:10px;align-items:center;">
    <label for="method" style="font-size:13px;color:#374151;">Méthode :</label>
    <select id="method" name="method" style="padding:6px 10px;border-radius:6px;border:1px solid #e5e7eb;">
      <option value="fifo" <?= (isset($method) && $method === 'fifo') ? 'selected' : (!isset($method) ? 'selected' : '') ?>>FIFO (par défaut)</option>
      <option value="min" <?= (isset($method) && $method === 'min') ? 'selected' : '' ?>>Min (petits besoins d'abord)</option>
      <option value="proportionnel" <?= (isset($method) && in_array($method, ['proportionnel','prorata','pro rata'])) ? 'selected' : '' ?>>Proportionnel</option>
    </select>
    <button type="submit" style="padding:7px 12px;background:#3b82f6;color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:600;">Simuler</button>
  </form>

  <!-- Résumé du don -->
  <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:24px;">
    <div style="background:white;border-radius:8px;padding:14px 20px;border-left:3px solid #6366f1;box-shadow:0 1px 4px rgba(0,0,0,0.07);">
      <span style="display:block;font-size:11px;color:#6b7280;margin-bottom:4px;">Produit</span>
      <strong style="font-size:18px;"><?= htmlspecialchars($don->idProduit ?? '') ?></strong>
    </div>
    <div style="background:white;border-radius:8px;padding:14px 20px;border-left:3px solid #3b82f6;box-shadow:0 1px 4px rgba(0,0,0,0.07);">
      <span style="display:block;font-size:11px;color:#6b7280;margin-bottom:4px;">Quantité initiale</span>
      <strong style="font-size:18px;"><?= (int)($don->quantite ?? 0) ?></strong>
    </div>
    <div style="background:white;border-radius:8px;padding:14px 20px;border-left:3px solid #10b981;box-shadow:0 1px 4px rgba(0,0,0,0.07);">
      <span style="display:block;font-size:11px;color:#6b7280;margin-bottom:4px;">Sera distribué</span>
      <strong style="font-size:18px;color:#10b981;"><?= (int)$quantite_distribuee ?></strong>
    </div>
    <div style="background:white;border-radius:8px;padding:14px 20px;border-left:3px solid <?= $quantite_restante > 0 ? '#f59e0b' : '#10b981' ?>;box-shadow:0 1px 4px rgba(0,0,0,0.07);">
      <span style="display:block;font-size:11px;color:#6b7280;margin-bottom:4px;">Restera après</span>
      <strong style="font-size:18px;color:<?= $quantite_restante > 0 ? '#f59e0b' : '#10b981' ?>;"><?= (int)$quantite_restante ?></strong>
    </div>
  </div>

  <!-- Tableau du plan de distribution -->
  <?php if (empty($plan)): ?>
    <div style="padding:16px;background:#fffbeb;border:1px solid #fef3c7;border-radius:8px;margin-bottom:24px;color:#92400e;">
      Aucune attribution possible — aucun besoin non satisfait pour ce produit.
    </div>
  <?php else: ?>
    <div style="overflow-x:auto;margin-bottom:24px;">
      <table style="width:100%;border-collapse:collapse;background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.07);">
        <thead>
          <tr style="background:#e0e7ff;text-align:left;">
            <th style="padding:10px 16px;font-size:13px;color:#3730a3;">Besoin #</th>
            <th style="padding:10px 16px;font-size:13px;color:#3730a3;">Ville</th>
            <th style="padding:10px 16px;font-size:13px;color:#3730a3;text-align:center;">Demandé</th>
            <th style="padding:10px 16px;font-size:13px;color:#3730a3;text-align:center;">Déjà attribué</th>
            <th style="padding:10px 16px;font-size:13px;color:#3730a3;text-align:center;">Sera attribué</th>
            <th style="padding:10px 16px;font-size:13px;color:#3730a3;text-align:center;">Reste après</th>
            <th style="padding:10px 16px;font-size:13px;color:#3730a3;text-align:center;">État</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($plan as $p): ?>
          <tr style="border-bottom:1px solid #f1f5f9;">
            <td style="padding:10px 16px;font-family:monospace;color:#6366f1;font-weight:600;">#<?= (int)$p['idBesoin'] ?></td>
            <td style="padding:10px 16px;"><?= htmlspecialchars($p['ville'] ?? '—') ?></td>
            <td style="padding:10px 16px;text-align:center;"><?= (int)$p['quantite_demande'] ?></td>
            <td style="padding:10px 16px;text-align:center;"><?= (int)$p['deja_attribue'] ?></td>
            <td style="padding:10px 16px;text-align:center;font-weight:700;color:#6366f1;"><?= (int)$p['attribuer'] ?></td>
            <td style="padding:10px 16px;text-align:center;"><?= (int)$p['restant_apres'] ?></td>
            <td style="padding:10px 16px;text-align:center;">
              <?php if ($p['restant_apres'] === 0): ?>
                <span style="display:inline-block;padding:2px 10px;background:#d1fae5;color:#065f46;border-radius:999px;font-size:12px;font-weight:600;">✓ Satisfait</span>
              <?php else: ?>
                <span style="display:inline-block;padding:2px 10px;background:#fef3c7;color:#92400e;border-radius:999px;font-size:12px;font-weight:600;">Partiel</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <!-- Actions -->
  <div style="display:flex;gap:12px;align-items:center;">
    <?php if (!empty($plan)): ?>
    <form method="POST" action="/api/dons/<?= (int)($don->id ?? '') ?>/distribuer"
          onsubmit="return confirm('Confirmer la distribution réelle du don #<?= (int)($don->id ?? '') ?> ?');">
      <button type="submit"
        style="padding:10px 22px;background:#10b981;color:white;border:none;border-radius:8px;cursor:pointer;font-size:14px;font-weight:600;">
        ✓ Valider la distribution
      </button>
    </form>
    <?php endif; ?>

    <a href="/don"
       style="padding:10px 18px;background:#e2e8f0;color:#0f172a;border-radius:8px;text-decoration:none;font-size:14px;">
      Annuler
    </a>
  </div>

</div>