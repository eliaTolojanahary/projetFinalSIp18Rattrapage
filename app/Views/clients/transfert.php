<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mobile Money - Transfert</title>
      <link rel="stylesheet" href="<?= base_url('style.css') ?>">
</head>
<body>
    <div class="login-card">
    <h1>Transfert d'argent</h1>
    <p>Depuis : <?= esc($compte['numero_telephone']) ?></p>
    <p>Solde : <?= number_format($compte['solde'], 0, ',', ' ') ?> Ar</p>

    <?php if (session('error')): ?>
        <div style="color:red;"><?= esc(session('error')) ?></div>
    <?php endif; ?>

    <?php if (session('success')): ?>
        <div style="color:green;"><?= esc(session('success')) ?></div>
    <?php endif; ?>

    <form method="post" action="/transfert">
        <?= csrf_field() ?>

        <h3>Montant total à transférer</h3>
        <p>
            <label>Montant (Ar) :</label>
            <input type="number" name="montant_total" id="montant_total" min="100" step="100" required oninput="calculerTotaux()">
        </p>

        <hr>

        <h3>Destinataires</h3>
        <div id="recipients">
            <div class="recipient-group">
                <p>
                    <label>Destinataire 1 :</label>
                    <select name="destinataires[0][numero]" class="select-destinataire" required onchange="calculerTotaux()">
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($comptes as $c): ?>
                            <?php if ($c['numero_telephone'] !== $compte['numero_telephone']): ?>
                                <option value="<?= esc($c['numero_telephone']) ?>" 
                                        data-prefixe="<?= substr($c['numero_telephone'], 0, 3) ?>"
                                        data-est-principal="<?= isset($prefixesInfos[substr($c['numero_telephone'], 0, 3)]['est_operateur_principal']) ? $prefixesInfos[substr($c['numero_telephone'], 0, 3)]['est_operateur_principal'] : 0 ?>"
                                        data-commission="<?= isset($prefixesInfos[substr($c['numero_telephone'], 0, 3)]['commission']) ? $prefixesInfos[substr($c['numero_telephone'], 0, 3)]['commission'] : 0 ?>">
                                    <?= esc($c['numero_telephone']) ?> - <?= esc($c['nom'] ?? '') ?> <?= esc($c['prenom'] ?? '') ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" onclick="removeRecipient(this)">✕</button>
                </p>
                <p>
                    <label>Montant :</label>
                    <input type="number" name="destinataires[0][montant]" class="montant-reparti" readonly>
                    <span class="pourcentage">0%</span>
                </p>
                <p>
                    <span class="info-operateur"></span>
                </p>
                <hr>
            </div>
        </div>

        <button type="button" onclick="addRecipient()">+ Ajouter un destinataire</button>

        <p>
            <input type="checkbox" name="inclure_frais_retrait" value="1" id="inclure_frais" onchange="calculerTotaux()">
            <label>Inclure les frais de retrait <small>(uniquement opérateur principal)</small></label>
        </p>

        <hr>
        <h3>Récapitulatif</h3>
        <div id="recap">
            <p>Nombre de destinataires : <span id="nb">1</span></p>
            <p>Montant total : <span id="total-montant">0 Ar</span></p>
            <p>Frais de transfert : <span id="total-frais">0 Ar</span></p>
            <p>Frais de retrait : <span id="total-frais-retrait">0 Ar</span></p>
            <p>Commission : <span id="total-commission">0 Ar</span></p>
            <p><strong>Total à débiter : <span id="total-debit">0 Ar</span></strong></p>
        </div>

        <button type="submit">Confirmer le transfert</button>
    </form>
    </div>
     <script>
        let count = 1;
        
        const operateursData = <?= json_encode($prefixesInfos ?? []) ?>;
    
        const optionsData = <?php 
            $options = [];
            foreach ($comptes as $c) {
                if ($c['numero_telephone'] !== $compte['numero_telephone']) {
                    $prefixe = substr($c['numero_telephone'], 0, 3);
                    $options[] = [
                        'value' => $c['numero_telephone'],
                        'label' => $c['numero_telephone'] . ' - ' . ($c['nom'] ?? '') . ' ' . ($c['prenom'] ?? ''),
                        'prefixe' => $prefixe,
                        'est_principal' => isset($prefixesInfos[$prefixe]['est_operateur_principal']) ? $prefixesInfos[$prefixe]['est_operateur_principal'] : 0,
                        'commission' => isset($prefixesInfos[$prefixe]['commission']) ? $prefixesInfos[$prefixe]['commission'] : 0
                    ];
                }
            }
            echo json_encode($options);
        ?>;

        function getOperateurInfo(prefixe) {
            if (!prefixe) return null;
            return operateursData[prefixe] || null;
        }

        function calculerTotaux() {
            const montantTotal = parseFloat(document.getElementById('montant_total').value) || 0;
            const inclureRetrait = document.getElementById('inclure_frais').checked;
            
            const groups = document.querySelectorAll('.recipient-group');
            const groupesValides = [];
            
            groups.forEach(group => {
                const select = group.querySelector('.select-destinataire');
                if (select && select.value) {
                    groupesValides.push(group);
                }
            });
            
            const nbValide = groupesValides.length;
            document.getElementById('nb').textContent = nbValide || 0;
            
            if (nbValide === 0 || montantTotal <= 0) {
                document.getElementById('total-montant').textContent = '0 Ar';
                document.getElementById('total-frais').textContent = '0 Ar';
                document.getElementById('total-frais-retrait').textContent = '0 Ar';
                document.getElementById('total-commission').textContent = '0 Ar';
                document.getElementById('total-debit').textContent = '0 Ar';
                return;
            }

            const montantParPersonne = Math.floor(montantTotal / nbValide);
            const reste = montantTotal - (montantParPersonne * nbValide);
            
            let totalFrais = 0;
            let totalFraisRetrait = 0;
            let totalCommission = 0;
            
            groupesValides.forEach((group, index) => {
                const select = group.querySelector('.select-destinataire');
                const montantInput = group.querySelector('.montant-reparti');
                const pourcentageSpan = group.querySelector('.pourcentage');
                const infoSpan = group.querySelector('.info-operateur');
                
                const numero = select.value;
                const prefixe = numero.substring(0, 3);
                const operateur = getOperateurInfo(prefixe);
                
                let montant = montantParPersonne;
                if (index === 0) {
                    montant += reste;
                }
                
                montantInput.value = montant;
                pourcentageSpan.textContent = `${Math.round((montant / montantTotal) * 100)}%`;
                
                // Frais de transfert (10%)
                const frais = montant * 0.10;
                totalFrais += frais;
                
                // Frais de retrait (uniquement opérateur principal)
                let fraisRetrait = 0;
                if (inclureRetrait && operateur && operateur.est_operateur_principal == 1) {
                    fraisRetrait = montant * 0.05;
                    totalFraisRetrait += fraisRetrait;
                }
                
                // Commission (depuis la base de données)
                let commission = 0;
                if (operateur && operateur.est_operateur_principal == 0 && operateur.commission) {
                    commission = (montant * operateur.commission) / 100;
                    totalCommission += commission;
                }
                
                let infoText = '';
                if (operateur) {
                    infoText = `Opérateur: ${operateur.libelle || prefixe}`;
                    if (operateur.est_operateur_principal == 1) {
                        infoText += ' Principal';
                        if (inclureRetrait) {
                            infoText += ` | Frais retrait: ${fraisRetrait.toFixed(0)} Ar`;
                        }
                    } else {
                        infoText += ` Autre opérateur`;
                        if (commission > 0) {
                            infoText += ` | Commission: ${commission.toFixed(0)} Ar (${operateur.commission}%)`;
                        }
                    }
                } else {
                    infoText = ` Opérateur non reconnu`;
                }
                infoSpan.textContent = infoText;
            });
            
            const totalDebit = montantTotal + totalFrais + totalFraisRetrait + totalCommission;
            
            document.getElementById('total-montant').textContent = montantTotal.toLocaleString('fr-FR') + ' Ar';
            document.getElementById('total-frais').textContent = totalFrais.toFixed(0).toLocaleString('fr-FR') + ' Ar';
            document.getElementById('total-frais-retrait').textContent = totalFraisRetrait.toFixed(0).toLocaleString('fr-FR') + ' Ar';
            document.getElementById('total-commission').textContent = totalCommission.toFixed(0).toLocaleString('fr-FR') + ' Ar';
            document.getElementById('total-debit').textContent = totalDebit.toFixed(0).toLocaleString('fr-FR') + ' Ar';
        }

        function addRecipient() {
            count++;
            const div = document.getElementById('recipients');
            const newDiv = document.createElement('div');
            newDiv.className = 'recipient-group';
            const index = count - 1;
            
            // Construire les options du select en JavaScript
            let optionsHtml = '<option value="">-- Sélectionner --</option>';
            optionsData.forEach(opt => {
                optionsHtml += `<option value="${opt.value}" 
                                    data-prefixe="${opt.prefixe}"
                                    data-est-principal="${opt.est_principal}"
                                    data-commission="${opt.commission}">
                                    ${opt.label}
                                </option>`;
            });
            
            newDiv.innerHTML = `
                <p>
                    <label>Destinataire ${count} :</label>
                    <select name="destinataires[${index}][numero]" class="select-destinataire" required onchange="calculerTotaux()">
                        ${optionsHtml}
                    </select>
                    <button type="button" onclick="removeRecipient(this)">✕</button>
                </p>
                <p>
                    <label>Montant :</label>
                    <input type="number" name="destinataires[${index}][montant]" class="montant-reparti" readonly>
                    <span class="pourcentage">0%</span>
                </p>
                <p>
                    <span class="info-operateur"></span>
                </p>
                <hr>
            `;
            div.appendChild(newDiv);
            
            document.getElementById('nb').textContent = count;
            calculerTotaux();
        }

        function removeRecipient(btn) {
            const group = btn.parentElement.parentElement;
            if (document.querySelectorAll('.recipient-group').length <= 1) {
                alert('Vous devez avoir au moins un destinataire');
                return;
            }
            group.remove();
            count = document.querySelectorAll('.recipient-group').length;
            document.getElementById('nb').textContent = count;
            calculerTotaux();
        }

        // Initialiser
        setTimeout(calculerTotaux, 100);
    </script>
</body>
</html>