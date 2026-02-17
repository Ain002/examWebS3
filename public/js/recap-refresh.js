document.addEventListener('DOMContentLoaded', function() {
    function formatMontant(montant) {
        return new Intl.NumberFormat('fr-MG', { minimumFractionDigits: 0 }).format(montant) + ' Ar';
    }
    function formatNumber(number) {
        return new Intl.NumberFormat('fr-MG', { minimumFractionDigits: 0 }).format(number);
    }

    var btn = document.getElementById('btnActualiser');
    if (!btn) return;

    btn.addEventListener('click', function() {
        btn.disabled = true;
        btn.textContent = '⏳ Chargement...';

        var xhr = new XMLHttpRequest();
        xhr.open('GET', '/api/recapitulatif', true);
        xhr.setRequestHeader('Accept', 'application/json');

        xhr.onreadystatechange = function() {
            if (xhr.readyState !== XMLHttpRequest.DONE) return;

            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    var data = JSON.parse(xhr.responseText);

                    var el;
                    el = document.getElementById('montantTotal'); if (el) el.textContent = formatMontant(data.montant_total);
                    el = document.getElementById('montantSatisfait'); if (el) el.textContent = formatMontant(data.montant_satisfait);
                    el = document.getElementById('montantRestant'); if (el) el.textContent = formatMontant(data.montant_restant);

                    el = document.getElementById('nombreTotal'); if (el) el.textContent = data.nombre_total;
                    el = document.getElementById('nombreSatisfait'); if (el) el.textContent = data.nombre_satisfait;
                    el = document.getElementById('nombreRestant'); if (el) el.textContent = data.nombre_restant;

                    el = document.getElementById('pourcentageSatisfait'); if (el) el.textContent = data.pourcentage_satisfait + '%';
                    var progressFill = document.querySelector('.progress-fill'); if (progressFill) progressFill.style.width = data.pourcentage_satisfait + '%';

                    el = document.getElementById('nombreDons'); if (el) el.textContent = data.nombre_dons;
                    el = document.getElementById('valeurDons'); if (el) el.textContent = formatNumber(data.valeur_totale_dons);
                    el = document.getElementById('donsDistribues'); if (el) el.textContent = data.dons_distribues;
                    el = document.getElementById('nombreAchats'); if (el) el.textContent = data.nombre_achats;
                    el = document.getElementById('montantAchats'); if (el) el.textContent = formatNumber(data.montant_total_achats);
                    el = document.getElementById('argentDisponible'); if (el) el.textContent = formatMontant(data.argent_disponible);

                    var statsParType = document.getElementById('statsParType');
                    if (statsParType) {
                        statsParType.innerHTML = '';
                        (data.par_type || []).forEach(function(type) {
                            var card = document.createElement('div');
                            card.className = 'stat-card-small';
                            card.innerHTML = '<div class="stat-label-small">'+ type.type +'</div>' +
                                             '<div class="stat-value-small">'+ formatMontant(type.montant) +'</div>' +
                                             '<div class="stat-detail">'+ type.nombre +' besoin(s) | '+ type.nombre_satisfait +' satisfait(s)</div>';
                            statsParType.appendChild(card);
                        });
                    }

                    var tbody = document.querySelector('#topVillesTable tbody');
                    if (tbody) {
                        tbody.innerHTML = '';
                        (data.top_villes || []).forEach(function(ville) {
                            var row = document.createElement('tr');
                            row.innerHTML = '<td>'+ ville.ville +'</td><td>'+ ville.nombre_besoins +'</td><td>'+ formatMontant(ville.montant_besoins) +'</td>';
                            tbody.appendChild(row);
                        });
                    }

                } catch (e) {
                    alert('Erreur lors du traitement des données: ' + e.message);
                }
            } else {
                alert('Erreur lors de l\'actualisation: ' + xhr.status + ' ' + xhr.statusText);
            }

            btn.disabled = false;
            btn.textContent = '🔄 Actualiser';
        };

        xhr.onerror = function() {
            alert('Erreur réseau lors de l\'actualisation');
            btn.disabled = false;
            btn.textContent = '🔄 Actualiser';
        };

        xhr.send();
    });
});