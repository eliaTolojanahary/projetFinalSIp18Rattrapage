<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mobile Money - Epargne</title>
    <link rel="stylesheet" href="<?= base_url('style.css') ?>">
</head>
<body>
    <div class="login-card">
        <h1>Configuration Épargne</h1>
        <?php include APPPATH . 'Views/clients/layoutClient.php'; ?>

        <p class="subtitle">Compte : <?= esc($compte['numero_telephone']) ?></p>
        <p class="subtitle">Solde principal : <?= number_format($compte['solde'], 0, ',', ' ') ?> Ar</p>
        <p class="subtitle">Solde épargne : <?= number_format($soldeEpargne, 0, ',', ' ') ?> Ar</p>
        <p class="subtitle">Pourcentage actuel : <?= number_format($compte['pourcentage_epargne'], 0, ',', ' ') ?> %</p>

        <?php if (session('error')): ?>
            <div class="alert"><?= esc(session('error')) ?></div>
        <?php endif; ?>
        <?php if (session('success')): ?>
            <div class="alert alert-success"><?= esc(session('success')) ?></div>
        <?php endif; ?>

        <form method="post" action="/epargne">
            <?= csrf_field() ?>

            <label>Pourcentage Épargne (0 - 100 %)</label>
            <input type="number" name="pourcentage" id="pourcentage" min="0" max="100" required
                   value="<?= esc($compte['pourcentage_epargne']) ?>">

            <button type="submit">Confirmer le Pourcentage épargné</button>
        </form>

    </div>
</body>
</html>