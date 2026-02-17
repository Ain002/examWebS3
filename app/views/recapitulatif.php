<?php
?>
<div>
    <h1>Récapitulatif des Besoins</h1>
    <p>Statistiques globales sur les besoins et leur satisfaction.</p>
    
    <div style="text-align:right;margin-bottom:20px;">
        <button id="btnActualiser" class="btn-actualiser">🔄 Actualiser</button>
    </div>
    
    <div id="statsContainer">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Montant Total des Besoins</div>
                <div class="stat-value" id="montantTotal">
                    <?php echo number_format($montant_total ?? 0, 0, ',', ' '); ?> Ar
                </div>
                <div class="stat-subvalue">
                    <span id="nombreTotal"><?php echo $nombre_total ?? 0; ?></span> besoin(s)
                </div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-label">Montant Satisfait</div>
                <div class="stat-value" id="montantSatisfait">
                    <?php echo number_format($montant_satisfait ?? 0, 0, ',', ' '); ?> Ar
                </div>
                <div class="stat-subvalue">
                    <span id="nombreSatisfait"><?php echo $nombre_satisfait ?? 0; ?></span> besoin(s)
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo $pourcentage_satisfait ?? 0; ?>%"></div>
                </div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-label">Montant Restant</div>
                <div class="stat-value" id="montantRestant">
                    <?php echo number_format($montant_restant ?? 0, 0, ',', ' '); ?> Ar
                </div>
                <div class="stat-subvalue">
                    <span id="nombreRestant"><?php echo $nombre_restant ?? 0; ?></span> besoin(s)
                </div>
            </div>
        </div>

        <div class="section-title">Statistiques par type</div>
        <div id="statsParType" class="stats-grid">
            <?php foreach ($par_type ?? [] as $pt): ?>
                <div class="stat-card-small">
                    <div class="stat-label-small"><?php echo htmlspecialchars($pt['type']); ?></div>
                    <div class="stat-value-small"><?php echo number_format($pt['montant'], 0, ',', ' '); ?> Ar</div>
                    <div class="stat-detail"><?php echo $pt['nombre']; ?> besoin(s) | <?php echo $pt['nombre_satisfait']; ?> satisfait(s)</div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="stats-grid" style="margin-top:20px;">
            <div class="stat-card info">
                <div class="stat-label">Dons</div>
                <div class="stat-value" id="nombreDons"><?php echo $nombre_dons ?? 0; ?></div>
                <div class="stat-subvalue">Valeur totale: <span id="valeurDons"><?php echo number_format($valeur_totale_dons ?? 0, 0, ',', ' '); ?></span></div>
                <div class="stat-detail">Distribués: <span id="donsDistribues"><?php echo $dons_distribues ?? 0; ?></span></div>
            </div>

            <div class="stat-card info">
                <div class="stat-label">Achats</div>
                <div class="stat-value" id="nombreAchats"><?php echo $nombre_achats ?? 0; ?></div>
                <div class="stat-subvalue">Montant total: <span id="montantAchats"><?php echo number_format($montant_total_achats ?? 0, 0, ',', ' '); ?></span></div>
            </div>

            <div class="stat-card info">
                <div class="stat-label">Argent disponible</div>
                <div class="stat-value" id="argentDisponible"><?php echo number_format($argent_disponible ?? 0, 0, ',', ' '); ?> Ar</div>
            </div>
        </div>

        <div class="section-title">Top 5 villes par montant de besoins</div>
        <div class="table-container">
            <table class="stats-table" id="topVillesTable">
                <thead>
                    <tr>
                        <th>Ville</th>
                        <th>Nombre de besoins</th>
                        <th>Montant des besoins</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_villes ?? [] as $ville): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ville['ville']); ?></td>
                        <td><?php echo $ville['nombre_besoins']; ?></td>
                        <td><?php echo number_format($ville['montant_besoins'], 0, ',', ' '); ?> Ar</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script nonce="<?php echo htmlspecialchars($_SERVER['CSP_NONCE'] ?? ''); ?>">
    function formatMontant(montant) {
        return new Intl.NumberFormat('fr-MG', { 
            minimumFractionDigits: 0
        }).format(montant) + ' Ar';
    }

    function formatNumber(number) {
        return new Intl.NumberFormat('fr-MG', { 
            minimumFractionDigits: 0
        }).format(number);
    }
</script>


<style>
#statsContainer {
    transition: opacity 0.2s ease;
}

.section-title {
    font-size: 20px;
    font-weight: 600;
    margin-top: 40px;
    margin-bottom: 15px;
    color: #111827;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-left: 4px solid #2563eb;
}

.stat-card.success {
    border-left-color: #10b981;
}

.stat-card.warning {
    border-left-color: #f59e0b;
}

.stat-card.info {
    border-left-color: #3b82f6;
}

.stat-card-small {
    background: white;
    border-radius: 6px;
    padding: 15px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    border-left: 3px solid #6366f1;
}

.stat-label {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 8px;
}

.stat-label-small {
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 6px;
    font-weight: 500;
}

.stat-value {
    font-size: 32px;
    font-weight: bold;
    color: #111827;
}

.stat-value-small {
    font-size: 24px;
    font-weight: bold;
    color: #111827;
}

.stat-subvalue {
    font-size: 14px;
    color: #9ca3af;
    margin-top: 4px;
}

.stat-detail {
    font-size: 12px;
    color: #9ca3af;
    margin-top: 6px;
}

.btn-actualiser {
    background-color: #2563eb;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: background-color 0.2s;
}

.btn-actualiser:hover:not(:disabled) {
    background-color: #1d4ed8;
}

.btn-actualiser:disabled {
    background-color: #9ca3af;
    cursor: not-allowed;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background-color: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    margin-top: 10px;
}

.progress-fill {
    height: 100%;
    background-color: #10b981;
    transition: width 0.3s ease;
}

.table-container {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-top: 20px;
    overflow-x: auto;
}

.stats-table {
    width: 100%;
    border-collapse: collapse;
}

.stats-table th {
    background-color: #f3f4f6;
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
}

.stats-table td {
    padding: 12px;
    border-bottom: 1px solid #e5e7eb;
    color: #111827;
}

.stats-table tr:hover {
    background-color: #f9fafb;
}

.stats-table tbody tr:last-child td {
    border-bottom: none;
}
</style>
