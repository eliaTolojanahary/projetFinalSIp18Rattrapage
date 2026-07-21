<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mobile Money - Epargne</title>
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

        <form method="post" action="/epargne">
            <?= csrf_field() ?>

            <label>Pourcentage Epargne</label>
            <input type="number" name="pourcentage" id="pourcentage" min="0" max="100" required>

            <button type="submit">Confirmer le Pourcentage épargné</button>
        </form>

    </div>
</body>
</html>