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
        <p class="subtitle">Depuis : <?= esc($compte['numero_telephone']) ?></p>
        <p class="subtitle">Solde : <?= number_format($compte['solde'], 0, ',', ' ') ?> Ar</p>

        <?php if (session('error')): ?>
            <div class="alert"><?= esc(session('error')) ?></div>
        <?php endif; ?>
        <?php if (session('success')): ?>
            <div class="alert alert-success"><?= esc(session('success')) ?></div>
        <?php endif; ?>

        <form method="post" action="/transfert">
            <?= csrf_field() ?>

            <label>Montant total à transférer (Ar)</label>
            <input type="number" name="montant" id="montant_total" min="100" step="100" required>

            <h3 style="margin-top:20px; font-size:15px; color:#1e3c72;">Destinataires</h3>
            <div id="recipients">
                <div class="recipient-group">
                    <label>Destinataire 1</label>
                    <select name="numero_destinataire[]" class="select-destinataire" required>
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($comptes as $c): ?>
                            <?php if ($c['numero_telephone'] !== $compte['numero_telephone']): ?>
                                <option value="<?= esc($c['numero_telephone']) ?>">
                                    <?= esc($c['numero_telephone']) ?> - <?= esc($c['nom'] ?? '') ?> <?= esc($c['prenom'] ?? '') ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="button" id="ajouter-destinataire" style="background:#eee;color:#333;">+ Ajouter un destinataire</button>

            <label style="display:flex; align-items:center; gap:8px; font-weight:normal; margin-top:16px;">
                <input type="checkbox" name="inclure_frais_retrait" id="inclure_frais" value="1" style="width:auto;">
                Inclure les frais de retrait (opérateur principal uniquement)
            </label>

            <label>Date de transfert</label>
            <input type="datetime-local" name="date_transfert" value="<?= date('Y-m-d\TH:i') ?>">

            <div id="recap" style="margin-top:16px; font-size:14px; color:#333;">
                <p>Nombre de destinataires : <span id="nb">1</span></p>
                <p>Frais estimés : <span id="total-frais">0 Ar</span></p>
            </div>

            <button type="submit">Confirmer le transfert</button>
        </form>

    </div>

    <script>
        const container = document.getElementById('recipients');
        const montantInput = document.getElementById('montant_total');
        const inclureFraisInput = document.getElementById('inclure_frais');
        const nbSpan = document.getElementById('nb');
        const totalFraisSpan = document.getElementById('total-frais');

        document.getElementById('ajouter-destinataire').addEventListener('click', () => {
            const div = document.createElement('div');
            div.className = 'recipient-group';
            div.style.marginTop = '10px';

            const original = document.querySelector('.select-destinataire');
            const select = original.cloneNode(true);
            select.value = '';
            select.addEventListener('change', calculerFrais);

            const label = document.createElement('label');
            label.textContent = 'Destinataire';

            div.appendChild(label);
            div.appendChild(select);
            container.appendChild(div);

            calculerFrais();
        });

       
        async function calculerFrais() {
            const montantTotal = parseFloat(montantInput.value) || 0;
            const selects = document.querySelectorAll('.select-destinataire');
            const numeros = Array.from(selects).map(s => s.value).filter(v => v !== '');

            nbSpan.textContent = numeros.length;

            if (numeros.length === 0 || montantTotal <= 0) {
                totalFraisSpan.textContent = '0 Ar';
                return;
            }

            const montantParPersonne = montantTotal / numeros.length;
            const inclureRetrait = inclureFraisInput.checked ? 1 : 0;

            let totalFrais = 0;

            for (const numero of numeros) {
                const url = `<?= base_url('frais') ?>?montant=${montantParPersonne}&type=transfert&numero=${numero}&inclure_frais_retrait=${inclureRetrait}`;
                const res = await fetch(url);
                const data = await res.json();
                totalFrais += (data.frais || 0);
            }

            totalFraisSpan.textContent = totalFrais.toLocaleString('fr-FR') + ' Ar';
        }

        montantInput.addEventListener('input', calculerFrais);
        inclureFraisInput.addEventListener('change', calculerFrais);
        document.querySelector('.select-destinataire').addEventListener('change', calculerFrais);
    </script>
</body>
</html>