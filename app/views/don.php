<?php
// Variables attendues : $dons (array de DonModel avec ->quantiteRestante)
?>
<div>
  <h1 style="margin-bottom:4px;">Dons disponibles</h1>
  <p style="color:#6b7280;margin-bottom:20px;">Simulez ou validez la distribution de chaque don.</p>

  <?php if (isset($_GET['success'])): ?>
    <div style="padding:10px;background:#d1fae5;color:#065f46;border:1px solid #6ee7b7;border-radius:6px;margin-bottom:16px;">
      ✓ <?= htmlspecialchars($_GET['success']) ?>
    </div>
  <?php endif; ?>

  <?php if (isset($_GET['error'])): ?>
    <div style="padding:10px;background:#fee2e2;color:#b91c1c;border:1px solid #fca5a5;border-radius:6px;margin-bottom:16px;">
      ✗ <?= htmlspecialchars($_GET['error']) ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($dons)): ?>
  <div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.07);">
      <thead>
        <tr style="background:#f8fafc;text-align:left;border-bottom:2px solid #e2e8f0;">
          <th style="padding:12px 16px;font-size:13px;color:#64748b;font-weight:600;">ID</th>
          <th style="padding:12px 16px;font-size:13px;color:#64748b;font-weight:600;">Produit</th>
          <th style="padding:12px 16px;font-size:13px;color:#64748b;font-weight:600;text-align:center;">Qté initiale</th>
          <th style="padding:12px 16px;font-size:13px;color:#64748b;font-weight:600;text-align:center;">Qté restante</th>
          <th style="padding:12px 16px;font-size:13px;color:#64748b;font-weight:600;">Date don</th>
          <th style="padding:12px 16px;font-size:13px;color:#64748b;font-weight:600;">Date saisie</th>
          <th style="padding:12px 16px;font-size:13px;color:#64748b;font-weight:600;text-align:center;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($dons as $d):
          $restant  = $d->quantiteRestante ?? $d->quantite;
          $prodObj  = method_exists($d, 'getProduit') ? $d->getProduit() : null;
          $prodName = $prodObj->description ?? ($d->idProduit ?? '');
          $color    = $restant <= 0 ? '#10b981' : ($restant < $d->quantite ? '#f59e0b' : '#3b82f6');
        ?>
        <tr style="border-bottom:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
          <td style="padding:12px 16px;font-family:monospace;font-size:13px;color:#94a3b8;">#<?= htmlspecialchars($d->id ?? '') ?></td>
          <td style="padding:12px 16px;font-weight:500;"><?= htmlspecialchars($prodName) ?></td>
          <td style="padding:12px 16px;text-align:center;"><?= htmlspecialchars($d->quantite ?? '') ?></td>
          <td style="padding:12px 16px;text-align:center;">
            <span style="font-weight:600;color:<?= $color ?>;">
              <?= $restant <= 0 ? '✓ Soldé' : htmlspecialchars($restant) ?>
            </span>
          </td>
          <td style="padding:12px 16px;color:#64748b;font-size:13px;"><?= htmlspecialchars($d->dateDon ?? '') ?></td>
          <td style="padding:12px 16px;color:#64748b;font-size:13px;"><?= htmlspecialchars($d->dateSaisie ?? '') ?></td>
          <td style="padding:12px 16px;text-align:center;">
            <?php if ($restant > 0): ?>
              <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">

                <!-- Simuler : lien simple vers la page de simulation -->
                <a href="<?= BASE_URL ?>/don/<?= (int)$d->id ?>/simuler"
                   style="padding:6px 14px;background:#6366f1;color:white;border-radius:6px;text-decoration:none;font-size:13px;font-weight:500;">
                  🔍 Simuler
                </a>

                <!-- Valider : POST direct -->
        <form method="POST" action="<?= BASE_URL ?>/api/dons/<?= (int)$d->id ?>/distribuer" style="display:inline;"
                      onsubmit="return confirm('Confirmer la distribution du don #<?= (int)$d->id ?> ?');">
                  <button type="submit"
                    style="padding:6px 14px;background:#10b981;color:white;border:none;border-radius:6px;cursor:pointer;font-size:13px;font-weight:500;">
                    ✓ Valider
                  </button>
                </form>

              </div>
            <?php else: ?>
              <span style="color:#10b981;font-size:13px;font-weight:600;">✓ Distribué</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
    <p style="color:#6b7280;">Aucun don disponible.</p>
  <?php endif; ?>

    <div style="margin-top:20px;">
    <a href="<?= BASE_URL ?>/produit" style="padding:8px 16px;background:#2563eb;color:white;border-radius:6px;text-decoration:none;font-size:14px;">
      + Insérer un don
    </a>
  </div>
</div>