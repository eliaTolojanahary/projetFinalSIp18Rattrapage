<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mobile Money - Retrait</title>
    <link rel="stylesheet" href="<?= base_url('style.css') ?>">
</head>
<body>
 
    <div class="login-card">
           <?php include(APPPATH . 'Views/clients/layoutClient.php') ?>
        <h1>Retrait</h1>
        <p class="subtitle">Compte : <?= esc($compte['numero_telephone']) ?></p>
        <p class="subtitle">Solde actuel : <?= number_format($compte['solde'], 0, ',', ' ') ?> Ar</p>

        <?php if (session('error')): ?>
            <div class="alert"><?= esc(session('error')) ?></div>
        <?php endif; ?>

        <form method="post" action="/retrait">
            <?= csrf_field() ?>

            <label>Montant à retirer (Ar)</label>
            <input type="number" name="montant" id="montant" min="1" step="1" value="<?= old('montant') ?>" required>

            <label>Frais estimés</label>
            <input type="text" id="frais-affiche" value="0 Ar" disabled>

            <button type="submit">Confirmer le retrait</button>
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
            fetch(`<?= base_url('frais') ?>?montant=${montantInput.value}&type=retrait`)
                .then(res => res.json())
                .then(data => fraisAffiche.value = data.frais + ' Ar');
        });
    </script>
</body>
</html>