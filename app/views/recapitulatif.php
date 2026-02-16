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
            
            <div class="stat-card">
                <div class="stat-label">Taux de Satisfaction</div>
                <div class="stat-value" id="pourcentageSatisfait">
                    <?php echo $pourcentage_satisfait ?? 0; ?>%
                </div>
                <div class="stat-subvalue">
                    Des besoins sont satisfaits
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('btnActualiser').addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    btn.textContent = '⏳ Chargement...';
    
    fetch('/api/recapitulatif', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Mettre à jour les valeurs dans le DOM
        document.getElementById('montantTotal').textContent = formatMontant(data.montant_total);
        document.getElementById('montantSatisfait').textContent = formatMontant(data.montant_satisfait);
        document.getElementById('montantRestant').textContent = formatMontant(data.montant_restant);
        
        document.getElementById('nombreTotal').textContent = data.nombre_total;
        document.getElementById('nombreSatisfait').textContent = data.nombre_satisfait;
        document.getElementById('nombreRestant').textContent = data.nombre_restant;
        
        document.getElementById('pourcentageSatisfait').textContent = data.pourcentage_satisfait + '%';
        
        // Mettre à jour la barre de progression
        document.querySelector('.progress-fill').style.width = data.pourcentage_satisfait + '%';
        
        // Animation de confirmation
        const container = document.getElementById('statsContainer');
        container.style.opacity = '0.5';
        setTimeout(() => {
            container.style.opacity = '1';
        }, 200);
        
        btn.disabled = false;
        btn.textContent = '🔄 Actualiser';
    })
    .catch(error => {
        alert('Erreur lors de l\'actualisation: ' + error.message);
        btn.disabled = false;
        btn.textContent = '🔄 Actualiser';
    });
});

function formatMontant(montant) {
    return new Intl.NumberFormat('fr-MG', { 
        minimumFractionDigits: 0
    }).format(montant) + ' Ar';
}
</script>

<style>
#statsContainer {
    transition: opacity 0.2s ease;
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

.stat-label {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 8px;
}

.stat-value {
    font-size: 32px;
    font-weight: bold;
    color: #111827;
}

.stat-subvalue {
    font-size: 14px;
    color: #9ca3af;
    margin-top: 4px;
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
</style>
