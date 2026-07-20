<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mobile Money - Transfert</title>
    <link rel="stylesheet" href="<?= base_url('style.css') ?>">
</head>
<body>
    <div class="login-card">
        <h1>Transfert</h1>
        <p class="subtitle">Depuis : <?= esc($compte['numero_telephone']) ?></p>
        <p class="subtitle">Solde actuel : <?= number_format($compte['solde'], 0, ',', ' ') ?> Ar</p>

        <?php if (session('error')): ?>
            <div class="alert"><?= esc(session('error')) ?></div>
        <?php endif; ?>

        <form method="post" action="/transfert">
            <?= csrf_field() ?>

            <label>Numéro du destinataire</label>
            <input type="text" name="numero_destinataire" list="liste-comptes"
                   value="<?= old('numero_destinataire') ?>" placeholder="0371234567" required>
            <datalist id="liste-comptes">
                <?php foreach ($comptes as $c): ?>
                    <?php if ($c['numero_telephone'] !== $compte['numero_telephone']): ?>
                        <option value="<?= esc($c['numero_telephone']) ?>">
                    <?php endif; ?>
                <?php endforeach; ?>
            </datalist>

            <label>Montant (Ar)</label>
            <input type="number" name="montant" id="montant" min="1" step="1"
                   value="<?= old('montant') ?>" required>

            <label>Date de transfert</label>
            <input type="datetime-local" name="date_transfert"
                   value="<?= date('Y-m-d\TH:i') ?>">

            <label>Frais estimés</label>
            <input type="text" id="frais-affiche" value="0 Ar" disabled>

            <button type="submit">Confirmer le transfert</button>
        </form>
    </div>

    <script>
        const montantInput = document.getElementById('montant');
        const fraisAffiche = document.getElementById('frais-affiche');

        montantInput.addEventListener('input', () => {
            if (!montantInput.value || montantInput.value <= 0) {
                fraisAffiche.value = '0 Ar';
                return;
            }
            fetch(`<?= base_url('frais') ?>?montant=${montantInput.value}&type=transfert`)
                .then(res => res.json())
                .then(data => fraisAffiche.value = data.frais + ' Ar');
        });
    </script>
</body>
</html>