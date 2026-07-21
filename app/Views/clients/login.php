<!DOCTYPE html>
<html lang="fr">
<head>
   <link rel="stylesheet" href="<?= base_url('style.css') ?>">
    <meta charset="UTF-8">
    <title>Mobile Money - Connexion</title>

</head>
<body>
    <div class="login-card">
        <h1>Mobile Money </h1>
        <p>( veuillez utiliser le numero pour tester : 034123456 )</p>
        <p class="subtitle">Entrez votre numéro pour continuer</p>

        <?php if (session('error')): ?>
            <div class="alert"><?= esc(session('error')) ?></div>
        <?php endif; ?>

        <form method="post" action="/login">
            <?= csrf_field() ?>
            <label>Numéro de téléphone</label>
            <input type="text" name="numero_telephone" value="<?= old('numero_telephone') ?>" placeholder="0331234567">
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>