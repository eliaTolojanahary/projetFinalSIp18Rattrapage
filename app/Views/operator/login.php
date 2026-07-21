<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div style="display:flex; justify-content:center; align-items:center; min-height:60vh;">
    <div class="card" style="width:100%; max-width:420px; padding:32px;">
        <h1 style="text-align:center; margin-bottom:8px;">Connexion Opérateur</h1>
        <p class="muted" style="text-align:center; margin-bottom:24px;">Identifiants : <strong>admin / admin</strong></p>

        <form method="post" action="<?= site_url('/operator/login') ?>">
            <?= csrf_field() ?>

            <div class="field">
                <label for="login">Login</label>
                <input type="text" id="login" name="login" value="<?= old('login') ?>" placeholder="admin" required>
            </div>

            <div class="field">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="admin" required>
            </div>

            <button type="submit" class="btn" style="width:100%; margin-top:16px;">Se connecter</button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
