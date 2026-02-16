<?php

?>
<div>
    <h1>Besoins pour la ville</h1>
    <p>Gérez les besoins associés à cette ville.</p>

    <div style="margin:12px 0;">
        <button id="btnAdd" style="padding:8px 12px;border-radius:6px;background:#2563eb;color:#fff;border:none;cursor:pointer">Ajouter un besoin</button>
    </div>

    <?php if (!empty($besoins) && is_iterable($besoins)): ?>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f3f4f6;text-align:left;">
                    <th style="padding:8px;border:1px solid #e5e7eb;">Description / Produit</th>
                    <th style="padding:8px;border:1px solid #e5e7eb;">Quantité</th>
                    <th style="padding:8px;border:1px solid #e5e7eb;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($besoins as $b):
                    $rowId = (int)($b->id ?? 0);
                    $rowProduit = $b->idProduit ?? null;
                    $rowQuant = (int)($b->quantite ?? 0);
                    $rowVille = (int)($b->idVille ?? ($villeId ?? 0));
                ?>
                    <tr data-id="<?= $rowId ?>" data-id-produit="<?= $rowProduit ?>" data-quantite="<?= $rowQuant ?>" data-id-ville="<?= $rowVille ?>">
                        <td style="padding:8px;border:1px solid #e5e7eb;"><?= htmlspecialchars($rowProduit ? ($b->getProduit()?->description ?? 'Produit #'.$rowProduit) : ($b->description ?? '')) ?></td>
                        <td style="padding:8px;border:1px solid #e5e7eb;text-align:center;"><?= $rowQuant ?></td>
                        <td style="padding:8px;border:1px solid #e5e7eb;">
                            <button class="editBtn" style="margin-right:8px;padding:6px 10px">Modifier</button>
                            <button class="delBtn" style="padding:6px 10px;background:#ef4444;color:#fff;border:none;border-radius:6px">Supprimer</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun besoin trouvé.</p>
    <?php endif; ?>

    <div id="modal" style="display:none;position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.4);align-items:center;justify-content:center">
        <div style="background:#fff;padding:18px;border-radius:8px;min-width:320px;max-width:720px">
            <h3 id="modalTitle">Nouveau besoin</h3>

            <div id="besoinFormContainer">
                <?php
                    $besoin = null;
                    $produits = $produits ?? [];
                    include __DIR__ . '/besoinForm.php';
                ?>
                <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px">
                    <button type="button" id="cancelBtn" style="padding:8px 12px">Annuler</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function(){
            const modal = document.getElementById('modal');
            // le formulaire est inclus dynamiquement dans le modal via le partial
            const form = modal.querySelector('form');
            const btnAdd = document.getElementById('btnAdd');

            function openModal(data){
                modal.style.display = 'flex';
                // utiliser form.elements pour éviter collision avec form.id
                if (form && form.elements) {
                    form.elements['id'].value = data?.id || '';
                    if (form.elements['idProduit']) form.elements['idProduit'].value = data?.idProduit || '';
                    if (form.elements['quantite']) form.elements['quantite'].value = data?.quantite || '';
                    if (form.elements['idVille']) form.elements['idVille'].value = data?.idVille || form.elements['idVille'].value || '';
                }
                document.getElementById('modalTitle').textContent = data?.id ? 'Modifier le besoin' : 'Nouveau besoin';
            }

            function closeModal(){ modal.style.display = 'none'; }

            btnAdd.addEventListener('click', () => openModal({ idVille: form?.elements['idVille']?.value }));
            document.getElementById('cancelBtn').addEventListener('click', closeModal);

            document.querySelectorAll('.editBtn').forEach(btn => {
                btn.addEventListener('click', function(){
                    const tr = this.closest('tr');
                    const id = tr.dataset.id;
                    const idProduit = tr.dataset.idProduit || '';
                    const quant = tr.dataset.quantite || '';
                    const idVille = tr.dataset.idVille || (form?.elements['idVille']?.value) || '';
                    openModal({ id: id, idProduit: idProduit, quantite: quant, idVille: idVille });
                });
            });

            document.querySelectorAll('.delBtn').forEach(btn => {
                btn.addEventListener('click', async function(){
                    const tr = this.closest('tr');
                    const id = tr.dataset.id;
                    if(!confirm('Supprimer le besoin #'+id+' ?')) return;
                    try {
                        const fd = new FormData();
                        fd.append('idVille', form?.elements['idVille']?.value || '');
                        const res = await fetch('/besoin/delete/' + id, { method: 'POST', body: fd });
                        if(!res.ok) throw new Error('Erreur');
                        const html = await res.text();
                        const container = document.getElementById('pageContent') || document.body;
                        container.innerHTML = html;
                    } catch(err) { alert('Erreur suppression'); }
                });
            });

            form.addEventListener('submit', async function(e){
                e.preventDefault(); 
                try {
                    const data = new FormData(form);
                    const res = await fetch(form.action, {
                        method: 'POST',
                        body: data
                    });
                    if(!res.ok) throw new Error('Erreur');
                    const html = await res.text();
                    closeModal();
                    const container = document.getElementById('pageContent') || document.body;
                    container.innerHTML = html;
                } catch(err) { alert('Erreur sauvegarde'); }
            });

        })();
    </script>

</div>