<?php
?>
<div>
    <h1>Tableau de bord</h1>

    <p>Régions: <?php echo isset($regions_count) ? (int)$regions_count : '—'; ?> —
       Villes: <?php echo isset($villes_count) ? (int)$villes_count : '—'; ?> —
       Dons: <?php echo isset($dons_count) ? (int)$dons_count : '—'; ?></p>

    <h2>Villes et besoins</h2>

    <?php if (!empty($cities) && is_array($cities)): ?>
        <table style="width:100%;border-collapse:collapse;margin-top:12px;">
            <thead>
                <tr style="background:#f3f4f6;text-align:left;">
                    <th style="padding:8px;border:1px solid #e5e7eb;">Ville</th>
                    <th style="padding:8px;border:1px solid #e5e7eb;">Produit (besoin)</th>
                    <th style="padding:8px;border:1px solid #e5e7eb;">Quantité demandée</th>
                    <th style="padding:8px;border:1px solid #e5e7eb;">Dons attribués</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($cities as $entry):
                $ville = $entry['ville'];
                $besoins = $entry['besoins'];

                if (empty($besoins)) {
            ?>
                <tr>
                    <td style="padding:8px;border:1px solid #e5e7eb;"><?php echo htmlspecialchars($ville->nom ?? 'Ville #'.($ville->id ?? '')); ?></td>
                    <td style="padding:8px;border:1px solid #e5e7eb;" colspan="3"><em>Aucun besoin enregistré pour cette ville.</em></td>
                </tr>
            <?php
                } else {
                    foreach ($besoins as $b) {
                        $besoin = $b['besoin'];
                        $produit = $b['produit'];
                        $atts = $b['attributions'];

                        $attsText = '';
                        if (!empty($atts)) {
                            $parts = [];
                            foreach ($atts as $att) {
                                $don = $att['don'];
                                $attrib = $att['attribution'];
                                $parts[] = sprintf('Don #%s (%d) — %s', $don ? (int)$don->id : 'n/a', (int)($attrib->quantite ?? 0), $don ? htmlspecialchars($don->dateSaisie) : '-');
                            }
                            $attsText = implode('<br>', $parts);
                        } else {
                            $attsText = '<em>Aucun don attribué</em>';
                        }
            ?>
                <tr>
                    <td style="padding:8px;border:1px solid #e5e7eb;vertical-align:top"><?php echo htmlspecialchars($ville->nom ?? 'Ville #'.($ville->id ?? '')); ?></td>
                    <td style="padding:8px;border:1px solid #e5e7eb;vertical-align:top"><?php echo $produit ? htmlspecialchars($produit->description) : 'Produit #' . ($besoin->idProduit ?? ''); ?></td>
                    <td style="padding:8px;border:1px solid #e5e7eb;vertical-align:top;text-align:center"><?php echo (int)($besoin->quantite ?? 0); ?></td>
                    <td style="padding:8px;border:1px solid #e5e7eb;vertical-align:top"><?php echo $attsText; ?></td>
                </tr>
            <?php
                    }
                }
            endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune ville trouvée.</p>
    <?php endif; ?>
</div>
